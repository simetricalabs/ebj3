<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 2.0
* @copyright (C) 2004-2009 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_listalpha extends Savant2_Plugin {
	
	function plugin()
	{
		global $Itemid;

		list($alpha, $cat_id, $attr) = array_merge(func_get_args(), array(null, null));

		if ( $cat_id == null || !is_numeric($cat_id) ) {
			global $cat_id;
		}

		if ( preg_match('/[a-z0-9]{1}[0-9]*/', $alpha) ) {

			$html = '<a href="';
			$html .= JRoute::_("index.php?option=com_mtree&task=listalpha&alpha=".strtolower($alpha)."&cat_id=".$cat_id."&Itemid=".$Itemid);
			$html .= '"';
			
			# Insert attributes
			if (is_array($attr)) {
				// from array
				foreach ($attr as $key => $val) {
					$key = htmlspecialchars($key);
					$val = htmlspecialchars($val);
					$html .= " $key=\"$val\"";
				}
			} elseif (! is_null($attr)) {
				// from scalar
				$html .= " $attr";
			}

			$html .= '>';
			if ( $alpha == "0" ) {
				$html .= "0-9";
			} else {
				$html .= strtoupper($alpha);
			}
			$html .= "</a>";

			# Return the listalpha link
			return $html;
		}

	}

}
?>