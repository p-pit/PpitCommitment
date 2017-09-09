<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Community;
use PpitCore\Model\Vcard;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Place;
use PpitCore\Model\Document;
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
    public $name;
    public $contact_1_id;
    public $contact_1_status;
    public $contact_2_id;
    public $contact_2_status;
    public $contact_3_id;
    public $contact_3_status;
    public $contact_4_id;
    public $contact_4_status;
    public $contact_5_id;
    public $contact_5_status;
    public $opening_date;
    public $closing_date;
    public $callback_date;
    public $origine;
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
    public $comment_1;
    public $comment_2;
    public $comment_3;
    public $comment_4;
    public $audit;
    public $update_time;
    
    // Joined properties
    public $place_caption;

   	public $customer_community_id; // Deprecated
    
    public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $email;
    public $birth_date;
    public $tel_work;
    public $tel_cell;
    public $adr_street;
    public $adr_extended;
    public $adr_post_office_box;
    public $adr_zip;
    public $adr_city;
    public $adr_state;
    public $adr_country;
    public $photo_link_id;
    
    public $n_title_2;
    public $n_first_2;
    public $n_last_2;
    public $n_fn_2;
    public $email_2;
    public $birth_date_2;
    public $tel_work_2;
    public $tel_cell_2;
    public $adr_street_2;
    public $adr_extended_2;
    public $adr_post_office_box_2;
    public $adr_zip_2;
    public $adr_city_2;
    public $adr_state_2;
    public $adr_country_2;
    
    public $n_title_3;
    public $n_first_3;
    public $n_last_3;
    public $n_fn_3;
    public $email_3;
    public $birth_date_3;
    public $tel_work_3;
    public $tel_cell_3;
    public $adr_street_3;
    public $adr_extended_3;
    public $adr_post_office_box_3;
    public $adr_zip_3;
    public $adr_city_3;
    public $adr_state_3;
    public $adr_country_3;
    
    public $n_title_4;
    public $n_first_4;
    public $n_last_4;
    public $n_fn_4;
    public $email_4;
    public $birth_date_4;
    public $tel_work_4;
    public $tel_cell_4;
    public $adr_street_4;
    public $adr_extended_4;
    public $adr_post_office_box_4;
    public $adr_zip_4;
    public $adr_city_4;
    public $adr_state_4;
    public $adr_country_4;
    
    public $n_title_5;
    public $n_first_5;
    public $n_last_5;
    public $n_fn_5;
    public $email_5;
    public $birth_date_5;
    public $tel_work_5;
    public $tel_cell_5;
    public $adr_street_5;
    public $adr_extended_5;
    public $adr_post_office_box_5;
    public $adr_zip_5;
    public $adr_city_5;
    public $adr_state_5;
    public $adr_country_5;
    
    // Transient properties
    public $place;
    public $contact_1;
    public $contact_2;
    public $contact_3;
    public $contact_4;
    public $contact_5;
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
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->contact_1_id = (isset($data['contact_1_id'])) ? $data['contact_1_id'] : null;
        $this->contact_1_status = (isset($data['contact_1_status'])) ? $data['contact_1_status'] : null;
        $this->contact_2_id = (isset($data['contact_2_id'])) ? $data['contact_2_id'] : null;
        $this->contact_2_status = (isset($data['contact_2_status'])) ? $data['contact_2_status'] : null;
        $this->contact_3_id = (isset($data['contact_3_id'])) ? $data['contact_3_id'] : null;
        $this->contact_3_status = (isset($data['contact_3_status'])) ? $data['contact_3_status'] : null;
        $this->contact_4_id = (isset($data['contact_4_id'])) ? $data['contact_4_id'] : null;
        $this->contact_4_status = (isset($data['contact_4_status'])) ? $data['contact_4_status'] : null;
        $this->contact_5_id = (isset($data['contact_5_id'])) ? $data['contact_5_id'] : null;
        $this->contact_5_status = (isset($data['contact_5_status'])) ? $data['contact_5_status'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date']) && $data['closing_date'] != '9999-12-31') ? $data['closing_date'] : null;
        $this->callback_date = (isset($data['callback_date']) && $data['callback_date'] != '9999-12-31') ? $data['callback_date'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
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
        $this->comment_1 = (isset($data['comment_1'])) ? $data['comment_1'] : null;
        $this->comment_2 = (isset($data['comment_2'])) ? $data['comment_2'] : null;
        $this->comment_3 = (isset($data['comment_3'])) ? $data['comment_3'] : null;
        $this->comment_4 = (isset($data['comment_4'])) ? $data['comment_4'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Joined properties
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        
        $this->customer_community_id = (isset($data['customer_community_id'])) ? $data['customer_community_id'] : null;
        
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->birth_date = (isset($data['birth_date'])) ? $data['birth_date'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->adr_street = (isset($data['adr_street'])) ? $data['adr_street'] : null;
        $this->adr_extended = (isset($data['adr_extended'])) ? $data['adr_extended'] : null;
        $this->adr_post_office_box = (isset($data['adr_post_office_box'])) ? $data['adr_post_office_box'] : null;
        $this->adr_zip = (isset($data['adr_zip'])) ? $data['adr_zip'] : null;
        $this->adr_city = (isset($data['adr_city'])) ? $data['adr_city'] : null;
        $this->adr_state = (isset($data['adr_state'])) ? $data['adr_state'] : null;
        $this->adr_country = (isset($data['adr_country'])) ? $data['adr_country'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
        
        $this->n_title_2 = (isset($data['n_title_2'])) ? $data['n_title_2'] : null;
        $this->n_first_2 = (isset($data['n_first_2'])) ? $data['n_first_2'] : null;
        $this->n_last_2 = (isset($data['n_last_2'])) ? $data['n_last_2'] : null;
        $this->n_fn_2 = (isset($data['n_fn_2'])) ? $data['n_fn_2'] : null;
        $this->email_2 = (isset($data['email_2'])) ? $data['email_2'] : null;
        $this->birth_date_2 = (isset($data['birth_date_2'])) ? $data['birth_date_2'] : null;
        $this->tel_work_2 = (isset($data['tel_work_2'])) ? $data['tel_work_2'] : null;
        $this->tel_cell_2 = (isset($data['tel_cell_2'])) ? $data['tel_cell_2'] : null;
        $this->adr_street_2 = (isset($data['adr_street_2'])) ? $data['adr_street_2'] : null;
        $this->adr_extended_2 = (isset($data['adr_extended_2'])) ? $data['adr_extended_2'] : null;
        $this->adr_post_office_box_2 = (isset($data['adr_post_office_box_2'])) ? $data['adr_post_office_box_2'] : null;
        $this->adr_zip_2 = (isset($data['adr_zip_2'])) ? $data['adr_zip_2'] : null;
        $this->adr_city_2 = (isset($data['adr_city_2'])) ? $data['adr_city_2'] : null;
        $this->adr_state_2 = (isset($data['adr_state_2'])) ? $data['adr_state_2'] : null;
        $this->adr_country_2 = (isset($data['adr_country_2'])) ? $data['adr_country_2'] : null;
        
        $this->n_title_3 = (isset($data['n_title_3'])) ? $data['n_title_3'] : null;
        $this->n_first_3 = (isset($data['n_first_3'])) ? $data['n_first_3'] : null;
        $this->n_last_3 = (isset($data['n_last_3'])) ? $data['n_last_3'] : null;
        $this->n_fn_3 = (isset($data['n_fn_3'])) ? $data['n_fn_3'] : null;
        $this->email_3 = (isset($data['email_3'])) ? $data['email_3'] : null;
        $this->birth_date_3 = (isset($data['birth_date_3'])) ? $data['birth_date_3'] : null;
        $this->tel_work_3 = (isset($data['tel_work_3'])) ? $data['tel_work_3'] : null;
        $this->tel_cell_3 = (isset($data['tel_cell_3'])) ? $data['tel_cell_3'] : null;
        $this->adr_street_3 = (isset($data['adr_street_3'])) ? $data['adr_street_3'] : null;
        $this->adr_extended_3 = (isset($data['adr_extended_3'])) ? $data['adr_extended_3'] : null;
        $this->adr_post_office_box_3 = (isset($data['adr_post_office_box_3'])) ? $data['adr_post_office_box_3'] : null;
        $this->adr_zip_3 = (isset($data['adr_zip_3'])) ? $data['adr_zip_3'] : null;
        $this->adr_city_3 = (isset($data['adr_city_3'])) ? $data['adr_city_3'] : null;
        $this->adr_state_3 = (isset($data['adr_state_3'])) ? $data['adr_state_3'] : null;
        $this->adr_country_3 = (isset($data['adr_country_3'])) ? $data['adr_country_3'] : null;
        
        $this->n_title_4 = (isset($data['n_title_4'])) ? $data['n_title_4'] : null;
        $this->n_first_4 = (isset($data['n_first_4'])) ? $data['n_first_4'] : null;
        $this->n_last_4 = (isset($data['n_last_4'])) ? $data['n_last_4'] : null;
        $this->n_fn_4 = (isset($data['n_fn_4'])) ? $data['n_fn_4'] : null;
        $this->email_4 = (isset($data['email_4'])) ? $data['email_4'] : null;
        $this->birth_date_4 = (isset($data['birth_date_4'])) ? $data['birth_date_4'] : null;
        $this->tel_work_4 = (isset($data['tel_work_4'])) ? $data['tel_work_4'] : null;
        $this->tel_cell_4 = (isset($data['tel_cell_4'])) ? $data['tel_cell_4'] : null;
        $this->adr_street_4 = (isset($data['adr_street_4'])) ? $data['adr_street_4'] : null;
        $this->adr_extended_4 = (isset($data['adr_extended_4'])) ? $data['adr_extended_4'] : null;
        $this->adr_post_office_box_4 = (isset($data['adr_post_office_box_4'])) ? $data['adr_post_office_box_4'] : null;
        $this->adr_zip_4 = (isset($data['adr_zip_4'])) ? $data['adr_zip_4'] : null;
        $this->adr_city_4 = (isset($data['adr_city_4'])) ? $data['adr_city_4'] : null;
        $this->adr_state_4 = (isset($data['adr_state_4'])) ? $data['adr_state_4'] : null;
        $this->adr_country_4 = (isset($data['adr_country_4'])) ? $data['adr_country_4'] : null;
        
        $this->n_title_5 = (isset($data['n_title_5'])) ? $data['n_title_5'] : null;
        $this->n_first_5 = (isset($data['n_first_5'])) ? $data['n_first_5'] : null;
        $this->n_last_5 = (isset($data['n_last_5'])) ? $data['n_last_5'] : null;
        $this->n_fn_5 = (isset($data['n_fn_5'])) ? $data['n_fn_5'] : null;
        $this->email_5 = (isset($data['email_5'])) ? $data['email_5'] : null;
        $this->birth_date_5 = (isset($data['birth_date_5'])) ? $data['birth_date_5'] : null;
        $this->tel_work_5 = (isset($data['tel_work_5'])) ? $data['tel_work_5'] : null;
        $this->tel_cell_5 = (isset($data['tel_cell_5'])) ? $data['tel_cell_5'] : null;
        $this->adr_street_5 = (isset($data['adr_street_5'])) ? $data['adr_street_5'] : null;
        $this->adr_extended_5 = (isset($data['adr_extended_5'])) ? $data['adr_extended_5'] : null;
        $this->adr_post_office_box_5 = (isset($data['adr_post_office_box_5'])) ? $data['adr_post_office_box_5'] : null;
        $this->adr_zip_5 = (isset($data['adr_zip_5'])) ? $data['adr_zip_5'] : null;
        $this->adr_city_5 = (isset($data['adr_city_5'])) ? $data['adr_city_5'] : null;
        $this->adr_state_5 = (isset($data['adr_state_5'])) ? $data['adr_state_5'] : null;
        $this->adr_country_5 = (isset($data['adr_country_5'])) ? $data['adr_country_5'] : null;
    }

    public function getProperties()
    {
    	$data = array();
    	 
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['type'] =  ($this->type) ? $this->type : null;
    	$data['place_id'] = (int) $this->place_id;
    	$data['identifier'] = $this->identifier;
    	$data['name'] = $this->name;
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : null;
    	$data['callback_date'] =  ($this->callback_date) ? $this->callback_date : null;
    	$data['origine'] =  ($this->origine) ? $this->origine : null;
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
    	$data['comment_1'] =  ($this->comment_1) ? $this->comment_1 : null;
    	$data['comment_2'] =  ($this->comment_2) ? $this->comment_2 : null;
    	$data['comment_3'] =  ($this->comment_3) ? $this->comment_3 : null;
    	$data['comment_4'] =  ($this->comment_4) ? $this->comment_4 : null;
    	$data['audit'] = $this->audit;

    	// Joined properties
    	$data['place_caption'] = $this->place_caption;

    	$data['contact_1_id'] = $this->contact_1_id;
    	$data['contact_1_status'] = $this->contact_1_status;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['email'] = $this->email;
    	$data['birth_date'] = $this->birth_date;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['address'] = '';
    	if ($this->adr_street) $data['address'] .= $this->adr_street;
    	if ($this->adr_extended) $data['address'] .= ' '.$this->adr_extended;
    	if ($this->adr_post_office_box) $data['address'] .= ' - '.$this->adr_post_office_box;
    	if ($this->adr_zip) $data['address'] .= ' - '.$this->adr_zip;
    	if ($this->adr_city) $data['address'] .= ' '.$this->adr_city;
    	if ($this->adr_state) $data['address'] .= ' - '.$this->adr_state;
    	if ($this->adr_country) $data['address'] .= ' - '.$this->adr_country;
    	$data['photo_link_id'] = $this->photo_link_id;

    	$data['contact_2_id'] = $this->contact_2_id;
    	$data['contact_2_status'] = $this->contact_2_status;
    	$data['n_title_2'] = $this->n_title_2;
    	$data['n_first_2'] = $this->n_first_2;
    	$data['n_last_2'] = $this->n_last_2;
    	$data['n_fn_2'] = $this->n_fn_2;
    	$data['email_2'] = $this->email_2;
    	$data['birth_date_2'] = $this->birth_date_2;
    	$data['tel_work_2'] = $this->tel_work_2;
    	$data['tel_cell_2'] = $this->tel_cell_2;
    	$data['address_2'] = '';
    	if ($this->adr_street_2) $data['address_2'] .= $this->adr_street_2;
    	if ($this->adr_extended_2) $data['address_2'] .= ' '.$this->adr_extended_2;
    	if ($this->adr_post_office_box_2) $data['address_2'] .= ' - '.$this->adr_post_office_box_2;
    	if ($this->adr_zip_2) $data['address_2'] .= ' - '.$this->adr_zip_2;
    	if ($this->adr_city_2) $data['address_2'] .= ' '.$this->adr_city_2;
    	if ($this->adr_state_2) $data['address_2'] .= ' - '.$this->adr_state_2;
    	if ($this->adr_country_2) $data['address_2'] .= ' - '.$this->adr_country_2;
    	 
    	$data['contact_3_id'] = $this->contact_3_id;
    	$data['contact_3_status'] = $this->contact_3_status;
    	$data['n_title_3'] = $this->n_title_3;
    	$data['n_first_3'] = $this->n_first_3;
    	$data['n_last_3'] = $this->n_last_3;
    	$data['n_fn_3'] = $this->n_fn_3;
    	$data['email_3'] = $this->email_3;
    	$data['birth_date_3'] = $this->birth_date_3;
    	$data['tel_work_3'] = $this->tel_work_3;
    	$data['tel_cell_3'] = $this->tel_cell_3;
    	$data['address_3'] = '';
    	if ($this->adr_street_3) $data['address_3'] .= $this->adr_street_3;
    	if ($this->adr_extended_3) $data['address_3'] .= ' '.$this->adr_extended_3;
    	if ($this->adr_post_office_box_3) $data['address_3'] .= ' - '.$this->adr_post_office_box_3;
    	if ($this->adr_zip_3) $data['address_3'] .= ' - '.$this->adr_zip_3;
    	if ($this->adr_city_3) $data['address_3'] .= ' '.$this->adr_city_3;
    	if ($this->adr_state_3) $data['address_3'] .= ' - '.$this->adr_state_3;
    	if ($this->adr_country_3) $data['address_3'] .= ' - '.$this->adr_country_3;
    	 
    	$data['contact_4_id'] = $this->contact_4_id;
    	$data['contact_4_status'] = $this->contact_4_status;
    	$data['n_title_4'] = $this->n_title_4;
    	$data['n_first_4'] = $this->n_first_4;
    	$data['n_last_4'] = $this->n_last_4;
    	$data['n_fn_4'] = $this->n_fn_4;
    	$data['email_4'] = $this->email_4;
    	$data['birth_date_4'] = $this->birth_date_4;
    	$data['tel_work_4'] = $this->tel_work_4;
    	$data['tel_cell_4'] = $this->tel_cell_4;
    	$data['address_4'] = '';
    	if ($this->adr_street_4) $data['address_4'] .= $this->adr_street_4;
    	if ($this->adr_extended_4) $data['address_4'] .= ' '.$this->adr_extended_4;
    	if ($this->adr_post_office_box_4) $data['address_4'] .= ' - '.$this->adr_post_office_box_4;
    	if ($this->adr_zip_4) $data['address_4'] .= ' - '.$this->adr_zip_4;
    	if ($this->adr_city_4) $data['address_4'] .= ' '.$this->adr_city_4;
    	if ($this->adr_state_4) $data['address_4'] .= ' - '.$this->adr_state_4;
    	if ($this->adr_country_4) $data['address_4'] .= ' - '.$this->adr_country_4;
    	 
    	$data['contact_5_id'] = $this->contact_5_id;
    	$data['contact_5_status'] = $this->contact_5_status;
    	$data['n_title_5'] = $this->n_title_5;
    	$data['n_first_5'] = $this->n_first_5;
    	$data['n_last_5'] = $this->n_last_5;
    	$data['n_fn_5'] = $this->n_fn_5;
    	$data['email_5'] = $this->email_5;
    	$data['birth_date_5'] = $this->birth_date_5;
    	$data['tel_work_5'] = $this->tel_work_5;
    	$data['tel_cell_5'] = $this->tel_cell_5;
    	$data['address_5'] = '';
    	if ($this->adr_street_5) $data['address_5'] .= $this->adr_street_5;
    	if ($this->adr_extended_5) $data['address_5'] .= ' '.$this->adr_extended_5;
    	if ($this->adr_post_office_box_5) $data['address_5'] .= ' - '.$this->adr_post_office_box_5;
    	if ($this->adr_zip_5) $data['address_5'] .= ' - '.$this->adr_zip_5;
    	if ($this->adr_city_5) $data['address_5'] .= ' '.$this->adr_city_5;
    	if ($this->adr_state_5) $data['address_5'] .= ' - '.$this->adr_state_5;
    	if ($this->adr_country_5) $data['address_5'] .= ' - '.$this->adr_country_5;
    	$data['update_time'] = $this->update_time;
    	 
    	return $data;
    }
    
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['callback_date'] =  ($this->callback_date) ? $this->callback_date : '9999-12-31';
    	$data['contact_history'] = json_encode($this->contact_history);
    	$data['terms_of_sales'] =  ($this->terms_of_sales) ? json_encode($this->terms_of_sales) : null;
    	$data['json_property_1'] = json_encode($this->json_property_1);
    	$data['json_property_2'] = json_encode($this->json_property_2);
    	$data['audit'] = json_encode($this->audit);

    	unset($data['place_caption']);

    	unset($data['n_title']);
    	unset($data['n_first']);
    	unset($data['n_last']);
    	unset($data['n_fn']);
    	unset($data['email']);
    	unset($data['birth_date']);
    	unset($data['tel_work']);
    	unset($data['tel_cell']);
    	unset($data['address']);
    	unset($data['photo_link_id']);

    	unset($data['n_title_2']);
    	unset($data['n_first_2']);
    	unset($data['n_last_2']);
    	unset($data['n_fn_2']);
    	unset($data['email_2']);
    	unset($data['birth_date_2']);
    	unset($data['tel_work_2']);
    	unset($data['tel_cell_2']);
    	unset($data['address_2']);
    	 
    	unset($data['n_title_3']);
    	unset($data['n_first_3']);
    	unset($data['n_last_3']);
    	unset($data['n_fn_3']);
    	unset($data['email_3']);
    	unset($data['birth_date_3']);
    	unset($data['tel_work_3']);
    	unset($data['tel_cell_3']);
    	unset($data['address_3']);
    	 
    	unset($data['n_title_4']);
    	unset($data['n_first_4']);
    	unset($data['n_last_4']);
    	unset($data['n_fn_4']);
    	unset($data['email_4']);
    	unset($data['birth_date_4']);
    	unset($data['tel_work_4']);
    	unset($data['tel_cell_4']);
    	unset($data['address_4']);
    	 
    	unset($data['n_title_5']);
    	unset($data['n_first_5']);
    	unset($data['n_last_5']);
    	unset($data['n_fn_5']);
    	unset($data['email_5']);
    	unset($data['birth_date_5']);
    	unset($data['tel_work_5']);
    	unset($data['tel_cell_5']);
    	unset($data['address_5']);
    	 
    	return $data;
    }
    
    public static function getList($type, $entry, $params, $major = 'name', $dir = 'ASC', $mode = 'search', $limitation = 300)
    {
    	$context = Context::getCurrent();
    	$select = Account::getTable()->getSelect()
			->join('core_place', 'commitment_account.place_id = core_place.id', array('place_caption' => 'caption'), 'left')
			->join('core_vcard', 'commitment_account.contact_1_id = core_vcard.id', array('n_title', 'n_first', 'n_last', 'n_fn', 'email', 'birth_date', 'tel_work', 'tel_cell', 'photo_link_id', 'adr_street', 'adr_extended', 'adr_post_office_box', 'adr_zip', 'adr_city', 'adr_state', 'adr_country'), 'left')
			->join(array('contact_2' => 'core_vcard'), 'commitment_account.contact_2_id = contact_2.id', array('n_title_2' =>'n_title', 'n_first_2' => 'n_first', 'n_last_2' => 'n_last', 'n_fn_2' => 'n_fn', 'email_2' => 'email', 'birth_date_2' => 'birth_date', 'tel_work_2' => 'tel_work', 'tel_cell_2' => 'tel_cell', 'adr_street_2' => 'adr_street', 'adr_extended_2' => 'adr_extended', 'adr_post_office_box_2' => 'adr_post_office_box', 'adr_zip_2' => 'adr_zip', 'adr_city_2' => 'adr_city', 'adr_state_2' => 'adr_state', 'adr_country_2' => 'adr_country'), 'left')
			->join(array('contact_3' => 'core_vcard'), 'commitment_account.contact_3_id = contact_3.id', array('n_title_3' =>'n_title', 'n_first_3' => 'n_first', 'n_last_3' => 'n_last', 'n_fn_3' => 'n_fn', 'email_3' => 'email', 'birth_date_3' => 'birth_date', 'tel_work_3' => 'tel_work', 'tel_cell_3' => 'tel_cell', 'adr_street_3' => 'adr_street', 'adr_extended_3' => 'adr_extended', 'adr_post_office_box_3' => 'adr_post_office_box', 'adr_zip_3' => 'adr_zip', 'adr_city_3' => 'adr_city', 'adr_state_3' => 'adr_state', 'adr_country_3' => 'adr_country'), 'left')
			->join(array('contact_4' => 'core_vcard'), 'commitment_account.contact_4_id = contact_4.id', array('n_title_4' =>'n_title', 'n_first_4' => 'n_first', 'n_last_4' => 'n_last', 'n_fn_4' => 'n_fn', 'email_4' => 'email', 'birth_date_4' => 'birth_date', 'tel_work_4' => 'tel_work', 'tel_cell_4' => 'tel_cell', 'adr_street_4' => 'adr_street', 'adr_extended_4' => 'adr_extended', 'adr_post_office_box_4' => 'adr_post_office_box', 'adr_zip_4' => 'adr_zip', 'adr_city_4' => 'adr_city', 'adr_state_4' => 'adr_state', 'adr_country_4' => 'adr_country'), 'left')
			->join(array('contact_5' => 'core_vcard'), 'commitment_account.contact_5_id = contact_5.id', array('n_title_5' =>'n_title', 'n_first_5' => 'n_first', 'n_last_5' => 'n_last', 'n_fn_5' => 'n_fn', 'email_5' => 'email', 'birth_date_5' => 'birth_date', 'tel_work_5' => 'tel_work', 'tel_cell_5' => 'tel_cell', 'adr_street_5' => 'adr_street', 'adr_extended_5' => 'adr_extended', 'adr_post_office_box_5' => 'adr_post_office_box', 'adr_zip_5' => 'adr_zip', 'adr_city_5' => 'adr_city', 'adr_state_5' => 'adr_state', 'adr_country_5' => 'adr_country'), 'left')
			->order(array($major.' '.$dir, 'name'));
			
		$where = new Where;
		if ($type) $where->equalTo('type', $type);
		$where->notEqualTo('commitment_account.status', 'deleted');
		if ($entry == 'contact') $where->notEqualTo('commitment_account.status', 'active');
		else $where->equalTo('commitment_account.status', 'active');
		
    	// Todo list vs search modes
    	if ($mode == 'todo') {
//    		if ($entry == 'contact') $where->lessThanOrEqualTo('commitment_account.callback_date', date('Y-m-d'));
			$where->notEqualTo('commitment_account.status', 'gone');
//			$select->limit(20);
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment_account.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment_account.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (strpos($params[$propertyId], ',')) $where->in('commitment_account.'.$propertyId, array_map('trim', explode(', ', $params[$propertyId])));
    			elseif ($params[$propertyId] == '*') $where->notEqualTo('commitment_account.'.$propertyId, '');
    			else $where->like('commitment_account.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
			if ($limitation) $select->limit($limitation);
    	}
    	$select->where($where);
		$cursor = Account::getTable()->selectWith($select);
		$accounts = array();

		foreach ($cursor as $account) {
			$account->properties = $account->getProperties();

			// Filter on authorized perimeter
			$keep = true;
			if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
				foreach ($context->getPerimeters()['p-pit-admin'] as $key => $values) {
					$keep2 = false;
					foreach ($values as $value) {
						if ($account->properties[$key] == $value) $keep2 = true;
					}
					if (!$keep2) $keep = false;
				}
			}
			if (array_key_exists($type, $context->getPerimeters())) {
				foreach ($context->getPerimeters()[$type] as $key => $values) {
					$keep2 = false;
					foreach ($values as $value) {
						if (!array_key_exists($key, $account->properties)) $keep2 = true;
						elseif ($account->properties[$key] == $value) $keep2 = true;
					}
					if (!$keep2) $keep = false;
				}
			}
			if ($keep) $accounts[] = $account;
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
    	if ($account->contact_1_id) {
	    	$account->contact_1 = Vcard::get($account->contact_1_id);
		    	
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

    	$account->n_title = $account->contact_1->n_title;
	    $account->n_first = $account->contact_1->n_first;
    	$account->n_last = $account->contact_1->n_last;
    	$account->email = $account->contact_1->email;
    	$account->birth_date = $account->contact_1->birth_date;
    	$account->tel_work = $account->contact_1->tel_work;
    	$account->tel_cell = $account->contact_1->tel_cell;
    	$account->is_notified = $account->contact_1->is_notified;
    	$account->locale = $account->contact_1->locale;
	    	
	    if ($account->contact_2_id) {
	        $account->contact_2 = Vcard::get($account->contact_2_id);
	    	$account->n_title_2 = $account->contact_2->n_title;
		    $account->n_first_2 = $account->contact_2->n_first;
	    	$account->n_last_2 = $account->contact_2->n_last;
	    	$account->email_2 = $account->contact_2->email;
	    	$account->birth_date_2 = $account->contact_2->birth_date;
	    	$account->tel_work_2 = $account->contact_2->tel_work;
	    	$account->tel_cell_2 = $account->contact_2->tel_cell;
	    }
	    if ($account->contact_3_id) {
	        $account->contact_3 = Vcard::get($account->contact_3_id);
	    	$account->n_title_3 = $account->contact_3->n_title;
		    $account->n_first_3 = $account->contact_3->n_first;
	    	$account->n_last_3 = $account->contact_3->n_last;
	    	$account->email_3 = $account->contact_3->email;
	    	$account->birth_date_3 = $account->contact_3->birth_date;
	    	$account->tel_work_3 = $account->contact_3->tel_work;
	    	$account->tel_cell_3 = $account->contact_3->tel_cell;
	    }
	    if ($account->contact_4_id) {
	        $account->contact_4 = Vcard::get($account->contact_4_id);
	    	$account->n_title_4 = $account->contact_4->n_title;
		    $account->n_first_4 = $account->contact_4->n_first;
	    	$account->n_last_4 = $account->contact_4->n_last;
	    	$account->email_4 = $account->contact_4->email;
	    	$account->birth_date_4 = $account->contact_4->birth_date;
	    	$account->tel_work_4 = $account->contact_4->tel_work;
	    	$account->tel_cell_4 = $account->contact_4->tel_cell;
	    }
	    if ($account->contact_5_id) {
	        $account->contact_5 = Vcard::get($account->contact_5_id);
	    	$account->n_title_5 = $account->contact_5->n_title;
		    $account->n_first_5 = $account->contact_5->n_first;
	    	$account->n_last_5 = $account->contact_5->n_last;
	    	$account->email_5 = $account->contact_5->email;
	    	$account->birth_date_5 = $account->contact_5->birth_date;
	    	$account->tel_work_5 = $account->contact_5->tel_work;
	    	$account->tel_cell_5 = $account->contact_5->tel_cell;
	    }

    	return $account;
    }
   
    public static function instanciate($type = null)
    {
		$account = new Account;
		$account->status = 'new';
		$account->type = $type;
		$account->contact_history = array();
		$account->audit = array();
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
    		if (array_key_exists('name', $data)) {
		    	$this->name = trim(strip_tags($data['name']));
		    	if (!$this->name || strlen($this->name) > 255) return 'Integrity';
			}
    		if (array_key_exists('contact_1_id', $data)) $this->contact_1_id = (int) $data['contact_1_id'];
    		if (array_key_exists('contact_1_status', $data)) {
		    	$this->contact_1_status = trim(strip_tags($data['contact_1_status']));
		    	if (strlen($this->contact_1_status) > 255) return 'Integrity';
			}
        	if (array_key_exists('contact_2_id', $data)) $this->contact_2_id = (int) $data['contact_2_id'];
    		if (array_key_exists('contact_2_status', $data)) {
		    	$this->contact_2_status = trim(strip_tags($data['contact_2_status']));
		    	if (strlen($this->contact_2_status) > 255) return 'Integrity';
			}
        	if (array_key_exists('contact_3_id', $data)) $this->contact_3_id = (int) $data['contact_3_id'];
    		if (array_key_exists('contact_3_status', $data)) {
		    	$this->contact_3_status = trim(strip_tags($data['contact_3_status']));
		    	if (strlen($this->contact_3_status) > 255) return 'Integrity';
			}
        	if (array_key_exists('contact_4_id', $data)) $this->contact_4_id = (int) $data['contact_4_id'];
    		if (array_key_exists('contact_4_status', $data)) {
		    	$this->contact_4_status = trim(strip_tags($data['contact_4_status']));
		    	if (strlen($this->contact_4_status) > 255) return 'Integrity';
			}
        	if (array_key_exists('contact_5_id', $data)) $this->contact_5_id = (int) $data['contact_5_id'];
    		if (array_key_exists('contact_5_status', $data)) {
		    	$this->contact_5_status = trim(strip_tags($data['contact_5_status']));
		    	if (strlen($this->contact_5_status) > 255) return 'Integrity';
			}
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
			if (array_key_exists('opening_date', $data)) {
		    	$this->opening_date = trim(strip_tags($data['opening_date']));
		    	if (!$this->opening_date || !checkdate(substr($this->opening_date, 5, 2), substr($this->opening_date, 8, 2), substr($this->opening_date, 0, 4))) return 'Integrity';
			}
			if (array_key_exists('closing_date', $data)) {
		    	$this->closing_date = trim(strip_tags($data['closing_date']));
		    	if ($this->closing_date && !checkdate(substr($this->closing_date, 5, 2), substr($this->closing_date, 8, 2), substr($this->closing_date, 0, 4))) return 'Integrity';
			}
    		if (array_key_exists('callback_date', $data)) {
		    	$this->callback_date = trim(strip_tags($data['callback_date']));
		    	if ($this->callback_date && !checkdate(substr($this->callback_date, 5, 2), substr($this->callback_date, 8, 2), substr($this->callback_date, 0, 4))) return 'Integrity';
			}
    		if (array_key_exists('origine', $data)) {
				$this->origine = trim(strip_tags($data['origine']));
				if (strlen($this->origine) > 255) return 'Integrity';
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
    		if (array_key_exists('comment_1', $data)) {
				$this->comment_1 = trim(strip_tags($data['comment_1']));
				if (strlen($this->comment_1) > 65535) return 'Integrity';
			}
        	if (array_key_exists('comment_2', $data)) {
				$this->comment_2 = trim(strip_tags($data['comment_2']));
				if (strlen($this->comment_2) > 65535) return 'Integrity';
			}
        	if (array_key_exists('comment_3', $data)) {
				$this->comment_3 = trim(strip_tags($data['comment_3']));
				if (strlen($this->comment_3) > 65535) return 'Integrity';
			}
        	if (array_key_exists('comment_4', $data)) {
				$this->comment_4 = trim(strip_tags($data['comment_4']));
				if (strlen($this->comment_4) > 65535) return 'Integrity';
			}
			if (array_key_exists('is_notified', $data)) {
				$this->is_notified = $data['is_notified'];
			}
            if (array_key_exists('locale', $data)) {
				$this->locale = $data['locale'];
			}
			if (array_key_exists('update_time', $data)) $this->update_time = $data['update_time'];
				
			if (!$this->name) $this->name = $this->n_last.', '.$this->n_first;
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
    	if ($update_time && $account->update_time > $update_time) return 'Isolation';
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

    /**
     * @param Interaction $interaction
     * @return string
     */
    public static function processInteraction($data, $interaction)
    {
    	$rc = 'OK';
    	$context = Context::getCurrent();
    	if ($interaction->format == 'text/csv') {
    		$account = Account::instanciate();
			$account->contact_1 = Vcard::instanciate();
			$targetData = array();
    		$targetData['type'] = $interaction->category;
			$targetData['status'] = 'new';
    		$targetData['callback_date'] = date('Y-m-d');
    		$targetData['origine'] = 'file';
    		foreach ($data as $column => $value) {
    			if (array_key_exists($column, $context->getConfig('interaction/csv/contact')['columns'])) {
    				$targetData[$context->getConfig('interaction/csv/contact')['columns'][$column]['property']] = $value;
    			}
    		}
			$account->contact_1->loadData($targetData);
			$account->loadData($targetData);
			$place = Place::get($targetData['place_identifier'], 'identifier');
			if ($place) $account->place_id = $place->id;
    		$account->contact_1 = Vcard::optimize($account->contact_1);
    		$account->contact_1_id = $account->contact_1->id;
    		$account->contact_1_status = 'main';
			$account->id = null;
    		$rc = $account->add();
    		if ($rc != 'OK') return $rc;
    	}
    	return $rc;
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
    	$account = Account::getTable()->get($this->id);
    
    	// Isolation check
    	if ($update_time && $account->update_time > $update_time) return 'Isolation';
    	$user = User::get($this->contact_1->id, 'vcard_id');
    	if ($user) $user->delete($user->update_time);
    	 
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