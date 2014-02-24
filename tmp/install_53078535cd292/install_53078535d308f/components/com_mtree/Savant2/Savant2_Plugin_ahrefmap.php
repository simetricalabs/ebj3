<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 1.50
* @copyright (C) 2005 Mosets Consulting
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_ahrefmap extends Savant2_Plugin {

	/**
	* 
	* Output an HTML <a href="">...</a> link that point to Google Maps
	* 
	* @param object $link Reference to link object.
	* 
	* @return string The <a href="">...</a> tag.
	* 
	*/
	
	function plugin()
	{
		global $mtconf;

		list($link, $attr, $show_arrow) = array_merge(func_get_args(), array(null, 1));

		# Load Parameters
		$params = new JRegistry( $link->attribs );
		$params->def( 'show_map', $mtconf->get('show_map') );
		$params->def( 'map', $mtconf->get('map') );
		$html = '';

		if ( $params->get( 'show_map' ) == 1 ) {

			if ( $show_arrow == 1 ) {
				// $html = '<img src="images/M_images/indent1.png" width="9" height="9" />';
			} else {
				$html = '';
			}

			$html .= '<a href="http://';
			$html .= 'maps.google.com/maps?';
			$html .= 'q=' . urlencode($link->address);
			$html .= '+' . urlencode($link->city) . '+' . urlencode($link->state) . '+' . urlencode($link->postcode);
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
			
			$html .= ' target="_blank">'.JText::_( 'COM_MTREE_MAP' )	."</a>";

			# Return the map link
			return $html;
		}

	}

}
?>