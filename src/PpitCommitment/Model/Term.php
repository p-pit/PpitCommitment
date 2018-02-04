<?php
namespace PpitCommitment\Model;

use PpitCommitment\Model\Commitment;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
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
    public $settlement_date;
    public $collection_date;
    public $amount;
	public $means_of_payment;
	public $reference;
	public $comment;
	public $document;
    public $invoice_id;
    public $audit;
    public $update_time;

    // Joined properties
    public $name;
    public $commitment_caption;
    public $place_id;
    public $place_caption;
    public $place_identifier;
    
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
        $this->settlement_date = (isset($data['settlement_date'])) ? (($data['settlement_date'] == '9999-12-31') ? null : $data['settlement_date']) : null;
        $this->collection_date = (isset($data['collection_date'])) ? (($data['collection_date'] == '9999-12-31') ? null : $data['collection_date']) : null;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : null;
        $this->means_of_payment = (isset($data['means_of_payment'])) ? $data['means_of_payment'] : null;
        $this->reference = (isset($data['reference'])) ? $data['reference'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
        $this->document = (isset($data['document'])) ? $data['document'] : null;
        $this->invoice_id = (isset($data['invoice_id'])) ? $data['invoice_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Joined properties
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->commitment_caption = (isset($data['commitment_caption'])) ? $data['commitment_caption'] : null;
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
    }
    
    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['commitment_id'] = (int) $this->commitment_id;
    	$data['subscription_id'] = (int) $this->subscription_id;
    	$data['caption'] = $this->caption;
    	$data['due_date'] =  ($this->due_date) ? $this->due_date : null;
    	$data['settlement_date'] = ($this->settlement_date) ? $this->settlement_date : null;
    	$data['collection_date'] = ($this->collection_date) ? $this->collection_date : null;
    	$data['amount'] = $this->amount;
    	$data['means_of_payment'] = $this->means_of_payment;
    	$data['reference'] = $this->reference;
    	$data['comment'] = $this->comment;
    	$data['document'] = $this->document;
    	$data['invoice_id'] = (int) $this->invoice_id;
    	$data['audit'] = $this->audit;

    	$data['name'] = $this->name;
    	$data['commitment_caption'] = $this->commitment_caption;
    	$data['place_caption'] = $this->place_caption;
    	$data['place_identifier'] = $this->place_identifier;
    	$data['place_id'] = $this->place_id;
    	
    	return $data;
    }

    public function toArray()
    {
    	$data = $this->getProperties();
    	if (!$data['settlement_date']) $data['settlement_date'] = '9999-12-31';
    	if (!$data['collection_date']) $data['collection_date'] = '9999-12-31';
    	$data['audit'] = json_encode($data['audit']);
    	unset($data['name']);
    	unset($data['commitment_caption']);
    	unset($data['place_caption']);
    	unset($data['place_identifier']);
    	unset($data['place_id']);
    	return $data;
    }
    
    public static function getList($params, $major = 'due_date', $dir = 'DESC', $mode = 'search')
    {
    	$context = Context::getCurrent();

    	$select = Term::getTable()->getSelect()
    		->join('commitment', 'commitment.id = commitment_term.commitment_id', array('commitment_caption' => 'caption'), 'left')
    		->join('core_account', 'core_account.id = commitment.account_id', array('place_id', 'name'), 'left')
			->join('core_place', 'core_account.place_id = core_place.id', array('place_caption' => 'caption', 'place_identifier' => 'identifier'), 'left')
    		->order(array($major.' '.$dir, 'due_date', 'amount DESC'));
		$where = new Where;
		$where->notEqualTo('commitment_term.status', 'deleted');

		// Filter on place
		$keep = true;
		if (array_key_exists('p-pit-admin', $context->getPerimeters()) && array_key_exists('place_id', $context->getPerimeters()['p-pit-admin'])) {
			$where->in('core_account.place_id', $context->getPerimeters()['p-pit-admin']['place_id']);
		}
		
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->notEqualTo('commitment_term.status', 'collected');
    		$where->lessThanOrEqualTo('collection_date', date('Y-m-d'));
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if ($propertyId == 'place_id') $where->equalTo('core_account.place_id', $params['place_id']);
				elseif ($propertyId == 'name') $where->like('core_account.name', '%'.$params[$propertyId].'%');
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('commitment_term.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);
		$cursor = Term::getTable()->selectWith($select);
		$terms = array();

		foreach ($cursor as $term) {
			$term->properties = $term->getProperties();
			$terms[] = $term;
		}
		return $terms;
    }

    public static function get($id, $column = 'id')
    {
    	$term = Term::getTable()->get($id, $column);
    	if (!$term) return null;
    	$commitment = Commitment::get($term->commitment_id);
    	if ($commitment) {
    		$term->commitment_caption = $commitment->caption;
			$account = Account::get($commitment->account_id);
			if ($account) {
				$term->name = $account->name;
			}
    	}
    	$term->properties = $term->getProperties();
    	return $term;
    }

    public static function instanciate($commitment_id = null)
    {
		$term = new Term;
		$term->status = 'expected';
		$term->commitment_id = $commitment_id;
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
        	if (array_key_exists('settlement_date', $data)) {
		    	$this->settlement_date = trim(strip_tags($data['settlement_date']));
		    	if ($this->settlement_date && !checkdate(substr($this->settlement_date, 5, 2), substr($this->settlement_date, 8, 2), substr($this->settlement_date, 0, 4))) return 'Integrity';
			}
            if (array_key_exists('collection_date', $data)) {
		    	$this->collection_date = trim(strip_tags($data['collection_date']));
		    	if ($this->collection_date && !checkdate(substr($this->collection_date, 5, 2), substr($this->collection_date, 8, 2), substr($this->collection_date, 0, 4))) return 'Integrity';
            }
			if (array_key_exists('amount', $data)) {
				$this->amount = trim(strip_tags($data['amount']));
				if (strlen($this->amount) > 255) return 'Integrity';
			}
			if (array_key_exists('means_of_payment', $data)) {
				$this->means_of_payment = trim(strip_tags($data['means_of_payment']));
				if (strlen($this->means_of_payment) > 255) return 'Integrity';
			}
    		if (array_key_exists('reference', $data)) {
				$this->reference = trim(strip_tags($data['reference']));
				if (strlen($this->reference) > 255) return 'Integrity';
			}
        	if (array_key_exists('comment', $data)) {
				$this->comment = trim(strip_tags($data['comment']));
				if (strlen($this->comment) > 2047) return 'Integrity';
			}
			if (array_key_exists('document', $data)) {
    			$this->document = trim(strip_tags($data['document']));
				if (strlen($this->document) > 255) return 'Integrity';
    		}
    		if (array_key_exists('invoice_id', $data)) $this->invoice_id = (int) $data['invoice_id'];
    		if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
    		$this->properties = $this->toArray();
    		$this->files = $files;

    	// Update the audit
    	$this->audit[] = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
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
/*    	if (isset($this->files)) {
			$root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->document_id = $document->save();
    	}*/
    	Term::getTable()->save($this);
    	return 'OK';
    }

    public function isDeletable()
    {
    	// Only deleted while deleting the related commitment
    	return true;
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