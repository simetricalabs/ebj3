<?php
/**
 * @version	$Id: helper.php 1967 2013-07-16 05:04:58Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTStaticcatsHelper {

	function getCategories( $params ) {
		global $mtconf;
		
		$db =& JFactory::getDBO();
		$itemid = MTModuleHelper::getItemid();
		
		$parent_cat_id		= $params->get( 'parent_cat_id',	0 );
		$primary_order		= $params->get( 'primary_order',	$mtconf->get('first_cat_order1') );
		$primary_sort		= $params->get( 'primary_sort',		$mtconf->get('first_cat_order2') );
		$secondary_order	= $params->get( 'secondary_order',	$mtconf->get('second_cat_order1') );
		$secondary_sort		= $params->get( 'secondary_sort',	$mtconf->get('second_cat_order2') );
		$show_empty_cat		= $params->get( 'show_empty_cat',	$mtconf->get('display_empty_cat') );

		if ($show_empty_cat == -1) $show_empty_cat = $mtconf->get('display_empty_cat');
		if ($primary_order == -1) $primary_order = $mtconf->get('first_cat_order1');
		if ($primary_sort == -1) $primary_sort = $mtconf->get('first_cat_order2');
		if ($secondary_order == -1) $secondary_order = $mtconf->get('second_cat_order1');
		if ($secondary_sort == -1) $secondary_sort = $mtconf->get('second_cat_order2');

		$sql = "SELECT * FROM #__mt_cats WHERE cat_published=1 && cat_approved=1 && cat_parent='".$parent_cat_id."' ";

		if ( !$show_empty_cat ) { $sql .= " && ( cat_cats > 0 || cat_links > 0 ) ";	}

		if( !empty($primary_order) )
		{
			$sql .= "ORDER BY $primary_order $primary_sort";
			if( !empty($secondary_order) )
			{
				$sql .= ", $secondary_order $secondary_sort";
			}
		}

		$db->setQuery( $sql );
		$categories = $db->loadObjectList();
		
		$i = 0;
		foreach( $categories AS $cat ) {
			$categories[$i++]->link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $cat->cat_id . $itemid );
		}
		
		return $categories;
	}
	
	function getCategoryId() {
		$db =& JFactory::getDBO();
		
		# Try to retrieve current category
		$link_id	= JFactory::getApplication()->input->getInt('link_id');
		$cat_id		= JFactory::getApplication()->input->getInt('cat_id');;

		if ( $link_id > 0 && $cat_id == 0 ) {
			$db->setQuery( 'SELECT cat_id FROM #__mt_cl WHERE link_id =\''.$link_id.'\' AND main = 1' );
			$cat_id = $db->loadResult();
		}

		return $cat_id;
	}
}