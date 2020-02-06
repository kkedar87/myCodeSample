<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CategoryArticleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveCategory_Article($article_id = false, $category_ids = array())
    {
    	if(!$article_id)
    	{
    		return false;
    	}
    	else
    	{
    		$this->tableGateway->delete(array('article_id' => $article_id));
    	}
    	
    	if(count($category_ids) <= 0)
    	{
    		return false;
    	}

    	foreach($category_ids as $category_id)
    	{
    		$data = array(
    			'article_id'	=> $article_id,
    			'category_id'	=> $category_id,
    		);
    		$this->tableGateway->insert($data);
    	}

    	return true;
    }
    
    public function getArticleCategories($article_id = false)
    {
	
    	$resultSet = $this->tableGateway->select(array('article_id' => $article_id));
    	$article_categories = array();
    	foreach ($resultSet as $row)
    	{
    		$article_categories[] = $row->category_id;
    	}
    	return $article_categories;
    }

    /****************************************
     * Purpose: Used to get the categries associated with article for article tags
     * Created By: Kedar
     * Created On: Sep 30, 2014
     * Modified By: Kedar
     * Modified On: Sep 30, 2014
     ****************************************/
    public function getArticleCategoriesNames($article_id = false)
    {
    	$resultSet  = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s)use ($article_id){
    		$s->columns(array(
    				'category_id',
    		))
    		->join(
    				'category',
    				'category.category_id = category_article.category_id',
    				array('cat_name' => 'name'),
    				$s::JOIN_LEFT
    		);
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('category.status = ? AND category_article.article_id = ?', array('1', $article_id)));
    	});
    	
    	$article_categories = array();
    	foreach($resultSet as $row)
    	{
    		$article_categories[$row->category_id] = $row->cat_name;
    	}
    	 
    	return $article_categories;
    }
    
    /****************************************
     * Purpose: Used to get article categories with alias 
     * Created By: Kedar
     * Created On: Oct 14, 2014
     * Modified By: Kedar
     * Modified On: Oct 14, 2014
     ****************************************/
    public function getArticleCategoriesNamesWithAlias($article_id = false)
    {
    	$resultSet  = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s)use ($article_id){
    		$s->columns(array(
    				'category_id',
    		))
    		->join(
    				'category',
    				'category.category_id = category_article.category_id',
    				array('cat_name' => 'name', 'cat_alias' => 'alias'),
    				$s::JOIN_LEFT
    		);
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('category.status = ? AND category_article.article_id = ?', array('1', $article_id)));
    	});
    		 
    		$article_categories = array();
    		foreach($resultSet as $row)
    		{
    			$article_categories[$row->category_id] = $row;
    		}
    
    		return $article_categories;
    }
}