<?php
namespace Application\Controller;

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
use MyLibs\LinkedIn;

class RuntimeController extends AbstractActionController
{
	protected $memeberTable;
	protected $articleTable;
	protected $categoryTable;
	protected $companyTable;
	protected $commentTable;
	protected $followTable;
	protected $msgTable;
	protected $templateTable;
	
	protected $roles;
	protected $user_id;
	protected $user_role;

	public function __construct(){
		global $u_roles;
		$this->roles = $u_roles;
		$this->user_id = 0;
		$this->user_role = '0';
	}
	
	public function getTemplateTable()
	{
		if (!$this->templateTable) {
			$sm = $this->getServiceLocator();
			$this->templateTable = $sm->get('Admin\Model\TemplateTable');
		}
		return $this->templateTable;
	}
	
	public function getCommentTable()
	{
		if (!$this->commentTable) {
			$sm = $this->getServiceLocator();
			$this->commentTable = $sm->get('Application\Model\CommentTable');
		}
		return $this->commentTable;
	}
	public function getArticleTable()
	{
		if (!$this->articleTable) {
			$sm = $this->getServiceLocator();
			$this->articleTable = $sm->get('Application\Model\ArticleTable');
		}
		return $this->articleTable;
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
	
	public function getCompanyTable()
	{
		if (!$this->companyTable) {
			$sm = $this->getServiceLocator();
			$this->companyTable = $sm->get('Application\Model\companyTable');
		}
		return $this->companyTable;
	}
	
	public function getFollowTable()
	{
		if (!$this->followTable) {
			$sm = $this->getServiceLocator();
			$this->followTable = $sm->get('Application\Model\followTable');
		}
		return $this->followTable;
	}
	
	public function getMsgTable()
	{
		if (!$this->msgTable) {
			$sm = $this->getServiceLocator();
			$this->msgTable = $sm->get('Application\Model\msgTable');
		}
		return $this->msgTable;
	}
	
	/****************************************
	 * Purpose: Used for search suggestions
	 * Created By: Kedar
	 * Created On: Jul 8, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 8, 2014
	 ****************************************/
	public function searchAction()
	{
		$keywords = trim(urldecode($this->params()->fromRoute('keywords', '')), ' ');
		$search_options = array();
		
		/****************** START - GET articles realted to keyword **********/
		$articles = $this->getArticleTable()->getArticlesListSearch($keywords, 5);
    	 
    	if($articles && count($articles)>0)
    	{
    		foreach($articles as $article)
    		{
    			$search_options['Articles'][] = array(
    					'label' => $article->title,
    					'link'	=> HOME_URL.'article/'.$article->alias,
    			);
    		}
    	}
    	/****************** START - GET articles realted to keyword **********/
    	
    	
    	/****************** START - GET Symbols realted to keyword **********/
    	$companies = $this->getCompanyTable()->getCompaniesList($keywords, 5);
    	 
    	if($companies && count($companies)>0)
    	{
    		foreach($companies as $symbol => $company)
    		{
    			$search_options['Symbols'][] = array(
    					'label' => $company . ' (' . $symbol . ')',
    					'link'	=> HOME_URL.'symbol/'.$symbol,
    			);
    		}
    	}
    	/****************** START - GET Symbols realted to keyword **********/
    	
    	/****************** START - GET User realted to keyword **********/
    	$users = $this->getMemberTable()->getUsersList($keywords, 5);
    	
    	if($users && count($users)>0)
    	{
    		foreach($users as $username => $display_name)
    		{
    			$search_options['Authors'][] = array(
    					'label' => $display_name . ' (' . $username . ')',
    					'link'	=> HOME_URL.'user/'.$username,
    			);
    		}
    	}
    	/****************** START - GET User realted to keyword **********/
    	   
		$view_data = array(
					'keywords' => $keywords,
					'search_options' => $search_options,	
				);
		$view  = new ViewModel($view_data);
		$view->setTerminal(true);
		//$view->setTemplate('\application\runtime\search_options');
		
		return $view;
	}
	
	/****************************************
	 * Purpose: Used for Tag options
	 * Created By: Kedar
	 * Created On: Jul 8, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 8, 2014
	 ****************************************/
	public function tagsAction()
	{
		$keywords = trim(urldecode($this->params()->fromRoute('keywords', '')));
		$search_options = array();
	
		/****************** START - GET Symbols realted to keyword **********/
		$companies = $this->getCompanyTable()->getCompaniesList($keywords);
	
		if(count($companies) > 0)
		{
			foreach($companies as $symbol => $company)
			{
				$search_options['Symbols'][] = array(
						'label' => $company . ' (' . $symbol . ')',
						'tag' => $symbol,
						'link'	=> HOME_URL.'symbol/'.$symbol,
				);
			}
		}
		/****************** START - GET Symbols realted to keyword **********/
	
		$view_data = array(
				'keywords' => $keywords,
				'search_options' => $search_options,
		);
		$view  = new ViewModel($view_data);
		$view->setTerminal(true);
		//$view->setTemplate('\application\runtime\search_options');
	
		return $view;
	}
	
	/****************************************
	 * Purpose: Used to check the symbol existance link in database 
	 * Created By: Kedar
	 * Created On: Jul 23, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 23, 2014
	 ****************************************/
	public function symbolAction()
	{
		$keywords = trim(urldecode($this->params()->fromRoute('keywords', '')));
		$search_options = array();
	
		/****************** START - GET Symbols realted to keyword **********/
		$symbol_exists = false;
		$companies = $this->getCompanyTable()->getCompaniesList($keywords);
	
		if(count($companies) > 0)
		{
			foreach($companies as $symbol => $company)
			{
				if(strtoupper($keywords) == strtoupper($symbol))
				{
					$symbol_exists = strtoupper($symbol);
					break;
				}
			}
		}
		
		if($symbol_exists)
		{
			echo $symbol_exists; 
		}
		exit;
	}
	
	/****************************************
	 * Purpose: Used to post comments on article from front-end 
	 * Created By: Kedar
	 * Created On: Jul 31, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 31, 2014
	 ****************************************/
	public function commentAction()
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
    	
    	$this->postedData = array_merge(
	    		(array) $this->getRequest()->getPost(),
	            (array) $this->getRequest()->getFiles()
	    	);
		$comment = new \Application\Model\Comment();
		$comment->exchangeArray($this->postedData);
		$comment->author_id = $this->user_id;
		$comment->status = '1';
		;
		if($comment_id = $this->getCommentTable()->saveComment($comment))
		{
			$member = $this->getMemberTable()->getMember($this->user_id);
			$comment = $this->getCommentTable()->getComment($comment_id);
			$view_data = array(
					'author_name'	=> (($member->display_name)?$member->display_name:$member->username),
					'comment'		=> $comment->comment,
					'comment_date'	=> date('M d, Y H:i', strtotime($comment->date_created)),
					'comment_id'	=> $comment_id,
			);
			$view  = new ViewModel($view_data);
			$view->setTerminal(true);
		}
		/* else
		{
			echo "Not Saved";
		} */
		return $view;
	}

	/****************************************
	 * Purpose: Used for Replies on comment 
	 * Created By: Kedar
	 * Created On: Aug 4, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 4, 2014
	 ****************************************/
	public function commentreplyAction()
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
		 
		$this->postedData = array_merge(
				(array) $this->getRequest()->getPost(),
				(array) $this->getRequest()->getFiles()
		);
		$comment = new \Application\Model\Comment();
		$comment->exchangeArray($this->postedData);
		$comment->author_id = $this->user_id;
		$comment->status = '1';
		$comment->parent_id = $this->postedData['cmnt_id'];
		
		if($comment_id = $this->getCommentTable()->saveComment($comment))
		{
			$member = $this->getMemberTable()->getMember($this->user_id);
			$comment = $this->getCommentTable()->getComment($comment_id);
			$view_data = array(
					'author_name'	=> (($member->display_name)?$member->display_name:$member->username),
					'comment'		=> $comment->comment,
					'comment_date'	=> date('M d, Y H:i', strtotime($comment->date_created)),
					'comment_id'	=> $comment_id,
			);
			$view  = new ViewModel($view_data);
			$view->setTerminal(true);
		}
		/* else
			{
		echo "Not Saved";
		} */
		return $view;
	}
	
	/****************************************
	 * Purpose: Used for Abuse Report Request handle 
	 * Created By: Kedar
	 * Created On: Aug 1, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 1, 2014
	 ****************************************/
	public function reportAction()
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
		$this->postedData = array_merge(
				(array) $this->getRequest()->getPost(),
				(array) $this->getRequest()->getFiles()
		);
		
		$article_id = $this->postedData['article_id'];
		
		$member 	= $this->getMemberTable()->getMember($this->user_id);
		$article 	= $this->getArticleTable()->getArticle($article_id);
		
		$comment = '';
		$reply = '';
		if(isset($this->postedData['cmnt_id']) && $this->postedData['cmnt_id'] !='')
		{
			$cmnt_id = $this->postedData['cmnt_id'];
			$comment = $this->getCommentTable()->getComment($cmnt_id);
		}
		if(isset($comment->parent_id) && $comment->parent_id != 0)
		{
			$reply_id	= $this->postedData['cmnt_id'];
			$reply 		= $comment;
			$comment 	= $this->getCommentTable()->getComment($reply->parent_id);
		}
		
		$report 	= $this->postedData['report'];

		$data = array(
			'report' 		=> $report,
			'author_name' 	=> (($member->display_name)?$member->display_name:$member->username),
			'author_link' 	=> HOME_URL.'user/'.$member->username,
			'article_title' => $article->title,
			'article_id' 	=> $article_id,
			'article_link'	=> HOME_URL.'article/'.$article->alias,
			'comment'		=> $comment,
			'reply'			=> $reply,
				
		);
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');  
		$report_abuse_email = $this->renderer->render('application/email/report_abuse', $data);  
        
        // make a header as html  
        /* $html = new MimePart($request_flight_email);  
        $html->type = "text/html";  
        $body = new MimeMessage();  
        $body->setParts(array($html,));  
        
        $message = new Message();
        $message->addTo('kedar@orangemantra.com')
                //->addTo('rahul.jaisingh@gmail.com')
                //->addTo('Vivek.Gera@gmail.com')
                //->addTo('vinit@orangemantra.com')
                ->addFrom($member->email)
                ->setSubject('Abusive Content: '.$article->title)
                ->setBody($body);
        
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'host'              => 'smtp.gmail.com',
            'name'              => 'smtp.gmail.com',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' 		=> 'kkedar87@gmail.com',
                'password' 		=> 'ked@rkum@r',
                'port'			=> 465,
                'ssl' 			=> 'tls',
            ),
        ));
		$transport->setOptions($options);
		$m = $transport->send($message); */
		//$to = 'Rahul Jaisingh <rahul.jaisingh@gmail.com>, Vivek Gera <Vivek.Gera@gmail.com>, Vinit Choudhary <vinit@orangemantra.com>, kedar@orangemantra.com';
		$to = $this->Options()->getOptionValue('admin_notification_email_ids');
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		//$headers .= 'To: Kedar <kedar@orangemantra.com>' . "\r\n";
		$headers .= 'From: ' . $member->email . "\r\n";
		
		// Mail it
		mail($to, 'Abusive Content: '.$article->title, $report_abuse_email, $headers);
		exit;
	}
	
	public function linkedinAction()
	{
		$linkedin = new LinkedIn\LinkedIn(
				array(
						'api_key' => '755zdciuaww4z4',
						'api_secret' => 'wMcXsauAuRvT08IY',
						'callback_url' => 'http://localhost/workspace/test_work/linked_in_login/LinkedIn/llinkedin_login.php'
				)
		);
		
		if(!isset($REQUEST['core']))
		{
			$url = $linkedin->getLoginUrl(
					array(
							LinkedIn\LinkedIn::SCOPE_BASIC_PROFILE,
							LinkedIn\LinkedIn::SCOPE_EMAIL_ADDRESS,
							LinkedIn\LinkedIn::SCOPE_NETWORK
					)
			);
			header("Location: " . $url);
			exit;
		}
		
		
		print_r($_REQUEST);
		
		$data = serialize($_REQUEST);
	
		mail('kedar@orangemantra.com', 'Linked In', $data);
		echo "<pre>";
		print_r($_REQUEST);
		
		exit;
	}
	
	/****************************************
	 * Purpose: Used to check the exitance of the username in database for registration page 
	 * Created By: Kedar
	 * Created On: Aug 21, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 21, 2014
	 ****************************************/
	public function checkUsernameAction()
	{
		/*************** START - Check User permission for the controller/action ****************/
		if ($this->zfcUserAuthentication()->hasIdentity()) {
			$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
		}
		/*************** END   - Check User permission for the controller/action ****************/
		
		$username = trim(urldecode($this->params()->fromRoute('keywords', '')));
		if($this->getMemberTable()->checkUsernameExistance($username))
		{
			echo 'exist';
		}
		else
		{
			echo 'not_exist';
		}
		exit;
	}
	
	/****************************************
	 * Purpose: Used to check the exitance of the email in database for registration page
	* Created By: Kedar
	* Created On: Aug 21, 2014
	* Modified By: Kedar
	* Modified On: Aug 21, 2014
	****************************************/
	public function checkEmailAction()
	{
		/*************** START - Check User permission for the controller/action ****************/
		if ($this->zfcUserAuthentication()->hasIdentity()) {
			$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
		}
		/*************** END   - Check User permission for the controller/action ****************/
		
		$email = trim(urldecode($this->params()->fromRoute('keywords', '')));
		if($this->getMemberTable()->checkEmailExistance($email, $this->user_id))
		{
			echo 'exist';
		}
		else
		{
			echo 'not_exist';
		}
		exit;
	}

	/****************************************
	 * Purpose: Used for change article status
	 * Created By: Kedar
	 * Created On: Jul 8, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 8, 2014
	 ****************************************/
	public function articleStatusAction()
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
    	
    	$article_id = trim(urldecode($this->params()->fromRoute('keywords', '')), ' ');
		$status		= trim(urldecode($this->params()->fromRoute('keywords2', '')), 0);
		$search_options = array();
		
		$data = array('status' => $status, 'approved_by_editor' => $this->user_id);
		$articles = $this->getArticleTable()->updateArticle($data, $article_id );
    	
		/************** Rejected Email Template **************/
		if($status == '3')
		{
			$request = $this->getRequest();
			$article = $this->getArticleTable()->getArticle($article_id);
			$user = $this->getMemberTable()->getMember($article->author_id);
		
			$template_placeholders['{{TO_USER_NAME}}'] 				= ($user->display_name)?$user->display_name:$user->username;
			$template_placeholders['{{TO_USER_EMAIL}}'] 			= $user->email;;
			$template_placeholders['{{TO_USER_NAME_LINK}}'] 		= '<a href="'.HOME_URL.'user/' . $user->username . '">'.$template_placeholders['{{TO_USER_NAME}}'].'</a>';
		
			$template_placeholders['{{ARTICLE_TITLE}}'] 			= $article->title;
			$template_placeholders['{{ARTICLE_EDIT_LINK}}'] 		= '<a href="'.HOME_URL.'articles/edit/' . $article_id . '">Edit Article</a>';
			$template_placeholders['{{ARTICLE_TITLE_LINK}}'] 		= '<a href="'.HOME_URL.'article/' . $article->alias . '">' . $article->title . '</a>';
			$template_placeholders['{{REASON_TO_REJECT_ARTICLE}}'] 	= $request->getPost()->reason;
		
			$email_template = $this->getTemplateTable()->getTemplate('article_rejected');
			 
			$msg_subject	= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->subject);
			$msg_body 		= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->description);
			$msg_from 		= $email_template->from_default;
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= "From: " . $msg_from;
		
			if(mail($user->email, $msg_subject, $msg_body, $headers))
			{
			}
		}
		elseif($status == '1')
		{
			$article = $this->getArticleTable()->getArticle($article_id);
			$user = $this->getMemberTable()->getMember($article->author_id);
			 
			$template_placeholders['{{TO_USER_NAME}}'] 				= ($user->display_name)?$user->display_name:$user->username;
			$template_placeholders['{{TO_USER_EMAIL}}'] 			= $user->email;;
			$template_placeholders['{{TO_USER_NAME_LINK}}'] 		= '<a href="'.HOME_URL.'user/' . $user->username . '">'.$template_placeholders['{{TO_USER_NAME}}'].'</a>';
			 
			$template_placeholders['{{ARTICLE_TITLE}}'] 			= $article->title;
			$template_placeholders['{{ARTICLE_EDIT_LINK}}'] 		= '<a href="'.HOME_URL.'articles/edit/' . $article_id . '">Edit Article</a>';
			$template_placeholders['{{ARTICLE_TITLE_LINK}}'] 		= '<a href="'.HOME_URL.'article/' . $article->alias . '">' . $article->title . '</a>';
			 
			$email_template = $this->getTemplateTable()->getTemplate('article_approved');
			 
			$msg_subject	= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->subject);
			$msg_body 		= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->description);
			$msg_from 		= $email_template->from_default;
			 
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= "From: " . $msg_from;
			 
			if(mail($user->email, $msg_subject, $msg_body, $headers))
			{
			}
		}
		/************** Rejected Email Template **************/
		   
		
    	$view_data = array('success' => 1);
    	echo json_encode($view_data);
    	exit;
	}
	
	/****************************************
	 * Purpose: Used for change article status
	* Created By: Kedar
	* Created On: Jul 8, 2014
	* Modified By: Kedar
	* Modified On: Jul 8, 2014
	****************************************/
	public function editorsChoiceAction()
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
		 
		$article_id = trim(urldecode($this->params()->fromRoute('keywords', '')), ' ');
		$status		= trim(urldecode($this->params()->fromRoute('keywords2', '')), '0');
		$search_options = array();
	
		$data = array('editors_choice' => ''.$status);
		$articles = $this->getArticleTable()->updateArticle($data, $article_id );
		 
		$view_data = array('success' => 1);
		echo json_encode($view_data);
		exit;
	}
	
	/****************************************
	 * Purpose: Used to follow a member 
	 * Created By: Kedar
	 * Created On: Aug 28, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 28, 2014
	 ****************************************/
	public function followAction()
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
		$followed_id	= trim(urldecode($this->params()->fromRoute('keywords', '')), '0');
		$follower_id = $this->user_id;
		$followed_suc = $this->getFollowTable()->saveFollow($follower_id, $followed_id);
		if($followed_suc)
		{
			$followerCount =  $this->getFollowTable()->getFollowerCount($followed_id);
			$followedCount =  $this->getFollowTable()->getFollowedCount($followed_id);
			$view_data = array('success' => 1, 'followers' => $followerCount, 'followed' => $followedCount);
		}
		else
		{
			$view_data = array('success' => 0, 'error' => '1');
		}
		echo json_encode($view_data);
		exit;	
	}
	
	/****************************************
	 * Purpose: Used to unfollow functionality 
	 * Created By: Kedar
	 * Created On: Aug 29, 2014
	 * Modified By: Kedar
	 * Modified On: Aug 29, 2014
	 ****************************************/
	public function unfollowAction()
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
		$followed_id	= trim(urldecode($this->params()->fromRoute('keywords', '')), '0');
		$follower_id = $this->user_id;
		$followed_suc = $this->getFollowTable()->unfollow($follower_id, $followed_id);
		if($followed_suc)
		{
			$followerCount =  $this->getFollowTable()->getFollowerCount($followed_id);
			$followedCount =  $this->getFollowTable()->getFollowedCount($followed_id);
			$view_data = array('success' => 1, 'followers' => $followerCount, 'followed' => $followedCount);
		}
		else
		{
			$view_data = array('success' => 0, 'error' => '1');
		}
		echo json_encode($view_data);
		exit;
	}
	
	/****************************************
	 * Purpose: Used to send messages to the user 
	 * Created By: Kedar
	 * Created On: Sep 1, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 1, 2014
	 ****************************************/
	public function sendAction()
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
		$this->postedData = array_merge(
	    		(array) $this->getRequest()->getPost(),
	            (array) $this->getRequest()->getFiles()
	    	);
		//$to	= trim(urldecode($this->params()->fromRoute('keywords', '')), '0');
		$this->postedData['from'] = $this->user_id;
		$save_rs = $this->getMsgTable()->saveMsg($this->postedData);
		if($save_rs)
		{
			$view_data = array('success' => 1, 'error' => 0);
		}
		else
		{
			$view_data = array('success' => 0, 'error' => '1');
		}
		echo json_encode($view_data);
		exit;
	}
	
	/****************************************
	 * Purpose: Used to set the message status to read  
	 * Created By: Kedar
	 * Created On: Sep 3, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 3, 2014
	 ****************************************/
	public function setReadAction()
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
		$msg_id = $this->params()->fromRoute('keywords', '');
		$msg = $this->getMsgTable()->getMsg($msg_id);
		if($msg)
		{
			if($msg->to == $this->user_id)
			{
				$data = array('read' => '1');
				$rs = $this->getMsgTable()->updateMsg($data, $msg_id);
				if($rs)
				{
					$newMsgCount = $this->getMsgTable()->getUserMsgCount($this->user_id, false);
					$view_data = array('success' => 1, 'error' => 0, 'newMsgCount' => $newMsgCount);
				}
				else
				{
					$view_data = array('success' => 0, 'error' => '1');
				}
			}
			else
			{
				$view_data = array('success' => 0, 'error' => '1');
			}
		}
		else
		{
			$view_data = array('success' => 0, 'error' => '1');
		}
		echo json_encode($view_data);
		exit;
	}
	
	/****************************************
	 * Purpose: Used to edit user profile 
	 * Created By: Kedar
	 * Created On: Sep 2, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 2, 2014
	 ****************************************/
	public function edituserAction()
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
		$this->postedData = array_merge(
				(array) $this->getRequest()->getPost(),
				(array) $this->getRequest()->getFiles()
		);
		
		if(isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] != '')
		{
			$milliseconds = round(microtime(true) * 1000);
			$upload_image_name = $_FILES['image']['name'];
			$ext = substr($upload_image_name, strrpos($upload_image_name, '.'), strlen($upload_image_name)-strrpos($upload_image_name, '.'));
			$file_name = 'member_' . $milliseconds . $ext;
		
			if(!move_uploaded_file($_FILES['image']['tmp_name'], MEMBER_IMAGE_PATH.$file_name))
			{
				echo "file could not be uploaded.";
				exit;
			}
			else
			{
				$old_image = $this->getMemberTable()->getMember($this->user_id)->image;
				if($old_image != '' && file_exists(MEMBER_IMAGE_PATH.$old_image))
				{
					unlink(MEMBER_IMAGE_PATH.$old_image);
				}
				$this->postedData['image'] = $file_name;
			}
		}
		else
		{
			unset($this->postedData['image']);
		}
		
		$rs = $this->getMemberTable()->updateMemberProfile($this->postedData, $this->user_id);
		if(isset($this->postedData['ajx']))
		{
			if($rs)
			{
				$view_data = array('success' => 1, 'error' => 0);
			}
			else
			{
				$view_data = array('success' => 0, 'error' => '1');
			}
			echo json_encode($view_data);
			exit;
		}
		else
		{
			if($rs === true)
			{
				return $this->redirect()->toUrl(HOME_URL.'user');
			}
			else
			{
				return $this->redirect()->toUrl(HOME_URL.'user?show=edituser&error='.$rs);
			}
		}
		
	}
}