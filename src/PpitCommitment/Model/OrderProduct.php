<?php
namespace PpitOrder\Model;

use PpitCore\Model\Context;
use PpitEquipment\Model\Equipment;
use PpitMasterData\Model\Product;
use PpitMasterData\Model\ProductOption;
use PpitMasterData\Model\ProductOptionMatrix;
use PpitOrder\Model\Message;
use Zend\Http\Client;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class OrderProduct implements InputFilterAwareInterface
{
    public $id;
    public $order_id;
    public $product_id;
    public $line_identifier;
    public $product_reference;
    public $product_caption;
    public $equipment_id;
    public $equipment_identifier;
    public $changed_equipment_identifier;
    public $price;
	public $hoped_delivery_date;
	public $shipment_date;
	public $delivery_date;
	public $commissioning_date;
	public $comment;
	public $shipment_message_id;
	public $delivery_message_id;
	public $commissioning_message_id;
	public $update_time;

	// Denormalized properties
	public $caption;

	// Additionnal properties from joined tables
	public $brand;
	public $order_identifier;
	
	// Transient properties
	public $option_list = array();
	public $option_price;
	public $matrix;
	public $order;
	
    protected $inputFilter;

    // Static fields
    private static $table;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->order_id = (isset($data['order_id'])) ? $data['order_id'] : null;
        $this->product_id = (isset($data['product_id'])) ? $data['product_id'] : null;
        $this->line_identifier = (isset($data['line_identifier'])) ? $data['line_identifier'] : null;
        $this->product_reference = (isset($data['product_reference'])) ? $data['product_reference'] : null;
        $this->product_caption = (isset($data['product_caption'])) ? $data['product_caption'] : null;
        $this->equipment_id = (isset($data['equipment_id'])) ? $data['equipment_id'] : null;
        $this->equipment_identifier = (isset($data['equipment_identifier'])) ? $data['equipment_identifier'] : null;
        $this->changed_equipment_identifier = (isset($data['changed_equipment_identifier'])) ? $data['changed_equipment_identifier'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        $this->hoped_delivery_date = (isset($data['hoped_delivery_date'])) ? $data['hoped_delivery_date'] : null;
        $this->shipment_date = (isset($data['shipment_date'])) ? $data['shipment_date'] : null;
        $this->delivery_date = (isset($data['delivery_date'])) ? $data['delivery_date'] : null;
        $this->commissioning_date = (isset($data['commissioning_date'])) ? $data['commissioning_date'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
        $this->shipment_message_id = (isset($data['shipment_message_id'])) ? $data['shipment_message_id'] : null;
        $this->delivery_message_id = (isset($data['delivery_message_id'])) ? $data['delivery_message_id'] : null;
        $this->commissioning_message_id = (isset($data['commissioning_message_id'])) ? $data['commissioning_message_id'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Denormalized properties
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        
		// Additionnal properties from joined tables
        $this->brand = (isset($data['brand'])) ? $data['brand'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['order_id'] = (int) $this->order_id;
    	$data['product_id'] = (int) $this->product_id;
    	$data['line_identifier'] = (int) $this->line_identifier;
    	$data['product_reference'] = $this->product_reference;
    	$data['equipment_id'] = (int) $this->equipment_id;
    	$data['equipment_identifier'] = $this->equipment_identifier;
    	$data['changed_equipment_identifier'] = $this->changed_equipment_identifier;
    	$data['price'] = (float) $this->price;
    	$data['hoped_delivery_date'] = ($this->hoped_delivery_date) ? $this->hoped_delivery_date : null;
    	$data['shipment_date'] = ($this->shipment_date) ? $this->shipment_date : null;
    	$data['delivery_date'] = ($this->delivery_date) ? $this->delivery_date : null;
    	$data['commissioning_date'] = ($this->commissioning_date) ? $this->commissioning_date : null;
    	$data['comment'] = $this->comment;
    	$data['shipment_message_id'] = $this->shipment_message_id;
    	$data['delivery_message_id'] = $this->delivery_message_id;
    	$data['commissioning_message_id'] = $this->commissioning_message_id;
    	 
        // Denormalized properties
    	$data['caption'] = $this->caption;

    	return $data;
    }

    public static function getList($order, $major = 'id', $dir = '')
    {
		// Retrieve the options
    	$select = ProductOption::getTable()->getSelect();
    	$cursor = ProductOption::getTable()->selectWith($select);
    	$options = array();
    	foreach($cursor as $option) {
    		$option->price = $option->prices[$order->tax_regime];
    		$options[$option->id] = $option;
    	}
    	
    	// Retrieve the option matrix
    	$select = ProductOptionMatrix::getTable()->getSelect()->order(array('product_id'));
    	$cursor = ProductOptionMatrix::getTable()->selectWith($select);
    	$matrix = array();
    	$current = null;
    	foreach($cursor as $cell) {
    		if ($cell->product_id != $current) {
    			$current = $cell->product_id;
    			$matrix[$current] = array();
    		}
    		$matrix[$current][] = $cell;
    	}

    	// Retrieve the order products
    	$select = OrderProduct::getTable()->getSelect()
    		->join('md_product', 'order_product.product_id = md_product.id', array('brand', 'product_reference' => 'reference', 'product_caption' => 'caption'), 'left')
    		->where(array('order_id' => $order->id))
    		->order(array($major.' '.$dir, 'id'));
    	$cursor = OrderProduct::getTable()->selectWith($select);
    	$orderProducts = array();
    	foreach ($cursor as $orderProduct) {
    		
    		if ($orderProduct->product_id) {

				// Retrieve the available options for this product
				$orderProduct->option_list = array();
				foreach($options as $option) {
					if ($option->product_id == $orderProduct->product_id) {
						$copy = clone $option;
						$copy->selected = false;
						$orderProduct->option_list[$option->id] = $copy;
					}
				}

				// Retrieve the option matrix
				$orderProduct->matrix = isset($matrix[$orderProduct->product_id]) ? $matrix[$orderProduct->product_id] : array();
    		}

    		$orderProducts[$orderProduct->id] = $orderProduct;
    	}

    	// Select all the options for this order and store in the working array
    	$select = OrderProductOption::getTable()->getSelect()
	    	->join('order_product', 'order_product_option.order_product_id = order_product.id', array('order_id'), 'left')
    		->join('md_product_option', 'order_product_option.product_option_id = md_product_option.id', array(), 'left')
    		->where(array('order_id' => $order->id));
    	$cursor = OrderProductOption::getTable()->selectWith($select);
    	foreach ($cursor as $orderProductOption) {
    		$orderProduct = $orderProducts[$orderProductOption->order_product_id];
    		$orderProduct->option_list[$orderProductOption->product_option_id]->selected = true;
    	}

    	return $orderProducts;
    }
    
    public static function get($id)
    {
    	$context = Context::getCurrent();
    	$orderProduct = OrderProduct::getTable()->get($id);
    	if ($orderProduct->product_id) {
    		$product = Product::getTable()->get($orderProduct->Product_id);
    		$orderProduct->product_reference = $product->reference;
    		$orderProduct->product_caption = $product->caption;
    	}
    	
    	// Retrieve the order properties
    	$order = Order::get($orderProduct->order_id);
    	$orderProduct->order = $order;
    	$orderProduct->order_identifier = $order->identifier;

    	return $orderProduct;
    }

    public static function instanciate($order_id)
    {
    	$orderProduct = new OrderProduct;
    	$orderProduct->order_id = $order_id;
    	return $orderProduct;
    }

    public function loadData($data, $action) {
    
    	$context = Context::getCurrent();

    	// Retrieve the data from the request
    	$this->product_id = $data['product_id'];
    	$this->product_reference = $data['product_reference'];
    	$this->product_caption = $data['product_caption'];
    	$this->equipment_identifier = trim(strip_tags($data['equipment_identifier']));
    	$this->changed_equipment_identifier = trim(strip_tags($data['changed_equipment_identifier']));
    	$this->price = (float) $data['price'];
    	$this->hoped_delivery_date = trim(strip_tags($data['hoped_delivery_date']));
    	$this->shipment_date = trim(strip_tags($data['shipment_date']));
    	$this->delivery_date = trim(strip_tags($data['delivery_date']));
    	$this->commissioning_date = trim(strip_tags($data['commissioning_date']));
    	$this->comment = trim(strip_tags($data['comment']));

    	// Check integrity
    
    	if (strlen($this->product_reference) > 255) return 'Integrity';
    	if (strlen($this->product_caption) > 255) return 'Integrity';
    	if (strlen($this->comment) > 2047) return 'Integrity';

    	// Change the status
    	if ($action == 'ship') {
    		$this->status = 'shipped';
    	}
    	elseif ($action == 'deliver') {
    		$this->status = 'delivered';
    	}
    	elseif ($action == 'commission') {
    		$this->status = 'commissioned';
    	}
        elseif ($action == 'change') {
    		$this->status = 'changed';
    	}
    	// Update the audit
    	if ($action != 'reinit') {
    		$this->order->audit[] = array(
    				'status' => $this->status,
    				'time' => Date('Y-m-d G:i:s'),
    				'n_fn' => $context->getFormatedName(),
    				'comment' => $this->comment,
    		);
    	}
        return 'OK';
    }

    public function loadDataFromRequest($request, $action)
    {
    	$data = array();
    	$data['product_id'] = $request->getPost('product_id');
    	$data['product_reference'] = $request->getPost('product_reference');
    	$data['product_caption'] = $request->getPost('product_caption');
    	$data['equipment_identifier'] = $request->getPost('equipment_identifier');
    	$data['changed_equipment_identifier'] = $request->getPost('changed_equipment_identifier');
    	$data['price'] = $request->getPost('price');
    	$data['hoped_delivery_date'] = $request->getPost('hoped_delivery_date');
    	$data['shipment_date'] = $request->getPost('shipment_date');
    	$data['delivery_date'] = $request->getPost('delivery_date');
    	$data['commissioning_date'] = $request->getPost('commissioning_date');
    	$data['comment'] = $request->getPost('comment');
    	$return = $this->loadData($data, $action);
    	if ($return != 'OK') throw new \Exception($return);
    	return $return;
    }

    public function add()
    {
    	$context = Context::getCurrent();
    
    	$this->id = null;
    	OrderProduct::getTable()->save($this);
    
    	return ('OK');
    }
    
    public function update($update_time = null)
    {
    	$orderProduct = OrderProduct::get($this->id);
    
    	// Isolation check
    	if ($update_time && $orderProduct->update_time > $update_time) return 'Isolation';
    
    	OrderProduct::getTable()->save($this);
    	
    	return 'OK';
    }

    public function ship($action, $request)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Submit the response message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    
    	$client = new Client(
    			$context->getConfig()['ppitOrderSettings']['shipmentMessageUrl'],
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    
    	$client->setAuth('XEROX', $safe['ugap']['XEROX'], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('POST');
    
    	if ($action == 'ship') $xmlShipmentResponse = new XmlShipmentResponse('ASNLIV');
    	elseif ($action == 'deliver') $xmlShipmentResponse = new XmlShipmentResponse('ASNLIV1');
    	elseif ($action == 'commission') $xmlShipmentResponse = new XmlShipmentResponse('ASNLIV2');
    
    	$xmlShipmentResponse->setASNIssueDate(date('Y-m-d').'T'.date('G:i:s'));
    	$xmlShipmentResponse->setBuyerOrderNumber($this->order->identifier);
    	$xmlShipmentResponse->setType(($action == 'ship') ? 'Planned' : 'Actual');
    
    	if ($action == 'ship') $shipDate = $this->shipment_date;
    	elseif ($action == 'deliver') $shipDate = $this->delivery_date;
    	elseif ($action == 'commission') $shipDate = $this->commissioning_date;
    	$xmlShipmentResponse->setShipDate($shipDate.'T00:00:00');
    
    	$xmlShipmentResponse->setSellerParty($this->order->property_14);
    
    	$xmlMessage = Message::get($this->order->order_message_id);
    	$xmlOrder = new XmlOrder(new \SimpleXMLElement($xmlMessage->xml_content));
    	if ($this->changed_equipment_identifier) $equipment_identifier = $this->changed_equipment_identifier;
    	else $equipment_identifier = $this->equipment_identifier;
    	for ($i = 0; $i < $xmlOrder->getNumberOfLines(); $i++) {
    		$xmlShipmentResponse->addItemDetail($xmlOrder->getBuyerLineItemNum($i), $equipment_identifier, $shipDate.'T00:00:00');
    	}
    
    	//if ($action == 'deliver' || $action == 'commission') $xmlShipmentResponse->setProductIdentifier($this->equipment_identifier);
    	 
    	$return = $this->update($this->update_time);
    	if ($return != 'OK') return $return;
    	else {
    
    		// Save the message
    		$message = Message::instanciate('orderResponse/ship', $xmlShipmentResponse->asXML());
    		if ($action == 'ship') $message->type = 'ASNLIV';
    		elseif ($action == 'deliver') $message->type = 'ASNLIV1';
    		elseif ($action == 'commission') $message->type = 'ASNLIV2';
    		$message->identifier = $this->order_identifier;
    		$message->add();
    			
    		// Add the message id to the order line
    		if ($action == 'ship') $this->shipment_message_id = $message->id;
    		elseif ($action == 'deliver') $this->delivery_message_id = $message->id;
    		elseif ($action == 'commission') $this->commissioning_message_id = $message->id;
    
    		$xmlShipmentResponse->setASNNumber($message->id);
    		$content = $xmlShipmentResponse->asXML();
    		$message->identifier = $this->line_identifier;
    		$message->xml_content = $content;
    
    		// Post the confirmation message
    		$client->setRawBody($content);
    		$response = $client->send();
    		$message->http_status = $response->renderStatusLine();
    
    		// Write to the log
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
    			$logger = new \Zend\Log\Logger();
    			$logger->addWriter($writer);
    			$logger->info('ship;'.$this->line_identifier.';'.$response->renderStatusLine());
    		}
    
    		// Save the message
    		$message->update($message->update_time);
    			
    		return 'OK';
    	}
    }

    public function isUsed($object)
    {
    	// Allow or not deleting an equipment
    	if (get_class($object) == 'PpitEquipment\Model\Equipment') {
    		if (OrderProduct::getTable()->get($object->id, 'equipment_id')) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function isDeletable() {
    
    	// Check the order status
    	$order = Order::getTable()->get($this->order_id);
    	if ($order->status != 'new') return false;
    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$orderProduct = OrderProduct::get($this->id);
    
    	// Isolation check
    	if ($orderProduct->update_time > $update_time) return 'Isolation';

    	// Unlock the equipment
    	if ($this->equipment_id) $equipment = Equipment::getTable()->get($this->equipment_id);
    	if ($equipment->lock) $equipment->lock = null;
    	Equipment::getTable()->save($equipment);

    	OrderProduct::getTable()->delete($this->id);
    
    	return 'OK';
    }

 	public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

   public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!OrderProduct::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		OrderProduct::$table = $sm->get('PpitOrder\Model\OrderProductTable');
    	}
    	return OrderProduct::$table;
    }
}
    