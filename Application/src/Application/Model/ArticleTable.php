<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ArticleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /****************************************
     * Purpose: Used to fetch all the articles 
     * Created By: Kedar
     * Created On: Jul 9, 2014
     * Modified By: Kedar
     * Modified On: Jul 9, 2014
     ****************************************/
    public function fetchAll($paginated=false, $params = false, $whr = array())
    {
        $sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
        $order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	$status	= isset($params['status'])	?	$params['status']	:	'-1';
    	$limit 	= isset($params['limit'])	?	$params['limit']	:	ARTICLES_ON_HOME_PAGE;
    	 
    	//if ($paginated) {
            // create a new Select object for the table album
            $select = new \Zend\Db\Sql\Select('article');
			
            if($status != -1)
            {
            	$select->where->like('article.status', $status);
            }
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
      		$select->join(
      				'user',
      				new \Zend\Db\Sql\Predicate\Expression('user.user_id = article.author_id', array()),
      				//' . $sub_cat,
      				array('user_image' => 'image', 'username', 'display_name', 'first_name', 'last_name')
      				//$select::JOIN_OUTER // (optional), one of inner, outer, left, right also represented by constants in the API
      		);
      		
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
		    
		    if(!$paginated)
		    {
		    	$select->limit(1);
		    	$select->offset(0);
		    }
		    /* 
		    echo  str_replace('"', '`', $select->getSqlString());
		    exit; */
		    
		    // create a new result set based on the Album entity
             $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
             $resultSetPrototype->setArrayObjectPrototype(new Article());
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
         //}
         //$resultSet = $this->tableGateway->select();
         //return $resultSet;
    }
    
    /****************************************
     * Purpose: Used to fetch Articles and Blogs in search
    * Created By: Kedar
    * Created On: Jul 14, 2014
    * Modified By: Kedar
    * Modified On: Jul 14, 2014
    ****************************************/
    public function fetchAllNew($paginated=false, $params = false, $whr = array())
    {
    	$sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	$status	= isset($params['status'])	?	$params['status']	:	'-1';
    	$limit 	= isset($params['limit'])	?	$params['limit']	:	ARTICLES_ON_HOME_PAGE;
    	 
    	if ($paginated) {
    		// create a new Select object for the table album
    		$select = new \Zend\Db\Sql\Select('article');
    
    		$select->columns(array(
    				'article_id',
					'alias',
					'type',
					'author_id',
					'title',
					'bullet1',
					'bullet2',
					'bullet3',
					'bullet4',
					'bullet5',
					'description',
					'tickers',
					'primary_ticker',
					'secondary_ticker',
					'category',
					'position_dislosure',
					'position_types',
					'position_stocks',
					'position_other_info',
					'business_rel_disclosure',
					'business_rel_not_own_specify',
					'user_site_url',
					'user_site_name',
					'mini_bio',
					'visits',
					'date_created',
    				'editors_choice',
    				'status', //=> new \Zend\Db\Sql\Predicate\Expression('if(article.status = \'1\', \'Actvie\', \'Deactive\')'),
    				'comments' => new \Zend\Db\Sql\Predicate\Expression('(SELECT count(comment_id) FROM comment WHERE article_id=article.article_id)')
    		))
    		->join(
    				'user', // table name
    				'user_id = article.author_id', // expression to join on (will be quoted by platform object before insertion),
    				array('username', 'email', 'user_image' => 'image'), // (optional) list of columns, same requirements as columns() above
    				$select::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
    		)
    		->join(
    				array('ac' => new \Zend\Db\Sql\Predicate\Expression('(SELECT ca.article_id, GROUP_CONCAT(c.name) as cat_names, GROUP_CONCAT(c.alias) as cat_aliases FROM `category_article` ca LEFT JOIN `category` c ON c.category_id = ca.category_id WHERE c.name IS NOT NULL GROUP BY ca.article_id)')), // table name
    				'ac.article_id = article.article_id', // expression to join on (will be quoted by platform object before insertion),
    				array('cat_names', 'cat_aliases'), // (optional) list of columns, same requirements as columns() above
    				$select::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
    		);
    		/* $select->from(array('a' => 'article'))
    		 ->columns(array('a.*', 'username' => 'u.username'))
    		->join(array('u' => 'user'), 'a.author_id = u.user_id')
    		; */
    		/* echo "<pre>";
    		 print_r($whr);
    		exit; */
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
    		
    		if(isset($status) && $status != '-1')
    		{
    			$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article.status IN (?)', array($status)));
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
    		
    		if(isset($_GET['qr']))
    		{
    			echo  str_replace('"', '`', $select->getSqlString());
    		}
    		
    		/*echo  str_replace('"', '`', $select->getSqlString());
		    exit;*/
    
    		// create a new result set based on the Album entity
    		$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
    		$resultSetPrototype->setArrayObjectPrototype(new Article());
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
     * Purpose: Used to fetch the active articles for category specific 
     * Created By: Kedar
     * Created On: Jul 9, 2014
     * Modified By: Kedar
     * Modified On: Jul 9, 2014
     ****************************************/
    public function fetchCategoryArticlesAll($paginated=false, $cat = '', $sub_cat = '', $user = '', $type = 'article')
    {
	   	if ($paginated) {
            // create a new Select object for the table album
            $select = new \Zend\Db\Sql\Select('article');
            $select->where->like('article.status', '1');
            
            $select->columns(array(
            		'article_id',
					'alias',
					'type',
					'author_id',
					'title',
					'bullet1',
					'bullet2',
					'bullet3',
					'bullet4',
					'bullet5',
					'description',
					'tickers',
					'primary_ticker',
					'secondary_ticker',
					'category',
					'position_dislosure',
					'position_types',
					'position_stocks',
					'position_other_info',
					'business_rel_disclosure',
					'business_rel_not_own_specify',
					'user_site_url',
					'user_site_name',
					'mini_bio',
					'visits',
					'date_created',
    				'editors_choice',
    				'status', // => new \Zend\Db\Sql\Predicate\Expression('if(article.status = \'1\', \'Actvie\', \'Deactive\')'),
            		'comments' => new \Zend\Db\Sql\Predicate\Expression('(SELECT count(comment_id) FROM comment WHERE article_id=article.article_id)')
            	));
            if($cat != '' || $sub_cat != '')
            {
            	$select->join(
			     	'category_article',
			     	'category_article.article_id = article.article_id',
            		array()
            			
  			     	//$select::JOIN_OUTER // (optional), one of inner, outer, left, right also represented by constants in the API
				)
            	->join(
			     	'category',
			     	new \Zend\Db\Sql\Predicate\Expression('category.category_id = category_article.category_id AND category.alias = ? ', array(($sub_cat == '')?$cat:$sub_cat)),
			     	//' . $sub_cat,
            		array()
  			     	//$select::JOIN_OUTER // (optional), one of inner, outer, left, right also represented by constants in the API
				);
            }
            
            $select->join(
            		'user',
            		new \Zend\Db\Sql\Predicate\Expression('user.user_id = article.author_id', array()),
            		//' . $sub_cat,
            		array('user_image' => 'image', 'username', 'display_name', 'first_name', 'last_name')
            		//$select::JOIN_OUTER // (optional), one of inner, outer, left, right also represented by constants in the API
            )->join(
    				array('ac' => new \Zend\Db\Sql\Predicate\Expression('(SELECT ca.article_id, GROUP_CONCAT(c.name) as cat_names, GROUP_CONCAT(c.alias) as cat_aliases FROM `category_article` ca LEFT JOIN `category` c ON c.category_id = ca.category_id WHERE c.name IS NOT NULL GROUP BY ca.article_id)')), // table name
    				'ac.article_id = article.article_id', // expression to join on (will be quoted by platform object before insertion),
    				array('cat_names', 'cat_aliases'), // (optional) list of columns, same requirements as columns() above
    				$select::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
    		);
            
            if($user != '')
            {
            	$select->where->addPredicate(
            			new \Zend\Db\Sql\Predicate\Expression(
            					'article.author_id = (SELECT user_id FROM user WHERE username = ? LIMIT 1)',
            					array($user))
            	);
            }

            $select->where->addPredicate(
            			new \Zend\Db\Sql\Predicate\Expression(
            					'article.type = ?',
            					array($type))
            	);
			/* $select->from(array('a' => 'article'))
            		->columns(array('a.*', 'username' => 'u.username'))
            		->join(array('u' => 'user'), 'a.author_id = u.user_id')
            		; */
            /* if($sub_cat != '')
            {
            	$select->where->and->like('category.alias', $sub_cat);
            }
            elseif($cat != '')
            {
            	$select->where->and->like('category.alias', $cat);
            } */
            
            //$select->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('category.alias IN (?, ?)', array($cat, $sub_cat)));
            
           	$select->order('date_created DESC');
    		
		    /* echo  str_replace('"', '`', $select->getSqlString());
		    exit; */
		    
		    // create a new result set based on the Album entity
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Article());
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

    public function getArticles($user_article_count = false, $type = 'article')
    {
        $resultSet = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($type) {
    		$select->where->like('type', $type);
    	});
        
        $articles = array();
        $user_articles_count = array();
        foreach($resultSet as $row)
        {
        	$articles[$row->article_id] = $row->title;
        	
        	if(isset($user_articles_count[$row->author_id]))
        	{
        		if($row->status == '1')
        		{	
        			$user_articles_count[$row->author_id]++;
        		}
        	}
        	else
        	{
        		if($row->status == '1')
	        	{
    	    		$user_articles_count[$row->author_id] = 1;
    	    	}
        		else
        		{
        			$user_articles_count[$row->author_id] = 0;
        		}
        	}
       	}
       	
       	if($user_article_count)
       	{
       		return $user_articles_count;
       	}
       	else
       	{
	        return $articles;
       	}
    }
    
    /****************************************
     * Purpose: Used to get the specific user's articles count 
     * Created By: Kedar
     * Created On: Jul 14, 2014
     * Modified By: Kedar
     * Modified On: Jul 14, 2014
     ****************************************/
    public function getArticlesCount($type = 'article', $user_id = false, $status = false)
    {
    	if(!$user_id)
    	{
    		return 0;
    	}
    	
    	$articles_count = $this->getArticles(true, $type);
    	
    	if(isset($articles_count[$user_id]))
    	{
    		return $articles_count[$user_id];
    	}
    	else
    	{
    		return 0;
    	}
    }
    
    public function getArticle($id)
    {
    	if(!is_numeric($id))
		{
			$alias = $id;
			$rowset = $this->tableGateway->select(array('alias' => $alias));
		}
		else
		{
			$id  = (int) $id;
			$rowset = $this->tableGateway->select(array('article_id' => $id));
		}
		
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        
		return $row;
    }

    public function saveArticle(Article $article)
    {
    	$chars 		= array( " ", "_", "@", "'", "&", "\/", ",", "<", ">", "(", ")", "%", '"', ';');
    	$replaceWith = array( '-', '-', '', "", "x", "x", "", "", "", "", "", "", "", "");
    	
    	$status = 0;
    	if($article->step == '2' || $article->step == '3')
    	{
    		$status = 2;
    	}
    	else
    	{
    		$status = ($article->type == 'blog')?'1':'0';
    	}
    	$alias = str_replace($chars, $replaceWith, $article->title);
    	$alias = $this->check_alias($alias, $article->article_id);
    	$data = array(
			'article_id'		=> $article->article_id,
			'alias'				=> $alias,
			'type'				=> $article->type,
			'author_id'			=> $article->author_id,
    		'last_edited_by'	=> $article->last_edited_by,
			'title'				=> $article->title,
			'bullet1'			=> $article->bullet1,
			'bullet2'			=> $article->bullet2,
			'bullet3'			=> $article->bullet3,
			'bullet4'			=> $article->bullet4,
			'bullet5'			=> $article->bullet5,
			'description'		=> $article->description,
			'tickers'			=> $article->tickers,
			'primary_ticker'	=> $article->primary_ticker,
			'secondary_ticker'	=> $article->secondary_ticker,
			'category'			=> $article->category,
			'position_dislosure'=> $article->position_dislosure,
			'position_types'	=> $article->position_types,
    		'position_stocks'	=> $article->position_stocks,
			'position_other_info'	=> $article->position_other_info,
    		'business_rel_disclosure'			=> $article->business_rel_disclosure,
    		'business_rel_not_own_specify'			=> $article->business_rel_not_own_specify,
    		'user_site_url'			=> $article->user_site_url,
    		'user_site_name'		=> $article->user_site_name,
    		'mini_bio'			=> $article->mini_bio,
    		'step'				=> $article->step,
    		'visits'			=> $article->visits,
    		'status'			=> $status,
			'date_created'		=> $article->date_created,
   			'date_edited'		=> $article->date_edited,
        );
    	array_walk($data, function(&$value) {
    		$value = addslashes($value);
    	});
    	
    	/* echo "<pre>";
    	print_r($data);
    	exit; */
        $article_id = (int)$article->article_id;
       	if ($article_id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
        	$c_article = $this->getArticle($article_id);
        	unset($data['date_created']);
        	unset($data['visits']);
        	unset($data['author_id']);
        	
        	if($c_article->status == '1')
        	{
        		unset($data['status']);
        	}
        	if ($cat = $this->getArticle($article_id)) {
            	$this->tableGateway->update($data, array('article_id' => $article_id));
            	return $article_id;
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
	/****************************************
	 * Purpose: Used for Check the alias of an article and generate a new one for the article
	 * Created By: Kedar
	 * Created On: Jul 25, 2014
	 * Modified By: Kedar
	 * Modified On: Jul 25, 2014
	 ****************************************/
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
    
		$resultSet = $this->tableGateway->select(function (\Zend\Db\Sql\Select $s) use ($id,$alias) {
			$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('alias = ?', array($alias)));
			if($id)
			{
				$s->where->notEqualTo('article_id', $id);
			}
		});
    	if(count($resultSet) > 0)
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
    
    public function deleteArticle($article_id)
    {
        $this->tableGateway->delete(array('article_id' => $article_id));
    }
    
    /****************************************
     * Purpose: Used to get the articles associted with the company  
     * Created By: Kedar
     * Created On: Aug 9, 2014
     * Modified By: Kedar
     * Modified On: Aug 9, 2014
     ****************************************/
    public function getCompanyArticles($symbol = false, $type = 'article')
    {
    	if($symbol && $symbol != '')
    	{
    		$resultSet = $this->tableGateway->select(function (\Zend\Db\Sql\Select $s) use ($symbol) {
			    // $s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('parent_id = ?', array((int)$parent_id)));
			    $s->where->like('tickers', '%'.$symbol.'%');
			    $s->where->like('status', '1');
			    //$s->limit(GENERAL_NUM_RECORDS_IN_LIST);
			    $s->order('date_created DESC');
			});
	    	
	    	return $resultSet;
    	}
    }

    /****************************************
     * Purpose: Used to update article for specific fields
    * Created By: Kedar
    * Created On: Jul 30, 2014
    * Modified By: Kedar
    * Modified On: Jul 30, 2014
    ****************************************/
    public function updateArticle($data, $article_id = false)
    {
    	if($article_id)
    	{
    		$this->tableGateway->update($data, array('article_id' => $article_id));
    	}
    }
    
    /****************************************
     * Purpose: Used to get editores choice articles 
     * Created By: Kedar
     * Created On: Aug 27, 2014
     * Modified By: Kedar
     * Modified On: Aug 27, 2014
     ****************************************/
    public function getEditorsChoiceArticles()
    {
    	$must_read_articles = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s){
    		$s->columns(array(
    				'article_id',
					'alias',
					'author_id',
					'title',
					'description',
					'date_created',
    				'editors_choice',
    		))
    		->join(
    				'user',
    				'user_id = article.author_id', 
    				array('user_image' => 'image', 'username', 'display_name', 'first_name', 'last_name'), 
    				$s::JOIN_LEFT 
    		);
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article.status = ? AND article.editors_choice = ?', array('1', '1')));
	    	$s->limit(10);
    	});
    	 
    	return $must_read_articles;
    }
    
    /****************************************
     * Purpose: Used to get all articles for search 
     * Created By: Kedar
     * Created On: Sep 18, 2014
     * Modified By: Kedar
     * Modified On: Sep 18, 2014
     ****************************************/
    public function getArticlesListSearch($keyword = '', $limit = false, $type = false, $paginated = false, $curated = false, $editors_choice = false)
    {
    	if($keyword === '')
    	{
    		return false;
    	}
    
    	$adapter = $this->tableGateway->adapter;
    	$sql = new \Zend\Db\Sql\Sql($adapter);
    	$select = $sql->select();
    	$select->from('article');
    	
    	$select->where->nest
    	->like('title', '%'.$keyword.'%')
    	->or->like('description', '%'.$keyword.'%')
    	->unnest;
    	
    	// Set Condition for the type of article. ie. blog or article
    	if($type)
    	{
    		$select->where->equalTo('type', $type);
    	}
    	if($curated)
    	{
    		$select->where->equalTo('curated_article', '1');
    	}
    	if($editors_choice)
    	{
    		$select->where->equalTo('editors_choice', '1');
    	}
    	
    	$select->where->like('status', '1');
    	$select->order('title ASC');
    	
    	if(!$paginated)
    	{
    		if($limit)
    		{
    			$select->limit($limit);
    		}

    		$selectString = $sql->getSqlStringForSqlObject($select);
    		$rowset = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    		
    		$articles = array();
    		if($rowset)
    		{
    			foreach($rowset as $row)
    			{
    				$articles[$row->alias] = $row;
    			}
    		}
    		return $articles;
    	}
    	else
    	{
	    	$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
	    	$resultSetPrototype->setArrayObjectPrototype(new Article());
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
    }
    
    /****************************************
     * Purpose: Used to get curated articles for the edotors choice section on home page 
     * Created By: Kedar
     * Created On: Oct 1, 2014
     * Modified By: Kedar
     * Modified On: Oct 1, 2014
     ****************************************/
    public function getCuratedArticles()
    {
    	$must_read_articles = $this->tableGateway->select( function (\Zend\Db\Sql\Select $s){
    		$s->columns(array(
    				'article_id',
    				'alias',
    				'author_id',
    				'title',
    				'description',
    				'date_created',
    				'curated_article',
    				'curated_source',
    		))
    		->join(
    				'user',
    				'user_id = article.author_id',
    				array('user_image' => 'image', 'username', 'display_name', 'first_name', 'last_name'),
    				$s::JOIN_LEFT
    		)->join(
    				array('ac' => new \Zend\Db\Sql\Predicate\Expression('(SELECT ca.article_id, GROUP_CONCAT(c.name) as cat_names, GROUP_CONCAT(c.alias) as cat_aliases FROM `category_article` ca LEFT JOIN `category` c ON c.category_id = ca.category_id WHERE c.name IS NOT NULL GROUP BY ca.article_id)')), // table name
    				'ac.article_id = article.article_id', // expression to join on (will be quoted by platform object before insertion),
    				array('cat_names', 'cat_aliases'), // (optional) list of columns, same requirements as columns() above
    				$s::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
    		);
    		$s->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression('article.status = ? AND article.curated_article = ?', array('1', '1')));
    		$s->limit(10);
    	});
    
    	return $must_read_articles;
    }
    
    /****************************************
     * Purpose: Used to increment article visits count 
     * Created By: Kedar
     * Created On: Oct 7, 2014
     * Modified By: Kedar
     * Modified On: Oct 7, 2014
     ****************************************/
    public function increaseArticleVisit($visits = false, $article_id = false)
    {
    	if($article_id && $visits !== false)
    	{
    		$data['visits'] = $visits+1;
    		$this->tableGateway->update($data, array('article_id' => $article_id));
    	}
    }
}
