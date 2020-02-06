<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class pageLinks extends AbstractHelper
{
    protected $count = 0;
    protected $pageTable;
    
    public function setPageTable($service_locator)
    {
    	if (!$this->pageTable) {
    		$this->pageTable = $service_locator->get('Application\Model\PageTable');
    	}
    	return $this->pageTable;
    }
    
    public function __invoke()
    {
    	$limit 	= isset($params['limit'])	?	$params['limit']	:	0;
    	$start 	= isset($params['start'])	?	$params['start']	:	0;
    	$sort 	= isset($params['sort'])	?	$params['sort']		:	'date';
    	$order 	= isset($params['order'])	?	$params['order']	:	'desc';
    	$total 	= isset($params['total'])	?	$params['total']	:	false;
    	$status = isset($params['status'])	?	$params['status']	:	false;
    	 
    	$params = array('sort' => 'sortorder', 'order' => 'asc', 'status' => true);
    	$pages	= $this->pageTable->getAll($params);
    	
    	return $this->getView()->render('application/helper/pagelinks', array('pages' => $pages));
    }
}