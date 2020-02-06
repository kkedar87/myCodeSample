<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CategoryTable
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
            $select = new \Zend\Db\Sql\Select('category');
			
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
             $resultSetPrototype->setArrayObjectPrototype(new Category());
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

    public function getCategories()
    {
        $resultSet = $this->tableGateway->select();
        $categories = array();
        foreach($resultSet as $row)
        {
        	$categories[$row->category_id] = $row->name;
       	}
        return $categories;
    }

    public function getCategory($id)
    {
    	if($id)
    	{
	    	if(is_numeric($id))
	    	{
		        $rowset = $this->tableGateway->select(array('category_id' => $id));
	    	}
	    	else 
	    	{
	    		$rowset = $this->tableGateway->select(array('alias' => $id));
	    	}
	        $row = $rowset->current();
	        if (!$row) {
	            throw new \Exception("Could not find row $id");
	        }
	        return $row;
    	}
    	return false;
    }

    public function saveCategory(Category $category)
    {
        $data = array(
            'alias' 		=> $category->alias,
            'name'  		=> $category->name,
            'description'  	=> $category->description,
            'image'			=> $category->image,
            'parent_id'		=> $category->parent_id,
            'sortorder'		=> $category->sortorder,
            'status'		=> $category->status,
        );
        $category_id = (int)$category->category_id;
       	if ($category_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($cat = $this->getCategory($category_id)) {
            	if($data["image"] != '')
            	{
            		if($cat->image != '' && file_exists('./public/uploads/category/'.$cat->image))
            		{
            			unlink('./public/uploads/category/'.$cat->image);
            		}
            	}
            	$this->tableGateway->update($data, array('category_id' => $category_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteCategory($category_id)
    {
        $this->tableGateway->delete(array('category_id' => $category_id));
    }
    
	/******************************
	* Purpose: Method is used to get the categories recursively with parent children array
	* Created By: Kedar
	* Created On: May 29, 2014
	*****************************/
	public function getCategoriesParental($parent_id = '0', $status = '1', $key = 'category_id')
    {
    	$whr_ar = array('parent_id' => $parent_id, 'status' => $status);
    	$resultSet = $this->tableGateway->select($whr_ar);
        $categories = array();
        foreach($resultSet as $row)
        {
        	$categories[$row->$key] = $row;
        	$children = $this->getCategoriesParental($row->category_id, $status);
        	$categories[$row->$key]->children = $children;
       	}
       	return $categories;
    }

    /******************************
	* Purpose: Method is used to get the categories recursively with parent children with dropdown options
	* Created By: Kedar
	* Created On: May 29, 2014
	*****************************/
	public function getCategoriesParentalOptions($parent_id = 0, $selected = 0, $status = '1', $recusive_count = 0)
    {
    	$resultSet = $this->tableGateway->select(array('parent_id' => $parent_id, 'status' => $status));
        $category_options = '';
        
        if($resultSet)
        {
	        foreach($resultSet as $row)
	        {
	        	$category_options .= '<option value="' . $row->category_id . '" style="padding-left: ' . ($recusive_count*30) . 'px"' . 
	        							(($selected == $row->category_id)?'selected="selected"': '') . '>' . $row->name . '</option>';
	        	$category_options .= $this->getCategoriesParentalOptions($row->category_id, $selected, $status, ++$recusive_count);
	        	--$recusive_count;
	        }
        }
       	return $category_options;
    }
    
    /******************************
     * Purpose: Method is used to get the categories recursively with parent children with checkboxes options
    * Created By: Kedar
    * Created On: May 29, 2014
    *****************************/
	public function getCategoriesParentalCheckboxes($parent_id = 0, $selected = array(), $status = '1', $recusive_count = 0)
    {
    	$resultSet = $this->tableGateway->select(array('parent_id' => $parent_id, 'status' => $status));
    	$category_options = '';
    
    	if($resultSet)
    	{
    		$category_options .= '<div class="tbr"><ul id="sub_tab_' . $parent_id . '">';
    		foreach($resultSet as $row)
    		{
    			$category_options .= '<li>';
      			$category_options .= '<span class="cat_group cat_checkbox opened " id="tab_' . $row->category_id . '"><input type="checkbox" name="categories[]" value="' . $row->category_id . '"' . (in_array($row->category_id, $selected)?' checked="checked"':'') . '>' . $row->name . '</span>';
    			$category_options .= $this->getCategoriesParentalCheckboxes($row->category_id, $selected, $status, ++$recusive_count);
    			--$recusive_count;
    			$category_options .= '</li>';
    		}
    		$category_options .= '</ul></div>';
    	}
    	return $category_options;
    }
}