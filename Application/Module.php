<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model AS Application_Model;
use Admin\Model AS Admin_Model;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	/*public function init(MvcEvent $e)
	{
	    $events = $e->getApplication()->getEventManager();
	    $sharedEvents = $events->getSharedManager();
	    $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
	        // fired when an ActionController under the namespace is dispatched.
	        $controller = $e->getTarget();
	        $routeMatch = $e->getRouteMatch();
	        $routeName = $routeMatch->getMatchedRouteName();
	        $controller->layout('application/layout/layout');
	    }, 100);
	}*/
	public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $sharedEvents = $eventManager->getSharedManager();
	    $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
	        /* @var $e \Zend\Mvc\MvcEvent */
	        // fired when an ActionController under the namespace is dispatched.
	        $controller = $e->getTarget();
	        $routeMatch = $e->getRouteMatch();
	        /* @var $routeMatch \Zend\Mvc\Router\RouteMatch */
	        $routeName = $routeMatch->getMatchedRouteName();
	        $controller->layout('application/layout');
	    }, 100);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\MemberTable' =>  function($sm) {
                	$tableGateway = $sm->get('MemberTableGateway2');
                	$table = new Application_Model\MemberTable($tableGateway);
                	return $table;
                },
                'MemberTableGateway2' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Application_Model\Member());
                	return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\ArticleTable' =>  function($sm) {
                    $tableGateway = $sm->get('ArticleTableGateway2');
                    $table = new Application_Model\ArticleTable($tableGateway);
                    return $table;
                },
                'ArticleTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Article());
                    return new TableGateway('article', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CommentTable' =>  function($sm) {
                    $tableGateway = $sm->get('CommentTableGateway2');
                    $table = new Application_Model\CommentTable($tableGateway);
                    return $table;
                },
                'CommentTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Comment());
                    return new TableGateway('comment', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CategoryTable' =>  function($sm) {
                    $tableGateway = $sm->get('CategoryTableGateway2');
                    $table = new Application_Model\CategoryTable($tableGateway);
                    return $table;
                },
                'CategoryTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Category());
                    return new TableGateway('category', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\PageTable' =>  function($sm) {
                    $tableGateway = $sm->get('PageTableGateway');
                    $table = new Application_Model\PageTable($tableGateway);
                    return $table;
                },
                'PageTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Page());
                    return new TableGateway('page', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\TemplateTable' =>  function($sm) {
                    $tableGateway = $sm->get('TemplateTableGateway');
                    $table = new Application_Model\TemplateTable($tableGateway);
                    return $table;
                },
                'TemplateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Template());
                    return new TableGateway('template', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CategoryArticleTable' =>  function($sm) {
                	$tableGateway = $sm->get('CategoryArticleTableGateway2');
                	$table = new Application_Model\CategoryArticleTable($tableGateway);
                	return $table;
                },
                'CategoryArticleTableGateway2' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Application_Model\CategoryArticle());
                	return new TableGateway('category_article', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CompanyTable' =>  function($sm) {
                    $tableGateway = $sm->get('CompanyTableGateway2');
                    $table = new Application_Model\CompanyTable($tableGateway);
                    return $table;
                },
                'CompanyTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Company());
                    return new TableGateway('company', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CompanyFeedTable' =>  function($sm) {
                    $tableGateway = $sm->get('CompanyFeedTableGateway2');
                    $table = new Application_Model\CompanyFeedTable($tableGateway);
                    return $table;
                },
                'CompanyFeedTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\CompanyFeed());
                    return new TableGateway('company_data_feeds', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\FollowTable' =>  function($sm) {
                    $tableGateway = $sm->get('FollowTableGateway2');
                    $table = new Application_Model\FollowTable($tableGateway);
                    return $table;
                },
                'FollowTableGateway2' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application_Model\Follow());
                    return new TableGateway('follow', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\MsgTable' =>  function($sm) {
                	$tableGateway = $sm->get('MsgTableGateway2');
                	$table = new Application_Model\MsgTable($tableGateway);
                	return $table;
                },
                'MsgTableGateway2' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Application_Model\Msg());
                	return new TableGateway('msg', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CuratedTable' =>  function($sm) {
                	$tableGateway = $sm->get('CuratedTableGateway2');
                	$table = new Application_Model\CuratedTable($tableGateway);
                	return $table;
                },
                'CuratedTableGateway2' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Application_Model\Curated());
                	return new TableGateway('curated', $dbAdapter, null, $resultSetPrototype);
                },
            ),	
        );
    }
    
    public function getViewHelperConfig()
    {
    	return array(
    			'factories' => array(
    					'common' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\Common;
    						return $viewHelper;
    					},
    					'pageLinks' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\pageLinks;
    						$viewHelper->setPageTable($locator);
    						return $viewHelper;
    					},
    					'getExcerpt' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\getExcerpt;
    						return $viewHelper;
    					},
    					'getPastTimeFormat' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\getPastTimeFormat;
    						return $viewHelper;
    					},
    					'getUserAllowedStatus' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\getUserAllowedStatus;
    						return $viewHelper;
    					},
    					'userDisplayName' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\userDisplayName;
    						return $viewHelper;
    					},
    					'userMsgCount' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\userMsgCount;
    						$viewHelper->setMsgTable($locator);
    						return $viewHelper;
    					},
    					'getCategoryLinks' => function ($sm) {
    						$locator = $sm->getServiceLocator();
    						$viewHelper = new View\Helper\getCategoryLinks;
    						return $viewHelper;
    					},
    			),
    	);
    
    }
    
    public function getControllerPluginConfig()
    {
    	return array(
    			'factories' => array(
    					'firstPlugin' => function ($sm) {
    						$serviceLocator = $sm->getServiceLocator();
    						$controllerPlugin = new Controller\Plugin\firstPlugin;
    						return $controllerPlugin;
    					},
    					'loginAndPermissionsCheck' => function ($sm) {
    						$serviceLocator = $sm->getServiceLocator();
    						$controllerPlugin = new Controller\Plugin\loginAndPermissionsCheck;
    						$controllerPlugin->setSerManager($sm);
    						$controllerPlugin->setSerLocator($serviceLocator);
    						return $controllerPlugin;
    					},
    			),
    	);
    }
}
