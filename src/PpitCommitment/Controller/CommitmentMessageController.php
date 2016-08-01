<?php
namespace PpitCommitment\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCommitment\Model\CommitmentMessage;
use Zend\Http\Client;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class CommitmentMessageController extends AbstractActionController
{
	public function indexAction()
    {
    	$context = Context::getCurrent();
//		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$type = $this->params()->fromRoute('type', null);
		$menu = $context->getConfig('menu');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'menu' => $menu,
    			'type' => $type,
    	));
    }
	
	public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Prepare the SQL request
    	$major = $this->params()->fromQuery('major', 'update_time');
    	$dir = $this->params()->fromQuery('dir', 'DESC');
    	$messages = CommitmentMessage::getList($major, $dir);

    	// Return the link list
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'major' => $major,
    		'dir' => $dir,
    		'messages' => $messages,
        ));
		$view->setTerminal(true);
		return $view;
    }

    public function ppitSubscribeAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

		// Atomically save
    	$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    	$connection->beginTransaction();
    	try {
    
    		$rc = CommitmentMessage::sendPpitSubscriptionMessage($this->getRequest());

    		if ($rc != '200' && $rc != '422') $connection->rollback();
    		else $connection->commit();
    	}
    	catch (\Exception $e) {
    		$connection->rollback();
    		throw $e;
    	}
    
    	$this->getResponse()->setStatusCode($rc);
    	return $this->getResponse();
    }

    public function importAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$xmlMessage = CommitmentMessage::instanciate();
    	
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
    	
    			$xmlMessage->loadDataFromRequest($request);
    	
    			// Atomically save
    			 
    			$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {

    				// Import the file
    				$xmlMessage->add();

    				$connection->commit();
    				$message = 'OK';
    			}
    			catch (Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'xmlMessage' => $xmlMessage,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function processAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    
    	$id = (int) $this->params()->fromRoute('message_id', 0);
    	if (!$id) return $this->redirect()->toUrl('index');
    	$xmlMessage = Message::get($id);
    
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
    
    			if ($context->getConfig('commitment')['importTypes'][$xmlMessage->type] == 'invoice') {
    				$rc = $xmlMessage->processInvoice($request);
    				if ($rc != 'OK') $error = $rc;
    			}
    			else {
    					
    				$resultMessage = array();
    				foreach (json_decode($xmlMessage->content, true) as $row) {
    
    					// Retrieve the order
    					$sales_order_number = $row[SALES_ORDER_NUMBER];
    					$order = Order::get($sales_order_number, 'property_9');
    					if ($order) {
    
    						// Check integrity
    						$error = false;
    							
    						$shipment_date = $row[SHIPMENT_DATE];
    						if ($shipment_date) {
    							if (!checkdate(substr($shipment_date, 3, 2), substr($shipment_date, 0, 2), substr($shipment_date, 6, 4))) $error = 'Consistency';
    							else $shipment_date = substr($shipment_date, 6, 4).'-'.substr($shipment_date, 3, 2).'-'.substr($shipment_date, 0, 2);
    						}
    						if ($shipment_date > date('Y-m-d')) $error = 'Consistency';
    
    						$delivery_date = $row[DELIVERY_DATE];
    						if ($delivery_date) {
    							if (!checkdate(substr($delivery_date, 3, 2), substr($delivery_date, 0, 2), substr($delivery_date, 6, 4))) $error = 'Consistency';
    							else $delivery_date = substr($delivery_date, 6, 4).'-'.substr($delivery_date, 3, 2).'-'.substr($delivery_date, 0, 2);
    						}
    						if ($delivery_date > date('Y-m-d')) $error = 'Consistency';
    							
    						$serial_number = $row[SERIAL_NUMBER];
    
    						$commissioning_date = $row[COMMISSIONING_DATE];
    						if ($commissioning_date) {
    							if (!checkdate(substr($commissioning_date, 3, 2), substr($commissioning_date, 0, 2), substr($commissioning_date, 6, 4))) $error = 'Consistency';
    							else $commissioning_date = substr($commissioning_date, 6, 4).'-'.substr($commissioning_date, 3, 2).'-'.substr($commissioning_date, 0, 2);
    						}
    
    						if ($error) $row[] = 'KO';
    						else {
    							// Shipment case
    							if ($order->status == 'registered' && $shipment_date) {
    								$row[] = 'ASNLIV';
    
    								// Atomically save
    								$connection = OrderProduct::getTable()->getAdapter()->getDriver()->getConnection();
    								$connection->beginTransaction();
    								try {
    										
    									$select = OrderProduct::getTable()->getSelect()->where(array('order_id' => $order->id));
    									$cursor = OrderProduct::getTable()->selectWith($select);
    									foreach ($cursor as $orderProduct) {
    										$orderProduct->order = $order;
    										$orderProduct->shipment_date = $shipment_date;
    										if ($serial_number) {
    											if (!$orderProduct->equipment_identifier) $orderProduct->equipment_identifier = $serial_number;
    											elseif ($serial_number != $orderProduct->equipment_identifier && !$orderProduct->changed_equipment_identifier) $orderProduct->changed_equipment_identifier = $serial_number;
    										}
    										$orderProduct->ship('ship', $request);
    									}
    									// Update the order
    									$order->status = 'shipped';
    									$order->audit[] = array(
    											'status' => $order->status,
    											'time' => Date('Y-m-d G:i:s'),
    											'n_fn' => $context->getFormatedName(),
    											'comment' => '',
    									);
    									$return = $order->update($order->update_time);
    									if ($return != 'OK') {
    										$connection->rollback();
    										$error = $return;
    									}
    									else {
    										$return = $orderProduct->update();
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
    								catch (\Exception $e) {
    									$connection->rollback();
    									throw $e;
    								}
    							}
    
    							// Delivery case
    							else if ($order->status == 'shipped' && $delivery_date && $serial_number) {
    								$row[] = 'ASNLIV1';
    
    								// Atomically save
    								$connection = OrderProduct::getTable()->getAdapter()->getDriver()->getConnection();
    								$connection->beginTransaction();
    								try {
    
    									$select = OrderProduct::getTable()->getSelect()->where(array('order_id' => $order->id));
    									$cursor = OrderProduct::getTable()->selectWith($select);
    									foreach ($cursor as $orderProduct) {
    										$orderProduct->order = $order;
    										$orderProduct->delivery_date = $delivery_date;
    										if ($serial_number) {
    											if (!$orderProduct->equipment_identifier) $orderProduct->equipment_identifier = $serial_number;
    											elseif ($serial_number != $orderProduct->equipment_identifier && !$orderProduct->changed_equipment_identifier) $orderProduct->changed_equipment_identifier = $serial_number;
    										}
    										$orderProduct->ship('deliver', $request);
    									}
    									// Update the order
    									$order->status = 'delivered';
    									$order->audit[] = array(
    											'status' => $order->status,
    											'time' => Date('Y-m-d G:i:s'),
    											'n_fn' => $context->getFormatedName(),
    											'comment' => '',
    									);
    									$return = $order->update($order->update_time);
    									if ($return != 'OK') {
    										$connection->rollback();
    										$error = $return;
    									}
    									else {
    										$return = $orderProduct->update();
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
    								catch (\Exception $e) {
    									$connection->rollback();
    									throw $e;
    								}
    							}
    						}
    					}
    					$resultMessage[] = $row;
    				}
    				$xmlMessage->content = json_encode($resultMessage);
    			}
    			$xmlMessage->http_status = 'Processed';
    			$xmlMessage->update($xmlMessage->update_time);
    			if (!$error) $message = 'OK';
    		}
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $config,
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function submitAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the message
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$xmlMessage = CommitmentMessage::getTable()->get($id);

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

//				$message->loadDataFromRequest($request);

				// Atomically save
		        	
				$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
		    	$connection->beginTransaction();
				try {
					// Submit the response message
					$safe = $context->getConfig()['ppitUserSettings']['safe'];
					if ($xmlMessage->type == 'ORDRSP') $url = $context->getConfig()['ppitOrderSettings']['responseMessageUrl'];
					elseif ($xmlMessage->type == 'ASNLIV' || $xmlMessage->type == 'ASNLIV1' || $xmlMessage->type == 'ASNLIV2') $url = $context->getConfig()['ppitOrderSettings']['shipmentMessageUrl'];

					$client = new Client(
							$url,
							array(
									'adapter' => 'Zend\Http\Client\Adapter\Curl',
									'maxredirects' => 0,
									'timeout'      => 30,
							)
					);

					$client->setAuth('XEROX', $safe['ugap']['XEROX'], Client::AUTH_BASIC);
					$client->setEncType('text/xml');
					$client->setMethod('POST');
					$client->setRawBody($xmlMessage->content);
					$response = $client->send();
		    		$xmlMessage->http_status = $response->renderStatusLine();

		    		// Write to the log
		    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
		    			$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
		    			$logger = new \Zend\Log\Logger();
		    			$logger->addWriter($writer);
		    			$logger->info('confirm;'.$xmlMessage->identifier.';'.$response->renderStatusLine());
		    		}
		    		$rc = $xmlMessage->update($request->getPost('update_time'));
		    		if ($rc != 'OK') {
		    			$error = $rc;
		    			$connection->rollback();
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
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function downloadAction()
    {
    	// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

       	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the XML message
    	$xmlMessage = CommitmentMessage::getTable()->get($id);
    
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function deleteAction()
    {
    	// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

       	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the XML message
    	$xmlMessage = CommitmentMessage::getTable()->get($id);

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

				// Atomically delete the message
		        try {
			    	$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
		    		$connection->beginTransaction();
					$xmlMessage->delete();
			        $connection->commit();
			        $message = 'OK';
				}
		    	catch (Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
    		}
    	}
    
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
		$view->setTerminal(true);
		return $view;
    }
}
