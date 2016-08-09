<?php
namespace PpitCommitment\Controller;

use DateInterval;
use Date;
use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Account;
use PpitCommitment\Model\Message;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Subscription;
use PpitContact\Model\Vcard;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Credit;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;

class CommitmentController extends AbstractActionController
{
	public function indexAction()
    {
    	$context = Context::getCurrent();
//		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$type = $this->params()->fromRoute('type', null);
		$applicationName = 'P-PIT Engagements';
		$menu = Context::getCurrent()->getConfig('menus')['p-pit-engagements'];

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'active' => 'application',
    			'applicationName' => $applicationName,
    			'menu' => $menu,
    			'type' => $type,
    	));
    }
	
	public function getFilters($params)
	{
		// Retrieve the query parameters
		$filters = array();

		$account_id = ($params()->fromQuery('account_id', null));
		if ($account_id) $filters['account_id'] = $account_id;
		
		$subscription_id = ($params()->fromQuery('subscription_id', null));
		if ($subscription_id) $filters['subscription_id'] = $subscription_id;
		
		$status = ($params()->fromQuery('status', null));
		if ($status) $filters['status'] = $status;

		$min_amount = ($params()->fromQuery('min_amount', null));
		if ($min_amount) $filters['min_amount'] = $min_amount;
		
		$max_amount = ($params()->fromQuery('max_amount', null));
		if ($max_amount) $filters['max_amount'] = $max_amount;
		
		$identifier = ($params()->fromQuery('identifier', null));
		if ($identifier) $filters['identifier'] = $identifier;

		$quotation_identifier = ($params()->fromQuery('quotation_identifier', null));
		if ($quotation_identifier) $filters['quotation_identifier'] = $quotation_identifier;
		
		$invoice_identifier = ($params()->fromQuery('invoice_identifier', null));
		if ($invoice_identifier) $filters['invoice_identifier'] = $invoice_identifier;
		
		$min_commitment_date = ($params()->fromQuery('min_commitment_date', null));
		if ($min_commitment_date) $filters['min_commitment_date'] = $min_commitment_date;
		
		$max_commitment_date = ($params()->fromQuery('max_commitment_date', null));
		if ($max_commitment_date) $filters['max_commitment_date'] = $max_commitment_date;

		$min_retraction_limit = ($params()->fromQuery('min_retraction_limit', null));
		if ($min_retraction_limit) $filters['min_retraction_limit'] = $min_retraction_limit;
		
		$max_retraction_limit = ($params()->fromQuery('max_retraction_limit', null));
		if ($max_retraction_limit) $filters['max_retraction_limit'] = $max_retraction_limit;
		
		$min_retraction_date = ($params()->fromQuery('min_retraction_date', null));
		if ($min_retraction_date) $filters['min_retraction_date'] = $min_retraction_date;
		
		$max_retraction_date = ($params()->fromQuery('max_retraction_date', null));
		if ($max_retraction_date) $filters['max_retraction_date'] = $max_retraction_date;

		$min_expected_delivery_date = ($params()->fromQuery('min_expected_delivery_date', null));
		if ($min_expected_delivery_date) $filters['min_expected_delivery_date'] = $min_expected_delivery_date;
		
		$max_expected_delivery_date = ($params()->fromQuery('max_expected_delivery_date', null));
		if ($max_expected_delivery_date) $filters['max_expected_delivery_date'] = $max_expected_delivery_date;

		$min_shipment_date = ($params()->fromQuery('min_shipment_date', null));
		if ($min_shipment_date) $filters['min_shipment_date'] = $min_shipment_date;
		
		$max_shipment_date = ($params()->fromQuery('max_shipment_date', null));
		if ($max_shipment_date) $filters['max_shipment_date'] = $max_shipment_date;

		$min_delivery_date = ($params()->fromQuery('min_delivery_date', null));
		if ($min_delivery_date) $filters['min_delivery_date'] = $min_delivery_date;
		
		$max_delivery_date = ($params()->fromQuery('max_delivery_date', null));
		if ($max_delivery_date) $filters['max_delivery_date'] = $max_delivery_date;

		$min_commissioning_date = ($params()->fromQuery('min_commissioning_date', null));
		if ($min_commissioning_date) $filters['min_commissioning_date'] = $min_commissioning_date;
		
		$max_commissioning_date = ($params()->fromQuery('max_commissioning_date', null));
		if ($max_commissioning_date) $filters['max_commissioning_date'] = $max_commissioning_date;

		$min_invoice_date = ($params()->fromQuery('min_invoice_date', null));
		if ($min_invoice_date) $filters['min_invoice_date'] = $min_invoice_date;
		
		$max_invoice_date = ($params()->fromQuery('max_invoice_date', null));
		if ($max_invoice_date) $filters['max_invoice_date'] = $max_invoice_date;

		for ($i = 1; $i < 20; $i++) {
		
			$property = ($params()->fromQuery('property_'.$i, null));
			if ($property) $filters['property_'.$i] = $property;
			$min_property = ($params()->fromQuery('min_property_'.$i, null));
			if ($min_property) $filters['min_property_'.$i] = $min_property;
			$max_property = ($params()->fromQuery('max_property_'.$i, null));
			if ($max_property) $filters['max_property_'.$i] = $max_property;
		}
		
		return $filters;
	}
	
   	public function searchAction()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);

		$params = $this->getFilters($this->params());
		
   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'accounts' => Account::getList($params, 'customer_name', 'ASC'),
   				'subscriptions' => Subscription::getList(array(), 'product_identifier', 'ASC'),
   				'statuses' => $context->getConfig('commitment')['statuses'],
   				'type' => $type,
   		));
		$view->setTerminal(true);
       	return $view;
   	}

   	public function getList()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		$params = $this->getFilters($this->params());

		// Retrieve the order type
		$type = $this->params()->fromRoute('type', null);
		
		$major = ($this->params()->fromQuery('major', 'identifier'));
		$dir = ($this->params()->fromQuery('dir', 'ASC'));

		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

		// Retrieve the list
		$commitments = Commitment::getList($type, $params, $major, $dir, $mode);

		// Retrieve the credits
		$credit = Credit::getTable()->get('p-pit-engagements', 'type'); 
		
   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'type' => $type,
   				'properties' => $context->getConfig('commitment')['properties'],
   				'statuses' => $context->getConfig('commitment')['statuses'],
   				'commitments' => $commitments,
   				'mode' => $mode,
   				'params' => $params,
   				'major' => $major,
   				'dir' => $dir,
   				'credit' => $credit,
   		));
		$view->setTerminal(true);
       	return $view;
   	}
   	
   	public function listAction()
   	{
   		return $this->getList();
   	}

   	public function accountListAction()
   	{
   		return $this->getList();
   	}
   	
   	public function exportAction()
   	{
   		return $this->getList();
   	}
    
    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);
    	
		$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $commitment = Commitment::get($id);
    	else $commitment = Commitment::instanciate($type);

    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
   			'statuses' => $context->getConfig('commitment')['statuses'],
    		'id' => $commitment->id,
    		'commitment' => $commitment,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function subscribeAction()
    {
    	$context = Context::getCurrent();
    	$product = $this->params()->fromRoute('product', null);
		if (!$context->isAuthenticated()) return $this->redirect()->toRoute('commitmentAccount/register', array('product' => $product));

		$subscription = Subscription::getCurrent($product);
    	$commitment = Commitment::instanciate($subscription->type, $subscription);

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check

    			// Load the input data
    			$commitment->loadDataFromRequest($request, $action);

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$commitment->id) $return = $commitment->add();
    				else $return = $commitment->update($request->getPost('update_time'));

    				if ($return != 'OK') {
    					$connection->rollback();
    					$error = $return;
    				}
    				else {
    					$connection->commit();
	    				$message = 'OK';
    				}
    			}
    			catch (\Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
	    		$action = null;
    		}
    	}

    	$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
   				'properties' => $context->getConfig('commitment')['properties'],
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
		$view->setTerminal(true);
       	return $view;
    }
    
    public function updateAction()
    {
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the type
		$type = $this->params()->fromRoute('type', null);
		
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
    	if ($id) $commitment = Commitment::get($id);
    	else $commitment = Commitment::instanciate($type);

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	if ($action == 'delete') $message = 'confirm-delete';
    	elseif ($action) $message =  'confirm-update';
    	else $message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$message = null;
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check

    			// Load the input data
    			$commitment->loadDataFromRequest($request, $action);

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$commitment->id) $return = $commitment->add();
    				else $return = $commitment->update($request->getPost('update_time'));

    				if ($return != 'OK') {
    					$connection->rollback();
    					$error = $return;
    				}
    				else {
    					$connection->commit();
	    				$message = 'OK';
    				}
    			}
    			catch (\Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
	    		$action = null;
    		}
    	}

    	$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'accounts' => Account::getList(array(), 'customer_name', 'ASC'),
   				'properties' => $context->getConfig('commitment')['properties'],
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
		if ($context->isSpaMode()) $view->setTerminal(true);
       	return $view;
    }

    public function notifyAction()
    {
    	Commitment::notify();
    }

    public function deleteAction()
    {
		// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');

    	// Retrieve the current user
    	$context = Context::getCurrent();

    	// Retrieve the order
    	$commitment = Commitment::getTable()->get($id);

    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
       	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check

    			// Atomically delete the user and the role
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
	    				 
	    			// Delete order structure and workflow
	    			$rc = $commitment->delete($request->getPost('update_time'));

	    			if ($rc != 'OK') {
	    				$connection->rollback();
	    				$error = $rc;
	    			}
	    			else {
	    				$connection->commit();
	    				$message = 'OK';
	    			}
    			}    
    		    catch (Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    		}
    	}

    	return array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'id' => $id,
    		'commitment' => $commitment,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	);
    }
}
