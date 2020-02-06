<?php
namespace Application\Controller;

use Application\Form\FlightrequestForm;
use Application\Model\Flightrequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Session\Config\StandardConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
	protected $memeberTable;
	protected $articleTable;
	protected $pageTable;
	protected $templateTable;
	protected $categoryTable;
	protected $categoryArticleTable;
	protected $commentTable;
	protected $followTable;
	protected $curatedTable;
	
	protected $roles;
	protected $user_id;
	protected $user_role;
	public function __construct()
	{
		global $u_roles;
		$this->roles = $u_roles;
		$this->user_id = 0;
		$this->user_role = 0;
	}
	public function getArticleTable()
    {
        if (!$this->articleTable) {
            $sm = $this->getServiceLocator();
            $this->articleTable = $sm->get('Application\Model\ArticleTable');
        }
        return $this->articleTable;
    }
    
    public function getCuratedTable()
    {
    	if (!$this->curatedTable) {
    		$sm = $this->getServiceLocator();
    		$this->curatedTable = $sm->get('Application\Model\CuratedTable');
    	}
    	return $this->curatedTable;
    }
    
	public function getPageTable()
    {
        if (!$this->pageTable) {
            $sm = $this->getServiceLocator();
            $this->pageTable = $sm->get('Application\Model\PageTable');
        }
        return $this->pageTable;
    }

	public function getTemplateTable()
    {
        if (!$this->templateTable) {
            $sm = $this->getServiceLocator();
            $this->templateTable = $sm->get('Application\Model\TemplateTable');
        }
        return $this->templateTable;
    }
    
    public function getMemberTable()
    {
        if (!$this->memeberTable) {
            $sm = $this->getServiceLocator();
            $this->memeberTable = $sm->get('Application\Model\MemberTable');
        }
        return $this->memeberTable;
    }

    public function getCategoryTable()
    {
        if (!$this->categoryTable) {
            $sm = $this->getServiceLocator();
            $this->categoryTable = $sm->get('Application\Model\CategoryTable');
        }
        return $this->categoryTable;
    }

    public function getCategoryArticleTable()
    {
        if (!$this->categoryArticleTable) {
            $sm = $this->getServiceLocator();
            $this->categoryArticleTable = $sm->get('Application\Model\CategoryArticleTable');
        }
        return $this->categoryArticleTable;
    }

    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('Application\Model\CommentTable');
        }
        return $this->commentTable;
    }
    
    public function getFollowTable()
    {
    	if(!$this->followTable)
    	{
    		$sm = $this->getServiceLocator();
    		$this->followTable = $sm->get('Application\Model\FollowTable');
    	}
    	return $this->followTable;
    }
    
    
    /******************************************
     * Purpose: It is used to handle the home page request
     * Created By: Kedar
     * Created On: May 11, 2014 
     * Modified By: Kedar
     * Modified On: Jun 17, 2014
     */
    public function indexAction()
    {
    	/*************** START - Check User permission for the controller/action ****************/
    	$controller = $this->params()->fromRoute('controller', '');
    	$action = $this->params()->fromRoute('action', '');
    	if ($this->zfcUserAuthentication()->hasIdentity()) {
    		$this->user_role	= $this->zfcUserAuthentication()->getIdentity()->getRole();
    		$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
    	}
    	if(!$this->roles[$this->user_role][$controller][$action])
    	{
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}
    	/*************** END   - Check User permission for the controller/action ****************/
    	
    	$params = array(
        			'limit' => ARTICLES_ON_HOME_PAGE,
        			'start' => 0,
        			'sort' 	=> array('date_created' => 'desc', 'article_id' => 'desc'),
        			'order' => '', // Not required now as I am passing it in array of "sort" parameter
        			'total' => false,
        			'status'=> '1',
       			);
    	$whr[] = array(
    			'func' => 'in',
    			'data'=> array('column' => 'article.status', 'value' => '1'),
    			'with' => 'and'
    		);
    	$articles 				= $this->getArticleTable()->fetchAllNew(true, $params);
    	$user_articles_count 	= $this->getArticleTable()->getArticles(true);
    	$must_read_articles 	= $this->getCuratedTable()->getCurateds(false, ARTICLES_ON_HOME_PAGE);
    	$authors				= $this->getMemberTable()->getMembersForDropdowm(false);
    	$authors_usernames		= $this->getMemberTable()->getMembersForDropdowm('username');
    	$top_authors = $this->getMemberTable()->getTopUsers();

    	$articles->setItemCountPerPage(ARTICLES_ON_HOME_PAGE);
    	
    	return new ViewModel(array(
    			'articles' 				=> $articles,
    			'user_articles_count' 	=> $user_articles_count,
    			'authors' 				=> $authors,
    			'authors_usernames' 	=> $authors_usernames,
    			'top_authors' 			=> $top_authors,
    			'must_read_articles'	=> $must_read_articles,
    	));
    }

    /******************************************
     * Purpose: Used to handle Individual/Single Article request
     * Created By: Kedar
     * Created On: May 11, 2014 
     * Modified By: Kedar
     * Modified On: May 11, 2014
     */
    public function articleAction()
    {
    	/*************** START - Check User permission for the controller/action ****************/
    	$controller = $this->params()->fromRoute('controller', '');
    	$action = $this->params()->fromRoute('action', '');
    	$this->user_role = '0';
    	$this->user_id = 0;
    	if ($this->zfcUserAuthentication()->hasIdentity()) {
    		$this->user_role	= $this->zfcUserAuthentication()->getIdentity()->getRole();
    		$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
    	}
    	if(!$this->roles[$this->user_role][$controller][$action])
    	{
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}
    	/*************** END   - Check User permission for the controller/action ****************/
    	
    	$alias = $this->params()->fromRoute('alias', 'home');
    	if ($alias == 'home')
    	{
    		$this->indexAction();
    	}
    	else
    	{    	
    		$article	= $this->getArticleTable()->getArticle($alias);
    		
    		// Increase Article Count
    		$this->getArticleTable()->increaseArticleVisit($article->visits, $article->article_id);

    		/* echo "asdlasdl;askd;asd<br>";
    		echo $article->author_id."---".$this->user_id."---".$this->user_role."--"; */
    		if(!($article->author_id == $this->user_id || $this->user_role == 'admin'))
    		{
    			if(($article->status == '2') && ($this->user_role == 'reader' || $this->user_role == 'editor' || $this->user_role == '0' ))
    			{
    				$this->getResponse()->setStatusCode(404);
    			}
    			elseif(($article->status != '1') && ($this->user_role == 'reader' || $this->user_role == '0' ))
    			{
    				$this->getResponse()->setStatusCode(404);
    			}
    		}
    		$article	= get_object_vars($article);
    		array_walk($article,  function(&$value) {
    			if(!is_array($value))
    			$value = stripslashes($value);
    		});
    		$article	= (object)$article;
    		$author		= $this->getMemberTable()->getMember($article->author_id);
    		$authors	= $this->getMemberTable()->getMembersForDropdowm(false);
    		$categories = $this->getCategoryArticleTable()->getArticleCategoriesNamesWithAlias($article->article_id);
    		/************ START - Get all comments of the article ******************/
    		$params = array(
    				'sort' 	=> 'date_created',
    				'order'	=> 'desc',
    		);
    		$whr[] = array( 'func' => 'like',
    				'with' => '',
    				'data' => array('column' => 'article_id', 'value' => $article->article_id)
    		);
    		$whr[] = array( 'func' => 'like',
    				'with' => '',
    				'data' => array('column' => 'status', 'value' => '1')
    		);
    		// grab the paginator from the AlbumTable
    		//$comments = $this->getCommentTable()->fetchAll(true, $params, $whr);
    		$comments = $this->getCommentTable()->getCommentsParental($article->article_id);
    		$comment_count_article = $this->getCommentTable()->getArticleCommentsCount($article->article_id);
			/************ END - Get all comments of the article ******************/
    		
    		$user_data_for_right_col = $this->getArticleRightUserData($author);
    		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set($article->title);
    		$meta_description = $article->bullet1." ".$article->bullet2." ".$article->bullet3." ".$article->bullet4." ".$article->bullet5;
    		$meta_keywords = $article->title." ".trim($article->tickers, ', ')." ".$article->category;
    		$this->layout()->setVariable('meta_description', $meta_description);
    		$this->layout()->setVariable('meta_keywords', $meta_keywords);
    		
    		$view_data = array(
    				'comments' => $comments,
    				'article' 	=> $article,
    				'author' 	=> $author,
    				'authors'	=> $authors,
					'comment_count_article' => $comment_count_article,
    				'categories'			=> $categories,
    		);
    		$data = array_merge($user_data_for_right_col, $view_data);
    		$view = new ViewModel($data);
    		return $view;
    	}
    }
    
    /******************************************
     * Purpose: Used to handle Individual/Single Page request
     * Created On: Jun 23, 2014 
     * Modified On: Jun 23, 2014 
     */
    public function singlepageAction()
    {
    	/*************** START - Check User permission for the controller/action ****************/
    	$controller = $this->params()->fromRoute('controller', '');
    	$action = $this->params()->fromRoute('action', '');
    	if ($this->zfcUserAuthentication()->hasIdentity()) {
    		$this->user_role	= $this->zfcUserAuthentication()->getIdentity()->getRole();
    		$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
    	}
    	if(!$this->roles[$this->user_role][$controller][$action])
    	{
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}
    	/*************** END   - Check User permission for the controller/action ****************/
    	
    	$alias = $this->params()->fromRoute('alias', 'home');
    	if ($alias == 'home') 
	    {
	    	$this->indexAction();
	    }
	    else
	    {
			$page	= $this->getPageTable()->getPage($alias);
			if(!$page)
    		{
    			$this->getResponse()->setStatusCode(404);
    			return;
    		}
    		elseif($page->status == 0)
    		{
    			$this->getResponse()->setStatusCode(404);
    			return;
    		}
    		
    		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set($page->meta_title);
            $this->layout()->setVariable('meta_keywords', $page->meta_keywords);
    		$this->layout()->setVariable('meta_description', $page->meta_description);
    		
    		$view_data = array(
	            'page' 				=> $page,
    		);
    		
    		$view = new ViewModel($view_data);
    		return $view;	
        }
    }
    
    /****************************************
     * Purpose: Used to get the right columns data for the article view page 
     * Created By: Kedar
     * Created On: Sep 4, 2014
     * Modified By: Kedar
     * Modified On: Sep 4, 2014
     ****************************************/
    private function getArticleRightUserData($member)
    {
    	if($member)
    	{
    		$user_articles_count	= $this->getArticleTable()->getArticlesCount('article', $member->user_id);
    		$user_blogs_count 		= $this->getArticleTable()->getArticlesCount('blog', $member->user_id);
    		$user_comments_count 	= $this->getCommentTable()->getUserCommentsCount($member->user_id);
    		$followed_status		= $this->getFollowTable()->isFollowing($this->user_id, $member->user_id);
    		$followerCount			= $this->getFollowTable()->getFollowerCount($member->user_id);
    		$followedCount			= $this->getFollowTable()->getFollowedCount($member->user_id);
    
    		$left_view_data = array(
    				'member' 				=> $member,
    				'user_articles_count' 	=> $user_articles_count,
    				'user_blogs_count' 		=> $user_blogs_count,
    				'user_comments_count'	=> $user_comments_count,
    				'followed_status'		=> $followed_status,
    				'followerCount'			=> $followerCount,
    				'followedCount'			=> $followedCount,
    		);
    
    		return $left_view_data;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to search functionality 
     * Created By: Kedar
     * Created On: Sep 18, 2014
     * Modified By: Kedar
     * Modified On: Sep 18, 2014
     ****************************************/
    public function searchAction()
    {
    	/*************** START - Check User permission for the controller/action ****************/
    	$controller = $this->params()->fromRoute('controller', '');
    	$action = $this->params()->fromRoute('action', '');
    	if ($this->zfcUserAuthentication()->hasIdentity()) {
    		$this->user_role	= $this->zfcUserAuthentication()->getIdentity()->getRole();
    		$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
    	}
    	if(!$this->roles[$this->user_role][$controller][$action])
    	{
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}
    	/*************** END   - Check User permission for the controller/action ****************/
    	$type 		= $this->params()->fromRoute('type', '');
    	$keywords 	= $this->params()->fromRoute('keywords', '');
    	if($keywords == '')
    	{
    		$this->redirect()->toUrl(HOME_URL);
    	}
    	 
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Search - '.$keywords);
        
		$articles 			= '';
		$blogs 				= '';
		$must_read_8edge 	= '';
		$paginator 			= '';
		$cur_page_no 		= 1;
		if($type == "articles")
		{
			$paginator	= $this->getArticleTable()->getArticlesListSearch($keywords, false, 'article', true);
			
		}
		elseif($type == "blogs")
		{
			$paginator	= $this->getArticleTable()->getArticlesListSearch($keywords, false, 'blog', true);
		}
		elseif($type == "must_reads")
		{
			$paginator	= $this->getArticleTable()->getArticlesListSearch($keywords, false, false, true, true);
		}
		else //($type == "all") or no type
		{
    		$articles 			= $this->getArticleTable()->getArticlesListSearch($keywords, GENERAL_NUM_RECORDS_IN_LIST+1, 'article');
			$blogs 				= $this->getArticleTable()->getArticlesListSearch($keywords, GENERAL_NUM_RECORDS_IN_LIST+1, 'blog');
			$must_read_8edge 	= $this->getArticleTable()->getArticlesListSearch($keywords, GENERAL_NUM_RECORDS_IN_LIST+1, false, false, true);
		}
		
		if($paginator)
		{
			$cur_page_no = $this->getRequest()->getQuery('pg', 1);
			$paginator->setCurrentPageNumber((int)$cur_page_no);
			$paginator->setItemCountPerPage(RECORDS_PER_PAGE);
		}
		
		$view_data = array(
				'paginator' 		=> $paginator,
				'articles' 			=> $articles,
				'blogs' 			=> $blogs,
				'must_read_8edge' 	=> $must_read_8edge,
				'type'				=> $type,
				'keywords'			=> $keywords,
				'pg'				=> $cur_page_no,
		);
		
   		$view = new ViewModel($view_data);
   		return $view;
    }
	
	public function getArticleCommentCount()
	{
	
		$comment_count_article = $this->getCommentTable()->getArticleCommentsCount($article->article_id);
		$view = new ViewModel($comment_count_article);
    		return $view;
	}
}
