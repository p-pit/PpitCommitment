<?php
namespace PpitCommitment\Model;

use PpitContact\Model\Community;
use PpitContact\Model\Vcard;
use PpitContact\Model\ContactMessage;
use PpitCore\Model\Context;
use PpitDocument\Model\Document;
use PpitEquipment\Model\Area;
use PpitMasterData\Model\Place;
use PpitMasterData\Model\OrgUnit;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\db\sql\Where;

class Commitment implements InputFilterAwareInterface
{
    public $id;
	public $type;
	public $project_id;
	public $account_id;
	public $status;
	public $caption;
	public $description;
	public $amount;
	public $renewable;
	public $identifier;
	public $quotation_identifier;
	public $invoice_identifier;
	public $commitment_date;
	public $retraction_limit;
    public $retraction_date;
	public $expected_shipment_date;
    public $shipment_date;
	public $expected_delivery_date;
	public $delivery_date;
	public $expected_commissioning_date;
	public $commissioning_date;
	public $due_date;
	public $invoice_date;
	public $expected_settlement_date;
	public $settlement_date;
	public $order_form_id;
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
	public $property_11;
	public $property_12;
	public $property_13;
	public $property_14;
	public $property_15;
	public $property_16;
	public $property_17;
	public $property_18;
	public $property_19;
	public $property_20;
	public $audit = array();
	public $tax_regime;
	public $tax_amount;
	public $tax_inclusive;
	public $commitment_message_id;
	public $confirmation_message_id;
	public $shipment_message_id;
	public $delivery_message_id;
	public $commissioning_message_id;
	public $invoice_message_id;
	public $settlement_message_id;
	public $notification_time;
	public $update_time;

	// Additional field from joined tables
	public $customer_name;
	public $properties;
    
	// Transient properties
//	public $properties;
	public $projects;
	public $breadcrumb;
    public $vat_rate;
    public $files;
    public $comment;
    
    protected $inputFilter;
    protected $validatePiInputFilter;
    protected $validateInputFilter;

    // Static fields
    private static $table;

    public static function getProjects()
    {
    	$context = Context::getCurrent();
    	return $context->getInstance()->specifications['ppitCommitment']['projects'];
    }

    public static function getProperties()
    {
    	// Retrieve the properties
    	return $context->getInstance()->specifications['ppitCommitment']['properties'];
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->project_id = (isset($data['project_id'])) ? $data['project_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : null;
        $this->renewable = (isset($data['renewable'])) ? $data['renewable'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->quotation_identifier = (isset($data['quotation_identifier'])) ? $data['quotation_identifier'] : null;
        $this->invoice_identifier = (isset($data['invoice_identifier'])) ? $data['invoice_identifier'] : null;
        $this->commitment_date = (isset($data['commitment_date'])) ? $data['commitment_date'] : null;
        $this->retraction_limit = (isset($data['retraction_limit'])) ? $data['retraction_limit'] : null;
        $this->retraction_date = (isset($data['retraction_date'])) ? $data['retraction_date'] : null;
        $this->expected_shipment_date = (isset($data['expected_shipment_date'])) ? $data['expected_shipment_date'] : null;
        $this->shipment_date = (isset($data['shipment_date'])) ? $data['shipment_date'] : null;
        $this->expected_delivery_date = (isset($data['expected_delivery_date'])) ? $data['expected_delivery_date'] : null;
        $this->delivery_date = (isset($data['delivery_date'])) ? $data['delivery_date'] : null;
        $this->expected_commissioning_date = (isset($data['expected_commissioning_date'])) ? $data['expected_commissioning_date'] : null;
        $this->commissioning_date = (isset($data['commissioning_date'])) ? $data['commissioning_date'] : null;
        $this->due_date = (isset($data['due_date'])) ? $data['due_date'] : null;
        $this->invoice_date = (isset($data['invoice_date'])) ? $data['invoice_date'] : null;
        $this->expected_settlement_date = (isset($data['expected_settlement_date'])) ? $data['expected_settlement_date'] : null;
        $this->settlement_date = (isset($data['settlement_date'])) ? $data['settlement_date'] : null;
        $this->order_form_id = (isset($data['order_form_id'])) ? $data['order_form_id'] : null;
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
        $this->property_11 = (isset($data['property_11'])) ? $data['property_11'] : null;
        $this->property_12 = (isset($data['property_12'])) ? $data['property_12'] : null;
        $this->property_13 = (isset($data['property_13'])) ? $data['property_13'] : null;
        $this->property_14 = (isset($data['property_14'])) ? $data['property_14'] : null;
        $this->property_15 = (isset($data['property_15'])) ? $data['property_15'] : null;
        $this->property_16 = (isset($data['property_16'])) ? $data['property_16'] : null;
        $this->property_17 = (isset($data['property_17'])) ? $data['property_17'] : null;
        $this->property_18 = (isset($data['property_18'])) ? $data['property_18'] : null;
        $this->property_19 = (isset($data['property_19'])) ? $data['property_19'] : null;
        $this->property_20 = (isset($data['property_20'])) ? $data['property_20'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : array();
        $this->tax_regime = (isset($data['tax_regime'])) ? $data['tax_regime'] : null;
        $this->tax_amount = (isset($data['tax_amount'])) ? $data['tax_amount'] : null;
        $this->tax_inclusive = (isset($data['tax_inclusive'])) ? $data['tax_inclusive'] : null;
        $this->commitment_message_id = (isset($data['commitment_message_id'])) ? $data['commitment_message_id'] : null;
        $this->confirmation_message_id = (isset($data['confirmation_message_id'])) ? $data['confirmation_message_id'] : null;
        $this->shipment_message_id = (isset($data['shipment_message_id'])) ? $data['shipment_message_id'] : null;
        $this->delivery_message_id = (isset($data['delivery_message_id'])) ? $data['delivery_message_id'] : null;
        $this->commissioning_message_id = (isset($data['commissioning_message_id'])) ? $data['commissioning_message_id'] : null;
        $this->invoice_message_id = (isset($data['invoice_message_id'])) ? $data['invoice_message_id'] : null;
        $this->settlement_message_id = (isset($data['settlement_message_id'])) ? $data['settlement_message_id'] : null;
        $this->notification_time = (isset($data['notification_time'])) ? $data['notification_time'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Additional properties from joined tables
        $this->customer_name = (isset($data['customer_name'])) ? $data['customer_name'] : null;

        // Denormalized properties
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->site_identifier = (isset($data['site_identifier'])) ? $data['site_identifier'] : null;
        $this->site_caption = (isset($data['site_caption'])) ? $data['site_caption'] : null;
        $this->area_caption = (isset($data['area_caption'])) ? $data['area_caption'] : null;
    }

    public function toArray() {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['type'] = $this->type;
    	$data['account_id'] = $this->account_id;
    	$data['project_id'] = $this->project_id;
    	$data['status'] = $this->status;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['amount'] = $this->amount;
    	$data['renewable'] = (int) $this->renewable;
    	$data['identifier'] = $this->identifier;
    	$data['quotation_identifier'] = $this->quotation_identifier;
    	$data['invoice_identifier'] = $this->invoice_identifier;
    	$data['commitment_date'] = ($this->commitment_date) ? $this->commitment_date : null;
    	$data['retraction_limit'] = ($this->retraction_limit) ? $this->retraction_limit : null;
    	$data['retraction_date'] = ($this->retraction_date) ? $this->retraction_date : null;
    	$data['expected_shipment_date'] = ($this->expected_shipment_date) ? $this->expected_shipment_date: null;
    	$data['shipment_date'] = ($this->shipment_date) ? $this->shipment_date: null;
    	$data['expected_delivery_date'] = ($this->expected_delivery_date) ? $this->expected_delivery_date: null;
    	$data['delivery_date'] = ($this->delivery_date) ? $this->delivery_date: null;
    	$data['expected_commissioning_date'] = ($this->expected_commissioning_date) ? $this->expected_commissioning_date: null;
    	$data['commissioning_date'] = ($this->commissioning_date) ? $this->commissioning_date: null;
    	$data['due_date'] = ($this->due_date) ? $this->due_date: null;
    	$data['invoice_date'] = ($this->invoice_date) ? $this->invoice_date: null;
    	$data['expected_settlement_date'] = ($this->expected_settlement_date) ? $this->expected_settlement_date: null;
    	$data['settlement_date'] = ($this->settlement_date) ? $this->settlement_date: null;
    	$data['order_form_id'] = $this->order_form_id;
    	$data['property_1'] = $this->property_1;
    	$data['property_2'] = $this->property_2;
    	$data['property_3'] = $this->property_3;
    	$data['property_4'] = $this->property_4;
    	$data['property_5'] = $this->property_5;
    	$data['property_6'] = $this->property_6;
    	$data['property_7'] = $this->property_7;
    	$data['property_8'] = $this->property_8;
    	$data['property_9'] = $this->property_9;
    	$data['property_10'] = $this->property_10;
    	$data['property_11'] = $this->property_11;
    	$data['property_12'] = $this->property_12;
    	$data['property_13'] = $this->property_13;
    	$data['property_14'] = $this->property_14;
    	$data['property_15'] = $this->property_15;
    	$data['property_16'] = $this->property_16;
    	$data['property_17'] = $this->property_17;
    	$data['property_18'] = $this->property_18;
    	$data['property_19'] = $this->property_19;
    	$data['property_20'] = $this->property_20;
    	$data['audit'] = json_encode($this->audit);
    	$data['tax_regime'] = $this->tax_regime;
    	$data['tax_amount'] = $this->tax_amount;
    	$data['tax_inclusive'] = $this->tax_inclusive;
    	$data['commitment_message_id'] = $this->commitment_message_id;
    	$data['confirmation_message_id'] = $this->confirmation_message_id;
    	$data['shipment_message_id'] = $this->shipment_message_id;
    	$data['delivery_message_id'] = $this->delivery_message_id;
    	$data['commissioning_message_id'] = $this->commissioning_message_id;
    	$data['invoice_message_id'] = $this->invoice_message_id;
    	$data['settlement_message_id'] = $this->settlement_message_id;
    	$data['notification_time'] = ($this->notification_time) ? $this->notification_time : null;

    	return $data;
    }

    public static function getList($type, $params, $major, $dir, $mode)
    {
    	$context = Context::getCurrent();
    	$select = Commitment::getTable()->getSelect()
    		->join('commitment_account', 'commitment.account_id = commitment_account.id', array(), 'left')
    		->join('contact_community', 'commitment_account.customer_community_id = contact_community.id', array('customer_name' => 'name'), 'left');
    	
    	$where = new Where();

    	// Filter on type
		if ($type) $where->equalTo('commitment.type', $type);

		// Todo list vs search modes
		if ($mode == 'todo') {

			$todo = $context->getInstance()->specifications['ppitCommitment']['todo'][$type];
			foreach($todo as $role => $properties) {
				if ($context->hasRole($role)) {
					foreach($properties as $property => $predicate) {
						if ($predicate['selector'] == 'equalTo') $where->equalTo($property, $predicate['value']);
						elseif ($predicate['selector'] == 'in') $where->in($property, $predicate['value']);
						elseif ($predicate['selector'] == 'deadline') $where->lessThanOrEqualTo($property, date('Y-m-d', strtotime(date('Y-m-d').'+ '.$predicate['value'].' days')));
					}
				}
			}
		}
		else {
			
			// Set the filters
			if (isset($params['account_id'])) $where->equalTo('account_id', $params['account_id']);
			if (isset($params['project_id'])) $where->equalTo('project_id', $params['project_id']);
			if (isset($params['status'])) $where->like('status', '%'.$params['status'].'%');
			if (isset($params['min_amount'])) $where->greaterThanOrEqualTo('amount', $params['min_amount']);
			if (isset($params['max_amount'])) $where->lessThanOrEqualTo('amount', $params['max_amount']);
			if (isset($params['identifier'])) $where->like('order.identifier', '%'.$params['identifier'].'%');
			if (isset($params['quotation_identifier'])) $where->like('quotation_identifier', '%'.$params['quotation_identifier'].'%');
			if (isset($params['invoice_identifier'])) $where->like('invoice_identifier', '%'.$params['invoice_identifier'].'%');
			if (isset($params['min_commitment_date'])) $where->greaterThanOrEqualTo('commitment_date', $params['min_commitment_date']);
			if (isset($params['max_commitment_date'])) $where->lessThanOrEqualTo('commitment_date', $params['max_commitment_date']);
			if (isset($params['min_retraction_limit'])) $where->greaterThanOrEqualTo('retraction_limit', $params['min_retraction_limit']);
			if (isset($params['max_retraction_limit'])) $where->lessThanOrEqualTo('retraction_limit', $params['max_retraction_limit']);
			if (isset($params['min_retraction_date'])) $where->greaterThanOrEqualTo('retraction_date', $params['min_retraction_date']);
			if (isset($params['max_retraction_date'])) $where->lessThanOrEqualTo('retraction_date', $params['max_retraction_date']);
			if (isset($params['min_expected_shipment_date'])) $where->greaterThanOrEqualTo('expected_shipment_date', $params['min_expected_shipment_date']);
			if (isset($params['max_expected_shipment_date'])) $where->lessThanOrEqualTo('expected_shipment_date', $params['max_expected_shipment_date']);
			if (isset($params['min_shipment_date'])) $where->greaterThanOrEqualTo('shipment_date', $params['min_shipment_date']);
			if (isset($params['max_shipment_date'])) $where->lessThanOrEqualTo('shipment_date', $params['max_shipment_date']);
			if (isset($params['min_expected_delivery_date'])) $where->greaterThanOrEqualTo('expected_delivery_date', $params['min_expected_delivery_date']);
			if (isset($params['max_expected_delivery_date'])) $where->lessThanOrEqualTo('expected_delivery_date', $params['max_expected_delivery_date']);
			if (isset($params['min_delivery_date'])) $where->greaterThanOrEqualTo('delivery_date', $params['min_delivery_date']);
			if (isset($params['max_delivery_date'])) $where->lessThanOrEqualTo('delivery_date', $params['max_delivery_date']);
			if (isset($params['min_expected_commissioning_date'])) $where->greaterThanOrEqualTo('expected_commissioning_date', $params['min_expected_commissioning_date']);
			if (isset($params['max_expected_commissioning_date'])) $where->lessThanOrEqualTo('expected_commissioning_date', $params['max_expected_commissioning_date']);
			if (isset($params['min_commissioning_date'])) $where->greaterThanOrEqualTo('commissioning_date', $params['min_commissioning_date']);
			if (isset($params['max_commissioning_date'])) $where->lessThanOrEqualTo('commissioning_date', $params['max_commissioning_date']);
			if (isset($params['min_due_date'])) $where->greaterThanOrEqualTo('due_date', $params['min_due_date']);
			if (isset($params['max_due_date'])) $where->lessThanOrEqualTo('due_date', $params['max_due_date']);
			if (isset($params['min_invoice_date'])) $where->greaterThanOrEqualTo('invoice_date', $params['min_invoice_date']);
			if (isset($params['max_invoice_date'])) $where->lessThanOrEqualTo('invoice_date', $params['max_invoice_date']);
			if (isset($params['min_expected_settlement_date'])) $where->greaterThanOrEqualTo('expected_settlement_date', $params['min_expected_settlement_date']);
			if (isset($params['max_expected_settlement_date'])) $where->lessThanOrEqualTo('expected_settlement_date', $params['max_expected_settlement_date']);
			if (isset($params['min_settlement_date'])) $where->greaterThanOrEqualTo('settlement_date', $params['min_settlement_date']);
			if (isset($params['max_settlement_date'])) $where->lessThanOrEqualTo('settlement_date', $params['max_settlement_date']);
				
			for ($i = 1; $i < 20; $i++) {
				if (isset($params['property_'.$i])) $where->like('property_'.$i, '%'.$params['property_'.$i].'%');
				if (isset($params['min_property_'.$i])) $where->greaterThanOrEqualTo('property_'.$i, $params['min_property_'.$i]);
				if (isset($params['max_property_'.$i])) $where->lessThanOrEqualTo('property_'.$i, $params['max_property_'.$i]);
			}
		}

    	// Sort the list
    	$select->where($where)->order(array($major.' '.$dir, 'identifier'));

    	$cursor = Commitment::getTable()->selectWith($select);
    	$orders = array();
    	foreach ($cursor as $order) {
    		$order->properties = $order->toArray();
    		$orders[] = $order;
    	}

    	return $orders;
    }

    public static function get($id, $column = 'id')
    {
    	$commitment = Commitment::getTable()->get($id, $column);
    	if (!$commitment) return null;
    	$commitment->properties = $commitment->toArray();
    	$commitment->projects = Commitment::getProjects();

		return $commitment;
    }

    public function computeDeadlines()
    {
    	$context = Context::getCurrent();
		foreach ($context->getInstance()->specifications['ppitCommitment']['deadlines'][$this->type] as $step => $deadline) {
			if ($this->status == $deadline['status']) {
				
				// Retrieve the start date
				if ($this->status == 'new') $start = $this->commitment_date;
				if ($this->status == 'shipped') $start = $this->shipment_date;
				if ($this->status == 'delivered') $start = $this->delivery_date;
				if ($this->status == 'commissioned') $start = $this->commissioning_date;
				if ($this->status == 'invoiced') $start = $this->invoice_date;
				if ($this->status == 'settled') $start = $this->settlement_date;

				// Compute the expected target date
				$targetDate = strtotime(($deadline['period']) ? $start.' +'.$deadline['period'].' '.$deadline['unit'] : $start);
				if ($step == 'retraction') $this->retraction_limit = date('Y-m-d', $targetDate);
				if ($step == 'shipment') $this->expected_shipment_date = date('Y-m-d', $targetDate);
				if ($step == 'delivery') $this->expected_delivery_date = date('Y-m-d', $targetDate);
				if ($step == 'commissioning') $this->expected_commissioning_date = date('Y-m-d', $targetDate);
				if ($step == 'invoice') $this->due_date = date('Y-m-d', $targetDate);
				if ($step == 'settlement') $this->expected_settlement_date = date('Y-m-d', $targetDate);
			}
		}
    }

    public static function instanciate($type)
    {
    	$commitment = new Commitment;
    	$commitment->type = $type;
    	$commitment->status = 'new';
    	$commitment->properties = $commitment->toArray();
    	$commitment->projects = Commitment::getProjects();
    	return $commitment;
    }
    
    public static function instanciateFromJson($type, $content)
    {
    	$customerCommunity = Community::get($content['buyer_party'], 'name');

		$account = Account::get($customerCommunity->id, 'customer_community_id');
		if (!$account) return null;

		$commitment = Commitment::instanciate($type);
		$commitment->account_id = $account->id;

    	// Load from a JSON web service
    	$commitment->identifier = $content['message_identifier'];
    	$commitment->commitment_date = $content['issue_date'];
    	$commitment->computeDeadlines();

    	return $commitment;
    }

    public static function instanciateFromXcbl($xmlMessage)
    {
    	$commitment = new Commitment;
    
    	// Load from an XML web service
    	$xmlOrder = new XmlOrder($xmlMessage);
    	$commitment->type = $xmlOrder->getType();
    	$commitment->commitment_date = $xmlOrder->getOrderIssueDate();
    	$commitment->identifier = $xmlOrder->getBuyerOrderNumber();
    	$commitment->expected_delivery_date = $xmlOrder->getRequestedDeliverByDate();
    
    	// Check integrity
    	if ($commitment->type == 'unknown') return null;
    
    	return $commitment;
    }

    public function loadData($data, $files) 
    {
    	$context = Context::getCurrent();
		$settings = $context->getConfig();

    	// Retrieve the data from the request

		if (array_key_exists('account_id', $data)) $this->account_id = (int) $data['account_id'];

		if (array_key_exists('project_id', $data)) $this->project_id = (int) $data['project_id'];
	    
		if (array_key_exists('caption', $data)) {
		    $this->caption = trim(strip_tags($data['caption']));
		   	if (strlen($this->caption) > 255) return 'Integrity';
		}
	    
		if (array_key_exists('description', $data)) {
			$this->description = trim(strip_tags($data['description']));
		    if (strlen($this->description) > 2047) return 'Integrity';
		}

		if (array_key_exists('amount', $data)) {
			$this->amount = trim(strip_tags($data['amount']));
			if (strlen($this->amount) > 255) return 'Integrity';
		}
		
		if (array_key_exists('identifier', $data)) {
			$this->identifier = $data['identifier'];
			if (strlen($this->identifier) > 255) return 'Integrity';
		}
		
		if (array_key_exists('quotation_identifier', $data)) {
			$this->quotation_identifier = $data['quotation_identifier'];
			if (strlen($this->quotation_identifier) > 255) return 'Integrity';
		}
		
		if (array_key_exists('invoice_identifier', $data)) {
			$this->invoice_identifier = $data['invoice_identifier'];
			if (strlen($this->invoice_identifier) > 255) return 'Integrity';
		}

		if (array_key_exists('commitment_date', $data)) {
			$this->commitment_date = trim(strip_tags($data['commitment_date']));
			if ($this->commitment_date && !checkdate(substr($this->commitment_date, 5, 2), substr($this->commitment_date, 8, 2), substr($this->commitment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('retraction_limit', $data)) {
			$this->retraction_limit = trim(strip_tags($data['retraction_limit']));
			if ($this->retraction_limit && !checkdate(substr($this->retraction_limit, 5, 2), substr($this->retraction_limit, 8, 2), substr($this->retraction_limit, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('retraction_date', $data)) {
			$this->retraction_date = trim(strip_tags($data['retraction_date']));
			if ($this->retraction_date && !checkdate(substr($this->retraction_date, 5, 2), substr($this->retraction_date, 8, 2), substr($this->retraction_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('expected_shipment_date', $data)) {
			$this->expected_shipment_date = trim(strip_tags($data['expected_shipment_date']));
			if ($this->expected_shipment_date && !checkdate(substr($this->expected_shipment_date, 5, 2), substr($this->expected_shipment_date, 8, 2), substr($this->expected_shipment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('shipment_date', $data)) {
			$this->shipment_date = trim(strip_tags($data['shipment_date']));
			if ($this->shipment_date && !checkdate(substr($this->shipment_date, 5, 2), substr($this->shipment_date, 8, 2), substr($this->shipment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('expected_delivery_date', $data)) {
			$this->expected_delivery_date = trim(strip_tags($data['expected_delivery_date']));
			if ($this->expected_delivery_date && !checkdate(substr($this->expected_delivery_date, 5, 2), substr($this->expected_delivery_date, 8, 2), substr($this->expected_delivery_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('delivery_date', $data)) {
			$this->delivery_date = trim(strip_tags($data['delivery_date']));
			if ($this->delivery_date && !checkdate(substr($this->delivery_date, 5, 2), substr($this->delivery_date, 8, 2), substr($this->delivery_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('expected_commissioning_date', $data)) {
			$this->expected_commissioning_date = trim(strip_tags($data['expected_commissioning_date']));
			if ($this->expected_commissioning_date && !checkdate(substr($this->expected_commissioning_date, 5, 2), substr($this->expected_commissioning_date, 8, 2), substr($this->expected_commissioning_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('commissioning_date', $data)) {
			$this->commissioning_date = trim(strip_tags($data['commissioning_date']));
			if ($this->commissioning_date && !checkdate(substr($this->commissioning_date, 5, 2), substr($this->commissioning_date, 8, 2), substr($this->commissioning_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('due_date', $data)) {
			$this->due_date = trim(strip_tags($data['due_date']));
			if ($this->due_date && !checkdate(substr($this->due_date, 5, 2), substr($this->due_date, 8, 2), substr($this->due_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('invoice_date', $data)) {
			$this->invoice_date = trim(strip_tags($data['invoice_date']));
			if ($this->invoice_date && !checkdate(substr($this->invoice_date, 5, 2), substr($this->invoice_date, 8, 2), substr($this->invoice_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('expected_settlement_date', $data)) {
			$this->expected_settlement_date = trim(strip_tags($data['expected_settlement_date']));
			if ($this->expected_settlement_date && !checkdate(substr($this->expected_settlement_date, 5, 2), substr($this->expected_settlement_date, 8, 2), substr($this->expected_settlement_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('settlement_date', $data)) {
			$this->settlement_date = trim(strip_tags($data['settlement_date']));
			if ($this->settlement_date && !checkdate(substr($this->settlement_date, 5, 2), substr($this->settlement_date, 8, 2), substr($this->settlement_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('property_1', $data)) {
			$this->property_1 = $data['property_1'];
		    if (strlen($this->property_1) > 255) return 'Integrity';
		}

		if (array_key_exists('property_2', $data)) {
			$this->property_2 = $data['property_2'];
	    	if (strlen($this->property_2) > 255) return 'Integrity';
		}

		if (array_key_exists('property_3', $data)) {
			$this->property_3 = $data['property_3'];
		    if (strlen($this->property_3) > 255) return 'Integrity';
		}

		if (array_key_exists('property_4', $data)) {
			$this->property_4 = $data['property_4'];
		    if (strlen($this->property_4) > 255) return 'Integrity';
		}

		if (array_key_exists('property_5', $data)) {
			$this->property_5 = $data['property_5'];
		    if (strlen($this->property_5) > 255) return 'Integrity';
		}

		if (array_key_exists('property_6', $data)) {
			$this->property_6 = $data['property_6'];
		    if (strlen($this->property_6) > 255) return 'Integrity';
		}

		if (array_key_exists('property_7', $data)) {
			$this->property_7 = $data['property_7'];
		    if (strlen($this->property_7) > 255) return 'Integrity';
		}

		if (array_key_exists('property_8', $data)) {
			$this->property_8 = $data['property_8'];
		    if (strlen($this->property_8) > 255) return 'Integrity';
		}

		if (array_key_exists('property_9', $data)) {
			$this->property_9 = $data['property_9'];
		    if (strlen($this->property_9) > 255) return 'Integrity';
		}

		if (array_key_exists('property_10', $data)) {
			$this->property_10 = $data['property_10'];
		    if (strlen($this->property_10) > 255) return 'Integrity';
		}

		if (array_key_exists('property_11', $data)) {
			$this->property_11 = $data['property_11'];
		    if (strlen($this->property_11) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_12', $data)) {
			$this->property_12 = $data['property_12'];
		    if (strlen($this->property_12) > 255) return 'Integrity';
		}

		if (array_key_exists('property_13', $data)) {
			$this->property_14 = $data['property_13'];
			if (strlen($this->property_13) > 255) return 'Integrity';
		}

		if (array_key_exists('property_14', $data)) {
			$this->property_14 = $data['property_14'];
			if (strlen($this->property_14) > 255) return 'Integrity';
		}

		if (array_key_exists('property_15', $data)) {
			$this->property_15 = $data['property_15'];
			if (strlen($this->property_15) > 255) return 'Integrity';
		}

		if (array_key_exists('property_16', $data)) {
			$this->property_16 = $data['property_16'];
			if (strlen($this->property_16) > 255) return 'Integrity';
		}

		if (array_key_exists('property_17', $data)) {
			$this->property_17 = $data['property_17'];
			if (strlen($this->property_17) > 255) return 'Integrity';
		}

		if (array_key_exists('property_18', $data)) {
			$this->property_18 = $data['property_18'];
			if (strlen($this->property_18) > 255) return 'Integrity';
		}

		if (array_key_exists('property_19', $data)) {
			$this->property_19 = $data['property_19'];
			if (strlen($this->property_19) > 255) return 'Integrity';
		}

		if (array_key_exists('property_20', $data)) {
			$this->property_20 = $data['property_20'];
			if (strlen($this->property_20) > 255) return 'Integrity';
		}
		
		if (array_key_exists('comment', $data)) {
			$this->comment = trim(strip_tags($data['comment']));
    		if (strlen($this->comment) > 2047) return 'Integrity';
		}
    	
		$this->files = $files;

		// Update the audit
    	$this->audit[] = array(
				'status' => $this->status,
				'time' => Date('Y-m-d G:i:s'),
				'n_fn' => $context->getFormatedName(),
				'comment' => $this->comment,
		);
	    $this->notification_time = null;
		$this->properties = $this->toArray();
		$this->update_time = $data['update_time'];
		return 'OK';
    }

    public function loadDataFromRequest($request, $action) {

    	$context = Context::getCurrent();
    	$updatableProperties = $context->getInstance()->specifications['ppitCommitment']['actions'][$action]['properties'];

    	// Retrieve the data from the request
    	$data = array();

    	// Specify the area only in creation
    	if (array_key_exists('account_id', $updatableProperties)) $data['account_id'] = $request->getPost('account_id');
    	if (array_key_exists('project_id', $updatableProperties)) $data['project_id'] = $request->getPost('project_id');
    	if (array_key_exists('caption', $updatableProperties)) $data['caption'] = $request->getPost('caption');
    	if (array_key_exists('description', $updatableProperties)) $data['description'] = $request->getPost('description');
    	if (array_key_exists('amount', $updatableProperties)) $data['amount'] = $request->getPost('amount');
    	if (array_key_exists('identifier', $updatableProperties)) $data['identifier'] = $request->getPost('identifier');
    	if (array_key_exists('quotation_identifier', $updatableProperties)) $data['quotation_identifier'] = $request->getPost('quotation_identifier');
    	if (array_key_exists('invoice_identifier', $updatableProperties)) $data['invoice_identifier'] = $request->getPost('invoice_identifier');
    	if (array_key_exists('commitment_date', $updatableProperties)) $data['commitment_date'] = $request->getPost('commitment_date');
    	if (array_key_exists('retraction_limit', $updatableProperties)) $data['retraction_limit'] = $request->getPost('retraction_limit');
    	if (array_key_exists('retraction_date', $updatableProperties)) $data['retraction_date'] = $request->getPost('retraction_date');
    	if (array_key_exists('expected_shipment_date', $updatableProperties)) $data['expected_shipment_date'] = $request->getPost('expected_shipment_date');
    	if (array_key_exists('shipment_date', $updatableProperties)) $data['shipment_date'] = $request->getPost('shipment_date');
    	if (array_key_exists('expected_delivery_date', $updatableProperties)) $data['expected_delivery_date'] = $request->getPost('expected_delivery_date');
    	if (array_key_exists('delivery_date', $updatableProperties)) $data['delivery_date'] = $request->getPost('delivery_date');
    	if (array_key_exists('expected_commissioning_date', $updatableProperties)) $data['expected_commissioning_date'] = $request->getPost('expected_commissioning_date');
    	if (array_key_exists('commissioning_date', $updatableProperties)) $data['commissioning_date'] = $request->getPost('commissioning_date');
    	if (array_key_exists('due_date', $updatableProperties)) $data['due_date'] = $request->getPost('due_date');
    	if (array_key_exists('invoice_date', $updatableProperties)) $data['invoice_date'] = $request->getPost('invoice_date');
    	if (array_key_exists('expected_settlement_date', $updatableProperties)) $data['expected_settlement_date'] = $request->getPost('expected_settlement_date');
    	if (array_key_exists('settlement_date', $updatableProperties)) $data['settlement_date'] = $request->getPost('settlement_date');
    	if (array_key_exists('comment', $updatableProperties)) $data['comment'] = $request->getPost('comment');

    	foreach ($context->getInstance()->specifications['ppitCommitment']['properties'] as $propertyId => $property) {
    		if ($property['type'] != 'file') $data[$propertyId] = $request->getPost($propertyId);
    	}

    	$data['update_time'] = $request->getPost('update_time');
    	 
    	// Change the status
    	if ($action && array_key_exists($action, $context->getInstance()->specifications['ppitCommitment']['actions'])) {
	    	$actionRules = $context->getInstance()->specifications['ppitCommitment']['actions'][$action];
	    	if (array_key_exists('targetStatus', $actionRules)) $this->status = $actionRules['targetStatus'];
    	}

    	// Retrieve the order form
    	$files = $request->getFiles()->toArray();
    	 
    	$return = $this->loadData($data, $files);
    	if ($return != 'OK') throw new \Exception('View error');
    }

    public function add($xcblOrder = null)
    {
		$context = Context::getCurrent();
		$config = $context->getConfig();

    	// Check consistency
    	$commitment = Commitment::getTable()->get($this->identifier, 'identifier');
    	if ($commitment) return 'Duplicate'; // Already exists

    	if (!$this->commitment_date) $this->commitment_date = date('Y-m-d');
    	$this->id = Commitment::getTable()->save($this);
		if (!$this->identifier) $this->identifier = sprintf('%1$06d', $this->id);
		
    	Commitment::getTable()->save($this);

    	// Send the confirmation message
    	if ($xcblOrder) {
    	
			// To be completed
    	}
    	return 'OK';
    }

    public function update($update_time, $xcblOrderResponse = null)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	$commitment = Commitment::get($this->id);

    	// Isolation check
    	if ($commitment->update_time > $update_time) return 'Isolation';

    	// Consistency check
	    $select = Commitment::getTable()->getSelect()->columns(array('id'))->where(array('identifier' => $this->identifier, 'id <> ?' => $this->id));
	    $cursor = Commitment::getTable()->selectWith($select);
	    if (count($cursor) > 0) return 'Duplicate';

    	// Save the order form and the commitment
    	if ($this->files) {
    		if ($context->getCommunityId()) {
    			$community = Community::get($context->getCommunityId());
    			$root_id = $community->root_document_id;
    		}
    		else $root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->order_form_id = $document->save();
    	}
    	Commitment::getTable()->save($this);

    	// Send the confirmation message
    	if ($xcblOrderResponse) {

    		$orderMessage = Message::get($this->$commitment_message_id);
			$xcblOrder = new XcblOrder($orderMessage);
    		$xcblOrderResponse->setOrderResponseIssueDate(date('Y-m-d').'T'.date('G:i:s'));
    		$xcblOrderResponse->setOrderReference($this->identifier);
    		$xcblOrderResponse->setSellerIdent($xcblOrder->getSellerIdent());
    		$xcblOrderResponse->setBuyerIdent($xcblOrder->getBuyerIdent());
    		$xcblOrderResponse->setHeaderStatusEvent($this->expected_delivery_date);
    	}
    	return 'OK';
    }

    public function invoice($invoiceProperties, $request)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    
    	// Submit the response message
    	$credentials = $context->getInstance()->specifications['ppitCommitment']['invoiceMessage'];
    
    	$client = new Client(
    			$credentials['url'],
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    
    	$client->setAuth($credential['user'], $credential['password'], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('POST');

    	$supplyerSheet = $context->getInstance()->getContactSpecs()['supplyerIdentificationSheet'];
    	$customerSheet = $context->getInstance()->getContactSpecs()['customerIdentificationSheet'];

    	$xmlMessage = Message::get($this->commitment_message_id);
    	$xmlOrder = new XmlOrder(new \SimpleXMLElement($xmlMessage->content));

    	$xmlUblInvoiceResponse = new XmlUblInvoice;
    	$xmlUblInvoiceResponse->setID($invoiceProperties['1-cbc:ID']);
    	$xmlUblInvoiceResponse->setIssueDate($invoiceProperties['4-cbc:IssueDate']);
    	$xmlUblInvoiceResponse->setContractDocumentReference('Bon de commande');
    	$xmlUblInvoiceResponse->setDelivery(null, $xmlOrder->getShipToPartyName(), $xmlOrder->getShipToPartyCity(), $xmlOrder->getShipToPartyPostalCode());
       	$xmlUblInvoiceResponse->setPaymentMeans($invoiceProperties['49-cbc:PaymentDueDate'], null, $supplyerSheet['PayeeFinancialAccount']);
    	$xmlUblInvoiceResponse->setPaymentTerms($supplyerSheet['PaymentTerms']);
    	$xmlUblInvoiceResponse->setTaxTotal($invoiceProperties['57-cbc:TaxAmount'], $invoiceProperties['58-cbc:TaxableAmount'], $invoiceProperties['60-cbc:Percent']);
    	$xmlUblInvoiceResponse->setLegalMonetaryTotal($invoiceProperties['62-cbc:LineExtensionAmount'], $order->excluding_tax, $invoiceProperties['64-cbc:TaxInclusiveAmount'], $invoiceProperties['65-cbc:PayableAmount']);

    	for ($i = 0; $i < $xmlOrder->getNumberOfLines(); $i++) {
    		$xmlUblInvoiceResponse->addInvoiceLine(
    				$i+1,
    				$xmlOrder->getLineTotalQuantity($i),
    				$xmlOrder->getLineItemTotal($i),
    				'EUR',
    				$this->commissioning_date,
    				round($xmlOrder->getLineItemTotal($i) * $invoiceProperties['60-cbc:Percent'] / 100, 2),
    				$xmlOrder->getLineItemTotal($i),
    				'TVA',
    				$xmlOrder->getLineItemTotal($i),
    				$xmlOrder->getLineCalculatedPriceBasisQuantity($i)
    		);
    	}
    	// Save the message
    	$message = Message::instanciate('orderResponse/invoice', $xmlUblInvoiceResponse->asXML());
    	$message->type = 'INVOICE';
    	$message->identifier = $this->identifier;
    	$message->add();
    	
    	// Add the message id to the order
    	$this->invoice_message_id = $message->id;
    	
    	$xmlUblInvoiceResponse->setUUID($message->id);
    	$content = $xmlUblInvoiceResponse->asXML();
    	$message->content = $content;
    	
    	// Post the confirmation message
    	$client->setRawBody($content);
    	$response = $client->send();
    	$message->http_status = $response->renderStatusLine();
    	
    	// Write to the log
    	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    		$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
    		$logger = new \Zend\Log\Logger();
    		$logger->addWriter($writer);
    		$logger->info('invoice;'.$this->identifier.';'.$response->renderStatusLine());
    	}
    	
    	// Save the message
    	$message->update($message->update_time);
    	 
    	return 'OK';
    }

    public static function notify()
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	$select = Commitment::getTable()->getSelect()->where(array('notification_time' => null));
		$cursor = Commitment::getTable()->selectWith($select);

		$newCommitments = array();
		$confirmedCommitments = array();
		$rejectedCommitments = array();
		$registeredCommitments = array();
		
		foreach ($context->getInstance()->getSpecs['ppitCommitment']['types'] as $type => $properties) {
			$newCommitments[$type] = array();
			$confirmedCommitments[$type] = array();
			$rejectedCommitments[$type] = array();
			$registeredCommitments[$type] = array();
		}
		foreach($cursor as $commiment) {
			if ($commitment->status == 'new') $newCommitments[$commitment->type][] = $commitment->identifier;
			elseif ($commitment->status == 'confirmed') $confirmedCommitments[$commitment->type][] = $commitment->identifier;
			elseif ($commitment->status == 'rejected') $rejectedCommitments[$commitment->type][] = $commitment->identifier;
			elseif ($commitment->status == 'registered') $registeredCommitments[$commitment->type][] = $commitment->identifier;
			$commitment->notification_time = date('Y-m-d H:i:s');
			Commitment::getTable()->save($commitment);
		}
		
		foreach ($context->getInstance()->getSpecs['ppitCommitment']['types'] as $type => $properties) {
			if (count($newCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%sales_manager%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['addTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['addText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $newCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
	
			if (count($confirmedCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%business_owner%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['confirmTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['confirmText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $confirmedCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
	    	
	    	if (count($rejectedCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%business_owner%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['rejectTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['rejectText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $rejectedCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
	    	
	    	if (count($registeredCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%business_owner%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['registerTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getInstance()->specifications['ppitCommitment']['messages']['registerText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $registeredCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
		}
    }

    public function isUsed($object)
    {
    	return false;
    }
    
    public function isDeletable() {
    
    	// Check the commitment status
    	if ($this->status != 'new') return false;
    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$commitment = Commitment::get($this->id);
    
    	// Isolation check
    	if ($commitment->update_time > $update_time) return 'Isolation';
    	 
    	Commitment::getTable()->delete($this->id);
    
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
    	if (!Commitment::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Commitment::$table = $sm->get('PpitCommitment\Model\CommitmentTable');
    	}
    	return Commitment::$table;
    }
}
