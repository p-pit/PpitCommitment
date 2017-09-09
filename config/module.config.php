<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitCommitment\Controller\Account' => 'PpitCommitment\Controller\AccountController',
        	'PpitCommitment\Controller\Commitment' => 'PpitCommitment\Controller\CommitmentController',
        	'PpitCommitment\Controller\CommitmentCredit' => 'PpitCommitment\Controller\CommitmentCreditController',
        	'PpitCommitment\Controller\CommitmentMessage' => 'PpitCommitment\Controller\CommitmentMessageController',
        	'PpitCommitment\Controller\OrderResponse' => 'PpitCommitment\Controller\OrderResponseController',
        	'PpitCommitment\Controller\OrderProduct' => 'PpitCommitment\Controller\OrderProductController',
            'PpitCommitment\Controller\Term' => 'PpitCommitment\Controller\TermController',
        ),
    ),
		
	'console' => array(
		'router' => array(
			'routes' => array(
                'notify' => array(
                    'options' => array(
                        'route'    => 'order notify',
                        'defaults' => array(
                            'controller' => 'PpitCommitment\Controller\Commitment',
                            'action'     => 'notify'
                        )
                    )
                )
			)
		)
	),

    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                ),
          		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       		),
            ),
        	'commitmentAccount' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/commitment-account',
                    'defaults' => array(
                        'controller' => 'PpitCommitment\Controller\Account',
                        'action'     => 'list',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index[/:entry][/:type]',
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'contactIndex' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/contact-index[/:entry][/:type]',
        										'defaults' => array(
        												'action' => 'contactIndex',
        										),
        								),
        						),
	       						'search' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/search[/:entry][/:type]',
        										'defaults' => array(
        												'action' => 'search',
        										),
        								),
        						),
        						'list' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/list[/:entry][/:type]',
        										'defaults' => array(
        												'action' => 'list',
        										),
        								),
        						),
        						'export' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/export[/:entry][/:type]',
        										'defaults' => array(
        												'action' => 'export',
        										),
        								),
        						),
	       						'group' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/group[:type]',
        										'defaults' => array(
        												'action' => 'group',
        										),
        								),
        						),
	       						'sendMessage' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/send-message[:type]',
        										'defaults' => array(
        												'action' => 'sendMessage',
        										),
        								),
        						),
        						'dropboxLink' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/dropbox-link[/:document]',
        										'defaults' => array(
        												'action' => 'dropboxLink',
        										),
        								),
        						),
	       						'detail' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/detail[/:type][/:id]',
        										'constraints' => array(
        												'id' => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'detail',
        										),
        								),
        						),
	       						'get' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/get[/:email]',
        										'defaults' => array(
        												'action' => 'get',
        										),
        								),
        						),
	       						'post' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/post[/:type]',
        										'defaults' => array(
        												'action' => 'post',
        										),
        								),
        						),
	       						'processPost' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/process-post[/:interaction_id]',
        										'defaults' => array(
        												'action' => 'processPost',
        										),
        								),
        						),
	       						'update' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/update[/:id][/:type]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'update',
		        								),
		        						),
		        				),
	       						'updateUser' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/update-user[/:type][/:id][/:act]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'update-user',
		        								),
		        						),
		        				),
	       						'updateContact' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/update-contact[/:type][/:contactNumber][/:id][/:act]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'updateContact',
		        								),
		        						),
		        				),
			       				'register' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/register[/:type]',
		        								'defaults' => array(
		        										'action' => 'register',
		        								),
		        						),
		        				),
        				'rephase' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/rephase',
        								'defaults' => array(
        										'action' => 'rephase',
        								),
        						),
        				),
	       		),
	       	),
        	'commitment' => array(
        		'type'    => 'segment',
        			'options' => array(
        				'route'    => '/commitment',
        				'defaults' => array(
        						'controller' => 'PpitCommitment\Controller\Commitment',
        						'action'     => 'index',
        				),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        				'index' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/index[/:type]',
        								'defaults' => array(
        										'action' => 'index',
        								),
        						),
        				),
        				'search' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/search[/:type]',
        								'defaults' => array(
        										'action' => 'search',
        								),
        						),
        				),
        				'list' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/list[/:type]',
        								'defaults' => array(
        										'action' => 'list',
        								),
        						),
        				),
        				'accountList' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/account-list[/:type]',
        								'defaults' => array(
        										'action' => 'accountList',
        								),
        						),
        				),
        				'export' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/export[/:type]',
        								'defaults' => array(
        										'action' => 'export',
        								),
        						),
        				),
        				'post' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/post',
        								'defaults' => array(
        										'action' => 'post',
        								),
        						),
        				),
        				'detail' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/detail[/:type][/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'detail',
        								),
        						),
        				),
        				'update' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/update[/:type][/:id][/:act]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'update',
        								),
        						),
        				),
        				'invoice' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/invoice[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'invoice',
        								),
        						),
        				),
        				'xmlUblInvoice' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/xml-ubl-invoice[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'xmlUblInvoice',
        								),
        						),
        				),
        				'settle' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/settle[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'settle',
        								),
        						),
        				),
        				'updateProduct' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/update-product[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'updateProduct',
        								),
        						),
        				),
        				'updateOption' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/update-option[/:id][/:number]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        										'number'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'updateOption',
        								),
        						),
        				),
        				'updateTerm' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/update-term[/:id][/:number]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        										'number'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'updateTerm',
        								),
        						),
        				),
        				'suspend' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/suspend[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'suspend',
        								),
        						),
        				),
        				'serviceAdd' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/service-add',
        								'defaults' => array(
        										'action' => 'serviceAdd',
        								),
        						),
        				),
/*        				'workflow' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/workflow[/:type][/:id][/:act]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'workflow',
        								),
        						),
        				),*/
        				'try' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/try[/:product]',
        								'defaults' => array(
        										'action' => 'try',
        								),
        						),
        				),
        				'serviceSettle' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/service-settle[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'serviceSettle',
        								),
        						),
        				),
        				'downloadInvoice' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/download-invoice[/:type][/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'downloadInvoice',
        								),
        						),
        				),
        				'paymentResponse' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/payment-response[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'paymentResponse',
        								),
        						),
        				),
        				'delete' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/delete[/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'delete',
        								),
        						),
        				),
        				'notify' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/notify',
        								'defaults' => array(
        										'action' => 'notify',
        								),
        						),
        				),
        				'rephase' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/rephase',
        								'defaults' => array(
        										'action' => 'rephase',
        								),
        						),
        				),
        		),
        	),
        	'commitmentCredit' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/commitment-credit',
                    'defaults' => array(
                        'controller' => 'PpitCommitment\Controller\CommitmentCredit',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index',
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'search' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/search',
        										'defaults' => array(
        												'action' => 'search',
        										),
        								),
        						),
        						'list' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/list',
        										'defaults' => array(
        												'action' => 'list',
        										),
        								),
        						),
        						'export' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/export',
        										'defaults' => array(
        												'action' => 'export',
        										),
        								),
        						),
	       						'detail' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/detail[/:id]',
        										'constraints' => array(
        												'id' => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'detail',
        										),
        								),
        						),
		        				'accept' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/accept[/:id]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'accept',
		        								),
		        						),
		        				),
		        				'settle' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/settle[/:id]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'settle',
		        								),
		        						),
		        				),
	       						'downloadInvoice' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/download-invoice[/:id]',
        										'constraints' => array(
        												'id' => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'downloadInvoice',
        										),
        								),
        						),
	       		),
            ),
        	'commitmentMessage' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/commitment-message',
                    'defaults' => array(
                        'controller' => 'PpitCommitment\Controller\CommitmentMessage',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'index' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/index',
            								'defaults' => array(
            										'action' => 'index',
            								),
            						),
            				),
            				'search' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/search',
            								'defaults' => array(
            										'action' => 'search',
            								),
            						),
            				),
            				'download' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/download[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'download',
            								),
            						),
            				),
	        				'accountPost' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/account-post[/:instance_caption]',
	        								'defaults' => array(
	        										'action' => 'account-post',
	        								),
	        						),
	        				),
            				'commitmentList' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/commitment-list[/:instance_caption]',
	        								'defaults' => array(
	        										'action' => 'commitmentList',
	        								),
	        						),
	        				),
	        				'commitmentGet' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/commitment-get[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
	        								'defaults' => array(
	        										'action' => 'commitmentGet',
	        								),
	        						),
	        				),
	        				'commitmentPost' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/commitment-post[/:instance_caption][/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
	        								'defaults' => array(
	        										'action' => 'commitment-post',
	        								),
	        						),
	        				),
	        				'xmlXcblOrder' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/xml-xcbl-order',
	        								'defaults' => array(
	        										'action' => 'xmlXcblOrder',
	        								),
	        						),
	        				),
            				'paymentAutoresponse' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/payment-autoresponse[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
	        								'defaults' => array(
	        										'action' => 'paymentAutoresponse',
	        								),
	        						),
	        				),
	        				'invoiceGet' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/invoice-get[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
	        								'defaults' => array(
	        										'action' => 'invoiceGet',
	        								),
	        						),
	        				),
            				'ppitSubscribe' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/ppit-subscribe',
	        								'defaults' => array(
	        										'action' => 'ppitSubscribe',
	        								),
	        						),
	        				),
            				'addPhotograph' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add-photograph',
            								'defaults' => array(
            										'action' => 'addPhotograph',
            								),
            						),
            				),
            				'import' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/import[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'import',
            								),
            						),
            				),
            				'process' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/process[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'process',
            								),
            						),
            				),
            				'submit' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/submit[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'submit',
            								),
            						),
            				),
            		),
        	),
        	'commitmentTerm' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/commitment-term',
                    'defaults' => array(
                        'controller' => 'PpitCommitment\Controller\Term',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index[/:type]',
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'search' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/search',
        										'defaults' => array(
        												'action' => 'search',
        										),
        								),
        						),
        						'list' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/list',
        										'defaults' => array(
        												'action' => 'list',
        										),
        								),
        						),
        						'export' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/export',
        										'defaults' => array(
        												'action' => 'export',
        										),
        								),
        						),
	       						'detail' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/detail[/:id]',
        										'constraints' => array(
        												'id' => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'detail',
        										),
        								),
        						),
		        				'update' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/update[/:commitment_id][/:id][/:act]',
		        								'constraints' => array(
		        										'commitment_id'     => '[0-9]*',
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'update',
		        								),
		        						),
		        				),
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       		),
			),
        ),
    ),

    'bjyauthorize' => array(
        // default role for unauthenticated users
        'default_role' => 'guest',
        
        // identity provider service name
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        
        // Role providers to be used to load all available roles into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'role_providers' => array(
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table' => 'user_role',
                'role_id_field' => 'role_id',
                'parent_role_field' => 'parent'
            )
        ),
    		
        // Guard listeners to be attached to the application event manager
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(

            	// Orders
				array('route' => 'commitmentAccount', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/index', 'roles' => array('sales_manager', 'manager')),
				array('route' => 'commitmentAccount/contactIndex', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/search', 'roles' => array('sales_manager', 'manager')),
				array('route' => 'commitmentAccount/group', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/sendMessage', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/dropboxLink', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/detail', 'roles' => array('sales_manager', 'manager')),
            	array('route' => 'commitmentAccount/get', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/post', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/processPost', 'roles' => array('admin', 'ws-incoming')),
//            	array('route' => 'commitmentAccount/delete', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/export', 'roles' => array('sales_manager', 'manager')),
            	array('route' => 'commitmentAccount/list', 'roles' => array('sales_manager', 'manager')),
				array('route' => 'commitmentAccount/update', 'roles' => array('sales_manager', 'manager')),
            	array('route' => 'commitmentAccount/updateUser', 'roles' => array('sales_manager', 'manager')),
            	array('route' => 'commitmentAccount/updateContact', 'roles' => array('sales_manager', 'manager')),
            	array('route' => 'commitmentAccount/register', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/rephase', 'roles' => array('admin')),
            		
            	array('route' => 'commitment', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/accountlist', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/index', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/search', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/list', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/accountList', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/export', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/detail', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/message', 'roles' => array('guest')),
            	array('route' => 'commitment/post', 'roles' => array('admin')),
            	array('route' => 'commitment/try', 'roles' => array('guest')),
            	array('route' => 'commitment/invoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/xmlUblInvoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/settle', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/update', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateProduct', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateOption', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateTerm', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/suspend', 'roles' => array('admin')),
            	array('route' => 'commitment/serviceAdd', 'roles' => array('guest')),
//            	array('route' => 'commitment/workflow', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/serviceSettle', 'roles' => array('accountant')),
            	array('route' => 'commitment/downloadInvoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/paymentResponse', 'roles' => array('accountant')),
            	array('route' => 'commitment/delete', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/notify', 'roles' => array('admin')),
            	array('route' => 'commitment/rephase', 'roles' => array('admin')),

            	array('route' => 'commitmentCredit', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/index', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/search', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/list', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/export', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/detail', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/accept', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/settle', 'roles' => array('admin')),
            	array('route' => 'commitmentCredit/downloadInvoice', 'roles' => array('admin')),
	
            	array('route' => 'commitmentMessage/download', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/index', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/search', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/accountPost', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentList', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentGet', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentPost', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/xmlXcblOrder', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/paymentAutoresponse', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/invoiceGet', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/ppitSubscribe', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/addPhotograph', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/import', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/process', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/submit', 'roles' => array('admin')),
				array('route' => 'commitmentTerm', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/index', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/search', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/detail', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/delete', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/export', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/list', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/update', 'roles' => array('sales_manager')),
            )
        )
    ),

    'view_manager' => array(
    	'strategies' => array(
    			'ViewJsonStrategy',
    	),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'PpitCommitment' => __DIR__ . '/../view',
        ),
    ),
/*	'service_manager' => array(
		'factories' => array(
				'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
		),
	),*/
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-commitment'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),

	'ppitApplications' => array(
    		'p-pit-engagements' => array(
    				'labels' => array('fr_FR' => 'P-Pit Engagements', 'en_US' => 'Commitments by 2Pit'),
    				'route' => 'commitmentAccount/index',
    				'params' => array(),
					'roles' => array(
							'sales_manager' => array(
									'show' => true,
									'labels' => array(
											'en_US' => 'Sales manager',
											'fr_FR' => 'Gestion commerciale',
									),
							),
					),
			),
	),

	'menus/p-pit-engagements' => array(
					'contact' => array(
							'route' => 'commitmentAccount/contactIndex',
							'params' => array('entry' => 'contact'),
							'glyphicon' => 'glyphicon-user',
							'label' => array(
									'en_US' => 'Contacts',
									'fr_FR' => 'Contacts',
							),
					),
					'account' => array(
							'route' => 'commitmentAccount/index',
							'params' => array('entry' => 'account'),
							'glyphicon' => 'glyphicon-user',
							'label' => array(
									'en_US' => 'Accounts',
									'fr_FR' => 'Comptes',
							),
					),
					'commitment' => array(
							'route' => 'commitment/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-link',
							'label' => array(
									'en_US' => 'Commitments',
									'fr_FR' => 'Engagements',
							),
					),
					'term' => array(
							'route' => 'commitmentTerm/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-calendar',
							'label' => array(
									'en_US' => 'Terms',
									'fr_FR' => 'Echéances',
							),
					),
					'product' => array(
							'route' => 'product/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-book',
							'label' => array(
									'en_US' => 'Catalogue',
									'fr_FR' => 'Catalogue',
							),
					),
					'interaction' => array(
							'route' => 'commitmentMessage/index',
							'params' => array(),
							'urlParams' => array(),
							'glyphicon' => 'glyphicon-transfer',
							'label' => array(
									'en_US' => 'Interactions',
									'fr_FR' => 'Interactions',
							),
					),
	),

	'creditConsumers' => array(
			'\PpitCommitment\Model\Commitment::consumeCredits',
	),
		
	'perimeters' => array(
			'p-pit-engagements' => array(
			),
	),
	
	'currentApplication' => 'ppitCommitment',

	'ppitCoreDependencies' => array(
			'commitment_account' => new \PpitCommitment\Model\Account,
	),
		
	'ppitCommitmentDependencies' => array(
	),

	'commitmentAccount/property/origine' => array(
			'type' => 'select',
			'modalities' => array(
					'web' => array('en_US' => 'Web site', 'fr_FR' => 'Site web'),
					'show' => array('en_US' => 'Show', 'fr_FR' => 'Salon'),
					'incoming' => array('en_US' => 'Incoming call', 'fr_FR' => 'Appel entrant'),
					'outcoming' => array('en_US' => 'Outcoming call', 'fr_FR' => 'Appel sortant'),
					'file' => array('en_US' => 'File', 'fr_FR' => 'Fichier'),
					'agency' => array('en_US' => 'Agency', 'fr_FR' => 'Agence'),
			),
			'labels' => array(
					'en_US' => 'Origine',
					'fr_FR' => 'Origine',
			),
	),
		
	'commitmentAccount' => array(
			'statuses' => array(),
			'properties' => array(
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'prospect' => array('en_US' => 'Prospect', 'fr_FR' => 'Prospect'),
									'active' => array('en_US' => 'Customer', 'fr_FR' => 'Client'),
									'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'identifier' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Identifier',
									'fr_FR' => 'Identifiant',
							),
					),
					'name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Dénomination',
							),
					),
					'n_title' => array(
							'type' => 'select',
							'modalities' => array(
									'Mr' => array('fr_FR' => 'M.', 'en_US' => 'Mr'),
									'Mrs' => array('fr_FR' => 'Mme', 'en_US' => 'Mrs'),
									'Ms' => array('fr_FR' => 'Melle', 'en_US' => 'Ms'),
							),
							'labels' => array(
									'en_US' => 'Title',
									'fr_FR' => 'Titre',
							),
					),
					'n_first' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Contact - First name',
									'fr_FR' => 'Contact - Prénom',
							),
					),
					'n_last' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Contact - Last name',
									'fr_FR' => 'Contact - Nom',
							),
					),
					'email' => array(
							'type' => 'email',
							'labels' => array(
									'en_US' => 'Contact - Email',
									'fr_FR' => 'Contact - Email',
							),
					),
					'tel_work' => array(
							'type' => 'phone',
							'labels' => array(
									'en_US' => 'Contact - Phone',
									'fr_FR' => 'Contact - Téléphone',
							),
					),
					'tel_cell' => array(
							'type' => 'phone',
							'labels' => array(
									'en_US' => 'Contact - Cellular',
									'fr_FR' => 'Contact - Mobile',
							),
					),
					'place_id' => array(
							'type' => 'select',
							'modalities' => array(
									'2pit' => array('fr_FR' => 'P-PIT', 'en_US' => '2PIT'),
							),
							'labels' => array(
									'en_US' => 'Center',
									'fr_FR' => 'Centre',
							),
					),
					'opening_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Opening date',
									'fr_FR' => 'Date d\'ouverture',
							),
					),
					'closing_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Closing date',
									'fr_FR' => 'Date de fermeture',
							),
					),
					'callback_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Callback date',
									'fr_FR' => 'Date de rappel',
							),
					),
					'origine' => array(
							'type' => 'repository',
							'definition' => 'commitmentAccount/property/origine',
					),
					'contact_history' => array(
							'type' => 'log',
							'labels' => array(
									'en_US' => 'Comment',
									'fr_FR' => 'Commentaire',
							),
					),
			),
			'order' => 'name',
	),

	// Account
	'commitmentAccount/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitmentAccount/search' => array(
			'title' => array('en_US' => 'Accounts', 'fr_FR' => 'Comptes'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'main' => array(
					'status' => 'select',
				'name' => 'contains',
				'callback_date' => 'range',
				'origine' => 'contains',
				'email' => 'contains',
			),
			'more' => array(
			),
	),
	'commitmentAccount/list' => array(
			'name' => 'text',
	),
	'commitmentAccount/detail' => array(
			'title' => array('en_US' => 'Account detail', 'fr_FR' => 'Détail du compte'),
			'displayAudit' => true,
			'tabs' => array(
					'contact_1' => array(
							'route' => 'commitmentAccount/update',
							'params' => array('type' => ''),
							'labels' => array('en_US' => 'Main contact', 'fr_FR' => 'Contact principal'),
					),
					'contact_2' => array(
							'route' => 'commitmentAccount/updateContact',
							'params' => array('type' => '', 'contactNumber' => 2),
							'labels' => array('en_US' => 'Invoicing', 'fr_FR' => 'Facturation'),
					),
			),
	),
	'commitmentAccount/update' => array(
			'status' => array('mandatory' => true),
			'identifier' => array('mandatory' => false),
			'name' => array('mandatory' => true),
			'callback_date' => array('mandatory' => false),
			'origine' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'place_id' => array('mandatory' => true),
			'contact_history' => array('mandatory' => false),
	),
	'commitmentAccount/updateContact' => array(
			'n_title' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
			'email' => array('mandatory' => false),
			'adr_street' => array('mandatory' => false),
			'adr_extended' => array('mandatory' => false),
			'adr_zip' => array('mandatory' => false),
			'adr_post_office_box' => array('mandatory' => false),
			'adr_city' => array('mandatory' => false),
			'adr_state' => array('mandatory' => false),
			'adr_country' => array('mandatory' => false),
			'locale' => array('mandatory' => true),
	),
		
	'commitmentAccount/post' => array(
			'place_identifier' => array('mandatory' => false),
			'n_title' => array('mandatory' => false),
			'n_last' => array('mandatory' => true),
			'n_first' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'request' => array('mandatory' => true),
			'request_comment' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
			'adr_street' => array('mandatory' => false),
			'adr_extended' => array('mandatory' => false),
			'adr_zip' => array('mandatory' => false),
			'adr_post_office_box' => array('mandatory' => false),
			'adr_city' => array('mandatory' => false),
			'adr_state' => array('mandatory' => false),
			'adr_country' => array('mandatory' => false),
			'birth_date' => array('mandatory' => false),
			'gender' => array('mandatory' => false),
			'place_identifier' => array('mandatory' => false),
			'locale' => array('mandatory' => false),
	),
	'commitmentAccount/requestTypes' => array(
			'general_information' => array('en_US' => 'General information', 'fr_FR' => 'Information générale'),
	),
	'commitmentAccount/export' => array(
			'status' => array('mandatory' => true),
			'identifier' => array('mandatory' => false),
			'name' => array('mandatory' => true),
			'callback_date' => array('mandatory' => false),
			'origine' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'place_id' => array('mandatory' => true),
			'contact_history' => array('mandatory' => false),
	),

	'interaction/type' => array(
			'modalities' => array(
					'contact' => array('en_US' => 'Contacts', 'fr_FR' => 'Contacts'),
			),
	),
		
	'interaction/type/contact' => array(
			'controller' => '\PpitCommitment\Model\Account::controlInteraction',
			'processor' => '\PpitCommitment\Model\Account::processInteraction',
	),

	'interaction/csv/contact' => array(
			'columns' => array(
					'last_name' => array('property' => 'n_last'),
					'first_name' => array('property' => 'n_first'),
					'email' => array('property' => 'email'),
					'tel_work' => array('property' => 'tel_work'),
					'tel_cell' => array('property' => 'tel_cell'),
					'place' => array('property' => 'place_identifier'),
			),
	),
		
	'commitment/types' => array(
			'type' => 'select',
			'modalities' => array(
					'rental' => array('en_US' => 'Rental', 'fr_FR' => 'Location'),
					'service' => array('en_US' => 'Service', 'fr_FR' => 'Prestation'),
					'learning' => array('en_US' => 'Learning', 'fr_FR' => 'Formation'),
					'p-pit-studies' => array('en_US' => 'Subscription', 'fr_FR' => 'Inscription'),
//					'p-pit-stays' => array('en_US' => 'Stay', 'fr_FR' => 'Séjour'),
			),
			'labels' => array('en_US' => 'Type', 'fr_FR' => 'Type'),
	),
		
	'commitment' => array(
			'properties' => array(
					'type' => array(
							'type' => 'repository',
							'definition' => 'commitment/types',
					),
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
									'approved' => array('en_US' => 'Approved', 'fr_FR' => 'Validé'),
									'delivered' => array('en_US' => 'Delivered', 'fr_FR' => 'Livré'),
									'commissioned' => array('en_US' => 'Commissioned', 'fr_FR' => 'Mis en service'),
									'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
									'invoiced' => array('en_US' => 'Invoiced', 'fr_FR' => 'Facturé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'account_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'quantity' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Quantity',
									'fr_FR' => 'Quantité',
							),
					),
					'unit_price' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Unit price',
									'fr_FR' => 'Prix unitaire',
							),
					),
					'amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'including_options_amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'invoice_identifier' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Invoice identifier',
									'fr_FR' => 'Numéro de facture',
							),
					),
					'invoice_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Invoice date',
									'fr_FR' => 'Date de facture',
							),
					),
					'tax_amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Tax amount',
									'fr_FR' => 'Montant TVA',
							),
					),
					'tax_inclusive' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Tax inclusive',
									'fr_FR' => 'TTC',
							),
					),
			),
			'order' => 'account_name ASC',
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
			'actions' => array(
					'confirm' => array(
							'currentStatuses' => array('new' => null),
							'targetStatus' => 'confirmed',
							'label' => array('en_US' => 'Confirm', 'fr_FR' => 'Confirmer'),
							'properties' => array(
							),
					),
					'reject' => array(
							'currentStatuses' => array('new' => null),
							'targetStatus' => 'rejected',
							'label' => array('en_US' => 'Reject', 'fr_FR' => 'Rejeter'),
							'properties' => array(
							),
					),
					'settle' => array(
							'currentStatuses' => array('approved' => null),
							'targetStatus' => 'settled',
							'label' => array('en_US' => 'Settle', 'fr_FR' => 'Régler'),
							'properties' => array(
							),
					),
					'invoice' => array(
							'currentStatuses' => array('settled' => null),
							'targetStatus' => 'invoiced',
							'label' => array('en_US' => 'Invoice', 'fr_FR' => 'Facturer'),
							'properties' => array(
							),
					),
			),
	),

	'commitment/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
		
	'commitment/search' => array(
			'title' => array('en_US' => 'Commitments', 'fr_FR' => 'Engagements'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
					'type' => 'select',
					'status' => 'select',
					'including_options_amount' => 'range',
					'account_name' => 'contains',
			),
	),

	'commitment/list' => array(
			'status' => 'select',
			'including_options_amount' => 'number',
	),

	'commitment/update' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),

	'commitment/invoice_identifier_mask' => date('Y-'),
	'commitment/invoice_tax_mention' => '',
	'commitment/invoice_bank_details' => null,
	'commitment/invoice_footer_mention_1' => null,
	'commitment/invoice_footer_mention_2' => null,
	'commitment/invoice_footer_mention_3' => null,
	'commitment/invoice' => array(
			'header' => array(
					array(
							'format' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('account_name'),
					),
			),
			'description' => array(
					array(
							'left' => array('en_US' => 'Description', 'fr_FR' => 'Description'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('description'),
					),
					array(
							'left' => array('en_US' => 'Caption', 'fr_FR' => 'Libellé'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('caption'),
					),
					array(
							'left' => array('en_US' => 'Invoice date', 'fr_FR' => 'Date de facture'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('invoice_date'),
					),
			),
	),

	'commitment/proforma' => array(
			'header' => array(
					array(
							'format' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('account_name'),
					),
			),
			'description' => array(
					array(
							'left' => array('en_US' => 'Description', 'fr_FR' => 'Description'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('description'),
					),
					array(
							'left' => array('en_US' => 'Caption', 'fr_FR' => 'Libellé'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('caption'),
					),
					array(
							'left' => array('en_US' => 'Situation date', 'fr_FR' => 'Date de situation'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('date'),
					),
			),
	),
		
	'commitment/supplierIdentificationSheet' => array(
			'ID' => '',
			'Name' => '',
			'CityName' => '',
			'PostalZone' => '',
			'AddressLine1' => '',
			'AddressLine2' => '',
			'Country' => '',
			'TaxSchemeID' => '',
			'TaxSchemeName' => '',
			'RegistrationName' => '',
			'LegalEntityID' => '',
			'LegalEntityCityName' => '',
			'LegalEntityPostalZone' => '',
			'LegalEntityAddressLine1' => '',
			'LegalEntityAddressLine2' => '',
			'LegalEntityCountry' => '',
			'CorporateRegistrationScheme' => '',
			'ContactID' => '',
			'ContactName' => '',
			'ContactTelephone' => '',
			'ContactTelefax' => '',
			'ContactElectronicMail' => '',
			'PaymentMeansCodelistID' => '',
			'PaymentMeansCodelistAgencyID' => '',
			'PaymentMeansCodelistAgencyName' => '',
			'PaymentMeansCode' => '',
			'PaymentChannelCode' => '',
			'InstructionNote' => '',
			'PayeeFinancialAccount' => '',
			'PaymentTerms' => 'taux d\'intérêt légal x 1,5 sur montant impayé',
	),
		
	'commitment/try' => array(
			'caption' => array('mandatory' => true),
			'n_title' => array('mandatory' => true),
			'n_first' => array('mandatory' => true),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
	),

	// Rental
		
	'commitment/rental' => array(
			'currencySymbol' => '€',
			'tax' => 'excluding',
			'properties' => array(
					'type' => array(
							'type' => 'repository',
							'definition' => 'commitment/types',
					),
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
									'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
									'invoiced' => array('en_US' => 'Invoiced', 'fr_FR' => 'Facturé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'including_options_amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
	),

	'commitment/index/rental' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'commitment/search/rental' => array(
			'title' => array('en_US' => 'Rental', 'fr_FR' => 'Location'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
					'type' => 'select',
					'status' => 'select',
					'including_options_amount' => 'range',
					'name' => 'contains',
			),
	),
	
	'commitment/list/rental' => array(
			'caption' => 'input',
			'including_options_amount' => 'number',
			'status' => 'select',
	),
		
	'commitment/update/rental' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),

	// Service
	
	'commitment/service' => array(
			'currencySymbol' => '€',
			'tax' => 'excluding',
			'properties' => array(
					'type' => array(
							'type' => 'repository',
							'definition' => 'commitment/types',
					),
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
									'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
									'invoiced' => array('en_US' => 'Invoiced', 'fr_FR' => 'Facturé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'including_options_amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
	),

	'commitment/index/service' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'commitment/search/service' => array(
			'title' => array('en_US' => 'Learning', 'fr_FR' => 'Formations'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
					'type' => 'select',
					'status' => 'select',
					'including_options_amount' => 'range',
					'name' => 'contains',
			),
	),
		
	'commitment/list/service' => array(
			'caption' => 'input',
			'including_options_amount' => 'number',
			'status' => 'select',
	),
		
	'commitment/update/service' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),

	// Learning

	'commitment/property_10/learning' => array(
			'type' => 'select',
			'modalities' => array(),
			'labels' => array('en_US' => 'Generic 1', 'fr_FR' => 'Générique 1'),
	),

	'commitment/property_11/learning' => array(
			'type' => 'input',
			'labels' => array('en_US' => 'Generic 2', 'fr_FR' => 'Générique 2'),
	),

	'commitment/property_12/learning' => array(
			'type' => 'input',
			'labels' => array('en_US' => 'Generic 3', 'fr_FR' => 'Générique 3'),
	),

	'commitment/property_13/learning' => array(
			'type' => 'input',
			'labels' => array('en_US' => 'Generic 4', 'fr_FR' => 'Générique 4'),
	),

	'commitment/property_14/learning' => array(
			'type' => 'input',
			'labels' => array('en_US' => 'Generic 5', 'fr_FR' => 'Générique 5'),
	),
		
	'commitment/learning' => array(
			'tax' => 'excluding',
			'currencySymbol' => '€',
			'properties' => array(
					'type' => array(
							'type' => 'repository',
							'definition' => 'commitment/types',
					),
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'To be confirmed', 'fr_FR' => 'A confirmer'),
									'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
									'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
									'closed' => array('en_US' => 'Closed', 'fr_FR' => 'Clôturé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'property_1' => array(
							'type' => 'select',
							'modalities' => array(
									'A1' => array('en_US' => 'Non applicable', 'fr_FR' => 'A1 - Entreprise - Formation salarié hors professionalisation'),
									'A1p' => array('en_US' => 'Non applicable', 'fr_FR' => 'A1\' - Entreprise - Formation salarié professionalisation'),
									'A2a' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2a - Collecteur paritaire agréé - Plan de formation'),
									'A2b' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2b - Collecteur paritaire agréé - Professionnalisation'),
									'A2c' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2c - Collecteur paritaire agréé - Compte personnel de formation'),
									'A2d' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2d - Collecteur paritaire agréé - Congé individuel de formation'),
									'A2e' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2e - Fonds assurance formation de non-salariés'),
									'A3a' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3a - Pouvoirs publics - Agents'),
									'A3b' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3b - Pouvoirs publics - Spécifique instances européennes'),
									'A3c' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3c - Pouvoirs publics - Spécifique états'),
									'A3d' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3d - Pouvoirs publics - Spécifique conseils régionaux'),
									'A3e' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3e - Pouvoirs publics - Spécifique Pôle emploi'),
									'A3f' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3f - Pouvoirs publics - Spécifique Autres ressources publiques'),
									'A4' => array('en_US' => 'Non applicable', 'fr_FR' => 'A4 - Contrat conclus avec particuliers'),
									'A5' => array('en_US' => 'Non applicable', 'fr_FR' => 'A4 - Contrat conclus avec autres organismes de formation'),
							),
							'labels' => array(
									'en_US' => 'Non applicable',
									'fr_FR' => 'Origine produits (BF)',
							),
					),
					'property_2' => array(
							'type' => 'select',
							'modalities' => array(
									'A1a' => array('en_US' => 'Non applicable', 'fr_FR' => 'A1a - Salariés financement employeur hors professionnalisation'),
									'A1b' => array('en_US' => 'Non applicable', 'fr_FR' => 'A1b - Salariés financement employeur professionnalisation'),
									'A2' => array('en_US' => 'Non applicable', 'fr_FR' => 'A2 - Demandeurs d\'emploi financement public'),
									'A3' => array('en_US' => 'Non applicable', 'fr_FR' => 'A3 - Particuliers à leurs propres frais'),
									'A4' => array('en_US' => 'Non applicable', 'fr_FR' => 'A4 - Autres stagiaires'),
							),
							'labels' => array(
									'en_US' => 'Non applicable',
									'fr_FR' => 'A - Type stagiaires',
							),
					),
					'property_3' => array(
							'type' => 'select',
							'modalities' => array(
									'B1' => array('en_US' => 'Non applicable', 'fr_FR' => 'B1 - Formés par votre organisme pour son propre compte'),
									'B2' => array('en_US' => 'Non applicable', 'fr_FR' => 'B1 - Formés par votre organisme pour le compte d\'un autre organisme'),
									'B3' => array('en_US' => 'Non applicable', 'fr_FR' => 'B1 - Confiés par votre organisme à un autre organisme de formation'),
							),
							'labels' => array(
									'en_US' => 'Non applicable',
									'fr_FR' => 'B - Activité propre',
							),
					),
					'property_4' => array(
							'type' => 'select',
							'modalities' => array(
									'C1a' => array('en_US' => 'Non applicable', 'fr_FR' => 'C1a - Certification enregistrée au RNCP - Niveau I et II'),
									'C1b' => array('en_US' => 'Non applicable', 'fr_FR' => 'C1b - Certification enregistrée au RNCP - Niveau III'),
									'C1c' => array('en_US' => 'Non applicable', 'fr_FR' => 'C1c - Certification enregistrée au RNCP - Niveau IV'),
									'C1d' => array('en_US' => 'Non applicable', 'fr_FR' => 'C1d - Certification enregistrée au RNCP - Niveau V'),
									'C2' => array('en_US' => 'Non applicable', 'fr_FR' => 'C2 - Autres formations professionnelles continues'),
									'C3a' => array('en_US' => 'Non applicable', 'fr_FR' => 'C3 - Prestations d\'orientation et d\'accompagnement - Hors bilan'),
									'C3b' => array('en_US' => 'Non applicable', 'fr_FR' => 'C3 - Prestations d\'orientation et d\'accompagnement - Bilan'),
							),
							'labels' => array(
									'en_US' => 'Non applicable',
									'fr_FR' => 'C - Objectif formation',
							),
					),
					'property_5' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Non applicable',
									'fr_FR' => 'Spécialités formation',
							),
					),
					'property_10' => array('type' => 'repository', 'definition' => 'commitment/property_10/learning'),
					'property_11' => array('type' => 'repository', 'definition' => 'commitment/property_11/learning'),
					'property_12' => array('type' => 'repository', 'definition' => 'commitment/property_12/learning'),
					'property_13' => array('type' => 'repository', 'definition' => 'commitment/property_13/learning'),
					'property_14' => array('type' => 'repository', 'definition' => 'commitment/property_14/learning'),
					'including_options_amount' => array(
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'invoice_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Invoice date',
									'fr_FR' => 'Date de facture',
							),
					),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
	),

	'commitment/index/learning' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'commitment/search/learning' => array(
			'title' => array('en_US' => 'Learning', 'fr_FR' => 'Formations'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
					'type' => 'select',
					'status' => 'select',
					'including_options_amount' => 'range',
					'name' => 'contains',
					'property_1' => 'select',
					'property_2' => 'select',
					'property_3' => 'select',
					'property_4' => 'select',
					'property_5' => 'contains',
					'property_10' => 'select',
					'property_11' => 'contains',
			),
	),
	
	'commitment/list/learning' => array(
			'caption' => 'input',
			'including_options_amount' => 'number',
			'status' => 'select',
	),
	
	'commitment/update/learning' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
			'property_1' => array('mandatory' => false),
			'property_2' => array('mandatory' => false),
			'property_3' => array('mandatory' => false),
			'property_4' => array('mandatory' => false),
			'property_5' => array('mandatory' => false),
			'property_10' => array('mandatory' => false),
			'property_11' => array('mandatory' => false),
	),
		
	'commitmentTerm' => array(
			'statuses' => array(),
			'properties' => array(
					'name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'expected' => array('fr_FR' => 'Attendu', 'en_US' => 'Expected'),
									'settled' => array('fr_FR' => 'Réglé', 'en_US' => 'Settled'),
									'collected' => array('fr_FR' => 'Encaissé', 'en_US' => 'Collected'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'due_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Due date',
									'fr_FR' => 'Date d\'échéance',
							),
					),
					'settlement_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Settlement date',
									'fr_FR' => 'Date de règlement',
							),
					),
					'collection_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Collection date',
									'fr_FR' => 'Date d\'encaissement',
							),
					),
					'amount' => array(
							'type' => 'number',
							'minValue' => 0,
							'maxValue' => 99999999,
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'means_of_payment' => array(
							'type' => 'select',
							'modalities' => array(
									'bank_card' => array('fr_FR' => 'CB', 'en_US' => 'Bank card'),
									'transfer' => array('fr_FR' => 'Virement', 'en_US' => 'Transfer'),
									'check' => array('fr_FR' => 'Chèque', 'en_US' => 'Check'),
									'cash' => array('fr_FR' => 'Espèces', 'en_US' => 'Cash'),
							),
							'labels' => array(
									'en_US' => 'Means of payment',
									'fr_FR' => 'Mode de règlement',
							),
					),
					'reference' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Reference',
									'fr_FR' => 'Référence',
							),
					),
					'comment' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Comment',
									'fr_FR' => 'Commentaire',
							),
					),
					'document' => array(
							'type' => 'dropbox',
							'labels' => array(
									'en_US' => 'Attachment',
									'fr_FR' => 'Justificatif',
							),
					),
			),
	),
	'commitmentTerm/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitmentTerm/search' => array(
			'title' => array('en_US' => 'Terms', 'fr_FR' => 'Echéances'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'main' => array(
				'name' => 'contains',
				'status' => 'select',
				'collection_date' => 'range',
				'amount' => 'range',
				'reference' => 'contains',
				'comment' => 'contains',
			),
			'more' => array(
				'caption' => 'contains',
				'means_of_payment' => 'select',
			),
	),
	'commitmentTerm/list' => array(
			'name' => 'text',
			'status' => 'select',
			'collection_date' => 'date',
			'amount' => 'number',
	),
	'commitmentTerm/detail' => array(
			'title' => array('en_US' => 'Term detail', 'fr_FR' => 'Détail de l\'échéance'),
			'displayAudit' => true,
	),
	'commitmentTerm/update' => array(
			'status' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'due_date' => array('mandatory' => true),
			'settlement_date' => array('mandatory' => false),
			'collection_date' => array('mandatory' => false),
			'amount' => array('mandatory' => true),
			'means_of_payment' => array('mandatory' => false),
			'reference' => array('mandatory' => false),
			'comment' => array('mandatory' => false),
			'document' => array('mandatory' => false),
	),
	'commitmentMessage' => array(
			'importMaxRows' => 100,
			'importTypes' => array('csv' => array('en_US' => 'CSV file', 'fr_FR' => 'Fichier CSV')),
			'inputMessages' => array(
					'order' => array(
							'action' => '',
							'format' => 'Web-service - json',
							'description' => array(
									
									// Generic
									'message_identifier',
									'issue_date',
									
									// Specific
									'order_number',
									'buyer_party',
									'seller_party',
									'product_identifier',
									'quantity',
							)
					),
			),
			'outputMessages' => array(
					'commissioning' => array(
							'action' => 'commission',
							'format' => 'Web-service - json',
							'description' => array(
									'message_identifier' => array('source' => 'this', 'property' => 'id'),
									'order_number' => array('source' => 'commitment_message', 'property' => 'order_number'),
									'issue_date' => array('source' => 'system', 'property' => 'now'),
									'commissioning_date' => array('source' => 'commitment', 'property' => 'commissioning_date'),
									'buyer_party' => array('source' => 'commitment_message', 'property' => 'buyer_party'),
									'seller_party' => array('source' => 'commitment_message', 'property' => 'seller_party'),
									'product_identifier' => array('source' => 'commitment_message', 'property' => 'product_identifier'),
									'quantity' => array('source' => 'commitment_message', 'property' => 'quantity'),
							)
					),
			),
	),
	'commitment/accountList' => array(
			'title' => array('en_US' => 'Commitments', 'fr_FR' => 'Engagements'),
			'properties' => array(
				'caption' => 'text',
				'property_1' => 'text',
			),
			'anchors' => array(
/*				'document' => array(
						'type' => 'nav',
						'labels' => array('en_US' => 'Documents', 'fr_FR' => 'Documents'),
						'entries' => array(
						),
				),*/
			),
	),
	'commitment/consumeCredit' => array(
			'messages' => array(
					'availabilityAlertTitle' => array(
							'en_US' => 'P-PIT Commitments credits available',
							'fr_FR' => 'Crédits P-PIT Engagements disponibles',
					),
					'availabilityAlertText' => array(
							'en_US' => 'Hello %s,
							
Your available P-PIT Commitments credits reserve for %s is almost out of stock (*). 
In order to avoid the risk of suffering use restrictions, you can right now renew your subscription, for the desired period of time.
Our tip : Have peace of mind by renewing for a 1-year period of time.
					
(*) Your current P-PIT Commitments reserve rises %s units. Your next monthly consumption is estimated up to now to %s units, estimation based on the current active subscriptions.

We hope that our services are giving you full satisfaction. Plesase send your requests or questions to the P-PIT support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-PIT staff
',
							'fr_FR' => 'Bonjour %s,
							
Votre réserve de crédits P-PIT Engagements disponibles pour %s est bientôt épuisée (*). 
Pour ne pas risquer de subir des restrictions à l\'utilisation, vous pouvez dès à présent renouveller en ligne votre souscription pour la durée que vous souhaitez.
Notre conseil : Ayez l\'esprit tranquille en renouvelant pour un an.

(*) Votre réserve actuelle P-PIT Engagements est de %s unités. Votre prochain décompte mensuel est estimé à ce jour à %s unités, estimation basée sur le nombre de dossiers actifs à ce jour.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-PIT : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-PIT
',
					),
					'suspendedServiceTitle' => array(
							'en_US' => 'P-Pit Commitments records suspended',
							'fr_FR' => 'Dossiers P-Pit Engagements suspendus',
					),
					'suspendedServiceText' => array(
							'en_US' => 'Hello %s,
							
Your available P-Pit Commitments credits reserve for %s is out of stock (*). 
Please note that the access has been automatically suspended for the records listed below until a new subscription of credits occurs:
%s
							
Our tip : Have peace of mind by renewing for a record average life-time (for example 6 monthly credits per the yearly average number of records).
					
(*) Your current P-Pit Commitments solde rises %s units.

We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-Pit staff
',
							'fr_FR' => 'Bonjour %s,
							
Votre réserve de crédits P-Pit Engagements pour %s est épuisée (*). 
Veuillez noter que l\'accès a été automatiquement suspendu pour les dossiers listées ci-après jusqu\'à la souscription de nouveaux crédits :
%s
							
Notre conseil : Ayez l\'esprit tranquille en souscrivant le nombre de crédits pour la durée de vie moyenne de vos dossiers (par exemple 6 crédits mensuels par le nombre moyen de dossiers par an).

(*) Votre solde actuel P-Pit Engagements est de %s unités.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-Pit
',
					),
			),
	),
	'commitment/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitment/subscribe/rental' => array(
//			'due_date' => array('mandatory' => false, 'disabled' => true),
	),
	'commitmentMessage/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),

	'journal/accountingChart/sale' => array(
			'rental' => array(
					'settlement' => array(
							'512' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44571' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
							'706' => array(
									'direction' => 1,
									'source' => 'excluding_tax',
							),
					),
			),
			'service' => array(
					'registration' => array(
							'411' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44587' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
							'706' => array(
									'direction' => 1,
									'source' => 'excluding_tax',
							),
					),
					'settlement' => array(
							'411' => array(
									'direction' => 1,
									'source' => 'tax_inclusive',
							),
							'512' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44587' => array(
									'direction' => -1,
									'source' => 'tax_amount',
							),
							'44571' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
					),
			),
	),
		
	'demo' => array(
			'commitmentAccount/search/title' => array(
					'en_US' => '
<h4>Account list</h4>
<p>As a default, all the accounts with a <em>Active</em> status are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des comptes</h4>
<p>Par défaut, tous les comptes dont le statut est <em>Actif</em> sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'commitmentAccount/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered in todo-list mode.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée en mode todo-list.</p>
',
			),
			'commitmentAccount/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'commitmentAccount/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'commitmentAccount/list/checkAll' => array(
					'en_US' => '
<h4>Check all</h4>
<p>This check-box allows to check at one time all the items of the list.</p>
					',
					'fr_FR' => '
<h4>Tout sélectionner</h4>
<p>Cette case à cocher permet de sélectionner d\'un coup tous les éléments de la liste.</p>
',
			),
			'commitmentAccount/list/groupedActions' => array(
					'en_US' => '
<h4>Grouped actions</h4>
<p>The group action button operates along with the individual or global checkboxes on the left column.</p>
<p>It opens a new panel proposing actions to apply to each student who has previously been checked in the list.</p>
<p>For example you can send an emailing by checking the target accounts and then send the email in a grouped way.</p>
					',
					'fr_FR' => '
<h4>Actions groupées</h4>
<p>Le bouton d\'actions groupées agit conjointement avec les cases à cocher individuelles ou globales en colonne de gauche de la liste.</p>
<p>Il ouvre un nouveau panneau proposant des actions à appliquer à chaque compte qui a préalablement été sélectionné dans la liste.</p>
<p>Par exemple vous pouvez envoyer un emailing en cochant dans la liste les comptes à cibler puis émettre l\'email de façon groupée.</p>
					',
			),
			'commitmentAccount/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un compte</h4>
<p>Le bouton + permet l\'ajout d\un nouveau compte.</p>
<p>Les engagements liés à ce compte seront créés dans un second temps.</p>
<p>On peut ainsi gérer un regroupement des engagements par compte.</p>
					',
			),
			'commitmentAccount/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un compte</h4>
<p>Lors de la création d\'un compte les données principales sont renseignées.</p>
	<ul>
		<li>Identification</li>
		<li>Données de contact</li>
		<li>période de validité du compte (seule la date d\'ouverture est obligatoire)</li>
		<li>Le statut (pour mémoire, le statut <em>Actif</em> conditionne la sélection du compte dans la liste par défaut)</li>
	</ul>
					',
			),
			'commitmentAccount/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un compte</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un compte et aux engagements associés.</p>
					',
			),
			'commitmentAccount/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des données du compte</h4>
<p>L\'accès au détail d\'un compte permet de consulter et éventuellement en rectifier les données.</p>
<p>Il donne également accès à l\'onglet de gestion du contact de facturation.</p>
<p>Il donne enfin un accès centralisé, en ajout ou modification, aux engagements associés à ce compte.</p>
					',
			),
			'commitment/accountList/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un engagement</h4>
<p>Le bouton + permet l\'ajout d\un nouvel engagement pour ce compte.</p>
					',
			),
			'commitment/accountList/documents' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Documents</h4>
<p>Quatre documents pré-formatés sont disponibles au niveau du dossier d\'inscription annuelle :</p>
	<ul>
		<li>L\'accusé de réception</li>
		<li>La confirmation d\'inscription</li>
		<li>L\'engagement de prise en charge</li>
		<li>L\'attestation scolaire</li>
	</ul>
<p>Ces documents sont générés au format Word et peuvent être complétés manuellement après téléchargement, par exemple si besoin d\'ajouter une mention spécifique.</p>
',
			),

			'commitment/search/title' => array(
					'en_US' => '
<h4>Commitment list</h4>
<p>As a default, all the active commitments are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des engagements</h4>
<p>Par défaut, tous les engagements actifs sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'commitment/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on active commitments.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les engagements actifs.</p>
',
			),
			'commitment/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'commitment/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'commitment/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un engagement</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un engagement et aux données de facturation et d\'échéancier associées.</p>
					',
			),
			'commitment/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des données de l\'engagement</h4>
<p>L\'accès au détail d\'un engagement permet de consulter et éventuellement en rectifier les données.</p>
<p>Il donne également accès au détail de facturation :</p>
	<ul>
		<li>Le produit souscrit</li>
		<li>Les différentes options souscrites</li>
	</ul>
<p>Il donne enfin accès à l\'échéancier associé à cet engagement.</p>
					',
			),
			'commitment/invoice' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Facture</h4>
<p>Une facture comptable est disponible en téléchargement, ainsi qu\'une facture simplifiée, dite proforma (TTC sans données de TVA).</p>
					',
			),

			'commitmentTerm/search/title' => array(
					'en_US' => '
<h4>Term list</h4>
<p>As a default, all the current terms (to be settled or collected) are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des échéances</h4>
<p>Par défaut, toutes les échéances en cours (à régler ou encaisser) sont présentées dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'commitmentTerm/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on current terms.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les échéances en cours.</p>
',
			),
			'commitmentTerm/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'commitmentTerm/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'commitmentTerm/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'une échéance</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'une échéance.</p>
					',
			),
			'commitmentTerm/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion du statut et des attributs de l\'échéance</h4>
<p>L\'accès au détail d\'une échéance permet de consulter et éventuellement en rectifier les données.</p>
<p>Il permet également d\'en actualiser la statut et y associer une pièce jointe (ex. scan de chèque).</p>
					',
			),
	),
);
