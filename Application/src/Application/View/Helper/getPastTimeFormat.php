<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class getPastTimeFormat extends AbstractHelper
{
    public function __invoke($date)
    {
   		$diff_time =  (($date)?(time()-strtotime($date)):0);
		$days = (int)($diff_time/(3600*24));
		if($diff_time <= 60)
		{
			$past_time_text = (int)$diff_time;
			$past_time_text .= ($past_time_text == 1)?" Second":" Seconds";
		}
		elseif($diff_time <= 60*60)
		{
			$past_time_text = (int)($diff_time/60);
			$past_time_text .= ($past_time_text == 1)?" Minute":" Minutes";
		}
		elseif($diff_time <= 60*60*24)
		{
			$past_time_text = (int)($diff_time/(60*60));
			$past_time_text .= ($past_time_text == 1)?" Hour":" Hours";
		}
		elseif($days <= 7)
		{
			$past_time_text = (int)$days;
			$past_time_text .= ($past_time_text == 1)?" Days":" Days";
		}
		elseif($days >= 7 && $days < 30)
		{
			$past_time_text = (int)($days/7);
			$past_time_text .= ($past_time_text == 1)?" Week":" Weeks";
		}
		elseif($days >= 30 && $days < 365)
		{
			$past_time_text = (int)($days/30);
			$past_time_text .= ($past_time_text == 1)?" Month":" Months";
		}
		elseif($days >= 365)
		{
			$past_time_text = (int)($days/365);
			$past_time_text .= ($past_time_text == 1)?" Year":" Years";
		}
		return $past_time_text;
    }
}