<?php
namespace PpitOrder\Model;

use PpitCore\Model\Context;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class OrderProductOption implements InputFilterAwareInterface
{
    public $id;
    public $order_product_id;
    public $product_option_id;
	public $price;

	// Additionnal fields (not in database)
	public $caption;
	public $description;
	public $order_id;

	// Transitional fields
	public $selected;
	
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
        $this->order_product_id = (isset($data['order_product_id'])) ? $data['order_product_id'] : null;
        $this->product_option_id = (isset($data['product_option_id'])) ? $data['product_option_id'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;

        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->order_id = (isset($data['order_id'])) ? $data['order_id'] : null;
    }

    public function toArray() {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['order_product_id'] = (int) $this->order_product_id;
    	$data['product_option_id'] = (int) $this->product_option_id;
    	$data['price'] = (float) $this->price;
    	return $data;
    }

    public static function getList($order, $major = 'id', $dir = '')
    {    
    	// Retrieve the order products
    	$select = OrderProductOption::getTable()->getSelect()
    		->join('md_product', 'order_product.product_id = md_product.id', array('brand', 'product_reference' => 'reference', 'product_caption' => 'caption'), 'left')
    		->where(array('order_id' => $order->id))
    		->order(array($major.' '.$dir, 'id'));
    	$cursor = OrderProduct::getTable()->selectWith($select);
    	$orderProducts = array();
    	foreach ($cursor as $orderProduct) {
    		$orderProducts[$orderProduct->id] = $orderProduct;
    	}
    	return $orderProducts;
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
    	if (!OrderProductOption::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		OrderProductOption::$table = $sm->get('PpitOrder\Model\OrderProductOptionTable');
    	}
    	return OrderProductOption::$table;
    }
}
    