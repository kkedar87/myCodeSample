<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\sql\Select;

class CommentTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated=false, $params = false, $whr = array())
    {
        $sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	
    	if ($paginated) {
            // create a new Select object for the table album
            $select = new \Zend\Db\Sql\Select('comment');
			
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
             $resultSetPrototype->setArrayObjectPrototype(new Comment());
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
     * Purpose: Method is used to get the Comments recursively on the basis of parent comment
     * Created By: Kedar
     * Created On: Aug 5, 2014
     * Modified By: Kedar
     * Modified On: Aug 5, 2014
     ****************************************/
    public function getCommentsParental($article_id, $parent_id = 0, $status = 1, $key = 'comment_id')
    {
		$resultSet = $this->tableGateway->select(function (\Zend\Db\Sql\Select $s) use ($parent_id, $article_id) {
		     $s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('parent_id = ?', array((int)$parent_id)));
		     $s->where->like('article_id', $article_id);
		     $s->order('date_created '.(($parent_id == 0)?'DESC':'ASC'));
		});
    	
    	$comments = array();
    	foreach($resultSet as $row)
    	{
    		$comments[$row->$key] = $row;
    		$children = $this->getCommentsParental($article_id, $row->comment_id, $status, $key);
    		$comments[$row->$key]->children = $children;
    	}
    	return $comments;
    }
    
    public function saveComment(Comment $comment)
    {
    	$data = array(
			'comment_id'		=> $comment->comment_id,
			'comment'			=> $comment->comment,
			'article_id'		=> $comment->article_id,
			'parent_id'			=> $comment->parent_id,
			'author_id'			=> $comment->author_id,
			'ip_address'		=> $comment->ip_address,
			'status'			=> $comment->status,
			'date_created'		=> $comment->date_created,
        );
        $comment_id = (int)$comment->comment_id;
       	if ($comment_id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
            if ($cat = $this->getComment($comment_id)) {
	            	$this->tableGateway->update($data, array('comment_id' => $comment_id));
	            	return $comment_id;
            } else {
                return false;
            }
        }
    }
    
    /****************************************
     * Purpose: Used to get the single comment data for the comments on ajax based 
     * Created By: Kedar
     * Created On: Aug 1, 2014
     * Modified By: Kedar
     * Modified On: Aug 1, 2014
     ****************************************/
    public function getComment($comment_id = false)
    {
    	if($comment_id)
    	{
    		$rs = $this->tableGateway->select(array('comment_id' => $comment_id));
    		if($rs)
    		{
	    		$row = $rs->current();
	    		return $row;
    		}
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to get the comments count of a secific user 
     * Created By: Kedar
     * Created On: Aug 28, 2014
     * Modified By: Kedar
     * Modified On: Aug 28, 2014
     ****************************************/
    public function getUserCommentsCount($user_id = false)
    {
    	if($user_id)
    	{
	    	$adapter = $this->tableGateway->adapter;
	    	$sql = new \Zend\Db\Sql\Sql($adapter);
			$select = $sql->select();
			$select->from('comment');
	    	
	    	$select->columns(array(
	    			'comments_count' => new \Zend\Db\Sql\Predicate\Expression(' count(comment_id) '),
	    	))
	    	->join(
    				'article',
    				'article.article_id = comment.article_id', 
    				array(), 
    				$select::JOIN_LEFT 
    		);
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('comment.author_id = ? AND comment.status = ? AND article.status = ?', array($user_id, '1', '1')));
    		
	    	//$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('author_id = ? AND parent_id = ?', array($user_id, '0')));
	    	//$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('author_id = ?', array($user_id)));
	    	
	    	$selectString = $sql->getSqlStringForSqlObject($select);
	    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE); 
	    	$row = $resultSet->current();
			return $row->comments_count;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to list the comments posted by user on articles 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function getUserPostedComments($user_id = false, $limit = false)
    {
    	if($user_id)
    	{
	    	$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
    		$select = $sql->select();
    		$select->from('comment');
    
    		
    		$select->columns(array(
    				'comment_id',
					'comment',
					'article_id',
					'author_id',
					'date_created',
    		))
    		->join(
    				'article',
    				'article.article_id = comment.article_id', 
    				array('article_title' => 'title', 'article_alias' => 'alias'), 
    				$select::JOIN_LEFT 
    		);
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('comment.author_id = ? AND comment.status = ? AND article.status = ?', array($user_id, '1', '1')));
    		$select->order('date_created DESC');
    		if(!$limit)
    		{
    			$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
    			$resultSetPrototype->setArrayObjectPrototype(new Comment());
    			$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
    					$select,
    					$this->tableGateway->getAdapter(),
    					$resultSetPrototype
    			);
    			$paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
    			return $paginator;
    		}
    		else 
    		{
    			$select->limit($limit);
    		}
    		
    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		/* echo "<pre>";
    		foreach($resultSet as $row)
    		{
    			print_r($row);
    		}
    		exit; */
    		return $resultSet;
    		
    	}
    	return false;
    }
    
	/****************************************
	 * Purpose: Used to get users commented on the articles posted by the user 
	 * Created By: Kedar
	 * Created On: Sep 1, 2014
	 * Modified By: Kedar
	 * Modified On: Sep 1, 2014
	 ****************************************/
	public function getUsersWhoPostedCommentsOnUserArticles($user_id = false, $limit = false)
    {
    	if($user_id)
    	{
    		$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
    		$select = $sql->select();
    		$select->from('comment');
    
    
    		$select->columns(array(
    				'comment_id',
    				'comment',
    				'article_id',
    				'author_id',
    				'date_created',
    		))
    		->join(
    				'article',
    				'article.article_id = comment.article_id',
    				array('article_title' => 'title', 'article_alias' => 'alias'),
    				$select::JOIN_LEFT
    		)->join(
    				'user',
    				'comment.author_id = user.user_id',
    				array('display_name','first_name', 'last_name', 'username', 'email'),
    				$select::JOIN_LEFT
    		);
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article.author_id = ? AND comment.status = ? AND article.status = ? AND user.status = ?', array($user_id, '1', '1', '1')));
    		$select->order('date_created DESC');
    		if(!$limit)
    		{
    			$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
    			$resultSetPrototype->setArrayObjectPrototype(new Comment());
    			$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
    					$select,
    					$this->tableGateway->getAdapter(),
    					$resultSetPrototype
    			);
    			$paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
    			return $paginator;
    		}
    		else 
    		{
    			$select->limit($limit);
    		}
    
    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		return $resultSet;
    
    	}
    	return false;
    }
	 /****************************************
     * Purpose: Used to get the comments count of a specific article 
     * Created By: Ayush
     * Created On: Aug 28, 2014
     * Modified By: Ayush
     * Modified On: Aug 28, 2014
     ****************************************/
    public function getArticleCommentsCount($article_id = false)
    {
    	if($article_id)
    	{
	    	$adapter = $this->tableGateway->adapter;
	    	$sql = new \Zend\Db\Sql\Sql($adapter);
			$select = $sql->select();
			$select->from('comment');
	    	
	    	$select->columns(array(
	    			'comments_count' => new \Zend\Db\Sql\Predicate\Expression(' count(article_id) '),
	    	));
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article_id = ? AND status = ?', array($article_id, '1')));
    		//$select->group(article_id);
	    	//$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('author_id = ? AND parent_id = ?', array($user_id, '0')));
	    	//$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('author_id = ?', array($user_id)));
	    	
	    	$selectString = $sql->getSqlStringForSqlObject($select);
	    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE); 
	    	$row = $resultSet->current();
			return $row->comments_count;
    	}
    	return false;
    }
	
	
}