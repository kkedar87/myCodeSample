<?php
return array(
    'controllers' => array(
        'invokables' => array(
			'Application\Controller\Article' 	=> 'Application\Controller\ArticleController',
			'Application\Controller\Index' 		=> 'Application\Controller\IndexController',
			'Application\Controller\Member' 	=> 'Application\Controller\MemberController',
        	'Application\Controller\Runtime'	=> 'Application\Controller\RuntimeController',
        	'Application\Controller\Company'	=> 'Application\Controller\CompanyController',
       		'Application\Controller\Blog'		=> 'Application\Controller\BlogController',
		),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                	'constraints' => array(
                			'controller' => 'Application\Controller\Index',
                			'action' => 'index',
                	),
                    'defaults' => array(
						'controller' => 'Application\Controller\Index',
                        //'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),

            'frontarticle' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/article[/:alias]',
                    'constraints' => array(
                        'action' => 'article',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'article',
                    ),
                ),
            ),
            'frontloginsuccess' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/loginsuccess',
                    'constraints'	=> array(
            				//'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
            				//'keywords'	=> '',
            		),
            		'defaults' 		=> array(
            				'controller'	=> 'Application\Controller\Member',
            				'action'		=> 'loginsuccess',
            		),
                ),
            ),
            'frontregistersuccess' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    => '/registersuccess[/:user_id]',
            				'constraints'	=> array(
            						//'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
            						//'keywords'	=> '',
            				),
            				'defaults' 		=> array(
            						'controller'	=> 'Application\Controller\Member',
            						'action'		=> 'registersuccess',
            				),
            		),
            ),
            'frontmemeber' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user[/:action][/:keywords][/:param2]',
                    'constraints'	=> array(
            				//'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
            				//'keywords'	=> '',
            		),
            		'defaults' 		=> array(
            				'controller'	=> 'Application\Controller\Member',
            				'action'		=> 'portfolio',
            		),
                ),
            ),
            'frontportfolio' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    => '/user[/:username]',
            				'constraints' => array(
            						'action' => 'add',
            				),
            				'defaults' => array(
            						'controller' => 'Application\Controller\Member',
            						'action'     => 'portfolio',
            				),
            		),
            ),
            'frontcompany' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/symbol[/:symbolname]',
                    'constraints' => array(
                        'action' => 'company',
                        //'symbolname'     => '',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Company',
                        'action'     => 'company',
                    ),
                ),
            ),
           
            'frontsearchresult'	=> array(
            		'type' 		=> 'segment',
            		'options'	=> array(
            			'route'			=> '/search[/:type][/:keywords]',
            			'constraints'	=> array(
            					'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
            					//'keywords'	=> '',
            			),
            			'defaults' 		=> array(
            					'controller'	=> 'Application\Controller\Index',
            					'action'		=> 'search',
            			),
            	),	
            ),
            'frontsearch'	=> array(
            		'type' 		=> 'segment',
            		'options'	=> array(
            			'route'			=> '/rt[/:action][/:keywords][/:keywords2]',
            			'constraints'	=> array(
            					'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
            					//'keywords'	=> '',
            			),
            			'defaults' 		=> array(
            					'controller'	=> 'Application\Controller\Runtime',
            					'action'		=> 'search',
            			),
            	),	
            ),
            'frontcomment' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/discuss[/:alias]',
                    'constraints' => array(
                        'action' => 'comment',
            			'alias'     => '[a-zA-Z0-9_-][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Article',
                        'action'     => 'comment',
                    ),
                ),
            ),
            'frontarticles' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/articles[/:action][/:article_id][/:param2][/:param3][/:param4]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Article',
                        'action'     => 'all',
                    ),
                ),
            ),
            'frontblogs' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    => '/blogs[/:action][/:param1][/:param2][/:param3][/:param4]',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            				),
            				'defaults' => array(
            						'controller' => 'Application\Controller\Blog',
            						'action'     => 'all',
            				),
            		),
            ),
            'registerthanks' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/thankyou',
                    'constraints' => array(
                        'action' => 'registersuccess',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Member',
                        'action'     => 'registersuccess',
                    ),
                ),
            ),
            'frontpage' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    => '/page[/:alias]',
            				'constraints' => array(
            						'action' => 'singlepage',
            						'alias'     => '[a-zA-Z0-9_-][a-zA-Z0-9_-]*',
            				),
            				'defaults' => array(
            						'controller' => 'Application\Controller\Index',
            						'action'     => 'singlepage',
            				),
            		),
            ),
      	),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'application/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' 	  => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'application' => __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
