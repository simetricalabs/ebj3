<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTVotedbestHelper {

	public static function getList( $params, $cat_id=0 ) {
		global $mtconf;
		
		$parent_cat			= $params->get( 'parent_cat', '' );
		$only_subcats		= $params->get( 'only_subcats', 1 );
		$count				= $params->get( 'count', 5 );
		$parent_cat			= $params->get( 'parent_cat', '' );
		$only_subcats		= $params->get( 'only_subcats', 1 );
		$max_name_char		= $params->get( 'max_name_char', 24 );
		
		$db 		= JFactory::getDBO();
		$jdate 		= JFactory::getDate();
		$now 		= $jdate->toSql();
		$nullDate	= $db->getNullDate();

		# Generate SQL conditional to display category specific listing
		$only_subcats_sql = '';
		if ( $only_subcats == 1 || $parent_cat > 0 ) {

			if ( $parent_cat > 0 ) {
				$cat_id = $parent_cat;
			}

			$mtCats = new mtCats( $db );
			if ( is_numeric($cat_id) && $cat_id > 0 ) {
				$subcats = $mtCats->getSubCats_Recursive( $cat_id );
				$subcats[] = $cat_id;
			}
			if ( isset($subcats) && count($subcats) > 0 ) {
				$only_subcats_sql = "\n AND c.cat_id IN (" . implode( ", ", $subcats ) . ")";
			}

		}

		$db->setQuery( "SELECT l.*, cl.cat_id AS cat_id, c.cat_name AS category "
			. "\n FROM (#__mt_links AS l, #__mt_cats AS c, #__mt_cl AS cl)"
			. "\n WHERE l.link_id = cl.link_id AND c.cat_id = cl.cat_id AND cl.main = 1"
			. "\n AND link_published='1' && link_approved='1' "
			. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) "
			.	( ( $mtconf->get('min_votes_for_toprated') && $mtconf->get('min_votes_for_toprated') >= 1 ) ? "\n AND l.link_votes >= " . $mtconf->get('min_votes_for_toprated') . " " : '' )
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			.	"\n ORDER BY link_rating DESC, link_votes DESC  "
			.	"\n LIMIT $count" );

		$listing = $db->loadObjectList();
		
		$itemid = MTModuleHelper::getItemid();
		$i = 0;
		foreach( $listing AS $l ) {
			$listing[$i]->link = JRoute::_( 'index.php?option=com_mtree&task=viewlink&link_id=' . $l->link_id . $itemid );
			$listing[$i]->cat_link = JRoute::_( JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $l->cat_id . $itemid ) );
			
			// Round rating value
			$listing[$i]->link_rating = round( $l->link_rating, 1 );
			
			// Trim listing name
			if ( $max_name_char <= 0 || strlen($l->link_name) <= $max_name_char ) {
				$listing[$i]->trimmed_link_name = $l->link_name;
			} else {
				$link_name = substr($l->link_name, 0, $max_name_char);
				$words = explode(" ", $link_name);
				array_pop($words);
				$listing[$i]->trimmed_link_name = implode(" ", $words)."...";
			}
			$i++;
		}
		
		return $listing;
	}

	public static function getCategoryId() {
		$db = JFactory::getDBO();
		
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