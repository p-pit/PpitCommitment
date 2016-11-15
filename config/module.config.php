<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitCommitment\Controller\Account' => 'PpitCommitment\Controller\AccountController',
        	'PpitCommitment\Controller\Commitment' => 'PpitCommitment\Controller\CommitmentController',
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
        						'export' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/export[/:type]',
        										'defaults' => array(
        												'action' => 'export',
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
	       						'put' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/put[/:email]',
        										'defaults' => array(
        												'action' => 'put',
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
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'register',
		        								),
		        						),
		        				),
	       		),
	       			'dataList' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/data-list[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'dataList',
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
/*	       			'list' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'list',
	                        ),
	                    ),
	                ),*/
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
        				'serviceAdd' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/service-add',
        								'defaults' => array(
        										'action' => 'serviceAdd',
        								),
        						),
        				),
        				'workflow' => array(
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
        				),
        				'try' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/try[/:product]',
        								'defaults' => array(
        										'action' => 'try',
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
        				'invoice' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/invoice[/:type][/:id]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'invoice',
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
				array('route' => 'commitmentAccount/index', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/search', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/detail', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/get', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/put', 'roles' => array('guest')),
            	array('route' => 'commitmentAccount/delete', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/export', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/list', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/update', 'roles' => array('sales_manager')),
				array('route' => 'commitmentAccount/updateUser', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/updateContact', 'roles' => array('sales_manager')),
            	array('route' => 'commitmentAccount/register', 'roles' => array('guest')),
            	array('route' => 'commitment', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/accountlist', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/index', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/search', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/list', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/accountList', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/export', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/detail', 'roles' => array('sales_manager', 'business_owner')),
            	array('route' => 'commitment/message', 'roles' => array('guest')),
            	array('route' => 'commitment/post', 'roles' => array('admin')),
            	array('route' => 'commitment/try', 'roles' => array('guest')),
            	array('route' => 'commitment/update', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateProduct', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateOption', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/updateTerm', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/serviceAdd', 'roles' => array('guest')),
            	array('route' => 'commitment/workflow', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/accept', 'roles' => array('accountant')),
            	array('route' => 'commitment/settle', 'roles' => array('accountant')),
            	array('route' => 'commitment/invoice', 'roles' => array('sales_manager', 'accountant')),
            	array('route' => 'commitment/paymentResponse', 'roles' => array('accountant')),
            	array('route' => 'commitment/delete', 'roles' => array('sales_manager')),
            	array('route' => 'commitment/notify', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/download', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/index', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/search', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/accountPost', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentList', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentGet', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/commitmentPost', 'roles' => array('guest')),
            	array('route' => 'commitmentMessage/paymentAutoresponse', 'roles' => array('guest')),
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

	'ppitRoles' => array(
			'PpitCommitment' => array(
					'accountant' => array(
							'show' => true,
							'labels' => array(
									'en_US' => 'Accountant',
									'fr_FR' => 'Comptable',
							),
					),
					'sales_manager' => array(
							'show' => true,
							'labels' => array(
									'en_US' => 'Sales manager',
									'fr_FR' => 'Gestion commerciale',
							),
					),
					'business_owner' => array(
							'show' => true,
							'labels' => array(
									'en_US' => 'Business owner',
									'fr_FR' => 'Gestion opérationnelle',
							),
					),
			),
	),

	'menus' => array(
			'p-pit-engagements' => array(
					'account' => array(
							'route' => 'commitmentAccount/index',
							'params' => array(),
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
	),
		
	'contact/perimeters' => array(
			'ppitCommitment' => array(
			),
	),
	
	'currentApplication' => 'ppitCommitment',
		
	'ppitCommitmentDependencies' => array(
	),

	'commitmentAccount' => array(
			'statuses' => array(),
			'properties' => array(
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'active' => array('en_US' => 'Active', 'fr_FR' => 'Actif'),
									'inactive' => array('en_US' => 'Inactive', 'fr_FR' => 'Inactif'),
									'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'customer_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
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
			),
			'order' => 'customer_name',
	),
	'commitmentAccount/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitmentAccount/search' => array(
			'title' => array('en_US' => 'Accounts', 'fr_FR' => 'Comptes'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'main' => array(
				'status' => 'select',
				'customer_name' => 'contains',
			),
			'more' => array(
				'email' => 'contains',
				'opening_date' => 'range',
				'closing_date' => 'range',
			),
	),
	'commitmentAccount/list' => array(
			'customer_name' => 'text',
	),
	'commitmentAccount/detail' => array(
			'title' => array('en_US' => 'Account detail', 'fr_FR' => 'Détail du compte'),
			'displayAudit' => true,
			'tabs' => array(),
	),
	'commitmentAccount/update' => array(
			'status' => array('mandatory' => true),
			'n_first' => array('mandatory' => true),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'opening_date' => array('mandatory' => true),
			'closing_date' => array('mandatory' => false),
	),
	'commitmentAccount/updateContact' => array(
			'n_title' => array('mandatory' => false),
			'n_first' => array('mandatory' => true),
			'n_last' => array('mandatory' => true),
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

	'commitment/types' => array(
			'type' => 'select',
			'modalities' => array(
					'rental' => array('en_US' => 'Rental', 'fr_FR' => 'Location'),
					'service' => array('en_US' => 'Service', 'fr_FR' => 'Prestation'),
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
					'customer_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'repository',
							'definition' => 'student/property/school_year',
							'labels' => array(
									'en_US' => 'School year',
									'fr_FR' => 'Année scolaire',
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
			'order' => 'customer_name ASC',
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
					'customer_name' => 'contains',
			),
	),

	'commitment/list' => array(
			'including_options_amount' => 'number',
			'status' => 'select',
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
	
	'commitment/rental' => array(
			'properties' => array(
			),
	),

	'commitment/detail/rental' => array(
			'title' => array('en_US' => 'Commitment detail', 'fr_FR' => 'Détail de l\'engagement'),
	),
		
	'commitment/update/rental' => array(
//			'caption' => array('mandatory' => true),
//			'description' => array('mandatory' => false),
	),

	'commitment/service' => array(
			'currencySymbol' => '€',
			'tax' => 'including',
			'properties' => array(
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
					'customer_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'repository',
							'definition' => 'student/property/school_year',
							'labels' => array(
									'en_US' => 'School year',
									'fr_FR' => 'Année scolaire',
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
	),

	'commitment/detail/service' => array(
			'title' => array('en_US' => 'Commitment detail', 'fr_FR' => 'Détail de l\'engagement'),
	),
		
	'commitment/update/service' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),
/*		
	'commitment' => array(
			'types' => array(
					'rental' => array(
							'labels' => array(
									'en_US' => 'Rental',
									'fr_FR' => 'Location',
							)
					),
					'service' => array(
							'labels' => array(
									'en_US' => 'Service offer',
									'fr_FR' => 'Prestation de service',
							)
					),
			),
			'messageTemplates' => array(
					'addTitle' => array(
							'en_US' => 'New order(s) %s',
							'fr_FR' => 'Nouvelle(s) commande(s) %s',
					),
					'addText' => array(
							'en_US' => 'Hello,
We inform you that orders listed below have been submitted and requires your confirmation. To proceed, please follow this links: %s.
New orders %s: %s
',
							'fr_FR' => 'Bonjour,
Nous vous informons que les commandes dont la liste suit ont été émises et doivent recevoir votre confirmation. Pour ce faire, veuillez suivre ce lien: %s.
Nouvelles commandes %s : %s
',
					),
					'confirmTitle' => array(
							'en_US' => 'Order(s) accepted %s',
							'fr_FR' => 'Commande(s) acceptée(s) %s',
					),
					'confirmText' => array(
							'en_US' => 'Hello,
We inform you that the orders listed below have been accepted. For more details, please follow this links: %s.
Accepted orders %s: %s
',
							'fr_FR' => 'Bonjour,
Nous vous informons que les commandes dont la liste suit ont été acceptées. Pour plus de détails, veuillez suivre ce lien: %s.
Commandes acceptées %s : %s
',
					),
					'rejectTitle' => array(
							'en_US' => 'Order(s) rejected %s',
							'fr_FR' => 'Commande(s) rejetée(s) %s',
					),
					'rejectText' => array(
							'en_US' => 'Hello,
We inform you that the orders listed below have been rejected. For more details, please follow this links: %s.
Rejected orders %s: %s
',
							'fr_FR' => 'Bonjour,
Nous vous informons que les commandes dont la liste suit ont été rejetées. Pour plus de détails, veuillez suivre ce lien: %s.
Commandes rejetées %s : %s
',
					),
					'registerTitle' => array(
							'en_US' => 'Order(s) registered %s',
							'fr_FR' => 'Commande(s) enregistrée(s) %s',
					),
					'registerText' => array(
							'en_US' => 'Hello,
We inform you that the orders listed below have been registered. For more details, please follow this links: %s.
Registered orders %s: %s
',
							'fr_FR' => 'Bonjour,
Nous vous informons que les commandes dont la liste suit ont été enregistrées. Pour plus de détails, veuillez suivre ce lien: %s.
Commandes enregistrées %s : %s
',
					),
			),
	),

	'commitment' => array(
			'statuses' => array(
					'new' => array(
							'labels' => array(
									'en_US' => 'To be approved',
									'fr_FR' => 'A valider',
							)
					),
					'approved' => array(
							'labels' => array(
									'en_US' => 'Approved',
									'fr_FR' => 'Validé',
							)
					),
					'settled' => array(
							'labels' => array(
									'en_US' => 'Settled',
									'fr_FR' => 'Réglé',
							)
					),
					'commissioned' => array(
							'labels' => array(
									'en_US' => 'Commissioned',
									'fr_FR' => 'En service',
							)
					),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
			),
			'properties' => array(
					'status' => array(
							'type' => 'select',
							'modalities' => array(
								'new' => array('en_US' => 'To be confirmed', 'fr_FR' => 'A confirmer'),
								'confirmed' => array('en_US' => 'Confirmed', 'fr_FR' => 'Confirmé'),
								'settled' => array('en_US' => 'Settled', 'fr_FR' => 'Réglé'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'customer_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'School year',
									'fr_FR' => 'Année scolaire',
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
			'order' => 'school_year DESC',
			'actions' => array(
					'' => array(
						'currentStatuses' => array(),
						'label' => array('en_US' => 'Update', 'fr_FR' => 'Modifier'),
						'properties' => array(
								'account_id' => 'update',
//								'subscription_id' => 'update',
								'caption' => 'update',
								'description' => 'update',
								'quantity' => 'update',
								'unit_price' => 'update',
								'amount' => 'update',
								'identifier' => 'update',
								'comment' => 'update',
								'product_identifier' => 'update',
						),
					),
					'update' => array(
						'currentStatuses' => array('new' => null),
						'glyphicon' => 'glyphicon-edit',
						'label' => array('en_US' => 'Update', 'fr_FR' => 'Modifier'),
						'properties' => array(
								'status' => 'display',
								'account_id' => 'update',
//								'subscription_id' => 'update',
								'caption' => 'update',
								'description' => 'update',
								'quantity' => 'update',
								'unit_price' => 'update',
								'amount' => 'update',
								'identifier' => 'update',
								'comment' => 'update',
								'product_identifier' => 'update',
						),
					),
					'delete' => array(
						'currentStatuses' => array('new' => null),
						'targetStatus' => 'deleted',
						'glyphicon' => 'glyphicon-trash',
						'label' => array('en_US' => 'Delete', 'fr_FR' => 'Supprimer'),
						'properties' => array(
						),
					),
					'approve' => array(
						'currentStatuses' => array('new' => null),
						'targetStatus' => 'approved',
						'label' => array('en_US' => 'Approve', 'fr_FR' => 'Valider'),
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
					'commission' => array(
						'currentStatuses' => array('settled' => null),
						'targetStatus' => 'commissioned',
						'label' => array('en_US' => 'Commission', 'fr_FR' => 'Mettre en service'),
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
					'status' => 'select',
					'including_options_amount' => 'range',
					'customer_name' => 'contains',
			),
	),

	'commitment/list' => array(
			'including_options_amount' => 'number',
			'status' => 'select',
	),
		
	'commitment/update' => array(
			'caption' => array('mandatory' => true),
			'description' => array('mandatory' => false),
	),
	
	'commitment/service' => array(
			'properties' => array(
					'type' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Product',
									'fr_FR' => 'Produit',
							),
					),
					'due_date' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Due date',
									'fr_FR' => 'Echéance',
							),
					),
			),
			'statuses' => array(
					'new' => array(
							'labels' => array(
									'en_US' => 'To be confirmed',
									'fr_FR' => 'A confirmer',
							)
					),
					'confirmed' => array(
							'labels' => array(
									'en_US' => 'Confirmed',
									'fr_FR' => 'Confirmé',
							)
					),
					'rejected' => array(
							'labels' => array(
									'en_US' => 'Rejected',
									'fr_FR' => 'Rejeté',
							)
					),
					'delivered' => array(
							'labels' => array(
									'en_US' => 'Delivered',
									'fr_FR' => 'Livré',
							)
					),
					'commissioned' => array(
							'labels' => array(
									'en_US' => 'To invoice',
									'fr_FR' => 'A facturer',
							)
					),
					'invoiced' => array(
							'labels' => array(
									'en_US' => 'Invoiced',
									'fr_FR' => 'Facturé',
							)
					),
					'settled' => array(
							'labels' => array(
									'en_US' => 'Settled',
									'fr_FR' => 'Réglé',
							)
					),
			),
			'deadlines' => array(
					'retraction' => array('status' => 'new', 'period' => 5, 'unit' => 'day'),
					'shipment' => array('status' => 'new', 'period' => 10, 'unit' => 'day'),
					'delivery' => array('status' => 'new', 'period' => 13, 'unit' => 'day'),
					'commissioning' => array('status' => 'new', 'period' => 15, 'unit' => 'day'),
					'invoice' => array('status' => 'commissioned', 'period' => 0),
					'settlement' => array('status' => 'commissioned', 'period' => 0),
			),
			'todo' => array(
					'sales_manager' => array(
							'status' => array('selector' => 'in', 'value' => array('new')),
					),
					'business_owner' => array(
							'status' => array('selector' => 'in', 'value' => array('new', 'registered', 'delivered', 'commissioned')),
					),
			),
			'actions' => array(
					'' => array(
						'currentStatuses' => array(),
						'label' => array('en_US' => 'Update', 'fr_FR' => 'Modifier'),
						'properties' => array(
								'status' => 'display',
								'account_id' => 'update',
								'subscription_id' => 'update',
								'caption' => 'update',
								'description' => 'update',
								'amount' => 'update',
								'identifier' => 'update',
								'quotation_identifier' => 'update',
								'commitment_date' => 'update',
								'expected_delivery_date' => 'update',
								'due_date' => 'update',
								'expected_settlement_date' => 'update',
								'comment' => 'update',
						),
					),
					'update' => array(
						'currentStatuses' => array('new' => null),
						'glyphicon' => 'glyphicon-edit',
						'label' => array('en_US' => 'Update', 'fr_FR' => 'Modifier'),
						'properties' => array(
								'status' => 'display',
								'account_id' => 'update',
								'subscription_id' => 'update',
								'caption' => 'update',
								'description' => 'update',
								'amount' => 'update',
								'identifier' => 'update',
								'quotation_identifier' => 'update',
								'commitment_date' => 'update',
								'requested_delivery_date' => 'update',
								'comment' => 'update',
						),
					),
					'delete' => array(
						'currentStatuses' => array('new' => null),
						'targetStatus' => 'deleted',
						'glyphicon' => 'glyphicon-trash',
						'label' => array('en_US' => 'Delete', 'fr_FR' => 'Supprimer'),
						'properties' => array(
						),
					),
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
					'deliver' => array(
						'currentStatuses' => array('confirmed' => null),
						'targetStatus' => 'delivered',
						'label' => array('en_US' => 'Deliver', 'fr_FR' => 'Livrer'),
						'properties' => array(
						),
					),
					'settle' => array(
						'currentStatuses' => array('delivered' => null),
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
					'renew' => array(
						'currentStatuses' => array('invoiced' => null),
						'targetStatus' => 'renewed',
						'label' => array('en_US' => 'Renew', 'fr_FR' => 'Renouveller'),
						'properties' => array(
						),
					),
			),
	),*/
		
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
									'expected' => array('fr_FR' => 'Facturé', 'en_US' => 'Invoiced'),
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
									'bank_card' => array('fr_FR' => 'Carte bancaire', 'en_US' => 'Bank card'),
									'transfer' => array('fr_FR' => 'Virement', 'en_US' => 'Transfer'),
									'check' => array('fr_FR' => 'Chèque', 'en_US' => 'Check'),
									'cash' => array('fr_FR' => 'Espèces', 'en_US' => 'Cash'),
							),
							'labels' => array(
									'en_US' => 'Means of payment',
									'fr_FR' => 'Mode de règlement',
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
			),
			'more' => array(
				'due_date' => 'range',
				'amount' => 'range',
				'caption' => 'contains',
				'means_of_payment' => 'select',
			),
	),
	'commitmentTerm/list' => array(
			'name' => 'text',
			'status' => 'select',
			'due_date' => 'date',
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
			'document' => array('mandatory' => false),
	),
	'commitmentMessage' => array(
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
			'importTypes' => array(),
	),
	'commitment/accountList' => array(
			'title' => array('en_US' => 'Subscriptions', 'fr_FR' => 'Souscriptions'),
			'addRoute' => 'commitment/update',
			'glyphicons' => array(
				'commitment/update' => array(
						'labels' => array('en_US' => 'Update', 'fr_FR' => 'Modifier'),
						'glyphicon' => 'glyphicon-edit',
				),
				'commitment/delete' => array(
						'labels' => array('en_US' => 'Delete', 'fr_FR' => 'Supprimer'),
						'glyphicon' => 'glyphicon-trash',
				),
			),
			'properties' => array(
				'caption' => 'text',
				'property_1' => 'text',
			),
			'anchors' => array(
				'document' => array(
						'type' => 'nav',
						'labels' => array('en_US' => 'Documents', 'fr_FR' => 'Documents'),
						'entries' => array(
						),
				),
				'bill' => array(
						'type' => 'btn',
						'labels' => array('en_US' => 'Bill', 'fr_FR' => 'Facture'),
				),
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
							
Link to P-PIT order site : https://www.p-pit.fr/public/product/%s
					
(*) Your current P-PIT Commitments reserve rises %s units. Your next monthly consumption is estimated up to now to %s units, estimation based on the current active subscriptions.

We hope that our services are giving you full satisfaction. Plesase send your requests or questions to the P-PIT support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-PIT staff
',
							'fr_FR' => 'Bonjour %s,
							
Votre réserve de crédits P-PIT Engagements disponibles pour %s est bientôt épuisée (*). 
Pour ne pas risquer de subir des restrictions à l\'utilisation, vous pouvez dès à présent renouveller en ligne votre souscription pour la durée que vous souhaitez.
Notre conseil : Ayez l\'esprit tranquille en renouvelant pour un an.

Lien vers le site de commande P-PIT : https://www.p-pit.fr/public/product/%s

(*) Votre réserve actuelle P-PIT Engagements est de %s unités. Votre prochain décompte mensuel est estimé à ce jour à %s unités, estimation basée sur le nombre de dossiers actifs à ce jour.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-PIT : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-PIT
',
					),
/*					'consumeCreditTitle' => array(
							'en_US' => 'Monthly P-PIT Commitments credits consumption report',
							'fr_FR' => 'Rapport mensuel de consommation de crédits P-PIT Engagements',
					),
					'consumeCreditText' => array(
							'en_US' => 'Hello %s,
							
Please note that the monthly count of P-PIT Commitments credits has occurred on %s. Given the current %s active subscriptions, %s units have been consumed. Your new P-PIT Commitments reserve rises %s units.

We hope that our services are giving you full satisfaction. Plesase send your requests or questions to the P-PIT support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-PIT staff
',
							'fr_FR' => 'Bonjour %s,
							
Veuillez noter que le décompte mensuel de crédits P-PIT Engagements a été effectué en date du %s. Compte tenu du nombre de dossiers %s actifs à ce jour, %s unités ont été décomptées. Votre nouvelle réserve P-PIT Engagements est de %s unités.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-PIT : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-PIT
',
					),*/
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
);
