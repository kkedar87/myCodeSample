<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class getUserAllowedStatus extends AbstractHelper
{
    public function __invoke()
    {
    	$controller = $this->params()->fromRoute('controller', '');
		$action = $this->params()->fromRoute('action', '');
		if ($this->zfcUserAuthentication()->hasIdentity()) {
			$this->user_role	= $this->zfcUserAuthentication()->getIdentity()->getRole();
			$this->user_id		= $this->zfcUserAuthentication()->getIdentity()->getId();
		}
		if(!$this->roles[$this->user_role][$controller][$action])
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
    }
}