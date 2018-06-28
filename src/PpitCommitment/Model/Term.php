<?php
namespace PpitCommitment\Model;

use PpitCommitment\Model\Commitment;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Encryption;
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
    public $transfer_order_id;
    public $transfer_order_date;
    public $bank_identifier;
    
    public $commitment_property_1;
    public $commitment_property_2;
    public $commitment_property_3;
    public $commitment_property_4;
    public $commitment_property_5;
    public $commitment_property_6;
    public $commitment_property_7;
    public $commitment_property_8;
    public $commitment_property_9;
    public $commitment_property_10;
    public $commitment_property_11;
    public $commitment_property_12;
    public $commitment_property_13;
    public $commitment_property_14;
    public $commitment_property_15;
    public $commitment_property_16;
    public $commitment_property_17;
    public $commitment_property_18;
    public $commitment_property_19;
    public $commitment_property_20;
    public $commitment_property_21;
    public $commitment_property_22;
    public $commitment_property_23;
    public $commitment_property_24;
    public $commitment_property_25;
    public $commitment_property_26;
    public $commitment_property_27;
    public $commitment_property_28;
    public $commitment_property_29;
    public $commitment_property_30;
    
    public $account_property_1;
    public $account_property_2;
    public $account_property_3;
    public $account_property_4;
    public $account_property_5;
    public $account_property_6;
    public $account_property_7;
    public $account_property_8;
    public $account_property_9;
    public $account_property_10;
    public $account_property_11;
    public $account_property_12;
    public $account_property_13;
    public $account_property_14;
    public $account_property_15;
    public $account_property_16;
    
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
        $this->transfer_order_id = (isset($data['transfer_order_id'])) ? $data['transfer_order_id'] : null;
        $this->transfer_order_date = (isset($data['transfer_order_date'])) ? $data['transfer_order_date'] : null;
        $this->bank_identifier = (isset($data['bank_identifier'])) ? $data['bank_identifier'] : null;
        
        $this->commitment_property_1 = (isset($data['commitment_property_1'])) ? $data['commitment_property_1'] : null;
        $this->commitment_property_2 = (isset($data['commitment_property_2'])) ? $data['commitment_property_2'] : null;
        $this->commitment_property_3 = (isset($data['commitment_property_3'])) ? $data['commitment_property_3'] : null;
        $this->commitment_property_4 = (isset($data['commitment_property_4'])) ? $data['commitment_property_4'] : null;
        $this->commitment_property_5 = (isset($data['commitment_property_5'])) ? $data['commitment_property_5'] : null;
        $this->commitment_property_6 = (isset($data['commitment_property_6'])) ? $data['commitment_property_6'] : null;
        $this->commitment_property_7 = (isset($data['commitment_property_7'])) ? $data['commitment_property_7'] : null;
        $this->commitment_property_8 = (isset($data['commitment_property_8'])) ? $data['commitment_property_8'] : null;
        $this->commitment_property_9 = (isset($data['commitment_property_9'])) ? $data['commitment_property_9'] : null;
        $this->commitment_property_10 = (isset($data['commitment_property_10'])) ? $data['commitment_property_10'] : null;
        $this->commitment_property_11 = (isset($data['commitment_property_11'])) ? $data['commitment_property_11'] : null;
        $this->commitment_property_12 = (isset($data['commitment_property_12'])) ? $data['commitment_property_12'] : null;
        $this->commitment_property_13 = (isset($data['commitment_property_13'])) ? $data['commitment_property_13'] : null;
        $this->commitment_property_14 = (isset($data['commitment_property_14'])) ? $data['commitment_property_14'] : null;
        $this->commitment_property_15 = (isset($data['commitment_property_15'])) ? $data['commitment_property_15'] : null;
        $this->commitment_property_16 = (isset($data['commitment_property_16'])) ? $data['commitment_property_16'] : null;
        $this->commitment_property_17 = (isset($data['commitment_property_17'])) ? $data['commitment_property_17'] : null;
        $this->commitment_property_18 = (isset($data['commitment_property_18'])) ? $data['commitment_property_18'] : null;
        $this->commitment_property_19 = (isset($data['commitment_property_19'])) ? $data['commitment_property_19'] : null;
        $this->commitment_property_20 = (isset($data['commitment_property_20'])) ? $data['commitment_property_20'] : null;
        $this->commitment_property_21 = (isset($data['commitment_property_21'])) ? $data['commitment_property_21'] : null;
        $this->commitment_property_22 = (isset($data['commitment_property_22'])) ? $data['commitment_property_22'] : null;
        $this->commitment_property_23 = (isset($data['commitment_property_23'])) ? $data['commitment_property_23'] : null;
        $this->commitment_property_24 = (isset($data['commitment_property_24'])) ? $data['commitment_property_24'] : null;
        $this->commitment_property_25 = (isset($data['commitment_property_25'])) ? $data['commitment_property_25'] : null;
        $this->commitment_property_26 = (isset($data['commitment_property_26'])) ? $data['commitment_property_26'] : null;
        $this->commitment_property_27 = (isset($data['commitment_property_27'])) ? $data['commitment_property_27'] : null;
        $this->commitment_property_28 = (isset($data['commitment_property_28'])) ? $data['commitment_property_28'] : null;
        $this->commitment_property_29 = (isset($data['commitment_property_29'])) ? $data['commitment_property_29'] : null;
        $this->commitment_property_30 = (isset($data['commitment_property_30'])) ? $data['commitment_property_30'] : null;
        
        $this->account_property_1 = (isset($data['account_property_1'])) ? $data['account_property_1'] : null;
        $this->account_property_2 = (isset($data['account_property_2'])) ? $data['account_property_2'] : null;
        $this->account_property_3 = (isset($data['account_property_3'])) ? $data['account_property_3'] : null;
        $this->account_property_4 = (isset($data['account_property_4'])) ? $data['account_property_4'] : null;
        $this->account_property_5 = (isset($data['account_property_5'])) ? $data['account_property_5'] : null;
        $this->account_property_6 = (isset($data['account_property_6'])) ? $data['account_property_6'] : null;
        $this->account_property_7 = (isset($data['account_property_7'])) ? $data['account_property_7'] : null;
        $this->account_property_8 = (isset($data['account_property_8'])) ? $data['account_property_8'] : null;
        $this->account_property_9 = (isset($data['account_property_9'])) ? $data['account_property_9'] : null;
        $this->account_property_10 = (isset($data['account_property_10'])) ? $data['account_property_10'] : null;
        $this->account_property_11 = (isset($data['account_property_11'])) ? $data['account_property_11'] : null;
        $this->account_property_12 = (isset($data['account_property_12'])) ? $data['account_property_12'] : null;
        $this->account_property_13 = (isset($data['account_property_13'])) ? $data['account_property_13'] : null;
        $this->account_property_14 = (isset($data['account_property_14'])) ? $data['account_property_14'] : null;
        $this->account_property_15 = (isset($data['account_property_15'])) ? $data['account_property_15'] : null;
        $this->account_property_16 = (isset($data['account_property_16'])) ? $data['account_property_16'] : null;
    }
    
    public function getProperties($passphrase = null)
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
    	$data['transfer_order_id'] = $this->transfer_order_id;
    	$data['transfer_order_date'] = $this->transfer_order_date;
    	$data['transfer_order_id'] = $this->transfer_order_id;
    	if ($passphrase) {
    		$value = Encryption::decrypt($this->bank_identifier, $passphrase);
    		if ($value) $data['bank_identifier'] = $value;
    	}

    	$data['commitment_property_1'] = $this->commitment_property_1;
    	$data['commitment_property_2'] = $this->commitment_property_2;
    	$data['commitment_property_3'] = $this->commitment_property_3;
    	$data['commitment_property_4'] = $this->commitment_property_4;
    	$data['commitment_property_5'] = $this->commitment_property_5;
    	$data['commitment_property_6'] = $this->commitment_property_6;
    	$data['commitment_property_7'] = $this->commitment_property_7;
    	$data['commitment_property_8'] = $this->commitment_property_8;
    	$data['commitment_property_9'] = $this->commitment_property_9;
    	$data['commitment_property_10'] = $this->commitment_property_10;
    	$data['commitment_property_11'] = $this->commitment_property_11;
    	$data['commitment_property_12'] = $this->commitment_property_12;
    	$data['commitment_property_13'] = $this->commitment_property_13;
    	$data['commitment_property_14'] = $this->commitment_property_14;
    	$data['commitment_property_15'] = $this->commitment_property_15;
    	$data['commitment_property_16'] = $this->commitment_property_16;
    	$data['commitment_property_17'] = $this->commitment_property_17;
    	$data['commitment_property_18'] = $this->commitment_property_18;
    	$data['commitment_property_19'] = $this->commitment_property_19;
    	$data['commitment_property_20'] = $this->commitment_property_20;
    	$data['commitment_property_21'] = $this->commitment_property_21;
    	$data['commitment_property_22'] = $this->commitment_property_22;
    	$data['commitment_property_23'] = $this->commitment_property_23;
    	$data['commitment_property_24'] = $this->commitment_property_24;
    	$data['commitment_property_25'] = $this->commitment_property_25;
    	$data['commitment_property_26'] = $this->commitment_property_26;
    	$data['commitment_property_27'] = $this->commitment_property_27;
    	$data['commitment_property_28'] = $this->commitment_property_28;
    	$data['commitment_property_29'] = $this->commitment_property_29;
    	$data['commitment_property_30'] = $this->commitment_property_30;
    	 
    	$data['account_property_1'] = $this->account_property_1;
    	$data['account_property_2'] = $this->account_property_2;
    	$data['account_property_3'] = $this->account_property_3;
    	$data['account_property_4'] = $this->account_property_4;
    	$data['account_property_5'] = $this->account_property_5;
    	$data['account_property_6'] = $this->account_property_6;
    	$data['account_property_7'] = $this->account_property_7;
    	$data['account_property_8'] = $this->account_property_8;
    	$data['account_property_9'] = $this->account_property_9;
    	$data['account_property_10'] = $this->account_property_10;
    	$data['account_property_11'] = $this->account_property_11;
    	$data['account_property_12'] = $this->account_property_12;
    	$data['account_property_13'] = $this->account_property_13;
    	$data['account_property_14'] = $this->account_property_14;
    	$data['account_property_15'] = $this->account_property_15;
    	$data['account_property_16'] = $this->account_property_16;
    	 
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
    	unset($data['transfer_order_id']);
    	unset($data['transfer_order_date']);
    	unset($data['bank_identifier']);
    	 
    	unset($data['commitment_property_1']);
    	unset($data['commitment_property_2']);
    	unset($data['commitment_property_3']);
    	unset($data['commitment_property_4']);
    	unset($data['commitment_property_5']);
    	unset($data['commitment_property_6']);
    	unset($data['commitment_property_7']);
    	unset($data['commitment_property_8']);
    	unset($data['commitment_property_9']);
    	unset($data['commitment_property_10']);
    	unset($data['commitment_property_11']);
    	unset($data['commitment_property_12']);
    	unset($data['commitment_property_13']);
    	unset($data['commitment_property_14']);
    	unset($data['commitment_property_15']);
    	unset($data['commitment_property_16']);
    	unset($data['commitment_property_17']);
    	unset($data['commitment_property_18']);
    	unset($data['commitment_property_19']);
    	unset($data['commitment_property_20']);
    	unset($data['commitment_property_21']);
    	unset($data['commitment_property_22']);
    	unset($data['commitment_property_23']);
    	unset($data['commitment_property_24']);
    	unset($data['commitment_property_25']);
    	unset($data['commitment_property_26']);
    	unset($data['commitment_property_27']);
    	unset($data['commitment_property_28']);
    	unset($data['commitment_property_29']);
    	unset($data['commitment_property_30']);
    	 
    	unset($data['account_property_1']);
    	unset($data['account_property_2']);
    	unset($data['account_property_3']);
    	unset($data['account_property_4']);
    	unset($data['account_property_5']);
    	unset($data['account_property_6']);
    	unset($data['account_property_7']);
    	unset($data['account_property_8']);
    	unset($data['account_property_9']);
    	unset($data['account_property_10']);
    	unset($data['account_property_11']);
    	unset($data['account_property_12']);
    	unset($data['account_property_13']);
    	unset($data['account_property_14']);
    	unset($data['account_property_15']);
    	unset($data['account_property_16']);
    	return $data;
    }
    
    public static function getList($params, $major = 'due_date', $dir = 'DESC', $mode = 'search')
    {
    	$context = Context::getCurrent();

    	$select = Term::getTable()->getSelect()
    		->join('commitment', 'commitment.id = commitment_term.commitment_id', array('commitment_caption' => 'caption', 'commitment_property_1' => 'property_1', 'commitment_property_2' => 'property_2', 'commitment_property_3' => 'property_3', 'commitment_property_4' => 'property_4', 'commitment_property_5' => 'property_5', 'commitment_property_6' => 'property_6', 'commitment_property_7' => 'property_7', 'commitment_property_8' => 'property_8', 'commitment_property_9' => 'property_9', 'commitment_property_10' => 'property_10', 'commitment_property_11' => 'property_11', 'commitment_property_12' => 'property_12', 'commitment_property_13' => 'property_13', 'commitment_property_14' => 'property_14', 'commitment_property_15' => 'property_15', 'commitment_property_16' => 'property_16', 'commitment_property_17' => 'property_17', 'commitment_property_18' => 'property_18', 'commitment_property_19' => 'property_19', 'commitment_property_20' => 'property_20', 'commitment_property_21' => 'property_21', 'commitment_property_22' => 'property_22', 'commitment_property_23' => 'property_23', 'commitment_property_24' => 'property_24', 'commitment_property_25' => 'property_25', 'commitment_property_26' => 'property_26', 'commitment_property_27' => 'property_27', 'commitment_property_28' => 'property_28', 'commitment_property_29' => 'property_29', 'commitment_property_30' => 'property_30'), 'left')
    		->join('core_account', 'core_account.id = commitment.account_id', array('place_id', 'name', 'transfer_order_id', 'transfer_order_date', 'bank_identifier', 'account_property_1' => 'property_1', 'account_property_2' => 'property_2', 'account_property_3' => 'property_3', 'account_property_4' => 'property_4', 'account_property_5' => 'property_5', 'account_property_6' => 'property_6', 'account_property_7' => 'property_7', 'account_property_8' => 'property_8', 'account_property_9' => 'property_9', 'account_property_10' => 'property_10', 'account_property_11' => 'property_11', 'account_property_12' => 'property_12', 'account_property_13' => 'property_13', 'account_property_14' => 'property_14', 'account_property_15' => 'property_15', 'account_property_16' => 'property_16'), 'left')
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
    		foreach ($params as $propertyId => $value) {
    			if ($propertyId == 'place_id') {
					if (strpos($value, ',')) $where->in('core_account.'.$propertyId, array_map('trim', explode(',', $value)));
    				$where->equalTo('core_account.place_id', $params['place_id']);
    			}
				elseif ($propertyId == 'name') $where->like('core_account.name', '%'.$params[$propertyId].'%');
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_term.'.substr($propertyId, 4), $params[$propertyId]);
				elseif (substr($propertyId, 0, 8) == 'account_') {
					if (strpos($value, ',')) $where->in('core_account.'.substr($propertyId, 8), array_map('trim', explode(',', $value)));
					else $where->like('core_account.'.substr($propertyId, 8), '%'.$params[$propertyId].'%');
				}
				elseif (substr($propertyId, 0, 11) == 'commitment_') {
					if (strpos($value, ',')) $where->in('commitment.'.substr($propertyId, 11), array_map('trim', explode(',', $value)));
					else $where->like('commitment.'.substr($propertyId, 11), '%'.$params[$propertyId].'%');
				}
				else {
					if (strpos($value, ',')) $where->in('commitment_term.'.$propertyId, array_map('trim', explode(',', $value)));
					else $where->like('commitment_term.'.$propertyId, '%'.$params[$propertyId].'%');
				}
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

    public static function get($id, $column = 'id', $passphrase = null)
    {
    	$term = Term::getTable()->get($id, $column);
    	if (!$term) return null;
    	$commitment = Commitment::get($term->commitment_id);
    	if ($commitment) {
    		$term->commitment_caption = $commitment->caption;
			$account = Account::get($commitment->account_id);
			if ($account) {
				$term->name = $account->name;
				
				$term->commitment_property_1 = $commitment->property_1;
				$term->commitment_property_2 = $commitment->property_2;
				$term->commitment_property_3 = $commitment->property_3;
				$term->commitment_property_4 = $commitment->property_4;
				$term->commitment_property_5 = $commitment->property_5;
				$term->commitment_property_6 = $commitment->property_6;
				$term->commitment_property_7 = $commitment->property_7;
				$term->commitment_property_8 = $commitment->property_8;
				$term->commitment_property_9 = $commitment->property_9;
				$term->commitment_property_10 = $commitment->property_10;
				$term->commitment_property_11 = $commitment->property_11;
				$term->commitment_property_12 = $commitment->property_12;
				$term->commitment_property_13 = $commitment->property_13;
				$term->commitment_property_14 = $commitment->property_14;
				$term->commitment_property_15 = $commitment->property_15;
				$term->commitment_property_16 = $commitment->property_16;
				$term->commitment_property_17 = $commitment->property_17;
				$term->commitment_property_18 = $commitment->property_18;
				$term->commitment_property_19 = $commitment->property_19;
				$term->commitment_property_20 = $commitment->property_20;
				$term->commitment_property_21 = $commitment->property_21;
				$term->commitment_property_22 = $commitment->property_22;
				$term->commitment_property_23 = $commitment->property_23;
				$term->commitment_property_24 = $commitment->property_24;
				$term->commitment_property_25 = $commitment->property_25;
				$term->commitment_property_26 = $commitment->property_26;
				$term->commitment_property_27 = $commitment->property_27;
				$term->commitment_property_28 = $commitment->property_28;
				$term->commitment_property_29 = $commitment->property_29;
				$term->commitment_property_30 = $commitment->property_30;
				
				$term->transfer_order_id = $account->transfer_order_id;
				$term->transfer_order_date = $account->transfer_order_date;
				$term->bank_identifier = $account->bank_identifier;
				$term->account_property_1 = $account->property_1;
		    	$term->account_property_2 = $account->property_2;
		    	$term->account_property_3 = $account->property_3;
		    	$term->account_property_4 = $account->property_4;
		    	$term->account_property_5 = $account->property_5;
		    	$term->account_property_6 = $account->property_6;
		    	$term->account_property_7 = $account->property_7;
		    	$term->account_property_8 = $account->property_8;
		    	$term->account_property_9 = $account->property_9;
		    	$term->account_property_10 = $account->property_10;
		    	$term->account_property_11 = $account->property_11;
		    	$term->account_property_12 = $account->property_12;
		    	$term->account_property_13 = $account->property_13;
		    	$term->account_property_14 = $account->property_14;
		    	$term->account_property_15 = $account->property_15;
		    	$term->account_property_16 = $account->property_16;
			}
    	}
    	$term->properties = $term->getProperties($passphrase);
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