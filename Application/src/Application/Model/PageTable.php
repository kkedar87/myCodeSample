<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class PageTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /*public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }*/
	
	public function fetchAll($paginated=false, $params = false, $whr = array())
    {
        $sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	
    	if ($paginated) {
            // create a new Select object for the table album
            $select = new \Zend\Db\Sql\Select('page');
			
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
		    
		    // create a new result set based on the Album entity
             $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
             $resultSetPrototype->setArrayObjectPrototype(new Page());
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
     
    public function getAll($params = false, $whr = array())
    {
        $limit 	= isset($params['limit'])	?	$params['limit']	:	0;
    	$start 	= isset($params['start'])	?	$params['start']	:	0;
    	$sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	$total 	= isset($params['total'])	?	$params['total']	:	false;
    	$status = isset($params['status'])	?	$params['status']	:	false;
    	
    	$rowset = $this->tableGateway
    				->select(function (\Zend\Db\Sql\Select $select) 
    				use ($whr, $limit, $start, $sort, $order, $total, $status) 
    				{
    					if(!empty($whr))
			      		{
			      			foreach($whr as $func => $data)
			      			{
				      			$select->where->$func($data['column'], $data['value']);
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
			      		
					    if($limit != 0)
					    {
						    $select->limit($limit);
					    }
					    
    					if($start != 0)
					    {
						    $select->offset($start);
					    }
    					
			      		if($total)
			      		{
			      			$count_exp = new \Zend\Db\Sql\Expression('COUNT(page_id)');
	    					$select->columns(array($count_exp => 'rows'));
			      		}
			      		
			      		if($status)
			      		{
			      			$select->where->equalTo('status', $status);
			      		}
					});
    	
    	if (!$rowset) {
            return false;
        }
        return $rowset;
    	
    }
    
    public function getPagesWithCategories()
    {
        $select = $this	->tableGateway
        				->getSql()
        				->select()
        				->join('category', 
        						'page.category_id=category.category_id',
        						array('name')
        					);
		$resultSet = $this->tableGateway->selectWith($select);
		
        foreach ($resultSet as $page)
		{
			echo "<pre>";
			print_r($page);
		} 
		exit;
		//$resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getPage($id)
    {
    	if(!is_numeric($id))
    	{
    		$alias = $id;
    		$rowset = $this->tableGateway->select(array('alias' => $alias));
    	}
    	else
    	{
	    	$id  = (int) $id;
    	    $rowset = $this->tableGateway->select(array('page_id' => $id));
    	}
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCategoryPage($cat_id = '', $limit = 0, $page_id_exclude = 0)
    {
    	if($cat_id == '')
    	{
    		return false;
    	}
    	$cat_id  = (int) $cat_id;
    	
    	if($limit == 0)
    	{
	    	$rowset = $this->tableGateway->select(array('category_id' => $cat_id));
    	}
    	else
    	{
    		$rowset = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($cat_id, $page_id_exclude, $limit) {
						    $select->where(array('category_id' => $cat_id));
						    if($page_id_exclude != 0)
						    {
							    $select->where->notEqualTo('page_id', $page_id_exclude);
						    }
						    
						    $rand = new \Zend\Db\Sql\Expression('RAND()');
				      		$select->order($rand);
							
				      		$select->limit($limit);
						});
			//$rowset = $this->tableGateway->select(array('category_id' => $cat_id))->limit($limit);
    	}	
    	
    	if (!$rowset) {
            return false;
        }
        return $rowset;
    }
    
    public function savePage(Page $page)
    {
        $data = array(
            'title'  			=> $page->title,
            'content'  			=> $page->content,
            'meta_title' 		=> $page->meta_title,
	        'meta_description' 	=> $page->meta_description,
	        'meta_keywords' 	=> $page->meta_keywords,
            'sortorder' 		=> $page->sortorder,
            'status' 			=> $page->status,
        	'image'				=> '',
        );
        
        if($page->image != '')
        {
        	$data['image'] = $page->image;
        }
    	
        $page_id = (int)$page->page_id;
        
		$alias = $this->check_alias($page->alias, $page_id);
		$data['alias'] = $alias;
		
        if ($page_id == 0) {
            $this->tableGateway->insert($data);
            return $page_id = $this->tableGateway->lastInsertValue;
        } else {
            if ($page = $this->getPage($page_id)) {
                
            	if($data["image"] != '')
            	{
            		if($page->image != '' && file_exists('./public/uploads/page/'.$page->image))
            		{
            			unlink('./public/uploads/page/'.$page->image);
            		}
            	}
            	$this->tableGateway->update($data, array('page_id' => $page_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deletePage($page_id)
    {
        $this->tableGateway->delete(array('page_id' => $page_id));
    }
	
	/******************************
	* Purpose: Check the alias of an page and generate a new one for the entity
	* Created By: Kedar
	* Created On: Dec 2, 2013
	* Modified By: Kedar
	* Modified On: Dec 2, 2013
	*****************************/
	function check_alias($alias, $id = false)
    {
    	static $incr_val=1;
    	
    	if($incr_val > 1)
    	{
	    	$first_alias = substr($alias, 0, strrpos($alias, "-"));
    	}
    	else
    	{
    		$first_alias = $alias;
    	}
    	    	
    	if(!$id)
    	{
	    	$entity_count = $this->tableGateway->select(array('alias' => $alias))->count();
    	}
    	else
    	{
    		$entity_count = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($alias, $id) {
						     $select->where(array('alias' => $alias));
						     $select->where->notEqualTo('page_id', $id);
						})->count();
			//$entity_count = $this->tableGateway->select(array('alias' => $alias, 'page_id <> '.$id => '1=1'));
    	}
    	if($entity_count > 0)
		{
			$alias = $first_alias."-".($incr_val);
			$incr_val++;
			return self::check_alias($alias, $id);
		}
		else
		{
			return $alias;
		}
    }
    
    public function savePageOrder($page_order)
    {
        foreach($page_order as $page_id => $pg_order)
        {
        	//echo $page_id . "---" . $pg_order;exit;
        	$this->tableGateway->update(array('sort_order'=>$pg_order), array('page_id' => $page_id));
        }
    }
}