<?php
namespace PpitCommitment\Model;

use PpitCommitment\Model\Commitment;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Term implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $status;
    public $commitment_id;
    public $subscription_id;
	public $caption;
    public $due_date;
    public $amount;
	public $means_of_payment;
    public $document_id;
    public $invoice_id;
    public $audit;
    public $update_time;
	
    // Transient properties
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
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->commitment_id = (isset($data['commitment_id'])) ? $data['commitment_id'] : null;
        $this->subscription_id = (isset($data['subscription_id'])) ? $data['subscription_id'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->due_date = (isset($data['due_date'])) ? $data['due_date'] : null;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : null;
        $this->means_of_payment = (isset($data['means_of_payment'])) ? $data['means_of_payment'] : null;
        $this->document_id = (isset($data['document_id'])) ? $data['document_id'] : null;
        $this->invoice_id = (isset($data['invoice_id'])) ? $data['invoice_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['commitment_id'] = (int) $this->commitment_id;
    	$data['subscription_id'] = (int) $this->subscription_id;
    	$data['caption'] = $this->caption;
    	$data['due_date'] =  ($this->due_date) ? $this->due_date : null;
    	$data['amount'] = $this->amount;
    	$data['means_of_payment'] = $this->means_of_payment;
    	$data['document_id'] = (int) $this->document_id;
    	$data['invoice_id'] = (int) $this->invoice_id;
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }
    
    public static function getList($params, $major, $dir, $mode = 'todo')
    {
    	$context = Context::getCurrent();

    	$select = Account::getTable()->getSelect()
			->order(array($major.' '.$dir, 'due_date', 'amount DESC'));
		$where = new Where;
		$where->notEqualTo('commitment_term.status', 'deleted');

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->greaterThanOrEqualTo('commitment_term.due_date', date('Y-m-d'));
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
				if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('commitment_term.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);
		$cursor = Term::getTable()->selectWith($select);
		$terms = array();
		foreach ($cursor as $term) {
			$term->properties = $term->toArray();
			$terms[] = $term;
		}
		return $terms;
    }

    public static function get($id, $column = 'id')
    {
    	$term = Term::getTable()->get($id, $column);
    	if (!$term) return null;
        $term->properties = $term->toArray();
    	return $term;
    }

    public static function instanciate($type = null)
    {
		$term = new Term;
		$term->status = 'new';
		$term->audit = array();
		return $term;
    }

    public function loadData($data, $files = array()) {
    
    	$context = Context::getCurrent();

    		if (array_key_exists('status', $data)) {
		    	$this->status = trim(strip_tags($data['status']));
		    	if (!$this->status || strlen($this->status) > 255) return 'Integrity';
			}
    		if (array_key_exists('commitment_id', $data)) $this->commitment_id = (int) $data['commitment_id'];
    		if (array_key_exists('subscription_id', $data)) $this->subscription_id = (int) $data['subscription_id'];
        	if (array_key_exists('caption', $data)) {
		    	$this->caption = trim(strip_tags($data['caption']));
		    	if (!$this->caption || strlen($this->caption) > 255) return 'Integrity';
			}
    		if (array_key_exists('due_date', $data)) {
		    	$this->due_date = trim(strip_tags($data['due_date']));
		    	if ($this->due_date && !checkdate(substr($this->due_date, 5, 2), substr($this->due_date, 8, 2), substr($this->due_date, 0, 4))) return 'Integrity';
			}
			if (array_key_exists('amount', $data)) {
				$this->amount = trim(strip_tags($data['amount']));
				if (strlen($this->amount) > 255) return 'Integrity';
			}
			if (array_key_exists('menas_of_payment', $data)) {
				$this->means_of_payment = trim(strip_tags($data['means_of_payment']));
				if (strlen($this->means_of_payment) > 255) return 'Integrity';
			}
    		if (array_key_exists('document_id', $data)) $this->document_id = (int) $data['document_id'];
    		if (array_key_exists('invoice_id', $data)) $this->invoice_id = (int) $data['invoice_id'];
    		if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
    		$this->properties = $this->toArray();
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
    	Term::getTable()->save($this);    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$term = Term::get($this->id);

    	// Isolation check
    	if ($update_time && $term->update_time > $update_time) return 'Isolation';
    	if ($this->files) {
			$root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->document_id = $document->save();
    	}
    	Term::getTable()->save($this);
    	return 'OK';
    }

    public function isDeletable()
    {
    	// Only deleted while deleting the related commitment
    	return false;
    }
    
    public function delete($update_time)
    {
		$context = Context::getCurrent();
    	$term = Term::get($this->id);
    
    	// Isolation check
    	if ($update_time && $term->update_time > $update_time) return 'Isolation';    	 
    	$this->status = 'deleted';
    	Term::getTable()->save($this);
    	 
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
    	if (!Term::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Term::$table = $sm->get('PpitCommitment\Model\TermTable');
    	}
    	return Term::$table;
    }
}