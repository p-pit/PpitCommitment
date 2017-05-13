<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\PdfInvoiceViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Instance;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Vcard;
use PpitDocument\Model\DocumentPart;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;
use Zend\Log\Writer;

class CommitmentCreditController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
		$instance_id = $context->getInstanceId();

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();
    	 
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
	   	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the credits
    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'type'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC')); 
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
       	$credits = Credit::getList($params, $major, $dir, $mode);

    	// Submit the P-Pit get-list message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCommitment/P-Pit']['commitmentListMessage']['url'].'/'.$context->getInstance()->caption;
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    	 
    	$username = $context->getConfig()['ppitCoreSettings']['commitmentListMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    	$invoices = array();
    	$orders = array();
    	foreach (json_decode($response->getContent(), true) as $commitmentData) {
    		$commitment = new Commitment();
    		$commitment->exchangeArray($commitmentData);
    		if ($commitment->status != 'deleted' || $commitment->status != 'canceled') {
    			$orders[] = $commitment;
    		}
    	}
    	 
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'credits' => $credits,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    			'orders' => $orders,
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
		(new SsmlCreditViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=Fichier.xlsx ');
		$writer->save('php://output');
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $credit = Credit::get($id);
    	else $credit = Credit::instanciate();
    	
    	$credit->periods = array();
    	foreach ($credit->audit as $event) {
    		if (!array_key_exists($event['period'], $credit->periods)) $credit->periods[$event['period']] = array();
    		$credit->periods[$event['period']][] = $event;
    	}
    	 
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $credit->id,
    			'credit' => $credit,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function acceptAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Initialize the logger
    	$writer = new \Zend\Log\Writer\Stream('data/log/credit.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);
    
    	// Retrieve the commitment id
    	$id = $this->params()->fromRoute('id', null);
    
    	// Submit the commitmentGet message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCoreSettings']['commitmentGetMessage']['url'].'/'.$id;
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    			);
    
    	$username = $context->getConfig()['ppitCoreSettings']['commitmentGetMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    
    	$commitmentData = json_decode($response->getContent(), true);
    	if ($commitmentData['status'] == 'deleted' || $commitmentData['status'] == 'canceled') {
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
    				$url = $context->getConfig()['ppitCoreSettings']['commitmentPostMessage']['url'].'/'.$context->getInstance()->caption.'/'.$id;
    				$client = new Client(
    						$url,
    						array(
    								'adapter' => 'Zend\Http\Client\Adapter\Curl',
    								'maxredirects' => 0,
    								'timeout'      => 30,
    						)
    						);
    
    				$username = $context->getConfig()['ppitCoreSettings']['commitmentPostMessage']['user'];
    				$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
    				$client->setEncType('application/json');
    				$client->setMethod('POST');
    				$client->setRawBody(json_encode(array('n_fn' => $context->getFormatedName(), 'status' => 'approved', 'cgv' => $content)));
    				$response = $client->send();
    
    				// Write to the log
    				if ($context->getConfig()['isTraceActive']) {
    					$logger->info('credit/accept;'.$commitmentData['id'].';'.$url.';'.$response->renderStatusLine());
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
    			'content' => $content,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function settleAction()
    {
    	$context = Context::getCurrent();
    	$id = $this->params()->fromRoute('id', null);
    	if (array_key_exists('onlineSettlementRoute', $context->getConfig('ppitCoreSettings'))) {
	    	return $this->redirect()->toRoute($context->getConfig('ppitCoreSettings')['onlineSettlementRoute'], array('id' => $id));
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'error' => 'No online settlement solution is currently implemented on this installation.',
    	));
    	return $view;
    }
    
    public function downloadInvoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$id = $this->params()->fromRoute('id');
    	$proforma = $this->params()->fromQuery('proforma', null);
    	 
    	// Submit the P-Pit commitment-get message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCoreSettings']['invoiceGetMessage']['url'].'/'.$id;
    	if ($proforma) $url .= '?proforma=1';
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    			);
    
    	$username = $context->getConfig()['ppitCoreSettings']['invoiceGetMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
//    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'content' => $response->getContent(),
    	));
    	$view->setTerminal(true);
    	return $view;
    }
}
