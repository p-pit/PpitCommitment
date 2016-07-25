<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitCommitment\Controller\Account' => 'PpitCommitment\Controller\AccountController',
        	'PpitCommitment\Controller\Commitment' => 'PpitCommitment\Controller\CommitmentController',
        	'PpitCommitment\Controller\CommitmentMessage' => 'PpitCommitment\Controller\CommitmentMessageController',
        	'PpitCommitment\Controller\OrderResponse' => 'PpitCommitment\Controller\OrderResponseController',
        	'PpitCommitment\Controller\OrderProduct' => 'PpitCommitment\Controller\OrderProductController',
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
        								'route' => '/update[/:id][/:act]',
        								'constraints' => array(
        										'id'     => '[0-9]*',
        								),
        								'defaults' => array(
        										'action' => 'update',
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
	        				'ppitSubscribe' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/ppit-subscribe',
	        								'defaults' => array(
	        										'action' => 'ppitSubscribe',
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
				array('route' => 'commitmentAccount', 'roles' => array('user')),
				array('route' => 'commitmentAccount/index', 'roles' => array('user')),
				array('route' => 'commitmentAccount/search', 'roles' => array('user')),
				array('route' => 'commitmentAccount/detail', 'roles' => array('user')),
				array('route' => 'commitmentAccount/delete', 'roles' => array('admin')),
				array('route' => 'commitmentAccount/export', 'roles' => array('user')),
            	array('route' => 'commitmentAccount/list', 'roles' => array('user')),
				array('route' => 'commitmentAccount/update', 'roles' => array('admin')),
            	array('route' => 'commitment', 'roles' => array('admin')),
            	array('route' => 'commitment/accountlist', 'roles' => array('user')),
            	array('route' => 'commitment/index', 'roles' => array('user')),
            	array('route' => 'commitment/search', 'roles' => array('user')),
            	array('route' => 'commitment/list', 'roles' => array('user')),
            	array('route' => 'commitment/export', 'roles' => array('user')),
            	array('route' => 'commitment/detail', 'roles' => array('user')),
            	array('route' => 'commitment/message', 'roles' => array('guest')),
            	array('route' => 'commitment/post', 'roles' => array('admin')),
            	array('route' => 'commitment/update', 'roles' => array('user')),
            	array('route' => 'commitment/delete', 'roles' => array(/*'admin'*/)),
            	array('route' => 'commitment/notify', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/download', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/index', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/ppitSubscribe', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/import', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/process', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/submit', 'roles' => array('admin')),
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
							'fr_FR' => 'Vendeur',
					),
			),
			'business_owner' => array(
					'show' => true,
					'labels' => array(
							'en_US' => 'Business owner',
							'fr_FR' => 'Gestion',
					),
			),
	),
	'ppitCommitmentDependencies' => array(
	),
);
