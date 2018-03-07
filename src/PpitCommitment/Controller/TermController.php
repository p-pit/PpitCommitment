<?php

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentYear;
use PpitCommitment\Model\Term;
use PpitCommitment\ViewHelper\SsmlTermViewHelper;
use PpitCore\Model\Account;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Place;
use PpitCore\Form\CsrfForm;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TermController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());
		
    	$type = $this->params()->fromRoute('type', 0);

		$applicationId = 'p-pit-engagements';
		$applicationName = 'P-Pit Engagements';
		$currentEntry = $this->params()->fromQuery('entry', 'term');
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		
    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'types' => $types,
    			'currentEntry' => $currentEntry,
    			'type' => $type,
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('commitmentTerm/search')['main'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}

    	foreach ($context->getConfig('commitmentTerm/search')['more'] as $propertyId => $rendering) {
    	
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

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
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
    
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$terms = Term::getList($params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
    			'terms' => $terms,
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

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

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
	    	foreach (json_decode($response->getBody(), true)['entries'] as $entry) {
	    		$documentList[] = $entry['name'];
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
		    	foreach($context->getConfig('commitmentTerm/update') as $propertyId => $unused) {
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
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'term' => $term,
	    		'dropbox' => $dropbox,
	    		'documentList' => $documentList,
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
						if ($param == 'year') $arguments[] = substr($commitment->invoice_date, 0, 4);
						elseif ($param == 'month') $arguments[] = substr($commitment->invoice_date, 5, 2);
						elseif ($param == 'counter') $arguments[] = sprintf("%'.03d", $year->next_value);
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
