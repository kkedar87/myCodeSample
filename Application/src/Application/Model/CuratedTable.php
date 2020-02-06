<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CuratedTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

	public function fetchAll($paginated=false, $params = false, $whr = array())
    {
    	$sort 	= isset($params['sort'])	?	$params['sort']		:	'curated_id';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	 
    	if ($paginated) {
    		$select = new \Zend\Db\Sql\Select('curated');
    
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
    
    		$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
    		$resultSetPrototype->setArrayObjectPrototype(new Curated());

    		$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
    				$select,
    				$this->tableGateway->getAdapter(),
    				$resultSetPrototype
    		);
    		$paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
    		return $paginator;
    	}
    	$resultSet = $this->tableGateway->select();
    	return $resultSet;
    }
    
    public function getCurateds($status = false, $limit = false)
    {
	    $resultSet  = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s) use ($status,$limit){
		    if($status !==false)
	    	{
	        	$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('curated.status = ? ', array(''.$status)));
	    	}
	    	
	    	if($limit !== false)
	    	{
	    		$s->limit($limit);
	    	}
		});
        foreach($resultSet as $row)
        {
        	$curateds[$row->curated_id] = $row;
       	}
        return $curateds;
    }

    public function getCurated($id)
    {
    	if(!is_numeric($id))
    	{
    		$alias = $id;
    		$rowset = $this->tableGateway->select(array('alias' => $alias));
    	}
    	else
    	{
	        $id  = (int) $id;
	        $rowset = $this->tableGateway->select(array('curated_id' => $id));
    	}
    	$row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveCurated(Curated $curated)
    {
		  $data = array(
					'curated_id' => $curated->curated_id,
					'title' => $curated->title,
					'description' => $curated->description,
					'link' => $curated->link,
					'date' => $curated->date,
					'status' => $curated->status,
        );
		
        $curated_id = (int)$curated->curated_id;
       	if ($curated_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($testimon = $this->getCurated($curated_id)) {
            	$this->tableGateway->update($data, array('curated_id' => $curated_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteCurated($curated_id)
    {
        $this->tableGateway->delete(array('curated_id' => $curated_id));
    }
}