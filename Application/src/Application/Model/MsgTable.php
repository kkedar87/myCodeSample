<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class MsgTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

   /****************************************
    * Purpose: Used to save msg 
    * Created By: Kedar
    * Created On: Sep 1, 2014
    * Modified By: Kedar
    * Modified On: Sep 1, 2014
    ****************************************/
   public function saveMsg($data)
    {
    	$save_data['msg_id']		= (isset($data['msg_id'])) ? $data['msg_id'] :'';
		$save_data['parent_id']	= (isset($data['parent_id'])) ? $data['parent_id'] :0;
		$save_data['to'] 		= (isset($data['to'])) ? $data['to'] :'';
		$save_data['from'] 		= (isset($data['from'])) ? $data['from'] : 0;
		$save_data['sub'] 		= (isset($data['sub'])) ? $data['sub'] :'';
		$save_data['msg'] 		= (isset($data['msg'])) ? $data['msg'] :'';
		$save_data['read']		= (isset($data['read'])) ? $data['read'] : '0';
		$save_data['status'] 	= (isset($data['status'])) ? $data['status'] :'1';
		$save_data['date'] 		= (isset($data['date'])) ? $data['date'] : date('Y-m-d H:i:s');
    	
		$msg_id = (int)$save_data['msg_id'];
       	if ($msg_id == 0) 
       	{
       		$this->tableGateway->insert($save_data);
       		$msg_id =  $this->tableGateway->lastInsertValue;
       		
           	$to_user 	= $this->getMember($save_data['to']);
           	$from_user 	= $this->getMember($save_data['from']);
           	
           	$template_placeholders['{{TO_USER_NAME}}'] 			= ($to_user->display_name)?$to_user->display_name:$to_user->username;
           	$template_placeholders['{{TO_USER_EMAIL}}'] 		= $to_user->email;
           	$template_placeholders['{{TO_USER_NAME_LINK}}'] 	= '<a href="'.HOME_URL.'user/' . $to_user->username . '">'.$template_placeholders['{{TO_USER_NAME}}'].'</a>';
           	
           	$template_placeholders['{{FROM_USER_NAME}}'] 		= ($from_user->display_name)?$from_user->display_name:$from_user->username;
           	$template_placeholders['{{FROM_USER_EMAIL}}'] 		= $from_user->email;
           	$template_placeholders['{{FROM_USER_NAME_LINK}}'] 	= '<a href="'.HOME_URL.'user/' . $from_user->username . '">'.$template_placeholders['{{FROM_USER_NAME}}'].'</a>';
           	
           	$template_placeholders['{{USER_INBOX_LINK}}'] 		= '<a href="'.HOME_URL.'user/msg/inbox">Inbox Messages</a>';
           	
           	$email_template = $this->getTemplate('new_message');
           	//$email_template = $this->getTemplateTable()->getTemplate('New_message');
           	 
           	$msg_subject	= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->subject);
           	$msg_body 		= str_replace(array_keys($template_placeholders), array_values($template_placeholders), $email_template->description);
           	$msg_from 		= $email_template->from_default;
           	
           	$headers  = 'MIME-Version: 1.0' . "\r\n";
           	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
           	$headers .= "From: " . $msg_from;
           	
           	if(mail($to_user->email, $msg_subject, $msg_body, $headers))
           	{
           	}
           	
           	return $msg_id;
        } 
        else 
        {
        	if ($msg = $this->getMsg($msg_id)) {
            	$this->tableGateway->update($data, array('msg_id' => $msg_id));
            	return true;
            } else {
                throw new \Exception('Msg id does not exist');
            }
        }
    }
    
	/****************************************
	 * Purpose: Used to fetch all the users 
	 * Created By: Kedar
	 * Created On: Sep 3, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 3, 2014
	 ****************************************/
	public function fetchAll($paginated = false, $params = false, $whr = array())
    {
        $sort 	= isset($params['sort'])	?	$params['sort']		:	'date_created';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	
    	if ($paginated) {
            $select = new \Zend\Db\Sql\Select('msg');
			
            $select->columns(array(
            		'msg_id',
            		'parent_id',
            		'to',
            		'from',
            		'sub',
            		'msg',
            		'read',
            		'date',
            		'status' => new \Zend\Db\Sql\Predicate\Expression('if(msg.status = \'1\', \'Actvie\', \'Deactive\')'),
            ))
            ->join(
			    array('from_user' => 'user'),
			    'from_user.user_id = msg.from',
			    array(
			    		'from_username' => new \Zend\Db\Sql\Predicate\Expression('from_user.username'), 
			    		'from_email' => new \Zend\Db\Sql\Predicate\Expression('from_user.email'), 
            		),
				$select::JOIN_LEFT
			)
            ->join(
			    array('to_user' => 'user'),
			    'to_user.user_id = msg.to',
			    array(
			    		'to_username' => new \Zend\Db\Sql\Predicate\Expression('to_user.username'), 
			    		'to_email' => new \Zend\Db\Sql\Predicate\Expression('to_user.email'), 
            		),
				$select::JOIN_LEFT
			);
            
    		if(!empty($whr))
    		{
    			$nest = false;
    			$unnest = false;
    			$select_new = $select->where;
    			foreach($whr as $cond)
    			{
    				$func = $cond['func'];
    
    				if($func == 'NEST')
    				{
    					$nest = true;
    					continue;
    				}
    				if($func == 'UNNEST')
    				{
    					$unnest = true;
    					continue;
    				}
    
    				$with = isset($cond['with'])?$cond['with']:'AND';
    				if($with == '')
    				{
    					$with = 'AND';
    				}
    				$select_new = $select_new->$with;
    
    				if($nest)
    				{
    					$select_new = $select_new ->NEST;
    				}
    				elseif($nest)
    				{
    					$select_new = $select_new ->NEST;
    				}
    
    				$data = $cond['data'];
    				if($func == 'notin')
    				{
    					$select_new = $select_new->addPredicate(new \Zend\Db\Sql\Predicate\Expression($data['column'].' NOT IN (?)', array(implode(",", $data['value']))));
    				}
    				else
    				{
    					$select_new = $select_new->$func($data['column'], $data['value']);
    				}
    				$nest = false;
    				$unnest = false;
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
		    $select->group('msg_id');
		    
		    /* echo  str_replace('"', '`', $select->getSqlString());
		    exit; */
		    
		    // create a new result set based on the Album entity
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Msg());
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
     * Purpose: Used to get all the unread msg count of the user 
     * Created By: Kedar
     * Created On: Sep 3, 2014
     * Modified By: Kedar
     * Modified On: Sep 3, 2014
     ****************************************/
    public function getUserMsgCount($user_id, $read = false)
    {
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('msg');
    	
    	$select->columns(array(
    			'unread_count' => new \Zend\Db\Sql\Predicate\Expression(' count(msg_id) '),
    	));
    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('`to` = ? AND `read` = ?', array($user_id, ($read?'1':'0'))));
    	
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	//echo $selectString;
    	//exit;
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$row = $resultSet->current();
    	return $row->unread_count;
    	 
    }
    
    /****************************************
     * Purpose: Used to ge the message on the basis of message id 
     * Created By: Kedar
     * Created On: Sep 3, 2014
     * Modified By: Kedar
     * Modified On: Sep 3, 2014
     ****************************************/
    public function getMsg($msg_id)
    {
    	$resultset = $this->tableGateway->select(array('msg_id' => $msg_id));
    	
    	if(count($resultset) > 0)
    	{
    		return $resultset->current();
    	}
    	return false;

    }
    
    /****************************************
     * Purpose: Used to update message on the basis of msg_id 
     * Created By: Kedar
     * Created On: Sep 3, 2014
     * Modified By: Kedar
     * Modified On: Sep 3, 2014
     ****************************************/
    public function updateMsg($data, $msg_id)
    {
    	if(is_array($data) && $msg_id)
    	{
    		$rs = $this->tableGateway->update($data, array('msg_id' => $msg_id));
    		return true;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to get the email template
     * Created By: Kedar
     * Created On: Oct 13, 2014
     * Modified By: Kedar
     * Modified On: Oct 13, 2014
     ****************************************/
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
    
    /****************************************
     * Purpose: Used to get member records from the database
     * Created By: Kedar
     * Created On: Oct 13, 2014
     * Modified By: Kedar
     * Modified On: Oct 13, 2014
     ****************************************/
    public function getMember($user_id)
    {
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('user');
    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('user_id = ?', array($user_id)));
    
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$row = $resultSet->current();
    	return $row;
    }    
}
