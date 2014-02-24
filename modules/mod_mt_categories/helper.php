<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTCategoriesHelper {

	function getCategories( $params, $cat_id, $link_id ) {
		
		$db	= JFactory::getDBO();
		$itemid = MTModuleHelper::getItemid();
		$cats	= modMTCategoriesHelper::loadCategoriesList( $params, $cat_id );
		
		// If the current categories does not have child categories, load up all its sibling categories.
		if( empty($cats) ) {
			unset( $cats );
			$cat_parent_id	= modMTCategoriesHelper::getCategoryParentId( $cat_id );
			$cats			= modMTCategoriesHelper::loadCategoriesList( $params, $cat_parent_id );
		}
		
		$i = 0;
		foreach( $cats AS $cat ) {
			$cats[$i]->link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $cat->cat_id . $itemid );
			if( $cat_id > 0 && $cat_id == $cat->cat_id ) {
				$cats[$i]->active = true;
			} else {
				$cats[$i]->active = false;
			}
			$i++;
		}
		return $cats;
	}
	
	function getBackCategory( $params, $cat_id, $link_id, $current_category_is_empty ) {
		$db = JFactory::getDBO();
		$itemid = MTModuleHelper::getItemid();

		$back_symbol	= htmlspecialchars($params->get( 'back_symbol', '<<' ));
		$show_back		= $params->get( 'show_back', '0' );
		
		if ( !$show_back && !$current_category_is_empty ) {
			return null;
		}
		
		// If the current page is a listing details, get a back link to its parent category
		if( $link_id > 0 )
		{
			$db->setQuery( 'SELECT c.cat_id, c.cat_name FROM #__mt_cl AS cl LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id WHERE link_id = ' . $link_id . ' AND main = 1 LIMIT 1' );
			$cat_parent = $db->loadObject();
			$cat_parent->link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $cat_parent->cat_id . $itemid );
			return $cat_parent;
			
		// otherwise, get a back link to the parent of the current category.
		} 
		elseif ( $cat_id > 0 ) 
		{
			$cat_parent_id = modMTCategoriesHelper::getCategoryParentId( $cat_id );
			$db->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats WHERE cat_id = ' . $cat_parent_id . ' LIMIT 1' );
			$cat_parent = $db->loadObject();
			$cat_parent->link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $cat_parent_id . $itemid );
			return $cat_parent;
		} 
		else 
		{
			return null;
		}
	}

	function getLinkId() {
		return JFactory::getApplication()->input->getInt('link_id');
	}
	
	function getCategoryId( $link_id, $cat_id ) {
		$db = JFactory::getDBO();
		
		if ( $link_id > 0 && $cat_id == 0 ) {
			$db->setQuery( 'SELECT cat_id FROM #__mt_cl WHERE link_id =\''.$link_id.'\' AND main = 1' );
			$cat_id = $db->loadResult();
		}

		return $cat_id;
	}
	
	function getCategoryParentId( $cat_id ) {
		$db = JFactory::getDBO();
		$db->setQuery( 'SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $cat_id . ' LIMIT 1' );
		$cat_parent_id = $db->loadResult();
		return $cat_parent_id;
	}

	function loadCategoriesList( $params, $cat_id ) {
		global $mtconf;

		$db = JFactory::getDBO();
		
		$primary_order		= $params->get( 'primary_order', $mtconf->get('first_cat_order1') );
		$primary_sort		= $params->get( 'primary_sort', $mtconf->get('first_cat_order2') );
		$secondary_order	= $params->get( 'secondary_order', $mtconf->get('second_cat_order1') );
		$secondary_sort		= $params->get( 'secondary_sort', $mtconf->get('second_cat_order2') );
		$show_empty_cat		= $params->get( 'show_empty_cat', $mtconf->get('display_empty_cat') );

		if ($show_empty_cat == -1) $show_empty_cat = $mtconf->get('display_empty_cat');
		if ($primary_order == -1) $primary_order = $mtconf->get('first_cat_order1');
		if ($primary_sort == -1) $primary_sort = $mtconf->get('first_cat_order2');
		if ($secondary_order == -1) $secondary_order = $mtconf->get('second_cat_order1');
		if ($secondary_sort == -1) $secondary_sort = $mtconf->get('second_cat_order2');
		
		$sql = "SELECT * FROM #__mt_cats WHERE cat_published=1 && cat_approved=1 && cat_parent='".$cat_id."' ";

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
		$cats = $db->loadObjectList();
		return $cats;
	}
}
?>