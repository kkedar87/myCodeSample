<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Common extends AbstractHelper
{
    protected $count = 0;

    public function __invoke()
    {
        return "hello";
    	$this->count++;
        $output  = sprintf("I have seen 'The Jerk' %d time(s).", $this->count);
        $escaper = $this->getView()->plugin('escapehtml');
        return $escaper($output);
    }
}