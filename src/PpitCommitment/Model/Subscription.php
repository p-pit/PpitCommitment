<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Place;
use PpitCore\Model\Document;
use PpitCore\Model\User;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Subscription implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $type;
    public $product_identifier;
    public $description;
    public $unit_price;
    public $opening_date;
    public $closing_date;
    public $audit;
    public $update_time;

    // Transient properties
    public $comment;
    public $properties;

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
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->product_identifier = (isset($data['product_identifier'])) ? $data['product_identifier'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->unit_price = (isset($data['unit_price'])) ? $data['unit_price'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date']) && $data['closing_date'] != '9999-12-31') ? $data['closing_date'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['type'] = $this->type;
    	$data['product_identifier'] =  $this->product_identifier;
    	$data['description'] =  $this->description;
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['audit'] =  ($this->audit) ? json_encode($this->audit) : null;
		return $data;
    }
    
    public static function getList($params, $major, $dir, $mode = 'todo')
    {
    	$select = Subscription::getTable()->getSelect()
			->order(array($major.' '.$dir, 'product_identifier', 'closing_date DESC'));
		$where = new Where;
        
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->greaterThanOrEqualTo('closing_date', date('Y-m-d'));
    	}
    	else {

    		// Set the filters
    		foreach ($params as $propertyId => $property) {
				if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_subscription.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_subscription.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('commitment_subscription.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
		
    	$select->where($where);
		$cursor = Subscription::getTable()->selectWith($select);
		$subscriptions = array();
		foreach ($cursor as $subscription) {
			$subscription->properties = $subscription->toArray();
			$subscriptions[$subscription->id] = $subscription;
		}
		return $subscriptions;
    }

    public static function get($id, $column = 'id')
    {
    	$subscription = Subscription::getTable()->get($id, $column);
    	return $subscription;
    }

    public static function getCurrent($product)
    {
    	$select = Subscription::getTable()->getSelect()
    		->order(array('closing_date ASC'));
		$where = new Where;
    	$where->equalTo('product_identifier', $product);
		$where->greaterThanOrEqualTo('closing_date', date('Y-m-d'));
		$select->where($where);
		$cursor = Subscription::getTable()->selectWith($select);
		foreach ($cursor as $subscription) return $subscription;
    	return null;
    }
    
    public static function instanciate()
    {
		$subscription = new Subscription;
		$subscription->audit = array();
		return $subscription;
    }

    public function loadData($data) {
    
    	$context = Context::getCurrent();

        if (array_key_exists('type', $data)) {
		    $this->type = trim(strip_tags($data['type']));
		    if (!$this->type || strlen($this->type) > 255) return 'Integrity';
		}
    	if (array_key_exists('product_identifier', $data)) {
		    $this->product_identifier = trim(strip_tags($data['product_identifier']));
		    if (!$this->product_identifier || strlen($this->product_identifier) > 255) return 'Integrity';
		}
		if (array_key_exists('description', $data)) {
	    	$this->description = trim(strip_tags($data['description']));
	    	if (strlen($this->description) > 2047) return 'Integrity';
		}
    	if (array_key_exists('unit_price', $data)) {
	    	$this->unit_price = trim(strip_tags($data['unit_price']));
    	}
		if (array_key_exists('opening_date', $data)) {
	    	$this->opening_date = trim(strip_tags($data['opening_date']));
	    	if (!$this->opening_date || !checkdate(substr($this->opening_date, 5, 2), substr($this->opening_date, 8, 2), substr($this->opening_date, 0, 4))) return 'Integrity';
		}
		if (array_key_exists('closing_date', $data)) {
	    	$this->closing_date = trim(strip_tags($data['closing_date']));
	    	if ($this->closing_date && !checkdate(substr($this->closing_date, 5, 2), substr($this->closing_date, 8, 2), substr($this->closing_date, 0, 4))) return 'Integrity';
		}
        if (array_key_exists('comment', $data)) {
		    $this->comment = trim(strip_tags($data['comment']));
		    if (strlen($this->comment) > 2047) return 'Integrity';
		}
		if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
    	$this->properties = $this->toArray();
    	
    	// Update the audit
    	$this->audit[] = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
    			'comment' => $this->comment,
    	);

    	return 'OK';
    }

    public function add()
    {
    	$context = Context::getCurrent();

    	$this->id = null;
    	Subscription::getTable()->save($this);
    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$subscription = Subscription::get($this->id);

    	// Isolation check
    	if ($subscription->update_time > $update_time) return 'Isolation';
    	 
    	Subscription::getTable()->save($this);
    
    	return 'OK';
    }

    public function isDeletable()
    {
    	$context = Context::getCurrent();
    
    	// Check dependencies
    	$config = $context->getConfig();
    	foreach($config['ppitCommitmentDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}

    	if (Generic::getTable()->cardinality('commitment', array('subscription_id' => $this->id)) > 0) return false;

    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$subscription = Subscription::get($this->id);
    
    	// Isolation check
    	if ($subscription->update_time > $update_time) return 'Isolation';
    	 
    	Subscription::getTable()->delete($this->id);
    
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
    	if (!Subscription::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Subscription::$table = $sm->get('PpitCommitment\Model\SubscriptionTable');
    	}
    	return Subscription::$table;
    }
}