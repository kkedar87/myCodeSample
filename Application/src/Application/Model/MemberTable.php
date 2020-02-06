<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;

class MemberTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated=false, $params = false, $whr = array())
    {
        $sort 	= isset($params['sort'])	?	$params['sort']		:	'date_created';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	
    	if ($paginated) {
            // create a new Select object for the table album
            $select = new \Zend\Db\Sql\Select('user');
			
            if(!empty($whr))
      		{
      			foreach($whr as $cond)
      			{
      				$func = $cond['func'];
      				$data = $cond['data'];
      				$with = $cond['with'];
      				if($func == 'notin')
	      			{
	      				if($with != '')
	      				{
		      				$select->where->$with->addPredicate(new \Zend\Db\Sql\Predicate\Expression($data['column'].' NOT IN (?)', array(implode(",", $data['value']))));
	      				}
	      				else
	      				{
	      					$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression($data['column'].' NOT IN (?)', array(implode(",", $data['value']))));
	      				}
	      			}
	      			else
	      			{
	      				if($with != '')
	      				{
		      				$select->where->$with->$func($data['column'], $data['value']);
	      				}
	      				else
	      				{
	      					$select->where->$func($data['column'], $data['value']);
	      				}
	      			}
      			}
      		}
      		
    		if(is_array($sort))
    		{
    			foreach($sort as $sort => $order)
    			{
    				$select->order($sort . ' ' . $order);
    			}
		    }
		    else
		    {
		    	$select->order($sort . ' ' . $order);
		    }
		    
		    /* echo  $select->getSqlString();
		    exit; */
		    
		    // create a new result set based on the Album entity
             $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
             $resultSetPrototype->setArrayObjectPrototype(new Member());
             // create a new pagination adapter object
             $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
                 // our configured select object
                 $select,
                 // the adapter to run it against
                 $this->tableGateway->getAdapter(),
                 // the result set to hydrate
                 $resultSetPrototype
             );
             $paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
             return $paginator;
         }
         $resultSet = $this->tableGateway->select();
         return $resultSet;
    }

	/****************************************
	 * Purpose: Used to get the listing on users for dropdown options. Array returns with userid and author name.
	 * Created By: Kedar
	 * Modified By: Kedar
	 * Modified On: Jul 10, 2014
	 ****************************************/
	public function getMembersForDropdowm($with = true)
    {
        $resultSet = $this->tableGateway->select();
        $users = array();
        
        if(!$with)
        {
        	$with = 'display_or_username';
        }
        
        if($with == 'name')
        {
	        foreach($resultSet as $row)
	        {
	        	$users[$row->user_id] = $row->first_name . ' ' . $row->last_name;
	       	}
        }
        elseif($with == 'display_or_username')
        {
	        foreach($resultSet as $row)
	        {
	        	$users[$row->user_id] = (($row->display_name)?$row->display_name:$row->username);
	       	}
        }
        elseif($with == 'email')
        {
	        foreach($resultSet as $row)
	        {
	        	$users[$row->user_id] = $row->email;
	       	}
        }
        elseif($with == 'username')
        {
	        foreach($resultSet as $row)
	        {
	        	$users[$row->user_id] = $row->username;
	       	}
        }
        else
        {
        	foreach($resultSet as $row)
        	{
        		 $users[$row->user_id] = $row->first_name . ' ' . $row->last_name . ($show_email?' (' . $row->email . ')':'');
        	}
        }
        return $users;
    }

    public function getMember($id)
    {
    	if(is_numeric($id))
    	{
        	$id  = (int) $id;
        	$rowset = $this->tableGateway->select(array('user_id' => $id));
    	}
    	elseif(strstr($id, '@'))
    	{
    		$email = $id;
    		$rowset = $this->tableGateway->select(array('email' => $email));
    	}
    	else
    	{
    		$username = $id;
    		$rowset = $this->tableGateway->select(array('username' => $username));
    	}
        $row = $rowset->current();
        if (!$row) {
        	return false;
        }
        return $row;
    }

    public function saveMember(Member $user)
    {
    	
    	$bcrypt = new Bcrypt;
    	$bcrypt->setCost(14);
    	$encoded_pass= $bcrypt->create($user->password);
    	
    	$data = array(
			'user_id'  		=> $user->user_id,
			'first_name'  	=> $user->first_name,
			'last_name'  	=> $user->last_name,
			'username'  	=> $user->username,
			'email'  		=> $user->email,
			'display_name'  => $user->display_name,
			'password'  	=> $encoded_pass,
			'role'  		=> $user->role,
			'state'  		=> $user->state,
			'dob'  			=> $user->dob,
			'company'  		=> $user->company,
			'about'  		=> $user->about,
			'status'  		=> $user->status,
			'date_created'  => $user->date_created,
			'last_login'  	=> $user->last_login,
			'date_modified'	=> $user->date_modified,
			'image'  		=> $user->image,
        );
    	/* echo "<pre>";
    	print_r($data);
    	exit; */
        $user_id = (int)$user->user_id;
       	if ($user_id == 0) {
       		$data['status'] = 0; 
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
        	if($user->password == '')
        	{
        		unset($data['password']);
        	}
            if ($cat = $this->getMember($user_id)) {
            	if($data["image"] != '')
            	{
            		if($cat->image != '' && file_exists('./public/uploads/user/'.$cat->image))
            		{
            			unlink('./public/uploads/user/'.$cat->image);
            		}
            	}
            	$this->tableGateway->update($data, array('user_id' => $user_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    /****************************************
     * Purpose: Used to update the us 
     * Created By: Kedar
     * Created On: Aug 20, 2014
     * Modified By: Kedar
     * Modified On: Aug 20, 2014
     ****************************************/
    public function approveMember($user_id)
    {
    	$data = array(
    			'state'  		=> '1',
    			'status'  		=> '1',
    			'verificationcode'	=> '',
    	);
    	$this->tableGateway->update($data, array('user_id' => $user_id));
    	
    }
    
    /****************************************
     * Purpose: Used to send verification email to the user. 
     * Created By: Kedar
     * Created On: Aug 20, 2014
     * Modified By: Kedar
     * Modified On: Aug 20, 2014
     ****************************************/
    public function verificationMail($user_id)
    {
    	global $template_placeholders;
    	$length = 10;
    	$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    	$data = array(
    			'verificationcode' => $randomString,
    	);
    	$this->tableGateway->update($data, array('user_id' => $user_id));
    	
    	$user = $this->getMember($user_id);
    	$verifictaion_link = HOME_URL . 'user/verifyemail/' . urlencode(base64_encode($user->user_id)) . '/' . $user->verificationcode;
    	 
    	$template_placeholders['{{TO_USER_NAME}}'] 				= ($user->display_name)?$user->display_name:$user->username;
    	$template_placeholders['{{TO_USER_EMAIL}}'] 			= $user->email;;
    	$template_placeholders['{{TO_USER_NAME_LINK}}'] 		= '<a href="'.HOME_URL.'user/' . $user->username . '">'.$template_placeholders['{{TO_USER_NAME}}'].'</a>';
    	$template_placeholders['{{USER_INBOX_LINK}}'] 			= '<a href="'.HOME_URL.'user/login?redirect=' . HOME_URL . 'user/msg/inbox">Inbox Messages</a>';
    	$template_placeholders['{{EMAIL_VERIFICATION_LINK}}'] 	= $verifictaion_link;
    	$email_template = $this->getTemplate('user_registration');
    	
    	$msg_subject	= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->subject);
    	$msg_body 		= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->description);
    	$msg_from 		= $email_template->from_default;
    	 
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: " . $msg_from;
		
    	if(mail($user->email, $msg_subject, $msg_body, $headers))
    	{
    		return true;
    	}
    	return false;
    }

    /****************************************
     * Purpose: Used to generate forget password email 
     * Created By: Kedar
     * Created On: Oct 6, 2014
     * Modified By: Kedar
     * Modified On: Oct 6, 2014
     ****************************************/
    public function forgotPassMail($user_id)
    {
    	$length = 10;
    	$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    	$data = array(
    			'verificationcode' => $randomString,
    	);
    	$this->tableGateway->update($data, array('user_id' => $user_id));
    	 
    	$user = $this->getMember($user_id);
    	$reset_pass_link = HOME_URL . 'user/resetpass/' . urlencode(base64_encode($user->user_id)) . '/' . $user->verificationcode;
    	 
    	$template_placeholders['{{TO_USER_NAME}}'] 		= ($user->display_name)?$user->display_name:$user->username;
    	$template_placeholders['{{TO_USER_EMAIL}}'] 	= $user->email;;
    	$template_placeholders['{{TO_USER_NAME_LINK}}'] = '<a href="'.HOME_URL.'user/' . $user->username . '">'.$template_placeholders['{{TO_USER_NAME}}'].'</a>';
    	$template_placeholders['{{USER_INBOX_LINK}}'] 	= '<a href="'.HOME_URL.'user/login?redirect=' . HOME_URL . 'user/msg/inbox">Inbox Messages</a>';
    	$template_placeholders['{{RESET_PASSWORD_LINK}}'] = $reset_pass_link;
    	$email_template = $this->getTemplate('forgot_password');
    	
    	$msg_subject	= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->subject);
    	$msg_body		= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->description);
    	$msg_from 		= $email_template->from_default;
    	 
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: ' . $msg_from . "\r\n";
		
    	if(mail($user->email, $msg_subject, $msg_body, $headers))
    	{
    		return true;
    	}
    	return false;
    }
    
    public function deleteMember($user_id)
    {
        $this->tableGateway->delete(array('user_id' => $user_id));
    }
    
    public function checkUsernameExistance($username = false)
    {
    	$rs = $this->tableGateway->select(function (\Zend\Db\Sql\Select $s) use ($username) {
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('username = ?', array($username)));
    	});
    	
    	if(count($rs) > 0)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    public function checkEmailExistance($email = false)
    {
    	$rs = $this->tableGateway->select(function (\Zend\Db\Sql\Select $s) use ($email) {
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('email = ?', array($email)));
    	});
    		 
    	if(count($rs) > 0)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public function getTopUsers()
    {
    	$top_authors = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s){
    		$s->columns(array(
    			'user_id',
    			'display_name',
    			'username',
    			'image',
    			'status', //=> new \Zend\Db\Sql\Predicate\Expression('if(article.status = \'1\', \'Actvie\', \'Deactive\')'),
    			//'comments' => new \Zend\Db\Sql\Predicate\Expression('(SELECT count(article_id) FROM article WHERE author_id=user.user_id)')
    		))->join(
    			'article', // table name
    			'author_id = user.user_id', // expression to join on (will be quoted by platform object before insertion),
    			array('article_count' => new \Zend\Db\Sql\Predicate\Expression('SUM(visits)')), // (optional) list of columns, same requirements as columns() above
    			$s::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
    		);
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article.status = ? AND user.status = ?', array('1', '1')));
    		$s->group('user.user_id');
	    	$s->order('article_count desc');
	    	$s->limit(10);
    	});
    	 
    	return $top_authors;
    }
    
    /****************************************
     * Purpose: Used to get all the followers of the user with pagination 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function fetchUserFollowers($user_id = false)
    {
    	if($user_id)
    	{ 
	    	$select = new \Zend\Db\Sql\Select('user');
			$select->columns(array(
	    				'user_id',
						'username',
						'first_name',
						'last_name',
						'display_name',
						'email',
						'image',
	    				/* 'articles_posted',
	    				'comments_posted', */
				))
	    		->join(
	    				'follow',
	    				'follower_id = user.user_id', 
	    				array(), 
	    				$select::JOIN_LEFT 
	    		);
			$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('followed_id = ? AND user.status = ?', array($user_id, '1')));
			
			$select->order('follow.follow_id DESC');
			 
	    	/* echo  $select->getSqlString();
	    	 exit; */
	    
	   		$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
	   		$resultSetPrototype->setArrayObjectPrototype(new Member());
	   		$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
	  				$select,
	   				$this->tableGateway->getAdapter(),
	   				$resultSetPrototype
	    		);
	   		$paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
	   		return $paginator;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to get the users followed by the user 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function fetchUserFollowedByUser($user_id = false)
    {
    
    	if($user_id)
    	{
	    	$select = new \Zend\Db\Sql\Select('user');
	    	$select->columns(array(
	    			'user_id',
	    			'username',
	    			'first_name',
	    			'last_name',
	    			'display_name',
	    			'email',
					'image',
	    			/* 'articles_posted',
	    				'comments_posted', */
	    	))
	    	->join(
	    			'follow',
	    			'followed_id = user.user_id',
	    			array(),
	    			$select::JOIN_LEFT
	    	);
			$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('follower_id = ? AND user.status = ?', array($user_id, '1')));
	    	$select->order('follow.follow_id DESC');
	    		
	    	/* echo  $select->getSqlString();
	    	 exit; */
	    
	    	$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
	    	$resultSetPrototype->setArrayObjectPrototype(new Member());
	    	$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
	    			$select,
	    			$this->tableGateway->getAdapter(),
	    			$resultSetPrototype
	    	);
	    	$paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
	    	return $paginator;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to updat the user profile from front-end 
     * Created By: Kedar
     * Created On: Sep 3, 2014
     * Modified By: Kedar
     * Modified On: Sep 3, 2014
     ****************************************/
    public function updateMemberProfile($postData, $user_id)
    {
    	if(is_array($postData) && $user_id)
    	{
    		$user = $this->getMember($user_id);
    		
    		if(isset($postData['cur_pass']) && $postData['cur_pass'] != '' && isset($postData['new_pass']) && $postData['new_pass'] != '' && isset($postData['conf_pass']) && $postData['conf_pass'] != '')
    		{
    			$bcrypt = new Bcrypt;
    			$bcrypt->setCost(14);
    			/* echo $postData['cur_pass'];
    			echo "----";
    			echo $old_encoded_pass = $bcrypt->create($postData['cur_pass']);
    			echo "----";
    			echo $user->password;
    			exit; */
    			if($bcrypt->verify($postData['cur_pass'], $user->password))
    			{
    				if($postData['new_pass'] == $postData['conf_pass'])
    				{
    					$encoded_pass = $bcrypt->create($postData['new_pass']);
    					$data['password'] = $encoded_pass;
    				}
    				else
    				{
	    				return false;
    				}
    			}
    			else
    			{
    				return false;
    			}
    		}
    		
    		if(isset($postData['about']))
	    	{
	    		$data['about'] = $postData['about'];
	    	}
	   		if(isset($postData['company']))
	    	{
	    		$data['company'] = $postData['company'];
	    	}
	   		if(isset($postData['company_url']))
	    	{
	    		$data['company_url'] = $postData['company_url'];
	    	}
	    	if(isset($postData['company_info']))
	    	{
	    		$data['company_info'] = $postData['company_info'];
	    	}
	    	if(isset($postData['email']))
	    	{
	    		$data['email'] = $postData['email'];
	    	}
	   		if(isset($postData['display_name']))
	    	{
	    		$data['display_name'] = $postData['display_name'];
	    	}
	    	if(isset($postData['image']))
	    	{
	    		$data['image'] = $postData['image'];
	    	}
	    	$this->tableGateway->update($data, array('user_id' => $user_id));
	    	return true;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to get the user lists 
     * Created By: Kedar
     * Created On: Sep 18, 2014
     * Modified By: Kedar
     * Modified On: Sep 18, 2014
     ****************************************/
    public function getUsersList($keyword = '', $limit = false)
    {
    	if($keyword === '')
    	{
    		return false;
    	}
    
    	$rowset = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($keyword, $limit)
    	{
    		$select->where->nest
    		->like('username', '%'.$keyword.'%')
    		->or->like('display_name', '%'.$keyword.'%')
    		->unnest;
    		$select->where->like('status', '1');
    		$select->order('display_name ASC');
    		if($limit)
    		{
    			$select->limit($limit);
    		}
    	});
    	$users = array();
    
    	if ($rowset)
    	{
    		foreach($rowset as $row)
    		{
    			$users[$row->username] = $row->display_name?$row->display_name:$row->username;
    		}
    	}
    
    	return $users;
    }
    
    /****************************************
     * Purpose: Used to updat the user profile from front-end
    * Created By: Kedar
    * Created On: Sep 3, 2014
    * Modified By: Kedar
    * Modified On: Sep 3, 2014
    ****************************************/
    public function resetPassword($postData, $user_id)
    {
    	if(is_array($postData) && $user_id)
    	{
    		$user = $this->getMember($user_id);
    
    		if(isset($postData['new_pass']) && $postData['new_pass'] != '' && isset($postData['conf_pass']) && $postData['conf_pass'] != '')
    		{
    			$bcrypt = new Bcrypt;
    			$bcrypt->setCost(14);
    			
    			if($postData['new_pass'] == $postData['conf_pass'])
    			{
    				$encoded_pass = $bcrypt->create($postData['new_pass']);
    				$data['password'] = $encoded_pass;
    			}
    			else
    			{
    				return false;
    			}
    		}
    
    		$this->tableGateway->update($data, array('user_id' => $user_id));
    		return true;
    	}
    	return false;
    }
    
    public function getTemplate($template_alias)
    {
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('template');
    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('alias = ?', array($template_alias)));
    	 
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$row = $resultSet->current();
    	return $row;
    }
}