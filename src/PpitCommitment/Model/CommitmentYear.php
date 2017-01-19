<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Context;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CommitmentYear implements InputFilterAwareInterface
{
    public $id;
    public $status;
    public $year;
    public $digits;
    public $next_value;
    
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
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->digits = (isset($data['digits'])) ? $data['digits'] : null;
        $this->next_value = (isset($data['next_value'])) ? $data['next_value'] : null;
    }

    public function toArray() {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['year'] = $this->year;
    	$data['digits'] = $this->digits;
    	$data['next_value'] = (int) $this->next_value;
    	return $data;
    }
   
    public static function getCurrent()
    {
    	$year = CommitmentYear::getTable()->get('current', 'status');
    	return $year;
    }

    public static function getNext($current)
    {
    	$year = CommitmentYear::getTable()->get($current->year + 1);
    	return $year;
    }
    
    public static function instanciate($year)
    {
    	$year = new CommitmentYear;
    	$year->status = 'current';
    	$year->year = $year;
    	$year->digits = 5;
    	$year->next_value = 1;
    	CommitmentYear::getTable()->save($year);
    	return $year;
    }

    public function increment()
    {
    	$this->next_value++;
    	CommitmentYear::getTable()->save($this);
    	return $this->next_value;
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
    	if (!CommitmentYear::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		CommitmentYear::$table = $sm->get('PpitCommitment\Model\CommitmentYearTable');
    	}
    	return CommitmentYear::$table;
    }
}