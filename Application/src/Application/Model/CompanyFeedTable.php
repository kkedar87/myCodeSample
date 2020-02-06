<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;

class CompanyFeedTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /****************************************
     * Purpose: Used feth the company's data feed on the basis of symbol 
     * Created By: Kedar
     * Created On: Jul 7, 2014
     * Modified By: Kedar
     * Modified On: Jul 7, 2014
     ****************************************/
    public function getCompanyData($symbol = false)
    {
		if(!$symbol)
		{
			return false;
		}
		
    	$rowset = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($symbol) 
    					{
	    					$select->where->like('symbol', $symbol);
	     					$select->order('update_time DESC');
						});
		
		$feed_data = array();
		
		if ($rowset)
		{
			foreach($rowset as $row)
			{
				$feed_data[] = $row;	
			}
		}
        
		return $feed_data;
    }

    /****************************************
     * Purpose: Used to get 52 Week High
    * Created By: Kedar
    * Created On: Sep 12, 2014
    * Modified By: Kedar
    * Modified On: Sep 12, 2014
    ****************************************/
    public function get52WkHigh($symbol = false)
    {
    	if(!$symbol)
    	{
    		return false;
    	}
    
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('company_data_feeds');
    
    	$select->columns(array(
    			'value' => new \Zend\Db\Sql\Predicate\Expression(' MAX(high_price) '),
    	));
    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression("symbol = ? AND STR_TO_DATE(update_date,'%m/%d/%Y') >= ?", array($symbol, date('Y-m-d', strtotime("-".(52*7)." day"))) ));
    	/* echo  str_replace('"', '`', $select->getSqlString());
    	exit; */
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$row = $resultSet->current();
    	return $row->value;
    }
    
    /****************************************
     * Purpose: Used to get 52 week low
    * Created By: Kedar
    * Created On: Sep 12, 2014
    * Modified By: Kedar
    * Modified On: Sep 12, 2014
    ****************************************/
    public function get52WkLow($symbol = false)
    {
    	if(!$symbol)
    	{
    		return false;
    	}
    
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('company_data_feeds');
    
    	$select->columns(array(
    			'value' => new \Zend\Db\Sql\Predicate\Expression(' Min(low_price) '),
    	));
    	$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression("symbol = ? AND STR_TO_DATE(update_date,'%m/%d/%Y') >= ?", array($symbol, date('Y-m-d', strtotime("-".(52*7)." day"))) ));
    	/* echo  str_replace('"', '`', $select->getSqlString());
    	exit; */
    	$selectString = $sql->getSqlStringForSqlObject($select);
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$row = $resultSet->current();
    	return $row->value;
    }    
}