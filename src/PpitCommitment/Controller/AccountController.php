<?php

namespace PpitCommitment\Controller;

use PpitContact\Model\ContactMessage;
use PpitCommitment\ViewHelper\PdfIndexCardViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCommitment\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Instance;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * 
 * Deprecated. For compatibility reasons with Shin Agency
 *
 */
class AccountController extends AbstractActionController
{
    public function postAction()
    {
    	$context = Context::getCurrent();
    	$writer = new Writer\Stream('data/log/account_get.txt');
    	$logger = new Logger();
    	$logger->addWriter($writer);

    	if (!$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		$logger->info('account/postAction;'.date('Y-m-d H:i:s').';401;');
    		return $this->getResponse();
    	}
    	else {
			$data = json_decode($this->request->getContent(), true);
    		
    		// Log the web-service as an incoming interaction
			$interaction = Interaction::instanciate();
			$reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
			$intData = array();
			$intData['type'] = 'web_service';
			$intData['category'] = (is_array($data) && array_key_exists('type', $data)) ? $data['type'] : 'unknown';
			$intData['format'] = $this->getRequest()->getHeaders()->get('content-type')->getFieldValue();
			$intData['direction'] = 'input';
			$intData['route'] = 'account/processPost';
			$intData['reference'] = $reference;
			$intData['content'] = $this->request->getContent();
			$rc = $interaction->loadData($intData);

			// Load the data from the post request
			if (!is_array($data) || !array_key_exists('type', $data) || !array_key_exists('place_identifier', $data) || !array_key_exists('email', $data)) {
				$this->getResponse()->setStatusCode('400');
				$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;'.$this->request->getContent().';');
				echo 'Mandatory data is not provided';
				$interaction->http_status = '400 - Mandatory data is not provided';
				$rc = $interaction->add();
				return $this->getResponse();
			}
    	   	if (!in_array($data['type'], array('p-pit-studies', 'business'))) {
    			$this->getResponse()->setStatusCode('400');
    			$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;Unknown type '.$data['type'].';');
    			echo 'Unknown type '.$data['type'];
				$interaction->http_status = '400 - Unknown type '.$data['type'];
    			$rc = $interaction->add();
    			return $this->getResponse();
    		}
    		$place = Place::get($data['place_identifier'], 'identifier');
    	   	if (!$place /* || !$context->hasAccessTo('place', $place) */) {
    			$this->getResponse()->setStatusCode('400');
    			$logger->info('account/postAction;'.date('Y-m-d H:i:s').';400;The place identified by '.$data['place_identifier'].' does not exist;');
    			echo 'The place identified by '.$data['place_identifier'].' does not exist';
				$interaction->http_status = '400 - The place identified by '.$data['place_identifier'].' does not exist';
    			$rc = $interaction->add();
    			return $this->getResponse();
    		}
			$interaction->http_status = '200';
			$rc = $interaction->add();
			$rc = Account::processPost($data, $interaction);
	   		$this->getResponse()->setStatusCode($interaction->http_status);
   			$this->response->setContent(json_encode(array('interaction_id' => $interaction->id, 'rc' => $rc)));
   			return $this->getResponse();
	    }
    }
}
