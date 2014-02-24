<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 0.8
* @copyright (C) 2004 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_ahrefprint extends Savant2_Plugin {
	
	function plugin()
	{
		global $Itemid, $mtconf;

		list($link, $attr) = array_merge(func_get_args(), array(null));

		# Load Parameters
		$params = new JRegistry( $link->attribs );
		$params->def( 'show_print', $mtconf->get('show_print') );

		if ( $params->get( 'show_print' ) == 1 ) {

			$html = '';
			$html .= '<a ';
			$html .= 'href="index.php?option=com_mtree&amp;task=print&amp;link_id='.$link->link_id.'&amp;tmpl=component&amp;Itemid='.$Itemid.'" ';
			$html .= 'onclick="javascript:void window.open(this.href, \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" title="'.JText::_( 'COM_MTREE_PRINT' ).'"';
			
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
			
			$html .= '>'.JText::_( 'COM_MTREE_PRINT' )."</a>";

			# Return the print link
			return $html;
		}

	}

}
?>