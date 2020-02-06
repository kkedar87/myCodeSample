<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class FollowTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /****************************************
     * Purpose: Used to 
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    public function saveFollow($follower_id = false, $followed_id = false)
    {
    	if($followed_id && $follower_id)
    	{
    		$data = array(
    				'follower_id'		=> $follower_id,
    				'followed_id'			=> $followed_id,
    		);
    		if(!$this->isFollowing($follower_id, $followed_id))
    		{
    			$this->tableGateway->insert($data);
    			return $this->tableGateway->lastInsertValue;
    		}
    		else
    		{
	    		return true;
    		}
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to check the follow status 
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    public function isFollowing($follower_id = false, $followed_id = false)
    {
    	if($followed_id && $follower_id)
    	{
    		$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
			$select = $sql->select();
			$select->from('follow');
	    	
	    	$select->columns(array(
	    			'follow_count' => new \Zend\Db\Sql\Predicate\Expression(' COUNT(follow_id) '),
	    	));
	    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('follower_id = ? AND followed_id = ?', array($follower_id, $followed_id)));
	    	
	    	$selectString = $sql->getSqlStringForSqlObject($select);
	    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE); 
	    	$row = $resultSet->current();
	    	
	    	if($row->follow_count > 0)
	    	{
	    		return true;
	    	}
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to unfollow a user 
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    public function unfollow($follower_id = false, $followed_id = false)
    {
    	if($followed_id && $follower_id)
    	{
    		$data = array(
    				'follower_id'		=> $follower_id,
    				'followed_id'			=> $followed_id,
    		);
    		$this->tableGateway->delete($data);
    		return true;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to get followers count 
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    public function getFollowerCount($followed_id = false)
    {
    	if($followed_id)
    	{
    		$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
    		$select = $sql->select();
    		$select->from('follow');
    
    		$select->columns(array(
    				'follow_count' => new \Zend\Db\Sql\Predicate\Expression(' COUNT(follow_id) '),
    		));
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('followed_id = ?', array($followed_id)));
    
    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		$row = $resultSet->current();
    
    		return $row->follow_count;
    	}
    	return false;
    }
    
    /****************************************
     * Purpose: Used to follwed count
     * Created By: Kedar
     * Created On: Aug 29, 2014
     * Modified By: Kedar
     * Modified On: Aug 29, 2014
     ****************************************/
    public function getFollowedCount($follower_id = false)
    {
    	if($follower_id)
    	{
    		$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
    		$select = $sql->select();
    		$select->from('follow');
    
    		$select->columns(array(
    				'follow_count' => new \Zend\Db\Sql\Predicate\Expression(' COUNT(follow_id) '),
    		));
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('follower_id = ?', array($follower_id)));
    
    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		$row = $resultSet->current();
    
    		return $row->follow_count;
    	}
    	return false;
    }

    /****************************************
     * Purpose: Used to get the listing of followers 
     * Created By: Kedar
     * Created On: Sep 1, 2014
     * Modified By: Kedar
     * Modified On: Sep 1, 2014
     ****************************************/
    public function getFollowers($user = false)
    {
    	if($followed_id)
    	{
    		$adapter = $this->tableGateway->adapter;
    		$sql = new \Zend\Db\Sql\Sql($adapter);
    		$select = $sql->select();
    		$select->from('follow');
    
    		$select->columns(array(
    				'follow_count' => new \Zend\Db\Sql\Predicate\Expression(' COUNT(follow_id) '),
    		));
    		$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('followed_id = ?', array($followed_id)));
    
    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		$row = $resultSet->current();
    
    		
    		$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
    		$resultSetPrototype->setArrayObjectPrototype(new Follow());
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
    	return false;
    }
}
