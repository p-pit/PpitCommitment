<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Community;
use PpitCore\Model\Vcard;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Place;
use PpitDocument\Model\Document;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Account implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $status;
    public $type;
    public $place_id;
    public $identifier;
    public $customer_community_id;
	public $customer_bill_contact_id;
    public $supplier_community_id;
    public $opening_date;
    public $closing_date;
    public $contact_history;
    public $terms_of_sales;
    public $property_1;
    public $property_2;
    public $property_3;
    public $property_4;
    public $property_5;
    public $property_6;
    public $property_7;
    public $property_8;
    public $property_9;
    public $property_10;
    public $json_property_1;
    public $json_property_2;
    public $audit;
    public $update_time;
        
    // Joined properties
    public $place_caption;
    public $customer_name;
    public $customer_status;
    public $contact_1_id;
    public $supplier_name;
    public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $email;
    public $birth_date;
    public $tel_work;
    public $tel_cell;
    public $photo_link_id;
    
    // Transient properties
    public $place;
	public $customer_community;
	public $supplier_community;
    public $contact_1;
    public $contact_1_status;
    public $contact_2;
    public $contact_2_status;
    public $contact_3;
    public $contact_3_status;
    public $contact_4;
    public $contact_4_status;
    public $contact_5;
    public $contact_5_status;
	public $properties;
    public $files;
	public $comment;
	public $is_notified;
	public $locale;
	public $username;
	public $new_password;
	public $user;
	public $userContact;
	
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
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->customer_community_id = (isset($data['customer_community_id'])) ? $data['customer_community_id'] : null;
        $this->customer_bill_contact_id = (isset($data['customer_bill_contact_id'])) ? $data['customer_bill_contact_id'] : null;
        $this->supplier_community_id = (isset($data['supplier_community_id'])) ? $data['supplier_community_id'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date']) && $data['closing_date'] != '9999-12-31') ? $data['closing_date'] : null;
        $this->contact_history = (isset($data['contact_history'])) ? json_decode($data['contact_history'], true) : null;
        $this->terms_of_sales = (isset($data['terms_of_sale'])) ? json_decode($data['terms_of_sale'], true) : null;
        $this->property_1 = (isset($data['property_1'])) ? $data['property_1'] : null;
        $this->property_2 = (isset($data['property_2'])) ? $data['property_2'] : null;
        $this->property_3 = (isset($data['property_3'])) ? $data['property_3'] : null;
        $this->property_4 = (isset($data['property_4'])) ? $data['property_4'] : null;
        $this->property_5 = (isset($data['property_5'])) ? $data['property_5'] : null;
        $this->property_6 = (isset($data['property_6'])) ? $data['property_6'] : null;
        $this->property_7 = (isset($data['property_7'])) ? $data['property_7'] : null;
        $this->property_8 = (isset($data['property_8'])) ? $data['property_8'] : null;
        $this->property_9 = (isset($data['property_9'])) ? $data['property_9'] : null;
        $this->property_10 = (isset($data['property_10'])) ? $data['property_10'] : null;
        $this->json_property_1 = (isset($data['json_property_1'])) ? json_decode($data['json_property_1'], true) : null;
        $this->json_property_2 = (isset($data['json_property_2'])) ? json_decode($data['json_property_2'], true) : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Joined properties
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->customer_name = (isset($data['customer_name'])) ? $data['customer_name'] : null;
        $this->customer_status = (isset($data['customer_status'])) ? $data['customer_status'] : null;
        $this->contact_1_id = (isset($data['contact_1_id'])) ? $data['contact_1_id'] : null;
        $this->supplier_name = (isset($data['supplier_name'])) ? $data['supplier_name'] : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->birth_date = (isset($data['birth_date'])) ? $data['birth_date'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
    }

    public function getProperties()
    {
    	$data = array();
    	 
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['type'] =  ($this->type) ? $this->type : null;
    	$data['place_id'] = (int) $this->place_id;
    	$data['identifier'] = $this->identifier;
    	$data['customer_community_id'] =  (int) $this->customer_community_id;
    	$data['customer_bill_contact_id'] =  (int) $this->customer_bill_contact_id;
    	$data['supplier_community_id'] =  (int) $this->supplier_community_id;
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : null;
    	$data['contact_history'] = $this->contact_history;
    	$data['terms_of_sales'] =  $this->terms_of_sales;
    	$data['property_1'] =  ($this->property_1) ? $this->property_1 : null;
    	$data['property_2'] =  ($this->property_2) ? $this->property_2 : null;
    	$data['property_3'] =  ($this->property_3) ? $this->property_3 : null;
    	$data['property_4'] =  ($this->property_4) ? $this->property_4 : null;
    	$data['property_5'] =  ($this->property_5) ? $this->property_5 : null;
    	$data['property_6'] =  ($this->property_6) ? $this->property_6 : null;
    	$data['property_7'] =  ($this->property_7) ? $this->property_7 : null;
    	$data['property_8'] =  ($this->property_8) ? $this->property_8 : null;
    	$data['property_9'] =  ($this->property_9) ? $this->property_9 : null;
    	$data['property_10'] =  ($this->property_10) ? $this->property_10 : null;
    	$data['json_property_1'] = $this->json_property_1;
    	$data['json_property_2'] = $this->json_property_2;
    	$data['audit'] = $this->audit;

    	// Joined properties
    	$data['place_caption'] = $this->place_caption;
    	$data['customer_name'] = $this->customer_name;
    	$data['customer_status'] = $this->customer_status;
    	$data['contact_1_id'] = $this->contact_1_id;
    	$data['supplier_name'] = $this->supplier_name;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['email'] = $this->email;
    	$data['birth_date'] = $this->birth_date;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['photo_link_id'] = $this->photo_link_id;
    	 
    	return $data;
    }
    
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['contact_history'] = json_encode($this->contact_history);
    	$data['terms_of_sales'] =  ($this->terms_of_sales) ? json_encode($this->terms_of_sales) : null;
    	$data['json_property_1'] = json_encode($this->json_property_1);
    	$data['json_property_2'] = json_encode($this->json_property_2);
    	$data['audit'] = json_encode($this->audit);

    	unset($data['place_caption']);
    	unset($data['customer_name']);
    	unset($data['customer_status']);
    	unset($data['contact_1_id']);
    	unset($data['supplier_name']);
    	unset($data['n_title']);
    	unset($data['n_first']);
    	unset($data['n_last']);
    	unset($data['n_fn']);
    	unset($data['email']);
    	unset($data['birth_date']);
    	unset($data['tel_work']);
    	unset($data['tel_cell']);
    	unset($data['photo_link_id']);

    	return $data;
    }
    
    public static function getList($type, $params, $major, $dir, $mode = 'todo')
    {
    	$context = Context::getCurrent();
    	$select = Account::getTable()->getSelect()
			->join('core_place', 'commitment_account.place_id = core_place.id', array('place_caption' => 'caption'), 'left')
			->join(array('supplier' => 'core_community'), 'commitment_account.supplier_community_id = supplier.id', array('supplier_name' => 'name'), 'left')
			->join(array('customer' => 'core_community'), 'commitment_account.customer_community_id = customer.id', array('customer_name' => 'name', 'customer_status' => 'status', 'contact_1_id'), 'left')
			->join('core_vcard', 'customer.contact_1_id = core_vcard.id', array('n_title', 'n_first', 'n_last', 'n_fn', 'email', 'birth_date', 'tel_work', 'tel_cell', 'photo_link_id'), 'left')
			->order(array($major.' '.$dir, 'supplier_name', 'customer_name'));
		$where = new Where;
		if ($type) $where->equalTo('type', $type);
		$where->notEqualTo('commitment_account.status', 'deleted');

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->equalTo('commitment_account.status', 'active');
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if ($propertyId == 'status') $where->equalTo('commitment_account.status', $params[$propertyId]);
    			elseif ($propertyId == 'customer_name') $where->like('customer.name', '%'.$params[$propertyId].'%');
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_account.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_account.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('commitment_account.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);
		$cursor = Account::getTable()->selectWith($select);
		$accounts = array();

		foreach ($cursor as $account) {
			$account->properties = $account->toArray();

			// Filter on authorized perimeter
			if (array_key_exists($type, $context->getPerimeters())) {
				$keep = true;
				foreach ($context->getPerimeters()[$type] as $key => $values) {
					$keep2 = false;
					foreach ($values as $value) {
						if ($account->properties[$key] == $value) $keep2 = true;
					}
					if (!$keep2) $keep = false;
				}
				if ($keep) $accounts[] = $account;
			}
			else $accounts[] = $account;
		}
		return $accounts;
    }

    public static function get($id, $column = 'id')
    {
    	$account = Account::getTable()->get($id, $column);

    	if (!$account) return null;
    	// Retrieve the place, the customer and the supplier
    	$account->place = Place::getTable()->get($account->place_id);
    	if ($account->place) $account->place_caption = $account->place->caption;
    	if ($account->supplier_community_id) {
    		$account->supplier_community = Community::getTable()->get($account->supplier_community_id);
	    	$account->supplier_name = $account->supplier_community->name;
    	}
    	if ($account->customer_community_id) {
    		$account->customer_community = Community::getTable()->get($account->customer_community_id);
    		$account->customer_name = $account->customer_community->name;
    		$account->customer_status = $account->customer_community->status;
    		if ($account->customer_community->contact_1_id) {
	    		$account->contact_1_id = $account->customer_community->contact_1_id;
	    		$account->contact_1_status = $account->customer_community->contact_1_status;
	    		$account->contact_1 = Vcard::get($account->customer_community->contact_1_id);
		    	
		    	$userContact = UserContact::get($account->contact_1_id, 'vcard_id');
		    	if ($userContact) {
		    		$account->userContact = $userContact;

		    		$user = User::get($userContact->user_id);
		    		$account->user = $user;
		    	}
		    	if (!$account->user) $account->user = User::instanciate();
		    	$account->username = $account->user->username;
	    	}
	    	else $account->contact_1 = Vcard::instanciate();

	    	$account->n_first = $account->contact_1->n_first;
	    	$account->n_last = $account->contact_1->n_last;
	    	$account->email = $account->contact_1->email;
	    	$account->birth_date = $account->contact_1->birth_date;
	    	$account->tel_work = $account->contact_1->tel_work;
	    	$account->tel_cell = $account->contact_1->tel_cell;
	    	$account->is_notified = $account->contact_1->is_notified;
	    	$account->locale = $account->contact_1->locale;
	    	
	    	if ($account->customer_community->contact_2_id) {
	        	$account->contact_2 = Vcard::get($account->customer_community->contact_2_id);
	    		$account->contact_2_status = $account->customer_community->contact_2_status;
	        }
	        if ($account->customer_community->contact_3_id) {
	        	$account->contact_3 = Vcard::get($account->customer_community->contact_3_id);
	    		$account->contact_3_status = $account->customer_community->contact_3_status;
	        }
	        if ($account->customer_community->contact_4_id) {
	        	$account->contact_4 = Vcard::get($account->customer_community->contact_4_id);
	    		$account->contact_4_status = $account->customer_community->contact_4_status;
	        }
	        if ($account->customer_community->contact_5_id) {
	        	$account->contact_5 = Vcard::get($account->customer_community->contact_5_id);
	    		$account->contact_5_status = $account->customer_community->contact_5_status;
	        }
    	}

    	return $account;
    }

    public static function getArray($id, $column = 'id')
    {
    	$account = Account::get($id, $column);
    
    	if (!$account) return null;
    	$data = $account->toarray();

    	// Retrieve the place, the customer and the supplier
    	$data['place'] = $account->place;
    	$data['place_caption'] = $account->place_caption;
    	if ($account->supplier_community_id) {
    		$data['supplier_community'] = $account->supplier_community;
    		$data['supplier_name'] = $account->supplier_name;
    	}
    	if ($account->customer_community_id) {
    		$data['customer_community'] = $account->customer_community;
    		$data['customer_name'] = $account->customer_name;
    		$data['customer_status'] = $account->customer_status;
    		if ($account->customer_community->contact_1_id) {
    			$data['contact_1_id'] = $account->contact_1_id;
    			$data['constact_1_status'] = $account->contact_1_status;
    			$data['contact_1'] = $account->contact_1->toArray();
    		  
    			if ($account->userContact) {
    				$data['userContact'] = $account->userContact->toArray();
    				$data['user'] = $account->user;
    			}
    			$data['username'] = $account->username;
    		}
    		if ($account->customer_community->contact_2_id) {
    			$data['contact_2'] = $account->contact_2->toArray();
    			$data['contact_2_status'] = $account->contact_2_status;
    		}
    	    if ($account->customer_community->contact_3_id) {
    			$data['contact_3'] = $account->contact_3->toArray();
    			$data['contact_3_status'] = $account->contact_3_status;
    		}
    	    if ($account->customer_community->contact_4_id) {
    			$data['contact_4'] = $account->contact_4->toArray();
    			$data['contact_4_status'] = $account->contact_4_status;
    		}
    	    if ($account->customer_community->contact_5_id) {
    			$data['contact_5'] = $account->contact_5->toArray();
    			$data['contact_5_status'] = $account->contact_5_status;
    		}
    	}
    	return $data;
    }
    
    public static function instanciate($type = null)
    {
		$account = new Account;
		$account->status = 'new';
		$account->type = $type;
		$account->contact_history = array();
		$account->audit = array();
		$account->customer_community = Community::instanciate();
		$account->contact_1 = Vcard::instanciate();
		$account->json_property_1 = array();
		$account->json_property_2 = array();
		$account->is_notified = 1;
		$account->locale = 'fr_FR';
		return $account;
    }

    public function loadData($data, $files = array()) {
    
    	$context = Context::getCurrent();

        	if (array_key_exists('status', $data)) {
		    	$this->status = trim(strip_tags($data['status']));
		    	if (strlen($this->status) > 255) return 'Integrity';
			}
    		if (array_key_exists('type', $data)) {
		    	$this->type = trim(strip_tags($data['type']));
		    	if (strlen($this->type) > 255) return 'Integrity';
			}
    		if (array_key_exists('place_id', $data)) $this->place_id = (int) $data['place_id'];
        	if (array_key_exists('identifier', $data)) {
		    	$this->identifier = trim(strip_tags($data['identifier']));
		    	if (strlen($this->identifier) > 255) return 'Integrity';
			}
    		if (array_key_exists('customer_name', $data)) {
		    	$this->customer_name = trim(strip_tags($data['customer_name']));
		    	if (!$this->customer_name || strlen($this->customer_name) > 255) return 'Integrity';
			}
    		if (array_key_exists('customer_community_id', $data)) $this->customer_community_id = (int) $data['customer_community_id'];
			if (array_key_exists('n_first', $data)) {
		    	$this->n_first = trim(strip_tags($data['n_first']));
		    	if (strlen($this->n_first) > 255) return 'Integrity';
			}
			if (array_key_exists('n_last', $data)) {
				$this->n_last = trim(strip_tags($data['n_last']));
				if (!$this->n_last || strlen($this->n_last) > 255) return 'Integrity';
			}
			if (array_key_exists('email', $data)) {
				$this->email = trim(strip_tags($data['email']));
				if (strlen($this->email) > 255) return 'Integrity';
			}
    		if (array_key_exists('birth_date', $data)) {
				$this->birth_date = trim(strip_tags($data['birth_date']));
		    	if ($this->birth_date && !checkdate(substr($this->birth_date, 5, 2), substr($this->birth_date, 8, 2), substr($this->birth_date, 0, 4))) return 'Integrity';
    		}
			if (array_key_exists('tel_work', $data)) {
		    	$this->tel_work = trim(strip_tags($data['tel_work']));
		    	if (strlen($this->tel_work) > 255) return 'Integrity';
			}
			if (array_key_exists('tel_cell', $data)) {
		    	$this->tel_cell = trim(strip_tags($data['tel_cell']));
		    	if (strlen($this->tel_cell) > 255) return 'Integrity';
			}
			if (array_key_exists('customer_bill_contact_id', $data)) $this->customer_bill_contact_id = (int) $data['customer_bill_contact_id'];
			if (array_key_exists('opening_date', $data)) {
		    	$this->opening_date = trim(strip_tags($data['opening_date']));
		    	if (!$this->opening_date || !checkdate(substr($this->opening_date, 5, 2), substr($this->opening_date, 8, 2), substr($this->opening_date, 0, 4))) return 'Integrity';
			}
			if (array_key_exists('closing_date', $data)) {
		    	$this->closing_date = trim(strip_tags($data['closing_date']));
		    	if ($this->closing_date && !checkdate(substr($this->closing_date, 5, 2), substr($this->closing_date, 8, 2), substr($this->closing_date, 0, 4))) return 'Integrity';
			}
			if (array_key_exists('contact_history', $data) && $data['contact_history']) {
				$this->contact_history[] = array(
						'time' => Date('Y-m-d G:i:s'),
						'n_fn' => $context->getFormatedName(),
						'comment' => $data['contact_history'],
				);
			}
			if (array_key_exists('property_1', $data)) {
				$this->property_1 = trim(strip_tags($data['property_1']));
				if (strlen($this->property_1) > 255) return 'Integrity';
			}
			if (array_key_exists('property_2', $data)) {
				$this->property_2 = trim(strip_tags($data['property_2']));
				if (strlen($this->property_2) > 255) return 'Integrity';
			}
			if (array_key_exists('property_3', $data)) {
				$this->property_3 = trim(strip_tags($data['property_3']));
				if (strlen($this->property_3) > 255) return 'Integrity';
			}
			if (array_key_exists('property_4', $data)) {
				$this->property_4 = trim(strip_tags($data['property_4']));
				if (strlen($this->property_4) > 255) return 'Integrity';
			}
			if (array_key_exists('property_5', $data)) {
				$this->property_5 = trim(strip_tags($data['property_5']));
				if (strlen($this->property_5) > 255) return 'Integrity';
			}
			if (array_key_exists('property_6', $data)) {
				$this->property_6 = trim(strip_tags($data['property_6']));
				if (strlen($this->property_6) > 255) return 'Integrity';
			}
			if (array_key_exists('property_7', $data)) {
				$this->property_7 = trim(strip_tags($data['property_7']));
				if (strlen($this->property_7) > 255) return 'Integrity';
			}
			if (array_key_exists('property_8', $data)) {
				$this->property_8 = trim(strip_tags($data['property_8']));
				if (strlen($this->property_8) > 255) return 'Integrity';
			}
			if (array_key_exists('property_9', $data)) {
				$this->property_9 = trim(strip_tags($data['property_9']));
				if (strlen($this->property_9) > 255) return 'Integrity';
			}
			if (array_key_exists('property_10', $data)) {
				$this->property_10 = trim(strip_tags($data['property_10']));
				if (strlen($this->property_10) > 255) return 'Integrity';
			}
    		if (array_key_exists('json_property_1', $data)) {
				$this->json_property_1 = $data['json_property_1'];
			}
        	if (array_key_exists('json_property_2', $data)) {
				$this->json_property_2 = $data['json_property_2'];
			}
			if (array_key_exists('is_notified', $data)) {
				$this->is_notified = $data['is_notified'];
			}
            if (array_key_exists('locale', $data)) {
				$this->locale = $data['locale'];
			}
			if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
				
			$this->customer_community->name = ($this->customer_name) ? $this->customer_name : $this->n_last.', '.$this->n_first;
			$this->contact_1->n_first = $this->n_first;
			$this->contact_1->n_last = $this->n_last;
			$this->contact_1->email = $this->email;
			$this->contact_1->birth_date = $this->birth_date;
			$this->contact_1->tel_work = $this->tel_work;
			$this->contact_1->tel_cell = $this->tel_cell;
			$this->contact_1->n_fn = $this->n_last.', '.$this->n_first;
    		$this->properties = $this->toArray(); // Deprecated
    		$this->files = $files;

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
    	Account::getTable()->save($this);    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$account = Account::get($this->id);

    	// Isolation check
    	if ($account->update_time > $update_time) return 'Isolation';
		$this->customer_community->update($this->customer_community->update_time);
    	$this->contact_1->update($this->contact_1->update_time);
    	Account::getTable()->save($this);
    	return 'OK';
    }

    public function isUsed($object)
    {
    	// Allow or not deleting a place
    	if (get_class($object) == 'PpitCore\Model\PLace') {
    		if (Generic::getTable()->cardinality('commitment_account', array('place_id' => $object->id)) > 0) return true;
    	}
    	return false;
    }
    
    public function isDeletable()
    {
    	$context = Context::getCurrent();
    
    	// Check dependencies
    	$config = $context->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}

    	if (Generic::getTable()->cardinality('commitment', array('account_id' => $this->id)) > 0) return false;

    	return true;
    }
    
    public function delete($update_time)
    {
		$context = Context::getCurrent();
    	$account = Account::get($this->id);
    
    	// Isolation check
    	if ($account->update_time > $update_time) return 'Isolation';
    	$user = User::get($this->contact_1->id, 'vcard_id');
    	if ($user) $user->delete($user->update_time);

    	$this->contact_1->delete($this->contact_1->update_time);
    	if ($this->customer_community->isDeletable()) $this->customer_community->delete($this->customer_community->update_time);
    	 
    	$this->status = 'deleted';
    	Account::getTable()->save($this);
    	 
    	return 'OK';
    }

    // Add content to this method:
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
    	if (!Account::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Account::$table = $sm->get('PpitCommitment\Model\AccountTable');
    	}
    	return Account::$table;
    }
}