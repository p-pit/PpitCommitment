<?php
namespace PpitCommitment\Model;

use PpitAccounting\Model\Journal;
use PpitCommitment\Model\Account;
use PpitCommitment\Model\Subscription;
use PpitCommitment\Model\Term;
use PpitCore\Model\Community;
use PpitCore\Model\Vcard;
use PpitContact\Model\ContactMessage;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitDocument\Model\Document;
use PpitEquipment\Model\Area;
use PpitMasterData\Model\ProductOption;
use PpitMasterData\Model\OrgUnit;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\db\sql\Where;
use Zend\Log\Logger;
use Zend\Log\Writer;

class Commitment implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $credit_status;
    public $next_credit_consumption_date;
    public $last_credit_consumption_date;
    public $type;
	public $subscription_id;
	public $account_id;
	public $status;
	public $caption;
	public $description;
	public $customer_identifier;
	public $customer_invoice_name;
	public $customer_n_fn;
	public $customer_adr_street;
	public $customer_adr_extended;
	public $customer_adr_post_office_box;
	public $customer_adr_zip;
	public $customer_adr_city;
	public $customer_adr_state;
	public $customer_adr_country;
	public $product_identifier;
	public $product_brand;
	public $product_caption;
	public $quantity;
	public $unit_price;
	public $amount;
	public $taxable_1_amount;
	public $taxable_2_amount;
	public $taxable_3_amount;
	public $options;
	public $including_options_amount;
	public $taxable_1_total;
	public $taxable_2_total;
	public $taxable_3_total;
	public $cgv;
	public $identifier;
	public $quotation_identifier;
	public $invoice_identifier;
	public $credit_identifier;
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
	public $property_21;
	public $property_22;
	public $property_23;
	public $property_24;
	public $property_25;
	public $property_26;
	public $property_27;
	public $property_28;
	public $property_29;
	public $property_30;
	public $audit = array();
	public $excluding_tax;
	public $tax_regime;
	public $tax_1_amount;
	public $tax_2_amount;
	public $tax_3_amount;
	public $tax_amount;
	public $tax_inclusive;
	public $commitment_message_id;
	public $change_message_id;
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
	public $account;
	public $terms;
	public $termSum;
	public $subscriptions;
	public $subscription;
	public $breadcrumb;
    public $vat_rate;
    public $files;
    public $comment;
    
    protected $inputFilter;
    protected $validatePiInputFilter;
    protected $validateInputFilter;

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
        $this->credit_status = (isset($data['credit_status'])) ? $data['credit_status'] : null;
        $this->next_credit_consumption_date = (isset($data['next_credit_consumption_date'])) ? $data['next_credit_consumption_date'] : null;
        $this->last_credit_consumption_date = (isset($data['last_credit_consumption_date'])) ? $data['last_credit_consumption_date'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->subscription_id = (isset($data['subscription_id'])) ? $data['subscription_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->customer_identifier = (isset($data['customer_identifier'])) ? $data['customer_identifier'] : null;
        $this->customer_invoice_name = (isset($data['customer_invoice_name'])) ? $data['customer_invoice_name'] : null;
        $this->customer_n_fn = (isset($data['customer_n_fn'])) ? $data['customer_n_fn'] : null;
        $this->customer_adr_street = (isset($data['customer_adr_street'])) ? $data['customer_adr_street'] : null;
        $this->customer_adr_extended = (isset($data['customer_adr_extended'])) ? $data['customer_adr_extended'] : null;
        $this->customer_adr_post_office_box = (isset($data['customer_adr_post_office_box'])) ? $data['customer_adr_post_office_box'] : null;
        $this->customer_adr_zip = (isset($data['customer_adr_zip'])) ? $data['customer_adr_zip'] : null;
        $this->customer_adr_city = (isset($data['customer_adr_city'])) ? $data['customer_adr_city'] : null;
        $this->customer_adr_state = (isset($data['customer_adr_state'])) ? $data['customer_adr_state'] : null;
        $this->customer_adr_country = (isset($data['customer_adr_country'])) ? $data['customer_adr_country'] : null;
        $this->product_identifier = (isset($data['product_identifier'])) ? $data['product_identifier'] : null;
        $this->product_brand = (isset($data['product_brand'])) ? $data['product_brand'] : null;
        $this->product_caption = (isset($data['product_caption'])) ? $data['product_caption'] : null;
        $this->quantity = (isset($data['quantity'])) ? $data['quantity'] : null;
        $this->unit_price = (isset($data['unit_price'])) ? $data['unit_price'] : null;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : null;
        $this->taxable_1_amount = (isset($data['taxable_1_amount'])) ? $data['taxable_1_amount'] : null;
        $this->taxable_2_amount = (isset($data['taxable_2_amount'])) ? $data['taxable_2_amount'] : null;
        $this->taxable_3_amount = (isset($data['taxable_3_amount'])) ? $data['taxable_3_amount'] : null;
        $this->options = (isset($data['options'])) ? ((is_array($data['options'])) ? $data['options'] : json_decode($data['options'], true)) : null;
        $this->including_options_amount = (isset($data['including_options_amount'])) ? $data['including_options_amount'] : null;
        $this->taxable_1_total = (isset($data['taxable_1_total'])) ? $data['taxable_1_total'] : null;
        $this->taxable_2_total = (isset($data['taxable_2_total'])) ? $data['taxable_2_total'] : null;
        $this->taxable_3_total = (isset($data['taxable_3_total'])) ? $data['taxable_3_total'] : null;
        $this->cgv = (isset($data['cgv'])) ? $data['cgv'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->quotation_identifier = (isset($data['quotation_identifier'])) ? $data['quotation_identifier'] : null;
        $this->invoice_identifier = (isset($data['invoice_identifier'])) ? $data['invoice_identifier'] : null;
        $this->credit_identifier = (isset($data['credit_identifier'])) ? $data['credit_identifier'] : null;
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
        $this->property_21 = (isset($data['property_21'])) ? $data['property_21'] : null;
        $this->property_22 = (isset($data['property_22'])) ? $data['property_22'] : null;
        $this->property_23 = (isset($data['property_23'])) ? $data['property_23'] : null;
        $this->property_24 = (isset($data['property_24'])) ? $data['property_24'] : null;
        $this->property_25 = (isset($data['property_25'])) ? $data['property_25'] : null;
        $this->property_26 = (isset($data['property_26'])) ? $data['property_26'] : null;
        $this->property_27 = (isset($data['property_27'])) ? $data['property_27'] : null;
        $this->property_28 = (isset($data['property_28'])) ? $data['property_28'] : null;
        $this->property_29 = (isset($data['property_29'])) ? $data['property_29'] : null;
        $this->property_30 = (isset($data['property_30'])) ? $data['property_30'] : null;
        $this->audit = (isset($data['audit'])) ? ((is_array($data['audit'])) ? $data['audit'] : json_decode($data['audit'], true)) : array();
        $this->excluding_tax = (isset($data['excluding_tax'])) ? $data['excluding_tax'] : null;
        $this->tax_regime = (isset($data['tax_regime'])) ? $data['tax_regime'] : null;
        $this->tax_1_amount = (isset($data['tax_1_amount'])) ? $data['tax_1_amount'] : null;
        $this->tax_2_amount = (isset($data['tax_2_amount'])) ? $data['tax_2_amount'] : null;
        $this->tax_3_amount = (isset($data['tax_3_amount'])) ? $data['tax_3_amount'] : null;
        $this->tax_amount = (isset($data['tax_amount'])) ? $data['tax_amount'] : null;
        $this->tax_inclusive = (isset($data['tax_inclusive'])) ? $data['tax_inclusive'] : null;
        $this->commitment_message_id = (isset($data['commitment_message_id'])) ? $data['commitment_message_id'] : null;
        $this->change_message_id = (isset($data['change_message_id'])) ? $data['change_message_id'] : null;
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
    	$data['credit_status'] = $this->credit_status;
    	$data['next_credit_consumption_date'] = ($this->next_credit_consumption_date) ? $this->next_credit_consumption_date : null;
    	$data['last_credit_consumption_date'] = ($this->last_credit_consumption_date) ? $this->last_credit_consumption_date : null;
    	$data['type'] = $this->type;
    	$data['account_id'] = $this->account_id;
    	$data['subscription_id'] = $this->subscription_id;
    	$data['status'] = $this->status;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['customer_identifier'] = $this->customer_identifier;
    	$data['customer_invoice_name'] = $this->customer_invoice_name;
    	$data['customer_n_fn'] = $this->customer_n_fn;
    	$data['customer_adr_street'] = $this->customer_adr_street;
    	$data['customer_adr_extended'] = $this->customer_adr_extended;
    	$data['customer_adr_post_office_box'] = $this->customer_adr_post_office_box;
    	$data['customer_adr_zip'] = $this->customer_adr_zip;
    	$data['customer_adr_city'] = $this->customer_adr_city;
    	$data['customer_adr_state'] = $this->customer_adr_state;
    	$data['customer_adr_country'] = $this->customer_adr_country;
    	$data['product_identifier'] = $this->product_identifier;
    	$data['product_brand'] = $this->product_brand;
    	$data['product_caption'] = $this->product_caption;
    	$data['quantity'] = $this->quantity;
    	$data['unit_price'] = $this->unit_price;
    	$data['amount'] = $this->amount;
    	$data['taxable_1_amount'] = $this->taxable_1_amount;
    	$data['taxable_2_amount'] = $this->taxable_2_amount;
    	$data['taxable_3_amount'] = $this->taxable_3_amount;
    	$data['options'] = json_encode($this->options);
    	$data['including_options_amount'] = $this->including_options_amount;
    	$data['taxable_1_total'] = $this->taxable_1_total;
    	$data['taxable_2_total'] = $this->taxable_2_total;
    	$data['taxable_3_total'] = $this->taxable_3_total;
    	$data['cgv'] = $this->cgv;
    	$data['identifier'] = $this->identifier;
    	$data['quotation_identifier'] = $this->quotation_identifier;
    	$data['invoice_identifier'] = $this->invoice_identifier;
    	$data['credit_identifier'] = $this->credit_identifier;
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
    	$data['property_21'] = $this->property_21;
    	$data['property_22'] = $this->property_22;
    	$data['property_23'] = $this->property_23;
    	$data['property_24'] = $this->property_24;
    	$data['property_25'] = $this->property_25;
    	$data['property_26'] = $this->property_26;
    	$data['property_27'] = $this->property_27;
    	$data['property_28'] = $this->property_28;
    	$data['property_29'] = $this->property_29;
    	$data['property_30'] = $this->property_30;
    	$data['audit'] = json_encode($this->audit);
    	$data['excluding_tax'] = $this->excluding_tax;
    	$data['tax_regime'] = $this->tax_regime;
    	$data['tax_1_amount'] = $this->tax_1_amount;
    	$data['tax_2_amount'] = $this->tax_2_amount;
    	$data['tax_3_amount'] = $this->tax_3_amount;
    	$data['tax_amount'] = $this->tax_amount;
    	$data['tax_inclusive'] = $this->tax_inclusive;
    	$data['commitment_message_id'] = $this->commitment_message_id;
    	$data['change_message_id'] = $this->change_message_id;
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
    		->join('core_community', 'commitment_account.customer_community_id = core_community.id', array('customer_name' => 'name'), 'left')
    		->join('commitment_subscription', 'commitment.subscription_id = commitment_subscription.id', array('product_identifier'), 'left');
    	
    	$where = new Where();
    	$where->notEqualTo('commitment.status', 'deleted');

    	// Filter on type
		if ($type) $where->equalTo('commitment.type', $type);

		// Todo list vs search modes
		if ($mode == 'todo') {

			$todo = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['todo'];
			foreach($todo as $role => $properties) {
				if ($context->hasRole($role)) {
					foreach($properties as $property => $predicate) {
						if ($predicate['selector'] == 'equalTo') $where->equalTo('commitment.'.$property, $predicate['value']);
						elseif ($predicate['selector'] == 'in') $where->in('commitment.'.$property, $predicate['value']);
						elseif ($predicate['selector'] == 'deadline') $where->lessThanOrEqualTo('commitment.'.$property, date('Y-m-d', strtotime(date('Y-m-d').'+ '.$predicate['value'].' days')));
					}
				}
			}
		}
		else {

			// Set the filters
			foreach ($params as $propertyId => $property) {
				if ($propertyId == 'account_id') $where->equalTo('account_id', $params['account_id']);
				elseif ($propertyId == 'subscription_id') $where->equalTo('subscription_id', $params['subscription_id']);
				elseif ($propertyId == 'customer_name') $where->like('core_community.name', '%'.$params[$propertyId].'%');
				elseif ($propertyId == 'product_identifier') $where->like('product_identifier', '%'.$params[$propertyId].'%');
				elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment.'.substr($propertyId, 4), $params[$propertyId]);
				elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment.'.substr($propertyId, 4), $params[$propertyId]);
				else $where->like('commitment.'.$propertyId, '%'.$params[$propertyId].'%');
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
        if ($commitment->account_id) {
	    	$commitment->account = Account::get($commitment->account_id);
	    	$community = Community::get($commitment->account->customer_community_id);
	    	$commitment->customer_name = $community->name;
    	}
/*    	if ($commitment->subscription_id) {
	    	$subscription = Subscription::get($commitment->subscription_id);
    		$commitment->product_identifier = $subscription->product_identifier;
    	}*/
    	$commitment->properties = $commitment->toArray();
    	$commitment->subscriptions = Subscription::getList(array(), 'product_identifier', 'ASC');

    	$commitment->terms = Term::getList(array('commitment_id' => $commitment->id), 'due_date', 'ASC', 'search');
    	$commitment->termSum = 0;
    	foreach ($commitment->terms as $term) $commitment->termSum += $term->amount;

    	return $commitment;
    }

    public static function getArray($id, $column = 'id')
    {
    	$commitment = Commitment::getTable()->get($id, $column);
    	$commitment->terms = Term::getList(array('commitment_id' => $commitment->id), 'due_date', 'ASC', 'search');
    	$commitment->termSum = 0;
    	foreach ($commitment->terms as $term) $commitment->termSum += $term->amount;
    	$data = $commitment->toarray();
        if ($commitment->account_id) {
	    	$data['account'] = Account::getArray($commitment->account_id);
	    	$community = Community::get($data['account']['customer_community_id']);
	    	$data['customer_name'] = $community->name;
    	}
/*    	if ($commitment->account->contact_1) $data['account']['contact_1'] = $commitment->account->contact_1->toArray();
    	if ($commitment->account->contact_2) $data['account']['contact_2'] = $commitment->account->contact_2->toArray();
    	if ($commitment->account->contact_3) $data['account']['contact_3'] = $commitment->account->contact_3->toArray();
    	if ($commitment->account->contact_4) $data['account']['contact_4'] = $commitment->account->contact_4->toArray();
    	if ($commitment->account->contact_5) $data['account']['contact_5'] = $commitment->account->contact_5->toArray();*/
    	return $data;
    }

    public function computeDeadlines()
    {
    	$context = Context::getCurrent();
		foreach ($context->getConfig('commitment'.(($this->type) ? '/'.$this->type : ''))['deadlines'] as $step => $deadline) {
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

    public static function instanciate($type, $subscription = null)
    {
    	$commitment = new Commitment;
    	$commitment->type = $type;
    	if ($subscription) {
    		$commitment->subscription_id = $subscription->id;
    		$commitment->subscription = $subscription;
    		$commitment->description = $subscription->description;
    	}
    	$commitment->status = 'new';
    	$commitment->quantity = 1;
    	$commitment->properties = $commitment->toArray();
    	$commitment->subscriptions = Subscription::getList(array('type' => $type), 'product_identifier', 'ASC');
    	$commitment->options = array();
    	$commitment->terms = array();
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
    
    public function computeHeader($proforma = false)
    {
    	$context = Context::getCurrent();
    	$specsId = ($proforma) ? 'commitment/proforma' : 'commitment/invoice';
    	$type = $this->type;
    	if ($context->getConfig($specsId.(($type) ? '/'.$type : ''))) $invoiceSpecs = $context->getConfig($specsId.(($type) ? '/'.$type : ''));
    	else $invoiceSpecs = $context->getConfig($specsId);
    	$this->customer_invoice_name = '';
    	$first = true;
    	foreach($invoiceSpecs['header'] as $line) {
    		$arguments = array();
    		foreach($line['params'] as $propertyId) {
    			if ($propertyId == 'date') $arguments[] = $context->decodeDate(date('Y-m-d'));
    			else {
    				if (array_key_exists($propertyId, $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'])) {
    					$property = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
    				}
    				else {
    					$property = $context->getConfig('commitment')['properties'][$propertyId];
    				}
    				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
    				if ($propertyId == 'customer_name') $arguments[] = $this->customer_name;
    				elseif ($property['type'] == 'date') $arguments[] = $context->decodeDate($this->properties[$propertyId]);
    				elseif ($property['type'] == 'number') $arguments[] = $context->formatFloat($this->properties[$propertyId], 2);
    				elseif ($property['type'] == 'select') $arguments[] = $property['modalities'][$this->properties[$propertyId]][$context->getLocale()];
    				else $arguments[] = $this->properties[$propertyId];
    			}
    		}
    		if (!$first) $this->customer_invoice_name .= "\n";
    		$first = false;
    		$this->customer_invoice_name .= vsprintf($line['format'][$context->getLocale()], $arguments);
    	}
    	$account = $this->account;
    	$invoicingContact = null;
    	if ($account->contact_1_status == 'invoice') $invoicingContact = $account->contact_1;
    	elseif ($account->contact_2_status == 'invoice') $invoicingContact = $account->contact_2;
    	elseif ($account->contact_3_status == 'invoice') $invoicingContact = $account->contact_3;
    	elseif ($account->contact_4_status == 'invoice') $invoicingContact = $account->contact_4;
    	elseif ($account->contact_5_status == 'invoice') $invoicingContact = $account->contact_5;
    		
    	if (!$invoicingContact) {
    		if ($account->contact_1_status == 'main') $invoicingContact = $account->contact_1;
    		elseif ($account->contact_2_status == 'main') $invoicingContact = $account->contact_2;
    		elseif ($account->contact_3_status == 'main') $invoicingContact = $account->contact_3;
    		elseif ($account->contact_4_status == 'main') $invoicingContact = $account->contact_4;
    		elseif ($account->contact_5_status == 'main') $invoicingContact = $account->contact_5;
    	}
    	if (!$invoicingContact) $invoicingContact = $account->contact_1;
    		 
    	$this->customer_n_fn = '';
    	if ($invoicingContact->n_title || $invoicingContact->n_last || $invoicingContact->n_first) {
    		if ($invoicingContact->n_title) $this->customer_n_fn .= $invoicingContact->n_title.' ';
    		$this->customer_n_fn .= $invoicingContact->n_last.' ';
    		$this->customer_n_fn .= $invoicingContact->n_first;
    	}
    	$this->customer_adr_street = $invoicingContact->adr_street;
    	$this->customer_adr_extended = $invoicingContact->adr_extended;
    	$this->customer_adr_post_office_box = $invoicingContact->adr_post_office_box;
    	$this->customer_adr_zip = $invoicingContact->adr_zip;
    	$this->customer_adr_city = $invoicingContact->adr_city;
    	$this->customer_adr_state = $invoicingContact->adr_state;
    	$this->customer_adr_country = $invoicingContact->adr_country;
    }
    
    public function computeFooter() 
    {
    	$context = Context::getCurrent();
    	$this->including_options_amount = $this->amount;
    	$this->taxable_1_total = $this->taxable_1_amount;
    	$this->taxable_2_total = $this->taxable_2_amount;
    	$this->taxable_3_total = $this->taxable_3_amount;
    	foreach($this->options as $option) {
    		$this->including_options_amount += $option['amount'];
    		if ($option['vat_id'] == 1) $this->taxable_1_total += $option['amount'];
    		if ($option['vat_id'] == 2) $this->taxable_2_total += $option['amount'];
    		if ($option['vat_id'] == 3) $this->taxable_3_total += $option['amount'];
    	}
    	if ($context->getConfig('commitment/'.$this->type)['tax'] == 'excluding') {
    		$this->excluding_tax = $this->including_options_amount;
    		$this->tax_1_amount = round($this->taxable_1_total * 0.2, 2);
    		$this->tax_2_amount = round($this->taxable_2_total * 0.1, 2);
    		$this->tax_3_amount = round($this->taxable_3_total * 0.055, 2);
    		$this->tax_amount = $this->tax_1_amount + $this->tax_2_amount + $this->tax_3_amount;
    		$this->tax_inclusive = $this->excluding_tax + $this->tax_amount;
    	}
    	else {
    		$this->tax_inclusive = $this->including_options_amount;
    		$this->tax_1_amount = $this->taxable_1_total - round($this->taxable_1_total / 1.2, 2);
    		$this->tax_2_amount = $this->taxable_2_total - round($this->taxable_2_total / 1.1, 2);
    		$this->tax_3_amount = $this->taxable_3_total - round($this->taxable_3_total / 1.055, 2);
    		$this->tax_amount = $this->tax_1_amount + $this->tax_2_amount + $this->tax_3_amount;
    		$this->excluding_tax = $this->tax_inclusive - $this->tax_amount;
    	}
    }
    
    public function loadData($data, $files = null) 
    {
    	$context = Context::getCurrent();
		$settings = $context->getConfig();

    	// Retrieve the data from the request

		if (array_key_exists('status', $data)) {
			$this->status = trim(strip_tags($data['status']));
			if (strlen($this->status) > 255) return 'Integrity';
		}

		if (array_key_exists('type', $data)) {
			$this->type = trim(strip_tags($data['type']));
			if (strlen($this->type) > 255) return 'Integrity';
		}
		
		if (array_key_exists('next_credit_consumption_date', $data)) $this->next_credit_consumption_date = $data['next_credit_consumption_date'];
		
		if (array_key_exists('account_id', $data)) $this->account_id = (int) $data['account_id'];

		if (array_key_exists('subscription_id', $data)) $this->subscription_id = (int) $data['subscription_id'];

		if (array_key_exists('caption', $data)) {
		    $this->caption = trim(strip_tags($data['caption']));
		   	if (strlen($this->caption) > 255) return 'Integrity';
		}
	    
		if (array_key_exists('description', $data)) {
			$this->description = trim(strip_tags($data['description']));
		    if (strlen($this->description) > 2047) return 'Integrity';
		}

		if (array_key_exists('customer_identifier', $data)) {
			$this->customer_identifier = trim(strip_tags($data['customer_identifier']));
			if (strlen($this->customer_identifier) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_name', $data)) {
			$this->customer_name = trim(strip_tags($data['customer_name']));
			if (strlen($this->customer_name) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_n_fn', $data)) {
			$this->customer_n_fn = trim(strip_tags($data['customer_n_fn']));
			if (!$this->customer_identifier || strlen($this->customer_n_fn) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_street', $data)) {
			$this->customer_adr_street = trim(strip_tags($data['customer_adr_street']));
			if (strlen($this->customer_adr_street) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_extended', $data)) {
			$this->customer_adr_extended = trim(strip_tags($data['customer_adr_extended']));
			if (strlen($this->customer_adr_extended) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_post_office_box', $data)) {
			$this->customer_adr_post_office_box = trim(strip_tags($data['customer_adr_post_office_box']));
			if (strlen($this->customer_adr_post_office_box) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_zip', $data)) {
			$this->customer_adr_zip = trim(strip_tags($data['customer_adr_zip']));
			if (strlen($this->customer_adr_zip) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_city', $data)) {
			$this->customer_adr_city = trim(strip_tags($data['customer_adr_city']));
			if (strlen($this->customer_adr_city) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_state', $data)) {
			$this->customer_adr_state = trim(strip_tags($data['customer_adr_state']));
			if (strlen($this->customer_adr_state) > 255) return 'Integrity';
		}

		if (array_key_exists('customer_adr_country', $data)) {
			$this->customer_adr_country = trim(strip_tags($data['customer_adr_country']));
			if (strlen($this->customer_adr_country) > 255) return 'Integrity';
		}

		if (array_key_exists('product_caption', $data)) {
			$this->product_caption = trim(strip_tags($data['product_caption']));
			if (strlen($this->product_caption) > 255) return 'Integrity';
		}

		if (array_key_exists('product_brand', $data)) {
			$this->product_brand = trim(strip_tags($data['product_brand']));
			if (strlen($this->product_brand) > 255) return 'Integrity';
		}
		
		if (array_key_exists('product_identifier', $data)) {
			$this->product_identifier = trim(strip_tags($data['product_identifier']));
			if (strlen($this->product_identifier) > 255) return 'Integrity';
		}

		if (array_key_exists('quantity', $data)) {
			$this->quantity = (float) $data['quantity'];
		}

		if (array_key_exists('unit_price', $data)) {
			$this->unit_price = (float) $data['unit_price'];
		}
		
		if (array_key_exists('amount', $data)) {
			$this->amount = trim(strip_tags($data['amount']));
			if (strlen($this->amount) > 255) return 'Integrity';
		}

		if (array_key_exists('taxable_1_amount', $data)) {
			$this->taxable_1_amount = (float) $data['taxable_1_amount'];
		}

		if (array_key_exists('taxable_2_amount', $data)) {
			$this->taxable_2_amount = (float) $data['taxable_2_amount'];
		}

		if (array_key_exists('taxable_3_amount', $data)) {
			$this->taxable_3_amount = (float) $data['taxable_3_amount'];
		}

    	if (array_key_exists('options', $data)) {
    		$this->options = array();
    		foreach($data['options'] as $entry) {
				$entry['identifier'] = trim(strip_tags($entry['identifier']));
    			$productOption = ProductOption::get($entry['identifier'], 'reference');
    			if ($productOption) {
    				$option = array();
    				$option['identifier'] = $entry['identifier'];
					$option['caption'] = $entry['caption'];
    				$option['unit_price'] = $entry['unit_price'];
    				$option['quantity'] = $entry['quantity'];
    				$option['amount'] = $option['unit_price'] * $option['quantity']; // Redundancy to solve
					$option['vat_id'] = $productOption->vat_id; // Redundancy to solve
					$this->options[] = $option;
    			}
    		}
		}
		
		$this->computeFooter();
		
		if (array_key_exists('cgv', $data)) {
			$this->cgv = trim(strip_tags($data['cgv']));
			if (strlen($this->cgv) > 16777215) return 'Integrity';
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

		if (array_key_exists('property_21', $data)) {
			$this->property_11 = $data['property_21'];
			if (strlen($this->property_21) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_22', $data)) {
			$this->property_12 = $data['property_22'];
			if (strlen($this->property_22) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_23', $data)) {
			$this->property_14 = $data['property_23'];
			if (strlen($this->property_23) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_24', $data)) {
			$this->property_14 = $data['property_24'];
			if (strlen($this->property_24) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_25', $data)) {
			$this->property_15 = $data['property_25'];
			if (strlen($this->property_25) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_26', $data)) {
			$this->property_16 = $data['property_26'];
			if (strlen($this->property_26) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_27', $data)) {
			$this->property_17 = $data['property_27'];
			if (strlen($this->property_27) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_28', $data)) {
			$this->property_18 = $data['property_28'];
			if (strlen($this->property_28) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_29', $data)) {
			$this->property_19 = $data['property_29'];
			if (strlen($this->property_29) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_30', $data)) {
			$this->property_20 = $data['property_30'];
			if (strlen($this->property_30) > 255) return 'Integrity';
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
				'n_fn' => array_key_exists('n_fn', $data) ? $data['n_fn'] : $context->getFormatedName(),
				'comment' => $this->comment,
		);
	    $this->notification_time = null;
		$this->properties = $this->toArray();
		return 'OK';
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
    	if ($update_time && $commitment->update_time > $update_time) return 'Isolation';

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

    public function record($step)
    {
    	$context = Context::getCurrent();
    	$accountingChart = $context->getConfig('journal/accountingChart/sale')[$this->type];
    	if (array_key_exists($step, $accountingChart)) {
    		$step = $accountingChart[$step];
	    	$journalEntry = Journal::instanciate();
	    	$data = array();
	    	$data['operation_date'] = $this->invoice_date;
	    	$data['reference'] = $this->invoice_identifier;
	    	$data['caption'] = $this->caption;
	    	$data['commitment_id'] = $this->id;
	    	$data['rows'] = array();
	    	foreach ($step as $account => $rule) {
	    		$amount = $this->properties[$rule['source']];
	    		if ($amount > 0) {
	    			$row = array();
	    			$row['account'] = $account;
	    			$row['direction'] = $rule['direction'];
	    			$row['amount'] = $amount;
	    			$data['rows'][] = $row;
	    		}
	    	}
	    	$journalEntry->loadData($data);
	    	$journalEntry->add();
    	}
    }
    
    public function invoice($invoiceProperties, $request)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    
    	// Submit the response message
    	$credentials = $context->getConfig('commitment')['invoiceMessage'];
    
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
    	if ($context->getConfig()['isTraceActive']) {
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
		
		foreach ($context->getConfig('commitment')['types'] as $type => $properties) {
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
		
		foreach ($context->getConfig('commitment')['types'] as $type => $properties) {
			if (count($newCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%sales_manager%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getConfig('commitment')['messages']['addTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getConfig('commitment')['messages']['addText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $newCommitments[$type]));
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
	    		$title = sprintf($context->getConfig('commitment')['messages']['confirmTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getConfig('commitment')['messages']['confirmText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $confirmedCommitments[$type]));
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
	    		$title = sprintf($context->getConfig('commitment')['messages']['rejectTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getConfig('commitment')['messages']['rejectText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $rejectedCommitments[$type]));
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
	    		$title = sprintf($context->getConfig('commitment')['messages']['registerTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getConfig('commitment')['messages']['registerText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $registeredCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
		}
    }

    public static function consumeCredits($live, $mailTo)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	
    	// Open log
    	if ($live) {
    		$writer = new Writer\Stream('data/log/console.txt');
	    	$logger = new Logger();
	    	$logger->addWriter($writer);
    	}
 
    	// Retrieve instances
    	$select = Instance::getTable()->getSelect();
    	$cursor = Instance::getTable()->selectWith($select);
    	$instances = array();
    	$instanceIds = array();
    	foreach ($cursor as $instance) {
    		$unlimitedCredits = (array_key_exists('unlimitedCredits', $instance->specifications)) ? $instance->specifications['unlimitedCredits'] : false;
    		
    		// Log
    		if ($config['isTraceActive']) {
	    		$logText = 'Instance : id='.$instance->id.', caption='.$instance->caption.', unlimitedCredits='.(($unlimitedCredits) ? 'true' : 'false');
	    		if ($live) $logger->info($logText);
	    		else print_r($logText."\n");
			}

    		if (!$unlimitedCredits) {
    			$instance->administrators = array();
    			$instances[$instance->id] = $instance;
    			$instanceIds[] = $instance->id;
    		}
    	}

    	// Retrieve credits
    	$select = Credit::getTable()->getSelect();
    	$where = new Where();
    	$where->in('instance_id', $instanceIds);
    	$where->equalTo('type', 'p-pit-engagements');
    	$select->where($where);
    	$cursor = Credit::getTable()->transSelectWith($select);
    	$credits = array();
    	foreach ($cursor as $credit) {
    		$credit->consumers = array();
    		$credits[$credit->instance_id] = $credit;
    	}
    	 
    	// Retrieve commitments and count
    	$select = Commitment::getTable()->getSelect()
    		->join('core_instance', 'commitment.instance_id = core_instance.id', array(), 'left')
    		->join('commitment_account', 'commitment.account_id = commitment_account.id', array(), 'left')
    		->join('core_community', 'commitment_account.customer_community_id = core_community.id', array('customer_name' => 'name'), 'left');
    	$where = new Where();
    	$where->in('commitment.instance_id', $instanceIds);
		$where->notEqualTo('commitment.credit_status', 'closed');
		$where->notEqualTo('commitment.credit_status', 'suspended');
		$select->where($where);
		$cursor = Commitment::getTable()->transSelectWith($select);
		foreach ($cursor as $commitment) {
			if (array_key_exists($commitment->instance_id, $credits)) $credits[$commitment->instance_id]->consumers[] = $commitment;
		}

		// Retrieve administrators to be notified
		$select = Vcard::getTable()->getSelect();
		$where = new Where;
		$where->like('roles', '%admin%');
		$select->where($where);
		$cursor = Vcard::getTable()->transSelectWith($select);
		foreach ($cursor as $contact) {
			if ($contact->is_notified) $instances[$contact->instance_id]->administrators[] = $contact;
		}
		
		// Check enough credits are available
		foreach ($credits as $credit) {
		 
			// Compute the credit consumption at -7 days and at due date
    		$counter7 = 0;
			$counter0 = 0;
			$dailyConsumption = 0;
			$creditModified = false;
			$blocked = array();
			foreach($credit->consumers as $commitment) {
				if ($commitment->credit_status == 'blocked') $blocked[] = $commitment->customer_name.' - '.$commitment->caption;
    			else {
					if ($commitment->next_credit_consumption_date <= date('Y-m-d', strtotime(date('Y-m-d').' + 7 days'))) $counter7++;
	    			if ($commitment->next_credit_consumption_date <= date('Y-m-d')) $counter0++;
	    			if ($commitment->next_credit_consumption_date <= date('Y-m-d')) {
	    				if ($credit->quantity > 0) {
		    				// Consume 1 credit
		    				$credit->quantity--;
		    				$credit_consumption_date = $commitment->next_credit_consumption_date;
							$commitment->next_credit_consumption_date = date('Y-m-d', strtotime($credit_consumption_date.' + 31 days'));
							$commitment->last_credit_consumption_date = $credit_consumption_date;
			    			$credit->audit[] = array(
		    						'period' => date('Y-m'),
			    					'quantity' => -1,
			    					'status' => 'used',
			    					'reference' => $commitment->customer_name.' - '.$commitment->caption,
			    					'time' => Date('Y-m-d G:i:s'),
			    					'n_fn' => 'P-PIT',
			    					'comment' => 'Utilisation mensuelle pour la période du '.$context->decodeDate($commitment->last_credit_consumption_date).' au '.$context->decodeDate($commitment->next_credit_consumption_date),
			    			);
								
		    				// Log
		    				if ($config['isTraceActive']) {
		   						$logText = 'Commitment : instance_id='.$commitment->instance_id.', id='.$commitment->id.', caption='.$commitment->identifier.', status='.$commitment->credit_status;
		    					if ($live) $logger->info($logText);
		    					else print_r($logText."\n");
		    				}
	    				}
	    				else {
	    					$blocked[] = $commitment->identifier;
	    					$commitment->credit_status = 'blocked';
			    			$credit->audit[] = array(
		    						'period' => date('Y-m'),
			    					'quantity' => 0,
			    					'status' => 'blocked',
			    					'reference' => $commitment->customer_name.' - '.$commitment->caption,
			    					'time' => Date('Y-m-d G:i:s'),
			    					'n_fn' => 'P-PIT',
			    					'comment' => array(
										'en_US' => 'Record suspended due to lack of credit',
										'fr_FR' => 'Dossier suspendu faute de crédit suffisant',
									),
			    			);
	    				}
	    				$creditModified = true;
	    				if ($live) Commitment::getTable()->transSave($commitment);
	    			}
    			}
			}
			if ($creditModified && $live) Credit::getTable()->transSave($credit);

			// Notify a suspension of service
    		if ($blocked) {

    			// Log
    			$logText = 'ALERT : Not enough credits for P-Pit Engagements available on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    			
    			// Notify
    			if ($live) {
    				$url = $config['ppitCoreSettings']['domainName'];
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['commitment/consumeCredit']['messages']['suspendedServiceTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['commitment/consumeCredit']['messages']['suspendedServiceText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								implode("\n", $blocked),
    								$credit->quantity
    						);
    						ContactMessage::sendMail($contact->email, $text, $title);
    					}
    				}
    			}
    		}
    		elseif ($credit->quantity >= 0 && $credit->quantity - $counter7 < 0) {
    
    			// Log
    			$logText = 'ALERT : Risk of credits lacking for P-PIT Commitments on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    
    			// Notify
    			if ($live) {
    				$url = $config['ppitCoreSettings']['domainName'];
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['commitment/consumeCredit']['messages']['availabilityAlertTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['commitment/consumeCredit']['messages']['availabilityAlertText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								$credit->quantity,
    								$counter7
    						);
    						ContactMessage::sendMail($contact->email, $text, $title);
    					}
    				}
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
 
    	Term::getTable()->multipleDelete(array('commitment_id' => $this->id));
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
