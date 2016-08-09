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
		        				'register' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/register',
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
        				'subscribe' => array(
        						'type' => 'segment',
        						'options' => array(
        								'route' => '/subscribe[/:product]',
        								'defaults' => array(
        										'action' => 'subscribe',
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
				array('route' => 'commitmentAccount/register', 'roles' => array('guest')),
            	array('route' => 'commitment', 'roles' => array('admin')),
            	array('route' => 'commitment/accountlist', 'roles' => array('user')),
            	array('route' => 'commitment/index', 'roles' => array('user')),
            	array('route' => 'commitment/search', 'roles' => array('user')),
            	array('route' => 'commitment/list', 'roles' => array('user')),
            	array('route' => 'commitment/accountList', 'roles' => array('user')),
            	array('route' => 'commitment/export', 'roles' => array('user')),
            	array('route' => 'commitment/detail', 'roles' => array('user')),
            	array('route' => 'commitment/message', 'roles' => array('guest')),
            	array('route' => 'commitment/post', 'roles' => array('admin')),
            	array('route' => 'commitment/subscribe', 'roles' => array('guest')),
            	array('route' => 'commitment/update', 'roles' => array('user')),
            	array('route' => 'commitment/delete', 'roles' => array(/*'admin'*/)),
            	array('route' => 'commitment/notify', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/download', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/index', 'roles' => array('admin')),
            	array('route' => 'commitmentMessage/search', 'roles' => array('admin')),
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

	'menus' => array(
			'p-pit-engagements' => array(
					'rental' => array(
							'action' => 'Commitment',
							'route' => 'commitment/index',
							'params' => array('type' => 'rental'),
							'urlParams' => array(),
							'label' => array(
									'en_US' => 'Rental',
									'fr_FR' => 'Location',
							),
					),
					'service' => array(
							'action' => 'Commitment',
							'route' => 'commitment/index',
							'params' => array('type' => 'service'),
							'urlParams' => array(),
							'label' => array(
									'en_US' => 'Service offer',
									'fr_FR' => 'Prestation de service',
							),
					),
					'product' => array(
							'action' => 'Product',
							'route' => 'product/index',
							'params' => array('type' => 'rental'),
							'urlParams' => array(),
							'label' => array(
									'en_US' => 'Catalogue',
									'fr_FR' => 'Catalogue',
							),
					),
					'account' => array(
							'action' => 'Account',
							'route' => 'commitmentAccount/index',
							'params' => array(),
							'urlParams' => array(),
							'label' => array(
									'en_US' => 'Accounts',
									'fr_FR' => 'Comptes',
							),
					),
					'commitmentMessage' => array(
							'action' => 'CommitmentMessage',
							'route' => 'commitmentMessage/index',
							'params' => array('type' => ''),
							'urlParams' => array(),
							'label' => array(
									'en_US' => 'Messages',
									'fr_FR' => 'Messages',
							),
					),
			),
	),
		
	'commitmentAccount' => array(
			'statuses' => array(),
			'properties' => array(
					'customer_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Name',
									'fr_FR' => 'Nom',
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
					'property_1' => array(
							'type' => 'select',
							'modalities' => array(
									'Basketball' => array('fr_FR' => 'Basketball'),
									'Equitation' => array('fr_FR' => 'Equitation'),
									'Football' => array('fr_FR' => 'Football'),
									'Golf' => array('fr_FR' => 'Golf'),
									'Tennis' => array('fr_FR' => 'Tennis'),
							),
							'labels' => array(
									'en_US' => 'Sport',
									'fr_FR' => 'Sport',
							),
					),
					'property_2' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Emergency phone',
									'fr_FR' => 'Tél. urgence',
							),
					),
					'property_3' => array(
							'type' => 'photo',
							'labels' => array(
									'en_US' => '',
									'fr_FR' => '',
							),
					),
					'property_4' => array(
							'type' => 'select',
							'modalities' => array(
									'6e' => array('fr_FR' => '6e'),
									'5e' => array('fr_FR' => '5e'),
									'4e' => array('fr_FR' => '4e'),
									'3e' => array('fr_FR' => '3e'),
									'2nde' => array('fr_FR' => '2nde'),
									'1ère' => array('fr_FR' => '1ère'),
									'Term.' => array('fr_FR' => 'Term.'),
							),
							'labels' => array(
									'en_US' => 'Class',
									'fr_FR' => 'Classe',
							),
					),
					'property_5' => array(
							'type' => 'select',
							'modalities' => array(
									'S' => array('fr_FR' => 'S'),
									'ES' => array('fr_FR' => 'ES'),
									'STMG' => array('fr_FR' => 'STMG'),
							),
							'labels' => array(
									'en_US' => 'Specialty',
									'fr_FR' => 'Spécialité',
							),
					),
					'property_6' => array(
							'type' => 'select',
							'modalities' => array(
									'Interne' => array('fr_FR' => 'Interne'),
									'Externe' => array('fr_FR' => 'Externe'),
									'Weekend' => array('fr_FR' => 'Weekend'),
									'Dimanche' => array('fr_FR' => 'Dimanche'),
							),
							'labels' => array(
									'en_US' => 'Boarding-school',
									'fr_FR' => 'Internat',
							),
					),
			),
	),
	'commitmentAccount/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitmentAccount/search' => array(
			'title' => array('en_US' => 'Students', 'fr_FR' => 'Eleves'),
			'todoTitle' => array('en_US' => 'registered', 'fr_FR' => 'inscrits'),
			'main' => array(
				'property_1' => 'select',
				'customer_name' => 'contains',
				'property_4' => 'select',
			),
			'more' => array(
				'place_id' => 'select',
				'opening_date' => 'range',
				'closing_date' => 'range',
			),
	),
	'commitmentAccount/list' => array(
			'property_1' => 'image',
			'property_3' => 'photo',
			'customer_name' => 'text',
			'property_2' => 'phone',
	),
	'commitmentAccount/detail' => array(
			'title' => array('en_US' => 'Account detail', 'fr_FR' => 'Détail du compte'),
			'displayAudit' => true,
	),
	'commitmentAccount/update' => array(
			'n_first' => array('mandatory' => true),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'place_id' => array('mandatory' => true),
			'opening_date' => array('mandatory' => true),
			'closing_date' => array('mandatory' => false),
	),
	'commitmentAccount/register' => array(
			'customer_name' => array('mandatory' => true),
			'n_first' => array('mandatory' => true),
			'n_last' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
	),
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
					'rental' => array(
						'invoice' => array('status' => 'new', 'period' => 0),
						'settlement' => array('status' => 'new', 'period' => 0),
						'renewal' => array('status' => 'new', 'period' => 1, 'unit' => 'month'),
					),
					'service' => array(
						'retraction' => array('status' => 'new', 'period' => 5, 'unit' => 'day'),
						'shipment' => array('status' => 'new', 'period' => 10, 'unit' => 'day'),
						'delivery' => array('status' => 'new', 'period' => 13, 'unit' => 'day'),
						'commissioning' => array('status' => 'new', 'period' => 15, 'unit' => 'day'),
						'invoice' => array('status' => 'commissioned', 'period' => 0),
						'settlement' => array('status' => 'commissioned', 'period' => 0),
					),
			),
			'todo' => array(
					'rental' => array(
							'sales_manager' => array(
									'status' => array('selector' => 'equalTo', 'value' => 'new'),
							),
							'business_owner' => array(
									'status' => array('selector' => 'in', 'value' => array('new', 'registered', 'delivered', 'commissioned')),
							),
					),
					'service' => array(
							'sales_manager' => array(
									'status' => array('selector' => 'equalTo', 'value' => 'new'),
							),
							'business_owner' => array(
									'status' => array('selector' => 'in', 'value' => array('new', 'registered', 'delivered', 'commissioned')),
							),
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
			'outputMessages' => array(),
			'importTypes' => array(),
			'messageTemplates' => array(),
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
					'consumeCreditTitle' => array(
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
					),
			),
	),
	'commitment/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitment/subscribe' => array(
			'property_1' => array('mandatory' => true),
			'due_date' => array('mandatory' => false, 'disabled' => true),
	),
	'commitmentMessage/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
);
