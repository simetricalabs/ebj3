<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 1.5
* @copyright (C) 2006 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_smartdatetime extends Savant2_Plugin {

	function plugin() {
		list($datetime) = func_get_args();

		$database	= JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		if ( $datetime == $database->getNullDate() ) {
			return JText::_( 'COM_MTREE_NEVER' );
		}

		$time_now = time();
		$time_str = strtotime( $datetime );

		$day_str = mktime( 0, 0, 0, date("m",$time_str),date("d",$time_str),date("Y",$time_str));
		$day_now = mktime( 0, 0, 0, date("m",$time_now),date("d",$time_now),date("Y",$time_now));
		
		# Today's date
		if ($day_now == $day_str) {
			$minutes = ceil(($time_now - $time_str)/60);
			if( $minutes < 60 ) {
				if($minutes == 1) {
					return JText::sprintf( 'Minute ago', $minutes ) ;
				} else {
					return JText::sprintf( 'Minutes ago', $minutes );
				}
			} else {
				$hours = ceil(($time_now - $time_str)/3600);
				if($hours == 1) {
					return  JText::sprintf( 'Hour ago', $hours );
				} else {
					return  JText::sprintf( 'Hours ago', $hours );
				}
			}
		} else {
			$days = ceil(($time_now - $time_str)/86400);
			if($days == 1) {
				return  JText::sprintf( 'Day ago', $days );
			} else {
				return  JText::sprintf( 'Days ago', $days );
			}
		}

	}

}
?>