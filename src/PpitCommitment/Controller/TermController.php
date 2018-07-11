<?php

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentYear;
use PpitCommitment\Model\Term;
use PpitCommitment\ViewHelper\SsmlTermViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\ViewHelper\ArrayToSsmlViewHelper;
use PpitCore\ViewHelper\ArrayToXmlViewHelper;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TermController extends AbstractActionController
{
	public function getConfigProperties() {
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
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());

		$applicationId = 'p-pit-engagements';
		$applicationName = 'P-Pit Engagements';
		$currentEntry = $this->params()->fromQuery('entry', 'term');
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		$configProperties = $this->getConfigProperties();

    	return new ViewModel(array(
    			'context' => $context,
				'termProperties' => $configProperties,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'types' => $types,
    			'currentEntry' => $currentEntry,
				'indexPage' => $context->getConfig('commitmentTerm/index'),
    			'termSearchPage' => $context->getConfig('commitmentTerm/search'),
				'listPage' => $context->getConfig('commitmentTerm/list'),
				'detailPage' => $context->getConfig('commitmentTerm/detail'),
				'termUpdatePage' => $context->getConfig('commitmentTerm/update'),
    			'termGroupPage' => $context->getConfig('commitmentTerm/group'),
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('commitmentTerm/search')['properties'] as $propertyId => $rendering) {
    
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
    	$configProperties = $this->getConfigProperties();
    	 
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
				'termProperties' => $configProperties,
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
    			'searchPage' => $context->getConfig('commitmentTerm/search'),
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'due_date'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    	$configProperties = $this->getConfigProperties();
    	 
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$terms = Term::getList($params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
				'termProperties' => $configProperties,
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
    			'terms' => $terms,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
				'listPage' => $context->getConfig('commitmentTerm/list'),
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
		(new SsmlTermViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Terms.xlsx ');
		$writer->save('php://output');

    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $term = Term::get($id);
    	else $term = Term::instanciate();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $term->id,
    			'term' => $term,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function generateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$configProperties = $this->getConfigProperties();
    	$commitment_id = (int) $this->params()->fromRoute('commitment_id', 0);
    	$commitment = Commitment::get($commitment_id);
    	$term = Term::instanciate($commitment_id);
    	$term->commitment_caption = $commitment->caption;
    
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
    			$numberOfTerms = $request->getPost('number_of_terms');
    			$termDate = $request->getPost('first_term_date');
    			$periodicity = $request->getPost('periodicity');
    			$paymentMean = $request->getPost('means_of_payment');
    			$termAmount = round($commitment->tax_inclusive / $numberOfTerms, 2);
    			$cumulativeAmount = 0;
    			// Atomically save
    			$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
		    	$data = array();
		    	$data['status'] = 'expected';
    			$data['means_of_payment'] = $paymentMean;
		    	try {
    				for ($i = 0; $i < $numberOfTerms; $i++) {
				    	$data['caption'] = 'Echéance '.($i + 1);
    					$data['due_date'] = $termDate;
    					$termDate = date('Y-m-d', strtotime($termDate.' + '.$periodicity.' days'));
    					if ($i == $numberOfTerms - 1) $data['amount'] = $commitment->tax_inclusive - $cumulativeAmount;
    					else {
	    					$data['amount'] = $termAmount;
	    					$cumulativeAmount += $termAmount;
    					}
    					if ($term->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');
	    				$term->id = null;
		    			$rc = $term->add();
	    				if ($rc != 'OK') {
	    					$error = $rc;
	    					break;
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
			}
		}
    
    	$view = new ViewModel(array(
    		'context' => $context,
    		'term' => $term,
    		'csrfForm' => $csrfForm,
    		'error' => $error,
    		'message' => $message,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$configProperties = $this->getConfigProperties();
    	$updatePage = $context->getConfig('commitmentTerm/update');

    	$commitment_id = (int) $this->params()->fromRoute('commitment_id', 0);
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $term = Term::get($id);
    	else $term = Term::instanciate($commitment_id);
    	$action = $this->params()->fromRoute('act', null);

    	$documentList = array();
    	if (is_array($context->getConfig('ppitDocument')) && array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
    		$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    		$client = new Client(
	    			'https://api.dropboxapi.com/2/files/list_folder',
	    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
	    	);
	    	$client->setEncType('application/json');
	    	$client->setMethod('POST');
	    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
	    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['settlements'])));
	    	$response = $client->send();
	    	if (array_key_exists('entries', json_decode($response->getBody(), true))) {
		    	foreach (json_decode($response->getBody(), true)['entries'] as $entry) {
		    		$documentList[] = $entry['name'];
		    	}
	    	}
    	}
    	else $dropbox = null;
    	 
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
		    	foreach($updatePage as $propertyId => $unused) {
					$property = $configProperties[$propertyId];
		    		$data[$propertyId] = $request->getPost(($propertyId));
		    	}
				if ($term->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');

	    		// Atomically save
	    		$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$term->id) $rc = $term->add();
	    			elseif ($action == 'delete') $rc = $term->delete($request->getPost('update_time'));
	    			else $rc = $term->update($request->getPost('term_update_time'));
    				if ($rc != 'OK') $error = $rc;
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
    
    	$view = new ViewModel(array(
    			'context' => $context,
				'termProperties' => $configProperties,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'term' => $term,
	    		'dropbox' => $dropbox,
	    		'documentList' => $documentList,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
    			'updatePage' => $updatePage,
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
    	$nbTerm = $request->getPost('nb-term');
    
    	$terms = array();
    	for ($i = 0; $i < $nbTerm; $i++) {
    		$term = Term::get($request->getPost('term_'.$i));
    		$terms[] = $term;
    	}
    	$input = $term->properties;
    	$input['status'] = '';
    
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
    			foreach ($context->getConfig('commitmentTerm/group') as $propertyId => $options) {
    				if ($request->getPost($propertyId.'_check')) $data[$propertyId] = $request->getPost($propertyId);
    			}
    			foreach ($terms as $term) {
    				// Atomically save
    				$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    					if ($term->loadData($data) != 'OK') throw new \Exception('View error');
    					$rc = $term->update(null);
    					if ($rc != 'OK') {
    						$connection->rollback();
    						$error = $rc;
    					}
    					$message = 'OK';
    					$connection->commit();
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
    		'terms' => $terms,
    		'input' => $input,
    		'places' => Place::getList(array()),
    		'termGroupPage' => $context->getConfig('commitmentTerm/group'),
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function debitAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$cursor = Place::getList([]);
		$places = array();
		foreach ($cursor as $place_id => $place) $places[$place_id] = ['caption' => $place->caption];
    	
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	 
    	$view = new ViewModel(array(
    		'context' => $context,
    		'places' => $places,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function generateDebit($terms, $interaction_id, $sum, $collection_date, $config)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$content = array();
    	$content['GrpHdr'] = array();
    	$content['GrpHdr']['MsgId'] = $interaction_id;
    	$content['GrpHdr']['CreDtTm'] = date('Y-m-d').'T'.date('h:i:s');
    	$content['GrpHdr']['NbOfTxs'] = count($terms);
    	$content['GrpHdr']['CtrlSum'] = $sum;
    	$content['GrpHdr']['InitgPty'] = array();
    	$content['GrpHdr']['InitgPty']['Nm'] = $config['InitgPty/Nm'];
    	$content['PmtInf'] = array();
    	$content['PmtInf']['PmtInfId'] = $context->getInstance()->caption.' '.$interaction_id;
    	$content['PmtInf']['PmtMtd'] = 'DD';
    	$content['PmtInf']['NbOfTxs'] = count($terms);
    	$content['PmtInf']['CtrlSum'] = $sum;
    	$content['PmtInf']['PmtTpInf'] = array();
    	$content['PmtInf']['PmtTpInf']['SvcLvl'] = array();
    	$content['PmtInf']['PmtTpInf']['SvcLvl']['Cd'] = 'SEPA';
    	$content['PmtInf']['PmtTpInf']['LclInstrm'] = array();
    	$content['PmtInf']['PmtTpInf']['LclInstrm']['Cd'] = 'CORE';
    	$content['PmtInf']['PmtTpInf']['SeqTp'] = 'OOFF';
    	$content['PmtInf']['ReqdColltnDt'] = ($collection_date) ? $collection_date : date('Y-m-d');
    	$content['PmtInf']['Cdtr'] = array();
    	$content['PmtInf']['Cdtr']['Nm'] = $config['Cdtr/Nm'];
    	$content['PmtInf']['CdtrAcct'] = array();
    	$content['PmtInf']['CdtrAcct']['Id'] = array();
    	$content['PmtInf']['CdtrAcct']['Id']['IBAN'] = $config['CdtrAcct/Id/IBAN'];
    	$content['PmtInf']['CdtrAgt'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId']['Othr'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId']['Othr']['Id'] = 'NOTPROVIDED';
    	$content['PmtInf']['CdtrSchmeId'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['Id'] = $config['CdtrSchmeId/Id/PrvtId/Othr/Id'];
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm']['Prtry'] = 'SEPA';
    
    	$content['PmtInf']['DrctDbtTxInf'] = array();
    	foreach ($terms as $term) {
    		$row = array();
    		$row['PmtId'] = array();
    		$row['PmtId']['EndToEndId'] = substr(($term['reference']) ? $term['reference'] : $term['commitment_caption'], 0, 35);
    		$row['InstdAmt'] = round($term['amount'], 2);
    		$row['DrctDbtTx'] = array();
    		$row['DrctDbtTx']['MndtRltdInf'] = array();
    		$row['DrctDbtTx']['MndtRltdInf']['MndtId'] = $term['transfer_order_id'];
    		$row['DrctDbtTx']['MndtRltdInf']['DtOfSgntr'] = $term['transfer_order_date'];
    		$row['DbtrAgt'] = array();
    		$row['DbtrAgt']['FinInstnId'] = array();
    		$row['DbtrAgt']['FinInstnId']['Othr'] = array();
    		$row['DbtrAgt']['FinInstnId']['Othr']['Id'] = 'NOTPROVIDED';
    		$row['Dbtr'] = array();
    		$row['Dbtr']['Nm'] = $term['name'];
    		$row['DbtrAcct'] = array();
    		$row['DbtrAcct']['Id'] = array();
    		$row['DbtrAcct']['Id']['IBAN'] = (array_key_exists('bank_identifier', $term)) ? $term['bank_identifier'] : '';
/*			$row['RgltryRptg'] = array();
    		$row['RgltryRptg']['Dtls'] = array();
    		$row['RgltryRptg']['Dtls']['Cd'] = $config['DrctDbtTxInf/RgltryRptg/Dtls/Cd'];*/
    		$content['PmtInf']['DrctDbtTxInf'][] = $row;
    	}
    	return $content;
    }
    
    public function debitXmlAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place_id = $this->params()->fromRoute('place_id');
    	$place = Place::get($place_id);
    	if ($place && array_key_exists('commitmentTerm/debit', $place->config)) $config = $place->config['commitmentTerm/debit'];
    	else $config = $context->getConfig('commitmentTerm/debit');
    	$passphrase = $this->params()->fromQuery('passphrase');

    	$termIds = explode(',', $this->params()->fromQuery('terms'));
    	$terms = array();
    	$sum = 0;
    	foreach ($termIds as $term_id) {
    		$term = Term::get($term_id, 'id', $passphrase)->properties;
    		$terms[$term['id']] = $term;
    		$sum += $term['amount'];
    	}

    	// Instanciate an interaction row for storing the XML content in database
    	$interaction = Interaction::instanciate();
    	$interaction->status = 'new';
    	$interaction->type = 'application';
    	$interaction->category = 'debit';
    	$interaction->direction = 'O';
    	$interaction->format = 'text/xml';
    	$interaction->route = 'interaction/download';
    	$interaction->reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
    	$interaction->add();

    	$content = $this->generateDebit($terms, $interaction->id, $sum, $term['collection_date'], $config);

    	header('Content-Type: application/xml; charset=utf-8');
		header("Content-disposition: attachment; filename=debit-".date('Y-m-d').".xml");
		$xmlContent = ArrayToXmlViewHelper::convert($content);
    	$interaction->content = $xmlContent;
		$interaction->update(null);
    	echo $xmlContent;
		return $this->response;
    }

    public function debitSsmlAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place_id = $this->params()->fromRoute('place_id');
    	$place = Place::get($place_id);
    	if ($place && array_key_exists('commitmentTerm/debit', $place->config)) $config = $place->config['commitmentTerm/debit'];
    	else $config = $context->getConfig('commitmentTerm/debit');
    	$passphrase = $this->params()->fromQuery('passphrase');
    
    	$termIds = explode(',', $this->params()->fromQuery('terms'));
    	$terms = array();
    	$sum = 0;
    	foreach ($termIds as $term_id) {
    		$term = Term::get($term_id, 'id', $passphrase)->properties;
    		$terms[$term['id']] = $term;
    		$sum += $term['amount'];
    	}
    
    	$content = $this->generateDebit($terms, 0, $sum, $term['collection_date'], $config);
		ArrayToSsmlViewHelper::convert($content);
    	
    	$view = new ViewModel(array());
		$view->setTerminal(true);
		return $view;
    }
    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the term
		$term = Term::get($id);
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
    			$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$rc = $term->delete($term->update_time);
					if ($rc != 'OK') {
						$connection->rollback();
						$error = $rc;
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
    		'term' => $term,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }
    
    public function invoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the term
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$term = Term::get($id);
    	$commitment = Commitment::get($term->commitment_id);
    	$account = Account::get($commitment->account_id);

    	$commitmentMessage = CommitmentMessage::instanciate('invoice');
    	$invoice = array();
    	
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

    			// Atomically save
    			$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$year = CommitmentYear::getcurrent($commitment->place_id);
    				if (!$year) $year = CommitmentYear::instanciate(date('Y'));
					$mask = $context->getConfig('commitment/invoice_identifier_mask');
					$arguments = array();
					foreach ($mask['params'] as $param) {
						if ($param == 'year') $arguments[] = date('Y');
						elseif ($param == 'month') $arguments[] = date('m');
						elseif ($param == 'counter') $arguments[] = $year->next_value;
					}
					$invoice['identifier'] = vsprintf($context->localize($mask['format']), $arguments);
    				$commitmentMessage->status = 'new';
    				$commitmentMessage->account_id = $account->id;
    				$commitmentMessage->identifier = $context->getInstance()->fqdn.'_'.$invoice['identifier'];
    				$commitmentMessage->direction = 'O';
    				$commitmentMessage->format = 'application/json';
    				$year->increment();
    				$invoice['date'] = date('Y-m-d');
    				$invoice['description'] = array();
    				$invoice['description'][] = array('title' => 'Description', 'value' => $commitment->description);
    				$invoice['description'][] = array('title' => 'Libellé', 'value' => $commitment->caption.' - '.$term->caption);
    				$invoice['currency_symbol'] = $context->getConfig('commitment')['currencySymbol'];
			    	$invoice['tax'] = 'excluding';
			    	$line = array();
			    	$line['caption'] = $term->caption;
			    	$line['tax_rate'] = 0.2;
			    	$line['unit_price'] = round($term->amount / (1 + $line['tax_rate']), 2);
			    	$line['quantity'] = 1;
			    	$line['amount'] = $line['unit_price'] * $line['quantity'];
			    	$invoice['lines'] = array($line);
			    	$invoice['excluding_tax'] = $line['amount'];
			    	$invoice['taxable_1_total'] = $line['amount'];
			    	$invoice['tax_1_amount'] = $term->amount - $line['unit_price'];
			    	$invoice['tax_inclusive'] = $term->amount;
    			    if ($term->status == 'expected' && $context->getConfig('commitment/invoice_bank_details')) {
					    $invoice['settled_amount'] = 0;
    			    	$invoice['still_due'] = $term->amount;
    			    }
    			    else {
    			    	$invoice['settled_amount'] = $term->amount;
    			    	$invoice['still_due'] = 0;
    			    }
				    $invoice['tax_mention'] = $context->getConfig('commitment/invoice_tax_mention');
    			    if ($term->status == 'expected' && $context->getConfig('commitment/invoice_bank_details')) {
			    		$invoice['bank_details'] = $context->getConfig('commitment/invoice_bank_details');
			    		$invoice['footer_mention_1'] = $context->getConfig('commitment/invoice_footer_mention_1');
			    		$invoice['footer_mention_2'] = $context->getConfig('commitment/invoice_footer_mention_2');
			    		$invoice['footer_mention_3'] = $context->getConfig('commitment/invoice_footer_mention_3');
			    	}
					$commitmentMessage->content = json_encode($invoice, JSON_PRETTY_PRINT);
			    	$rc = $commitmentMessage->add();
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				else {
    					$term->status = 'invoiced';
    					$term->invoice_id = $commitmentMessage->id;
    					$rc = $term->update($request->getPost('update_time'));
	    				if ($rc != 'OK') {
	    					$connection->rollback();
	    					$error = $rc;
	    				}
	    				else {
	    					$connection->commit();
	    					$message = 'OK';
	    				}
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
    		'term' => $term,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }
}
