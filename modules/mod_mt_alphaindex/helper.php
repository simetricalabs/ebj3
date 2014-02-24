<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTAlphaindexHelper {
	
	public static function getList($params) {
		$db = JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$display_total_links	= (int) $params->get( 'display_total_links',	0	);
		$show_number		= (int) $params->get( 'show_number',			1	);
		$show_empty		= (int) $params->get( 'show_empty',				0	);
		$limit_to_cat_id    = (int) $params->get( 'limit_to_cat_id',				0	);

		$lists = array();
		$itemid = MTModuleHelper::getItemid();
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();
		$currentalpha = self::getCurrentAlpha();

		$where_cat = array();
		$url_cat_id = '';
		if( $limit_to_cat_id > 0 )
		{
			$db->setQuery( "SELECT * FROM #__mt_cats WHERE cat_id = " . $limit_to_cat_id . " LIMIT 1" );
			$category = $db->loadObject();
			$where_cat = array("lft >= ".$category->lft, "rgt <= ".$category->rgt);
			$url_cat_id = '&cat_id='.$limit_to_cat_id;
		}

		// Count Integers
		if( $show_number )
		{
			if( $display_total_links || !$show_empty )
			{
				$where = array();

				$sql = "SELECT COUNT(*) FROM #__mt_links AS l ";
				$sql .= 'LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = 1 ';
				$sql .= 'LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id ';

				$where[] = "link_approved = '1'";
				$where[] = "link_published = '1'";
				$where[] = "( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  )";
				$where[] = "( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' )";
			
				for( $i=48; $i <= 57; $i++)
				{
					$where_int[] = "link_name LIKE '".chr($i)."%'";
				}
				$where[] = '(' . implode(' OR ', $where_int) . ')';

				$sql .= (count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '');
				$sql .= (count( $where_cat ) ? ' AND ' . implode( ' AND ', $where_cat ) : "");

				$db->setQuery( $sql );
				$lists[0] = new stdClass();
				$lists[0]->total = $db->loadResult();
			} else {
				$lists[0]->total = 0;
			}
			
			if ( $show_empty || ( !$show_empty && $lists[0]->total > 0 ) )
			{
				$lists[0]->text = '0-9';
				$lists[0]->link = JRoute::_( 'index.php?option=com_mtree&task=listalpha&alpha=0-9' . $itemid . $url_cat_id );
				$lists[0]->current = ($currentalpha === '0') ? true : false;
			} else {
				unset($lists[0]);
			}
		}
		
		// Count alphabet
		for($i=65; $i<=90; $i++) {

			if( $display_total_links || !$show_empty )
			{
				$where = array();
				
				// Get Total results - Links
				$sql = 'SELECT COUNT(*) FROM #__mt_links AS l ';
				$sql .= 'LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = 1 ';
				$sql .= 'LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id ';

				$where[] = "link_approved = '1'";
				$where[] = "link_published = '1'";
				$where[] = "( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  )";
				$where[] = "( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' )";
				$where[] = "link_name LIKE '".chr($i)."%'";
				$sql .= (count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : "");
				$sql .= (count( $where_cat ) ? ' AND ' . implode( ' AND ', $where_cat ) : "");

				$db->setQuery( $sql );
				$lists[chr($i)] = new stdClass();
				$lists[chr($i)]->total = $db->loadResult();
			} else {
				$lists[chr($i)]->total = 0;
			}
			
			if ( $show_empty || ( !$show_empty && $lists[chr($i)]->total > 0 ) )
			{
				$lists[chr($i)]->text = chr($i);
				$lists[chr($i)]->link = JRoute::_( 'index.php?option=com_mtree&task=listalpha&alpha=' . strtolower(chr($i)) . $itemid . $url_cat_id );
				$lists[chr($i)]->current = ($currentalpha == strtolower(chr($i))) ? true : false;
			} else {
				unset($lists[chr($i)]);
			}
		}
		return $lists;
	}

	public static function getCurrentAlpha()
	{
		$option = JFactory::getApplication()->input->getCmd('option');
		$task	= JFactory::getApplication()->input->getCmd('task');
		$alpha	= JFactory::getApplication()->input->getCmd('alpha');
		
		if( $option == 'com_mtree' && $task == 'listalpha' && $alpha != '' ) {
			return $alpha;
		} else {
			return false;
		}
	}
}
