<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class userMsgCount extends AbstractHelper
{
    protected $count = 0;
    protected $msgTable;
    
    public function setMsgTable($service_locator)
    {
    	if (!$this->msgTable) {
    		$this->msgTable = $service_locator->get('Application\Model\MsgTable');
    	}
    	return $this->msgTable;
    }
    
    public function __invoke($user_id = false, $read = false)
    {
    	if($user_id)
    	{
	    	$msg_count	= $this->msgTable->getUserMsgCount($user_id, $read);
	    	if($msg_count > 0)
	    	{
	    		return $msg_count;
	    	}
    	}
    	return false;
    }
}