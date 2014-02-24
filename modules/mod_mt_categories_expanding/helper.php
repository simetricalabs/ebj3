<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTCategoriesExpandingHelper {

	function getCategoryId($cat_id, $link_id) {
		$db = JFactory::getDBO();
		
		if ( $link_id > 0 && $cat_id == 0 ) {
			$db->setQuery( 'SELECT cat_id FROM #__mt_cl WHERE link_id =\''.$link_id.'\' AND main = 1' );
			$cat_id = $db->loadResult();
		}

		return $cat_id;
	}
	
	function getCategories( $params, $cat_id ) {
		global $mtconf;

		$primary_order				= $params->get( 'primary_order',			$mtconf->get('first_cat_order1') );
		$primary_sort				= $params->get( 'primary_sort',				$mtconf->get('first_cat_order2') );
		$secondary_order			= $params->get( 'secondary_order',			$mtconf->get('second_cat_order1') );
		$secondary_sort				= $params->get( 'secondary_sort',			$mtconf->get('second_cat_order2') );
		$expand_level_1_categories	= $params->get( 'expand_level_1_categories',0 );
		$show_empty_cat				= $params->get( 'show_empty_cat',			-1 );
		
		if( $show_empty_cat == -1 )
		{
			$show_empty_cat = $mtconf->get('display_empty_cat');
		}

		if ($primary_order == -1) $primary_order		= $mtconf->get('first_cat_order1');
		if ($primary_sort == -1) $primary_sort			= $mtconf->get('first_cat_order2');
		if ($secondary_order == -1) $secondary_order	= $mtconf->get('second_cat_order1');
		if ($secondary_sort == -1) $secondary_sort		= $mtconf->get('second_cat_order2');

		$db = JFactory::getDBO();
		
		# Get the main categories first
		$sql = "SELECT cat_name, cat_id, cat_cats, cat_links FROM #__mt_cats WHERE cat_published=1 && cat_approved=1 && cat_parent='0' ";

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
		$cats[0] = $db->loadObjectList();

		$pathway_cats = array();
		if( $expand_level_1_categories ) {
			foreach($cats[0] AS $cat) {
				$pathway_cats[] = $cat->cat_id;
			}
		}

		if( !in_array($cat_id,$pathway_cats) ) {
			$pathway = new mtPathWay( $cat_id );
			$pathway_cats = array_merge($pathway->getPathWayWithCurrentCat(),$pathway_cats);
		}

		foreach( $pathway_cats AS $pathway_cat ) {

			$sql = "SELECT cat_name, cat_id, cat_cats, cat_links FROM #__mt_cats WHERE cat_published=1 && cat_approved=1 && cat_parent='".$pathway_cat."' ";
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
			$cats[$pathway_cat] = $db->loadObjectList();
		}
		return $cats;
	}
}