<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 2.0
* @copyright (C) 2007 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_showrssfeed extends Savant2_Plugin {
	
	function plugin( $type )
	{
		global $Itemid, $mtconf, $cat_id;
		$html = '';
		if($type == 'listnew' || $type == 'listupdated') {
			$type = substr($type,4);
		}
		
		if( $type == 'new' || $type == 'updated' ) {
			if( $mtconf->get('show_list' . $type . 'rss') ) {
				$html = '<a href="';
				$html .= 'index.php?option=com_mtree&task=rss&type=' . $type;
				if( isset($cat_id) && $cat_id > 0 ) {
					$html .= '&cat_id=' . $cat_id;
				}
				$html .= '&Itemid=' . $Itemid;
				$html .= '">';
				$html .= '<img src="' . $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_images') . 'rss.png" width="14" height="14" hspace="5" alt="RSS" border="0" />';
				$html .= '</a>';
			}
		}
		return $html;
	}

}
?>