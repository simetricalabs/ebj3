<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 0.8
* @copyright (C) 2004 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_mtpath extends Savant2_Plugin {
	
function plugin()	{

	require_once( JPATH_ROOT.'/administrator/components/com_mtree/admin.mtree.class.php');

	list($cat_id, $attr) = array_merge(func_get_args(), array(null));

	$mtPathWay = new mtPathWay( $cat_id );
	$cat_ids = $mtPathWay->getPathWay();
	$cat_ids[] = $cat_id;

	$cat_names = array();

	if ( empty($cat_ids[0]) ) {
		$cat_names[] = JText::_( 'COM_MTREE_ROOT' );
	}

	foreach( $cat_ids AS $cid ) {
		// Do not add 'Root' name since its done above already
		if ( $cid > 0 ) {
			$cat_names[] = $mtPathWay->getCatName($cid);
		}
	}

	$html = '<a href="';
	$html .= JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$cat_id);
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
	
	# set the listing text, close the tag
	$html .= '>' . htmlspecialchars( implode(JText::_( 'COM_MTREE_ARROW' ), $cat_names) ) . '</a> ';

	return $html;

	}
}
?>