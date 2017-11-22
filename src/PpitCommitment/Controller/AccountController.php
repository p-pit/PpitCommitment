<?php

namespace PpitCommitment\Controller;

use PpitContact\Model\ContactMessage;
use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\PdfIndexCardViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Instance;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();

    	$templates = array();
    	foreach ($context->getConfig('commitmentAccount/sendMessage')['templates'] as $templateId => $template) {
    		if ($template['definition'] != 'inline') $template = $context->getConfig($template['definition']);
    		$templates[$templateId] = $template;
    	}

    	if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());
    	 
		$entry = $this->params()->fromRoute('entry', 'account');
    	$type = $this->params()->fromRoute('type', 'business');
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];

		if ($entry == 'contact') $status = 'new'; 
		else $status = 'active';
		
		$community_id = (int) $context->getCommunityId();
		$contact = Vcard::instanciate($community_id);

		$applicationId = 'p-pit-engagements';
		$applicationName = 'P-Pit Engagements';
		$currentEntry = $this->params()->fromQuery('entry', 'account');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationName' => $applicationName,
    			'applicationId' => $applicationId,
    			'community_id' => $community_id,
    			'types' => $types,
    			'contact' => $contact,
    			'currentEntry' => $currentEntry,
    			'entry' => $entry,
    			'type' => $type,
    			'templates' => $templates,
    			'status' => $status,
    	));
    }

    public function contactIndexAction()
    {
    	return $this->indexAction();
    }

    public function getFilters($params, $type)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('commitmentAccount/search'.(($type) ? '/'.$type : ''))['main'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}

    	foreach ($context->getConfig('commitmentAccount/search'.(($type) ? '/'.$type : ''))['more'] as $propertyId => $rendering) {
    	
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}
    	 
    	return $filters;
    }

    public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$entry = $this->params()->fromRoute('entry');
    	$type = $this->params()->fromRoute('type', null);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
				'places' => Place::getList(array()),
    			'entry' => $entry,
    			'type' => $type,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList($limitation = 300)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$entry = $this->params()->fromRoute('entry');
    	$type = $this->params()->fromRoute('type');
    	$status = $this->params()->fromQuery('status');

    	$params = $this->getFilters($this->params(), $type);
//    	if (!array_key_exists('min_closing_date', $params)) $params['min_closing_date'] = date('Y-m-d');

    	$major = ($this->params()->fromQuery('major', 'name'));
    	$dir = ($this->params()->fromQuery('dir'));
    
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
    
    	// Retrieve the list
    	$accounts = Account::getList($type, $entry, $params, $major, $dir, $mode, $limitation);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'accounts' => $accounts,
				'places' => Place::getList(array()),
    			'entry' => $entry,
    			'type' => $type,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function listAction()
    {
    	return $this->getList();
    }
    
    public function exportAction()
    {
    	$view = $this->getList(null);
    	 
   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';

   		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
   		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
   		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
   		 
		$workbook = new \PHPExcel;
		(new SsmlAccountViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Comptes.xlsx ');
		$writer->save('php://output');

    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function getAccountProperties($account)
    {
    	$data = $account->getProperties();
    	$data['contact_history'] = $account->contact_history;
    	$data['photo_link_id'] = $account->contact_1->photo_link_id;
    	$account->properties = $data;
    }

    public function groupAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');
    
    	$request = $this->getRequest();
    	if (!$request->isPost()) return $this->redirect()->toRoute('home');
    	$nbAccount = $request->getPost('nb-account');
    
    	$accounts = array();
    	for ($i = 0; $i < $nbAccount; $i++) {
    		$account = Account::get($request->getPost('account_'.$i));
    		$accounts[] = $account;
    	}
    	$input = Account::instanciate($type);
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
    			foreach ($context->getConfig('commitmentAccount/groupUpdate'.(($type) ? '/'.$type : '')) as $propertyId => $options) {
    				if ($request->getPost($propertyId)) $data[$propertyId] = $request->getPost($propertyId);
    			}
    			$input->loadData($data);
    			foreach ($accounts as $account) {
    				if ($account->loadData($data) != 'OK') throw new \Exception('View error');
    				$account->update($request->getPost('update_time'));
    			}
    		}
    	}
		$input->getProperties();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'accounts' => $accounts,
    			'input' => $input,
    			'places' => Place::getList(array()),
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function sendMessageAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute(0);

    	$templates = array();
    	foreach ($context->getConfig('commitmentAccount/sendMessage')['templates'] as $templateId => $template) {
    		if ($template['definition'] != 'inline') $template = $context->getConfig($template['definition']);
    		$templates[$templateId] = $template;
    	}
    	$signature = $context->getConfig('commitmentAccount/sendMessage')['signature'];
    	if ($signature['definition'] != 'inline') $signature = $context->getConfig($signature['definition']);
    	
    	$mail = ContactMessage::instanciate();
    	$mail->type = 'email';
    	$mail->subject = $context->getConfig('commitmentAccount/sendMessage/generic')['subject'][$context->getLocale()];
    	$mail->body = $context->getConfig('commitmentAccount/sendMessage/generic')['body'][$context->getLocale()];

    	$documentList = array();
    	if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
	    	$dropbox = $context->getConfig('ppitDocument')['dropbox'];
	    	$client = new Client(
	    			'https://api.dropboxapi.com/2/files/list_folder',
	    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
	    			);
	    	$client->setEncType('application/json');
	    	$client->setMethod('POST');
	    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
	    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['contact'])));
	    	$response = $client->send();
	    	foreach (json_decode($response->getBody(), true)['entries'] as $entry) {
	    		$documentList[] = $entry['name'];
	    	}
    	}
    	else $dropbox = null;

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$selectedTemplateId = null;
    	$body = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$nbAccount = $request->getPost('nb-account');
    		$accounts = array();
    		for ($i = 0; $i < $nbAccount; $i++) {
    			$account = Account::get($request->getPost('account_'.$i));
    			$accounts[] = $account;
    			
    			$data = array();
    			$data['type'] = 'email';
    			$data['to'] = array();
    			$data['cci'] = array();
    			foreach($accounts as $account) {
    				if ($account->email) {
    					if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->email;
    					else $data['to'][$account->email] = $account->email;
    				}
    			    if ($account->email_2) {
    					if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->email_2;
    					else $data['to'][$account->email_2] = $account->email_2;
    				}
    			    if ($account->email_3) {
    					if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->email_3;
    					else $data['to'][$account->email_3] = $account->email_3;
    				}
    			    if ($account->email_4) {
    					if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->email_4;
    					else $data['to'][$account->email_4] = $account->email_4;
    				}
    			    if ($account->email_5) {
    					if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->email_5;
    					else $data['to'][$account->email_5] = $account->email_5;
    				}
    			}
    			if (array_key_exists('cci', $context->getConfig('community/sendMessage'))) $data['cci'][$context->getConfig('community/sendMessage')['cci']] = $context->getConfig('community/sendMessage')['cci'];
    			$selectedTemplateId = $request->getPost('template_id');
    			$data['subject'] = $request->getPost($selectedTemplateId.'_subject');
    			$data['from_mail'] = $context->getConfig('community/sendMessage')['from_mail'];
    			$data['from_name'] = $context->getConfig('community/sendMessage')['from_name'];
    			$attachment = $request->getPost('attachment');
				$url = $this->getServiceLocator()->get('viewhelpermanager')->get('url');
   				$link = $url('commitmentAccount/dropboxLink', array('document' => $attachment), array('force_canonical' => true));
    			$body = sprintf($request->getPost($selectedTemplateId.'_body'), $link);
    			$body .= $signature['body'][$context->getLocale()];
    			$data['body'] = $body;
    			
    			if ($mail->loadData($data) != 'OK') throw new \Exception('View error');
    			
    			// Atomicity
    			$connection = ContactMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$rc = $mail->add();
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
    		}
    	}

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'templates' => $templates,
    			'selectedTemplateId' => $selectedTemplateId,
    			'body' => $body,
    			'signature' => $signature,
    			'mail' => $mail,
	    		'documentList' => $documentList,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function passwordRequestAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
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
	    		$nbAccount = $request->getPost('nb-account');
	    		$accounts = array();
	    		for ($i = 0; $i < $nbAccount; $i++) {
	    			$account = Account::get($request->getPost('account_'.$i));
	    			$accounts[] = $account;
	    			$user = User::get($account->contact_1_id, 'vcard_id');
	    			if (!$user) {
	    				$user = User::instanciate();
	    				$username = strtolower(substr($account->n_first, 0, 1).$account->n_last);
	    				$user->username = $username;
	    				for($i = 1; true; $i++) {
	    					$existingUser = User::getTable()->transGet($user->username, 'username');
	    					if (!$existingUser) break;
	    					else $user->username = $username.$i;
	    				}
						$user->vcard_id = $account->contact_1_id;
						$user->add(false, false);
						$userContact = UserContact::instanciate();
						$userContact->user_id = $user->user_id;
						$userContact->vcard_id = $account->contact_1_id;
						$userContact->add();
	    			}
	    			if (!$error) {
		    			$password_init_token = $context->getSecurityAgent()->requestPasswordInit($user, false);
		    			 
		    			// Insert a mail in the queue
		    			$contact = Vcard::getTable()->transGet($user->vcard_id);
		    			$mail = ContactMessage::instanciate();
		    			$mail->type = 'email';
		    			 
		    			$data = array();
		    			$data['to'] = array();
		    			if ($account->email) $data['to'][$account->email] = $account->email;
		    			if ($account->email_2) $data['to'][$account->email_2] = $account->email_2;
		    			if ($account->email_3) $data['to'][$account->email_3] = $account->email_3;
		    			if ($account->email_4) $data['to'][$account->email_4] = $account->email_4;
		    			if ($account->email_5) $data['to'][$account->email_5] = $account->email_5;
		    			$data['cci'] = array();
		    			if (array_key_exists('cci', $context->getConfig('community/sendMessage'))) $data['cci'][$context->getConfig('community/sendMessage')['cci']] = $context->getConfig('community/sendMessage')['cci'];
		    			$selectedTemplateId = $request->getPost('template_id');
		    			$data['subject'] = $context->getConfig()['ppitUserSettings']['messages']['addTitle'][$context->getLocale()];
		    			$data['from_mail'] = $context->getConfig('community/sendMessage')['from_mail'];
		    			$data['from_name'] = $context->getConfig('community/sendMessage')['from_name'];
		    			$body = $context->getconfig()['ppitUserSettings']['messages']['addText'][$context->getLocale()];
						$url = $this->getServiceLocator()->get('viewhelpermanager')->get('url');
		   				$link = $url('user/initpassword', array('id' => $user->user_id), array('force_canonical' => true)).'?hash='.$password_init_token;
		    			$body = sprintf($body, $user->username, $link);
		    			$data['body'] = $body;
		    			if ($mail->loadData($data) != 'OK') throw new \Exception('View error');
		    			$rc = $mail->add();
	    			}
	    		}
    			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
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
    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['contact'].'/'.$document)));
    	$response = $client->send();
    	$this->response->http_status = $response->renderStatusLine();
    	$result = json_decode($response->getBody(), true);
    	if (is_array($result) && array_key_exists('link', $result)) return $this->redirect()->toUrl($result['link']);
    	else {
	    	$this->response->http_status = 400;
    		return $this->response;
    	}
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', 0);

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);
    	$this->getAccountProperties($account);
    	if (!$type) $type = $account->type;
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $account->id,
    			'account' => $account,
    			'customer_community_id' => $account->customer_community_id,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the entry and type
    	$type = $this->params()->fromRoute('type');

    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromQuery('act', null);
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);
    	$this->getAccountProperties($account);

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
    			if ($action != 'delete') {

    				if ($account->id) {
						// Unlink the current place community for the account type
						$place = Place::get($account->place_id);
						$community = Community::get($account->type.'/'.$place->identifier, 'identifier');
						if ($place && array_key_exists($community->id, $account->contact_1->communities)) {
	    					unset($account->contact_1->communities[$community->id]);
	    				}
    				}
    				
    				$data = array();
					foreach ($context->getConfig('commitmentAccount/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
						$property = $context->getConfig('commitmentAccount'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
						if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
						if ((!array_key_exists('readonly', $property) || $property['readonly']) && $property['type'] != 'title') {
							if ($property['type'] == 'photo' && array_key_exists($propertyId, $request->getFiles()->toArray())) $data['file'] = $request->getFiles()->toArray()[$propertyId];
							else $data[$propertyId] = $request->getPost($propertyId);
						}
					}
					if ($type) $data['credits'] = array($type => true);

					// Add the main contact
					if (!$account->contact_1) $account->contact_1 = Vcard::instanciate();
					if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');
					if ($account->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');
    			
					// Link to the place community for the account type
					$place = Place::get($account->place_id);
    				$community = Community::get($account->type.'/'.$place->identifier, 'identifier');
    				if ($place) {
    					$account->contact_1->communities[$community->id] = true;
    				}
    			}

				if (!$error) {
	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if (!$account->contact_1_id) {
	    					$account->contact_1 = Vcard::optimize($account->contact_1);
	    					$account->contact_1_id = $account->contact_1->id;
	    				}
	    				if (!$account->id) {
	    					$account->add();
	    				}
	    				elseif ($action == 'delete') {
    						$return = $account->delete($request->getPost('update_time'));
    						if ($return != 'OK') $error = $return;
	    				}
	    				else {

	    					// Save the contact
	    					if (array_key_exists('file', $data)) {
								$account->contact_1->savePhoto($data['file']);
								$account->contact_1->photo_link_id = null;
							}
	    					$return = $account->contact_1->update($account->contact_1->update_time);
	    					if ($return != 'OK') $error = $return;
	    					else {
	    						$return = $account->update($request->getPost('update_time'));
	    						if ($return != 'OK') $error = $return;
		    				}
	    				}
	    				if ($error) $connection->rollback();
	    				else {
	    					$connection->commit();
	    					$message = 'OK';
    						$this->getAccountProperties($account);
	    				}
	    			}
	    			catch (\Exception $e) {
	    				$connection->rollback();
	    				throw $e;
	    			}
	    			$action = null;
				}
    		}
    	}
    	$account->properties = $account->getProperties();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'account' => $account,
				'places' => Place::getList(array()),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateUserAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');
    
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
    	if (!$id) return $this->redirect()->fromRoute('home'); 
    	
    	// Retrieve the account
    	$account = Account::get($id);
    	$account->user = User::get($account->contact_1_id, 'vcard_id');
    	if ($account->user) {
    		$account->username = $account->user->username;
    		$new_password = null;
    	}
    	else {
    		$account->user = User::instanciate();
    		$username = strtolower(substr($account->n_first, 0, 1).$account->n_last);
    		$account->user->username = $username;
    		for($i = 1; true; $i++) {
    			$existingUser = User::getTable()->transGet($account->user->username, 'username');
    			if (!$existingUser) break;
    			else $account->user->username = $username.$i;
    		}
    		$account->username = $account->user->username;

	    	// Generate the new password
	    	$new_password = '';
	    	$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	    		
	    	for($i = 0; $i < 6; $i++)
	    	{
	    		$new_password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
	    	}
    	}

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
    			$place = Place::get($account->place_id);
    			$community = Community::get('p-pit-studies/'.$place->identifier, 'identifier');

    			// Load the input data
    			$data = array();
    			$data['communities'] = array($community->id => true);
    			$data['roles'] = array();
    			$data['perimeters'] = array();
    			if ($type) $data['credits'] = array($type => true);
    			$account->username = $request->getPost('username');
    			$data['username'] = $account->username;
    			$data['state'] = $request->getPost('state');
    			$data['is_notified'] = $request->getPost('is_notified');
    			$new_password = $request->getPost('new_password');
    			$data['new_password'] = $new_password;
    			$data['locale'] = $request->getPost('locale');
    			$data['is_demo_mode_active'] = false;
 
    			if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');

					// Load the user data
					$rc = $account->user->loadData($request, $account->contact_1);
					if ($rc == 'Integrity') throw new \Exception('View error');
					elseif ($rc == 'Duplicate') $error = 'Duplicate user';
					$account->user->state = $data['state'];

    				if (!$error) {
    				// Atomically save
    				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    					$account->contact_1->update($account->contact_1->update_time);
    					if (!$account->user->user_id) {
    						// Create a new user
    						$user = User::getNew();
    						$account->user = $user;
    						$user->username = $data['username'];
    						$user->vcard_id = $account->contact_1_id;
    						if ($account->is_notified && !$data['new_password']) {
    							$rc = $user->add(false, true);
    						}
    						else $rc = $user->add(false, false);
    						if ($rc != 'OK') $error = $rc;
    						$userContact = UserContact::instanciate();
    						$userContact->user_id = $user->user_id;
    						$userContact->vcard_id = $account->contact_1_id;
    						$userContact->add();
    						$account->username = $user->username;
    					}
    					if (!$error) {
    						$rc = $account->user->update(null);
    						if ($rc != 'OK') $error = $rc;
    					}
    					if (!$error) {
	    					if ($data['new_password']) {
	    						$account->user->new_password = $data['new_password'];
	    						if ($rc != 'OK') $error = $rc;
	    						else $context->getSecurityAgent()->changePassword($account->user, $account->user->username, null, $account->user->new_password, null);
	    					}
    					}
    					if ($error) $connection->rollback();
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
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'account' => $account,
    			'new_password' => $new_password,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateContactAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$contactNumber = $this->params()->fromRoute('contactNumber', 0);
    	$account = Account::get($id);
    	$type = $account->type;
    	
    	if ($contactNumber == 'contact_1') {
    		if (!$account->contact_1) $account->contact_1 = Vcard::instanciate();
    		$contact = $account->contact_1;
    		$contact_status = $account->contact_1_status;
    	}
    	elseif ($contactNumber == 'contact_2') {
    		if (!$account->contact_2) $account->contact_2 = Vcard::instanciate();
    		$contact = $account->contact_2;
    		$contact_status = $account->contact_2_status;
    	}
    	elseif ($contactNumber == 'contact_3') {
    		if (!$account->contact_3) $account->contact_3 = Vcard::instanciate();
    		$contact = $account->contact_3;
    		$contact_status = $account->contact_3_status;
    	}
    	elseif ($contactNumber == 'contact_4') {
    		if (!$account->contact_4) $account->contact_4 = Vcard::instanciate();
    		$contact = $account->contact_4;
    		$contact_status = $account->contact_4_status;
    	}
    	elseif ($contactNumber == 'contact_5') {
    		if (!$account->contact_5) $account->contact_5 = Vcard::instanciate();
    		$contact = $account->contact_5;
    		$contact_status = $account->contact_5_status;
    	}

    	$action = $this->params()->fromRoute('act', null);
    
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
    			$data = array();
    			foreach ($context->getConfig('commitmentAccount/updateContact'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
					$property = $context->getConfig('vcard/properties')[$propertyId];
					$data[$propertyId] = $request->getPost($propertyId);
    			}

    			if ($contact->loadData($data) != 'OK') throw new \Exception('View error');
    			$contact = Vcard::optimize($contact);

    			$contact_status = $request->getPost('contact_status');
    			
    			// Atomically save
    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$contact->id) $rc = $contact->add();
    				elseif ($action == 'delete') $rc = $contact->delete($request->getPost('update_time'));
    				else $rc = $contact->update($contact->update_time);

    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				else {
    					if ($contactNumber == 'contact_1') {
							$contact_1_status = $account->contact_1_status = $contact_status;
							if ($contact_status == 'invoice') {
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
    						$account->contact_1_id = $contact->id;
    						$account->contact_1 = $contact;
    					}
    					elseif ($contactNumber == 'contact_2') {
							$account->contact_2_status = $contact_status;
    						if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_2_id = $contact->id;
    						$account->contact_2 = $contact;
    					}
    					elseif ($contactNumber == 'contact_3') {
							$account->contact_3_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_3_id = $contact->id;
    						$account->contact_3 = $contact;
    					}
    					elseif ($contactNumber == 'contact_4') {
							$account->contact_4_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_4_id = $contact->id;
    						$account->contact_4 = $contact;
    					}
    					elseif ($contactNumber == 'contact_5') {
							$account->contact_5_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
							}
							$account->contact_5_id = $contact->id;
    						$account->contact_5 = $contact;
    					}
    					$account->update($request->getPost('update_time'));

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
    	$contact->properties = $contact->toArray();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $id,
    			'contactNumber' => $contactNumber,
    			'action' => $action,
    			'account' => $account,
    			'contact' => $contact,
    			'contact_status' => $contact_status,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function indexCardAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the entry and type
    	$type = $this->params()->fromRoute('type');

    	$id = (int) $this->params()->fromRoute('id', 0);
		$account = Account::get($id);
		$account->properties = $account->getProperties();
		$place = Place::get($account->place->id);

		// create new PDF document
		$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		PdfIndexCardViewHelper::render($pdf, $place, $account);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$content = $pdf->Output('index-card-'.$context->getInstance()->caption.'-'.$account->name.'.pdf', 'I');
		return $this->response;
	}

    public function registerAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', null);

    	$account = Account::instanciate($type);
    
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
    			$data = array();
				foreach ($context->getConfig('commitmentAccount/register'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $unused) {
			    	$data[$propertyId] =  $request->getPost($propertyId);
				}
				$data['origine'] = 'web';

				if (!$account->name) $account->name = $account->n_last.', '.$account->n_first;

				// Add the main contact
				if (!$account->contact_1) $account->contact_1 = Vcard::instanciate();
				if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');
				if ($account->loadData($data) != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
	    			$account->contact_1 = Vcard::optimize($account->contact_1);
	    			$account->contact_1_id = $account->contact_1->id;
	    			$account->contact_1_status = 'main';
    				$return = $account->add();
    
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

		$this->layout('/layout/widget-layout');
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'account' => $account,
				'places' => Place::getList(array()),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	return $view;
    }

    public function contactFormAction()
	{
		$context = Context::getCurrent();
    	$place_identifier = $this->params()->fromRoute('place_identifier');
    	$place = Place::get($place_identifier, 'identifier');
    	$state_id = $this->params()->fromRoute('state_id');
    	$action_id = $this->params()->fromRoute('action_id');
    	$request = $this->getRequest();

    	$type = $this->params()->fromRoute('type');
		$template = $context->getConfig('commitmentAccount/contactForm/'.$type);
		if ($template['definition'] != 'inline') $template = $context->getConfig($template['definition']);

    	if ($state_id == 'index') {
	    	if ($request->isPost()) {
	    		return $this->redirect()->toRoute('commitmentAccount/contactForm', array('type' => $type, 'place_identifier' => $place_identifier, 'action_id' => $action_id, 'state_id' => 'state1'));
	    	}
    		$view = new ViewModel(array(
	    			'context' => $context,
	    			'config' => $context->getConfig(),
    				'template' => $template,
	    			'type' => $type,
	    			'place_identifier' => $place_identifier,
    				'place' => $place,
	    			'state_id' => $state_id,
	    	));
	    	$view->setTerminal(true);
	    	return $view;
    	}

    	$currentState = $template[$state_id];

    	$id = $this->params()->fromRoute('id');
    	if ($id) {
    		$account = Account::get($id);
    		if ($account->contact_1_id) $contact = Vcard::get($account->contact_1_id);
    		else $contact = Vcard::instanciate();
    	}
    	else {
    		$email = $request->getPost('email');
    		$account = null;
    		if ($email) {
    			$contact = Vcard::get($email, 'email');
    			if ($contact) $account = Account::get($contact->id, 'contact_1_id');
    			else $contact = Vcard::instanciate();
    		}
    		else $contact = Vcard::instanciate();
    		if (!$account) {
	    		$account = Account::instanciate($type);
	    		$account->place_id = $place->id;
	    		$account->opening_date = date('Y-m-d');
	    		$account->origine = 'inscription';
//	    		$account->property_1 = $action_id;
    		}
    	}
    
    	$applicationId = 'p-pit-contact';
    	$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
    	$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
    			$data = array();
    			$data[$template['index']['actions']['property']] = $action_id;
    			foreach ($currentState['sections'] as $sectionId => $section) {
    				foreach ($section['fields'] as $fieldId => $field) {
    					if ($field['type'] == 'repository') $field = $context->getConfig($field['definition']);
    
    					// Structured field
    					if ($field['type'] == 'structured') {
    						$fieldData = array();
    						foreach ($field['properties'] as $itemId => $item) {
    							if ($item['type'] == 'repeater') {
    								$repeater = array();
    								for ($i = 0; $i < 20; $i++) {
    									$line = array();
    									foreach ($item['properties'] as $propertyId => $property) {
    										if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
    										$label = $property['labels'][$context->getLocale()];
    										$value = $request->getPost(($i == 0) ? $propertyId : $propertyId.$i);
    										if ($value) {
    											if ($property['type'] == 'select') $value = $property['modalities'][$value][$context->getLocale()];
    											$line[$label] = $value;
    										}
    									}
    									if ($line) $repeater[] = $line;
    								}
    								$fieldData[] = $repeater;
    							}
    							else {
    								if ($item['type'] == 'repository') $item = $context->getConfig($item['definition']);
    								$value = $request->getPost($itemId);
    								if ($item['type'] == 'select') $value = $item['modalities'][$value][$context->getLocale()];
    								$fieldData[$itemId] = $value;
    							}
    						}
    						$data[$fieldId] = $fieldData;
    					}
    					else {
    						$value = $request->getPost($fieldId);
    						$data[$fieldId] = $value;
    					}
    				}
    			}

    			if ($contact->loadData($data) == 'OK') {
    				if ($account->loadData($data) == 'OK') {

    					// Link to the place community for the account type
    					$place = Place::get($account->place_id);
    					$community = Community::get($account->type.'/'.$place->identifier, 'identifier');
    					if ($place && $community) {
    						$account->contact_1->communities[$community->id] = true;
    					}

    					// Atomically save
    					$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    					$connection->beginTransaction();
    					try {
    						if (!$contact->id) {
    							$rc = $contact->add();
    							if ($rc != 'OK') $error = $rc;
    						}
    						if (!$error) {
    							$account->contact_1_id = $contact->id;
    							$account->contact_1_status = 'main';
    							if (!$account->callback_date || $account->callback_date > date('Y-m-d')) $account->callback_date = date('Y-m-d');
    							$account->contact_history[] = array(
    									'time' => date('Y-m-d H:i:s'),
    									'n_fn' => $account->contact_1->email,
    									'comment' => 'Request: Demande inscription - '.$currentState['title'][$context->getLocale()],
    							);
    							if ($account->id) $rc = $account->update(null);
    							else $rc = $account->add();
    							if ($rc != 'OK') $error = $rc;
    							$id = $account->id;
    						}
    						if ($error) $connection->rollback();
    						else {
								$path = $context->getConfig('ppitDocument')['dropbox']['folders']['students'].'/'.strtolower($account->contact_1->n_last).'_'.strtolower($account->contact_1->n_first).'_'.$account->contact_1->email;
								if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
									$dropbox = $context->getConfig('ppitDocument')['dropbox'];
							    	$client = new Client(
							    			'https://api.dropboxapi.com/2/files/create_folder_v2',
							    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
							    	);
							    	$client->setEncType('application/json');
							    	$client->setMethod('POST');
							    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
							    	$client->setRawBody(json_encode(array('path' => $path)));
							    	$response = $client->send();
							    	
						    		foreach($this->getRequest()->getFiles()->toArray() as $fileId => $file) {
								    	$f = fopen($file['tmp_name'], "rb");
								    	$client = new Client(
								    			'https://content.dropboxapi.com/2/files/upload',
								    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
								    	);
								    	$client->setEncType('application/octet-stream');
								    	$client->setMethod('POST');
								    	$client->getRequest()->getHeaders()->addHeaders(array(
								    			'Authorization' => 'Bearer '.$dropbox['credential'],
								    			'Dropbox-API-Arg' => json_encode(array(
								    					'path' => $path.'/'.$file['name'],
								    					'mode' => 'add',
								    					'autorename' => true,
								    					'mute' => false,
								    			)),
								    	));
								    	$client->setRawBody(fread($f, filesize($file['tmp_name'])));
										$response = $client->send();
								    	fclose($f);
						    		}
						    	}
    							$connection->commit();
    							return $this->redirect()->toRoute('commitmentAccount/contactForm', array('type' => $type, 'place_identifier' => $place_identifier, 'action_id' => $action_id, 'state_id' => $currentState['next-step']['state_id'], 'id' => $id));
    						}
    					}
    					catch (\Exception $e) {
    						$connection->rollback();
    						throw $e;
    					}
    				}
    			}
    		}
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'template' => $template,
    			'type' => $type,
    			'place_identifier' => $place_identifier,
    			'place' => $place,
    			'state_id' => $state_id,
    			'action_id' => $action_id,
    			'id' => $id,
    			'account' => $account,
    			'currentState' => $currentState,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'currentEntry' => $currentEntry,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function getAction()
    {
    	$context = Context::getCurrent();
    	$writer = new Writer\Stream('data/log/account_get.txt');
    	$logger = new Logger();
    	$logger->addWriter($writer);
    
    	$instance_caption = $context->getInstance()->caption;
    
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
    			
    		// Write to the log
    		$logger->info('account/get/'.$instance_caption.'/'.';401;'.$username);
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {
    
    		$email = $this->params()->fromRoute('email');
    		$contact = Vcard::get($email, 'email');
    	   	if (!$contact) {
    			$logger->info('account/get;'.$instance_caption.';404;'.'email: '.$email.';');
    			$this->getResponse()->setStatusCode('404');
    			return $this->getResponse();
    		}
    		$account =  Account::get($contact->id, 'contact_1_id');
    	    if (!$account) {
    			$logger->info('account/get;'.$instance_caption.';404;'.'contact_1_id: '.$contact->id.';');
    			$this->getResponse()->setStatusCode('404');
    			return $this->getResponse();
    		}
    		$result = array(
    				'account_identifier' => $account->id,
    				'status' => $account->status,
    				'name' => $account->name,
    				'title' => $contact->n_title,
    				'n_first' => $contact->n_first,
    				'n_last' => $contact->n_last,
    		);
    		return new JsonModel($result);
    	}
    }

    public function postAction()
    {
    	$context = Context::getCurrent();
    	$writer = new Writer\Stream('data/log/account_get.txt');
    	$logger = new Logger();
    	$logger->addWriter($writer);

    	if (!$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		$logger->info('account/postAction;'.date('Y-m-d H:i:s').';401;');
    		return $this->getResponse();
    	}
    	else {
			$data = json_decode($this->request->getContent(), true);
    		
    		// Log the web-service as an incoming interaction
			$interaction = Interaction::instanciate();
			$reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
			$intData = array();
			$intData['type'] = 'web_service';
			$intData['category'] = (is_array($data) && array_key_exists('type', $data)) ? $data['type'] : 'unknown';
			$intData['format'] = $this->getRequest()->getHeaders()->get('content-type')->getFieldValue();
			$intData['direction'] = 'input';
			$intData['route'] = 'commitmentAccount/processPost';
			$intData['reference'] = $reference;
			$intData['content'] = json_encode($data);
			$rc = $interaction->loadData($intData);

			// Load the data from the post request
			if (!is_array($data) || !array_key_exists('type', $data) || !array_key_exists('place_identifier', $data) || !array_key_exists('email', $data)) {
				$this->getResponse()->setStatusCode('400');
				$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;'.$this->request->getContent().';');
				echo 'Mandatory data is not provided';
				$interaction->http_status = '400 - Mandatory data is not provided';
				$rc = $interaction->add();
				return $this->getResponse();
			}
    	   	if (!in_array($data['type'], array('p-pit-studies', 'business'))) {
    			$this->getResponse()->setStatusCode('400');
    			$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;Unknown type '.$data['type'].';');
    			echo 'Unknown type '.$data['type'];
				$interaction->http_status = '400 - Unknown type '.$data['type'];
    			$rc = $interaction->add();
    			return $this->getResponse();
    		}
    		$place = Place::get($data['place_identifier'], 'identifier');
    	   	if (!$place /* || !$context->hasAccessTo('place', $place) */) {
    			$this->getResponse()->setStatusCode('400');
    			$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;The place identified by '.$data['place_identifier'].' does not exist;');
    			echo 'The place identified by '.$data['place_identifier'].' does not exist';
				$interaction->http_status = '400 - The place identified by '.$data['place_identifier'].' does not exist';
    			$rc = $interaction->add();
    			return $this->getResponse();
    		}
			$interaction->http_status = '200';
			$rc = $interaction->add();
			$rc = Account::processPost($data, $interaction);
	   		$this->getResponse()->setStatusCode($interaction->http_status);
   			$this->response->setContent(json_encode(array('interaction_id' => $interaction->id, 'rc' => $rc)));
   			return $this->getResponse();
	    }
    }

    public function processPostAction()
	{
    	$context = Context::getCurrent();
    	$translator = $context->getServiceManager()->get('translator');
    	$interactionId = $this->params()->fromRoute('interaction_id');
    	$interaction = Interaction::get($interactionId);
   		$this->response->setContent(json_encode(array('interaction_id' => $interactionId)));
    	if ($interaction->status != 'new') {
			$this->getResponse()->setStatusCode('403');
			return $this->getResponse();
		}
    	$data = json_decode($interaction->content, true);
		$rc = Account::processPost($data, $interaction);
   		$this->getResponse()->setStatusCode($interaction->http_status);
   		echo $rc;
		return $this->getResponse();
    }

    public function notifyAction()
    {
		$context = Context::getCurrent();
		$instances = Instance::getList(array());
		foreach ($instances as $instance) {
			$context->updateFromInstanceId($instance->id);
			$accounts = Account::getList(null, 'contact', array('status' => 'new', 'origine' => 'inscription', 'notification_time' => null));
			foreach($accounts as $account) {
				if (time() - strtotime($account->update_time) > 600) {
					if ($account->email && $account->n_fn) {
						$account->notification_time = date('Y-m-d H:i:s');
						Account::getTable()->save($account);
						$select = Vcard::getTable()->getSelect();
						$where = new Where;
						$where->like('roles', '%admin%');
						$select->where($where);
						$cursor = Vcard::getTable()->selectWith($select);
						$admins = array();
						foreach ($cursor as $contact) $admins[$contact->email] = $contact;
						$template = $context->getConfig('commitmentAccount/contactForm/'.$account->type);
						if ($template['definition'] != 'inline') $template = $context->getConfig($template['definition']);
						$action_id = $account->properties[$template['index']['actions']['property']];
						$url = 'https://'.$context->getInstance()->fqdn.'/commitment-account/contact-form/'.$account->type.'/'.$account->place_identifier.'/state1/'.$action_id.'/'.$account->id;
						$account->notify($admins, $url);
					}
				}
			}
		}
    }
    
    public function rephaseAction()
    {
    	$select = Account::getTable()->getSelect()->where(array('id > ?' => 0, 'status <> ?' => 'deleted', 'type' => 'p-pit-studies'));
    	$cursor = Account::getTable()->selectWith($select);
    	foreach ($cursor as $account) {
    		if ($account->contact_1_id) {
    			$contact = Vcard::getTable()->get($account->contact_1_id);
    			if ($contact && $account->property_2) {
					echo $contact->n_fn.' ('.$account->update_time.') : '.$contact->tel_cell.' > '.$account->property_2.'<br>';
/*					$contact->tel_cell = $account->property_2;
					$contact->update(null);*/
    			}
    		}
    	}
    	return $this->response;
    }
}
