<?php
namespace PpitCommitment\Controller;

use DateInterval;
use Date;
use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Account;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\Subscription;
use PpitContact\Model\Vcard;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Credit;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use PpitDocument\Model\DocumentPart;
use PpitUser\Model\User;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\Log\Writer;
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
   				'accounts' => Account::getList(null, $params, 'customer_name', 'ASC'),
   				'subscriptions' => Subscription::getList(array(), 'product_identifier', 'ASC'),
   				'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
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

    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
   			'statuses' => $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['statuses'],
    		'type' => $type,
    		'id' => $commitment->id,
    		'commitment' => $commitment,
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
    			$data['attributed_credits'] = array('p-pit-studies' => null);  //$product; à rectifier
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
    	
    	require_once('vendor/TCPDF-master/tcpdf.php');
    	// create new PDF document
    	$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    	
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-PIT');
    	$pdf->SetTitle('Invoice');
    	$pdf->SetSubject('TCPDF Tutorial');
    	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    	
    	// set default header data
    	$pdf->SetHeaderData('advert.png', '160');
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
    	$pdf->setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);
    	
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	
    	// add a page
    	$pdf->AddPage();
    	
    	// print a line of text
    	$text = 'This is a <b color="#FF0000">digitally signed document</b> using the default (example) <b>tcpdf.crt</b> certificate.<br />To validate this signature you have to load the <b color="#006600">tcpdf.fdf</b> on the Arobat Reader to add the certificate to <i>List of Trusted Identities</i>.<br /><br />For more information check the source code of this example and the source code documentation for the <i>setSignature()</i> method.<br /><br /><a href="http://www.tcpdf.org">www.tcpdf.org</a>';
    	$pdf->writeHTML($text, true, 0, true, 0);
    	
    	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	// *** set signature appearance ***
    	
    	// create content for signature (image and/or text)
    	$pdf->Image('vendor/TCPDF-master/examples/images/tcpdf_signature.png', 180, 60, 15, 15, 'PNG');
    	
    	// define active area for signature appearance
    	$pdf->setSignatureAppearance(180, 60, 15, 15);
    	
    	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	
    	// *** set an empty signature appearance ***
    	$pdf->addEmptySignatureAppearance(180, 80, 15, 15);
    	    	 
    	// ---------------------------------------------------------
    	
    	// Close and output PDF document
    	// This method has several options, check the source code documentation for more information.
    	$document = Document::instanciate(0);
    	$document->type = 'application/pdf';
//    	$document->add();
//    	$handle = fopen('data/documents/'.$document->id.'.pdf', 'w');
    	$content = $pdf->Output(null, 'I');
//    	fwrite($handle, $content);
//    	fclose($handle);
/*
		$account = Account::get($commitment->account_id);
		$community = Community::get($account->customer_community_id);
		$contact = Vcard::get($community->contact_1_id);
		$community_name = $community->name;
		$contact_adr_street = $contact->adr_street;
		$contact_adr_zip = $contact->adr_zip;
		$contact_adr_city = $contact->adr_city;
		$invoice_identifier = $commitment->invoice_identifier;
		$commitment_date = $commitment->commmitment_date;
		$commitment_caption = $commitment->caption;
		$object = 'Location';
		$invoice_date = $commitment->invoice_date;
		$product = $commitment->product;
		$amount = $commitment->amount;
		$tax_amount = $commitment->tax_amount;
		$tax_inclusive = $commitment->tax_inclusive;
		$content = "
<br><br>
<table class='ppit ppit-address'>
  <tr>
    <td width='60%'>&nbsp;</td>
    <td><strong>$community_name</strong></td
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>$contact_adr_street</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>$contact_adr_zip $contact_adr_city</td>
  </tr>
</table>
<h3 class='ppit' align='center'>Facture $invoice_identifier</h3>
<br>
<table class='table ppit'>
  <tr>
    <td class='title'>Commande</td>
    <td>$commitment_date</td>
  </tr>
  <tr>
    <td class='title'>Référence</td>
    <td>$commitment_caption</td>
  </tr>
  <tr>
    <td class='title'>Objet</td>
    <td>$object</td>
  </tr>
  <tr>
    <td class='title'>Date de facture</td>
    <td>$invoice_date</td>
  </tr>
</table>
<br>
<table width='80%' class='ppit ppit-bill-rows'>
<tr>
  <th width='70%'>Libellé</th>
  <th width='30%'>Prix (€ HT)</th>
</tr>
<tr>
  <td>$product</td>
  <td align='right'>$amount</td>
</tr>
</table>
<table class='ppit ppit-bill-sums'>
  <tr>
    <td width='75%' align='right'>Total HT :</td>
    <td width='25%' align='right'>$amount €</td>
  </tr>
  <tr>
    <td align='right'>TVA 20% :</td>
    <td align='right'>$tax_amount €</td>
  </tr>
  <tr>
    <td align='right'><strong>Total TTC :</td>
    <td align='right'><strong>$tax_inclusive €</strong></td>
  </tr>
</table>
<h5 class='ppit'>Valeur en votre obligeant règlement : <strong>$tax_inclusive €</strong></h5>
<h5 class='ppit'>Par virement auprès de : <strong>Société Marseillaise de Crédit</strong></h5>
<table class='table ppit' style='width: 90%'>
  <tr>
    <th>Code banque</th>
    <th>Code agence</th>
    <th>Numéro de compte</th>
    <th>Clé RIB</th>
    <th>Domiciliation</th>
  </tr>
  <tr>
    <td>30077</td>
    <td>04193</td>
    <td>18222100200</td>
    <td>87</td>
    <td>AVIGNON CRILLON</td>
  </tr>
</table>
<h5 class='ppit'><strong>IBAN : FR76 3007 7041 9318 2221 0020 087&nbsp;&nbsp;&nbsp;&nbsp;Code BIC : SMCTFR2A</strong</h5>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>
<div class='ppit'>&nbsp;</div>";
    			$document = Document::instanciate(array($content));
    			$document->add();*/
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
