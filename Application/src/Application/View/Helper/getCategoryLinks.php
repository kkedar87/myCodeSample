<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class getCategoryLinks extends AbstractHelper
{
    public function __invoke($article = false)
    {
    	if($article)
    	{
    		$category_names 		= explode(',', $article->cat_names);
    		$category_aliases 		= explode(',', $article->cat_aliases);
    		$category_links = '';
    		for($i = 0; $i < count($category_names); $i++)
    		{
    			$category_links .= '<a href="' . HOME_URL . 'articles/all/' . $category_aliases[$i] . '" target="_newTab">' . $category_names[$i] . '</a>, ';
			}
			$category_links = trim($category_links, ', ');
			
    		return $category_links;
    	}
		return false;
    }
}