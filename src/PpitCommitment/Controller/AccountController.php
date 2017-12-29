<?php

namespace PpitCommitment\Controller;

use PpitContact\Model\ContactMessage;
use PpitCommitment\ViewHelper\PdfIndexCardViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
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
					if ($account->loadData($type, $data) == 'OK') {

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
								if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
									$path = $context->getConfig('ppitDocument')['dropbox']['folders']['students'].'/'.strtolower($account->contact_1->n_last).'_'.strtolower($account->contact_1->n_first).'_'.$account->contact_1->email;
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

    public static function processPost($data, $interaction)
    {
    	$context = Context::getCurrent();
    	$translator = $context->getServiceManager()->get('translator');
    	$type = $interaction->category;
    	$reference = $interaction->reference;
    	$place = Place::get($data['place_identifier'], 'identifier');
    	$data['place_id'] = $place->id;
    	$data['origine'] = 'web';
    	$data['opening_date'] = date('Y-m-d');

    	if (array_key_exists('request', $data) && array_key_exists($data['request'], $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : '')))) {
    		$requestType = $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : ''))[$data['request']][$context->getLocale()];
    	}
    	else {
    		$requestType = $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : ''))['general_information'][$context->getLocale()];
    	}
    	 
    	if (array_key_exists('request_comment', $data)) $requestComment = $data['request_comment'];
    	else $requestComment = '';

    	unset($data['place_identifier']);
    	unset($data['type']);
    	unset($data['request']);
    	unset($data['request_comment']);
    	 
    	$vcard = Vcard::get($data['email'], 'email');
    	if ($vcard) {
    		// Check if the account already exists. No update and the sales manager are notified.
    		$accounts = Account::getList($interaction->category, array('status' => implode(',', $context->getConfig('core_account/'.$interaction->category)['properties']['status']['perspectives']['contact']), 'contact_1_id' => $vcard->id));
    		reset($accounts);
    		if (count($accounts) > 0) $account = Account::get(current($accounts)->id);
    		else $account = null;
    		if ($account /*&& $account->place_id == $place->id*/) { // Demande D. Elfassy de dédoublonner tous centres confondus. Règle non retenue dans l'API REST standard (account/v1)
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
    			return $rc;
    		}
    		else {
    			 
    			// Create the account
    			$account = Account::instanciate($type);
    			$rc = $account->loadData($type, $data);
    			if ($rc != 'OK') {
    				$interaction->http_status = '400';
    				$rc = 'Account integrity';
    			}
    			else {
    				$account->contact_1_id = $vcard->id;
    				$account->contact_1_status = 'main';
    				if (!$account->name) $account->name = $vcard->n_last.', '.$vcard->n_first;
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
    			return $rc;
    		}
    	}
    	 
    	// Create the contact 1
    	$vcard = Vcard::instanciate();
    	$rc = $vcard->loadData($data);
    	if ($rc != 'OK') {
    		$interaction->http_status = '400';
    		$rc = 'Vcard integrity';
    	}
    	else {
    		$rc = $vcard->add();
    		if ($rc != 'OK') {
    			$interaction->http_status = '500';
    		}
    		else {
    			// Create the account
    			$account = Account::instanciate($type);
    			if ($account->loadData($type, $data) != 'OK') {
    				$interaction->http_status = '400';
    				$rc = 'Account integrity';
    			}
    			else {
    				$account->contact_1_id = $vcard->id;
    				$account->contact_1_status = 'main';
    				if (!$account->name) $account->name = $vcard->n_last.', '.$vcard->n_first;
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
    	return $rc;
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
			$intData['route'] = 'account/processPost';
			$intData['reference'] = $reference;
			$intData['content'] = $this->request->getContent();
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
			$rc = AccountController::processPost($data, $interaction);
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
		$rc = AccountController::processPost($data, $interaction);
   		$this->getResponse()->setStatusCode($interaction->http_status);
   		echo $rc;
		return $this->getResponse();
    }
}
