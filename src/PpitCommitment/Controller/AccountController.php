<?php

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Model\Community;
use PpitCore\Model\Vcard;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Place;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
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

		$type = $this->params()->fromRoute('type', 0);
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		
		$community_id = (int) $context->getCommunityId();
		$contact = Vcard::instanciate($community_id);

		$applicationId = 'p-pit-engagements';
		$applicationName = 'P-Pit Engagements';
		$currentEntry = $this->params()->fromQuery('entry', 'account');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'active' => 'application',
    			'applicationName' => $applicationName,
    			'applicationId' => $applicationId,
    			'community_id' => $community_id,
    			'types' => $types,
    			'contact' => $contact,
    			'currentEntry' => $currentEntry,
    			'type' => $type,
    	));
    }

    public function getFilters($params, $type)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();
    
    	$customer_name = ($params()->fromQuery('customer_name', null));
    	if ($customer_name) $filters['customer_name'] = $customer_name;

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

    	$type = $this->params()->fromRoute('type', null);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
				'places' => Place::getList(array()),
    			'type' => $type,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', null);
    	 
    	$params = $this->getFilters($this->params(), $type);
//    	if (!array_key_exists('min_closing_date', $params)) $params['min_closing_date'] = date('Y-m-d');

    	$major = ($this->params()->fromQuery('major', 'customer_name'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
    
    	// Retrieve the list
    	$accounts = Account::getList($type, $params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'accounts' => $accounts,
				'places' => Place::getList(array()),
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
    	$view = $this->getList();

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
    	
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type', null);

    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
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
			    	$data = array();
					foreach ($context->getConfig('commitmentAccount/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
						$property = $context->getConfig('commitmentAccount'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
						if ($property['type'] == 'photo' && array_key_exists($propertyId, $request->getFiles()->toArray())) $data['file'] = $request->getFiles()->toArray()[$propertyId];
						else $data[$propertyId] = $request->getPost($propertyId);
					}
					if ($type) $data['credits'] = array($type => true);

					$communityData = array('name' => ($account->customer_name) ? $account->customer_name : $account->n_last.', '.$account->n_first);
    				$communityData['next_credit_consumption_date'] = date('Y-m-d', strtotime(date('Y-m-d').' + 31 days'));
					if ($account->customer_community->loadData($communityData, $account->customer_community->id) != 'OK') throw new \Exception('View error');

					// Add the main contact
					if (!$account->contact_1) $account->contact_1 = Vcard::instanciate();
					if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');
					if ($account->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');
    			}
				if (!$error) {
	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if (!$account->id) {
	    					$return = $account->customer_community->add();
			        		if ($return != 'OK') $error = 'Duplicate';
			        		else {
			        			$account->customer_community_id = $account->customer_community->id;
			        			$account->contact_1->community_id = $account->customer_community->id;
		    					$account->contact_1 = Vcard::optimize($account->contact_1);
		    					$account->customer_community->contact_1_id = $account->contact_1->id;
		    					$account->customer_community->contact_1_status = 'main';
		    					$account->customer_community->update($account->customer_community->update_time);
		    					$account->add();
			        		}
	    				}
	    				elseif ($action == 'delete') {
	    					$return = $account->customer_community->delete($request->getPost('update_time'));
	    					if ($return != 'OK') $error = $return;
	    					else {
	    						$return = $account->delete($request->getPost('update_time'));
	    						if ($return != 'OK') $error = $return;
	    					}
	    				}
	    				else {
	    					// Save the contact
	    					$return = $account->customer_community->update(null);
	    					if ($return != 'OK') $error = $return;
	    					else {
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
    			$data['roles'] = array('student' => true);
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
    		if (!$account->contact_1) $account->contact_1 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_1;
    		$contact_status = $account->contact_1_status;
    	}
    	elseif ($contactNumber == 'contact_2') {
    		if (!$account->contact_2) $account->contact_2 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_2;
    		$contact_status = $account->contact_2_status;
    	}
    	elseif ($contactNumber == 'contact_3') {
    		if (!$account->contact_3) $account->contact_3 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_3;
    		$contact_status = $account->contact_3_status;
    	}
    	elseif ($contactNumber == 'contact_4') {
    		if (!$account->contact_4) $account->contact_4 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_4;
    		$contact_status = $account->contact_4_status;
    	}
    	elseif ($contactNumber == 'contact_5') {
    		if (!$account->contact_5) $account->contact_5 = Vcard::instanciate($account->customer_community_id);
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
							$contact_1_status = $account->customer_community->contact_1_status = $contact_status;
							if ($contact_status == 'invoice') {
								if ($account->customer_community->contact_2_status == 'invoice') $account->customer_community->contact_2_status = '';
								if ($account->customer_community->contact_3_status == 'invoice') $account->customer_community->contact_3_status = '';
								if ($account->customer_community->contact_4_status == 'invoice') $account->customer_community->contact_4_status = '';
								if ($account->customer_community->contact_5_status == 'invoice') $account->customer_community->contact_5_status = '';
							}
    						$account->customer_community->contact_1_id = $contact->id;
    						$account->customer_community->update($account->customer_community->update_time);
    						$account->contact_1 = $contact;
    					}
    					elseif ($contactNumber == 'contact_2') {
							$account->contact_2_status = $account->customer_community->contact_2_status = $contact_status;
    						if ($contact_status == 'invoice') {
								if ($account->customer_community->contact_1_status == 'invoice') $account->customer_community->contact_1_status = '';
								if ($account->customer_community->contact_3_status == 'invoice') $account->customer_community->contact_3_status = '';
								if ($account->customer_community->contact_4_status == 'invoice') $account->customer_community->contact_4_status = '';
								if ($account->customer_community->contact_5_status == 'invoice') $account->customer_community->contact_5_status = '';
							}
							$account->customer_community->contact_2_id = $contact->id;
    						$account->customer_community->update($account->customer_community->update_time);
    						$account->contact_2 = $contact;
    					}
    					elseif ($contactNumber == 'contact_3') {
							$account->contact_3_status = $account->customer_community->contact_3_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->customer_community->contact_1_status == 'invoice') $account->customer_community->contact_1_status = '';
								if ($account->customer_community->contact_2_status == 'invoice') $account->customer_community->contact_2_status = '';
								if ($account->customer_community->contact_4_status == 'invoice') $account->customer_community->contact_4_status = '';
								if ($account->customer_community->contact_5_status == 'invoice') $account->customer_community->contact_5_status = '';
							}
							$account->customer_community->contact_3_id = $contact->id;
    						$account->customer_community->update($account->customer_community->update_time);
    						$account->contact_3 = $contact;
    					}
    					elseif ($contactNumber == 'contact_4') {
							$account->contact_4_status = $account->customer_community->contact_4_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->customer_community->contact_1_status == 'invoice') $account->customer_community->contact_1_status = '';
								if ($account->customer_community->contact_2_status == 'invoice') $account->customer_community->contact_2_status = '';
								if ($account->customer_community->contact_3_status == 'invoice') $account->customer_community->contact_3_status = '';
								if ($account->customer_community->contact_5_status == 'invoice') $account->customer_community->contact_5_status = '';
							}
							$account->customer_community->contact_4_id = $contact->id;
    						$account->customer_community->update($account->customer_community->update_time);
    						$account->contact_4 = $contact;
    					}
    					elseif ($contactNumber == 'contact_5') {
							$account->contact_5_status = $account->customer_community->contact_5_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->customer_community->contact_1_status == 'invoice') $account->customer_community->contact_1_status = '';
								if ($account->customer_community->contact_2_status == 'invoice') $account->customer_community->contact_2_status = '';
								if ($account->customer_community->contact_3_status == 'invoice') $account->customer_community->contact_3_status = '';
								if ($account->customer_community->contact_4_status == 'invoice') $account->customer_community->contact_4_status = '';
							}
							$account->customer_community->contact_5_id = $contact->id;
    						$account->customer_community->update($account->customer_community->update_time);
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

    	$account = Account::instanciate();
    
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
				foreach ($context->getConfig('commitmentAccount/register'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
			    	$data[$propertyId] =  $request->getPost($propertyId);
				}
		    	if ($account->loadData($data) != 'OK') throw new \Exception('View error');

    			// Atomically save
    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$return = $account->add(false /* Without creating the user */);
    
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
    			'type' => $type,
    			'account' => $account,
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
    		$community = Community::get($contact->id, 'contact_1_id');
    	   	if (!$community) {
    			$logger->info('account/get;'.$instance_caption.';404;'.'vcard_id: '.$contact->id.';');
    			$this->getResponse()->setStatusCode('404');
    			return $this->getResponse();
    		}
    		$account =  Account::get($community->id, 'customer_community_id');
    	    if (!$community) {
    			$logger->info('account/get;'.$instance_caption.';404;'.'customer_community_id: '.$community->id.';');
    			$this->getResponse()->setStatusCode('404');
    			return $this->getResponse();
    		}
    		$result = array(
    				'account_identifier' => $account->id.'-'.$community->id,
    				'status' => $account->status,
    				'name' => $community->name,
    				'title' => $contact->n_title,
    				'n_first' => $contact->n_first,
    				'n_last' => $contact->n_last,
    		);
    		return new JsonModel($result);
    	}
    }

    public function putAction()
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
    		$logger->info('account/put/'.$instance_caption.'/'.';401;'.$username);
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {

    		// Atomically save
    		$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
	    		$email = $this->params()->fromRoute('email');
	    		$contact = Vcard::get($email, 'email');
	    		if (!$contact) {
	    			$data = array();
	    			$data['n_title'] = $this->request->getPost('n_title');
	    			$data['n_first'] = $this->request->getPost('n_first');
	    			$data['n_last'] = $this->request->getPost('n_last');
	    			$data['email'] = $email;
	    			$contact = Vcard::instanciate();
	    			if ($contact->loadData($data) != 'OK') {
						$logger->info('account/put;500;vcard;');
				    	$this->getResponse()->setStatusCode('500');
						return $this->getResponse();
	    			}
	    			$rc = $contact->add();
	    			if ($rc != 'OK') {
	    				$connection->rollback();
	    				$logger->info('account/put;409;vcard');
	    				$this->getResponse()->setStatusCode('409');
	    				return $this->getResponse();
	    			}
	    		}
	    		$community = Community::get($contact->id, 'contact_1_id');
	    		if (!$community) {
	    			$data = array();
	    			$data['contact_1_id'] = $contact->id;
	    			$data['name'] = $contact->n_fn;
	    			$data['next_credit_consumption_date'] = '9999-12-31';
	    			$community = Community::instanciate();
	    			if ($community->loadData($data) != 'OK') {
						$logger->info('account/put;500;community;');
				    	$this->getResponse()->setStatusCode('500');
						return $this->getResponse();
	    			}
	    			$rc = $community->add();
	    			if ($rc != 'OK') {
	    				$connection->rollback();
	    				$logger->info('account/put;409;community;');
	    				$this->getResponse()->setStatusCode('409');
	    				return $this->getResponse();
	    			}
	    		}
	    		$account =  Account::get($community->id, 'customer_community_id');
	    		if (!$account) {
	    			$data = array();
	    			$data['customer_community_id'] = $community->id;
	    			$data['status'] = 'new';
	    			$data['opening_date'] = date('Y-m-d');
	    			$account = Account::instanciate();
	    			if ($account->loadData($data) != 'OK') {
						$logger->info('account/put;500;account;');
				    	$this->getResponse()->setStatusCode('500');
						return $this->getResponse();
	    			}
	    			$rc = $account->add();
	    			if ($rc != 'OK') {
	    				$connection->rollback();
	    				$logger->info('account/put;409;account;');
	    				$this->getResponse()->setStatusCode('409');
	    				return $this->getResponse();
	    			}
	    		}
		    	$connection->commit();
	    		$logger->info('account/put;'.';200;'.$account->id.';');
	    		$this->getResponse()->setStatusCode('200');
				return $this->getResponse();
    		}
    		catch (\Exception $e) {
    			$connection->rollback();
    			$logger->info('account/put;'.';500;');
    			$this->getResponse()->setStatusCode('500');
    			return $this->getResponse();
    		}
    	}
    }
    
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
    }
}
