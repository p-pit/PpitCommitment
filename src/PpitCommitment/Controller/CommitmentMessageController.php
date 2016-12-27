<?php
namespace PpitCommitment\Controller;

use PpitCommitment\Model\Account;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\ViewHelper\PdfInvoiceViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitDocument\Model\Document;
use PpitCore\Model\Community;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitDocument\Model\DocumentPart;
use Zend\Http\Client;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class CommitmentMessageController extends AbstractActionController
{
	public function indexAction()
    {
    	$context = Context::getCurrent();
//		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$type = $this->params()->fromRoute('type', null);
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		
    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'types' => $types,
    			'type' => $type,
    	));
    }
	
	public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Prepare the SQL request
    	$major = $this->params()->fromQuery('major', 'update_time');
    	$dir = $this->params()->fromQuery('dir', 'DESC');
    	$messages = CommitmentMessage::getList($major, $dir);

    	// Return the link list
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'major' => $major,
    		'dir' => $dir,
    		'messages' => $messages,
        ));
		$view->setTerminal(true);
		return $view;
    }

    public function accountPostAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Initialize the logger
    	$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);
    
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$username = null;
    	$password = null;
    
    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    
    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!array_key_exists($username, $safe['p-pit']) || $password != $safe['p-pit'][$username]) {
    		 
    		// Write to the log
    		$logger->info('account-post/'.$instance_caption.';401;'.$username.';'.$password);
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {
    		// Log the received message
    		$message = CommitmentMessage::instanciate('account', json_encode(array()));
    		$message->direction = 'I';
    		$message->format = 'application/json';
    		$message->identifier = $instance_caption;
    		$message->content = $this->getRequest()->getContent();
	    	$message->http_status = 'OK';
    		$message->add();

    		// Atomically save
    		$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
	    		
	    		// Create the community and the account
	    		$community = Community::instanciate();
	    		$community->status = 'new';
	    		$community->name = $instance_caption;
	    		$rc = $community->add();

	    		if ($rc != 'OK') {
	    			$connection->rollback();

	    			// Update the message with any return code from the account insert or update
	    			$message->http_status = $rc;
	    			$message->update($message->update_time);
	    			
	    			// Write to the log
	    			$logger->info('accountPost/'.$instance_caption.';422;'.$rc.';');
	    			$this->getResponse()->setStatusCode('422');
	    			return $this->getResponse();
	    		}

	    		$account = Account::instanciate();
	    		$account->status = 'new';
	    		$account->customer_community_id = $community->id;
	    		$rc = $account->add();
	    			
	    		if ($rc != 'OK') {
	    			$connection->rollback();

	    			// Update the message with any return code from the account insert or update
	    			$message->http_status = $rc;
	    			$message->update($message->update_time);
	    			
	    			// Write to the log
	    			$logger->info('accountPost/'.$instance_caption.';422;'.$rc.';');
	    			$this->getResponse()->setStatusCode('422');
	    			return $this->getResponse();
	    		}

	    		$connection->commit();
	    		
	    		// Write to the log
	    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
	    			$logger->info('accountPost/'.$instance_caption.';200;');
	    		}
	    		$this->getResponse()->setStatusCode('200');
	    		return $this->getResponse();
    		}
    	    catch (\Exception $e) {
    			$connection->rollback();
	    			
    			// Write to the log
    			$logger->info('accountPost/'.$instance_caption.';500;'.$e->getMessage().';');
    			$this->getResponse()->setStatusCode('500');
    			return $this->getResponse();
    	    }
    	}
    }

    public function commitmentListAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the account id
    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
//    	if (!$account_id) return $this->redirect()->toRoute('home');
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$username = null;
    	$password = null;
    	 
    	$this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

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
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    			$logger = new \Zend\Log\Logger();
    			$logger->addWriter($writer);
    			$logger->info('ppit-get-list;401;'.$username.';'.$password);
    		}
    		$this->getResponse()->setStatusCode('401');
    	}
    	else {
    		$community = Community::get($instance_caption, 'name');
    		if (!$community) {
    			$this->getResponse()->setContent(json_encode(array()));
    		}
    		else {
    			$account = Account::get($community->id, 'customer_community_id');
    			if (!$account) $this->getResponse()->setStatusCode('400');
    			else $this->getResponse()->setContent(json_encode(Commitment::getList(null, array('account_id' => $account->id), 'caption', 'ASC', 'search')));
    		}
    	}
    	return $this->getResponse();
    }
    
    public function commitmentGetAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$username = null;
    	$password = null;

    	$this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

    	$id = $this->params()->fromRoute('id', null);
    	if (!$id) return $this->redirect()->toRoute('home');
    	
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
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    			$logger = new \Zend\Log\Logger();
    			$logger->addWriter($writer);
    			$logger->info('ppit-get;401;'.$username.';'.$password);
    		}
    		$this->getResponse()->setStatusCode('401');
    	}
    	else {
    		$commitment = Commitment::getArray($id);
    		$this->getResponse()->setContent(json_encode($commitment));
    	}
    	return $this->getResponse();
    }

    public function commitmentPostAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Initialize the logger
    	$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);

    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$username = null;
    	$password = null;
    
    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    	$id = $this->params()->fromRoute('id', null);

    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!array_key_exists($username, $safe['p-pit']) || $password != $safe['p-pit'][$username]) {
    		 
    		// Write to the log
    		$logger->info('commitment-post/'.$id.';401;'.$username.';'.$password);
    		$this->getResponse()->setStatusCode('401');
	    	return $this->getResponse();
    	}
    	else {
    		// Log the received message
			$message = CommitmentMessage::instanciate('commitment', json_encode(array()));
			$message->direction = 'I';
			$message->format = 'application/json';
			$message->identifier = $id;
			$message->content = $this->getRequest()->getContent();
			$message->add();

			// Atomically save
			$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {

				// Retrieve the community and account and create if not exist
				$community = Community::get($instance_caption, 'name');
				if ($community) $account = Account::get($community->id, 'customer_community_id');
				else {
					$community = Community::instanciate();
		    		$community->status = 'new';
		    		$community->name = $instance_caption;
		    		$rc = $community->add();
	
		    		if ($rc != 'OK') {
		    			$connection->rollback();
	
		    			// Update the message with any return code from the account insert or update
		    			$message->http_status = $rc;
		    			$message->update($message->update_time);
		    			
		    			// Write to the log
		    			$logger->info('commitmentPost/'.$instance_caption.'/'.$id.';422;'.$rc.';');
		    			$this->getResponse()->setStatusCode('422');
		    			return $this->getResponse();
		    		}
	
		    		$account = Account::instanciate();
		    		$account->status = 'new';
		    		$account->customer_community_id = $community->id;
		    		$rc = $account->add();
		    			
		    		if ($rc != 'OK') {
		    			$connection->rollback();
	
		    			// Update the message with any return code from the account insert or update
		    			$message->http_status = $rc;
		    			$message->update($message->update_time);
		    			
		    			// Write to the log
		    			$logger->info('commitmentPost/'.$instance_caption.'/'.$id.';422;'.$rc.';');
		    			$this->getResponse()->setStatusCode('422');
		    			return $this->getResponse();
		    		}
				}
				
				// Create or update the commitment
				if ($id) $commitment = Commitment::get($id);
				else {
					$commitment = Commitment::instanciate();
					$commitment->type = 'rental';
					$commitment->account_id = $account->id;
				}
				$data = json_decode($this->getRequest()->getContent(), true);
				$commitment->loadData($data);
				if ($commitment->status == 'new') $commitment->commitment_message_id = $message->id;
				elseif ($commitment->status == 'approved') $commitment->confirmation_message_id = $message->id;
				$rc = ($commitment->id) ? $commitment->update(null) : $commitment->add();
				
				if ($rc != 'OK') {

					// Update the message with any return code from the commitment insert or update
					$message->http_status = $rc;
					$message->update($message->update_time);

					// Write to the log
					$logger->info('commitmentPost/'.$instance_caption.'/'.$id.';422;'.$rc.';');
			    	$this->getResponse()->setStatusCode('422');
			    	return $this->getResponse();
				}

				// Update the message with any return code from the commitment insert or update
				$message->http_status = 'OK';
				$message->update($message->update_time);

				$connection->commit();
				
				// Write to the log
				if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
					$logger->info('commitmentPost/'.$id.';200;');
				}
		    	$this->getResponse()->setStatusCode('200');
		    	return $this->getResponse();
	    	}
    	   	catch (\Exception $e) {
    			$connection->rollback();
	    			
    			// Write to the log
    			$logger->info('accountPost/'.$instance_caption.';500;'.$e->getMessage().';');
    			$this->getResponse()->setStatusCode('500');
    			return $this->getResponse();
    	    }
    	}
    }

    public function paymentAutoresponseAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    		$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    		$logger = new \Zend\Log\Logger();
    		$logger->addWriter($writer);
    	}
    	 
    	// Retrieve the commitment
    	$id = $this->params()->fromRoute('id', null);
    	$commitment = Commitment::get($id);
    	if (!$commitment) {
    		// Write to the log
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$logger->info('payment-autoresponse;422');
    		}
    		$this->getResponse()->setStatusCode('422');
    		return $this->getResponse();
    	}

    	// Récupération de la variable cryptée DATA
    	$message="message=$_POST[DATA]";

    	// Initialisation du chemin du fichier pathfile
    	$pathfile='pathfile='.$context->getConfig('ppit-payment')['pathfile'];
    	 
    	// Initialisation du chemin de l'exécutable response
    	$path_bin = $context->getConfig('ppit-payment')['path_bin'].'response';
    	
    	// Appel du binaire response
    	$message = escapeshellcmd($message);
    	$result=exec("$path_bin $pathfile $message");

    	//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
    	//		- code=0	: la fonction retourne les données de la transaction dans les variables v1, v2, ...
    	//					: Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
    	//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error
    	
    	//	on sépare les différents champs et on les met dans une variable tableau    	
    	$tableau = explode ("!", $result);
    	
    	//	Récupération des données de la réponse
    	$code = $tableau[1];
    	$error = $tableau[2];
      	
    	//  analyse du code retour
    	if (( $code == "" ) && ( $error == "" ) )
    	{
	    	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$logger->info("payment-autoresponse;;executable response non trouve $path_bin");
	    	}
    	}

    	//	Erreur, affiche le message d'erreur
    	else if ( $code != 0 ) {
    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
    			$logger->info("payment-autoresponse/$id;$code;$error");
	    	}
    	}
    	
    	// OK
    	else {
			$content = array(
	    		'merchant_id' => $tableau[3],
    			'merchant_country' => $tableau[4],
    			'amount' => $tableau[5],
    			'transaction_id' => $tableau[6],
    			'payment_means' => $tableau[7],
    			'transmission_date' => $tableau[8],
    			'payment_time' => $tableau[9],
    			'payment_date' => $tableau[10],
    			'response_code' => $tableau[11],
    			'payment_certificate' => $tableau[12],
    			'authorisation_id' => $tableau[13],
    			'currency_code' => $tableau[14],
    			'card_number' => $tableau[15],
    			'cvv_flag' => $tableau[16],
    			'cvv_response_code' => $tableau[17],
    			'bank_response_code' => $tableau[18],
    			'complementary_code' => $tableau[19],
    			'complementary_info' => $tableau[20],
    			'return_context' => $tableau[21],
    			'caddie' => $tableau[22],
    			'receipt_complement' => $tableau[23],
    			'merchant_language' => $tableau[24],
    			'language' => $tableau[25],
    			'customer_id' => $tableau[26],
    			'order_id' => $tableau[27],
    			'customer_email' => $tableau[28],
	    		'customer_ip_address' => $tableau[29],
    			'capture_day' => $tableau[30],
    			'capture_mode' => $tableau[31],
    			'data' => $tableau[32],
    			'order_validity' => $tableau[33],
    			'transaction_condition' => $tableau[34],
    			'statement_reference' => $tableau[35],
    			'card_validity' => $tableau[36],
    			'score_value' => $tableau[37],
    			'score_color' => $tableau[38],
    			'score_info' => $tableau[39],
    			'score_threshold' => $tableau[40],
    			'score_profile' => $tableau[41],
    			'threed_ls_code' => $tableau[43],
	    		'threed_relegation_code' => $tableau[44],
			);

	    	$message = CommitmentMessage::instanciate('payment-autoresponse', json_encode($content));
	    	$message->direction = 'I';
	    	$message->format = 'JSON';
	    	$message->identifier = $id;
	    	$message->http_status = 'HTTP/1.1 200 OK';
	    	$message->add();

	    	if ($tableau[11] == '00') {
		    	$commitment->status = 'commissioned';
		    	$commitment->commissioning_date = date('Y-m-d');
	    		$commitment->invoice_date = date('Y-m-d');
	    		$commitment->settlement_date = date('Y-m-d');
		    	$commitment->settlement_message_id = $message->id;
		    	$commitment->notification_time = null;
	    		$commitment->update($commitment->update_time);
	    	}

	    	// Write to the log
	    	if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
	    		$logger->info('payment-autoresponse;'.$tableau[11].';'.$id);
	    	}
	    	$this->getResponse()->setStatusCode('200');
	    	return $this->getResponse();
    	}
	}

	public function invoiceGetAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$safe = $context->getConfig()['ppitUserSettings']['safe'];
		$username = null;
		$password = null;
		
		$this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');

		$id = $this->params()->fromRoute('id');
    	$commitment = Commitment::get($id);
		$proforma = $this->params()->fromQuery('proforma', null);

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
			if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
				$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info('ppit-get;401;'.$username.';'.$password);
			}
			$this->getResponse()->setStatusCode('401');
		}
		else {
		
			// create new PDF document
			$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
			PdfInvoiceViewHelper::render($pdf, $commitment, $proforma);
			 
			// Close and output PDF document
			// This method has several options, check the source code documentation for more information.
/*			$document = Document::instanciate(0);
			$document->type = 'application/pdf';
			$document->add();
	    	$handle = fopen('data/documents/'.$document->id.'.pdf', 'I');*/
	    	$content = $pdf->Output('invoice-'.$context->getInstance()->caption.'-'.$commitment->invoice_identifier.'.pdf', 'I');
			$this->getResponse()->setContent(json_encode($commitment));
    	}
    	return $this->getResponse();
	}

    public function ppitSubscribeAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

		// Atomically save
    	$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    	$connection->beginTransaction();
    	try {
    
    		$rc = CommitmentMessage::sendPpitSubscriptionMessage($this->getRequest());

    		if ($rc != '200' && $rc != '422') $connection->rollback();
    		else $connection->commit();
    	}
    	catch (\Exception $e) {
    		$connection->rollback();
    		throw $e;
    	}
    
    	$this->getResponse()->setStatusCode($rc);
    	return $this->getResponse();
    }

    public function importAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$xmlMessage = CommitmentMessage::instanciate();
    	
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    	
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    	
    		if ($csrfForm->isValid()) { // CSRF check
    	
    			$xmlMessage->loadDataFromRequest($request);
    	
    			// Atomically save
    			 
    			$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {

    				// Import the file
    				$xmlMessage->add();

    				$connection->commit();
    				$message = 'OK';
    			}
    			catch (Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'xmlMessage' => $xmlMessage,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function addPhotographAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$folder = '';
    	
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		 
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    			$folder = $request->getPost('folder');
		    	$files = $request->getFiles()->toArray();
		    	reset($files);
		    	$file = current($files);
				$name = $file['name'];
				$destinationPath = time();
				$adapter = new \Zend\File\Transfer\Adapter\Http();
				$adapter->addFilter('Rename', 'data/documents/'.$destinationPath);
				if ($adapter->receive($file['name'])) {
					try {
				    	require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
						$dropbox = $context->getConfig('ppitDocument')['dropbox'];
						$dbxClient = new \Dropbox\Client($dropbox['credential'], $dropbox['clientIdentifier']);
						$f = fopen('data/documents/'.$destinationPath, "rb");
						$result = $dbxClient->uploadFile('/'.$dropbox['folders'][$folder].'/'.$name, \Dropbox\WriteMode::add(), $f);
						fclose($f);
						$message = 'OK';
					}
					catch (\Exception $e) {
						$error = 'Consistency';
					}
				}
				else $error = 'Consistency';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'folder' => $folder,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function processAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    
    	$id = (int) $this->params()->fromRoute('message_id', 0);
    	if (!$id) return $this->redirect()->toUrl('index');
    	$xmlMessage = Message::get($id);
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
    
    			if ($context->getConfig('commitment')['importTypes'][$xmlMessage->type] == 'invoice') {
    				$rc = $xmlMessage->processInvoice($request);
    				if ($rc != 'OK') $error = $rc;
    			}
    			else {
    					
    				$resultMessage = array();
    				foreach (json_decode($xmlMessage->content, true) as $row) {
    
    					// Retrieve the order
    					$sales_order_number = $row[SALES_ORDER_NUMBER];
    					$order = Order::get($sales_order_number, 'property_9');
    					if ($order) {
    
    						// Check integrity
    						$error = false;
    							
    						$shipment_date = $row[SHIPMENT_DATE];
    						if ($shipment_date) {
    							if (!checkdate(substr($shipment_date, 3, 2), substr($shipment_date, 0, 2), substr($shipment_date, 6, 4))) $error = 'Consistency';
    							else $shipment_date = substr($shipment_date, 6, 4).'-'.substr($shipment_date, 3, 2).'-'.substr($shipment_date, 0, 2);
    						}
    						if ($shipment_date > date('Y-m-d')) $error = 'Consistency';
    
    						$delivery_date = $row[DELIVERY_DATE];
    						if ($delivery_date) {
    							if (!checkdate(substr($delivery_date, 3, 2), substr($delivery_date, 0, 2), substr($delivery_date, 6, 4))) $error = 'Consistency';
    							else $delivery_date = substr($delivery_date, 6, 4).'-'.substr($delivery_date, 3, 2).'-'.substr($delivery_date, 0, 2);
    						}
    						if ($delivery_date > date('Y-m-d')) $error = 'Consistency';
    							
    						$serial_number = $row[SERIAL_NUMBER];
    
    						$commissioning_date = $row[COMMISSIONING_DATE];
    						if ($commissioning_date) {
    							if (!checkdate(substr($commissioning_date, 3, 2), substr($commissioning_date, 0, 2), substr($commissioning_date, 6, 4))) $error = 'Consistency';
    							else $commissioning_date = substr($commissioning_date, 6, 4).'-'.substr($commissioning_date, 3, 2).'-'.substr($commissioning_date, 0, 2);
    						}
    
    						if ($error) $row[] = 'KO';
    						else {
    							// Shipment case
    							if ($order->status == 'registered' && $shipment_date) {
    								$row[] = 'ASNLIV';
    
    								// Atomically save
    								$connection = OrderProduct::getTable()->getAdapter()->getDriver()->getConnection();
    								$connection->beginTransaction();
    								try {
    										
    									$select = OrderProduct::getTable()->getSelect()->where(array('order_id' => $order->id));
    									$cursor = OrderProduct::getTable()->selectWith($select);
    									foreach ($cursor as $orderProduct) {
    										$orderProduct->order = $order;
    										$orderProduct->shipment_date = $shipment_date;
    										if ($serial_number) {
    											if (!$orderProduct->equipment_identifier) $orderProduct->equipment_identifier = $serial_number;
    											elseif ($serial_number != $orderProduct->equipment_identifier && !$orderProduct->changed_equipment_identifier) $orderProduct->changed_equipment_identifier = $serial_number;
    										}
    										$orderProduct->ship('ship', $request);
    									}
    									// Update the order
    									$order->status = 'shipped';
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
    										$return = $orderProduct->update();
    										if ($return != 'OK') {
    											$connection->rollback();
    											$error = $return;
    										}
    										else {
    											$connection->commit();
    											$message = 'OK';
    										}
    									}
    								}
    								catch (\Exception $e) {
    									$connection->rollback();
    									throw $e;
    								}
    							}
    
    							// Delivery case
    							else if ($order->status == 'shipped' && $delivery_date && $serial_number) {
    								$row[] = 'ASNLIV1';
    
    								// Atomically save
    								$connection = OrderProduct::getTable()->getAdapter()->getDriver()->getConnection();
    								$connection->beginTransaction();
    								try {
    
    									$select = OrderProduct::getTable()->getSelect()->where(array('order_id' => $order->id));
    									$cursor = OrderProduct::getTable()->selectWith($select);
    									foreach ($cursor as $orderProduct) {
    										$orderProduct->order = $order;
    										$orderProduct->delivery_date = $delivery_date;
    										if ($serial_number) {
    											if (!$orderProduct->equipment_identifier) $orderProduct->equipment_identifier = $serial_number;
    											elseif ($serial_number != $orderProduct->equipment_identifier && !$orderProduct->changed_equipment_identifier) $orderProduct->changed_equipment_identifier = $serial_number;
    										}
    										$orderProduct->ship('deliver', $request);
    									}
    									// Update the order
    									$order->status = 'delivered';
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
    										$return = $orderProduct->update();
    										if ($return != 'OK') {
    											$connection->rollback();
    											$error = $return;
    										}
    										else {
    											$connection->commit();
    											$message = 'OK';
    										}
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
    			$xmlMessage->http_status = 'Processed';
    			$xmlMessage->update($xmlMessage->update_time);
    			if (!$error) $message = 'OK';
    		}
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $config,
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function submitAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the message
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$xmlMessage = CommitmentMessage::getTable()->get($id);

		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {

			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());

			if ($csrfForm->isValid()) { // CSRF check

//				$message->loadDataFromRequest($request);

				// Atomically save
		        	
				$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
		    	$connection->beginTransaction();
				try {
					// Submit the response message
					$safe = $context->getConfig()['ppitUserSettings']['safe'];
					if ($xmlMessage->type == 'ORDRSP') $url = $context->getConfig()['ppitOrderSettings']['responseMessageUrl'];
					elseif ($xmlMessage->type == 'ASNLIV' || $xmlMessage->type == 'ASNLIV1' || $xmlMessage->type == 'ASNLIV2') $url = $context->getConfig()['ppitOrderSettings']['shipmentMessageUrl'];

					$client = new Client(
							$url,
							array(
									'adapter' => 'Zend\Http\Client\Adapter\Curl',
									'maxredirects' => 0,
									'timeout'      => 30,
							)
					);

					$client->setAuth('XEROX', $safe['ugap']['XEROX'], Client::AUTH_BASIC);
					$client->setEncType('text/xml');
					$client->setMethod('POST');
					$client->setRawBody($xmlMessage->content);
					$response = $client->send();
		    		$xmlMessage->http_status = $response->renderStatusLine();

		    		// Write to the log
		    		if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
		    			$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
		    			$logger = new \Zend\Log\Logger();
		    			$logger->addWriter($writer);
		    			$logger->info('confirm;'.$xmlMessage->identifier.';'.$response->renderStatusLine());
		    		}
		    		$rc = $xmlMessage->update($request->getPost('update_time'));
		    		if ($rc != 'OK') {
		    			$error = $rc;
		    			$connection->rollback();
		    		}
		    		else {
				        $connection->commit();
				    	$message = 'OK';
		    		}
				}
		    	catch (Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
    		}
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function downloadAction()
    {
    	// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

       	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the XML message
    	$xmlMessage = CommitmentMessage::getTable()->get($id);
    
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function deleteAction()
    {
    	// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

       	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the XML message
    	$xmlMessage = CommitmentMessage::getTable()->get($id);

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    	
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    	
    		if ($csrfForm->isValid()) { // CSRF check

				// Atomically delete the message
		        try {
			    	$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
		    		$connection->beginTransaction();
					$xmlMessage->delete();
			        $connection->commit();
			        $message = 'OK';
				}
		    	catch (Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
    		}
    	}
    
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'xmlMessage' => $xmlMessage,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
		$view->setTerminal(true);
		return $view;
    }
}
