<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitCommitment\Controller\Account' => 'PpitCommitment\Controller\AccountController', // Deprecated. For compatibility reasons with Shin Agency
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
 	       						'contactForm' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/contact-form[/:type][/:place_identifier][/:state_id][/:action_id][/:id]',
        										'defaults' => array(
        												'action' => 'contactForm',
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
    					'dropboxLink' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/dropbox-link[/:document]',
        								'defaults' => array(
        										'action' => 'dropboxLink',
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
						'group' => array(
								'type' => 'segment',
								'options' => array(
										'route' => '/group[/:type]',
										'defaults' => array(
												'action' => 'group',
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
        				'sendMessage' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/send-message[/:type][/:template_id]',
        								'defaults' => array(
        										'action' => 'sendMessage',
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
	        				'downloadInvoice' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/download-invoice[/:id]',
	        								'constraints' => array(
	        										'id'     => '[0-9]*',
	        								),
	        								'defaults' => array(
	        										'action' => 'downloadInvoice',
	        								),
	        						),
	        				),
	        				'guestDownloadInvoice' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/guest-download-invoice[/:id]',
	        								'constraints' => array(
	        										'id'     => '[0-9]*',
	        								),
	        								'defaults' => array(
	        										'action' => 'guestDownloadInvoice',
	        								),
	        						),
	        				),
            				'serialize' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/serialize',
	        								'defaults' => array(
	        										'action' => 'serialize',
	        								),
	        						),
	        				),
            				'repair' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/repair',
	        								'defaults' => array(
	        										'action' => 'repair',
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
		        				'generate' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/generate[/:commitment_id]',
		        								'defaults' => array(
		        										'action' => 'generate',
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
	       						'invoice' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/invoice[/:id]',
		        								'constraints' => array(
		        										'id' => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'invoice',
		        								),
		        						),
		        				),
								'group' => array(
										'type' => 'segment',
										'options' => array(
												'route' => '/group',
												'defaults' => array(
														'action' => 'group',
												),
										),
		        				),
		        				'debit' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/debit',
		        								'defaults' => array(
		        										'action' => 'debit',
		        								),
		        						),
		        				),
		        				'debitSsml' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/debit-ssml[/:place_id]',
		        								'defaults' => array(
		        										'action' => 'debitSsml',
		        								),
		        						),
		        				),
	       						'debitXml' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/debit-xml[/:place_id]',
		        								'defaults' => array(
		        										'action' => 'debitXml',
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
            	array('route' => 'commitmentAccount/post', 'roles' => array('guest')), // Deprecated. For compatibility reasons with Shin Agency
            	array('route' => 'commitmentAccount/contactForm', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/processPost', 'roles' => array('admin', 'ws-incoming')),
            		
            	array('route' => 'commitment', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/index', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/search', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/list', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/accountlist', 'roles' => array('operational_management', 'sales_manager', 'manager')),
            	array('route' => 'commitment/accountList', 'roles' => array('operational_management', 'sales_manager', 'manager')),
            	array('route' => 'commitment/export', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/detail', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/dropboxLink', 'roles' => array('guest')),
            	array('route' => 'commitment/message', 'roles' => array('guest')),
            	array('route' => 'commitment/post', 'roles' => array('admin')),
            	array('route' => 'commitment/try', 'roles' => array('guest')),
            	array('route' => 'commitment/invoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/xmlUblInvoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/settle', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/update', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/group', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateProduct', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateOption', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateTerm', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/suspend', 'roles' => array('admin')),
            	array('route' => 'commitment/serviceAdd', 'roles' => array('guest')),
//            	array('route' => 'commitment/workflow', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/serviceSettle', 'roles' => array('accountant')),
            	array('route' => 'commitment/downloadInvoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/sendMessage', 'roles' => array('sales_manager', 'accountant')),
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
				array('route' => 'commitmentMessage/downloadInvoice', 'roles' => array('sales_manager', 'accountant')),
				array('route' => 'commitmentMessage/guestDownloadInvoice', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/serialize', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/repair', 'roles' => array('admin')),
            	 
            	array('route' => 'commitmentTerm', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/index', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/search', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/detail', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/delete', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/export', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/list', 'roles' => array('sales_manager')),
				array('route' => 'commitmentTerm/generate', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/update', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/group', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/debit', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/debitSsml', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/debitXml', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentTerm/invoice', 'roles' => array('sales_manager', 'accountant')),
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
					'default' => 'account',
					'roles' => array(
							'accountant' => array(
									'show' => true,
									'labels' => array(
											'en_US' => 'Accountant',
											'fr_FR' => 'Comptable',
									),
							),
					),
			),
	),

	'menus/p-pit-engagements' => array(
		'entries' => array(
					'account' => array(
							'route' => 'account/index',
							'params' => array('entry' => 'account', 'type' => 'business'),
							'glyphicon' => 'glyphicon-user',
							'label' => array(
									'en_US' => 'Enterprises',
									'fr_FR' => 'Entreprises',
							),
					),
					'b2c' => array(
							'route' => 'account/index',
							'params' => array('entry' => 'account', 'type' => 'b2c'),
							'glyphicon' => 'glyphicon-user',
							'label' => array(
									'en_US' => 'B2C',
									'fr_FR' => 'Particuliers',
							),
					),
					'service' => array(
							'route' => 'commitment/index',
							'params' => array('type' => 'service'),
							'glyphicon' => 'glyphicon-link',
							'label' => array(
									'en_US' => 'Services',
									'fr_FR' => 'Prestations',
							),
					),
					'rental' => array(
							'route' => 'commitment/index',
							'params' => array('type' => 'rental'),
							'glyphicon' => 'glyphicon-link',
							'label' => array(
									'en_US' => 'Rental',
									'fr_FR' => 'Location',
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
/*					'interaction' => array(
							'route' => 'commitmentMessage/index',
							'params' => array(),
							'urlParams' => array(),
							'glyphicon' => 'glyphicon-transfer',
							'label' => array(
									'en_US' => 'Interactions',
									'fr_FR' => 'Interactions',
							),
					),*/
		),
		'labels' => array(
			'default' => '2pit Commitments',
			'fr_FR' => 'P-Pit Engagements',
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
		'commitment' => new \PpitCommitment\Model\Commitment,
	),
		
	'ppitCommitmentDependencies' => array(
	),

	// Account business

	'core_account/business/property/title_1' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'ENTERPRISE IDENTIFICATION',
			'fr_FR' => 'IDENTIFICATION DE L\'ENTREPRISE',
		),
	),
	'core_account/business/property/title_2' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'REGISTRATION DATA',
			'fr_FR' => 'DONNEES D\'INSCRIPTION',
		),
	),
	'core_account/business/property/title_3' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'COMMENTS',
			'fr_FR' => 'COMMENTAIRES',
		),
	),
	'core_account/business/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'prospect' => array('en_US' => 'Prospect', 'fr_FR' => 'Prospect'),
			'committed' => array('en_US' => 'Committed', 'fr_FR' => 'Engagé'),
			'active' => array('en_US' => 'Customer', 'fr_FR' => 'Client'),
			'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'account' => array('new', 'prospect', 'gone', 'committed', 'active'),
		),
		'mandatory' => true,
	),
	'core_account/business/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Identifier',
			'fr_FR' => 'Identifiant',
		),
	),
	'core_account/business/property/name' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Name',
			'fr_FR' => 'Dénomination',
		),
	),
	'core_account/business/property/basket' => array('definition' => 'core_account/generic/property/basket'),
	'core_account/business/property/contact_1_id' => array(
		'definition' => 'inline',
		'type' => 'photo',
		'labels' => array(
			'en_US' => '',
			'fr_FR' => '',
		),
	),
	'core_account/business/property/n_title' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'Mr' => array('fr_FR' => 'M.', 'en_US' => 'Mr'),
			'Mrs' => array('fr_FR' => 'Mme', 'en_US' => 'Mrs'),
			'Ms' => array('fr_FR' => 'Melle', 'en_US' => 'Ms'),
			'mr-mrs' => array('fr_FR' => 'Mr et Mme', 'en_US' => 'Mr & Mrs'),
			'Maître' => array('fr_FR' => 'Maître', 'en_US' => 'Maître'),
		),
		'labels' => array(
			'en_US' => 'Title',
			'fr_FR' => 'Titre',
		),
	),
	'core_account/business/property/n_first' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - First name',
			'fr_FR' => 'Contact - Prénom',
		),
	),
	'core_account/business/property/n_last' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Last name',
			'fr_FR' => 'Contact - Nom',
		),
		'mandatory' => true,
	),
	'core_account/business/property/email' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Contact - Email',
			'fr_FR' => 'Contact - Email',
		),
	),
	'core_account/business/property/tel_work' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Contact - Phone',
			'fr_FR' => 'Contact - Téléphone',
		),
	),
	'core_account/business/property/tel_cell' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Contact - Cellular',
			'fr_FR' => 'Contact - Mobile',
		),
	),
	'core_account/business/property/adr_street' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Address',
			'fr_FR' => 'Contact - Adresse',
		),
	),
	'core_account/business/property/adr_extended' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Complement',
			'fr_FR' => 'Contact - Complément',
		),
	),
	'core_account/business/property/adr_post_office_box' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Post office box',
			'fr_FR' => 'Contact - Boîte postale',
		),
	),
	'core_account/business/property/adr_zip' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Zip code',
			'fr_FR' => 'Contact - Code postal',
		),
	),
	'core_account/business/property/adr_city' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - City',
			'fr_FR' => 'Contact - Ville',
		),
	),
	'core_account/business/property/adr_state' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - State',
			'fr_FR' => 'Contact - Etat',
		),
	),
	'core_account/business/property/adr_country' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Country',
			'fr_FR' => 'Contact - Pays',
		),
	),
	'core_account/business/property/n_title_2' => array(
		'definition' => 'inline',
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
	'core_account/business/property/n_first_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - First name',
			'fr_FR' => 'Contact - Prénom',
		),
	),
	'core_account/business/property/n_last_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Last name',
			'fr_FR' => 'Contact - Nom',
		),
	),
	'core_account/business/property/email_2' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Contact - Email',
			'fr_FR' => 'Contact - Email',
		),
	),
	'core_account/business/property/tel_work_2' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Contact - Phone',
			'fr_FR' => 'Contact - Téléphone',
		),
	),
	'core_account/business/property/tel_cell_2' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Contact - Cellular',
			'fr_FR' => 'Contact - Mobile',
		),
	),
	'core_account/business/property/address_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Contact - Address',
			'fr_FR' => 'Contact - Adresse',
		),
	),
	'core_account/business/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'2pit' => array('fr_FR' => 'P-PIT', 'en_US' => '2PIT'),
		),
		'labels' => array(
			'en_US' => 'Center',
			'fr_FR' => 'Centre',
		),
	),
	'core_account/business/property/place_caption' => array('definition' => 'core_account/properties/place_caption'),
	'core_account/business/property/opening_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => '1st contact date',
			'fr_FR' => 'Date 1er contact',
		),
	),
	'core_account/business/property/closing_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Closing date',
			'fr_FR' => 'Date de fermeture',
		),
	),
	'core_account/business/property/callback_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Callback date',
			'fr_FR' => 'Date de rappel',
		),
	),
	'core_account/business/property/contact_history' => array(
		'definition' => 'inline',
		'type' => 'log',
		'labels' => array(
			'en_US' => 'Comment',
			'fr_FR' => 'Commentaire',
		),
	),
	
	'core_account/business/property/origine' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'web' => array('en_US' => 'Web site', 'fr_FR' => 'Site web'),
			'subscription' => array('en_US' => 'Online subscription', 'fr_FR' => 'Inscription en ligne'),
			'show' => array('en_US' => 'Show', 'fr_FR' => 'Salon'),
			'cooptation' => array('en_US' => 'Cooptation', 'fr_FR' => 'Cooptation'),
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

	'core_account/business/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'agro' => array('en_US' => 'To be translated', 'fr_FR' => 'Agroalimentaire'),
			'banque' => array('en_US' => 'To be translated', 'fr_FR' => 'Banque / Assurance'),
			'bois' => array('en_US' => 'To be translated', 'fr_FR' => 'Bois / Papier / Carton / Imprimerie'),
			'btp' => array('en_US' => 'To be translated', 'fr_FR' => 'BTP / Matériaux de construction'),
			'chimie' => array('en_US' => 'To be translated', 'fr_FR' => 'Chimie / Parachimie'),
			'commerce' => array('en_US' => 'To be translated', 'fr_FR' => 'Commerce / Négoce / Distribution'),
			'edition' => array('en_US' => 'To be translated', 'fr_FR' => 'Édition / Communication / Multimédia'),
			'electricite' => array('en_US' => 'To be translated', 'fr_FR' => 'Électronique / Électricité'),
			'environnement' => array('en_US' => 'To be translated', 'fr_FR' => 'Environnement'),
			'conseil' => array('en_US' => 'To be translated', 'fr_FR' => 'Études et conseils'),
			'pharmacie' => array('en_US' => 'To be translated', 'fr_FR' => 'Industrie pharmaceutique'),
			'informatique' => array('en_US' => 'To be translated', 'fr_FR' => 'Informatique / Télécoms'),
			'equipement' => array('en_US' => 'To be translated', 'fr_FR' => 'Machines et équipements / Automobile'),
			'metallurgie' => array('en_US' => 'To be translated', 'fr_FR' => 'Métallurgie / Travail du métal'),
			'plastique' => array('en_US' => 'To be translated', 'fr_FR' => 'Plastique / Caoutchouc'),
			'services' => array('en_US' => 'To be translated', 'fr_FR' => 'Services aux entreprises'),
			'textile' => array('en_US' => 'To be translated', 'fr_FR' => 'Textile / Habillement / Chaussure'),
			'transport' => array('en_US' => 'To be translated', 'fr_FR' => 'Transports / Logistique'),
			'autre' => array('en_US' => 'To be translated', 'fr_FR' => 'Autres'),
		),
		'labels' => array(
			'en_US' => 'Sector',
			'fr_FR' => 'Secteur',
		),
	),

	'core_account/business/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Available field 1',
			'fr_FR' => 'Champs libre 1',
		),
	),

	'core_account/business/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Available field 2',
			'fr_FR' => 'Champs libre 2',
		),
	),
	
	'core_account/business/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Available field 3',
			'fr_FR' => 'Champs libre 3',
		),
	),

	'core_account/business/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Available field 4',
			'fr_FR' => 'Champs libre 4',
		),
	),
	
	'core_account/business/property/property_13' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'contact' => array('en_US' => 'Contact', 'fr_FR' => 'Prise de contact'),
		),
		'labels' => array(
			'en_US' => 'Next meeting context',
			'fr_FR' => 'Cadre prochaine rencontre',
		),
	),
	
	'core_account/business/property/comment_1' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Enterprise description',
			'fr_FR' => 'Description de l\'entreprise',
		),
		'max_length' => 65535,
	),

	'core_account/business/property/comment_2' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Offer description',
			'fr_FR' => 'Description de l\'offre',
		),
		'max_length' => 65535,
	),
		
	'core_account/business' => array(
		'statuses' => array(),
		'properties' => array(
			'title_1', 'title_2', 'title_3', 'status', 'identifier', 'name', 'basket', 'n_title',
			'n_first', 'n_last', 'email', 'tel_work', 'tel_cell', 
			'adr_street', 'adr_extended', 'adr_post_office_box', 'adr_zip', 'adr_city', 'adr_state', 'adr_country', 
			'n_title_2', 'n_first_2', 'n_last_2', 'email_2', 'tel_work_2', 'tel_cell_2', 'address_2', 
			'place_id'/*, 'place_caption'*/, 'opening_date', 'closing_date', 'callback_date', 'origine', 'contact_history',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_13',
			'comment_1', 'comment_2'),
		'order' => 'name',
		'options' => ['internal_identifier' => true],
	),
	
	'core_account/index/business' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'core_account/search/business' => array(
			'title' => array('en_US' => 'Accounts', 'fr_FR' => 'Comptes'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'properties' => array(
				'place_id' => [],
				'status' => ['multiple' => true],
				'name' => [],
				'basket' => ['multiple' => true],
				'property_1' => [],
				'opening_date' => [],
				'callback_date' => [],
				'origine' => ['multiple' => true],
				'email' => [],
			),
	),
	
	'core_account/list/business' => array(
			'properties' => array(
					'status' => ['color' => ['new' => 'LightGreen', 'interested' => 'LightSalmon', 'candidate' => 'LightBlue', 'answer' => 'LightSalmon', 'gone' => 'LightGrey']],
					'place_id' => [],
					'name' => [],
					'basket' => [],
					'property_1' => [],
					'opening_date' => [],
					'callback_date' => [],
					'origine' => [],
			),
	),
	
	'core_account/detail/business' => array(
			'title' => array('en_US' => 'Account detail', 'fr_FR' => 'Détail du compte'),
			'displayAudit' => true,
			'tabs' => array(
					'contact_1' => array(
							'definition' => 'inline',
							'route' => 'account/update',
							'params' => array('type' => ''),
							'labels' => array('en_US' => 'Main contact', 'fr_FR' => 'Contact principal'),
					),
					'contact_2' => array(
							'definition' => 'inline',
							'route' => 'account/updateContact',
							'params' => array('type' => '', 'contactNumber' => 2),
							'labels' => array('en_US' => 'Invoicing', 'fr_FR' => 'Facturation'),
					),
			),
	),
	
	'core_account/update/business' => array(
			'place_id' => array('mandatory' => true),
			'status' => array('mandatory' => true),
			'identifier' => [],
			'name' => array('mandatory' => false),
			'basket' => array('mandatory' => false),
			'opening_date' => array('mandatory' => false),
			'callback_date' => array('mandatory' => false),
			'origine' => array('mandatory' => false),
			'title_1' => null,
			'n_title' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => false),
			'email' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
			'adr_street' => array('mandatory' => false),
			'adr_zip' => array('mandatory' => false),
			'adr_city' => array('mandatory' => false),
			'title_2' => null,
			'property_1' => array('mandatory' => false),
			'property_2' => array('mandatory' => false),
			'property_3' => array('mandatory' => false),
			'property_4' => array('mandatory' => false),
			'property_5' => array('mandatory' => false),
			'title_3' => null,
			'comment_1' => array('mandatory' => false),
			'comment_2' => array('mandatory' => false),
			'contact_history' => array('mandatory' => false),
	),
	
	'core_account/updateContact/business' => array(
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
	
	'core_account/groupUpdate/business' => array(
			'status' => array('mandatory' => false),
			'callback_date' => array('mandatory' => false),
	),

	'core_account/requestTypes/business' => array(
			'contact' => array('en_US' => 'Contact', 'fr_FR' => 'Contact'),
			'newsletter' => array('en_US' => 'Newsletter', 'fr_FR' => 'Newsletter'),
			'general_information' => array('en_US' => 'General information', 'fr_FR' => 'Information générale'),
	),

	'core_account/export/business' => array(
			'status' => [],
			'identifier' => [],
			'name' => [],
			'basket' => [],
			'opening_date' => [],
			'callback_date' => [],
			'origine' => [],
			'place_id' => [],
			'n_title' => [],
			'n_first' => [],
			'n_last' => [],
			'email' => [],
			'tel_work' => [],
			'tel_cell' => [],
			'adr_street' => [],
			'adr_zip' => [],
			'adr_city' => [],
			'property_1' => [],
			'property_2' => [],
			'property_3' => [],
			'property_4' => [],
			'property_5' => [],
				
			'n_title_2' => [],
			'n_first_2' => [],
			'n_last_2' => [],
			'tel_work_2' => [],
			'tel_cell_2' => [],
			'email_2' => [],
			'address_2' => [],
			
			'comment_1' => [],
			'comment_2' => [],

			'contact_history' => [],
	),
	
	'core_account/indexCard/business' => array(
			'title' => array('en_US' => 'Enterprise index card', 'fr_FR' => 'Fiche entreprise'),
			'header' => array(
					'place_id' => null,
					'status' => null,
					'origine' => null,
			),
			'1st-column' => array(
					'title' => 'title_1',
					'rows' => array(
							'n_title' => array('mandatory' => true),
							'n_first' => array('mandatory' => true),
							'n_last' => array('mandatory' => true),
							'email' => array('mandatory' => false),
							'tel_work' => array('mandatory' => false),
							'tel_cell' => array('mandatory' => false),
							'adr_street' => array('mandatory' => false),
							'adr_extended' => array('mandatory' => false),
							'adr_post_office_box' => array('mandatory' => false),
							'adr_zip' => array('mandatory' => false),
							'adr_city' => array('mandatory' => false),
							'adr_state' => array('mandatory' => false),
							'adr_country' => array('mandatory' => false),
					),
			),
			'2nd-column' => array(
					'title' => 'title_2',
					'rows' => array(
							'property_1' => array('mandatory' => false),
							'property_2' => array('mandatory' => false),
							'property_3' => array('mandatory' => false),
							'property_4' => array('mandatory' => false),
					),
			),
			'pdfDetailStyle' => '
<style>
table.note-report {
	font-size: 1em;
	border: 1px solid gray;
}
table.note-report th {
	color: #FFF;
	font-weight: bold;
	text-align: center;
	vertical-align: center;
	border: 1px solid gray;
	background-color: #006169;
}
		
table.note-report td {
	color: #666;
	border: 1px solid gray;
}
</style>
',
	),

	// Account B2C
	
	'core_account/b2c/property/title_1' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'CLIENT IDENTIFICATION',
			'fr_FR' => 'IDENTIFICATION DU CLIENT',
		),
	),
	
	'core_account/b2c/property/title_2' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'INVOICING DATA',
			'fr_FR' => 'DONNEES DE FACTURATION',
		),
	),
	
	'core_account/b2c/property/status' => array('definition' => 'core_account/generic/property/status', 'mandatory' => true),
	
	'core_account/b2c/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Identifier',
			'fr_FR' => 'Identifiant',
		),
	),

	'core_account/b2c/property/name' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Formatted name',
			'fr_FR' => 'Nom formaté',
		),
	),
	
	'core_account/b2c/property/n_fn' => array('definition' => 'core_account/generic/property/n_fn', 'mandatory' => true),
	'core_account/b2c/property/photo_link_id' => array('definition' => 'core_account/generic/property/photo_link_id'),
	'core_account/b2c/property/place_id' => array('definition' => 'core_account/generic/property/place_id'),
	'core_account/b2c/property/contact_1_id' => array('definition' => 'core_account/generic/property/contact_id'),
	'core_account/b2c/property/n_title' => array('definition' => 'core_account/generic/property/n_title', 'mandatory' => true),
	'core_account/b2c/property/n_first' => array('definition' => 'core_account/generic/property/n_first', 'mandatory' => true),
	'core_account/b2c/property/n_last' => array('definition' => 'core_account/generic/property/n_last', 'mandatory' => true),
	'core_account/b2c/property/email' => array('definition' => 'core_account/generic/property/email', 'mandatory' => true),
	'core_account/b2c/property/tel_work' => array('definition' => 'core_account/generic/property/tel_work'),
	'core_account/b2c/property/tel_cell' => array('definition' => 'core_account/generic/property/tel_cell'),
	'core_account/b2c/property/adr_street' => array('definition' => 'core_account/generic/property/adr_street'),
	'core_account/b2c/property/adr_zip' => array('definition' => 'core_account/generic/property/adr_zip'),
	'core_account/b2c/property/adr_city' => array('definition' => 'core_account/generic/property/adr_city'),
	'core_account/b2c/property/adr_state' => array('definition' => 'core_account/generic/property/adr_state'),
	'core_account/b2c/property/adr_country' => array('definition' => 'core_account/generic/property/adr_country'),
	'core_account/b2c/property/birth_date' => array('definition' => 'core_account/generic/property/birth_date'),
	'core_account/b2c/property/callback_date' => array('definition' => 'core_account/generic/property/callback_date'),
	'core_account/b2c/property/invoice_n_title' => array('definition' => 'core_account/generic/property/invoice_n_title', 'mandatory' => true),
	'core_account/b2c/property/invoice_n_first' => array('definition' => 'core_account/generic/property/invoice_n_first', 'mandatory' => true),
	'core_account/b2c/property/invoice_n_last' => array('definition' => 'core_account/generic/property/invoice_n_last', 'mandatory' => true),
	'core_account/b2c/property/invoice_email' => array('definition' => 'core_account/generic/property/invoice_email', 'mandatory' => true),
	'core_account/b2c/property/invoice_tel_work' => array('definition' => 'core_account/generic/property/invoice_tel_work'),
	'core_account/b2c/property/invoice_tel_cell' => array('definition' => 'core_account/generic/property/invoice_tel_cell'),
	'core_account/b2c/property/invoice_adr_street' => array('definition' => 'core_account/generic/property/invoice_adr_street'),
	'core_account/b2c/property/invoice_adr_zip' => array('definition' => 'core_account/generic/property/invoice_adr_zip'),
	'core_account/b2c/property/invoice_adr_city' => array('definition' => 'core_account/generic/property/invoice_adr_city'),
	'core_account/b2c/property/invoice_adr_state' => array('definition' => 'core_account/generic/property/invoice_adr_state'),
	'core_account/b2c/property/invoice_adr_country' => array('definition' => 'core_account/generic/property/invoice_adr_country'),
	'core_account/b2c/property/transfer_order_id' => array('definition' => 'core_account/generic/property/transfer_order_id'),
	'core_account/b2c/property/transfer_order_date' => array('definition' => 'core_account/generic/property/transfer_order_date'),
	'core_account/b2c/property/bank_identifier' => array('definition' => 'core_account/generic/property/bank_identifier'),
	
	'core_account/b2c/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Reminder to communicate',
			'fr_FR' => 'Rappel à communiquer',
		),
	),

	'core_account/b2c/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'B',
			'fr_FR' => 'B',
		),
	),

	'core_account/b2c/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'C',
			'fr_FR' => 'C',
		),
	),

	'core_account/b2c/property/contact_history' => array('definition' => 'core_account/generic/property/contact_history'),
	
	'core_account/b2c/contact/contact_2' => array(
			'route' => 'account/updateContact',
			'params' => array('type' => 'b2c', 'contactNumber' => 2),
			'labels' => array('en_US' => 'Mother', 'fr_FR' => 'Mère'),
	),

	'core_account/b2c/contact/contact_3' => array(
			'route' => 'account/updateContact',
			'params' => array('type' => 'b2c', 'contactNumber' => 2),
			'labels' => array('en_US' => 'Father', 'fr_FR' => 'Père'),
	),

	'core_account/b2c' => array(
		'statuses' => array(),
		'properties' => array(
			'title_1', 'title_2', 'status', 'identifier', 'name', 'n_fn', 'photo_link_id', 'place_id',
			'contact_1_id', 'n_title', 'n_first', 'n_last', 'email', 'tel_work', 'tel_cell',
			'adr_street', 'adr_zip', 'adr_city', 'adr_state', 'adr_country', 'birth_date', 'callback_date',
			'invoice_n_title', 'invoice_n_first', 'invoice_n_last', 'invoice_email', 'invoice_tel_work', 'invoice_tel_cell',
			'invoice_adr_street', 'invoice_adr_zip', 'invoice_adr_city', 'invoice_adr_state', 'invoice_adr_country',
			'transfer_order_id', 'transfer_order_date', 'bank_identifier',
			'property_1', 'property_2', 'property_3', 'contact_history',
		),
		'order' => 'n_fn',
	),

	'core_account/index/b2c' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'core_account/search/b2c' => array(
			'title' => array('en_US' => 'Accounts', 'fr_FR' => 'Comptes'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'properties' => array(
					'place_id' => [],
					'status' => ['multiple' => true],
					'identifier' => [],
					'name' => [],
					'email' => [],
					'birth_date' => [],
					'callback_date' => [],
					'property_3' => [],
			),
	),
	
	'core_account/list/b2c' => array(
			'properties' => array(
					'status' => ['color' => ['new' => 'LightGreen', 'interested' => 'LightSalmon', 'candidate' => 'LightBlue', 'answer' => 'LightSalmon', 'gone' => 'LightGrey']],
					'name' => [],
					'identifier' => [],
					'tel_work' => ['rendering' => 'phone'],
					'tel_cell' => ['rendering' => 'phone'],
					'email' => ['rendering' => 'email'],
					'birth_date' => [],
					'callback_date' => [],
					'place_id' => [],
			),
	),
	
	'core_account/detail/b2c' => array(
			'title' => array('en_US' => 'Account detail', 'fr_FR' => 'Détail du compte'),
			'displayAudit' => true,
			'tabs' => array(
					'contact_1' => array(
							'definition' => 'inline',
							'route' => 'account/update',
							'params' => array('type' => ''),
							'labels' => array('en_US' => 'Main contact', 'fr_FR' => 'Contact principal'),
					),
					'contact_2' => array('definition' => 'core_account/b2c/contact/contact_2'),
					'contact_3' => array('definition' => 'core_account/b2c/contact/contact_3'),
			),
	),
	
	'core_account/update/b2c' => array(
		'place_id' => array('mandatory' => true),
		'status' => array('mandatory' => true),
		'name' => array('mandatory' => true),
		'identifier' => array('mandatory' => true),
		'n_first' => array('mandatory' => false),
		'n_last' => array('mandatory' => true),
		'callback_date' => array('mandatory' => false),
		'photo_link_id' => array('mandatory' => false),
		'email' => array('mandatory' => false),
		'tel_work' => array('mandatory' => false),
		'tel_cell' => array('mandatory' => false),
		'birth_date' => array('mandatory' => false),
		'property_1' => array('mandatory' => false),
		'property_2' => array('mandatory' => false),
		'property_3' => array('mandatory' => false),
		'transfer_order_id' => array('mandatory' => false), 
		'transfer_order_date' => array('mandatory' => false), 
		'bank_identifier' => array('mandatory' => false),
		'contact_history' => array('mandatory' => false),
	),
	
	'core_account/updateContact/b2c' => array(
			'n_title' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
			'email' => array('mandatory' => false),
			'adr_street' => array('mandatory' => false),
			'adr_zip' => array('mandatory' => false),
			'adr_city' => array('mandatory' => false),
			'adr_state' => array('mandatory' => false),
			'adr_country' => array('mandatory' => false),
	),
	
	'core_account/groupUpdate/b2c' => array(
			'status' => [],
			'callback_date' => [],
			'property_1' => [],
			'property_2' => [],
	),
	
	'core_account/requestTypes/b2c' => array(
			'contact' => array('en_US' => 'Contact', 'fr_FR' => 'Contact'),
			'newsletter' => array('en_US' => 'Newsletter', 'fr_FR' => 'Newsletter'),
			'general_information' => array('en_US' => 'General information', 'fr_FR' => 'Information générale'),
	),
	
	'core_account/export/b2c' => array(
			'place_id' => array('mandatory' => true),
			'status' => [],
			'identifier' => [],
			'name' => [],
			'callback_date' => [],
			'n_title' => [],
			'n_first' => [],
			'n_last' => [],
			'email' => [],
			'tel_work' => [],
			'tel_cell' => [],
			'birth_date' => [],
			'adr_street' => [],
			'adr_zip' => [],
			'adr_city' => [],
			'adr_state' => [],
			'adr_country' => [],
			'invoice_n_title' => [],
			'invoice_n_first' => [],
			'invoice_n_last' => [],
			'invoice_email' => [],
			'invoice_tel_work' => [],
			'invoice_tel_cell' => [],
			'invoice_adr_street' => [],
			'invoice_adr_zip' => [],
			'invoice_adr_city' => [],
			'invoice_adr_state' => [],
			'invoice_adr_country' => [],
			'property_1' => [],
			'property_2' => [],
			'property_3' => [],
			'contact_history' => [],
	),

	'core_account/indexCard/b2c' => array(
			'title' => array('en_US' => 'Client index card', 'fr_FR' => 'Fiche client'),
			'header' => array(
					'place_id' => null,
					'status' => null,
			),
			'1st-column' => array(
					'title' => 'title_1',
					'rows' => array(
							'identifier' => [],
							'n_title' => [],
							'n_first' => [],
							'n_last' => [],
							'email' => [],
							'tel_work' => [],
							'tel_cell' => [],
							'birth_date' => [],
							'adr_street' => [],
							'adr_zip' => [],
							'adr_city' => [],
							'adr_state' => [],
							'adr_country' => [],
							'property_1' => [],
					),
			),
			'2nd-column' => array(
					'title' => 'title_2',
					'rows' => array(
							'invoice_n_title' => [],
							'invoice_n_first' => [],
							'invoice_n_last' => [],
							'invoice_email' => [],
							'invoice_tel_work' => [],
							'invoice_tel_cell' => [],
							'invoice_adr_street' => [],
							'invoice_adr_zip' => [],
							'invoice_adr_city' => [],
							'invoice_adr_state' => [],
							'invoice_adr_country' => [],
					),
			),
			'pdfDetailStyle' => '
<style>
table.note-report {
	font-size: 1em;
	border: 1px solid gray;
}
table.note-report th {
	color: #FFF;
	font-weight: bold;
	text-align: center;
	vertical-align: center;
	border: 1px solid gray;
	background-color: #006169;
}
		
table.note-report td {
	color: #666;
	border: 1px solid gray;
}
</style>
',
	),
	
	// Interaction
	
	'interaction/type' => array(
			'modalities' => array(
					'contact' => array('en_US' => 'Contacts', 'fr_FR' => 'Contacts'),
			),
	),
		
	'interaction/type/contact' => array(
			'controller' => '\PpitCore\Model\Account::controlInteraction',
			'processor' => '\PpitCore\Model\Account::processInteraction',
	),

	'interaction/type/web_service' => array(
			'controller' => null,
			'processor' => '\PpitCommitment\Controller\AccountController::processPost',
	),
		
	'interaction/csv/contact' => array(
			'columns' => array(
					'name' => array('property' => 'name'),
					'n_last' => array('property' => 'n_last'),
					'n_first' => array('property' => 'n_first'),
					'email' => array('property' => 'email'),
					'tel_work' => array('property' => 'tel_work'),
					'tel_cell' => array('property' => 'tel_cell'),
					'place_identifier' => array('property' => 'place_identifier'),
					'comment' => array('property' => 'comment'),
			),
	),
	
	// Commitments

	'commitment/property/year' => array(
			'type' => 'text',
			'labels' => array(
					'en_US' => 'Accounting year',
					'fr_FR' => 'Année comptable',
			),
	),
	'commitment/types' => array(
			'type' => 'select',
			'modalities' => array(
					'rental' => array('en_US' => 'Rental', 'fr_FR' => 'Location'),
					'service' => array('en_US' => 'Service', 'fr_FR' => 'Prestation'),
					'human_service' => array('en_US' => 'Human service', 'fr_FR' => 'Service à la personne'),
					'learning' => array('en_US' => 'Learning', 'fr_FR' => 'Formation'),
					'p-pit-studies' => array('en_US' => 'Subscription', 'fr_FR' => 'Inscription'),
//					'p-pit-stays' => array('en_US' => 'Stay', 'fr_FR' => 'Séjour'),
			),
			'labels' => array('en_US' => 'Type', 'fr_FR' => 'Type'),
	),
	'commitment/property/place_id' => array(
			'type' => 'select',
			'modalities' => array(
			),
			'labels' => array(
					'en_US' => 'Place',
					'fr_FR' => 'Etablissement',
			),
	),
	'commitment/property/place_caption' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Place',
					'fr_FR' => 'Centre',
			),
	),
	'commitment/property/account_id' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Client',
					'fr_FR' => 'Client',
			),
	),
	'commitment/property/account_name' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Name',
					'fr_FR' => 'Nom',
			),
	),
	'commitment/property/account_identifier' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Client identifier',
					'fr_FR' => 'Référence client',
			),
	),
	'commitment/property/caption' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Caption',
					'fr_FR' => 'Libellé',
			),
	),
	'commitment/property/description' => array(
			'type' => 'textarea',
			'labels' => array(
					'en_US' => 'Description',
					'fr_FR' => 'Description',
			),
	),
	'commitment/property/quantity' => array(
			'type' => 'number',
			'minValue' => 0,
			'maxValue' => 1000000,
			'labels' => array(
					'en_US' => 'Quantity',
					'fr_FR' => 'Quantité',
			),
	),
	'commitment/property/unit_price' => array(
			'type' => 'number',
			'minValue' => 0,
			'maxValue' => 1000000,
			'labels' => array(
					'en_US' => 'Unit price',
					'fr_FR' => 'Prix unitaire',
			),
	),
	'commitment/property/amount' => array(
			'type' => 'number',
			'min_value' => 0,
			'max_value' => 1000000,
			'labels' => array(
					'en_US' => 'Amount',
					'fr_FR' => 'Montant',
			),
	),
	'commitment/property/including_options_amount' => array(
			'type' => 'number',
			'labels' => array(
					'en_US' => 'Including options',
					'fr_FR' => 'Options incluses',
			),
	),
	'commitment/property/invoice_identifier' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Invoice identifier',
					'fr_FR' => 'Numéro de facture',
			),
	),
	'commitment/property/invoice_date' => array(
			'type' => 'date',
			'labels' => array(
					'en_US' => 'Invoice date',
					'fr_FR' => 'Date de facture',
			),
	),
	'commitment/property/tax_amount' => array(
			'type' => 'number',
			'labels' => array(
					'en_US' => 'Tax amount',
					'fr_FR' => 'Montant TVA',
			),
	),
	'commitment/property/tax_inclusive' => array(
			'type' => 'number',
			'labels' => array(
					'en_US' => 'Tax inclusive',
					'fr_FR' => 'TTC',
			),
	),
	'commitment/property/update_time' => array(
			'type' => 'datetime',
			'labels' => array(
					'en_US' => 'Update time',
					'fr_FR' => 'Date de mise à jour',
			),
	),

	'commitment' => array(
			'currencySymbol' => '€',
			'tax' => 'excluding',
			'properties' => array(
					'year' => array('definition' => 'commitment/property/year'),
					'type' => array(
							'type' => 'repository', // Deprecated
							'definition' => 'commitment/types',
					),
					'status' => array(
							'definition' => 'inline',
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
					'place_id' => array('definition' => 'commitment/property/place_id'),
					'place_caption' => array('definition' => 'commitment/property/place_caption'),
					'account_id' => array('definition' => 'commitment/property/account_id'),
					'account_name' => array('definition' => 'commitment/property/account_id'),
					'account_identifier' => array('definition' => 'commitment/property/account_identifier'),
					'caption' => array('definition' => 'commitment/property/caption'),
					'description' => array('definition' => 'commitment/property/description'),
					'quantity' => array('definition' => 'commitment/property/quantity'),
					'unit_price' => array('definition' => 'commitment/property/unit_price'),
					'amount' => array('definition' => 'commitment/property/amount'),
					'including_options_amount' => array('definition' => 'commitment/property/including_options_amount'),
					'invoice_identifier' => array('definition' => 'commitment/property/invoice_identifier'),
					'invoice_date' => array('definition' => 'commitment/property/invoice_date'),
					'tax_amount' => array('definition' => 'commitment/property/tax_amount'),
					'tax_inclusive' => array('definition' => 'commitment/property/tax_inclusive'),
					'update_time' => array('definition' => 'commitment/property/update_time'),
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
					'place_id' => 'select',
					'type' => 'select',
					'status' => 'select',
					'year' => 'contains',
					'including_options_amount' => 'range',
					'account_name' => 'contains',
			),
	),

	'commitment/list' => array(
			'place_id' => 'select',
			'type' => 'select',
			'year' => 'text',
			'status' => 'select',
			'account_name' => 'text',
			'caption' => 'text',
			'quantity' => 'number',
			'unit_price' => 'number',
			'amount' => 'number',
			'including_options_amount' => 'number',
			'update_time' => 'datetime',
	),

	'commitment/update/business' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
			'amount' => array('mandatory' => false),
	),
		
	'commitment/update/generic' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
			'amount' => array('mandatory' => false),
	),

	'commitment/update/b2c' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
			'amount' => array('mandatory' => false),
	),
		
	'commitment/update' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),
	'commitment/group/generic' => array(
			'status' => [],
			'caption' => [],
			'description' => [],
	),
	'commitment/group/business' => array(
			'status' => [],
			'caption' => [],
			'description' => [],
	),
	'commitment/group/learning' => array(
			'status' => [],
			'caption' => [],
			'description' => [],
	),
	'commitment/group/b2c' => array(
			'status' => [],
			'caption' => [],
			'description' => [],
	),

	'place_config/default' => array(
			'commitment/invoice_header' => null,
	),
	
	'commitment/invoice_identifier_mask' => array(
		'format' => array('default' => '%s-%5d'),
		'params' => array('year', 'counter'),
	),
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
							'left' => array('en_US' => 'Caption', 'fr_FR' => 'Libellé'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('caption'),
					),
					array(
							'left' => array('en_US' => 'Description', 'fr_FR' => 'Description'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('description'),
					),
			),
			'tax' => false,
			'terms' => true,
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
							'definition' => 'inline',
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
					'year' => array('definition' => 'commitment/property/year'),
					'place_id' => array('definition' => 'commitment/property/place_id'),
					'account_id' => array('definition' => 'commitment/property/account_id'),
					'account_name' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'definition' => 'inline',
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'invoice_date' => array('definition' => 'commitment/property/invoice_date'),
					'including_options_amount' => array(
							'definition' => 'inline',
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'update_time' => array('definition' => 'commitment/property/update_time'),
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
					'place_id' => 'select',
					'type' => 'select',
					'status' => 'select',
					'year' => 'contains',
					'including_options_amount' => 'range',
					'account_name' => 'contains',
			),
	),
	
	'commitment/list/rental' => array(
			'place_id' => 'input',
			'account_name' => 'text',
			'year' => 'text',
			'caption' => 'input',
			'including_options_amount' => 'number',
			'status' => 'select',
			'update_time' => 'datetime',
	),
		
	'commitment/update/rental' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),

	'commitment/group/rental' => array(
			'status' => [],
			'caption' => [],
			'description' => [],
	),

	// Service
	
	'commitment/service' => array(
			'currencySymbol' => '€',
			'tax' => 'excluding',
			'properties' => array(
					'type' => array('definition' => 'commitment/types'),
					'status' => array(
							'definition' => 'inline',
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
					'account_name' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'year' => array('definition' => 'commitment/property/year'),
					'place_id' => array('definition' => 'commitment/property/place_id'),
					'account_id' => array('definition' => 'commitment/property/account_id'),
					'account_name' => array('definition' => 'commitment/property/account_id'),
					'caption' => array('definition' => 'commitment/property/caption'),
					'description' => array('definition' => 'commitment/property/description'),
					'quantity' => array('definition' => 'commitment/property/quantity'),
					'unit_price' => array('definition' => 'commitment/property/unit_price'),
					'amount' => array('definition' => 'commitment/property/amount'),
					'including_options_amount' => array('definition' => 'commitment/property/including_options_amount'),
					'invoice_identifier' => array('definition' => 'commitment/property/invoice_identifier'),
					'invoice_date' => array('definition' => 'commitment/property/invoice_date'),
					'tax_amount' => array('definition' => 'commitment/property/tax_amount'),
					'tax_inclusive' => array('definition' => 'commitment/property/tax_inclusive'),
					'update_time' => array('definition' => 'commitment/property/update_time'),
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
					'place_id' => 'select',
					'type' => 'select',
					'status' => 'select',
					'year' => 'contains',
					'account_name' => 'contains',
					'caption' => 'contains',
					'including_options_amount' => 'range',
			),
	),
		
	'commitment/list/service' => array(
			'place_id' => 'select',
			'type' => 'select',
			'status' => 'select',
			'year' => 'text',
			'account_name' => 'text',
			'caption' => 'text',
			'quantity' => 'number',
			'unit_price' => 'number',
			'amount' => 'number',
			'including_options_amount' => 'number',
			'update_time' => 'datetime',
	),
		
	'commitment/update/service' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),
	'commitment/group/service' => array(
			'year' => [],
			'status' => [],
			'invoice_date' => [],
			'caption' => [],
			'description' => [],
	),

	// Human service
	
	'commitment/human_service' => array(
			'currencySymbol' => '€',
			'tax' => 'none',
			'properties' => array(
					'type' => array('definition' => 'commitment/types'),
					'status' => array(
							'definition' => 'inline',
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
					'account_name' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'year' => array('definition' => 'commitment/property/year'),
					'place_id' => array('definition' => 'commitment/property/place_id'),
					'account_id' => array('definition' => 'commitment/property/account_id'),
					'account_name' => array('definition' => 'commitment/property/account_id'),
					'n_fn' => array('definition' => 'commitment/property/n_fn'),
					'caption' => array('definition' => 'commitment/property/caption'),
					'description' => array('definition' => 'commitment/property/description'),
					'quantity' => array('definition' => 'commitment/property/quantity'),
					'unit_price' => array('definition' => 'commitment/property/unit_price'),
					'amount' => array('definition' => 'commitment/property/amount'),
					'including_options_amount' => array('definition' => 'commitment/property/including_options_amount'),
					'invoice_identifier' => array('definition' => 'commitment/property/invoice_identifier'),
					'invoice_date' => array('definition' => 'commitment/property/invoice_date'),
					'tax_amount' => array('definition' => 'commitment/property/tax_amount'),
					'tax_inclusive' => array('definition' => 'commitment/property/tax_inclusive'),
					'update_time' => array('definition' => 'commitment/property/update_time'),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
	),
	
	'commitment/index/human_service' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'commitment/search/human_service' => array(
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
					'place_id' => 'select',
					'type' => 'select',
					'status' => 'select',
					'year' => 'contains',
					'account_name' => 'contains',
					'caption' => 'contains',
					'including_options_amount' => 'range',
			),
	),
	
	'commitment/list/human_service' => array(
			'place_id' => 'select',
			'type' => 'select',
			'status' => 'select',
			'year' => 'text',
			'account_name' => 'text',
			'caption' => 'text',
			'quantity' => 'number',
			'unit_price' => 'number',
			'amount' => 'number',
			'including_options_amount' => 'number',
			'update_time' => 'datetime',
	),
	
	'commitment/update/human_service' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),
	'commitment/group/human_service' => array(
			'year' => [],
			'status' => [],
			'invoice_date' => [],
			'caption' => [],
			'description' => [],
	),
		
	'commitment/invoice/human_service' => array(
			'header' => array(
					array(
							'format' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('account_name'),
					),
			),
			'description' => array(
					array(
							'left' => array('en_US' => 'Beneficiary', 'fr_FR' => 'Bénéficiaire'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('account_name'),
					),
					array(
							'left' => array('en_US' => 'Caption', 'fr_FR' => 'Libellé'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('caption'),
					),
					array(
							'left' => array('en_US' => 'Description', 'fr_FR' => 'Description'),
							'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
							'params' => array('description'),
					),
			),
	),

	'commitment/send-message/human_service' => array(
		'templates' => array(
			'generic' => array('definition' => 'commitment/template/generic'),
		),
	),

	'commitment/send-message/rental' => array(
		'templates' => array(
			'generic' => array('definition' => 'commitment/template/generic'),
		),
	),
	
	'commitment/template/generic' => array(
		'labels' => array(
			'en_US' => 'Generic',
			'fr_FR' => 'Générique',
		),
		'cci' => ['contact@p-pit.fr' => 'contact@p-pit.fr'],
		'from_mail' => 'contact@p-pit.fr',
		'from_name' => 'Le support P-Pit',
		'subject' => array(
			'text' => array('default' => '%s - Your invoice', 'fr_FR' => '%s - Votre facture'),
			'params' => array('place_caption'),
		),
		'body' => array(
			'text' => array(
				'default' => '<p>Hello,</p>
<p>We inform you that your online invoice is available: <a href="%s">Download your invoice</a>.</p>
<p>Should you need any additional information, please do not hesitate to contact us.</p>
',
				'fr_FR' => '<p>Bonjour,</p>
<p>Nous vous informons que votre facture est disponible : <a href="%s">T&eacute;l&eacute;charger votre facture</a>.</p>
<p>Nous vous en souhaitons bonne r&eacute;ception.</p>
',
			),
			'params' => array('invoice_route'),
		),
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
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'To be confirmed', 'fr_FR' => 'A confirmer'),
									'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
									'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
									'invoiced' => array('en_US' => 'Invoiced', 'fr_FR' => 'Facturé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'year' => array('definition' => 'commitment/property/year'),
					'place_id' => array('definition' => 'commitment/property/place_id'),
					'account_id' => array('definition' => 'commitment/property/account_id'),
					'account_name' => array('definition' => 'commitment/property/account_id'),
					'quantity' => array('definition' => 'commitment/property/quantity'),
					'unit_price' => array('definition' => 'commitment/property/unit_price'),
					'amount' => array('definition' => 'commitment/property/amount'),
					'name' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'description' => array(
							'definition' => 'inline',
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Description',
									'fr_FR' => 'Description',
							),
					),
					'property_1' => array(
							'definition' => 'inline',
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
							'definition' => 'inline',
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
							'definition' => 'inline',
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
							'definition' => 'inline',
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
							'definition' => 'inline',
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
							'definition' => 'inline',
							'type' => 'number',
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'invoice_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Invoice date',
									'fr_FR' => 'Date de facture',
							),
					),
					'update_time' => array('definition' => 'commitment/property/update_time'),
			),
			'todo' => array(
					'sales_manager' => array(
//							'status' => array('selector' => 'in', 'value' => array('new')),
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
					'status' => 'select',
					'property_10' => 'select',
					'year' => 'contains',
					'including_options_amount' => 'range',
					'name' => 'contains',
					'account_name' => 'contains',
					'property_1' => 'select',
					'property_2' => 'select',
					'property_3' => 'select',
					'property_4' => 'select',
					'property_5' => 'contains',
			),
	),
	
	'commitment/list/learning' => array(
			'place_id' => 'select',
			'property_10' => 'select',
			'year' => 'text',
			'status' => 'select',
			'account_name' => 'text',
			'caption' => 'text',
			'quantity' => 'number',
			'unit_price' => 'number',
			'including_options_amount' => 'number',
			'update_time' => 'datetime',
	),
	
	'commitment/update/learning' => array(
			'year' => array('mandatory' => true),
			'invoice_date' => array('mandatory' => true),
			'property_10' => array('mandatory' => false),
			'account_id' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
			'property_1' => array('mandatory' => false),
			'property_2' => array('mandatory' => false),
			'property_3' => array('mandatory' => false),
			'property_4' => array('mandatory' => false),
			'property_5' => array('mandatory' => false),
			'property_11' => array('mandatory' => false),
	),
		
	'commitmentTerm' => array(
			'statuses' => array(),
			'properties' => array(
					'name' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'status' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'expected' => array('fr_FR' => 'Attendu', 'en_US' => 'Expected'),
									'settled' => array('fr_FR' => 'Réglé', 'en_US' => 'Settled'),
									'collected' => array('fr_FR' => 'Encaissé', 'en_US' => 'Collected'),
									'invoiced' => array('fr_FR' => 'Facturé', 'en_US' => 'Invoiced'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'place_id' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
							),
							'labels' => array(
									'en_US' => 'Place',
									'fr_FR' => 'Etablissement',
							),
					),
					'caption' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'due_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Due date',
									'fr_FR' => 'Date d\'échéance',
							),
					),
					'settlement_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Settlement date',
									'fr_FR' => 'Date de règlement',
							),
					),
					'collection_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Collection date',
									'fr_FR' => 'Date d\'encaissement',
							),
					),
					'amount' => array(
							'definition' => 'inline',
							'type' => 'number',
							'minValue' => -99999999,
							'maxValue' => 99999999,
							'labels' => array(
									'en_US' => 'Amount',
									'fr_FR' => 'Montant',
							),
					),
					'means_of_payment' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'bank_card' => array('fr_FR' => 'CB', 'en_US' => 'Bank card'),
									'transfer' => array('fr_FR' => 'Virement', 'en_US' => 'Transfer'),
									'direct_debit' => array('fr_FR' => 'Prélèvement', 'en_US' => 'Direct debit'),
									'check' => array('fr_FR' => 'Chèque', 'en_US' => 'Check'),
									'cash' => array('fr_FR' => 'Espèces', 'en_US' => 'Cash'),
							),
							'labels' => array(
									'en_US' => 'Means of payment',
									'fr_FR' => 'Mode de règlement',
							),
					),
					'reference' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Reference',
									'fr_FR' => 'Référence',
							),
					),
					'comment' => array(
							'definition' => 'inline',
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Comment',
									'fr_FR' => 'Commentaire',
							),
					),
					'document' => array(
							'definition' => 'inline',
							'type' => 'dropbox',
							'labels' => array(
									'en_US' => 'Attachment',
									'fr_FR' => 'Justificatif',
							),
					),
					'commitment_caption' => array('definition' => 'commitment/property/caption'),
					'account_property_1' => array('definition' => 'core_account/generic/property/property_1'),
					'account_property_2' => array('definition' => 'core_account/generic/property/property_2'),
					'account_property_3' => array('definition' => 'core_account/generic/property/property_3'),
					'account_property_4' => array('definition' => 'core_account/generic/property/property_4'),
					'account_property_5' => array('definition' => 'core_account/generic/property/property_5'),
					'account_property_6' => array('definition' => 'core_account/generic/property/property_6'),
					'account_property_7' => array('definition' => 'core_account/generic/property/property_7'),
					'account_property_8' => array('definition' => 'core_account/generic/property/property_8'),
					'account_property_9' => array('definition' => 'core_account/generic/property/property_9'),
					'account_property_10' => array('definition' => 'core_account/generic/property/property_10'),
					'account_property_11' => array('definition' => 'core_account/generic/property/property_11'),
					'account_property_12' => array('definition' => 'core_account/generic/property/property_12'),
					'account_property_13' => array('definition' => 'core_account/generic/property/property_13'),
					'account_property_14' => array('definition' => 'core_account/generic/property/property_14'),
					'account_property_15' => array('definition' => 'core_account/p-pit-studies/property/property_15'),
					'account_property_16' => array('definition' => 'core_account/generic/property/property_16'),
			),
	),
	'commitmentTerm/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitmentTerm/search' => array(
			'title' => array('en_US' => 'Terms', 'fr_FR' => 'Echéances'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'properties' => array(
				'place_id' => ['multiple' => true],
				'name' => [],
				'status' => ['multiple' => true],
				'due_date' => [],
				'collection_date' => [],
				'amount' => [],
				'reference' => [],
				'comment' => [],
			),
	),
	'commitmentTerm/list' => array(
			'properties' => array(
				'name' => [],
				'status' => [],
				'due_date' => [],
				'collection_date' => [],
				'amount' => [],
			),
	),
	'commitmentTerm/detail' => array(
			'title' => array('en_US' => 'Term detail', 'fr_FR' => 'Détail de l\'échéance'),
			'displayAudit' => true,
	),
	'commitmentTerm/update' => array(
			'status' => ['mandatory' => true],
			'caption' => ['mandatory' => true],
			'due_date' => ['mandatory' => true],
			'settlement_date' => ['mandatory' => false],
			'collection_date' => ['mandatory' => false],
			'amount' => ['mandatory' => true],
			'means_of_payment' => ['mandatory' => false],
			'reference' => ['mandatory' => false],
			'comment' => ['mandatory' => false],
			'document' => ['mandatory' => false],
	),
	'commitmentTerm/group' => array(
		'status' => [],
		'caption' => [],
	),
	
	'commitmentTerm/debit' => array(
		'InitgPty/Nm' => '** 2.19 - Nom de l\'émetteur **',
		'Cdtr/Nm' => '** 2.19 - Nom du créancier **',
		'CdtrAcct/Id/IBAN' => '** 2.20 - IBAN du compte du créancier **',
		'CdtrSchmeId/Id/PrvtId/Othr/Id' => '** 2.66 - Identifiant créancier SEPA **',
		'DrctDbtTxInf/RgltryRptg/Dtls/Cd' => '** 2.79 - Code économique déclaration balance de paiements **',
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

		'core_account/sendMessage' => array(
				'templates' => array(
						'generic' => array('definition' => 'core_account/sendMessage/generic'),
				),
				'signature' => array(
					'definition' => 'inline',
					'body' => array(
						'en_US' => 'To be translated',
						'fr_FR' => '
<hr>
<div><a href="https://www.p-pit.fr"><img src="http://img.p-pit.fr/P-Pit/p-pit-advert.png" width="400" alt="P-Pit logo" /></a></div>
Le support P-Pit<br>
support@p-pit.fr
'
					),
				),
		),

		'core_account/sendMessage/generic' => array(
				'labels' => array(
						'en_US' => 'Generic',
						'fr_FR' => 'Générique',
				),
				'cci' => 'contact@p-pit.fr',
				'from_mail' => 'contact@p-pit.fr',
				'from_name' => 'noreply@p-pit.fr',
				'subject' => array('default' => 'Important message from P-Pit', 'fr_FR' => 'Message important de P-Pit'),
				'text' => array(
					'default' => '
<p>Hello %s,</p>
<p>We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr.</p>
<p>Best regards,</p>
<p>The P-Pit staff</p>
',
					'fr_FR' => '
<p>Bonjour %s,</p>
<p>Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr.</p>
<p>Bien cordialement,</p>
<p>L\'équipe P-Pit</p>
',
				),
				'params' => array('n_first'),
				'body' => '
<style>
        @font-face {
        font-family: "League Gothic";
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot");
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot?#iefix") format("embedded-opentype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
        font-weight: normal;
        font-style: normal;
    }
    
    @media only screen and (max-width: 480px) {
        @font-face {
            font-family: "League Gothic";
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
            font-weight: normal;
            font-style: normal;
        }

</style>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>
                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" style="font-family: arial, helvetica, sans-serif;" width="760">
                    <tbody>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="680">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" valign="top" width="680">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" height="40">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#ffffff" valign="top" width="40">&nbsp;</td>
                                            <td bgcolor="#ffffff" valign="top" width="600">
                                                <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
                                                    <tbody>
                                                        <tr>
                                                            <td style="line-height:24px;text-align:justify;font-size:16px; font-family: Georgia, Times New Roman, Times, serif; color:rgb(45,40,70);">
																%s
																%s
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td align="right" bgcolor="#ffffff" valign="top" width="38">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#eeeeee" colspan="3" height="15" width="682"><font style="color: rgb(51, 51, 51); font-family: arial, sans-serif; font-size: 10px; font-weight: normal;">P-Pit SAS - 25, rue du Faubourg du Temple - B&acirc;timent C - 75010 Paris<br /></font></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="40">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <p>&nbsp;</p>
            </td>
        </tr>
    </tbody>
</table>',
		),

		'core_account/notification' => array(
				'definition' => 'inline',
				'template' => array('definition' => 'core_account/notification/template'),
				'from_mail' => 'support@p-pit.fr',
				'from_name' => 'noreply@p-pit.fr',
				'signature' => array('definition' => 'customisation/esi/send-message/signature'),
		),
		
		'core_account/notification/template' => array(
				'subject' => array('en_US' => 'Current registration request', 'fr_FR' => 'Demande d\'inscription en cours'),
				'body' => array(
						'en_US' => '<p>Hello,</p>
<p>You have initiated a registration request on the web site %s that you have not been able to complete. We propose you to resume it by following this link: %s</p>
<p>Best regards,</p>
',
						'fr_FR' => '<p>Bonjour,</p>
<p>Vous avez initié une demande d\'inscription sur le site %s que vous n\'avez pas pu finaliser. Nous vous proposons de la reprendre en suivant ce lien : %s</p>
<p>Cordialement,</p>
',
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
			'core_account/search/title' => array(
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
			'core_account/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered in todo-list mode.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée en mode todo-list.</p>
',
			),
			'core_account/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'core_account/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'core_account/list/checkAll' => array(
					'en_US' => '
<h4>Check all</h4>
<p>This check-box allows to check at one time all the items of the list.</p>
					',
					'fr_FR' => '
<h4>Tout sélectionner</h4>
<p>Cette case à cocher permet de sélectionner d\'un coup tous les éléments de la liste.</p>
',
			),
			'core_account/list/groupedActions' => array(
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
			'core_account/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un compte</h4>
<p>Le bouton + permet l\'ajout d\un nouveau compte.</p>
<p>Les engagements liés à ce compte seront créés dans un second temps.</p>
<p>On peut ainsi gérer un regroupement des engagements par compte.</p>
					',
			),
			'core_account/add' => array(
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
			'core_account/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un compte</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un compte et aux engagements associés.</p>
					',
			),
			'core_account/business' => array(
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
