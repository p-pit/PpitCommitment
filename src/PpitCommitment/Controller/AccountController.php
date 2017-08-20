<?php

namespace PpitCommitment\Controller;

use PpitContact\Model\ContactMessage;
use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());

		$entry = $this->params()->fromRoute('entry');
    	$type = $this->params()->fromRoute('type', null);
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
    	$data = $account->toArray();
    	$data['contact_history'] = $account->contact_history;
    	$data['photo_link_id'] = $account->contact_1->photo_link_id;
    	$account->properties = $data;
    }

    public function groupAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute();
    
    	$request = $this->getRequest();
    	if (!$request->isPost()) return $this->redirect()->toRoute('home');
    	$nbAccount = $request->getPost('nb-account');
    
    	$accounts = array();
    	for ($i = 0; $i < $nbAccount; $i++) {
    		$account = Account::get($request->getPost('account_'.$i));
    		$accounts[] = $account;
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'accounts' => $accounts,
    			'places' => Place::getList(array()),
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

    	$mail = ContactMessage::instanciate();
    	$mail->type = 'email';
    	$mail->subject = $context->getConfig('community/sendMessage')['subject'][$context->getLocale()];
    	$mail->body = $context->getConfig('community/sendMessage')['body'][$context->getLocale()];

    	$documentList = array();
    	if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
    		require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
    		$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    		$dropboxClient = new \Dropbox\Client($dropbox['credential'], $dropbox['clientIdentifier']);
    		try {
    			$properties = $dropboxClient->getMetadataWithChildren($dropbox['folders']['contact']);
    			foreach ($properties['contents'] as $content) $documentList[] = substr($content['path'], strrpos($content['path'], '/')+1);
    		}
    		catch(\Exception $e) {}
    	}
    	else $dropbox = null;

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
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
    			$data['subject'] = $request->getPost('subject');
    			$data['from_mail'] = $context->getConfig('community/sendMessage')['from_mail'];
    			$data['from_name'] = $context->getConfig('community/sendMessage')['from_name'];
    			$data['body'] = $request->getPost('body');
    			$attachment = $request->getPost('attachment');
    			if ($attachment) {
					$url = $this->getServiceLocator()->get('viewhelpermanager')->get('url');
   					$link = $url('commitmentAccount/dropboxLink', array('document' => $attachment), array('force_canonical' => true));
    				$data['body'] .= '<br><br>Pièce jointe : <a href="'.$link.'">'.$attachment.'</a>';
    			}

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
    			'mail' => $mail,
	    		'dropbox' => $dropbox,
	    		'documentList' => $documentList,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function dropboxLinkAction()
    {
    	$context = Context::getCurrent();
    	$document = $this->params()->fromRoute('document', 0);
    	require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
    	$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    	$dropboxClient = new \Dropbox\Client($dropbox['credential'], $dropbox['clientIdentifier']);
    	$link = $dropboxClient->createTemporaryDirectLink($dropbox['folders']['contact'].'/'.$document);
    	if ($link[0]) return $this->redirect()->toUrl($link[0]);
    	else return $this->response;
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
    			    			
					// Unlink the current place community for the account type
					$place = Place::get($account->place_id);
					if ($place && array_key_exists($account->type, $place->communities)) {
    					unset($account->contact_1->communities[$place->communities[$account->type]]);
    				}
    				
    				$data = array();
					foreach ($context->getConfig('commitmentAccount/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
						$property = $context->getConfig('commitmentAccount'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
						if ($property['type'] != 'title') {
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
					if ($place && array_key_exists($account->type, $place->communities)) {
						$account->contact_1->communities[$place->communities[$account->type]] = true;
    				}
    			}
    			 
				if (!$error) {
	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if (!$account->id) {
	    					$account->contact_1 = Vcard::optimize($account->contact_1);
	    					$account->contact_1_id = $account->contact_1->id;
	    					$account->contact_1_status = 'main';
	    					$account->add();
	    				}
	    				elseif ($action == 'delete') {
    						$return = $account->delete($request->getPost('update_time'));
    						if ($return != 'OK') $error = $return;
	    				}
	    				else {

	    					// Save the contact
	    					$return = $account->contact_1->update($account->contact_1->update_time);
	    					if ($return != 'OK') $error = $return;
	    					else {
	    						$return = $account->update($request->getPost('update_time'));
	    						if ($return != 'OK') $error = $return;
								else {
									if (array_key_exists('file', $data)) $account->contact_1->savePhoto($data['file']);
								}
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
    			$data['roles'] = array();
    			$data['perimeters'] = array();
    			if ($type) $data['credits'] = array($type => true);
    			$data['username'] = $request->getPost('username');
    			$data['state'] = $request->getPost('state');
    			$data['is_notified'] = $request->getPost('is_notified');
    			$data['new_password'] = $request->getPost('new_password');
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
	    						else $context->getSecurityAgent()->changePassword($user, $user->username, $user->password, $user->new_password, null);
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
    	$type = $this->params()->fromRoute('type');

    	if (!$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {

    		// Load the data from the post request
    		$data = json_decode($this->request->getContent(), true);
    		if (!is_array($data) || !array_key_exists('email', $data) || !array_key_exists('n_first', $data) || !array_key_exists('n_last', $data)) {
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    		// Log the web-service as an incoming interaction
			$interaction = Interaction::instanciate();
			$reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
			$intData = array();
			$intData['type'] = 'web_service';
			$intData['category'] = $type;
			$intData['format'] = $this->getRequest()->getHeaders()->get('content-type')->getFieldValue();
			$intData['direction'] = 'input';
			$intData['route'] = 'commitmentAccount/processPost';
			$intData['reference'] = $reference;
			$intData['content'] = json_encode($data);
			$rc = $interaction->loadData($intData);
    		$interaction->http_status = '200';
			$rc = $interaction->add();
    		return $this->redirect()->toRoute('commitmentAccount/processPost', array('interaction_id' => $interaction->id));
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
    	$type = $interaction->category;
    	$reference = $interaction->reference;
    	$data = json_decode($interaction->content, true);

    	if (array_key_exists('request', $data) && array_key_exists($data['request'], $context->getConfig('commitmentAccount/requestTypes'.(($type) ? '/'.$type : '')))) {
	    	$requestType = $context->getConfig('commitmentAccount/requestTypes'.(($type) ? '/'.$type : ''))[$data['request']][$context->getLocale()];
    	}
    	else {
    		$requestType = $context->getConfig('commitmentAccount/requestTypes'.(($type) ? '/'.$type : ''))['general_information'][$context->getLocale()];
    	}
    	if (array_key_exists('request_comment', $data)) $requestComment = $data['request_comment'];
    	else $requestComment = '';

    	$vcard = Vcard::get($data['email'], 'email');
    	if ($vcard) {
    		// Check if the account already exists. No update and the sales manager are notified.
    		$accounts = Account::getList('p-pit-studies', 'contact', array('contact_1_id' => $vcard->id));
    		if (count($accounts) > 0) {
    			reset($accounts);
				$account = Account::get(current($accounts)->id);
				if (!$account->callback_date || $account->callback_date > date('Y-m-d')) $account->callback_date = date('Y-m-d');
				$account->contact_history[] = array(
						'time' => date('Y-m-d H:i:s'),
						'n_fn' => 'support@p-pit.fr',
						'comment' => $translator->translate('ALREADY EXISTING ACCOUNT', 'ppit-commitment', $context->getLocale()).' - Request: '.$requestType.' - Comment: '.$requestComment.' - Ref.: '.$reference,
				);
		   		$rc = $account->update(null);
		   		if ($rc != 'OK') {
		   			$interaction->http_status = '500';
		   		}
		   		else {
		   			$interaction->status = 'processed';
		   			$interaction->http_status = '200';
		   		}
		   		$interaction->update(null);
		   		$this->getResponse()->setStatusCode($interaction->http_status);
	    		return $this->getResponse();
	    	}
	    	else {
	
				// Create the account
		   		$account = Account::instanciate($type);
		   		if ($account->loadData($data) != 'OK') {
		   			$interaction->http_status = '400';
		   		}
		   		else {
		    		$account->contact_1_id = $vcard->id;
		    		$account->contact_1_status = 'main';
		    		$account->name = $vcard->n_last.', '.$vcard->n_first;
		   			if (!$account->callback_date || $account->callback_date > date('Y-m-d')) $account->callback_date = date('Y-m-d');
		   			$account->contact_history[] = array(
		   					'time' => date('Y-m-d H:i:s'),
		   					'n_fn' => 'support@p-pit.fr',
		   					'comment' => $translator->translate('ALREADY EXISTING CONTACT', 'ppit-commitment', $context->getLocale()).' - Request: '.$requestType.' - Comment: '.$requestComment.' - Ref.: '.$reference,
		   			);
		   			$rc = $account->add();
		    		if ($rc != 'OK') {
		    			$interaction->http_status = '500';
		    		}
		    		else {
		    			$interaction->status = 'processed';
		    			$interaction->http_status = '200';
		    		}
		   		}
		   		$interaction->update(null);
		   		$this->getResponse()->setStatusCode($interaction->http_status);
	    		return $this->getResponse();
	    	}
    	}
    			 
    	// Create the contact 1
    	$contact = Vcard::instanciate();
    	if ($contact->loadData($data) != 'OK') {
    		$interaction->http_status = '400';
    	}
    	else {
	   		$rc = $contact->add();
	   		if ($rc != 'OK') {
	   			$interaction->http_status = '500';
	   		}
	   		else {
				// Create the account
	    		$account = Account::instanciate($type);
	    		if ($account->loadData($data) != 'OK') {
	    			$interaction->http_status = '400';
	    		}
	    		else {
			    	$account->contact_1_id = $contact->id;
			    	$account->contact_1_status = 'main';
		    		if (!$account->callback_date || $account->callback_date > date('Y-m-d')) $account->callback_date = date('Y-m-d');
		    		$account->contact_history[] = array(
		    				'time' => date('Y-m-d H:i:s'),
		    				'n_fn' => 'support@p-pit.fr',
		    				'comment' => 'Request: '.$requestType.' - Comment: '.$requestComment.' - Ref.: '.$reference,
		    		);
		    		$rc = $account->add();
		    		if ($rc != 'OK') {
		    			$interaction->http_status = '500';
		    		}
		    		else {
			    		$interaction->status = 'processed';
			    		$interaction->http_status = '200';
		    		}
	    		}
	   		}
   		}
   		$interaction->update(null);
   		$this->getResponse()->setStatusCode($interaction->http_status);
		return $this->response;
    }
/*    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the organizational unit
		$account = Account::get($id);
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) {

    			// Atomicity
    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $account->delete($account->update_time);
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
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
    		'type' => $account->type,
    		'account' => $account,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }*/

    public function rephaseAction()
    {
    	$select = Account::getTable()->getSelect()->where(array('id > ?' => 0));
    	$cursor = Account::getTable()->transSelectWith($select);
    	foreach ($cursor as $account) {
/*    	    if ($account->customer_community_id) {
	    		$community = Community::getTable()->transGet($account->customer_community_id);
	    		if ($community) {
		    		$account->name = $community->name;
		    		$account->contact_1_id = $community->contact_1_id;
		    		$account->contact_1_status = $community->contact_1_status;
		    		$account->contact_2_id = $community->contact_2_id;
		    		$account->contact_2_status = $community->contact_2_status;
		    		$account->contact_3_id = $community->contact_3_id;
		    		$account->contact_3_status = $community->contact_3_status;
		    		$account->contact_4_id = $community->contact_4_id;
		    		$account->contact_4_status = $community->contact_4_status;
		    		$account->contact_5_id = $community->contact_5_id;
		    		$account->contact_5_status = $community->contact_5_status;
		    		Account::getTable()->transSave($account);
		    		echo $account->id.'<br>';
	    		}
    	    }*/
    		if ($account->contact_1_id) {
    			$contact = Vcard::getTable()->transGet($account->contact_1_id);
    			if ($contact) {
    				$place = Place::getTable()->transGet($account->place_id);
    				if ($place && array_key_exists($account->type, $place->communities)) {
    					$contact->communities[$place->communities[$account->type]] = true;
		    			Vcard::getTable()->transSave($contact);
    					echo $contact->id.'<br>';
    				}
    			}
    		}
    	}
    }
}
