<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corecreated extends mFieldType_date {
	var $name = 'link_created';
	var $numOfInputFields = 0;
	function parseValue($value) {
		return strip_tags($value);
	}
	function getOutput($view=1) {
		$dateFormat = $this->getParam('dateFormat','Y-m-d');
		$customDateFormat = $this->getParam('customDateFormat','');
		
		if( !empty($customDateFormat) )
		{
			$format = $customDateFormat;
		}
		else
		{
			$format = $dateFormat;
		}
		
		$value = $this->getValue();
		
		jimport('joomla.utilities.date'); 
		$tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset')); 

		$date = new JDate($value); 
		$date->setTimezone($tz); 

		$output = '<time datetime="'.$date->toSql(true).'">';
		$output .= $date->format($format);
		$output .= '</time>';
		
		return $output;
	}
}
?>