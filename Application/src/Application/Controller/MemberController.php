<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Member;
use Application\Form\MemberForm;
use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;


class MemberController extends AbstractActionController
{
	protected $memberTable;
	protected $articleTable;
	protected $commentTable;
	protected $followTable;
	protected $msgTable;
	
	protected $roles;
	protected $user_id;
	protected $user_role;
	public function __construct(){
		global $u_roles;
		$this->roles = $u_roles;
		$this->user_id = 0;
		$this->user_role = 0;
	}
	
	public function getMemberTable()
    {
        if (!$this->memberTable) {
            $sm = $this->getServiceLocator();
            $this->memberTable = $sm->get('Application\Model\MemberTable');
        }
        return $this->memberTable;
    }
    
    public function getArticleTable()
    {
    	if(!$this->articleTable)
    	{
    		$sm = $this->getServiceLocator();
    		$this->articleTable = $sm->get('Application\Model\ArticleTable');
    	}
    	return $this->articleTable;
    }
	
    public function getCommentTable()
    {
    	if(!$this->commentTable)
    	{
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
    
    public function getMsgTable()
    {
    	if(!$this->msgTable)
    	{
    		$sm = $this->getServiceLocator();
    		$this->msgTable = $sm->get('Application\Model\MsgTable');
    	}
    	return $this->msgTable;
    }
    
    /****************************************
	 * Purpose: Used to display Current Logged in user profile
	 * Created By: Kedar
	 * Created On: Jul 10, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 10, 2014
	 ****************************************/
	public function indexAction()
    {
    	if(!$this->zfcUserAuthentication()->getIdentity())
    	{
    		$url = urlencode(HOME_URL.trim($_SERVER['REQUEST_URI'], '/'));
    		return $this->redirect()->toUrl(HOME_URL.'user/login?redirect='.$url);
    	}
    	$view_data = $this->portfolio();
       	$view = new ViewModel($view_data);
       	$view->setTemplate('application/member/portfolio');
        return $view;
    }
    
    /****************************************
	 * Purpose: Used to display the user profile page 
	 * Created By: Kedar
	 * Created On: Jul 10, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 10, 2014
	 ****************************************/
	public function portfolioAction()
    {
    	$view_data = $this->portfolio();
    	$view = new ViewModel($view_data);
        return $view;
    }

	/****************************************
	 * Purpose: Used to display Registration success message
	 * Created By: Kedar
	 * Created On: Aug 20, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 20, 2014
	 ****************************************/
	public function registersuccessAction()
    {
    	$user_id = $this->params()->fromRoute('user_id', '');
    	$mail_sent = false;
    	if($user_id != '')
    	{
    		$user_id = base64_decode($user_id);
    		if($this->getMemberTable()->verificationMail($user_id))
    		{
    			$mail_sent = true;
    		}
    	}
    	
    	$view  = new ViewModel(array('mail_sent' => $mail_sent));
    	//$view->setTerminal(true);
    	return $view;
    }
	
	/****************************************
	 * Purpose: Used to resend verification email
	 * Created By: Kedar
	 * Created On: Aug 20, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 20, 2014
	 ****************************************/
	public function reverificationmailAction()
    {
    	$user_id = $this->params()->fromRoute('keywords', '');
    	$mail_sent = false;
    	if($user_id != '')
    	{
    		$user_id = base64_decode($user_id);
    		if($this->getMemberTable()->verificationMail($user_id))
    		{
    			$mail_sent = true;
    		}
    	}
    	
    	$view  = new ViewModel(array('mail_sent' => $mail_sent));
    	//$view->setTerminal(true);
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to verify email id 
     * Created By: Kedar
     * Created On: Aug 20, 2014
     * Modified By: Kedar
     * Modified On: Aug 20, 2014
     ****************************************/
    public function verifyemailAction()
    {
    	$link_expired = false;
    	$invalid_code = false;
    	$verification = false;
    	
    	$user_id = $this->params()->fromRoute('keywords', '');
    	$verification_code = $this->params()->fromRoute('param2', '');
    	
    	if($verification_code == '' || $user_id == '')
    	{
    		$link_expired = true;
    	}
    	else
    	{
    		$user_id = base64_decode($user_id);
    		$user = $this->getMemberTable()->getMember($user_id);
    		if($verification_code == $user->verificationcode)
    		{
    			$this->getMemberTable()->approveMember($user_id);
    			$verification = true;
    		}
    	}
    	$data['user_id'] = $user_id;
    	$data['link_expired'] = $link_expired;
    	$data['invalid_code'] = $invalid_code;
    	$data['verification'] = $verification;
    	
    	$view  = new ViewModel($data);
    	//$view->setTerminal(true);
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to forget pass to enter email 
     * Created By: Kedar
     * Created On: Oct 6, 2014
     * Modified By: Kedar
     * Modified On: Oct 6, 2014
     ****************************************/
    public function forgotpassAction()
    {
    	$mail_sent = false;
    	$email_id_not_registered = false;
    	 
    	$request = $this->getRequest();
    	if ($request->isPost()) 
    	{
    		$this->postedData = array_merge(
    				(array) $this->getRequest()->getPost(),
    				(array) $this->getRequest()->getFiles()
    		 	);
    		if(isset($this->postedData['email']) && $this->postedData != '')
    		{
    			$user 	= $this->getMemberTable()->getMember($this->postedData['email']);
    			if(!$user)
    			{
    				$email_id_not_registered = true;
    			}
    			else
    			{
	    			$rs 	= $this->getMemberTable()->forgotPassMail($user->user_id);
	    			if($rs)
	    			{
	    				$mail_sent = true;
	    			}
    			}
    		}
    	}
    	$data['mail_sent'] = $mail_sent;
    	$data['email_id_not_registered'] = $email_id_not_registered;
    	 
    	$view  = new ViewModel($data);
    	//$view->setTerminal(true);
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to handle the forgot password request 
     * Created By: Kedar
     * Created On: Oct 6, 2014
     * Modified By: Kedar
     * Modified On: Oct 6, 2014
     ****************************************/
    public function resetpassAction()
    {
    	$link_expired = false;
    	$invalid_code = false;
    	$verification = false;
    	$pass_reset_succ = false;
    	
    	$user_id = $this->params()->fromRoute('keywords', '');
    	$verification_code = $this->params()->fromRoute('param2', '');
    	 
    	if($verification_code == '' || $user_id == '')
    	{
    		$link_expired = true;
    	}
    	else
    	{
    		$user_id = base64_decode($user_id);
    		$user = $this->getMemberTable()->getMember($user_id);
    		if($verification_code == $user->verificationcode)
    		{
    			$request = $this->getRequest();
    			if ($request->isPost()) 
    			{
    			 	$this->postedData = array_merge(
    			 			(array) $this->getRequest()->getPost(),
    			 			(array) $this->getRequest()->getFiles()
    			 	);
    			 	$rs = $this->getMemberTable()->resetPassword($this->postedData, $user_id);
					
					if($rs)
					{

						$pass_reset_succ = true;
					}
    			}
    		}
    		else
    		{
    			$invalid_code = true;
    		}
    	}
    	$data['pass_reset_succ'] = $pass_reset_succ;
    	$data['user_id'] 		= $user_id;
    	$data['link_expired'] = $link_expired;
    	$data['invalid_code'] = $invalid_code;
    	$data['verification'] = $verification;
    	 
    	$view  = new ViewModel($data);
    	//$view->setTerminal(true);
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to list inbox messages of the user 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function msgAction()
    {
    	if(!$this->zfcUserAuthentication()->hasIdentity())
    	{
    		$url = urlencode(HOME_URL.trim($_SERVER['REQUEST_URI'], '/'));
			return $this->redirect()->toUrl(HOME_URL.'user/login?redirect='.$url);
    	}
    	
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
    	$type = $this->params()->fromRoute('keywords', 'all');
    	$pg = (int)$this->getRequest()->getQuery('pg', 1);
    	
        $sort = $this->getRequest()->getQuery('sort', 'date');
        $order =$this->getRequest()->getQuery('order', 'desc');
        $params = array(
    			'sort' 	=> $sort,
    			'order'	=> $order,
    	);
    	
    	$whr = array();
    	$search_keyword = '';
    	$search_data = array();
   		if($this->getRequest()->getQuery('read', -1) != -1)
    	{
    		$search_data['read'] = $read = (int)$this->getRequest()->getQuery('read');
    		$whr[] = array( 'func' => 'in',
    				'with' => '',
    				'data' => array('column' => 'read', 'value' => array((int)$read))
    		);
    	}
    	
    	if($type == 'inbox')
    	{
    		$whr[] = array( 'func' => 'like',
    				'with' => '',
    				'data' => array('column' => 'to', 'value' => $this->user_id)
    		);
    	}
    	elseif($type == 'sent')
    	{
    		$whr[] = array( 'func' => 'like',
    				'with' => '',
    				'data' => array('column' => 'from', 'value' => $this->user_id)
    		);
    	}
    	elseif(is_numeric($type))
    	{
    		
    	}
    	   	
    	$whr[] =array( 'func' => 'NEST');
    	if($this->getRequest()->getQuery('s') != '')
    	{
    		$search_data['s'] = $search_keyword = $this->getRequest()->getQuery('s');
    		$whr[] = array( 'func' => 'like',
    				'with' => '',
    				'data' => array('column' => 'from_user.username', 'value' => '%'.$search_keyword.'%')
    		);
    		$whr[] = array( 'func' => 'like',
    				'with' => 'OR',
    				'data' => array('column' => 'sub', 'value' => '%'.$search_keyword.'%')
    		);
    		$whr[] = array( 'func' => 'like',
    				'with' => 'OR',
    				'data' => array('column' => 'msg', 'value' => '%'.$search_keyword.'%')
    		);
    	}
    	$whr[] =array( 'func' => 'UNNEST');
    	
    	// grab the paginator from the AlbumTable
    	$paginator = $this->getMsgTable()->fetchAll(true, $params, $whr);
    	// set the current page to what has been passed in query string, or to 1 if none set
    	$paginator->setCurrentPageNumber($pg);
    	// set the number of items per page to 10
    	$paginator->setItemCountPerPage(RECORDS_PER_PAGE);
    	
    	return new ViewModel(array(
    			'paginator' 			=> $paginator,
    			//'members' 			=> $members,
    			//'member_msgs_count'	=> $member_msgs_count,
    			'sort'					=> $sort,
    			'order'					=> $order,
    			'search_data'			=> $search_data,
    			's'						=> $search_keyword,
    			'type'					=> $type,
    			'pg'					=> $pg,
    	));
    }
    
    /****************************************
     * Purpose: Used to list all the followers of user 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function followersAction()
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
    	$data = $this->follow('followers');
    	$data['page_title'] = 'Followers';
    	$view = new ViewModel($data);
    	
    	$view->setTemplate('application/member/followusers');
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to List the users followed by the user whose profile is being viewed 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function followedAction()
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
    	$data = $this->follow('followed');
    	$data['page_title'] = 'Followings';
    	$view = new ViewModel($data);
    	$view->setTemplate('application/member/followusers');
    	return $view;
    }
   
   	/****************************************
   	 * Purpose: Used to list all the user comments 
   	 * Created By: Kedar
   	 * Created On: Sep 1, 2014
   	 * Modified By: Kedar
   	 * Modified On: Sep 1, 2014
   	 ****************************************/
   	public function usercommentsAction()
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
    	$data = $this->comments('usercomments');
    	$view = new ViewModel($data);
    	
    	$view->setTemplate('application/member/comments');
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to list the comment posted on user articles 
     * Created By: Kedar
     * Created On: Sep 2, 2014
     * Modified By: Kedar
     * Modified On: Sep 2, 2014
     ****************************************/
    public function articleCommentsAction()
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
    	$data = $this->comments('articlecomments');
    	$view = new ViewModel($data);
    	
    	$view->setTemplate('application/member/comments');
    	return $view;
    }
    
    /****************************************
     * Purpose: Used to get the data for the left column of the profile page 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    private function getPortfolioLeftData(Member $member)
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
     * Purpose: Used to habdle portfolio requests
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    private function portfolio()
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
    	if($this->user_role === 'admin')
    	{
    		//return $this->redirect()->toUrl(ADMIN_URL);
    	}
    	$username = $this->params()->fromRoute('username', '');
    
    	if (!$username)
    	{
    		$member = $this->getMemberTable()->getMember($this->user_id);
    	}
    	else
    	{
    		$member = $this->getMemberTable()->getMember($username);
    	}
    
    	// *****************Data for portfolio left columns************************************
    	$left_column_data = $this->getPortfolioLeftData($member);
    	// *****************Data for portfolio left columns************************************
    
    	$params = array(
    			'limit' => 1,
    			'start' => 0,
    			'sort' 	=> array('date_created' => 'desc', 'article_id' => 'desc'),
    			'total' => false,
    			'status'=> '1', // Working, status is being checked from here
    	);
    	$whr[0] = array(
    			'func' => 'like',
    			'with' => '',
    			'data' => array('column' => 'author_id', 'value' => $member->user_id)
    	);
    
    	/****************** START - GET articles realted to keyword **********/
    	$whr[1] = array(
    			'func' => 'like',
    			'with' => '',
    			'data' => array('column' => 'article.type', 'value' => 'article')
    	);
    
    	$articles 	= $this->getArticleTable()->fetchAll(true, $params, $whr)->setItemCountPerPage(5);;
    	/****************** START - GET articles realted to keyword **********/
    	  
    	/****************** START - GET Blogs realted to keyword **********/
    	$whr[1] = array(
    			'func' => 'like',
    			'with' => '',
    			'data' => array('column' => 'type', 'value' => 'blog')
    	);
    
    	$blogs 	= $this->getArticleTable()->fetchAll(true, $params, $whr)->setItemCountPerPage(5);;
    	/****************** START - GET Blogs realted to keyword **********/
    	  
    	/****************** START - GET Comment related data **********/
    	$user_comments		 			= $this->getCommentTable()->getUserPostedComments($member->user_id, 5);
    	$comments_on_user_articles 		= $this->getCommentTable()->getUsersWhoPostedCommentsOnUserArticles($member->user_id, 5);
    	/****************** START - GET Comment related data **********/
    	 
    	$view_data = array(
    			'articles' 				=> $articles,
    			'blogs'					=> $blogs,
    			'user_comments'			=> $user_comments,
    			'comments_on_user_articles' => $comments_on_user_articles,
    	);
    	$data = array_merge($left_column_data, $view_data);
    	 
    	return $data;
    }
    
    /****************************************
     * Purpose: Used to get the follwers and followed data of the user
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    private function follow($type = false)
    {
    	if($type)
    	{
    		$user = $this->params()->fromRoute('keywords', $this->user_id);
    		$pg = (int)$this->getRequest()->getQuery('pg', 1);
    
    		$member = $this->getMemberTable()->getMember($user);
    
    		// *****************Data for portfolio left columns************************************
    		$left_column_data = $this->getPortfolioLeftData($member);
    		// *****************Data for portfolio left columns************************************
    		 
    		if($type == 'followers')
    		{
    			$paginator = $this->getMemberTable()->fetchUserFollowers($member->user_id);
    		}
    		elseif($type == 'followed')
    		{
    			$paginator = $this->getMemberTable()->fetchUserFollowedByUser($member->user_id);
    		}
    
    		$paginator->setCurrentPageNumber($pg);
    		$paginator->setItemCountPerPage(RECORDS_PER_PAGE);
    		 
    		$view_data = array(
    				'paginator' 			=> $paginator,
    				'type'					=> $type,
    				'pg'					=> $pg,
    		);
    
    		return $data = array_merge($left_column_data, $view_data);
    	}
    	return false;
    }
    
	/****************************************
	 * Purpose: Used to get the comments data 
	 * Created By: Kedar
	 * Created On: Sep 2, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 2, 2014
	 ****************************************/
	private function comments($type)
    {
    	if($type)
    	{
    		$user = $this->params()->fromRoute('keywords', $this->user_id);
    		$pg = (int)$this->getRequest()->getQuery('pg', 1);
    
    		$member = $this->getMemberTable()->getMember($user);
    
    		// *****************Data for portfolio left columns************************************
    		$left_column_data = $this->getPortfolioLeftData($member);
    		// *****************Data for portfolio left columns************************************
    		 
    		if($type == 'usercomments')
    		{
    			$paginator	= $this->getCommentTable()->getUserPostedComments($member->user_id, false);
    		}
    		elseif($type == 'articlecomments')
    		{
    			$paginator = $this->getCommentTable()->getUsersWhoPostedCommentsOnUserArticles($member->user_id, false);
    		}
    
    		$paginator->setCurrentPageNumber($pg);
    		$paginator->setItemCountPerPage(RECORDS_PER_PAGE);
    		 
    		$view_data = array(
    				'paginator' 			=> $paginator,
    				'type'					=> $type,
    				'pg'					=> $pg,
    		);
    
    		return $data = array_merge($left_column_data, $view_data);
    	}
    	return false;
    }
}

