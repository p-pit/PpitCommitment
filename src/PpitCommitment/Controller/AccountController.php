<?php

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitContact\Model\Community;
use PpitContact\Model\Vcard;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use PpitMasterData\Model\Place;
use PpitUser\Model\User;
use PpitUser\Model\UserContact;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$type = $this->params()->fromRoute('type', null);

		$community_id = (int) $context->getCommunityId();
		$contact = Vcard::getNew($community_id);

		$applicationName = 'P-PIT Engagements';
		$menu = Context::getCurrent()->getConfig('menus')['p-pit-engagements'];
		$currentEntry = $this->params()->fromQuery('entry', 'account');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'active' => 'application',
    			'applicationName' => $applicationName,
    			'community_id' => $community_id,
    			'menu' => $menu,
    			'contact' => $contact,
    			'currentEntry' => $currentEntry,
    			'type' => $type,
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();
    
    	$customer_name = ($params()->fromQuery('customer_name', null));
    	if ($customer_name) $filters['customer_name'] = $customer_name;

    	foreach ($context->getConfig('commitmentAccount/search')['main'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}

    	foreach ($context->getConfig('commitmentAccount/search')['more'] as $propertyId => $rendering) {
    	
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
				'places' => Place::getList(),
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
    	 
    	$params = $this->getFilters($this->params());
    
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
				'places' => Place::getList(),
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
		header('Content-Disposition:inline;filename=Fichier.xlsx ');
		$writer->save('php://output');

    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', 0);
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $account->type,
    			'id' => $account->id,
    			'account' => $account,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');

    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);
    	
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
				foreach ($context->getConfig('commitmentAccount/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
					$property = $context->getConfig('commitmentAccount'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
					if ($property['type'] == 'photo' && array_key_exists($propertyId, $request->getFiles()->toArray())) $data['file'] = $request->getFiles()->toArray()[$propertyId];
					else $data[$propertyId] =  $request->getPost($propertyId);
				}
				$data['roles'] = array('student' => true);
				$data['perimeters'] = array();
				if ($type) $data['credits'] = array($type => true);
				$data['username'] = $request->getPost('username');
				$data['is_notified'] = $request->getPost('is_notified');
				$data['new_password'] = $request->getPost('new_password');
				$data['locale'] = $request->getPost('locale');
				if (!$account->id) {
					
					// Add the community
					$account->customer_community = Community::instanciate();
					$communityData = array('name' => $data['n_last'].', '.$data['n_first']);
					if ($account->customer_community->loadData($communityData, $account->customer_community->id) != 'OK') throw new \Exception('View error');

					// Add the main contact
					$account->contact_1 = Vcard::instanciate();
					if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');

					// Add the main user
					$account->user = User::instanciate();
					$rc = $account->user->loadData($request, $account->contact_1);
					if ($rc == 'Integrity') throw new \Exception('View error');
					elseif ($rc == 'Duplicate') $error = 'Duplicate user';
				}
				if ($account->loadData($data) != 'OK') throw new \Exception('View error');
				if (!$error) {
	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if (!$account->id) {
			        		$return = $account->customer_community->add();
			        		if ($return != 'OK') $error = 'Duplicate commmunity';
			        		else {
			        			$account->customer_community_id = $account->customer_community->id;
			        			$account->contact_1->community_id = $account->customer_community->id;
		    					$account->contact_1 = Vcard::optimize($account->contact_1);
		    					$account->customer_community->contact_1_id = $account->contact_1->id;
		    					$account->customer_community->update($account->customer_community->update_time);
		    					$account->user->contact_id = $account->contact_1->id;
		
			        			// Save the user
				        		$return = $account->user->add($account->contact_1->email, ($account->is_notified) ? true : false);
				        		if ($return != 'OK') $error = 'Duplicate user';
								else {
									// Save the user-contact link
									$userContact = UserContact::instanciate();
									$userContact->user_id = $account->user->user_id;
									$userContact->contact_id = $account->contact_1->id;
									$return = $userContact->add();
					        		if ($return != 'OK') $error = $return;
									else {
										$return = $account->add();
										if ($return != 'OK') $error = $return;
										else $message = 'OK';
									}
								}
			        		}
	    				}
	    				elseif ($action == 'delete') $return = $account->delete($request->getPost('update_time'));
	    				else {
	    					// Save the contact
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
				'places' => Place::getList(),
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
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);
    	 
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
    			$data['roles'] = array('student' => true);
    			$data['perimeters'] = array();
    			if ($type) $data['credits'] = array($type => true);
    			$data['username'] = $request->getPost('username');
    			$data['is_notified'] = $request->getPost('is_notified');
    			$data['new_password'] = $request->getPost('new_password');
    			$data['locale'] = $request->getPost('locale');
    			$data['is_demo_mode_active'] = false;
    			if (!$account->username) {
    
    				// Add the main user
    				$account->user = User::instanciate();
    				$rc = $account->user->loadData($request, $account->contact_1);
    				if ($rc == 'Integrity') throw new \Exception('View error');
    				elseif ($rc == 'Duplicate') $error = 'Duplicate user';
    			}
    			if ($account->loadData($data) != 'OK') throw new \Exception('View error');
    			if (!$error) {
    				// Atomically save
    				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    					if (!$account->username) {
    							$account->contact_1->update($account->contact_1->update_time);
    							$account->user->contact_id = $account->contact_1->id;
    
    							// Save the user
    							$return = $account->user->add($account->contact_1->email, ($account->is_notified) ? true : false);
    							if ($return != 'OK') $error = 'Duplicate user';
    							else {
    								// Save the user-contact link
    								$userContact = UserContact::instanciate();
    								$userContact->user_id = $account->user->user_id;
    								$userContact->contact_id = $account->contact_1->id;
    								$return = $userContact->add();
    								if ($return != 'OK') $error = $return;
    								else {
    									$return = $account->add();
    									if ($return != 'OK') $error = $return;
    									else $message = 'OK';
    								}
    							}
    					}
    					else {
    						// To do
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
    	 
    	$type = $this->params()->fromRoute('type');    
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$contactNumber = $this->params()->fromRoute('contactNumber', 0);

    	$account = Account::get($id);
    	if ($contactNumber == 'contact_1') {
    		if (!$account->contact_1) $account->contact_1 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_1;
    	}
    	elseif ($contactNumber == 'contact_2') {
    		if (!$account->contact_2) $account->contact_2 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_2;
    	}
    	elseif ($contactNumber == 'contact_3') {
    		if (!$account->contact_3) $account->contact_3 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_3;
    	}
    	elseif ($contactNumber == 'contact_4') {
    		if (!$account->contact_4) $account->contact_4 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_4;
    	}
    	elseif ($contactNumber == 'contact_5') {
    		if (!$account->contact_5) $account->contact_5 = Vcard::instanciate($account->customer_community_id);
    		$contact = $account->contact_5;
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
					$data[$propertyId] =  $request->getPost($propertyId);
    			}

    			if ($contact->loadData($data) != 'OK') throw new \Exception('View error');
    			$contact = Vcard::optimize($contact);

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
    						if ($account->customer_community->contact_1_id != $contact->id) {
    							$account->customer_community->contact_1_id = $contact->id;
    							$account->customer_community->update($account->customer_community->update_time);
    						}
    						$account->contact_1 = $contact;
    					}
    					elseif ($contactNumber == 'contact_2') {
    					    if ($account->customer_community->contact_2_id != $contact->id) {
    							$account->customer_community->contact_2_id = $contact->id;
    							$account->customer_community->update($account->customer_community->update_time);
    						}
    						$account->contact_2 = $contact;
    					}
    					elseif ($contactNumber == 'contact_3') {
    					    if ($account->customer_community->contact_3_id != $contact->id) {
    							$account->customer_community->contact_3_id = $contact->id;
    							$account->customer_community->update($account->customer_community->update_time);
    						}
    						$account->contact_3 = $contact;
    					}
    					elseif ($contactNumber == 'contact_4') {
    					    if ($account->customer_community->contact_4_id != $contact->id) {
    							$account->customer_community->contact_4_id = $contact->id;
    							$account->customer_community->update($account->customer_community->update_time);
    						}
    						$account->contact_4 = $contact;
    					}
    					elseif ($contactNumber == 'contact_5') {
    					    if ($account->customer_community->contact_5_id != $contact->id) {
    							$account->customer_community->contact_5_id = $contact->id;
    							$account->customer_community->update($account->customer_community->update_time);
    						}
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
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $id,
    			'contactNumber' => $contactNumber,
    			'action' => $action,
    			'account' => $account,
    			'contact' => $contact,
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
   		if ($context->isSpaMode()) $view->setTerminal(true);
   		return $view;
    }
}
