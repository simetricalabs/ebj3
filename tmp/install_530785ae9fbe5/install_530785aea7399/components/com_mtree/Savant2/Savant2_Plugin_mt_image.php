<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 2.0
* @copyright (C) 2007-2009 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_mt_image extends Savant2_Plugin {

	function plugin()	{
		global $mtconf;

		list($filename, $size, $alt, $attr) = array_merge(func_get_args(), array(null, null, null));

		switch($size) {
			case 3:
			case 's':
				$path = $mtconf->get('relative_path_to_listing_small_image');
				break;
			case 2:
			case 'm':
				$path = $mtconf->get('relative_path_to_listing_medium_image');
				break;
			case 1:
			case 'o':
				$path = $mtconf->get('relative_path_to_listing_original_image');
				break;
		}
		$html = '<img border="0" src="' . $mtconf->getjconf('live_site').$path.$filename . '"';

		if (substr(PHP_OS, 0, 3) != 'WIN' ) {
			$listingimage_info = @getimagesize($mtconf->getjconf('absolute_path').$path.$filename);
			if($listingimage_info !== false && !empty($listingimage_info[0]) && $listingimage_info[0] > 0 && !empty($listingimage_info[1]) && $listingimage_info[1] >0) {
				$html .= ' width="'.$listingimage_info[0].'" height="'.$listingimage_info[1].'"';
			}
		}
		
		if(!is_null($alt)) {
			$html .= " alt=\"" . $alt . "\"";
		} else {
			$html .= " alt=\"" . $filename . "\"";
		}
		
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
		$html .= ' />';
		
		return $html;
	}
}
?>