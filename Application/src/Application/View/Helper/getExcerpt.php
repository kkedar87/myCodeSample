<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class getExcerpt extends AbstractHelper
{
    public function __invoke($content, $length)
    {
    	$excerpt 	= '';
    	$txt 		= stripslashes(strip_tags($content));
    	
    	if((strlen($txt) > $length))
    	{
    		$excerpt .= substr($txt, 0, ($length-3));
    		$excerpt .= '...';
    	}
    	else 
    	{
    		$excerpt .= $txt;
    	}
    	return $excerpt;
    }
}