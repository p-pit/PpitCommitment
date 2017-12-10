<?php
namespace PpitCommitment;

use PpitCore\Model\GenericTable;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentYear;
use PpitCommitment\Model\Event;
use PpitCommitment\Model\Notification;
use PpitCommitment\Model\Subscription;
use PpitCommitment\Model\Term;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\EventManager\EventInterface;
use Zend\Validator\AbstractValidator;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            	'PpitCommitment\Model\CommitmentTable' =>  function($sm) {
                	$tableGateway = $sm->get('CommitmentTableGateway');
                	$table = new GenericTable($tableGateway);
                	return $table;
                },
                'CommitmentTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Commitment());
                	return new TableGateway('commitment', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\CommitmentMessageTable' =>  function($sm) {
                	$tableGateway = $sm->get('CommitmentMessageTableGateway');
                	$table = new GenericTable($tableGateway);
                	return $table;
                },
                'CommitmentMessageTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new CommitmentMessage());
                	return new TableGateway('commitment_message', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\CommitmentYearTable' =>  function($sm) {
                	$tableGateway = $sm->get('CommitmentYearTableGateway');
                	$table = new GenericTable($tableGateway);
                	return $table;
                },
                'CommitmentYearTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new CommitmentYear());
                	return new TableGateway('commitment_year', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\EventTable' =>  function($sm) {
                	$tableGateway = $sm->get('EventTableGateway');
                	$table = new GenericTable($tableGateway);
                	return $table;
                },
                'EventTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Event());
                	return new TableGateway('commitment_event', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\NotificationTable' =>  function($sm) {
                	$tableGateway = $sm->get('NotificationTableGateway');
                	$table = new GenericTable($tableGateway);
                	return $table;
                },
                'NotificationTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Notification());
                	return new TableGateway('commitment_notification', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\SubscriptionTable' =>  function($sm) {
                    $tableGateway = $sm->get('SubscriptionTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'SubscriptionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Subscription());
                    return new TableGateway('commitment_subscription', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCommitment\Model\TermTable' =>  function($sm) {
                    $tableGateway = $sm->get('TermTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'TermTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Term());
                    return new TableGateway('commitment_term', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
