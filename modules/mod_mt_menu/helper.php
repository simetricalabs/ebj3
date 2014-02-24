<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTMenuHelper {

	public static function getCatId( $link_id ) {
		$db = JFactory::getDBO();
		
		$mtLink = new mtLinks( $db );
		$mtLink->load( $link_id );
		
		return $mtLink->cat_id;		
	}

	public static function getTopListCatId( $cat_id, $params ) {
		$limit_toplist = $params->get( 'limit_toplist', 0 );
		
		if ( $limit_toplist == 0 ) {
			$toplist_cat_id = 0;
		} else {
			if ( $cat_id > 0 ) {
				$toplist_cat_id = $cat_id;
			} else {
				$cat_id = 0;
				$toplist_cat_id = 0;
			}
		}
		return $toplist_cat_id;
	}

	public static function getCatAllowSubmission( $params, $cat_id ) {
		$db = JFactory::getDBO();

		$show_addlisting_force = $params->get( 'show_addlisting_force', 0 );

		# Check if this category allow link submission
		$cat_allow_submission = 0;
		if ( $show_addlisting_force ) {
			$cat_allow_submission = 1;
		} elseif( $cat_id > 0 ) {
			$db->setQuery( "SELECT cat_allow_submission FROM #__mt_cats WHERE cat_id = $cat_id LIMIT 1" );
			$cat_allow_submission = $db->loadResult();
		} elseif( $cat_id == 0 ) {
			global $mtconf;
			$cat_allow_submission = $mtconf->get('allow_listings_submission_in_root');
		}
		return ($cat_allow_submission) ? true : false ;
	}
	
	public static function getActive() {
		$active = null;
		$task	= JFactory::getApplication()->input->getCmd('task');
		$option	= JFactory::getApplication()->input->getCmd('option');
		
		switch( $task ) {
			case 'listallcats';
			case 'listall';
			case 'addlisting':
			case 'addcategory':
			case 'mypage':
			case 'viewusersfav':
			case 'viewusersreview':
			case 'listnew':
			case 'listupdated':
			case 'listfavourite':
			case 'listfeatured':
			case 'listpopular':
			case 'listmostrated':
			case 'listtoprated':
			case 'listmostreview':
				$active = $task;
				break;
			default:
				if( $option == 'com_mtree' && $task == '' ) {
					$active = 'directory';
				}
		}
		return $active;
	}
}