<?php
/**
 * @version	$Id: helper.php 1936 2013-07-01 13:11:28Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/mfields.class.php' );
require_once( JPATH_SITE.'/components/com_mtree/mtree.tools.php' );

class modMTListingsHelper {

	function getCatIdFilter( $params, $cat_id=0, $link_id=0 ) {
		$db =& JFactory::getDBO();

		$show_from_cat_id	= $params->get( 'show_from_cat_id', 0 );
		$only_subcats		= $params->get( 'only_subcats', 0 );

		$mtCats = new mtCats( $db );
		$limit_cat_to = 0;
		if( $show_from_cat_id > 0 )  {

			if( $only_subcats == 1 ) {
				$mtCats->load( $show_from_cat_id );

				if( $cat_id > 0 && $mtCats->isChild($cat_id) ) {
					$limit_cat_to = $cat_id;
				} else {
					$limit_cat_to = $show_from_cat_id;
				}

			} else {
				$limit_cat_to = $show_from_cat_id;
			}

		} elseif ( $only_subcats == 1 ) {
			if( $cat_id > 0 ) {
				$limit_cat_to = $cat_id;
			} elseif ( $link_id > 0 ) {
				$link = new mtLinks( $db );
				$link->load( $link_id );
				$limit_cat_to = $link->getCatID();
			}
		}
		return $limit_cat_to;
	}
	
	function getList( $params, $limit_cat_to=0 ) {
		global $mtconf;
		
		$db =& JFactory::getDBO();
		
		$type			= $params->get( 'type', 1 ); // Default is new listing
		$count			= $params->get( 'count', 5 );
		$show_images		= $params->get( 'show_images', 0 );
		$show_category		= $params->get( 'show_category', 1 );
		$trim_long_names	= $params->get( 'trim_long_names', 1 );
		$trim_long_urls		= $params->get( 'trim_long_urls', 1 );
		$max_name_char		= $params->get( 'max_name_char', 24 );
		$show_website		= $params->get( 'show_website', 1 );
		$filterfield		= $params->get( 'filterfield', array() );
		$shuffle_listing	= $params->get( 'shuffle_listing', 1 );
		
		# Get sub_cats queries
		if( $limit_cat_to > 0 ) {
			$mtCats = new mtCats( $db );
			$subcats = $mtCats->getSubCats_Recursive( $limit_cat_to );
			$subcats[] = $limit_cat_to;
			$only_subcats_sql = '';

			if ( count($subcats) > 0 ) {
				$only_subcats_sql = "\n AND cl.cat_id IN (" . implode( ", ", $subcats ) . ")";
			}
		}

		switch( $type ) {
			case 1: // New listing
				$order = "link_created";
				$sort = "DESC";
				$ltask= "listnew";
				break;
			case 2: // Featured Listing
				$order = "link_featured";
				$sort = "ASC";
				$ltask= "listfeatured";
				break;
			case 3: // Popular Listing
				$order = "link_hits";
				$sort = "DESC";
				$ltask= "listpopular";
				break;
			case 4: // Most Rated Listing
				$order = "link_votes";
				$sort = "DESC";
				$ltask= "listmostrated";
				break;
			case 5: // Top Rated Listing
				$order = "link_rating";
				$sort = "DESC";
				$ltask= "listtoprated";
				break;
			case 6: // Most Reviewed Listing
				$order = "reviews";
				$sort = "DESC";
				$ltask= "listmostreview";
				break;
			case 7: // Recently updated listing
				$order = "link_modified";
				$sort = "DESC";
				$ltask= "listupdated";
				break;
			case 8: // Random listing
				$order = "l.link_id";
				$sort = 'ASC';
				$ltask= '';
				break;
			case 9: // Alphabetically Ordered listings
				$order = "l.link_name";
				$sort = 'ASC';
				$ltask= 'listalpha';
				break;
		}

		# Get Listing
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();
		$nullDate	= $db->getNullDate();

		// Most Reviewed Listing
		if ( $type == 6 ) {

			$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, COUNT(r.link_id) AS reviews, cat.*, u.username AS username, u.name AS owner, u.email AS owner_email"
				. ( ($show_images) ? ', img.filename AS image' : '' )
				. "\n FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
				. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
				. "\n LEFT JOIN #__users AS u ON u.id = l.user_id"
				. "\n LEFT JOIN #__mt_reviews AS r ON r.link_id=l.link_id "
				. ( ($show_images) ? "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1" : '' );
				
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON (cfv.link_id = l.link_id AND cfv.cf_id = " . $filterfield[0] . ")";
			}
			
			$sql .= "\n WHERE link_published='1' && link_approved='1'"
				. ( ($show_images) ? "\n AND img.img_id > 0" : ' ')
				. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
				. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) "
				. "\n AND l.link_id = cl.link_id "
				. "\n AND cat.cat_id = cl.cat_id "
				. "\n AND cl.main = 1 "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
			
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n AND cfv.value " . $filterfield[1] . " " . $db->Quote( $filterfield[2] );
			}
		
			$sql .= "\n GROUP BY r.link_id "
				.	"\n ORDER BY $order $sort "
				.	"\n LIMIT $count";
				
			$db->setQuery( $sql );
			$listing = $db->loadObjectList();

		// Shuffled Featured Listing
		} elseif ( $type == 2 && $shuffle_listing ) {

			$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cat.*, u.username AS username, u.name AS owner, u.email AS owner_email"
				. ( ($show_images) ? ', img.filename AS image' : '' )
				. "\n FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
				. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
				. "\n LEFT JOIN #__users AS u ON u.id = l.user_id"
				. ( ($show_images) ? "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1" : '' );
	
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON (cfv.link_id = l.link_id AND cfv.cf_id = " . $filterfield[0] . ")";
			}
	
			$sql .= "\n WHERE link_published='1' && link_approved='1' && link_featured='1'"
				. ( ($show_images) ? "\n AND img.img_id > 0" : ' ')
				. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
				. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) "
				. "\n AND l.link_id = cl.link_id "
				. "\n AND cat.cat_id = cl.cat_id "
				. "\n AND cl.main = 1 "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
			
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n AND cfv.value " . $filterfield[1] . " " . $db->Quote( $filterfield[2] );
			}
			
			$sql .= "\n ORDER BY $order $sort ";
				
			$db->setQuery( $sql );
			$listing = $db->loadObjectList();

			shuffle( $listing );
			$listing = array_slice( $listing, 0, $count );

		// Other normal listing
		} else {

			$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cat.*, u.username AS username, u.name AS owner, u.email AS owner_email"
				. ( ($show_images) ? ', img.filename AS image' : '' )
				. "\n FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
				. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
				. "\n LEFT JOIN #__users AS u ON u.id = l.user_id"
				. ( ($show_images) ? "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 AND img.img_id > 0" : '' );
				
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON (cfv.link_id = l.link_id AND cfv.cf_id = " . $filterfield[0] . ")";
			}
		
			if( $type == 8 )
			{
				$sql .= "\n JOIN (SELECT (RAND() * (SELECT MAX(link_id)	FROM #__mt_links)) AS id) AS r2";
			}
			
			$sql .= "\n WHERE link_published='1' && link_approved='1' "
				. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
				. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) "
				. "\n AND l.link_id = cl.link_id "
				. "\n AND cat.cat_id = cl.cat_id "
				. "\n AND cl.main = 1 "
				.	( ( $type == 5 && $mtconf->get('min_votes_for_toprated') >= 1 ) ? "\n AND l.link_votes >= " . $mtconf->get('min_votes_for_toprated') . " " : '' )
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
				
			if( isset($filterfield[0]) && is_numeric($filterfield[0]) && !empty($filterfield[1]) && !empty($filterfield[2]) )
			{
				$sql .= "\n AND cfv.value " . $filterfield[1] . " " . $db->Quote( $filterfield[2] );
			}
			
			if( $type == 8 )
			{
				$sql .= "\n AND l.link_id >= r2.id";
			}
			
			if( $type == 2 )
			{
				$sql .= "\n AND l.link_featured = 1";
			}
			
			if( $type == 3 ) {
				$sql .=	"ORDER BY link_hits DESC ";
			} else {
				$sql .=	"\n ORDER BY $order $sort ";
			}

			if( $type == 4 ) {
				$sql .= ', link_rating DESC ';
			} elseif( $type == 5 ) {
				$sql .= ', link_votes DESC ';
			}
			$sql .= "\n LIMIT $count";
			$db->setQuery( $sql );
			$listing = $db->loadObjectList();

		}

		$i = 0;
		$itemid = MTModuleHelper::getItemid();
		foreach( $listing AS $l ) {
			$listing[$i]->link = JRoute::_( 'index.php?option=com_mtree&task=viewlink&link_id=' . $l->link_id . $itemid );
			$listing[$i]->image_path = '';
			if( $show_images && !empty($l->image)) {
				$listing[$i]->image_path = $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_listing_small_image') . $l->image;
			}
			
			// Trim name
			if ( !$trim_long_names || JString::strlen($l->link_name) <= $max_name_char ) {
				$listing[$i]->trimmed_link_name = $l->link_name;
			} elseif( $max_name_char == 0 ) {
				$listing[$i]->trimmed_link_name = '';
			} else {
				$link_name = JString::substr($l->link_name, 0, $max_name_char);
				$words = explode(" ", $link_name);
				if( count($words) > 1 ) {
					array_pop($words);
				}
				$listing[$i]->trimmed_link_name = implode(" ", $words)."...";
			}
			
			// Trim URL
			if ( $show_website == 1 && !empty($l->website) ) {
				if ( !$trim_long_urls || strlen($l->website) <= $max_name_char ) {
					$listing[$i]->trimmed_website = str_replace("http://",'',$l->website);
				} elseif( $max_name_char == 0 ) {
					$listing[$i]->trimmed_website = '';
				} else {
					$non_http_url = str_replace("http://",'',$l->website);
					$url = substr($non_http_url, 0, $max_name_char);
					$words = explode("/", $url);
					if ( count($words) > 1 ) {
						array_pop($words);
						$listing[$i]->trimmed_website = implode("/", $words)."...";
					} else {
						$listing[$i]->trimmed_website = implode("/", $words);
						if( strlen($non_http_url) > $max_name_char ) {
							$listing[$i]->trimmed_website .= "...";
						}
					}			
				}
				// If $value has a single slash and this is at the end of the string, we can safely remove this.
				if( substr($listing[$i]->trimmed_website,-1) == '/' && substr_count($listing[$i]->trimmed_website,'/') == 1 )
				{
					$listing[$i]->trimmed_website = substr($listing[$i]->trimmed_website,0,-1);
				}
			}
			
			// Category's URL
			if ( $show_category ) {
				$listing[$i]->cat_link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=' . $l->cat_id . $itemid );
			}
			$i++;
		}
		return $listing;
		
	}
	
	function getFields( $params, $listings ) {
		$db =& JFactory::getDBO();
		
		$fields	= $params->get( 'fields', array() );
		
		if( !empty($listings) && !empty($fields) )
		{
			$mfields = array();
			foreach( $listings AS $l )
			{
				$mfields[$l->link_id] = loadFields( $l, 0 );
				
			}
			return $mfields;
		}
		return false;
	}
	
	function getModuleTask( $type ) {
		switch( $type ) {
			case 1: // New listing
				$ltask= "listnew";
				break;
			case 2: // Featured Listing
				$ltask= "listfeatured";
				break;
			case 3: // Popular Listing
				$ltask= "listpopular";
				break;
			case 4: // Most Rated Listing
				$ltask= "listmostrated";
				break;
			case 5: // Top Rated Listing
				$ltask= "listtoprated";
				break;
			case 6: // Most Reviewed Listing
				$ltask= "listmostreview";
				break;
			case 7: // Recently updated listing
				$ltask= "listupdated";
				break;
			case 9: // Alphabetically ordered listings
				$ltask= "listalpha";
				break;
			case 8: // Random listing
			default:
				$ltask= '';
				break;
		}
		return $ltask;
	}
}