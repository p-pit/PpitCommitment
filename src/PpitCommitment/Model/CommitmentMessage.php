<?php
namespace PpitCommitment\Model;

use PpitCommitment\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitCore\Model\Document;
use PpitCommitment\Model\XmlCommitmentResponse;
use SplFileObject;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CommitmentMessage implements InputFilterAwareInterface
{
	public $id;
    public $instance_id;
    public $account_id;
    public $identifier;
    public $direction;
    public $format;
    public $type;
    public $content;
    public $http_status;
    public $update_time;

    // Joined properties
    public $account_name;

    // Transient properties
    public $files;
    
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
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->direction = (isset($data['direction'])) ? $data['direction'] : null;
        $this->format = (isset($data['format'])) ? $data['format'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->http_status = (isset($data['http_status'])) ? $data['http_status'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->account_name = (isset($data['account_name'])) ? $data['account_name'] : null;
    }

    public function toArray() {

    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['account_id'] = (int) $this->account_id;
    	$data['identifier'] = $this->identifier;
    	$data['direction'] = $this->direction;
    	$data['format'] = $this->format;
    	$data['type'] = $this->type;
    	$data['content'] = $this->content;
    	$data['http_status'] = $this->http_status;
    	 
    	return $data;
	}

	public static function getList($major, $dir, $filter = array())
	{
		$select = CommitmentMessage::getTable()->getSelect()->order(array($major.' '.$dir, 'update_time DESC'));
		$where = new Where;
		foreach ($filter as $property => $value) {
			$where->like($property, '%'.$value.'%');
		}
		$select->where($where);
		$cursor = CommitmentMessage::getTable()->selectWith($select);
		$messages = array();
		foreach ($cursor as $message) $messages[] = $message;
		return $messages;
	}

	public static function get($id)
	{
		$context = Context::getCurrent();
		$message = CommitmentMessage::getTable()->get($id);

		// Retrieve the account
		if ($message->account_id) {
			$account = Account::getTable()->get($message->account_id);
			$message->account_name = $account->name;
		}

		return $message;
	}

	public static function instanciate($type = null, $content = null)
	{
		$context = Context::getCurrent();
		$message = new CommitmentMessage;
		$message->type = $type;
		$message->content = $content;
		return $message;
	}

	public function loadData($data, $files)
	{
		$this->type = trim(strip_tags($data['type']));
		if (strlen($this->type) > 255) return 'Integrity';
		$this->files = $files;
		return 'OK';
	}

	public function add()
	{
		$context = Context::getCurrent();
		$this->id = null;

	    if ($this->files) {
			$root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$document_id = $document->save();
    		$this->import($document_id);
    	}
		CommitmentMessage::getTable()->save($this);
    	return ('OK');
	}

	public function import($document_id)
	{
		$context = Context::getCurrent();
		$maxRows = $context->getConfig('commitmentMessage')['importMaxRows'];
		$filePath = 'data/documents/'.$document_id;
		
		ini_set('auto_detect_line_endings', true);
		$file = new SplFileObject($filePath, 'r');
		$file->setFlags(SplFileObject::READ_CSV);
		$file->setCsvControl(';');
		$rows = array(); $first = TRUE;
		foreach($file as $row) {
			if ($first && count($row) > 0) {
				$properties = array();
				foreach ($row as $cell) {
					$cell = utf8_encode($cell);
					if (!array_key_exists($cell, $context->getConfig('commitment')['properties'])) $properties[$cell] = null;
					else $properties[$cell] = $context->getConfig('commitment')['properties'][$cell];
				}
			}
			else {
				$content = false;
				foreach ($row as $cell) if ($cell) $content = true;
				if ($content) $rows[] = $row;
			}
			$first = FALSE;
		}
		
		// Number of rows limitation
		$nbRows = count($rows);
		if ($nbRows > $maxRows) {
			$errors[] = array('line' => NULL, 'column' => NULL, 'type' => 'nb_rows', 'caption' => 'A maximum of '.$maxRows.' lines can be imported at a time');
			$this->http_status = 'Integrity errors';
		}
		else {
			for ($i = 0; $i < count($rows); $i++) {
				$row = $rows[$i];
					
				// Number of columns validation
				if (count($properties) != count($row)) $this->http_status = 'Integrity';
			}
			if (!$this->http_status) {
				$this->http_status = 'Loaded';
	
				$content = array();
				foreach ($rows as $row) {
					$contentRow = array();
					$i = 0;
					foreach ($properties as $propertyId => $property) {
						$value = $row[$i];
						$value = utf8_encode($value);
						$lineBreak = strpos($value, "\n");
						if ($lineBreak) $value = (substr($value, 0, $lineBreak));
						if ($property) {
							if ($property['type'] == 'date') $value = $context->encodeDate($value);
							elseif ($property['type'] == 'number') {
								$dotPos = strrpos($value, '.');
								$commaPos = strrpos($value, ',');
								$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
								((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
								if (!$sep) $value =  floatval(preg_replace("/[^0-9]/", "", $value));
								else {
									$value = floatval(
											preg_replace("/[^0-9]/", "", substr($value, 0, $sep)) . '.' .
											preg_replace("/[^0-9]/", "", substr($value, $sep+1, strlen($value)))
									);
								}
							}
						}
						$contentRow[$propertyId] = $value;
						$i++;
					}
					$content[] = $contentRow;
				}
				$this->content = json_encode($content, JSON_UNESCAPED_UNICODE);
			}
		}
	}
	
	public static function sendPpitSubscriptionMessage($request)
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$safe = $context->getConfig()['ppitUserSettings']['safe'];
		$username = null;
		$password = null;

		// Check basic authentication
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
				list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		}
		if (!array_key_exists($username, $safe['p-pit']) || $password != $safe['p-pit'][$username]) {
			 
			// Write to the log
			if ($context->getConfig()['isTraceActive']) {
				$writer = new \Zend\Log\Writer\Stream('data/log/ppit-subscription-message.txt');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info('401;'.$username.';'.$password);
			}
			return '401';
		}

		// Retrieve the XML content and save the message
		$content = json_decode($request->getContent(), true);
		$rc = '200';
		if (!$content) $rc = '400';
		else {
			if (!array_key_exists('message_identifier', $content)) $rc = '400';
			if (!array_key_exists('issue_date', $content)) $rc = '400';
			if (!array_key_exists('order_number', $content)) $rc = '400';
			if (!array_key_exists('buyer_party', $content)) $rc = '400';
			if (!array_key_exists('seller_party', $content)) $rc = '400';
			if (!array_key_exists('product_identifier', $content)) $rc = '400';
			if (!array_key_exists('quantity', $content)) $rc = '400';
		}

		if ($rc != '200') {
			// Write to the log
			if ($context->getConfig()['isTraceActive']) {
				$writer = new \Zend\Log\Writer\Stream('data/log/ppit-subscription-message.txt');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($rc);
			}
			return $rc;
		}
		$message = CommitmentMessage::instanciate('P-PIT', json_encode($content));
		$message->direction = 'I';
		$message->format = 'JSON';
		$message->identifier = $content['message_identifier'];
		$commitment = Commitment::instanciateFromJson('rental', $content, true);
		$commitment->caption = 'P-PIT ('.$content['quantity'].' x 0,5 €)';
		$commitment->amount = $content['quantity'] * 0.5;

		// Bad request
		if (!$commitment) {

			// Write to the log
			if ($context->getConfig()['isTraceActive']) {
				$writer = new \Zend\Log\Writer\Stream('data/log/ppit-subscription-message.txt');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info('400');
			}
			return '400';
		}

		$message->add();
		$commitment->commitment_message_id = $message->id;
		$rc = $commitment->add();
		$message->http_status = $rc;
		$message->update(null);
		
		if ($rc != 'OK') {

			// Write to the log
			if ($context->getConfig()['isTraceActive']) {
				$writer = new \Zend\Log\Writer\Stream('data/log/ppit-subscription-message.txt');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info('422;'.$commitment->identifier);
			}
			return '422';
		}
	
		// Write to the log
		if ($context->getConfig()['isTraceActive']) {
			$writer = new \Zend\Log\Writer\Stream('data/log/ppit-subscription-message.txt');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info('200');
		}
		return '200';
	}

	public function processInvoice($request)
	{
		$context = Context::getCurrent();
		$config = $context->getConfig();
		
		$resultMessage = array();
		foreach (json_decode($xmlMessage->content, true) as $row) {

			// Retrieve the order
			$select = Order::getTable()->getSelect();
			$where = new Where;
			$importType = $context->getConfig('commitment')['importTypes'][$this->type];
			$i = 0;
			$properties = array();
			foreach ($importType['description'] as $property) {
				$properties[$property['name']] = $row[$i];
				if (array_key_exists('key', $property)) $where->equalTo($property['key'], $row[$i]);
				$i++;
			}
			$select->where($where);
			$cursor = Order::getTable()->selectWith($select);
			if (count($cursor) > 0) {

				$order = $cursor->current();

				// Check integrity
				$error = false;

				if ($error) $row[] = 'KO';
				else {
					if ($order->status == 'commissioned') {
						$row[] = 'INVOICE';

						// Atomically save
						$connection = Order::getTable()->getAdapter()->getDriver()->getConnection();
						$connection->beginTransaction();
						try {
	
							$order->invoice($properties, $request);
							
							// Update the order
					    	$order->status = 'invoiced';
					    	$order->audit[] = array(
									'status' => $order->status,
									'time' => Date('Y-m-d G:i:s'),
									'n_fn' => $context->getFormatedName(),
									'comment' => '',
					    	);
					    	$return = $order->update($order->update_time);
							if ($return != 'OK') {
								$connection->rollback();
								$error = $return;						
							}
					   		else {
						    	$connection->commit();
						    	$message = 'OK';
					   		}
						}
						catch (\Exception $e) {
							$connection->rollback();
							throw $e;
						}
					}
				}
			}
			$resultMessage[] = $row;
		}
		$xmlMessage->content = json_encode($resultMessage);
	}

	public function update($update_time)
	{
		$context = Context::getCurrent();
		$message = CommitmentMessage::get($this->id);
		
		// Isolation check
		if ($update_time && $message->update_time > $update_time) return 'Isolation';

		CommitmentMessage::getTable()->save($this);
		
		return 'OK';
	}

	public function isDeletable()
	{
		$context = Context::getCurrent();
	
		// Check dependencies
		$config = $context->getConfig();
		foreach($config['ppitOrderDependencies'] as $dependency) {
			if ($dependency->isUsed($this)) return false;
		}
		
		return true;
	}
	
	public function delete($update_time)
	{
		$context = Context::getCurrent();
		$message = CommitmentMessage::get($this->id);
		
		// Isolation check
		if ($message->update_time > $update_time) return 'Isolation';
		
		CommitmentMessage::getTable()->delete($this->id);
		
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
    	if (!CommitmentMessage::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		CommitmentMessage::$table = $sm->get('PpitCommitment\Model\CommitmentMessageTable');
    	}
    	return CommitmentMessage::$table;
    }
}
