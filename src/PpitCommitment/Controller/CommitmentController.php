<?php
namespace PpitCommitment\Controller;

use DateInterval;
use Date;
use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Account;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentTerm;
use PpitCommitment\Model\Subscription;
use PpitCommitment\Model\Term;
use PpitContact\Model\Vcard;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Credit;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use PpitDocument\Model\DocumentPart;
use PpitMasterData\Model\Product;
use PpitMasterData\Model\ProductOption;
use PpitUser\Model\User;
use PpitUser\Model\UserContact;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;

require_once('vendor/TCPDF-master/tcpdf.php');

class PpitPDF extends \TCPDF {
	public function Footer() {
		$context = Context::getCurrent();
		parent::Footer();
		$this->SetY(-10);
		$this->SetFont('helvetica', 'N', 8);
		if ($context->getConfig('headerParams')['footer']['type'] == 'text') $this->Cell(0, 5, $context->getConfig('headerParams')['footer']['value'], 0, false, 'C', 0, '', 0, false, 'T', 'M');
		else {
			$img = file_get_contents('http://localhost/~bruno/p-pit.fr/public/img/FM%20Sports/bas-page.jpg');
			$this->Image('@' . $img, 20, 270, '', '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		}
	}
}

class CommitmentController extends AbstractActionController
{	
	public function indexAction()
    {
    	$context = Context::getCurrent();
//		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$type = $this->params()->fromRoute('type', null);
		$applicationName = 'P-PIT Engagements';
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];

		$params = $this->getFilters($this->params());

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'active' => 'application',
    			'applicationName' => $applicationName,
    			'types' => $types,
    			'type' => $type,
    			'params' => $params,
	    		'products' => Product::getList(null, array()),
	    		'options' => ProductOption::getList(array()),
    	));
    }
	
	public function getFilters($params)
	{
		// Retrieve the query parameters
		$filters = array();

		$id = ($params()->fromQuery('id', null));
		if ($id) $filters['id'] = $id;

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

		$min_including_options_amount = ($params()->fromQuery('min_including_options_amount', null));
		if ($min_including_options_amount) $filters['min_including_options_amount'] = $min_including_options_amount;
		
		$max_including_options_amount = ($params()->fromQuery('max_including_options_amount', null));
		if ($max_including_options_amount) $filters['max_including_options_amount'] = $max_including_options_amount;

		$customer_name = ($params()->fromQuery('customer_name', null));
		if ($customer_name) $filters['customer_name'] = $customer_name;
		
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
   				'accounts' => Account::getList(null, $params, 'customer_name', 'ASC'),
   				'subscriptions' => Subscription::getList(array(), 'product_identifier', 'ASC'),
//   				'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
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
//   				'properties' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'],
//   				'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
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

    	$documentList = array();
    	if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
    		require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
    		$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    		$dropboxClient = new \Dropbox\Client($dropbox['credential'], $dropbox['clientIdentifier']);
    		try {
    			$properties = $dropboxClient->getMetadataWithChildren('/'.$dropbox['folders']['settlements']);
    			foreach ($properties['contents'] as $content) $documentList[] = substr($content['path'], strrpos($content['path'], '/')+1);
    		}
    		catch(\Exception $e) {}
    	}
    	else $dropbox = null;

    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'type' => $type,
    		'id' => $commitment->id,
    		'commitment' => $commitment,
    		'products' => Product::getList(null, array()),
    		'options' => ProductOption::getList(array()),
    		'dropbox' => $dropbox,
    		'documentList' => $documentList,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function tryAction()
    {
    	$context = Context::getCurrent();
    	$request = $this->getRequest();
    	// Log
    	$logText = "\t";
    	foreach ($request->getHeaders()->toArray() as $headerId => $headerValue) $logText .= $headerValue."\t";
    	$logText .= (($request->isPost() ? 'POST' : 'GET'))."\t";
    	foreach ($request->getPost()->toArray() as $postId => $postValue) $logText .= $postValue."\t";
		$logText .= "\n";
    	$writer = new Writer\Stream('data/log/commitment_try.txt');
    	$logger = new Logger();
    	$logger->addWriter($writer);
    	$logger->info($logText);
    	
    	$product = $this->params()->fromRoute('product', null);
    	$instance = Instance::instanciate();
    	$contact = Vcard::instanciate();
    	$user = User::getNew();
    	 
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check

       			// Retrieve the data from the request
    			$data = array();
    			$data['caption'] = $request->getPost('caption');
    			$data['is_active'] = 1;
    			$rc = $instance->loadData($data);
    			if ($rc != 'OK') throw new \Exception('View error');

    			$data = array();
    			$data['attributed_credits'] = array($product => true);
    			$data['n_title'] = $request->getPost('n_title');
    			$data['n_first'] = $request->getPost('n_first');
    			$data['n_last'] = $request->getPost('n_last');
    			$data['email'] = $request->getPost('email');
    			$data['tel_work'] = $request->getPost('tel_work');
    			$data['tel_cell'] = null;
    			$data['roles'] = array('admin' => true, 'manager' => true);
    			$data['is_notified'] = 1;
    			$rc = $contact->loadData($data);
    			if ($rc != 'OK') throw new \Exception('View error');
    			$rc = $user->loadData($request, $contact, $instance->id);
    			 
    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				// Add the instance, the main contact and the user
    				$rc = $instance->add();
    				
    				if ($rc != 'OK') {
	    				if ($rc == 'Duplicate') $error = 'Duplicate instance';
    					$connection->rollback();
    				}
    				else {
    					$contact->instance_id = $instance->id;
    					Vcard::getTable()->transSave($contact);
		    			$user->contact_id = $contact->id;
		    			$user->email = $contact->email;
    					$rc = $user->add($contact->email, true);
						$userContact = new UserContact;
						$userContact->instance_id = $instance->id;
						$userContact->user_id = $user->user_id;
						$userContact->contact_id = $contact->id;
						
    					if ($rc != 'OK') {
    						if ($rc == 'Duplicate') $error = 'Duplicate identifier';
    						else $error = $rc;
    						$connection->rollback();
    					}
    					else {
		    				mkdir('./public/img/'.$instance->caption);
    						$connection->commit();
		    				$message = 'OK';
	    				}
    				}
    			}
    			catch (\Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    		}
    	}

    	$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
    			'product' => $product,
    			'instance' => $instance,
    			'contact' => $contact,
    			'user' => $user,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
		$view->setTerminal(true);
       	return $view;
    }
    
    public function workflowAction()
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

    			// Retrieve the data from the request
    			$data = array();
    			foreach ($context->getConfig('commitment'.(($type) ? '/'.$type : ''))['actions'][$action]['properties'] as $propertyId => $unused) {
    				$data[$propertyId] =  $request->getPost($propertyId);
    			}
    			foreach ($context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $property) {
    				if ($property['type'] != 'file') $data[$propertyId] = $request->getPost($propertyId);
    			}
    			 
    			$data['update_time'] = $request->getPost('update_time');
    			 
    			// Change the status
    			if ($action && array_key_exists($action, $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['actions'])) {
    				$actionRules = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['actions'][$action];
    				if (array_key_exists('targetStatus', $actionRules)) $commitment->status = $actionRules['targetStatus'];
    			}
    			
    			// Retrieve the order form
    			$files = $request->getFiles()->toArray();
    			 
    			$rc = $commitment->loadData($data, $files);
    			if ($rc != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$commitment->id) {
    					if ($commitment->subscription_id) {
    						$subscription = $commitment->subscriptions[$commitment->subscription_id];
    						$commitment->description = $subscription->description;
    						$commitment->product_identifier = $subscription->product_identifier;
    						$commitment->unit_price = $subscription->unit_price;
    					}
    					$rc = $commitment->add();
    				}
    				else {
						if ($action == 'update') {
	    					// Retrieve the CGV
	    					$document = Document::getWithPath('home/public/resources/cgv');
	    					$document->retrieveContent();
	    					reset($document->parts);
	    					$commitment->cgv = current($document->parts)->content;
						}
	    				$rc = $commitment->update($request->getPost('update_time'));
    				}

    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
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
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'accounts' => Account::getList(null, array(), 'customer_name', 'ASC'),
   				'properties' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'],
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
		if ($context->isSpaMode()) $view->setTerminal(true);
       	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type', null);

    	// Retrieve the account
    	$account_id = $this->params()->fromQuery('account_id', null);
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
    
    			// Retrieve the data from the request
    			$data = array();
    			if (!$commitment->id) $data['account_id'] = $account_id;
    			foreach ($context->getConfig('commitment/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
					$property = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
					if ($property['type'] == 'file' && array_key_exists($propertyId, $request->getFiles()->toArray())) $files = $request->getFiles()->toArray()[$propertyId];
					else $data[$propertyId] = $request->getPost('commitment-'.$propertyId);
    			}
    
    			$rc = $commitment->loadData($data, $request->getFiles()->toArray());
    			if ($rc != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$commitment->id) {
    					if ($commitment->subscription_id) {
    						$subscription = $commitment->subscriptions[$commitment->subscription_id];
    						$commitment->description = $subscription->description;
    						$commitment->product_identifier = $subscription->product_identifier;
    						$commitment->unit_price = $subscription->unit_price;
    					}
    					$rc = $commitment->add();
    				}
	    			elseif ($action == 'delete') $rc = $commitment->delete($request->getPost(null /*'update_time'*/));
    				else {
    					$rc = $commitment->update(null/*$request->getPost('update_time')*/);
    				}
    
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
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
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'accounts' => Account::getList(null, array(), 'customer_name', 'ASC'),
    			'properties' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'],
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	if ($context->isSpaMode()) $view->setTerminal(true);
    	return $view;
    }

    public function updateProductAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the commitment
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$commitment = Commitment::get($id);
    
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
    
    			// Retrieve the data from the request
    			$data = array();
    			$data['product_identifier'] = $request->getPost('product_identifier');
    			$data['quantity'] = $request->getPost('quantity');
    			$data['unit_price'] = $request->getPost('unit_price');
    			$data['amount'] = $data['quantity'] * $data['unit_price'];
    			$product = Product::get($data['product_identifier'], 'reference');
    			if ($product->tax_1_share) $data['taxable_1_amount'] = round($data['amount'] * $product->tax_1_share, 2);
    			if ($product->tax_2_share) $data['taxable_2_amount'] = round($data['amount'] * $product->tax_2_share, 2);
    			if ($product->tax_3_share) $data['taxable_3_amount'] = round($data['amount'] * $product->tax_3_share, 2);
    			$rc = $commitment->loadData($data, $request->getFiles()->toArray());
    			if ($rc != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$rc = $commitment->update($request->getPost('update_time'));
    
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
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
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateOptionAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the commitment
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$commitment = Commitment::get($id);

    	// Retrieve the option id
    	$number = (int) $this->params()->fromRoute('number', 0);

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
    
    			// Retrieve the data from the request
    			$data = array();
    			$options = array();
    			for ($i = 0; $i < $number; $i++) {
    				if ($request->getPost('option_identifier-'.$i)) {
		    			$option = array();
	    				$option['identifier'] = $request->getPost('option_identifier-'.$i);
		    			$option['quantity'] = $request->getPost('option_quantity-'.$i);
		    			$option['unit_price'] = $request->getPost('option_unit_price-'.$i);
		    			$options[] = $option;
    				}
    			}
    			$data['options'] = $options;
    			$rc = $commitment->loadData($data, $request->getFiles()->toArray());
    			if ($rc != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$rc = $commitment->update($request->getPost('update_time'));
    
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
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
		return $this->response;
    }

    public function updateTermAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the commitment
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$commitment = Commitment::get($id);
    
    	// Retrieve the option id
    	$number = (int) $this->params()->fromRoute('number', 0);
    
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
    
    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				Term::getTable()->multipleDelete(array('commitment_id' => $commitment->id));
    				for ($i = 0; $i < $number; $i++) {
    					if ($request->getPost('term_amount-'.$i)) {
	    					// Retrieve the data from the request
	    					$data = array();
    						$data['commitment_id'] = $commitment->id;
	    					$data['caption'] = $request->getPost('term_caption-'.$i);
    						$data['due_date'] = $request->getPost('term_due_date-'.$i);
    						$data['means_of_payment'] = $request->getPost('term_means_of_payment-'.$i);
    						$data['status'] = $request->getPost('term_status-'.$i);
    						$data['amount'] = $request->getPost('term_amount-'.$i);
    						$data['document'] = $request->getPost('term_document-'.$i);
    						$term = Term::instanciate();
		    				$rc = $term->loadData($data, $request->getFiles()->toArray());
		    				if ($rc != 'OK') throw new \Exception('View error');
		    				$rc = $term->add();
    					}
    				}
    
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
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
    	return $this->response;
    }

    public function acceptAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Initialize the logger
    	$writer = new \Zend\Log\Writer\Stream('data/log/commitment.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);

    	// Retrieve the commitment id
    	$id = $this->params()->fromRoute('id', null);

    	// Submit the commitmentGet message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCommitment/P-Pit']['commitmentGetMessage']['url'].'/'.$id;
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    	 
    	$username = $context->getConfig()['ppitCommitment/P-Pit']['commitmentGetMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    	 
    	$commitmentData = json_decode($response->getContent(), true);
    	$commitment = new Commitment();
    	$commitment->exchangeArray($commitmentData);
    	if ($commitment->status == 'deleted' || $commitment->status == 'canceled') {
    		return $this->redirect()->toRoute('home');
    	}
    	 
    	// To be replaced by a call to a web service
    	$content = DocumentPart::getTable()->transGet($context->getConfig('documentPart/currentTerms'))->content;
    	 
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check

    			if ($request->getPost('accept')) {
	    			// Submit the postCommitment message
	    			$safe = $context->getConfig()['ppitUserSettings']['safe'];
	    			$url = $context->getConfig()['ppitCommitment/P-Pit']['commitmentPostMessage']['url'].'/'.$context->getInstance()->caption.'/'.$id;
	    			$client = new Client(
	    					$url,
	    					array(
	    							'adapter' => 'Zend\Http\Client\Adapter\Curl',
	    							'maxredirects' => 0,
	    							'timeout'      => 30,
	    					)
	    			);
	    			
	    			$username = $context->getConfig()['ppitCommitment/P-Pit']['commitmentListMessage']['user'];
	    			$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
	    			$client->setEncType('application/json');
	    			$client->setMethod('POST');
					$client->setRawBody(json_encode(array('n_fn' => $context->getFormatedName(), 'status' => 'approved', 'cgv' => $content)));
	    			$response = $client->send();
	
					// Write to the log
			   		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
			   			$logger->info('commitment/accept;'.$commitment->id.';'.$url.';'.$response->renderStatusLine());
			   		}
			   		if ($response->renderStatusLine() == 'HTTP/1.1 200 OK') $message = 'OK';
			   		else $error = $response->renderStatusLine();
    			}
    			else $error = 'Unchecked';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'commitment' => $commitment,
    			'content' => $content,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function invoiceAction(/*$commitment*/)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', null);
    	 
    	$id = $this->params()->fromRoute('id', null);
    	if (!$id) return $this->redirect()->toRoute('index');

    	$proforma = $this->params()->fromQuery('proforma', null);
    	
    	$commitment = Commitment::get($id);
    	$account = Account::get($commitment->account_id);
    	
    	// create new PDF document
    	$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    	
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-PIT');
    	$pdf->SetTitle('Invoice');
    	$pdf->SetSubject('TCPDF Tutorial');
    	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    	
    	// set default header data
    	$pdf->SetHeaderData('logos/'.$context->getConfig('headerParams')['advert'], $context->getConfig('headerParams')['advert-width']);
    	// set header and footer fonts
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
    	// set default monospaced font
    	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	
    	// set margins
    	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	 
    	// set auto page breaks
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	
    	// set image scale factor
    	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    	
    	// set some language-dependent strings (optional)
    	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    		require_once(dirname(__FILE__).'/lang/eng.php');
    		$pdf->setLanguageArray($l);
    	}
    	
    	// ---------------------------------------------------------
    	
    	/*
    	 NOTES:
    	 - To create self-signed signature: openssl req -x509 -nodes -days 365000 -newkey rsa:1024 -keyout tcpdf.crt -out tcpdf.crt
    	 - To export crt to p12: openssl pkcs12 -export -in tcpdf.crt -out tcpdf.p12
    	 - To convert pfx certificate to pem: openssl pkcs12 -in tcpdf.pfx -out tcpdf.crt -nodes
    	 */
    	
    	// set certificate file
    	$certificate = 'file://vendor/TCPDF-master/examples/data/cert/tcpdf.crt';
    	
    	// set additional information
    	$info = array(
    			'Name' => 'Invoice',
    			'Location' => 'Office',
    			'Reason' => 'Invoice',
    			'ContactInfo' => 'https://www.p-pit.fr',
    	);
    	
    	// set document signature
//    	$pdf->setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);
    	
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	
    	// add a page
    	$pdf->AddPage();
    	 
    	// Invoice header
    	$pdf->MultiCell(100, 5, '', 0, 'L', 0, 0, '', '', true);
    	$pdf->SetTextColor(0);
    	$pdf->SetFont('', '', 12);
    	$invoicingContact = ($account->contact_2) ? $account->contact_2 : $account->contact_1;
    	$addressee = $account->customer_name;
    	$addressee .= "\n";
    	if ($invoicingContact->n_title) $addressee .= $invoicingContact->n_title.' ';
    	if ($invoicingContact->n_last) $addressee .= $invoicingContact->n_last.' ';
    	if ($invoicingContact->n_first) $addressee .= $invoicingContact->n_first.' ';
    	if ($invoicingContact->adr_street) $addressee .= "\n".$invoicingContact->adr_street;
    	if ($invoicingContact->adr_extended) $addressee .= "\n".$invoicingContact->adr_extended;
    	if ($invoicingContact->adr_post_office_box) $addressee .= "\n".$invoicingContact->adr_post_office_box;
    	if ($invoicingContact->adr_zip) $addressee .= "\n".$invoicingContact->adr_zip;
    	if ($invoicingContact->adr_city) $addressee .= "\n".$invoicingContact->adr_city;
    	if ($invoicingContact->adr_state) $addressee .= "\n".$invoicingContact->adr_state;
    	if ($invoicingContact->adr_country) $addressee .= "\n".$invoicingContact->adr_country;
    	$pdf->MultiCell(80, 5, $addressee, 0, 'L', 0, 1, '', '', true);
    	$pdf->Ln();

    	// Title
    	if ($proforma) $text = '<div style="text-align: center"><strong>Facture proforma '.$commitment->invoice_identifier.'</strong></div>';
    	else $text = '<div style="text-align: center"><strong>Facture n° '.$commitment->invoice_identifier.'</strong></div>';
    	$pdf->writeHTML($text, true, 0, true, 0);
    	$pdf->Ln(10);
    	 
    	// Invoice references
		$pdf->SetFillColor(255, 255, 255);
//    	$pdf->SetTextColor(255);
    	$pdf->SetDrawColor(255, 255, 255);
//    	$pdf->SetDrawColor(128, 0, 0);
    	$pdf->SetLineWidth(0.2);
    	$pdf->SetFont('', '', 9);
    	if ($commitment->description) {
	    	$pdf->MultiCell(30, 5, '<strong>Description</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
	    	$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
	    	$pdf->MultiCell(145, 5, $commitment->description, 1, 'L', 0, 1, '' ,'', true);
    	}
    	if ($commitment->caption) {
	    	$pdf->MultiCell(30, 5, '<strong>Libellé</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
	    	$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
	    	$pdf->MultiCell(145, 5, $commitment->caption, 1, 'L', 0, 1, '' ,'', true);
    	}
    	$pdf->MultiCell(30, 5, '<strong>Date de facture</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
	   	$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
    	$pdf->MultiCell(145, 5, $context->decodeDate(date('Y-m-d')), 1, 'L', 0, 2, '' ,'', true);
    	 
    	// Invoice lines
    	$pdf->Ln(10);
    	$pdf->SetDrawColor(0, 0, 0);
    	$pdf->SetFillColor(0, 97, 105);
    	$pdf->SetFont('', '', 8);
//    	$pdf->SetFillColor(196, 196, 196);
    	$pdf->SetTextColor(255);
    	$currencySymbol = $context->getConfig('commitment/'.$type)['currencySymbol'];
    	$taxComputing = (($context->getConfig('commitment/'.$type)['tax'] == 'excluding') ? 'HT' : 'TTC');
    	$pdf->Cell(105, 7, 'Libellé', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'PU ('.$currencySymbol.' '.$taxComputing.')', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Quantité', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Montant ('.$currencySymbol.' '.$taxComputing.')', 1, 0, 'R', 1);
    	// Color and font restoration
    	$pdf->SetFillColor(239, 239, 239);
    	$pdf->SetTextColor(0);
    	// Data
    	$product = Product::get($commitment->product_identifier, 'reference');
    	$taxable1Amount = $commitment->taxable_1_amount;
    	$taxable2Amount = $commitment->taxable_2_amount;
    	$taxable3Amount = $commitment->taxable_3_amount;
    	$taxExemptAmount = $commitment->amount - $commitment->taxable_1_amount - $commitment->taxable_2_amount - $commitment->taxable_3_amount;
    	$color = 0;
    	if ($proforma) {
    		$pdf->Ln();
    		$pdf->Cell(105, 6, $product->caption, 'LR', 0, 'L', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($commitment->amount, 2), 'LR', 0, 'R', $color);
    		$pdf->Cell(25, 6, $commitment->quantity, 'LR', 0, 'C', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($commitment->amount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
    		$color = ($color+1)%2;
    	}
    	else {
	    	if ($commitment->taxable_1_amount != 0) {
	    		$pdf->Ln();
	    		$pdf->Cell(105, 6, $product->caption.' (TVA 20%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable1Amount, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(25, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable1Amount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($commitment->taxable_2_amount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(105, 6, $product->caption.' - '.$product->description.' (TVA 10%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable2Amount, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(25, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable2Amount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($commitment->taxable_3_amount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(105, 6, $product->caption.' - '.$product->description.' (TVA 5,5%)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable3Amount, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(25, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxable3Amount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
	        if ($taxExemptAmount != 0) {
	    		$pdf->Ln();
	        	$pdf->Cell(105, 6, $product->caption.' - '.$product->description.' (exonéré)', 'LR', 0, 'L', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxExemptAmount, 2), 'LR', 0, 'R', $color);
		    	$pdf->Cell(25, 6, $commitment->quantity, 'LR', 0, 'C', $color);
		    	$pdf->Cell(25, 6, $context->formatFloat($taxExemptAmount * $commitment->quantity, 2), 'LR', 0, 'R', $color);
	    		$color = ($color+1)%2;
	    	}
    	}
    	
    	foreach ($commitment->options as $option) {
    		$pdf->Ln();
    		$productOption = ProductOption::get($option['identifier'], 'reference');
    		if ($option['vat_id'] == 0) $taxCaption = ' (exonéré)';
    		elseif ($option['vat_id'] == 1) $taxCaption = ' (TVA 20%)';
    		elseif ($option['vat_id'] == 2) $taxCaption = ' (TVA 10%)';
    		elseif ($option['vat_id'] == 3) $taxCaption = ' (TV 5,5%)';
    		$pdf->Cell(105, 6, $productOption->caption.$taxCaption, 'LR', 0, 'L', $color);
    		$pdf->Cell(25, 6, $context->formatFloat($option['unit_price'], 2), 'LR', 0, 'R', $color);    		
    		$pdf->Cell(25, 6, $option['quantity'], 'LR', 0, 'C', $color);    		
    		$pdf->Cell(25, 6, $context->formatFloat($option['amount'], 2), 'LR', 0, 'R', $color);
    		$color = ($color+1)%2;
    		if ($option['vat_id'] == 1) $taxable1Amount += $option['amount'];
    		elseif ($option['vat_id'] == 2) $taxable2Amount += $option['amount'];
    		elseif ($option['vat_id'] == 3) $taxable3Amount += $option['amount'];
    	}

    	if ($taxComputing == 'including') {
    		$excludingTaxAmount = $commitment->including_options_amount;
    		$tax1Amount = round($taxable1Amount * 0.2, 2);
    		$tax2Amount = round($taxable2Amount * 0.1, 2);
    		$tax3Amount = round($taxable3Amount * 0.055, 2);
    		$taxAmount = $tax1Amount + $tax2Amount + $tax3Amount;
    		$includingTaxAmount = $excludingTaxAmount + $taxAmount;
    	}
    	else {
    		$includingTaxAmount = $commitment->including_options_amount;
    		$tax1Amount = $taxable1Amount - round($taxable1Amount / 1.2, 2);
    		$tax2Amount = $taxable2Amount - round($taxable2Amount / 1.1, 2);
    		$tax3Amount = $taxable3Amount - round($taxable3Amount / 1.055, 2);
    		$taxAmount = $tax1Amount + $tax2Amount + $tax3Amount;
    		$excludingTaxAmount = $includingTaxAmount - $taxAmount;
    	}

    	$pdf->Ln();
    	$pdf->Cell(180, 0, '', 'T');
    	$pdf->SetDrawColor(255, 255, 255);
    	if (!$proforma) {
    		$pdf->Ln();
    		$pdf->Cell(155, 6, 'Total HT :', 'LR', 0, 'R', false);
	    	$pdf->Cell(25, 6, $context->formatFloat($excludingTaxAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	if ($tax1Amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 20% sur '.$context->formatFloat($taxable1Amount, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($tax1Amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
	        if ($tax2Amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 10% sur '.$context->formatFloat($taxable2Amount, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($tax2Amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
	        if ($tax3Amount != 0) {
		    	$pdf->Ln();
		    	$pdf->Cell(155, 6, 'TVA 5,5% sur '.$context->formatFloat($taxable3Amount, 2).' :', 'LR', 0, 'R', false);
		    	$pdf->Cell(25, 6, $context->formatFloat($tax3Amount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
	    	}
    	}
    	$pdf->Ln();
    	$pdf->SetFont('', 'B');
    	$pdf->Cell(155, 6, 'Total TTC :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($includingTaxAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);

    	// Terms
	    $pdf->Ln(10);
	    $text = '<strong>Echéancier</strong>';
	    $pdf->writeHTML($text, true, 0, true, 0);
    	$pdf->Ln();
    	$pdf->SetDrawColor(0, 0, 0);
    	$pdf->SetFillColor(0, 97, 105);
    	$pdf->SetFont('', '', 8);
    	$pdf->SetTextColor(255);
    	$pdf->Cell(80, 7, 'Echéance', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Prévue le', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Statut', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Réglée le', 1, 0, 'C', 1);
    	$pdf->Cell(25, 7, 'Montant ('.$currencySymbol.' '.$taxComputing.')', 1, 0, 'R', 1);
    	// Color and font restoration
    	$pdf->SetFillColor(239, 239, 239);
    	$pdf->SetTextColor(0);
    	// Data
    	$terms = Term::getList(array('commitment_id' => $commitment->id), 'due_date', 'ASC', 'search');
    	$settledAmount = 0;
    	$color = 0;
    	foreach($terms as $term) {
    		if ($term->status == 'settled') $settledAmount += $term->amount;
	    	$pdf->Ln();
	    	$pdf->Cell(80, 6, $term->caption, 'LR', 0, 'L', $color);
	    	$pdf->Cell(25, 6, $context->decodeDate($term->due_date), 'LR', 0, 'C', $color);
	    	$status = $context->getConfig('commitmentTerm')['properties']['status']['modalities'][$term->status][$context->getLocale()];
	    	$pdf->Cell(25, 6, $status, 'LR', 0, 'C', $color);
	    	$pdf->Cell(25, 6, $context->decodeDate($term->settlement_date), 'LR', 0, 'C', $color);
	    	$pdf->Cell(25, 6, $context->formatFloat($term->amount, 2), 'LR', 0, 'R', $color);
	    	$color = ($color+1)%2;
    	}
    	$pdf->Ln();
    	$pdf->Cell(180, 0, '', 'T');

    	$pdf->Ln();
    	$pdf->SetDrawColor(255, 255, 255);
    	$pdf->Cell(155, 6, 'Total réglé :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($settledAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);

    	$pdf->Ln();
    	$pdf->SetDrawColor(255, 255, 255);
    	$pdf->Cell(155, 6, 'Restant dû :', 'LR', 0, 'R', false);
    	$pdf->Cell(25, 6, $context->formatFloat($includingTaxAmount - $settledAmount, 2).' '.$currencySymbol, 'LR', 0, 'R', false);
/*
    	if ($commitment->settlement_date) {
	    	$pdf->Ln(20);
    		$text = '<strong>Réglé le : '.$context->decodeDate($commitment->settlement_date, 2).'</strong>';
	    	$pdf->writeHTML($text, true, 0, true, 0);
	    	$pdf->Ln();
    		$text = '<strong>Vous n\'avez rien à payer</strong>';
	    	$pdf->writeHTML($text, true, 0, true, 0);
	    	$pdf->Ln();
    	}
    	else {
	    	// Bank account
	    	$pdf->Ln(20);
	    	$text = '<strong>Valeur en votre obligeant règlement : '.$context->formatFloat($commitment->tax_inclusive, 2).' €'.'</strong>';
	    	$pdf->writeHTML($text, true, 0, true, 0);
	    	$pdf->Ln();
	    	$text = '<strong>Par carte ou virement auprès de : Société Marseillaise de Crédit'.'</strong>';
	    	$pdf->writeHTML($text, true, 0, true, 0);
	    	$pdf->Ln();
	    	 
	    	$pdf->SetFont('', '', 8);
	    	$pdf->SetFillColor(196, 196, 196);
	    	$pdf->Cell(20, 7, 'Code banque', 1, 0, 'C', 1);
	    	$pdf->Cell(20, 7, 'Code agence', 1, 0, 'C', 1);
	    	$pdf->Cell(40, 7, 'Numéro de compte', 1, 0, 'C', 1);
	    	$pdf->Cell(15, 7, 'Clé RIB', 1, 0, 'C', 1);
	    	$pdf->Cell(30, 7, 'Domiciliation', 1, 0, 'C', 1);
	    	$pdf->Ln();
	    	$pdf->Cell(20, 6, '30077', 'LR', 0, 'L', false);
	    	$pdf->Cell(20, 6, '04193', 'LR', 0, 'L', false);
	    	$pdf->Cell(40, 6, '18222100200', 'LR', 0, 'L', false);
	    	$pdf->Cell(15, 6, '87', 'LR', 0, 'L', false);
	    	$pdf->Cell(30, 6, 'AVIGNON CRILLON', 'LR', 0, 'L', false);
	    	$pdf->Ln(10);
	    	$text = '<strong>IBAN : </strong>FR76 3007 7041 9318 2221 0020 087    <strong>Code BIC : </strong>SMCTFR2A';
	    	$pdf->writeHTML($text, true, 0, true, 0);
    	}*/
/*
    	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	// *** set signature appearance ***
    	
    	// create content for signature (image and/or text)
    	$pdf->Image('vendor/TCPDF-master/examples/images/tcpdf_signature.png', 180, 60, 15, 15, 'PNG');
    	
    	// define active area for signature appearance
    	$pdf->setSignatureAppearance(180, 60, 15, 15);
    	
    	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	
    	// *** set an empty signature appearance ***
    	$pdf->addEmptySignatureAppearance(180, 80, 15, 15);*/
    	    	 
    	// ---------------------------------------------------------
    	
    	// Close and output PDF document
    	// This method has several options, check the source code documentation for more information.
    	$document = Document::instanciate(0);
    	$document->type = 'application/pdf';
    	$document->add();
//    	$handle = fopen('data/documents/'.$document->id.'.pdf', 'I');
    	$content = $pdf->Output('data/documents/'.$document->id.'.pdf', 'I');
    	return $this->response;
    }
    
    public function settleAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    		$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    		$logger = new \Zend\Log\Logger();
    		$logger->addWriter($writer);
    	}

    	// Retrieve the commitment id
    	$id = $this->params()->fromRoute('id', null);

		// Submit the P-Pit get message
		$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCommitment/P-Pit']['commitmentGetMessage']['url'].'/'.$id;
		$client = new Client(
				$url,
				array(
						'adapter' => 'Zend\Http\Client\Adapter\Curl',
						'maxredirects' => 0,
						'timeout'      => 30,
				)
		);
		
		$username = $context->getConfig()['ppitCommitment/P-Pit']['commitmentGetMessage']['user'];
		$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
		$client->setEncType('text/xml');
		$client->setMethod('GET');
		$response = $client->send();
		
		$commitmentData = json_decode($response->getContent(), true);
		$commitment = new Commitment();
		$commitment->exchangeArray($commitmentData);
		if ($commitment->status == 'deleted' || $commitment->status == 'canceled') {
			return $this->redirect()->toRoute('home');
		}
	
    	$parm="merchant_id=080419959400024";
    	$parm="$parm merchant_country=fr";
    	$parm="$parm advert=advert.png";
    	$parm="$parm amount=".($commitment->tax_inclusive * 100);
    	$parm="$parm currency_code=978";
//    	$parm="$parm normal_return_url=".$this->url()->fromRoute('commitment/paymentResponse', array('id' => $id), array('force_canonical' => true));
//    	$parm="$parm cancel_return_url=".$this->url()->fromRoute('commitment/paymentResponse', array('id' => $id), array('force_canonical' => true));
    	$parm="$parm normal_return_url=".$this->url()->fromRoute('credit', array(), array('force_canonical' => true));
    	$parm="$parm cancel_return_url=".$this->url()->fromRoute('credit', array(), array('force_canonical' => true));
    	$parm="$parm automatic_response_url=".$this->url()->fromRoute('commitmentMessage/paymentAutoresponse', array('id' => $id), array('force_canonical' => true));

    	// Initialisation du chemin du fichier pathfile
    	$parm="$parm pathfile=".$context->getConfig()['ppit-payment']['pathfile'];

    	// Initialisation du chemin de l'exécutable response
    	$path_bin = $context->getConfig()['ppit-payment']['path_bin'].'request';
    	
    	//      Appel du binaire request
    	// La fonction escapeshellcmd() est incompatible avec certaines options avancées
    	// comme le paiement en plusieurs fois qui nécessite  des caractères spéciaux
    	// dans le paramètre data de la requête de paiement.
    	// Dans ce cas particulier, il est préférable d.exécuter la fonction escapeshellcmd()
    	// sur chacun des paramètres que l.on veut passer à l.exécutable sauf sur le paramètre data.
    	$parm = escapeshellcmd($parm);
    	$parm="$parm data='<USE_CSS>;https://www.p-pit.fr/css/default_sips_payment_perso.css</USE_CSS>; NO_COPYRIGHT; NO_WINDOWS_MSG;'";
    	$result=exec("$path_bin $parm");

    	//      sortie de la fonction : $result=!code!error!buffer!
    	//          - code=0    : la fonction génère une page html contenue dans la variable buffer
    	//          - code=-1   : La fonction retourne un message d'erreur dans la variable error
    	
    	//On separe les differents champs et on les met dans une variable tableau
    	$tableau = explode ("!", "$result");
    	//      récupération des paramètres
    	$code = (array_key_exists(1, $tableau)) ? $tableau[1] : null;
    	$error = (array_key_exists(2, $tableau)) ? $tableau[2] : null;
    	$message = (array_key_exists(3, $tableau)) ? $tableau[3] : null;
    	
    	//  analyse du code retour
    	if (( $code == "" ) && ( $error == "" ) )
    	{
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$logger->info("payment-autoresponse;;executable response non trouve $path_bin");
	    	}
    	}
    	
    	//      Erreur, affiche le message d'erreur
    	
    	else if ($code != 0){
    	   	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$logger->info("payment-autoresponse/$id;$code;$error");
	    	}
    	}

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'commitment' => $commitment,
    			'message' => $message,
    			'error' => $error,
    	));
    	return $view;
    }

    public function paymentResponseAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the commitment id
    	$id = $this->params()->fromRoute('id', null);
    	$commitment = Commitment::get($id);
    	$message="message=$_POST[DATA]";

    	// Initialisation du chemin du fichier pathfile
    	$pathfile='pathfile='.$context->getConfig('ppit-payment')['pathfile'];
    	 
    	// Initialisation du chemin de l'exécutable response
    	$path_bin = $context->getConfig('ppit-payment')['path_bin'].'response';

    	// Appel du binaire response
    	$message = escapeshellcmd($message);
    	$result=exec("$path_bin $pathfile $message");
    	
    	//      Sortie de la fonction : !code!error!v1!v2!v3!...!v29
    	//              - code=0        : la fonction retourne les données de la transaction dans les variables v1, v2, ...
    	//                              : Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
    	//              - code=-1       : La fonction retourne un message d'erreur dans la variable error
    	//      on separe les differents champs et on les met dans une variable tableau
    	$tableau = explode ("!", $result);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'commitment' => $commitment,
    			'tableau' => $tableau,
    	));
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
