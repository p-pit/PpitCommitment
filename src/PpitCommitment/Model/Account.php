<?php
namespace PpitCommitment\Model;

use PpitContact\Model\Community;
use PpitContact\Model\Vcard;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitDocument\Model\Document;
use PpitMasterData\Model\Place;
use PpitUser\Model\User;
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
    public $type;
    public $place_id;
    public $customer_community_id;
	public $customer_bill_contact_id;
    public $supplier_community_id;
    public $opening_date;
    public $closing_date;
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
    public $audit;
    public $update_time;
        
    // Joined properties
    public $place_name;
    public $customer_name;
    public $supplier_name;
    public $n_first;
    public $n_last;
    public $email;
    public $tel_work;
    public $tel_cell;
    
    // Transient properties
    public $place;
	public $customer_community;
	public $supplier_community;
	public $main_contact;
	public $properties;
	public $comment;

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
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->customer_community_id = (isset($data['customer_community_id'])) ? $data['customer_community_id'] : null;
        $this->customer_bill_contact_id = (isset($data['customer_bill_contact_id'])) ? $data['customer_bill_contact_id'] : null;
        $this->supplier_community_id = (isset($data['supplier_community_id'])) ? $data['supplier_community_id'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date']) && $data['closing_date'] != '9999-12-31') ? $data['closing_date'] : null;
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
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Joined properties
        $this->place_name = (isset($data['place_name'])) ? $data['place_name'] : null;
        $this->customer_name = (isset($data['customer_name'])) ? $data['customer_name'] : null;
        $this->supplier_name = (isset($data['supplier_name'])) ? $data['supplier_name'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['type'] =  ($this->type) ? $this->type : null;
    	$data['place_id'] = (int) $this->place_id;
    	$data['customer_community_id'] =  (int) $this->customer_community_id;
    	$data['customer_bill_contact_id'] =  (int) $this->customer_bill_contact_id;
    	$data['supplier_community_id'] =  (int) $this->supplier_community_id;
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['terms_of_sales'] =  ($this->terms_of_sales) ? json_encode($this->terms_of_sales) : null;
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
    	$data['audit'] =  ($this->audit) ? json_encode($this->audit) : null;
    	return $data;
    }
    
    public static function getList($type, $params, $major, $dir, $mode = 'todo')
    {
    	$select = Account::getTable()->getSelect()
			->join('md_place', 'commitment_account.place_id = md_place.id', array('place_name' => 'name'), 'left')
			->join(array('supplier' => 'contact_community'), 'commitment_account.supplier_community_id = supplier.id', array('supplier_name' => 'name'), 'left')
			->join(array('customer' => 'contact_community'), 'commitment_account.customer_community_id = customer.id', array('customer_name' => 'name'), 'left')
			->order(array($major.' '.$dir, 'supplier_name', 'customer_name'));
		$where = new Where;
		if ($type) $where->equalTo('type', $type);
        
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->greaterThanOrEqualTo('commitment_account.closing_date', date('Y-m-d'));
    	}
    	else {

    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if ($propertyId == 'customer_name') $where->like('customer.name', '%'.$params[$propertyId].'%');
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
			$accounts[] = $account;
		}
		return $accounts;
    }

    public static function get($id, $column = 'id')
    {
    	$account = Account::getTable()->get($id, $column);
    	if (!$account) return null;
    	
    	// Retrieve the place, the customer and the supplier
    	$account->place = Place::getTable()->get($account->place_id);
    	if ($account->place) $account->place_name = $account->place->name;
    	$account->supplier_community = Community::getTable()->get($account->supplier_community_id);
    	if ($account->supplier_community) $account->supplier_name = $account->supplier_community->name;
    	$account->customer_community = Community::getTable()->get($account->customer_community_id);
    	$account->customer_name = $account->customer_community->name;
    	$account->main_contact = Vcard::get($account->customer_community->main_contact_id);
    	$account->n_first = $account->main_contact->n_first;
    	$account->n_last = $account->main_contact->n_last;
    	$account->email = $account->main_contact->email;
    	$account->tel_work = $account->main_contact->tel_work;
    	$account->tel_cell = $account->main_contact->tel_cell;
    	$account->properties = $account->toArray();

    	return $account;
    }

    public static function instanciate()
    {
		$account = new Account;
		$account->audit = array();
		$account->customer_community = Community::instanciate();
		$account->main_contact = Vcard::instanciate();
		return $account;
    }

    public function loadData($data) {
    
    	$context = Context::getCurrent();

    		if (array_key_exists('type', $data)) {
		    	$this->type = trim(strip_tags($data['type']));
		    	if (strlen($this->type) > 255) return 'Integrity';
			}
    		if (array_key_exists('place_id', $data)) $this->place_id = (int) $data['place_id'];
    		if (array_key_exists('customer_name', $data)) {
		    	$this->customer_name = trim(strip_tags($data['customer_name']));
		    	if (!$this->customer_name || strlen($this->customer_name) > 255) return 'Integrity';
			}
			if (array_key_exists('n_first', $data)) {
		    	$this->n_first = trim(strip_tags($data['n_first']));
		    	if (!$this->n_first || strlen($this->n_first) > 255) return 'Integrity';
			}
			if (array_key_exists('n_last', $data)) {
				$this->n_last = trim(strip_tags($data['n_last']));
				if (!$this->n_last || strlen($this->n_last) > 255) return 'Integrity';
			}
			if (array_key_exists('email', $data)) {
				$this->email = trim(strip_tags($data['email']));
				if (!$this->email || strlen($this->email) > 255) return 'Integrity';
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
			if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
				
			$this->customer_community->name = ($this->customer_name) ? $this->customer_name : $this->n_last.', '.$this->n_first;
			$this->main_contact->n_first = $this->n_first;
			$this->main_contact->n_last = $this->n_last;
			$this->main_contact->email = $this->email;
			$this->main_contact->tel_work = $this->tel_work;
			$this->main_contact->tel_cell = $this->tel_cell;
			$this->main_contact->n_fn = $this->n_last.', '.$this->n_first;
    		$this->properties = $this->toArray();
    	
    	// Update the audit
    	$this->audit[] = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
    			'comment' => $this->comment,
    	);

    	return 'OK';
    }

    public function add($createUser = true)
    {
    	$context = Context::getCurrent();

    	$document = new Document;
    	$document->parent_id = 0;
    	$document->type = 'directory';
    	$document->name = 'Documents';
    	$document->acl = array('communities' => array($this->customer_community->id => 'write'), 'contacts' => array());
    	Document::getTable()->save($document);
    	 
    	$this->customer_community->root_document_id = $document->id;
    	$this->customer_community->add();
    	$this->customer_community_id = $this->customer_community->id;

    	$this->main_contact->community_id = $this->customer_community->id;
    	$this->main_contact = Vcard::optimize($this->main_contact);
    	$this->main_contact->add();
    	$this->customer_community->main_contact_id = $this->main_contact->id;
    	Community::getTable()->save($this->customer_community);

    	if ($createUser) {
    		$user = User::getNew();
    		$user->username = $this->main_contact->email;
    		$user->contact_id = $this->main_contact->id;
    		$rc = $user->add();
    		if ($rc != 'OK') return $rc;
    	}

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
    	
    	$this->main_contact->update($this->main_contact->update_time);
    	 
    	Account::getTable()->save($this);
    
    	return 'OK';
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
    	$user = User::get($this->main_contact->id, 'contact_id');
    	$user->delete($user->update_time);

    	if ($this->customer_community->isDeletable()) $this->customer_community->delete($this->user_community->update_time);
    	
    	$this->main_contact->delete($this->main_contact->update_time);
    	 
    	Account::getTable()->delete($this->id);
    
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