<?php
/**
 * @version	$Id: helper.php 1713 2012-12-29 02:17:01Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTSearchHelper {

	function getCategories( $params ) {
		$db =& JFactory::getDBO();
		$tmp_mtconf = new mtConfig($db);
		
		$showCatDropdown= intval( $params->get( 'showCatDropdown', 0 ) );
		$parent_cat_id		= intval( $params->get( 'parent_cat', 0 ) );

		$tmp_mtconf->setCategory($parent_cat_id);
		
		if ( $showCatDropdown == 1 && $parent_cat_id >= 0 ) {
			$sql = 'SELECT cat_id, cat_name FROM #__mt_cats AS cat '
				. ' WHERE cat_approved=1 AND cat_published=1 AND cat_parent = ' . $parent_cat_id;
				
			if( $tmp_mtconf->get('first_cat_order1') != '' )
			{
				$sql .= ' ORDER BY ' . $tmp_mtconf->get('first_cat_order1') . ' ' . $tmp_mtconf->get('first_cat_order2');
				if( $tmp_mtconf->get('second_cat_order1') != '' )
				{
					$sql .= ', ' . $tmp_mtconf->get('second_cat_order1') . ' ' . $tmp_mtconf->get('second_cat_order2');
				}
			}
			
			$db->setQuery( $sql );
			$categories = $db->loadObjectList();
			return $categories;
		} else {
			return null;
		}
	}
}