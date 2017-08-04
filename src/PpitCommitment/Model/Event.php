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

class Event implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $status;
    public $type;
    public $category;
    public $begin_time;
    public $end_time;
    public $location;
    public $title;
    public $content;
    public $image;
    public $criteria;
    public $target;
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
        $this->begin_time = (isset($data['begin_time'])) ? $data['begin_time'] : null;
        $this->end_time = (isset($data['end_time']) && $data['end_time'] != '9999-12-31') ? $data['end_time'] : null;
        $this->location = (isset($data['location'])) ? $data['location'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : null;
        $this->criteria = (isset($data['criteria'])) ? json_decode($data['criteria'], true) : null;
        $this->target = (isset($data['target'])) ? json_decode($data['target'], true) : null;
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
    	$data['begin_time'] =  ($this->begin_time) ? $this->begin_time : null;
    	$data['end_time'] =  ($this->end_time) ? $this->end_time : '9999-12-31';
    	$data['location'] = $this->location;
    	$data['title'] = $this->title;
    	$data['content'] = $this->content;
    	$data['image'] = json_encode($this->image);
    	$data['criteria'] = json_encode($this->criteria);
    	$data['target'] = json_encode($this->target);
    	$data['audit'] =  ($this->audit) ? json_encode($this->audit) : null;
		return $data;
    }
    
    public static function getList($type, $params, $major, $dir, $mode = 'todo')
    {
    	$context = Context::getCurrent();
    	$select = Event::getTable()->getSelect()
			->order(array($major.' '.$dir, 'end_time DESC'));
		$where = new Where;
		$where->notEqualTo('status', 'deleted');
		$where->equalTo('commitment_event.type', $type);
		
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->greaterThanOrEqualTo('end_time', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 7 days')));
    	}
    	else {

    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (!array_key_exists($propertyId, $context->getConfig('commitmentEvent/update'.(($type) ? '/'.$type : ''))['criteria'])) {
					if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
	    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
	    			else $where->like($propertyId, '%'.$params[$propertyId].'%');
    			}
    		}
    	}

    	$select->where($where);
		$cursor = Event::getTable()->selectWith($select);
		$criteria = $context->getConfig('commitmentEvent/update'.(($type) ? '/'.$type : ''))['criteria'];
		$events = array();
		foreach ($cursor as $event) {
			$keep = true;
			foreach ($params as $propertyId => $property) {
				if (array_key_exists($propertyId, $criteria) && !array_key_exists($propertyId, $event->criteria)) $keep = false;
				else {
					if (substr($propertyId, 0, 4) == 'min_' && $event->criteria[$propertyId] < $params[$propertyId]) $keep = false;
	    			elseif (substr($propertyId, 0, 4) == 'max_' && $event->criteria[$propertyId] > $params[$propertyId]) $keep = false;
	    			elseif (array_key_exists($propertyId, $criteria) && $params[$propertyId] != $event->criteria[$propertyId]) $keep = false;
				}
			}
			if ($keep) {
				$event->properties = $event->toArray();
				$events[$event->id] = $event;
			}
		}
		return $events;
    }

    public static function get($id, $column = 'id')
    {
    	$event = Event::getTable()->get($id, $column);
    	return $event;
    }
    
    public function retrieveTarget()
    {
    	$params = $this->criteria;
    	$this->matchingAccounts = Account::getList($this->type, $params, 'name', 'ASC', 'search');
    	return $this->matchingAccounts;
    }

    public static function retrieveComing($type, $category, $account_id)
    {
    	$select = Event::getTable()->getSelect()
    		->order(array('end_time DESC'));
		$where = new Where;
		$where->notEqualTo('status', 'deleted');
		$where->equalTo('type', $type);
		if ($category) $where->equalTo('category', $category);
		$where->greaterThanOrEqualTo('end_time', date('Y-m-d'));
		$where->like('target', '%"'.$account_id.'"%');
		$select->where($where);
    	$cursor = Event::getTable()->selectWith($select);
    	$events = array();
    	foreach ($cursor as $event) $events[] = $event;
    	return $events;
    }
    
    public static function instanciate($type = null)
    {
		$event = new Event;
		$event->status = 'new';
		$event->type = $type;
		$event->criteria = array();
		$event->target = array();
		$event->audit = array();
		return $event;
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
    	if (array_key_exists('begin_time', $data)) {
	    	$this->begin_time = trim(strip_tags($data['begin_time']));
	    	if (!$this->begin_time || !checkdate(substr($this->begin_time, 5, 2), substr($this->begin_time, 8, 2), substr($this->begin_time, 0, 4)) || substr($this->begin_time, 11, 2) < 0 || substr($this->begin_time, 11, 2) > 24 || substr($this->begin_time, 14, 2) < 0 || substr($this->begin_time, 14, 2) > 59 || substr($this->begin_time, 17, 2) < 0 || substr($this->begin_time, 17, 2) > 59) return 'Integrity';
		}
		if (array_key_exists('end_time', $data)) {
	    	$this->end_time = trim(strip_tags($data['end_time']));
	    	if ($this->end_time && (!checkdate(substr($this->end_time, 5, 2), substr($this->end_time, 8, 2), substr($this->end_time, 0, 4)) || substr($this->end_time, 11, 2) < 0 || substr($this->end_time, 11, 2) > 24 || substr($this->end_time, 14, 2) < 0 || substr($this->end_time, 14, 2) > 59 || substr($this->end_time, 17, 2) < 0 || substr($this->end_time, 17, 2) > 59)) return 'Integrity';
		}
    	if (array_key_exists('location', $data)) {
		    $this->location = trim(strip_tags($data['location']));
		    if (strlen($this->location) > 65535) return 'Integrity';
		}
		if (array_key_exists('title', $data)) {
		    $this->title = trim(strip_tags($data['title']));
		    if (!$this->title || strlen($this->title) > 65535) return 'Integrity';
		}
		if (array_key_exists('content', $data)) {
		    $this->content = $data['content'];
		    if (strlen($this->content) > 16777215) return 'Integrity';
		}
        if (array_key_exists('image', $data)) {
        	$this->image = array();
			foreach ($data['image'] as $attributeId => $value) {
				$value = trim(strip_tags($value));
				if (!$value || strlen($value) > 255) return 'Integrity';
				$this->image[$attributeId] = $value;
			}
        }
    	if (array_key_exists('criteria', $data)) {
			$this->criteria = array();
			foreach ($data['criteria'] as $criterionId => $criterion) {
				$criterion = trim(strip_tags($criterion));
				if (!$criterion || strlen($criterion) > 255) return 'Integrity';
				$this->criteria[$criterionId] = $criterion;
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
        if (array_key_exists('comment', $data)) {
		    $this->comment = trim(strip_tags($data['comment']));
		    if (strlen($this->comment) > 2047) return 'Integrity';
		}
		if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
    	$this->properties = $this->toArray();

    	if (!$this->end_time) {
    		if (substr($this->begin_time, 11, 2) == '23') $this->end_time = substr($this->begin_time, 0, 8).(substr($this->begin_time, 5, 2)+1).'T00'.substr($this->begin_time, 13, 5);
    		else $this->end_time = substr($this->begin_time, 0, 11).'T'.(substr($this->begin_time, 11, 2)+1).substr($this->begin_time, 13, 5);
    	}
    	
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
    	Event::getTable()->save($this);
    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$event = Event::get($this->id);

    	// Isolation check
    	if ($event->update_time > $update_time) return 'Isolation';
    	 
    	Event::getTable()->save($this);
    
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
    	$event = Event::get($this->id);
    
    	// Isolation check
    	if ($event->update_time > $update_time) return 'Isolation';
    	 
    	Event::getTable()->delete($this->id);
    
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
    	if (!Event::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Event::$table = $sm->get('PpitCommitment\Model\EventTable');
    	}
    	return Event::$table;
    }
}