<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(  dirname(__FILE__).'/init.php' );

jimport( 'joomla.filter.filteroutput' );

global $mtconf, $sef_replace, $listing_tasks, $listlisting_names;
$sef_replace = array(
	'%26' => '&', // &
	'%3F' => '-3F', // ?
	'%2F' => '-2F', // /
	'%3C' => '-3C', // <
	'%3E' => '-3E', // >
	'%23' => '-23', // #
	'%24' => '-24', // $
	'%3A' => '-3A',  // :
	'%2E' => '-2E'  // .
	);

$listing_tasks = array(
	// task			=>	SEF String
	'viewgallery'		=>	$mtconf->get('sef_gallery'),
	'writereview'		=>	$mtconf->get('sef_review'),
	'recommend'		=>	$mtconf->get('sef_recommend'),
	'print'			=>	$mtconf->get('sef_print'),
	'contact'		=>	$mtconf->get('sef_contact'),
	'report'		=>	$mtconf->get('sef_report'),
	'claim'			=>	$mtconf->get('sef_claim'),
	'visit'			=>	$mtconf->get('sef_visit'),
	'deletelisting'		=>	$mtconf->get('sef_delete'),
	'editlisting'		=>	$mtconf->get('sef_editlisting'),
	'viewreviews'		=>	$mtconf->get('sef_viewreviews')
	);
	
$listlisting_names = array(
	$mtconf->get('sef_featured')	=> 'featured',
	$mtconf->get('sef_updated')	=> 'updated',
	$mtconf->get('sef_favourite')	=> 'favourite',
	$mtconf->get('sef_popular')	=> 'popular',
	$mtconf->get('sef_mostrated')	=> 'mostrated',
	$mtconf->get('sef_toprated')	=> 'toprated',
	$mtconf->get('sef_mostreview')	=> 'mostreview',
	$mtconf->get('sef_new')		=> 'new',
	$mtconf->get('sef_all')		=> 'all'
	);

function getMtreeMenuItems()
{
	$menus		= JFactory::getApplication('site')->getMenu();
	$component	= JComponentHelper::getComponent('com_mtree');
	$menu_items	= $menus->getItems('component_id', $component->id);
	
	return $menu_items;
}

function MtreeBuildRoute(&$query) {
	global $mtconf, $listing_tasks;
	$segments = array();
	$db = JFactory::getDBO();
	if(!class_exists('mtLinks')) {
		require_once( $mtconf->getjconf('absolute_path').'/administrator/components/com_mtree/admin.mtree.class.php');
	}

	if(!isset($query['task']) && !isset($query['view'])) {
		return $segments;
	} elseif(!isset($query['task']) && isset($query['view'])) {
		$task = $query['view'];
	} else {
		$task = $query['task'];
	}

	switch($task) {
			
		case 'listcats':
			if(isset($query['cat_id'])) {
				$segments = appendCat($query['cat_id'], $query);
				unset($query['cat_id']);
				if( isset($query['start']) ) {
					$page = getPage($query['start'],$mtconf->get('fe_num_of_links'));
					$segments[] = $mtconf->get('sef_category_page') . $page;
				}
			}
			break;

		case 'listallcats':
			if(isset($query['cat_id'])) {
				$segments = appendCat($query['cat_id'], $query);
				unset($query['cat_id']);
			}
			$segments[] = $mtconf->get('sef_listallcats');
			break;

		case 'viewlink':
			$mtLink = new mtLinks( $db );
			$mtLink->load( $query['link_id'] );
			$segments = array_merge($segments,appendCat( $mtLink->cat_id, $query ));
			
			if( isset($query['start']) ) {
				// http://example.com/c/mtree/Computer/Games/Donkey_Kong/page23
				$page = getPage($query['start'],$mtconf->get('fe_num_of_associated'));
				$segments = array_merge($segments,appendListing( $mtLink->link_name, $mtLink->link_id, $mtLink->alias, false ));
				$segments[] =  $mtconf->get('sef_associated_listing_page') . $page;
			} else {
				$segments = array_merge($segments,appendListing( $mtLink->link_name, $mtLink->link_id, $mtLink->alias, false ));
			}
			unset($query['limitstart']);
			unset($query['link_id']);
			break;
		
		case 'viewreviews':
			$mtLink = new mtLinks( $db );
			$mtLink->load( $query['link_id'] );
			$segments = array_merge($segments,appendCat( $mtLink->cat_id, $query ));
			$segments = array_merge($segments,appendListing( $mtLink->link_name, $mtLink->link_id, $mtLink->alias, false ));

			if( isset($query['start']) ) {
				$page = getPage($query['start'],$mtconf->get('fe_num_of_reviews'));
				$segments[] =  $mtconf->get('sef_reviews_page') . $page;
			} else {
				$segments[] =  $mtconf->get('sef_reviews_page');
			}
			unset($query['limitstart']);
			unset($query['link_id']);
			break;

		case 'mypage':
			$segments[] = $mtconf->get('sef_mypage');
			if( isset($query['start']) ) {
				$page = getPage($query['start'],$mtconf->get('fe_num_of_links'));
				$segments[] = $mtconf->get('sef_category_page') . $page;
			}
			break;
		
		case 'listfeatured':
		case 'listnew':
		case 'listupdated':
		case 'listfavourite':
		case 'listpopular':
		case 'listmostrated':
		case 'listtoprated':
		case 'listmostreview':
		case 'listall':
			$type = strtoupper(substr($task,4));
			$cat_id = getId( 'cat', $query );
			$segments = appendCat( $cat_id, $query );
			$segments[] = $mtconf->get('sef_'.strtolower($type));
			if( isset($query['start']) ) {
				$page = getPage($query['start'],$mtconf->get('fe_num_of_'.strtolower($type)));
				$segments[] = $mtconf->get('sef_category_page') . $page;
			}
			break;

		case 'advsearch':
			$segments[] = $mtconf->get('sef_advsearch');
			break;
		
		case 'advsearch2':
			$segments[] = $mtconf->get('sef_advsearch2');
			$search_id = getId( 'search', $query );
			$page = 1;
			if( isset($query['start']) ) {
				$page = getPage($query['start'],$mtconf->get('fe_num_of_all'));
				$segments[] = $search_id;
				$segments[] = $page;
			} else {
				$segments[] = $search_id;
			}
			break;
		
		case 'listalpha':
			$cat_id = getId( 'cat', $query );
			$segments = appendCat( $cat_id, $query );
			$segments[] = $mtconf->get('sef_listalpha');
			if( isset($query['alpha']) )
			{
				$segments[] = urlencode($query['alpha']);
				unset($query['alpha']);
			}
			if( isset($query['start']) )
			{
				$page = getPage($query['start'],$mtconf->get('fe_num_of_links'));
				$segments[] = $page;
			}
			break;

		case 'viewowner';
		case 'viewuserslisting';
		case 'viewusersreview';
		case 'viewusersfav';
			$user_id = getId( 'user', $query );
			$username = mtFactory::getUsername($user_id);
			if(!empty($username)) {
				switch($task) {
					default:
						$segments[] = $mtconf->get('sef_owner');
						break;
					case 'viewuserslisting':
						$segments[] = $mtconf->get('sef_listings');
						break;
					case 'viewusersreview':
						$segments[] = $mtconf->get('sef_reviews');
						break;
					case 'viewusersfav':
						$segments[] = $mtconf->get('sef_favourites');
						break;
				}
				$segments[] = murlencode($username);
			}
			if( isset($query['start']) ) {
				$page = getPage($query['start'],$mtconf->get('fe_num_of_links'));
				$segments[] = $page;
			}
			break;
		
		case 'viewimage':
			$segments[] = $mtconf->get('sef_image');
			$segments[] = getId( 'img', $query );
			break;

		case 'viewreview':
			$segments[] = $mtconf->get('sef_viewreview');
			$segments[] = getId( 'rev', $query );
			break;

		case 'replyreview':
			$segments[] = $mtconf->get('sef_replyreview');
			$segments[] = getId( 'rev', $query );
			break;

		case 'reportreview':
			$segments[] = $mtconf->get('sef_reportreview');
			$segments[] = getId( 'rev', $query );
			break;
		
		// Listing's tasks
		case array_key_exists($task,$listing_tasks) !== false:
			$mtLink = new mtLinks( $db );
			$mtLink->load( $query['link_id'] );
			$segments = appendCatListing( $mtLink, $query, false );
			$segments[] = $listing_tasks[$task];
			unset($query['link_id']);
			break;
		
		case 'addlisting':
		case 'addcategory':
			if(isset($query['link_id'])) {
				$mtLink = new mtLinks( $db );
				$mtLink->load( getId( 'link', $query ) );
				$segments = appendCat( $mtLink->cat_id, $query );
			} elseif(isset($query['cat_id'])) {
				$segments = appendCat( getId( 'cat', $query ), $query );
			}
			if($task == 'addlisting') {
				$segments[] = $mtconf->get('sef_addlisting');
			} else {
				$segments[] = $mtconf->get('sef_addcategory');
			}
			break;

		case 'searchby':
			$cf_id = getId( 'cf', $query );
			$cat_id = getId( 'cat', $query );
			$segments = appendCat( $cat_id, $query );
			$segments[] = $mtconf->get('sef_searchby');
			$segments[] = appendTag($cf_id);
			if( isset($query['start']) ) {
				if( isset($cf_id) && $cf_id > 0 && !empty($query['value']) ) {
					$page = getPage($query['start'],$mtconf->get('fe_num_of_all'));
				} elseif( isset($cf_id) && $cf_id > 0) {
					$page = getPage($query['start'],$mtconf->get('fe_num_of_searchbytags'));
				} else {
					$page = getPage($query['start'],$mtconf->get('fe_num_of_searchby'));
				}
				$segments[] = $page;
			}
			break;
			
		case 'search':
			$cat_id = getId( 'cat', $query );
			$segments = appendCat( $cat_id, $query );
			$segments[] = $mtconf->get('sef_search');
			
			$badchars = array('#','>','<','\\');
			$searchword = urldecode(trim(str_replace($badchars, '', $query['searchword'])));

			// limit searchword to x characters as configured in limit_max_chars
			if ( JString::strlen( $searchword ) > $mtconf->get('limit_max_chars') ) {
				$searchword	= JString::substr( $searchword, 0, ($mtconf->get('limit_max_chars')-1) );
			}

			if( 
				strpos($searchword,'?') !== false 
				OR
				strpos($searchword,'%') !== false
				OR
				strpos($searchword,'/') !== false
			) {
				$searchword = urlencode($searchword);
			}

			$query['searchword'] = urlencode(($searchword));
			break;
		
		case 'rss':
			$cat_id = getId( 'cat', $query );
			$segments = appendCat( $cat_id, $query );
			$segments[] = $mtconf->get('sef_rss');
			if( isset($query['type']) && $query['type'] == 'new') {
				$segments[] = $mtconf->get('sef_rss_new');
			} else {
				$segments[] = $mtconf->get('sef_rss_updated');
			}
			unset($query['type']);
			break;
	}

	if( $task != 'search' ) {
		unset($query['start']);
	}
	unset($query['limit']);
	unset($query['task']);
	return $segments;
}

function MtreeParseRoute($segments)
{
	global $mtconf, $listing_tasks, $listlisting_names;
	$vars = array();
	$db = JFactory::getDBO();
	$input = JFactory::getApplication()->input;

	$total_segments = count($segments);

	// This reverse Joomla's behaviour that always substitute the first occurance of dash with colon.
	// The reversal is done to all URL unless this is a simple search URL.
	if( $total_segments >= 2 && $segments[$total_segments-2] == $mtconf->get('sef_search') ) {
		$end_segment = end($segments);
	} else {
		$end_segment = preg_replace('/:/', '-', end($segments), 1);
	}

	for($i=0;$i<$total_segments;$i++) {
		$segments[$i] = preg_replace('/:/', '-', $segments[$i], 1);
	}

	switch($end_segment) {
			
		case $mtconf->get('sef_details'):
			// http://example.com/directory/arts/leonardo-da-vinci/details
			$path_names = array_slice( $segments, 0, -1 );
			$link_id = findLinkID( $path_names );
			$vars['task'] = 'viewlink';
			$vars['link_id'] = $link_id;
			break;

		case preg_match( '/' . $mtconf->get('sef_reviews_page') . "[0-9]+/",$end_segment) == true:
			// http://example.com/directory/arts/leonardo-da-vinci/reviews[0-9]+
			$path_names = array_slice( $segments, 0, -1 );
			$link_id = findLinkID( $path_names );
			$vars['task'] = 'viewreviews';
			$vars['link_id'] = $link_id;
		
			$pagenumber = substr( $end_segment, strlen($mtconf->get('sef_reviews_page')) );
			// $vars['limit'] = $mtconf->get('fe_num_of_reviews');
			$vars['limitstart'] = $mtconf->get('fe_num_of_reviews') * ($pagenumber -1);
		
			break;
			
		case $mtconf->get('sef_mypage'):
		case $total_segments > 1 && $mtconf->get('sef_mypage') == $segments[$total_segments-2]:
			$vars['task'] = 'mypage';
			$pagenumber = getPageNumber($segments);
			if ( $pagenumber > 0 ) {
				$vars['limit'] = $mtconf->get('fe_num_of_links');
				$vars['limitstart'] = ($mtconf->get('fe_num_of_links') * ($pagenumber -1));
			}
			break;
			
		// List listing page
		case $total_segments == 1 && array_key_exists($end_segment,$listlisting_names):

		case isset($segments[$total_segments-2]) 
			&& 
			(array_key_exists($segments[$total_segments-2],$listlisting_names) || array_key_exists($segments[$total_segments-1],$listlisting_names)) 
			&& 
			$segments[$total_segments-2] != $mtconf->get('sef_rss'):

			$last_segment = $end_segment;
			if( array_key_exists($last_segment,$listlisting_names) ) {
				$type = $listlisting_names[$last_segment];
				$offset = -1;
			} else {
				$type = $listlisting_names[$segments[$total_segments-2]];
				$offset = -2;
			}
			$vars['task'] = 'list'.$type;
			$page = getPageNumber($segments);
			$cat_id = findCatId(array_slice($segments,0,$offset));
			$vars['cat_id'] = $cat_id;
			if($page > 0) {
				$vars['limit'] = $mtconf->get('fe_num_of_'.$type);
				$vars['limitstart'] = $mtconf->get('fe_num_of_'.$type) * ($page -1);
			}
			break;

		case $mtconf->get('sef_advsearch'):
			$vars['task'] = 'advsearch';
			$vars['cat_id'] = findCatId(array_slice($segments,0,-1));
			break;
		
		case $total_segments == 3 && $mtconf->get('sef_advsearch2') == $segments[$total_segments-3]:
		case $total_segments == 2 && $mtconf->get('sef_advsearch2') == $segments[$total_segments-2]:
			if( $total_segments == 2 ) {
				$page = 1;
				$vars['limitstart'] = 0;
				$search_id = $end_segment;
			} else {
				$page = $end_segment;
				$vars['limitstart'] = ($mtconf->get('fe_num_of_all') * ($page -1));
				$search_id = $segments[$total_segments-2];
			}
			$vars['task'] = 'advsearch2';
			$vars['search_id'] = $search_id;
			$vars['limit'] = $mtconf->get('fe_num_of_all');
			break;
		
		case ($total_segments > 2 && $mtconf->get('sef_searchby') == $segments[$total_segments-3]):
		case ($total_segments > 1 && $mtconf->get('sef_searchby') == $segments[$total_segments-2]):
		case $mtconf->get('sef_searchby'):
			$vars['task'] = 'searchby';

			if( $total_segments == 1 ) {
				$vars['cf_id'] = 0;
			} elseif( $mtconf->get('sef_searchby') == $segments[$total_segments-2] ) {
				$vars['cf_id'] = findCfId($segments[$total_segments-1]);
			} else {
				$vars['cf_id'] = findCfId($segments[$total_segments-2]);
			}

			// Searchby Tag and Searchby Tag Listing 
			if( $vars['cf_id'] > 0 ) {
				if( $mtconf->get('sef_searchby') == $segments[$total_segments-2] ) {
					$page = 1;
					$vars['cf_id'] = findCfId($segments[$total_segments-1]);
					$vars['value'] = $input->getString('value');
					$vars['cat_id'] = findCatId(array_slice($segments,0,-2));
				} else {
					$page = $end_segment;
					$vars['cf_id'] = findCfId($segments[$total_segments-2]);
					$vars['value'] = $input->getString('value');
					$vars['cat_id'] = findCatId(array_slice($segments,0,-3));
				}
				
				$value = $input->getString('value');
				if( !empty($value) ) {
					// Searchby Tag Listing
					$vars['limit'] = $mtconf->get('fe_num_of_all');
					$vars['limitstart'] = ($mtconf->get('fe_num_of_all') * ($page -1));
				} else {
					// Searchby Tag 
					$vars['limit'] = $mtconf->get('fe_num_of_searchbytags');
					$vars['limitstart'] = ($mtconf->get('fe_num_of_searchbytags') * ($page -1));
				}
				
			// Searchby
			} else {
				if( $end_segment == $mtconf->get('sef_searchby') ) {
					$page = 1;
					$vars['cat_id'] = findCatId(array_slice($segments,0,-1));
				} else {
					$page = $end_segment;
					$vars['cat_id'] = findCatId(array_slice($segments,0,-2));
				}
				$vars['limit'] = $mtconf->get('fe_num_of_searchby');
				$vars['limitstart'] = ($mtconf->get('fe_num_of_searchby') * ($page -1));
			
			}
			break;

		// eg: http://example.com/directory/search.html?searchword=xxx
		case $mtconf->get('sef_search') == $segments[$total_segments-1]:
			$vars['searchword'] = $input->getString('searchword');
			$vars['task'] = 'search';
			$vars['limit'] = $mtconf->get('fe_num_of_all');
			$vars['limitstart'] = 0;
			$vars['cat_id'] = findCatId(array_slice($segments,0,-1));
			break;
		
		case $mtconf->get('sef_listalpha'):
			$vars['task'] = 'listalpha';
			$vars['cat_id'] = findCatId(array_slice($segments,0,-1));
			break;

		case $total_segments > 1 && $mtconf->get('sef_listalpha') == $segments[$total_segments-2]:
		case $total_segments > 2 && $mtconf->get('sef_listalpha') == $segments[$total_segments-3]:
			if( $mtconf->get('sef_listalpha') == $segments[$total_segments-2] ) {
				$vars['cat_id'] = findCatId(array_slice($segments,0,-2));
				$alpha = end($segments);
				
				if( !is_numeric($alpha) || intval($alpha) == 0 )
				{
					$vars['alpha'] = end($segments);
					$page = 1;
				}
				else{
					unset($vars['alpha']);
					$page = $segments[$total_segments-1];
				}
			} else {
				$vars['cat_id'] = findCatId(array_slice($segments,0,-3));
				$alpha = $segments[$total_segments-2];

				if( !is_numeric($alpha) || intval($alpha) == 0 )
				{
					$vars['alpha'] = $segments[$total_segments-2];
				}
				else
				{
					unset($vars['alpha']);
				}
				$page = $segments[$total_segments-1];
			}
			$vars['task'] = 'listalpha';
			if($page > 0) {
				$vars['limit'] = $mtconf->get('fe_num_of_links');
				$vars['limitstart'] = $mtconf->get('fe_num_of_links') * ($page -1);
			}
			break;

		case $total_segments == 3 && in_array($segments[$total_segments-3],array($mtconf->get('sef_owner'),$mtconf->get('sef_listings'),$mtconf->get('sef_reviews'),$mtconf->get('sef_favourites'))) == true:
		case $total_segments == 2 && in_array($segments[$total_segments-2],array($mtconf->get('sef_owner'),$mtconf->get('sef_listings'),$mtconf->get('sef_reviews'),$mtconf->get('sef_favourites'))) == true:
			if( $total_segments == 2 ) {
				$task = $segments[$total_segments-2];
				$owner_username = $segments[ ($total_segments-1) ];
			} else {
				$task = $segments[$total_segments-3];
				$owner_username = $segments[ ($total_segments-2) ];
			}
			switch($task) {
				case $mtconf->get('sef_owner'):
					$vars['task'] = $mtconf->get('owner_default_page');
					break;
				case $mtconf->get('sef_listings'):
					$vars['task'] = 'viewuserslisting';
					break;
				case $mtconf->get('sef_reviews'):
					$vars['task'] = 'viewusersreview';
					break;
				case $mtconf->get('sef_favourites'):
					$vars['task'] = 'viewusersfav';
					break;
			}
			$owner_username = murldecode($owner_username);
			
			$db->setQuery( "SELECT id FROM #__users WHERE username = " . $db->quote($owner_username) . " LIMIT 1" );
			$vars['user_id'] = $db->loadResult();
			$page = $segments[$total_segments-1];
			if( !is_numeric($page) ) $page = 1;
			if($page > 0) {
				$vars['limit'] = $mtconf->get('fe_num_of_links');
				$vars['limitstart'] = $mtconf->get('fe_num_of_links') * ($page -1);
			}
			break;
		
		case $total_segments == 2 && $mtconf->get('sef_editlisting') == $segments[$total_segments-2] && is_numeric($segments[$total_segments-1]):
			$vars['task'] = 'editlisting';
			$vars['link_id'] = $end_segment;
			break;
		
		case $total_segments == 2 && $mtconf->get('sef_image') == $segments[$total_segments-2] && is_numeric($segments[$total_segments-1]):
			$vars['task'] = 'viewimage';
			$vars['img_id'] = $end_segment;
			break;

		case $total_segments == 2 && $mtconf->get('sef_viewreview') == $segments[$total_segments-2]:
			$vars['task'] = 'viewreview';
			$vars['rev_id'] = $end_segment;
			break;
			
		case $total_segments == 2 && $mtconf->get('sef_replyreview') == $segments[$total_segments-2]:
			$vars['task'] = 'replyreview';
			$vars['rev_id'] = $end_segment;
			break;

		case $total_segments == 2 && $mtconf->get('sef_reportreview') == $segments[$total_segments-2]:
			$vars['task'] = 'reportreview';
			$vars['rev_id'] = $end_segment;
			break;
		
		// Listing's task - http://example.com/directory/Business/Mosets/listing_task
		case in_array($end_segment,$listing_tasks):
			$path_names = array_slice( $segments, 0, -1 );
			$link_id = findLinkID( $path_names );
			$vars['task'] = array_search($end_segment,$listing_tasks);
			$vars['link_id'] = $link_id;

			break;
		
		case $mtconf->get('sef_addlisting'):
		case $mtconf->get('sef_addcategory'):
			if($end_segment == $mtconf->get('sef_addlisting')) {
				$vars['task'] = 'addlisting';
			} else {
				$vars['task'] = 'addcategory';
			}
			$cat_id = findCatId(array_slice($segments,0,-1));
			$vars['cat_id'] = $cat_id;
			break;

		case $mtconf->get('sef_listallcats'):
			$vars['task'] = 'listallcats';
			$cat_id = findCatId(array_slice($segments,0,-1));
			$vars['cat_id'] = $cat_id;
			break;

		case $total_segments > 1 && $mtconf->get('sef_rss') == $segments[$total_segments-2]:
			$vars['task'] = 'rss';
			$vars['cat_id'] = findCatId(array_slice($segments,0,-2));
			if($end_segment==$mtconf->get('sef_rss_new')) {
				$vars['type'] = 'new';
			} elseif ($end_segment==$mtconf->get('sef_rss_updated')) {
				$vars['type'] = 'updated';
			}
			break;
	
		default:
		
			// Find as category
			$pagepattern = '/' . $mtconf->get('sef_category_page') . "[0-9]+/";
			if( preg_match($pagepattern,$end_segment) ) {
				$cat_segments = $segments;
				array_pop($cat_segments);
				$cat_id = findCatId($cat_segments);
			} else {
				$cat_id = findCatId($segments);
			}
			if( !empty($cat_id) ) {
				$vars['cat_id'] = $cat_id;
			}
			$vars['task'] = 'listcats';
			$page = getPageNumber($segments);
			if($page > 0) {
				$vars['limit'] = $mtconf->get('fe_num_of_links');
				$vars['limitstart'] = $mtconf->get('fe_num_of_links') * ($page -1);
			}

			// If no category is found, find as a listing
			if( empty($cat_id) )
			{
				$isPaged = preg_match( '/' . $mtconf->get('sef_associated_listing_page') . '[0-9]+/',$end_segment);
				if( $isPaged ) {
					$link_id = findLinkID( array_slice( $segments, 0, -1 ) );
				} else {
					$link_id = findLinkID( $segments );
				}
				
				$vars['task'] = 'viewlink';
				$vars['link_id'] = $link_id;

				if ( $isPaged ) {
					// Get the page numner
					$pagenumber = substr( $end_segment, strlen($mtconf->get('sef_associated_listing_page')) );
					$vars['limit'] = $mtconf->get('fe_num_of_associated');
					$vars['limitstart'] = $mtconf->get('fe_num_of_associated') * ($pagenumber -1);
				}
			}
			break;
	}

	return $vars;
}

function appendCat($cat_id, &$query)
{
	static $current_menu;
	
	$arr_menu_items = array();
	
	// Find if there are existing MT menu items that already provides alias/route
	$menu_items = getMtreeMenuItems();

	// Get an array of menu_items' cat_ids
	$menu_items_cat_ids  =array();
	foreach( $menu_items AS $menu_item )
	{
		if( isset($menu_item->query['view']) )
		{
			if( $menu_item->query['view'] == 'listcats' )
			{
				// Store the list of category IDs that has their own 
				// menu item and thus, top level alias.
				$menu_items_cat_ids[] = $menu_item->query['cat_id'];

				$arr_menu_items[$menu_item->query['cat_id']] = $menu_item;
			} else if ( $menu_item->query['view'] == 'home' ) {

				$menu_items_cat_ids[] = 0;
				$arr_menu_items[0] = $menu_item;
			}
		}
	}
	
	$pathWay = new mtPathWay( $cat_id );
	$pathway_ids = $pathWay->getPathWay( $cat_id );
	
	// Look for exact menu item matching the current cat_id.
	// If a menu item matches a category, we update the router's Itemid
	// to use the top level alias and return an empty segment.
	if( in_array($cat_id,$menu_items_cat_ids) && $cat_id != 0 )
	{
		$pathway_ids = array();
		$segments[] = $arr_menu_items[$cat_id]->route;
		$query['Itemid']=$arr_menu_items[$cat_id]->id;
		return array();
	}

	// Next, we are going to handle cases where the active menu item's 
	// cat_id  that is not ascendant of $cat_id. If this is the case, we
	// look for a task=listcats&cat_id=0 menu item and use its Itemid.
	if( !isset($current_menu) )
	{
		$menu		= JFactory::getApplication()->getMenu();
		$current_menu	= $menu->getActive();
		
	}
	if( 
		isset($current_menu->query['option'])
		&&
		$current_menu->query['option'] == 'com_mtree'
		&&
		isset($current_menu->query['cat_id'])
		&&
		$current_menu->query['cat_id'] > 0
		&&
		!in_array($current_menu->query['cat_id'],$pathway_ids)
	)
	{
		if( isset($arr_menu_items[0]) )
		{
			$query['Itemid']=$arr_menu_items[0]->id;
		}
		else
		{
			unset($query['Itemid']);
		}
		
	}

	$cache = JFactory::getCache('com_mtree');
	// return $cache->call('appendCat_cached', $cat_id, $menu_items_cat_ids, $arr_menu_items, $query);
	return appendCat_cached($cat_id, $menu_items_cat_ids, $arr_menu_items, $query);
}

function appendCat_cached( $cat_id, $menu_items_cat_ids, $arr_menu_items, &$query )
{
	global $mtconf;
	
	static $cat_segments = array();
	static $cat_segments_itemids = array();

	if( array_key_exists($cat_id,$cat_segments) )
	{
		if( array_key_exists($cat_id, $cat_segments_itemids) )
		{
			$query['Itemid'] = $cat_segments_itemids[$cat_id];
		}
		return $cat_segments[$cat_id];
	}
	
	$segments = array();
	$sefstring = '';

	if(!class_exists('mtPathWay')) {
		require_once( $mtconf->getjconf('absolute_path').'/administrator/components/com_mtree/admin.mtree.class.php');
	}

	$pathWay = new mtPathWay( $cat_id );
	$pathway_ids = $pathWay->getPathWay( $cat_id );

	// If one of the parent category has menu item alias, we use that alias
	// to produce a shorten and consistent URL. For example, if an alias 
	// 'companies' available for ''business/companies', we use /companies
	// instead of the full '/business/companies'
	if( !empty($pathway_ids) && !empty($menu_items_cat_ids) )
	{
		foreach( array_reverse($pathway_ids,true) AS $key => $pathway_id)
		{
			if( in_array($pathway_id, $menu_items_cat_ids) )
			{
				$pathway_ids = array_slice($pathway_ids,($key+1));
				$query['Itemid'] = $arr_menu_items[$pathway_id]->id;
				$cat_segments_itemids[$cat_id] = $query['Itemid'];
				break;
			}
			
		}
	}

	if( !empty($pathway_ids) ) {
		foreach( $pathway_ids AS $id ) {
			$segments[] = $pathWay->getCatAlias( $id );
		}
	}
	
	// If current category is not root, append to sefstring
	$cat_alias = $pathWay->getCatAlias($cat_id);
	if ( $cat_id > 0 && !empty($cat_alias) ) {
		$segments[] = $cat_alias;
	}
	
	$cat_segments[$cat_id] = $segments;

	return $segments;
}

function appendListing( $link_name, $link_id, $alias='', $add_details=false ) {
	global $mtconf;
	$segments = array();
	
	switch( $mtconf->get('sef_link_slug_type') )
	{
		case 1:
			$segments[] = $alias;
			break;
		case 2:
			$segments[] = $link_id;
			break;
		case 3:
			$segments[] = $link_id . $mtconf->get('sef_link_slug_type_hybrid_separator') . $alias;
			break;
	}

	if( $add_details ) {
		$segments[] = $mtconf->get('sef_details');
	}

	return $segments;
}

/***
* Find Category ID from an array list of names
* @param array Category name retrieved from SEF Advance URL. 
*/
function findCatID( $cat_names )
{
	global $mtconf;
	static $current_menu;

	$db = JFactory::getDBO();

	// If there are the category slugs, we check for the current active
	// menu. If it has cat_id assigned to it, it will be returned.
	// Otherwise, 0 will be returned.
	if ( count($cat_names) == 0 )
	{
		if( !isset($current_menu) )
		{
			$menu		= JFactory::getApplication()->getMenu();
			$current_menu	= $menu->getActive();
		}

		if( 
			isset($current_menu->query['option'])
			&&
			$current_menu->query['option'] == 'com_mtree'
			&&
			isset($current_menu->query['cat_id'])
			&&
			$current_menu->query['cat_id'] > 0
		)
		{
			return $current_menu->query['cat_id'];
		}
		else
		{
			return 0;
		}		
	}

	for($i=0;$i<count($cat_names);$i++) {
		$cat_names[$i] = preg_replace('/:/', '-', $cat_names[$i], 1);
	}

	// (1) 
	// First Attempt will try to search by category's alias. 
	// If it returns one result, then this is most probably the correct category
	$db->setQuery( "SELECT cat_id, cat_parent, alias FROM #__mt_cats WHERE cat_published='1' AND cat_approved='1' && BINARY alias = " . $db->quote($cat_names[ (count($cat_names)-1) ]) );
	$cat_ids = $db->loadObjectList();

	if ( count($cat_ids) == 1 && $cat_ids[0]->cat_id > 0 )
	{
		$mtconf->setCategory($cat_ids[0]->cat_id);
		return $cat_ids[0]->cat_id;
	}


	// (2)
	// Second attempt will match cat_id from the first level alias up to the 
	// final slug to get the definite category ID

	$full_cat_names_stack = $cat_names;

	// Check if the current active menu item is a Mosets Tree category. If 
	// so, populate $full_cat_names_stack variable with the full pathway 
	// of cat_ids. This is important when a short alias is used through 
	// Joomla menu to use a shorter SEF URL instead of the full MT's 
	// directory raw path URL.
	if( !isset($current_menu) )
	{
		$menu		= JFactory::getApplication()->getMenu();
		$current_menu	= $menu->getActive();
	}

	if( 
		isset($current_menu->query['cat_id'])
		&&
		$current_menu->component == 'com_mtree'
		&&
		is_numeric($current_menu->query['cat_id'])
		&&
		$current_menu->query['cat_id'] > 0
	) {
		if(!class_exists('mtPathway')) {
			require_once( $mtconf->getjconf('absolute_path').'/administrator/components/com_mtree/admin.mtree.class.php');
		}

		$pathWay = new mtPathWay( $current_menu->query['cat_id'] );
		$pathway_ids = $pathWay->getPathWayWithCurrentCat( $current_menu->query['cat_id'] );

		if( !empty($pathway_ids) )
		{
			$pathway_aliases = array();
			foreach( $pathway_ids AS $pathway_id )
			{
				$db->setQuery( "SELECT cat_id, cat_parent, alias FROM #__mt_cats WHERE cat_published='1' AND cat_approved='1' && cat_id = " . $db->quote($pathway_id) . ' LIMIT 1');
				$result = $db->loadObject();
				$pathway_aliases[] = $result->alias;
			}
			$full_cat_names_stack = array_merge( $pathway_aliases, $full_cat_names_stack );
		}
	}

	$pathway_cat_id_matches = array();
	$i=0;
	$category_depth = count($full_cat_names_stack);

	foreach( $full_cat_names_stack AS $key => $cat_name )
	{
		if( $i == 0 )
		{
			$db->setQuery( "SELECT cat_id, cat_parent, alias FROM #__mt_cats WHERE cat_published='1' AND cat_approved='1' && BINARY alias = " . $db->quote($cat_name) . " && cat_parent = 0 LIMIT 1");
		}
		else
		{
			$db->setQuery( "SELECT cat_id, cat_parent, alias FROM #__mt_cats WHERE cat_published='1' AND cat_approved='1' && BINARY alias = " . $db->quote($cat_name) );
		}
		$pathway_cat_id_matches[] = $db->loadObjectList();

		$i++;
		if( $i == ($category_depth-1) )
		{
			$pathway_cat_id_matches[] = $cat_ids;
			break;
		}
	}
	
	$i = 0;
	$pathway_cat_id = array();
	foreach( $pathway_cat_id_matches AS $pathway_cat_id_match )
	{
		if( $i == 0 )
		{
			if( isset($pathway_cat_id_match[$i]->cat_id) ) {
				$pathway_cat_id[$i] = $pathway_cat_id_match[$i]->cat_id;
			}
			$i++;
			continue;
		}
		else 
		{
			foreach( $pathway_cat_id_match AS $objCat )
			{
				if( isset($pathway_cat_id[$i-1]) && $objCat->cat_parent == $pathway_cat_id[$i-1] )
				{
					$pathway_cat_id[$i] = $objCat->cat_id;
					continue;
				}
			}
		}
		$i++;
	}

	if( count($pathway_cat_id) == $category_depth )
	{
		$result = array_pop($pathway_cat_id);
		$mtconf->setCategory($result);
		return $result;
	}
}

function findLinkID( $path_names ) {
	global $mtconf;

	$db = JFactory::getDBO();

	if( !isset($path_names[count($path_names)-1]) ) return null;

	$path_names[count($path_names)-1] = preg_replace('/:/', '-', $path_names[count($path_names)-1], 1);
	
	// (1) 
	// First Attempt will try to search by listing name. 
	// If it returns one result, then this is most probably the correct listing
	
	$link_name = $path_names[ (count($path_names)-1) ];
	$link_name = urldecode( $link_name );
	$link_ids = array();
	
	switch( $mtconf->get('sef_link_slug_type') )
	{
		// Alias slug type
		case 1:
			$db->setQuery( 'SELECT link_id FROM #__mt_links WHERE BINARY alias = ' . $db->quote($link_name) );
			$link_ids = $db->loadColumn();
			break;
		// Link ID slug type
		case 2:
			return intval( $link_name );
			break;
		// Link ID & Alias Hybrid slug type
		case 3:
			return intval(substr($link_name,0,strpos($link_name,$mtconf->get('sef_link_slug_type_hybrid_separator'))));
			break;
	}
	
	if ( count($link_ids) == 1 && $link_ids[0] > 0 ) {

		return $link_ids[0];

	} else {

	// (2)
	// Second attempt will look for the category ID and then pinpoint the listing ID
		
		$cat_id = findCatID( array_slice($path_names, 0, -1) );
		
		if( $mtconf->get('sef_link_slug_type') == 1 )
		{
			$db->setQuery( "SELECT l.link_id FROM #__mt_links AS l, #__mt_cl AS cl "
				. " WHERE link_published='1' AND link_approved='1' AND cl.cat_id = '".$cat_id."'"
				. " AND BINARY l.alias = " . $db->quote($link_name) . " AND l.link_id = cl.link_id LIMIT 1" );
			return $db->loadResult();
		} else {
			return null;
		}
	}
}

function getPage($start,$limit) {
	return (($start / $limit) +1);
}

/***
* Try to find the page number from virtual directory - http://example.com/c/mtree/My_Listing/Page3.html
*
* @param array $url_array The SEF advance URL split in arrays (first custom virtual directory beginning at $pos+1)
* @return int Page number
*/
function getPageNumber( $segments ) {
	global $mtconf;
	
	$pagepattern = '/' . $mtconf->get('sef_category_page') . "[0-9]+/";
	$pagenumber = 0;
	if ( preg_match($pagepattern,end($segments)) ) {
		// Get the page number
		$pagenumber = substr( end($segments), strlen($mtconf->get('sef_category_page')));
	}
	return $pagenumber;
}

function getId( $type, &$query )
{
	static $current_menu;

	if( $type == 'cat' )
	{
		if( !isset($current_menu) )
		{
			$menu		= JFactory::getApplication()->getMenu();
			$current_menu	= $menu->getActive();
		}
		
		// Below will detect URLs that have cat_id query and is also a MT Category menu item that contains 
		// cat_id. cat_id from URL query will have higher priority, thus it will override the menu item's 
		// (Itemid) cat_id.
		if(
			isset($current_menu->query['cat_id'])
			&&
			isset($query['cat_id'])
			&&
			$current_menu->query['cat_id'] != $query['cat_id']
		)
		{
			$query_cat_id = $query['cat_id'];
			unset($query['cat_id']);
			return $query_cat_id;
		}

		if( 
			isset($current_menu->query['option'])
			&&
			$current_menu->query['option'] == 'com_mtree'
			&&
			isset($current_menu->query['cat_id'])
			&&
			$current_menu->query['cat_id'] > 0
		)
		{
			unset($query[$type.'_id']);
			return $current_menu->query['cat_id'];
		}
	}

	$id = 0;
	if(isset($query[$type.'_id'])) {
		$id = intval($query[$type.'_id']);
		unset($query[$type.'_id']);
	}
	return $id;
}

/***
* Return value from appendCat + appendListing
*/
function appendCatListing( $mtLink, &$query, $add_extension=true ) {
	return array_merge( appendCat( $mtLink->cat_id, $query ), appendListing( $mtLink->link_name, $mtLink->link_id, $mtLink->alias, false ) );
}

function appendTag($cf_id) {
	static $tags;
	
	if( !$tags )
	{
		$tags = getTagAliases();
	}
	
	if( isset($tags[$cf_id]) ) {
		return $tags[$cf_id]->alias;
	} else {
		return false;
	}
}

function findCfId($alias) {
	static $tags;
	
	if( !$tags )
	{
		$tags = getTagAliases();
	}
	
	foreach( $tags AS $tag ) {
		if( $tag->alias == $alias ) {
			return $tag->cf_id;
		}
	}
	return false;
}

function getTagAliases() {
	$db = JFactory::getDBO();
	$db->setQuery('SELECT cf_id, caption, alias FROM #__mt_customfields WHERE tag_search = 1 AND published = 1');
	$tags = $db->loadObjectList('cf_id');
	if( !empty($tags) )
	{
		foreach($tags AS $tag)
		{
			if( !empty($tag->alias) )
			{
				$tags[$tag->cf_id]->alias = $tag->alias;
			} else {
				$tags[$tag->cf_id]->alias = JFilterOutput::stringURLSafe($tag->caption);
			}
		}
	}
	return $tags;
}

function murlencode($string) {
	global $mtconf, $sef_replace;
	$string = urlencode($string);
	$string = preg_replace('/' . $mtconf->get('sef_space') . '/', "%252D", $string);
	$string = preg_replace('/\+/', $mtconf->get('sef_space'), $string);
	$string = preg_replace('/\./', '%2E', $string);
	foreach ($sef_replace as $key => $value) {
		$string = preg_replace('/'.$key.'/', $value, $string);
	}
	return $string;
}

function murldecode($string) {
	global $mtconf, $sef_replace;
	foreach ($sef_replace as $key => $value) {
		$string = str_replace(strtolower($value), strtolower(urldecode($key)), strtolower($string));
	}
	$string = preg_replace('/'.$mtconf->get('sef_space').'/', "%20", $string);
	$string = preg_replace('/\+/', "%2B", $string);
	$string = preg_replace('/&quot;/', "%22", $string);
	$string = preg_replace("/%2D/", $mtconf->get('sef_space'), $string);
	$string = urldecode($string);
	return $string;
}
?>