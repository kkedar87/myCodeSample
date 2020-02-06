<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;

class CompanyTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /****************************************
     * Purpose: Used to get the company detail
     * Created By: Kedar
     * Created On: Jul 7, 2014
     * Modified By: Kedar
     * Modified On: Jul 7, 2014
     ****************************************/
    public function getCompany($symbol = false)
    {
    	if(!$symbol)
    	{
    		return false;
    	}
    	
    	$whr_ar = array(
    			'symbol' 	=> $symbol,
    	);
    	$rowset = $this->tableGateway->select($whr_ar);
    	$row = $rowset->current();

    	return $row;
    }
    
    /****************************************
     * Purpose: Used to Get all companies list for the dropdown and search options 
     * Created By: Kedar
     * Created On: Jul 7, 2014
     * Modified By: Kedar
     * Modified On: Jul 7, 2014
     ****************************************/
    public function getCompaniesList($keyword = '', $limit = false) 
    {
    	if($keyword === '')
    	{
    		return false;
    	}
    	 
    	$rowset = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($keyword, $limit) 
    					{
	    					$select->where->nest
				    		->like('symbol', '%'.$keyword.'%')
				    		->or->like('name', '%'.$keyword.'%')
				    		->unnest;
	     					$select->order('name ASC');
	     					if($limit)
	     					{
	     						$select->limit($limit);
	     					}
						});
		$companies = array();
		
		if ($rowset)
		{
			foreach($rowset as $row)
			{
				$companies[$row->symbol] = $row->name;	
			}
		}
        
		return $companies;
    }
}