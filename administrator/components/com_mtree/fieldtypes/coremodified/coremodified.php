<?php
/**
 * @version	$Id: coremodified.php 1674 2012-12-22 14:48:02Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coremodified extends mFieldType_date {
	var $name = 'link_modified';
	var $numOfInputFields = 0;
	
	function getOutput() {
		$value = $this->getValue();
		if($value == '0000-00-00 00:00:00')
		{
			return JText::_( 'COM_MTREE_NEVER' );
		}
		else
		{
			$dateFormat = $this->getParam('dateFormat','Y-m-d');
			$customDateFormat = $this->getParam('customDateFormat','');
			if( !empty($customDateFormat) )
			{
				$format = $customDateFormat;
			} else {
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
}
?>