<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Notification implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $status;
    public $type;
    public $category;
    public $criteria;
    public $title;
    public $content;
    public $image;
    public $target;
    public $begin_date;
    public $end_date;
    public $audit;
    public $update_time;

    // Transient properties
    public $comment;
    public $properties;
    public $matchingAccounts;
    
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
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->category = (isset($data['category'])) ? $data['category'] : null;
        $this->criteria = (isset($data['criteria'])) ? json_decode($data['criteria'], true) : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : null;
        $this->target = (isset($data['target'])) ? json_decode($data['target'], true) : null;
        $this->begin_date = (isset($data['begin_date'])) ? $data['begin_date'] : null;
        $this->end_date = (isset($data['end_date']) && $data['end_date'] != '9999-12-31') ? $data['end_date'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['type'] = $this->type;
    	$data['category'] = $this->category;
    	$data['criteria'] = json_encode($this->criteria);
    	$data['title'] = $this->title;
    	$data['content'] = $this->content;
    	$data['image'] = json_encode($this->image);
    	$data['target'] = json_encode($this->target);
    	$data['begin_date'] =  ($this->begin_date) ? $this->begin_date : null;
    	$data['end_date'] =  ($this->end_date) ? $this->end_date : '9999-12-31';
    	$data['audit'] =  ($this->audit) ? json_encode($this->audit) : null;
		return $data;
    }
    
    public static function getList($type, $params, $major, $dir, $mode = 'todo')
    {
    	$context = Context::getCurrent();
    	$select = Notification::getTable()->getSelect()
			->order(array($major.' '.$dir, 'end_date DESC'));
		$where = new Where;
		$where->notEqualTo('status', 'deleted');
		$where->equalTo('commitment_notification.type', $type);
		
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->greaterThanOrEqualTo('end_date', date('Y-m-d'));
    	}
    	else {

    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (!array_key_exists($propertyId, $context->getConfig('commitmentNotification/update'.(($type) ? '/'.$type : ''))['criteria'])) {
					if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
	    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
	    			else $where->like($propertyId, '%'.$params[$propertyId].'%');
    			}
    		}
    	}
		
    	$select->where($where);
		$cursor = Notification::getTable()->selectWith($select);
		$criteria = $context->getConfig('commitmentNotification/update'.(($type) ? '/'.$type : ''))['criteria'];
		$notifications = array();
		foreach ($cursor as $notification) {
			$keep = true;
			foreach ($params as $propertyId => $property) {
				if (array_key_exists($propertyId, $criteria) && !array_key_exists($propertyId, $notification->criteria)) $keep = false;
				else {
					if (substr($propertyId, 0, 4) == 'min_' && $notification->criteria[$propertyId] < $params[$propertyId]) $keep = false;
	    			elseif (substr($propertyId, 0, 4) == 'max_' && $notification->criteria[$propertyId] > $params[$propertyId]) $keep = false;
	    			elseif ($params[$propertyId] != $notification->criteria[$propertyId]) $keep = false;
				}
			}
			if ($keep) {
				$notification->properties = $notification->toArray();
				$notifications[$notification->id] = $notification;
			}
		}
		return $notifications;
    }

    public static function get($id, $column = 'id')
    {
    	$notification = Notification::getTable()->get($id, $column);
    	return $notification;
    }
    
    public function retrieveTarget()
    {
    	$params = $this->criteria;
    	$this->matchingAccounts = Account::getList($this->type, $params, 'customer_name', 'ASC', 'search');
    	return $this->matchingAccounts;
    }

    public static function retrieveCurrents($type, $category, $account_id)
    {
    	$select = Notification::getTable()->getSelect()
    		->order(array('end_date DESC'));
		$where = new Where;
		$where->notEqualTo('status', 'deleted');
		$where->equalTo('type', $type);
		$where->equalTo('category', $category);
		$where->lessThanOrEqualTo('begin_date', date('Y-m-d'));
		$where->greaterThanOrEqualTo('end_date', date('Y-m-d'));
		$where->like('target', '%"'.$account_id.'"%');
		$select->where($where);
    	$cursor = Notification::getTable()->selectWith($select);
    	$notifications = array();
    	foreach ($cursor as $notification) $notifications[] = $notification;
    	return $notifications;
    }
    
    public static function instanciate($type = null)
    {
		$notification = new Notification;
		$notification->type = $type;
		$notification->image = array();
		$notification->criteria = array();
		$notification->target = array();
		$notification->audit = array();
		return $notification;
    }

    public function loadData($data) {
    
    	$context = Context::getCurrent();

        if (array_key_exists('status', $data)) {
		    $this->status = trim(strip_tags($data['status']));
		    if (strlen($this->status) > 255) return 'Integrity';
		}
    	if (array_key_exists('type', $data)) {
		    $this->type = trim(strip_tags($data['type']));
		    if (strlen($this->type) > 255) return 'Integrity';
		}
        if (array_key_exists('category', $data)) {
		    $this->category = trim(strip_tags($data['category']));
		    if (strlen($this->category) > 255) return 'Integrity';
		}
		if (array_key_exists('criteria', $data)) {
			$this->criteria = array();
			foreach ($data['criteria'] as $criterionId => $criterion) {
				$criterion = trim(strip_tags($criterion));
				if (!$criterion || strlen($criterion) > 255) return 'Integrity';
				$this->criteria[$criterionId] = $criterion;
			}
		}
        if (array_key_exists('title', $data)) {
		    $this->title = trim(strip_tags($data['title']));
		    if (!$this->title || strlen($this->title) > 255) return 'Integrity';
		}
		if (array_key_exists('content', $data)) {
		    $this->content = $data['content'];
		    if (strlen($this->content) > 16777215) return 'Integrity';
		}
        if (array_key_exists('image', $data)) {
        	$this->image = array();
			foreach ($data['image'] as $attributeId => $value) {
				$value = trim(strip_tags($value));
				if (strlen($value) > 255) return 'Integrity';
				if ($value) $this->image[$attributeId] = $value;
			}
        }
		if (array_key_exists('target', $data)) {
			$this->target = array();
			foreach ($data['target'] as $account_id => $unused) {
				$account_id = (int) $account_id;
				if (!$account_id) return 'Integrity';
				$this->target[$account_id] = null;
			}
		}
		if (array_key_exists('begin_date', $data)) {
	    	$this->begin_date = trim(strip_tags($data['begin_date']));
	    	if (!$this->begin_date || !checkdate(substr($this->begin_date, 5, 2), substr($this->begin_date, 8, 2), substr($this->begin_date, 0, 4))) return 'Integrity';
		}
		if (array_key_exists('end_date', $data)) {
	    	$this->end_date = trim(strip_tags($data['end_date']));
	    	if ($this->end_date && !checkdate(substr($this->end_date, 5, 2), substr($this->end_date, 8, 2), substr($this->end_date, 0, 4))) return 'Integrity';
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
    	$this->status = 'new';
    	Notification::getTable()->save($this);
    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$notification = Notification::get($this->id);

    	// Isolation check
    	if ($notification->update_time > $update_time) return 'Isolation';
    	 
    	Notification::getTable()->save($this);
    
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
    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$notification = Notification::get($this->id);
    
    	// Isolation check
    	if ($notification->update_time > $update_time) return 'Isolation';
    	 
    	Notification::getTable()->delete($this->id);
    
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
    	if (!Notification::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Notification::$table = $sm->get('PpitCommitment\Model\NotificationTable');
    	}
    	return Notification::$table;
    }
}