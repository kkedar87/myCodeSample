<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class userDisplayName extends AbstractHelper
{
    public function __invoke($member, $alias = false)
    {
    	$memeber_name = '';
    	
    	if(!$alias)
    	{
    		if($member->display_name != '')
	    	{
	    		$memeber_name = $member->display_name;
	    	}
	    	elseif($member->first_name != '' && $member->last_name != '')
	    	{
		    	$memeber_name = $member->first_name.' '.$member->last_name;	
	    	}
	    	elseif($member->first_name != '')
	    	{
	    		$memeber_name = $member->first_name;
	    	}
	    	elseif($member->username != '')
	    	{
	    		$memeber_name = $member->username;
	    	}
	    	$memeber_name = ucwords($memeber_name);
    	}
    	else
    	{
    		if($member->username != '')
    		{
    			$memeber_name = $member->username;
    		}
    		elseif($member->email != '')
    		{
    			$memeber_name = $member->email;
    		}
    	}
    	
    	return $memeber_name;
    }
}