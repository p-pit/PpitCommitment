<?php
namespace PpitOrder\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitOrder\Model\Message;
use PpitOrder\Model\Order;
use PpitOrder\Model\OrderProduct;
use PpitOrder\Model\XmlOrder;
use PpitOrder\Model\XmlOrderResponse;
use PpitOrder\Model\XmlShipmentResponse;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

define ('DONE_DEAL_NUMBER', 31);
define ('SALES_ORDER_NUMBER', 36);
define ('SERIAL_NUMBER', 48);
define ('SHIPMENT_DATE', 54);
define ('DELIVERY_DATE', 56);
define ('COMMISSIONING_DATE', 57);

class OrderResponseController extends AbstractActionController
{	
	public function confirmAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		$id = (int) $this->params()->fromRoute('id', 0);
    	$act = $this->params()->fromRoute('act', null);
		if (!$id) return $this->redirect()->toUrl('index');
		$order = Order::get($id);

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
				$order->loadDataFromRequest($request, $act);
	
				// Atomically save
				$connection = Order::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
					// Submit the response message
					$safe = $context->getConfig()['ppitUserSettings']['safe'];
					
					$client = new Client(
							$context->getConfig()['ppitOrderSettings']['responseMessageUrl'],
							array(
									'adapter' => 'Zend\Http\Client\Adapter\Curl',
									'maxredirects' => 0,
									'timeout'      => 30,
							)
					);

					$client->setAuth('XEROX', $safe['ugap']['XEROX'], Client::AUTH_BASIC);
					$client->setEncType('text/xml');
					$client->setMethod('POST');

					$xmlOrderResponse = new XmlOrderResponse;
		    		$xmlOrderResponse->setResponseType(($act == 'confirm') ? 'Accepted' : 'Rejected');
//		    		$xmlOrderResponse->setItemDetailResponse(($act == 'confirm') ? 'ItemAccepted' : 'ItemRejected');
					if ($act == 'reject') {
						$xmlOrderResponse->setHeaderGeneralNote($order->comment);
						$xmlOrderResponse->setHeaderNoteId($order->property_7);
//						$xmlOrderResponse->setDetailGeneralNote($order->comment);
					}
					else {
						$xmlOrderResponse->setHeaderGeneralNote('');
						$xmlOrderResponse->setHeaderNoteId('');
//						$xmlOrderResponse->setDetailGeneralNote('');
					}
					// Save the message
				   	$message = Message::instanciate('orderResponse/confirm', $xmlOrderResponse->asXML());
				   	$message->add();

					// Update the order
					$order->confirmation_message_id = $message->id;
				   	$return = $order->update($request->getPost('update_time'), $act, $xmlOrderResponse);

					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {

			    		$xmlOrderResponse->setBuyerOrderResponseNumber($message->id);
						$content = $xmlOrderResponse->asXML();
						$message->identifier = $order->identifier;
						$message->content = $content;

						// Post the confirmation message
						$client->setRawBody($content);
						$response = $client->send();
						$message->http_status = $response->renderStatusLine();

						// Write to the log
				   		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
				   			$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
				   			$logger = new \Zend\Log\Logger();
				   			$logger->addWriter($writer);
				   			$logger->info('confirm;'.$order->identifier.';'.$response->renderStatusLine());
				   		}

				   		// Save the message
				   		$message->update($message->update_time);
				   		 
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
				'id' => $id,
				'act' => $act,
				'orderProperties' => $context->getInstance()->specifications['ppitCommitment']['properties'],
				'order' => $order,
				'csrfForm' => $csrfForm,
				'error' => $error,
				'message' => $message
		));
		$view->setTerminal(true);
		return $view;
	}

	public function shipAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the order line and the corresponding order
		$id = (int) $this->params()->fromRoute('id', 0);
		$action = $this->params()->fromRoute('act', null);

		if (!$id) return $this->redirect()->toUrl('index');
		$order = Order::get($id);
		$orderProduct = $order->uniqueOrderProduct;

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
				$orderProduct->loadDataFromRequest($request, $action);

				// Atomically save
				$connection = OrderProduct::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {

					if ($action != 'change') $return = $orderProduct->ship($action, $request);
					else $return = 'OK';
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;						
					}
		    		else {
				    	// Update the order and the unique order line
				    	if ($action == 'ship') $order->status = 'shipped';
				    	elseif ($action == 'deliver') $order->status = 'delivered';
				    	elseif ($action == 'commission') $order->status = 'commissioned';
	
				    	$return = $order->update($request->getPost('update_time'));
				    	if ($return != 'OK') {
							$connection->rollback();
							$error = $return;						
						}
			    		else {
					    	$return = $order->uniqueOrderProduct->update($order->uniqueOrderProduct->update_time);
					    	if ($return != 'OK') {
					    		$connection->rollback();
					    		$error = $return;
					    	}
							else {
						    	$connection->commit();
						    	$message = 'OK';
							}
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
				'id' => $id,
				'action' => $action,
				'orderProduct' => $orderProduct,
				'order' => $order,
				'csrfForm' => $csrfForm,
				'error' => $error,
				'message' => $message
		));
		$view->setTerminal(true);
		return $view;
	}

	public function testMessageAction()
   	{
		// Retrieve the context
		$context = Context::getCurrent();
   		$safe = $context->getConfig()['ppitUserSettings']['safe'];
		
		$client = new Client(
		$context->getConfig()['ppitOrderSettings']['responseMessageUrl'],
			array(
				'adapter' => 'Zend\Http\Client\Adapter\Curl',
				'maxredirects' => 0,
				'timeout'      => 30
			));
/*		$adapter = new Zend\Http\Client\Adapter\Socket();
		$adapter->setStreamContext(array(
			'ssl' => array(
				'verify_peer' => false,
				'allow_self_signed' => false,
				'cafile' => '/etc/ssl/certs/ugap_public_cert.cer',
				'verify_depth' => 5,
				'CN_match' => 'editest.ugap.fr'
			)
		));
		$client->setAdapter($adapter);*/

		$client->setAuth('XEROX', $safe['ugap']['XEROX'], Client::AUTH_BASIC);
		$client->setEncType('text/xml');
		$client->setMethod('POST');

		$message = Message::instanciate();
		$content = new XmlOrderResponse();
		$content->setBuyerOrderResponseNumber(111);
		$content->setOrderResponseIssueDate('2015-06-16T09:45:00');
		$content->setOrderReference('999');
		$content->setSellerParty('0000099201');
		$content->setBuyerParty('MIXT');
		$content->setResponseType('Accepted');
		$content->setShipDate('2015-06-28T12:00:00');
		$client->setRawBody($content->asXML());
		
		$response = $client->send();
   	
   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
   				'response'=> $response,
//				'config' => $context->getconfig(),
   		));
		$view->setTerminal(true);
       	return $view;
   	}

   	public function testReceiveAction()
   	{
   		// Retrieve the context
   		$context = Context::getCurrent();
   		$safe = $context->getConfig()['ppitUserSettings']['safe'];
   		$username = null;
   		$password = null;

   		// Check basic authentication
   		if (isset($_SERVER['PHP_AUTH_USER'])) {
   			$username = $_SERVER['PHP_AUTH_USER'];
   			$password = $_SERVER['PHP_AUTH_PW'];
   		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
   			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
   				list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
   		}
   		if ($username != 'XEROX' || $password != $safe['ugap']['XEROX']) {
   			$this->getResponse()->setStatusCode(401);
   			return $this->getResponse();
   		}

   		$request = $this->getRequest();
   	
   		$content = new \SimpleXMLElement($request->getContent());
   	
   		$this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
   		$this->getResponse()->setContent($content->asXML());
   		return $this->getResponse();
   	}
}
