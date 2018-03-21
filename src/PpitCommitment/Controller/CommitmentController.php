<?php
namespace PpitCommitment\Controller;

use DateInterval;
use Date;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentYear;
use PpitCommitment\Model\Subscription;
use PpitCommitment\Model\Term;
use PpitCommitment\ViewHelper\SsmlCommitmentViewHelper;
use PpitCommitment\ViewHelper\PdfInvoiceViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCommitment\ViewHelper\XmlUblInvoiceViewHelper;
use PpitCommitment\ViewHelper\XmlXcblOrderViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Credit;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Model\Document;
use PpitDocument\Model\DocumentPart;
use PpitMasterData\Model\Product;
use PpitMasterData\Model\ProductOption;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

require_once('vendor/TCPDF-master/tcpdf.php');

class CommitmentController extends AbstractActionController
{	
	public function getConfigProperties($type) {
		$context = Context::getCurrent();
		$properties = array();
		foreach($context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $property) {
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$properties[$propertyId] = $property;
		}
		return $properties;
	}

	public function getTermProperties() {
		$context = Context::getCurrent();
		$properties = array();
		foreach($context->getConfig('commitmentTerm')['properties'] as $propertyId => $property) {
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$properties[$propertyId] = $property;
		}
		return $properties;
	}

	public function indexAction()
    {
    	$context = Context::getCurrent();
//		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());

    	$type = $this->params()->fromRoute('type', null);
		$applicationId = 'p-pit-engagements';
		$applicationName = 'P-PIT Engagements';
		$instance = Instance::get($context->getInstanceId());
		$configProperties = $this->getConfigProperties($type);
		$termProperties = $this->getTermProperties($type);
		
/*    	$url = $context->getConfig()['ppitCommitment/P-Pit']['userGetApplicationsMessage']['url'];
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    	 
    	$username = $context->getConfig()['ppitCommitment/P-Pit']['userGetApplicationsMessage']['user'];
    	$client->setAuth($username, $context->getConfig()['ppitUserSettings']['safe'][$instance->caption][$username], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
		$applications = json_decode($response->getContent(), true);*/
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];

		$params = $this->getFilters($this->params(), $type);

    	return new ViewModel(array(
    			'context' => $context,
				'configProperties' => $configProperties,
				'termProperties' => $termProperties,
	    		'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
//    			'applications' => $applications,
    			'type' => $type,
    			'types' => $types,
    			'params' => $params,
	    		'products' => Product::getList(null, array()),
	    		'options' => ProductOption::getList(null, array()),
				'indexPage' => $context->getConfig('commitment/index/'.$type),
    			'searchPage' => $context->getConfig('commitment/search/'.$type),
				'listPage' => $context->getConfig('commitment/list/'.$type),
				'detailPage' => $context->getConfig('commitment/detail/'.$type),
				'updatePage' => $context->getConfig('commitment/update/'.$type),
    			'groupPage' => $context->getConfig('commitment/group/'.$type),
    			'termSearchPage' => $context->getConfig('commitmentTerm/search'),
				'termUpdatePage' => $context->getConfig('commitmentTerm/update'),
    	));
    }
	
	public function getFilters($params, $type)
	{
		// Retrieve the context
		$context = Context::getCurrent();
		
		// Retrieve the query parameters
		$filters = array();
		foreach ($context->getConfig('commitment/search'.(($type) ? '/'.$type : ''))['main'] as $propertyId => $unused) {
		
			$property = ($params()->fromQuery($propertyId, null));
			if ($property !== null) $filters[$propertyId] = $property;
			$min_property = ($params()->fromQuery('min_'.$propertyId, null));
			if ($min_property !== null) $filters['min_'.$propertyId] = $min_property;
			$max_property = ($params()->fromQuery('max_'.$propertyId, null));
			if ($max_property !== null) $filters['max_'.$propertyId] = $max_property;
		}
		$property = $params()->fromQuery('account_id');
		if ($property !== null) $filters['account_id'] = $property;

		return $filters;
	}
	
   	public function searchAction()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);
		$configProperties = $this->getConfigProperties($type);
		
   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'configProperties' => $configProperties,
   				'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
   				'subscriptions' => Subscription::getList(array(), 'product_identifier', 'ASC'),
//   				'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
   				'type' => $type,
    			'searchPage' => $context->getConfig('commitment/search/'.$type),
   		));
		$view->setTerminal(true);
       	return $view;
   	}

   	public function getList()
   	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', null);
		$params = $this->getFilters($this->params(), $type);
		$major = ($this->params()->fromQuery('major', 'including_options_amount'));
		$dir = ($this->params()->fromQuery('dir', 'DESC'));
		$configProperties = $this->getConfigProperties($type);
		
		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

		// Retrieve the list
		$commitments = Commitment::getList($type, $params, $major, $dir, $mode);
		$sum = 0;
		$distribution = array();
		foreach ($commitments as $commitment) {
			$majorSpecification = $configProperties[$major];
			if ($majorSpecification['type'] == 'number') $sum += $commitment->properties[$major];
			elseif ($majorSpecification['type'] == 'select') {
				if (array_key_exists($commitment->properties[$major], $distribution)) $distribution[$commitment->properties[$major]]++;
				else $distribution[$commitment->properties[$major]] = 1;
			}
		}
		$average = (count($commitments)) ? round($sum / count($commitments), 1) : null;
		
   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'configProperties' => $configProperties,
   				'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
   				'type' => $type,
//   				'properties' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'],
//   				'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
   				'commitments' => $commitments,
   				'mode' => $mode,
   				'params' => $params,
   				'major' => $major,
   				'dir' => $dir,
    			'count' => count($commitments),
    			'sum' => $sum,
    			'average' => $average,
				'distribution' => $distribution,
				'listPage' => $context->getConfig('commitment/list/'.$type),
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
   		$view = $this->getList();

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlCommitmentViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Engagements.xlsx ');
		$writer->save('php://output');
		return $this->response;
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
    		'type' => $type,
    		'id' => $commitment->id,
    		'commitment' => $commitment,
    		'products' => Product::getList(null, array('type' => $commitment->type, 'is_available' => true), null, null, 'search'),
    		'options' => ProductOption::getList(null, array('type' => $commitment->type, 'is_available' => true), null, null, 'search'),
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function dropboxLinkAction()
    {
    	$context = Context::getCurrent();
    	$document = $this->params()->fromRoute('document', 0);
    	$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    	$client = new Client(
    			'https://api.dropboxapi.com/2/files/get_temporary_link',
    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
    			);
    	$client->setEncType('application/json');
    	$client->setMethod('POST');
    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['settlements'].'/'.$document)));
    	$response = $client->send();
    	$this->response->http_status = $response->renderStatusLine();
    	$result = json_decode($response->getBody(), true);
    	if (is_array($result) && array_key_exists('link', $result)) return $this->redirect()->toUrl($result['link']);
    	else {
    		$this->response->http_status = 400;
    		return $this->response;
    	}
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
    	
//    	$product = $this->params()->fromRoute('product', null);
    	$instance = Instance::instanciate();
    	$contact = Vcard::instanciate();
    	$credit = Credit::instanciate();
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
    			$data['applications'] = array('p-pit-admin' => false, 'p-pit-engagements' => true);
    			$rc = $instance->loadData($data);
    			if ($rc != 'OK') throw new \Exception('View error');

    			$data = array();
    			$data['applications'] = array('p-pit-admin' => false, 'p-pit-engagements' => true);
    			$data['n_title'] = $request->getPost('n_title');
    			$data['n_first'] = $request->getPost('n_first');
    			$data['n_last'] = $request->getPost('n_last');
    			$data['email'] = $request->getPost('email');
    			$data['tel_work'] = $request->getPost('tel_work');
    			$data['tel_cell'] = null;
    			$data['roles'] = array('admin' => 'admin', 'sales_manager' => 'sales_manager');
    			$data['is_notified'] = 1;
    			$data['is_demo_mode_active'] = 1;
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
		    			$user->instance_id = $instance->id;
    					$user->vcard_id = $contact->id;
		    			$user->email = $contact->email;
    					$rc = $user->add($contact->email, true);
						$userContact = new UserContact;
						$userContact->instance_id = $instance->id;
						$userContact->user_id = $user->user_id;
						$userContact->vcard_id = $contact->id;
						UserContact::getTable()->transSave($userContact);

						$credit->instance_id = $instance->id;
						$credit->status = 'active';
						$credit->type = 'p-pit-engagements';
						$credit->quantity = 0;
						$credit->activation_date = date('Y-m-d');
						Credit::getTable()->transSave($credit);
						$credit->id = 0;
						$credit->type = 'p-pit-studies';
						Credit::getTable()->transSave($credit);

    					if ($rc != 'OK') {
    						if ($rc == 'Duplicate') $error = 'Duplicate identifier';
    						else $error = $rc;
    						$connection->rollback();
    					}
    					else {
							mkdir('public/logos/'.$instance->caption);
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

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type', null);
    	$configProperties = $this->getConfigProperties($type);
    	$updatePage = $context->getConfig('commitment/update/'.$type);

    	// Retrieve the account
    	$account_id = $this->params()->fromQuery('account_id', null);
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
    	if ($id) {
    		$commitment = Commitment::get($id);
    		$type = $commitment->type;
    	}
    	else $commitment = Commitment::instanciate($type);

    	$accounts = Account::getList(null, ['status' => 'committed,active'], '+name', null);
    	
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
				if (!$id) $data['type'] = $type;
    			foreach ($updatePage as $propertyId => $unused) {
					$property = $configProperties[$propertyId];
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
    					$commitment->credit_status = 'active';
    					$commitment->next_credit_consumption_date = date('Y-m-d', strtotime(date('Y-m-d').' + 31 days'));
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
				'configProperties' => $configProperties,
    			'config' => $context->getconfig(),
    			'type' => $commitment->type,
    			'id' => $id,
    			'action' => $action,
    			'accounts' => $accounts,
    			'properties' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'],
    			'commitment' => $commitment,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
    			'updatePage' => $updatePage,
    	));
    	$view->setTerminal(true);
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
    			$data['product_brand'] = $request->getPost('product_brand');
    			$data['product_caption'] = $request->getPost('product_caption');
    			$data['quantity'] = $request->getPost('quantity');
    			$data['unit_price'] = $request->getPost('unit_price');
    			$data['amount'] = round($data['quantity'] * $data['unit_price'], 2);
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

    public function groupAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');
    	$configProperties = $this->getConfigProperties($type);
    
    	$request = $this->getRequest();
    	if (!$request->isPost()) return $this->redirect()->toRoute('home');
    	$nbCommitment = $request->getPost('nb-commitment');
    
    	$commitments = array();
    	for ($i = 0; $i < $nbCommitment; $i++) {
    		$commitment = Commitment::get($request->getPost('commitment_'.$i));
    		$commitments[] = $commitment;
    	}
    	$input = $commitment;
    	$input->status = '';

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->getPost('action') == 'update') {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		if ($csrfForm->isValid()) { // CSRF check
    			$data = array();
    			foreach ($context->getConfig('commitment/group/'.$type) as $propertyId => $options) {
    				if ($request->getPost($propertyId.'_check')) $data[$propertyId] = $request->getPost($propertyId);
    			}
    			foreach ($commitments as $commitment) {
	    			// Atomically save
	    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if ($commitment->loadData($data) != 'OK') throw new \Exception('View error');
	    			    $terms = Term::getList(array('commitment_id' => $commitment->id));
	    				$commitment->id = null;
	    				$commitment->identifier = 0;
	    				$commitment->status = 'new';
	    				$rc = $commitment->add();
	    				if ($rc != 'OK') {
	    					$connection->rollback();
	    					$error = $rc;
	    				}
	    				else {
		    				foreach ($terms as $term) {
		    					$term->commitment_id = $commitment->id;
		    					$term->id = null;
		    					$term->status = 'expected';
		    					$rc = $term->add();
		    					if ($rc != 'OK') {
			    					$connection->rollback();
			    					$error = $rc;
			    					break;
			    				}
		    				}
		    				if (!$error) {
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
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'configProperties' => $configProperties,
    			'type' => $type,
    			'commitments' => $commitments,
    			'input' => $input,
    			'places' => Place::getList(array()),
    			'groupPage' => $context->getConfig('commitment/group/'.$type),
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    private function generateInvoice($type, $account, $commitment, $proforma = false)
    {
    	$context = Context::getCurrent();
    	
    	// Header
    	if ($commitment->account->place->getConfig('commitment/invoice_header')) $invoice['header'] = $commitment->account->place->getConfig('commitment/invoice_header');
    	else $invoice['header'] = $context->getConfig('commitment/invoice_header');
    	
    	$invoiceSpecs = $context->getConfig('commitment/invoice');
    	if ($account->type == 'business') $invoice['customer_invoice_name'] = $account->name;
    	$invoicingContact = null;
    	if ($account->contact_1_status == 'invoice') $invoicingContact = $account->contact_1;
    	elseif ($account->contact_2_status == 'invoice') $invoicingContact = $account->contact_2;
    	elseif ($account->contact_3_status == 'invoice') $invoicingContact = $account->contact_3;
    	elseif ($account->contact_4_status == 'invoice') $invoicingContact = $account->contact_4;
    	elseif ($account->contact_5_status == 'invoice') $invoicingContact = $account->contact_5;
    	 
    	if (!$invoicingContact) {
    		if ($account->contact_1_status == 'main') $invoicingContact = $account->contact_1;
    		elseif ($account->contact_2_status == 'main') $invoicingContact = $account->contact_2;
    		elseif ($account->contact_3_status == 'main') $invoicingContact = $account->contact_3;
    		elseif ($account->contact_4_status == 'main') $invoicingContact = $account->contact_4;
    		elseif ($account->contact_5_status == 'main') $invoicingContact = $account->contact_5;
    	}
    	if (!$invoicingContact) $invoicingContact = $account->contact_1;
    	
    	$invoice['customer_n_fn'] = '';
    	if ($invoicingContact->n_title || $invoicingContact->n_last || $invoicingContact->n_first) {
    		if ($invoicingContact->n_title) $invoice['customer_n_fn'] .= $invoicingContact->n_title.' ';
    		$invoice['customer_n_fn'] .= $invoicingContact->n_last.' ';
    		$invoice['customer_n_fn'] .= $invoicingContact->n_first;
    	}
    	if ($invoicingContact->adr_street) $invoice['customer_adr_street'] = $invoicingContact->adr_street;
    	if ($invoicingContact->adr_extended) $invoice['customer_adr_extended'] = $invoicingContact->adr_extended;
    	if ($invoicingContact->adr_post_office_box) $invoice['customer_adr_post_office_box'] = $invoicingContact->adr_post_office_box;
    	if ($invoicingContact->adr_zip) $invoice['customer_adr_zip'] = $invoicingContact->adr_zip;
    	if ($invoicingContact->adr_city) $invoice['customer_adr_city'] = $invoicingContact->adr_city;
    	if ($invoicingContact->adr_state) $invoice['customer_adr_state'] = $invoicingContact->adr_state;
    	if ($invoicingContact->adr_street) $invoice['customer_adr_country'] = $invoicingContact->adr_country;
    	
    	$invoice['identifier'] = ($proforma) ? null : $commitment->invoice_identifier;
    	$invoice['date'] = ($commitment->invoice_date) ? $commitment->invoice_date : date('Y-m-d');
    	$invoice['description'] = array();
    	foreach($invoiceSpecs['description'] as $line) {
    		$arguments = array();
    		foreach($line['params'] as $propertyId) {
    			if ($propertyId == 'date') $arguments[] = $context->decodeDate(date('Y-m-d'));
    			else {
    				if (array_key_exists($propertyId, $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'])) {
    					$property = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
    				}
    				else {
    					$property = $context->getConfig('commitment')['properties'][$propertyId];
    				}
    				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
    				if ($propertyId == 'account_name') $arguments[] = $commitment->account_name;
    				elseif ($propertyId == 'caption') $arguments[] = $commitment->caption;
    				elseif ($property['type'] == 'date') $arguments[] = $context->decodeDate($commitment->properties[$propertyId]);
    				elseif ($property['type'] == 'number') $arguments[] = $context->formatFloat($commitment->properties[$propertyId], 2);
    				elseif ($property['type'] == 'select' && array_key_exists($commitment->properties[$propertyId], $property['modalities'])) $arguments[] = $context->localize($property['modalities'][$commitment->properties[$propertyId]]);
    				else $arguments[] = $commitment->properties[$propertyId];
    			}
    		}
    		$value = vsprintf($context->localize($line['right']), $arguments);
    		if ($value) $invoice['description'][]  = array('title' => $context->localize($line['left']), 'value' => $value);
    	}
    	
    	$invoice['currency_symbol'] = $context->getConfig('commitment')['currencySymbol'];
    	$invoice['tax'] = $context->getConfig('commitment/'.$type)['tax'];
    	 
    	// Lines
    	
    	$product = Product::get($commitment->product_identifier, 'reference');
    	$taxExemptAmount = $commitment->amount - $commitment->taxable_1_amount - $commitment->taxable_2_amount - $commitment->taxable_3_amount;
    	if ($commitment->product_caption) $caption = $commitment->product_caption;
    	else $caption = $commitment->description;
    	 
    	$invoice['lines'] = array();
    	if ($proforma) {
    		$line = array();
    		$line['caption'] = $caption;
    		$line['unit_price'] = $commitment->unit_price;
    		$line['quantity'] = $commitment->quantity;
    		$line['amount'] = $commitment->amount;
    		$invoice['lines'][] = $line;
    	}
    	else {
	    	if ($commitment->taxable_1_amount != 0) {
	    		$line = array();
	    		$line['caption'] = $caption;
	    		$line['tax_rate'] = 0.2;
	    		$line['unit_price'] = round($commitment->taxable_1_amount / $commitment->quantity, 2);
	    		$line['quantity'] = $commitment->quantity;
	    		$line['amount'] = $commitment->taxable_1_amount;
	    		$invoice['lines'][] = $line;
	    	}
	    	if ($commitment->taxable_2_amount != 0) {
	    		$line = array();
	    		$line['caption'] = $caption;
	    		$line['tax_rate'] = 0.1;
	    		$line['unit_price'] = round($commitment->taxable_2_amount / $commitment->quantity, 2);
	    		$line['quantity'] = $commitment->quantity;
	    		$line['amount'] = $commitment->taxable_2_amount;
	    		$invoice['lines'][] = $line;
	    	}
	    	if ($commitment->taxable_3_amount != 0) {
	    		$line = array();
	    		$line['caption'] = $caption;
	    		$line['tax_rate'] = 0.055;
	    		$line['unit_price'] = round($commitment->taxable_3_amount / $commitment->quantity, 2);
	    		$line['quantity'] = $commitment->quantity;
	    		$line['amount'] = $commitment->taxable_3_amount;
	    		$invoice['lines'][] = $line;
	    	}
	    	if ($taxExemptAmount != 0) {
	    		$line = array();
	    		$line['caption'] = $caption;
	    		$line['tax_rate'] = 0;
	    		$line['unit_price'] = round($taxExemptAmount / $commitment->quantity, 2);
	    		$line['quantity'] = $commitment->quantity;
	    		$line['amount'] = $taxExemptAmount;
	    		$invoice['lines'][] = $line;
	    	}
    	}
	    	 
    	if (is_array($commitment->options)) foreach ($commitment->options as $option) {
    		$line['caption'] = $option['caption'];
    		if (!$proforma) {
	    		if ($option['vat_id'] == 0) $line['tax_rate'] = 0;
	    		elseif ($option['vat_id'] == 1) $line['tax_rate'] = 0.2;
	    		elseif ($option['vat_id'] == 2) $line['tax_rate'] = 0.1;
	    		elseif ($option['vat_id'] == 3) $line['tax_rate'] = 0.055;
    		}
    		$line['unit_price'] = $option['unit_price'];
    		$line['quantity'] = $option['quantity'];
    		$line['amount'] = $option['amount'];
    		$invoice['lines'][] = $line;
    	}
    	
    	// Sums
		if (!$proforma) {
	    	$invoice['excluding_tax'] = $commitment->excluding_tax;
	    	if ($commitment->tax_1_amount != 0) {
	    		$invoice['taxable_1_total'] = $commitment->taxable_1_total;
	    		$invoice['tax_1_amount'] = $commitment->tax_1_amount;
	    	}
	    	if ($commitment->tax_2_amount != 0) {
	    		$invoice['taxable_2_total'] = $commitment->taxable_2_total;
	    		$invoice['tax_2_amount'] = $commitment->tax_2_amount;
	    	}
	    	if ($commitment->tax_3_amount != 0) {
	    		$invoice['taxable_3_total'] = $commitment->taxable_3_total;
	    		$invoice['tax_3_amount'] = $commitment->tax_3_amount;
	    	}
		}
    	$invoice['tax_inclusive'] = $commitment->tax_inclusive;
    	 
    	// Terms

    	if (array_key_exists('terms', $invoiceSpecs)) $invoice['terms'] = array();
	    $settledAmount = 0;
	    foreach(Term::getList(array('commitment_id' => $commitment->id), 'due_date', 'ASC', 'search') as $term) {
	    	if ($term->status != 'expected') $settledAmount += $term->amount;
	    	if (array_key_exists('terms', $invoiceSpecs)) {
		    	$line[] = array();
	    		$line['caption'] = $term->caption;
	    		$line['status'] = ($term->status == 'collected') ? 'settled' : $term->status;
	    		$line['due_date'] = $term->due_date;
	    		$line['settlement_date'] = $term->settlement_date;
	    		$line['means_of_payment'] = $term->means_of_payment;
	    		$line['amount'] = $term->amount;
		    	$invoice['terms'][] = $line;
	    	}
		}

	    $invoice['settled_amount'] = $settledAmount;
	    $invoice['still_due'] = $commitment->tax_inclusive - $settledAmount;
    	
    	$invoice['tax_mention'] = $context->getConfig('commitment/invoice_tax_mention');
    	if ($commitment->status != 'settled') {
	    	if ($commitment->account->place->getConfig('commitment/invoice_bank_details')) $invoiceBankDetails = $commitment->account->place->getConfig('commitment/invoice_bank_details');
    		elseif ($context->getConfig('commitment/invoice_bank_details')) $invoiceBankDetails = $context->getConfig('commitment/invoice_bank_details');
    		else $invoiceBankDetails = null;
	    	if ($invoiceBankDetails) {
    			$invoice['bank_details'] = $invoiceBankDetails;
	    	}
    	}
		$invoice['footer_mention_1'] = $context->getConfig('commitment/invoice_footer_mention_1');
		$invoice['footer_mention_2'] = $context->getConfig('commitment/invoice_footer_mention_2');
		$invoice['footer_mention_3'] = $context->getConfig('commitment/invoice_footer_mention_3');
    	return $invoice;
    }
    
    public function invoiceAction()
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

				$type = $commitment->type;
				$account = $commitment->account;
				$commitment->computeHeader();
				$commitment->status = 'invoiced';
				$year = CommitmentYear::getcurrent($account->place_id);
				if (!$year) $year = CommitmentYear::instanciate(date('Y'));
				$mask = $context->getConfig('commitment/invoice_identifier_mask');
				$arguments = array();
				foreach ($mask['params'] as $param) {
					if ($param == 'year') $arguments[] = substr($commitment->invoice_date, 0, 4);
					elseif ($param == 'month') $arguments[] = substr($commitment->invoice_date, 5, 2);
					elseif ($param == 'counter') $arguments[] = sprintf("%'.03d", $year->next_value);
				}
				$commitment->invoice_identifier = vsprintf($context->localize($mask['format']), $arguments);
				$commitmentMessage = CommitmentMessage::instanciate('invoice');
				$invoice = $this->generateInvoice($type, $account, $commitment);
				
				$commitmentMessage->status = 'new';
				$commitmentMessage->account_id = $account->id;
				$commitmentMessage->identifier = $context->getInstance()->fqdn.'_'.$invoice['identifier'];
				$commitmentMessage->direction = 'O';
				$commitmentMessage->format = 'application/json';
				
    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$year->increment();
					$commitmentMessage->content = json_encode($invoice, JSON_PRETTY_PRINT);
				    $rc = $commitmentMessage->add();
    
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				if (!$error) {
    					$commitment->invoice_message_id = $commitmentMessage->id;
	    				$rc = $commitment->update($request->getPost('update_time'));
    					if ($rc != 'OK') {
	    					$connection->rollback();
	    					$error = $rc;
	    				}
    				}
    				if (!$error) {
    					if (array_key_exists('p-pit-finance', $context->getApplications())) $commitment->record('registration');
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
    	if ($error) echo $error."\n";
    	echo $message;
    	return $this->response;
    }

    public function xmlUblInvoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the commitment
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$commitment = Commitment::get($id);
    
    	$commitmentMessage = CommitmentMessage::get($commitment->commitment_message_id);
    	$xmlXcbl = new XmlXcblOrderViewHelper(new \SimpleXMLElement($commitmentMessage->content));

    	if ($commitment->change_message_id) {
    		$changeCommitmentMessage = Message::get($commitment->change_message_id);
    		$changeXmlXcbl = new XmlXcbl(new \SimpleXMLElement($changeCommitmentMessage->content));
    	}
    			 
    			$type = $commitment->type;
    			$commitment->computeHeader();
    			$commitment->status = 'invoiced';
    	
    			// Atomically save
    			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
			    				 
			    	// Format the XML UBL message
			    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
			    	$credentials = $context->getConfig()['ppitCommitment/P-Pit']['xmlUblInvoiceMessage'];
			    	$client = new Client(
			    			$credentials['url'],
			    			array(
			    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
			    					'maxredirects' => 0,
			    					'timeout'      => 30,
			    			)
			   		);
			    	$client->setAuth($credentials['user'], $safe[$credentials['edi']][$credentials['user']], Client::AUTH_BASIC);
			    	$client->setEncType('text/xml');
			    	$client->setMethod('POST');
			    
			    	$supplyerSheet = $context->getConfig('commitment/supplierIdentificationSheet');
			    
			    	$viewHelper = new XmlUblInvoiceViewHelper();
			    	$viewHelper->setID($commitment->invoice_identifier);
			    	$viewHelper->setIssueDate($commitment->invoice_date);
			    	if ($commitment->status == 'invoicing' || $commitment->status == 'invoiced') $viewHelper->setInvoiceTypeCode('Facture', 380);
			    	elseif ($commitment->status == 'credit-issued') $viewHelper->setInvoiceTypeCode('Avoir', 381);
			    	$viewHelper->setNote('N° de contrat: '.$commitment->identifier);
			    	$viewHelper->setLineCountNumeric($xmlXcbl->getNumberOfLines());
			    	if ($commitment->type == 'part_2') {
			    		$startDate = ($commitment->change_message_id && $changeXmlXcbl->getStartOfScheduleLineDate()) ? $changeXmlXcbl->getStartOfScheduleLineDate() : $xmlXcbl->getStartOfScheduleLineDate();
			    		$endDate = ($commitment->change_message_id && $changeXmlXcbl->getEndOfScheduleLineDate()) ? $changeXmlXcbl->getEndOfScheduleLineDate() : $xmlXcbl->getEndOfScheduleLineDate();
			    		$viewHelper->setInvoicePeriod($startDate, $endDate);
			    	}
			    	if ($commitment->status == 'credit-issued') $viewHelper->setBillingReference($commitment->invoice_identifier);
			    	$viewHelper->setContractDocumentReference(($commitment->change_message_id) ? $changeXmlXcbl->getIdentifier() : $xmlXcbl->getIdentifier(), 'Bon de commande');
			    	$viewHelper->setAccountingSupplierParty();
			    	$viewHelper->setAccountingCustomerParty($commitment);
			    	if ($commitment->change_message_id) {
				    	$viewHelper->setDelivery(null, $changeXmlXcbl->getNameAddressName, $changeXmlXcbl->getNameAddressCity, $changeXmlOrder->getShipToPartyPostalCode());
			    	}
			    	else {
			    		$viewHelper->setDelivery(null, $xmlXcbl->getNameAddressName(), $xmlXcbl->getNameAddressCity(), $xmlXcbl->getShipToPartyPostalCode());
			    	}
			    	$viewHelper->setPaymentMeans($commitment->settlement_date, null, $supplyerSheet['PayeeFinancialAccount']);
			    	$viewHelper->setPaymentTerms($supplyerSheet['PaymentTerms']);
			    	$viewHelper->setTaxTotal($commitment->tax_amount, $commitment->excluding_tax, 20, 'EUR');
			    	$viewHelper->setLegalMonetaryTotal($commitment->excluding_tax, ($commitment->change_message_id) ? $changeXmlXcbl->getTaxExclusive() : $xmlXcbl->getTaxExclusive(), $commitment->tax_inclusive, $commitment->tax_inclusive, 'EUR');
			    
			    	for ($i = 0; $i < $xmlXcbl->getNumberOfLines(); $i++) {
			    		$viewHelper->addInvoiceLine(
			    				$i+1,
			    				$xmlXcbl->getLineTotalQuantity($i),
			    				$xmlXcbl->getLineItemTotal($i),
			    				'EUR',
			    				($commitment->type == ('part_2')) ? $endDate : $commitment->commissioning_date,
			    				round($xmlXcbl->getLineItemTotal($i) * 20 / 100, 2),
			    				$xmlXcbl->getLineItemTotal($i),
			    				'TVA',
			    				$xmlXcbl->getLineItemTotal($i),
			    				$xmlXcbl->getLineCalculatedPriceBasisQuantity($i),
			    				'N° série : '.$commitment->product_identifier,
			    				'N° contrat : '.$commitment->identifier,
			    				'N° sales order : ',
			    				'Libellé produit : '.$commitment->product_caption,
			    				null,
			    				$xmlXcbl->getLineProductIdentifier($i),
			    				20,
			    				'VAT'
			    				);
			    	}
			    	// Save the message
			    	$invoiceMessage = CommitmentMessage::instanciate('INVOICE', $viewHelper->asXML());
			    	$invoiceMessage->identifier = $commitment->identifier;
			    	$invoiceMessage->add();
			    
			    	// Add the message id to the order
			    	$commitment->invoice_message_id = $invoiceMessage->id;
			    
			    	$viewHelper->setUUID($invoiceMessage->id);
			    	$invoiceMessage->content = $viewHelper->asXML();
			    	// Save the XBL Invoice and the commitment
			    	$invoiceMessage->update(null);
			    	$commitment->update(null);
			    	$connection->commit();
			    }
		    	catch (\Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
    	return $this->response;
    }
    
    public function settleAction()
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
    			 
    			$type = $commitment->type;
    			$commitment->status = 'settled';
    			$commitment->credit_status = 'closed';
    			 
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
    					$commitment->record('settlement');
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
	    				$option['caption'] = $request->getPost('option_caption-'.$i);
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

    public function suspendAction()
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
    
    			if ($commitment->credit_status == 'active') $commitment->credit_status = 'suspended';
    			elseif ($commitment->credit_status == 'suspended') $commitment->credit_status = 'active';

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

    public function serviceAddAction()
    {
    	$context = Context::getCurrent();
    	$writer = new Writer\Stream('data/log/service_add.txt');
    	$logger = new Logger();
    	$logger->addWriter($writer);
    
    	$instance_caption = $context->getInstance()->caption;
    	$type = 'service';
    
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$safeEntry = $safe[$instance_caption];
    	$username = null;
    	$password = null;
    
    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!array_key_exists($username, $safeEntry) || $password != $safeEntry[$username]) {
    		$logger->info('commitment/serviceAdd;'.$instance_caption.';401;'.$username.';');
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {
			$commitment = Commitment::instanciate('service');
			$contact_email = $this->request->getPost('email');

    		// Retrieve the data from the request
    		$data = array();
    		$account_identifier = $this->request->getPost('account_identifier');
    		$account = Account::get($account_identifier);
    		if (!$account) {
    			$logger->info('commitment/serviceAdd;'.$instance_caption.';400;'.'account_identifier: '.$this->request->getPost('account_identifier').';');
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    		
    		$data['credit_status'] = 'active';
    		$data['next_credit_consumption_date'] = date('Y-m-d', strtotime(date('Y-m-d').' + 31 days'));
    		$data['status'] = 'new';
    		$data['account_id'] = $account->id;
    		foreach ($context->getConfig('commitment/update/service') as $propertyId => $unused) {
    			if ($this->request->getPost($propertyId)) $data[$propertyId] = $this->request->getPost($propertyId);
    		}
    		// Retrieve the product
    		$data['product_identifier'] = $this->request->getPost('product_identifier');
    		$product = Product::get($data['product_identifier'], 'reference');
    	    if (!$product) {
    			$logger->info('commitment/serviceAdd;'.$instance_caption.';400;'.'product_identifier: '.$data['product_identifier'].';');
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    		$data['property_1'] = $product->property_1;
    		$data['property_2'] = $product->property_2;
    		$data['property_3'] = $product->property_3;
    		$data['property_4'] = $product->property_4;
    		$data['property_5'] = $product->property_5;
    		
    		// Retrieve the variant
    		$variant = $product->variants[$this->request->getPost('variant_identifier')];
    	   	if (!array_key_exists($this->request->getPost('variant_identifier'), $product->variants)) {
    			$logger->info('commitment/serviceAdd;'.$instance_caption.';400;'.'variant_identifier: '.$this->request->getPost('variant_identifier').';');
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    		$i = 10;
    		foreach ($context->getConfig('ppitProduct/service')['criteria'] as $variantId => $unused) {
    			$data['property_' + $i] = $variant[$variantId];
    			$i++;
    		}
    		$data['unit_price'] = $variant['price'];

    		$data['quantity'] = 1;
    		$data['amount'] = $data['unit_price'];
    		$data['taxable_1_amount'] = round($data['amount'] * $product->tax_1_share, 2);
    		$data['taxable_2_amount'] = round($data['amount'] * $product->tax_2_share, 2);
    		$data['taxable_3_amount'] = round($data['amount'] * $product->tax_3_share, 2);

	    	$including_options_amount = 0;
    		$data['options'] = array();
    		for ($i=0; $i < $this->request->getPost('number_of_options'); $i++) {
    			$option = ProductOption::get($this->request->getPost('option_identifier_'.$i), 'reference');
	    		if (!$option) {
	    			$logger->info('commitment/serviceAdd;'.$instance_caption.';400;'.'option_identifier: '.$data['option_identifier_'.$i].';');
	    			$this->getResponse()->setStatusCode('400');
	    			return $this->getResponse();
	    		}
    			$amount = round($option->variants[0]['price'] * $this->request->getPost('option_quantity_'.$i), 2);
				$including_options_amount += $amount;
	    		$data['options'][] = array(
    					'identifier' => $option->reference,
    					'caption' => $option->caption,
    					'unit_price' => $option->variants[0]['price'],
    					'quantity' => $this->request->getPost('option_quantity_'.$i),
    					'amount' => $amount,
    					'vat_id' => $option->vat_id,
    			);
    		}
    		
    		$rc = $commitment->loadData($data);
    		if ($rc != 'OK') {
		    		$logger->info('commitment/serviceAdd;'.$instance_caption.';500;');
    				$this->getResponse()->setStatusCode('500');
		    		return $this->getResponse();
    		}

    		// Atomically save
    		$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $commitment->add();
    		
    			if ($rc != 'OK') {
    				$connection->rollback();
    				$logger->info('commitment/serviceAdd;'.$instance_caption.';409;');
    				$this->getResponse()->setStatusCode('409');
		    		return $this->getResponse();
    			}
    			else {

    				for ($i=0; $i < $this->request->getPost('number_of_terms'); $i++) {
    					$term = Term::instanciate($commitment->id);
    					$data = array(
    							'status' => ($this->request->getPost('term_status_'.$i)) ? $this->request->getPost('term_status_'.$i) : 'expected',
    							'caption' => ($this->request->getPost('term_caption_'.$i)) ? $this->request->getPost('term_caption_'.$i) : 'Echéance '.($i+1),
    							'due_date' => $this->request->getPost('term_date_'.$i),
    							'amount' => $this->request->getPost('term_amount_'.$i),
    							'means_of_payment' => $this->request->getPost('term_means_of_payment_'.$i),
    					);
			    		$rc = $term->loadData($data);
			    		if ($rc != 'OK') {
					    		$logger->info('commitment/serviceAdd;'.$instance_caption.';500;');
			    				$this->getResponse()->setStatusCode('500');
					    		return $this->getResponse();
			    		}
    					$rc = $term->add();
		    			if ($rc != 'OK') {
		    				$connection->rollback();
		    				$logger->info('commitment/serviceAdd;'.$instance_caption.';409;');
		    				$this->getResponse()->setStatusCode('409');
				    		return $this->getResponse();
		    			}
    				}
    			}
		    	$connection->commit();
			    $logger->info('commitment/serviceAdd;'.$instance_caption.';200;'.$commitment->id);
			    $this->getResponse()->setStatusCode('200');
				return $this->getResponse();
    		}
    		catch (\Exception $e) {
    			$connection->rollback();
    			$logger->info('commitment/serviceAdd;'.$instance_caption.';500;');
    			$this->getResponse()->setStatusCode('500');
		    	return $this->getResponse();
    		}
    	}
    }

    public function downloadInvoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = $this->params()->fromRoute('id', null);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$commitment = Commitment::get($id);

    	$invoice = $this->generateInvoice($commitment->type, $commitment->account, $commitment, true);

    	// create new PDF document
    	$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    	PdfInvoiceViewHelper::render($pdf, $invoice, $commitment->account->place);
 
    	$content = $pdf->Output('invoice-'.$context->getInstance()->caption.'-'.$commitment->account->name.'.pdf', 'I');
    	return $this->response;
    }
    
    public function serviceSettleAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	if ($context->getConfig()['isTraceActive']) {
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
	
    	$parm="merchant_id=".$context->getConfig()['ppitUserSettings']['safe']['p-pit']['atos_merchant_id'];
		$parm="$parm merchant_country=fr";
    	$parm="$parm advert=advert.png";
    	$parm="$parm amount=".($commitment->tax_inclusive * 100);
    	$parm="$parm currency_code=978";
//    	$parm="$parm normal_return_url=".$this->url()->fromRoute('commitment/paymentResponse', array('id' => $id), array('force_canonical' => true));
//    	$parm="$parm cancel_return_url=".$this->url()->fromRoute('commitment/paymentResponse', array('id' => $id), array('force_canonical' => true));
    	$parm="$parm normal_return_url=".$this->url()->fromRoute('commitmentCredit', array(), array('force_canonical' => true));
    	$parm="$parm cancel_return_url=".$this->url()->fromRoute('commitmentCredit', array(), array('force_canonical' => true));
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
    		if ($context->getConfig()['isTraceActive']) {
    			$logger->info("payment-autoresponse;;executable response non trouve $path_bin");
	    	}
    	}
    	
    	//      Erreur, affiche le message d'erreur
    	
    	else if ($code != 0){
    	   	if ($context->getConfig()['isTraceActive']) {
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
    
    public function rephaseAction()
    {
    	$select = Commitment::getTable()->getSelect()->where(array('commitment.id > ?' => 0));
    	$cursor = Commitment::getTable()->selectWith($select);
    	foreach ($cursor as $commitment) {
    		$commitment->computeFooter();
    		$commitment->update(null);
    		echo $commitment->id.'<br>';
    	}
    }
}
