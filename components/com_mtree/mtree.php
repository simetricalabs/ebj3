<?php
/**
 * @version	$Id: mtree.php 2114 2013-10-16 13:40:36Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

global $task, $link_id, $cat_id, $user_id, $img_id, $start, $limitstart, $mtconf;
global $option;

$option = 'com_mtree';

require(  JPATH_COMPONENT.'/init.php' );
$app		= JFactory::getApplication();
$database 	= JFactory::getDBO();
$my		= JFactory::getUser();
$document	= JFactory::getDocument();

require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/admin.mtree.class.php' );
require_once( JPATH_COMPONENT.'/mtree.class.php' );
require_once( JPATH_COMPONENT.'/mtree.tools.php' );
require_once( JPATH_COMPONENT.'/methods.php' );

# Caches
global $cache_cat_names, $cache_paths, $cache_lft_rgt;
$cache_cat_names = array();
$cache_paths = array();
$cache_lft_rgt = array();
$cache = JFactory::getCache('com_mtree');

# Savant Class
require_once( JPATH_COMPONENT_SITE.'/Savant2.php');

$task		= JFactory::getApplication()->input->getCmd('task', '');
$link_id	= JFactory::getApplication()->input->getInt('link_id', 0);
$cat_id		= JFactory::getApplication()->input->getInt('cat_id', 0);
$user_id	= JFactory::getApplication()->input->getInt('user_id', 0);
$img_id		= JFactory::getApplication()->input->getInt('img_id', 0);
$rev_id		= JFactory::getApplication()->input->getInt('rev_id', 0);
$cf_id 		= JFactory::getApplication()->input->getInt( 'cf_id', 0);
$alpha		= JString::substr(JString::trim(JFactory::getApplication()->input->getString('alpha', '')), 0, 3);
$limitstart	= JFactory::getApplication()->input->getInt('limitstart', 0);
$type		= JFactory::getApplication()->input->getCmd('type', '');

if(empty($task))
{
	$task	= JFactory::getApplication()->input->getCmd('view', '');
}

# Itemid
global $Itemid;
$menu	= $app->getMenu();
$items	= $menu->getItems('link', 'index.php?option=com_mtree&view=categories');

if(isset($items[0])) {
	$Itemid = $items[0]->id;
} else if (JFactory::getApplication()->input->getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
	$Itemid = JFactory::getApplication()->input->getInt('Itemid');
}

$jdate 		= JFactory::getDate();
$now		= $jdate->toSql();

global $savantConf;
$savantConf = array (
		'template_path' => JPATH_SITE.'/components/com_mtree/templates/'.$mtconf->get('template').'/',
		'plugin_path' => JPATH_SITE.'/components/com_mtree/Savant2/',
		'filter_path' => JPATH_SITE.'/components/com_mtree/Savant2/'
);

$format		= JFactory::getApplication()->input->getCmd('format', '');
$tmpl		= JFactory::getApplication()->input->getCmd('tmpl', '');

if( $format != 'json' && $tmpl != 'component' )
{
	mtAppendPathWay( $option, $task, $cat_id, $link_id, $cf_id, $img_id, $rev_id, $user_id );
}

switch ($task) {
	
	case "att_download":
		$ordering	= JFactory::getApplication()->input->getInt( 'o'		,0 	);
		$filename 	= JFactory::getApplication()->input->getString( 'file'		,''	);
		$link_id 	= JFactory::getApplication()->input->getInt( 'link_id'		,0 	);
		$img_id 	= JFactory::getApplication()->input->getInt( 'img_id'		,0 	);
		$size 		= JFactory::getApplication()->input->getInt( 'size'		,0	);
		att_download( $ordering, $filename, $link_id, $cf_id, $img_id, $size );
		break;
		
	case "viewimage":
		viewimage( $img_id, $option );
		break;

	case "viewgallery":
		viewgallery( $link_id, $option );
		break;
	
	case "viewreviews":
		viewreviews( $link_id, $limitstart, $option );
		break;
	
	case "viewreview":
		viewreview( $rev_id, $option );
		break;

	case "viewlink":
		viewlink( $link_id, $my, $limitstart, $option );
		break;

	case "print":
		printlink( $link_id, $option );
		break;

	/* RSS feed */
	case 'rss':
		$type = JFactory::getApplication()->input->getCmd('type', 'new');
		$token = JFactory::getApplication()->input->getCmd('token', '');
		$rss_secret_token = $mtconf->get( 'rss_secret_token');
		if( 
			($type == 'new' && $mtconf->get('show_listnewrss') == 0) 
			|| 
			($type == 'type' && $mtconf->get('show_listupdatedrss') ==  0) 
		) {
			echo JText::_("ALERTNOTAUTH");
		} elseif( !empty($rss_secret_token) && $token != $rss_secret_token ) {
			echo JText::_("ALERTNOTAUTH");
		} else {
			require_once( JPATH_SITE.'/components/com_mtree/rss.php');
			rss( $option, $type, $cat_id );
		}
		break;

	/* Visit a URL */
	case "visit":
		visit( $link_id, $cf_id );
		break;

	/* Reviews */
	case "writereview":
		writereview( $link_id, $option );
		break;
	case "addreview":
		addreview( $link_id, $option );
		break;

	/* Ratings */
	case "rate":
		rate( $link_id, $option );
		break;
	case "addrating":
		addrating( $link_id, $option );
		break;
	
	/* Favourite */
	case "fav":
		$action = JFactory::getApplication()->input->getInt('action', 1);
		fav( $link_id, $action, $option );
		break;

	/* Vote review */
	case 'votereview':
		$rev_vote	= JFactory::getApplication()->input->getInt('vote', 0);
		$rev_id		= JFactory::getApplication()->input->getInt('rev_id', 0);
		votereview( $rev_id, $rev_vote, $option );
		break;

	/* Report review */
	case "reportreview":
		$rev_id	= JFactory::getApplication()->input->getInt('rev_id', 0);
		reportreview( $rev_id, $option );
		break;
	case "send_reportreview":
		$rev_id	= JFactory::getApplication()->input->getInt('rev_id', 0);
		send_reportreview( $rev_id, $option );
		break;

	/* Reply review */
	case 'replyreview':
		$rev_id	= JFactory::getApplication()->input->getInt('rev_id', 0);
		replyreview( $rev_id, $option );
		break;
	case 'send_replyreview':
		$rev_id	= JFactory::getApplication()->input->getInt('rev_id', 0);
		send_replyreview( $rev_id, $option );
		break;

	/* Recommend to Friend */
	case "recommend":
		recommend( $link_id, $option );
		break;
	case "send_recommend":
		send_recommend( $link_id, $option );
		break;

	/* Contact Owner */
	case "contact":
		contact( $link_id, $option );
		break;
	case "send_contact":
		send_contact( $link_id, $option );
		break;

	/* Report Listing */
	case "report":
		report( $link_id, $option );
		break;
	case "send_report":
		send_report( $link_id, $option );
		break;

	/* Claim Listing */
	case "claim":
		claim( $link_id, $option );
		break;
	case "send_claim":
		send_claim( $link_id, $option );
		break;

	/* Add Listing */
	case "addlisting":
		editlisting( 0, $option );
		break;
	case "editlisting":
		editlisting( $link_id, $option );
		break;
	case "savelisting":
		require_once( JPATH_COMPONENT_SITE.'/includes/diff.php');
		savelisting( $option );
		break;

	/* Add Category */
	case "addcategory":
		addcategory( $option );
		break;
	case "addcategory2":
		addcategory2( $option );
		break;

	/* Delete Listing */
	case "deletelisting":
		deletelisting( $link_id, $option );
		break;
	case "confirmdelete":
		confirmdelete( $link_id, $option );
		break;

	/* My Page */
	case "mypage":
		switch($type)
		{
			default:
			case 'listing':
				viewuserslisting( $my->id, $limitstart, $option );
				break;
			case 'favourite':
				viewusersfav( $my->id, $limitstart, $option );
				break;
			case 'review':
				viewusersreview( $my->id, $limitstart, $option );
				break;
		}
		break;

	/* All listing from this owner */
	case "viewowner":
		call_user_func($mtconf->get('owner_default_page'), $user_id, $limitstart, $option);
		break;
		
	/* All listing from this owner */
	case "viewuserslisting":
		viewuserslisting( $user_id, $limitstart, $option );
		break;

	/* All review from this user */
	case "viewusersreview":
		viewusersreview( $user_id, $limitstart, $option );
		break;

	/* All user's favourites */
	case "viewusersfav":
		viewusersfav( $user_id, $limitstart, $option );
		break;

	/* List Alphabetically */
	case "listalpha":
		listalpha( $cat_id, $alpha, $limitstart, $option );
		break;
	
	/* List Listing */
	case "listall":
	case "listpopular":
	case "listmostrated":
	case "listtoprated":
	case "listmostreview":
	case "listnew":
	case "listupdated":
	case "listfeatured":
	case "listfavourite":
	case "toplisting":
		if( $task == 'toplisting' ) {
			$task = "list".$type;
		}
		$sort	= JFactory::getApplication()->input->getCmd('sort', $mtconf->get('all_listings_sort_by'));
		
		require_once( JPATH_SITE.'/components/com_mtree/listlisting.php');
		listlisting( $cat_id, $option, $my, $task, $sort, $limitstart );
		break;

	/* Search */
	case "search":
		search( $option );
		break;
	case "searchby":
		searchby( $option );
		break;
	case "advsearch":
		advsearch( $cat_id, $option );
		break;
	case "advsearch2":
		advsearch2( $cat_id, $option );
		break;
	
	case "listallcats":
		listallcats( $cat_id, $option );
		break;
	
	/* Ajax Category */
	case "ajax":
		$task2 = JFactory::getApplication()->input->getCmd('task2', '');
	 	require_once($mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/admin.mtree.ajax.php');
		break;
	
	/* JSON List Fields */
	case "fields.list":
		require_once JPATH_COMPONENT.'/fields.json.php';
		break;
		
	/* Default Main Index */
	case "listcats":
	case "home":
	default:
		showTree( $cat_id, $limitstart, $option, $my );
		break;
}

// Append CSS file to Head
if( $mtconf->get('load_css') && $document->getType() == 'html')
{
	if ( file_exists( $savantConf['template_path'] . 'template.css' ) ) {
		if( $mtconf->getjconf('absolute_path') == '/' )
		{
			$document->addCustomTag("<link href=\"" .$mtconf->getjconf('live_site') . $savantConf['template_path'] . 'template.css' . "\" rel=\"stylesheet\" type=\"text/css\"/>");
			
		} else {
			$document->addCustomTag("<link href=\"" . str_replace($mtconf->getjconf('absolute_path'),$mtconf->getjconf('live_site'),$savantConf['template_path'] . 'template.css') . "\" rel=\"stylesheet\" type=\"text/css\"/>");
		}
	} elseif ( file_exists( $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $mtconf->get('template') . '/template.css' ) ) {
		$document->addCustomTag("<link href=\"". $mtconf->getjconf('live_site') ."/components/com_mtree/templates/".$mtconf->get('template')."/template.css\" rel=\"stylesheet\" type=\"text/css\"/>");
	} else {
		$document->addCustomTag("<link href=\"". $mtconf->getjconf('live_site') ."/components/com_mtree/templates/kinabalu/template.css\" rel=\"stylesheet\" type=\"text/css\"/>");
	}

	if (  $mtconf->get('load_bootstrap_css') ) {
		JHtml::_('bootstrap.loadCss');
	}
}

function showTree( $cat_id, $limitstart, $option, $my ) {
	global $mtconf;

	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();
	$document	= JFactory::getDocument();
	
	$database->setQuery( 'SELECT * FROM #__mt_cats '
		.	'WHERE cat_id=' . $database->quote($cat_id) . ' AND cat_published = 1 LIMIT 1' );
	$cat = $database->loadObject();

	if ( !is_null($cat) ) {
		# Set Page Title
		if ( $cat_id == 0 ) {
			$title = JText::_( 'COM_MTREE_PAGE_TITLE_ROOT' );
			$cat->cat_allow_submission = $mtconf->get('allow_listings_submission_in_root');
		} elseif( !empty($cat->title) ) {
			$title = $cat->title;
		} else {
			$title = $cat->cat_name;
		}
		
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_LISTCATS', $title ), $cat_id);

		# Add META tags
		if ($mtconf->getjconf('MetaTitle')=='1') {
			if( $cat_id == 0 ) {
				$document->setMetadata( 'title' , JText::_( 'COM_MTREE_PAGE_TITLE_ROOT' ) );
			} else {
				$document->setMetadata( 'title' , htmlspecialchars($cat->cat_name) );
			}
		}

		$rss_secret_token = $mtconf->get( 'rss_secret_token');
		if( $mtconf->get( 'show_category_rss' ) && empty($rss_secret_token) ) {
			$document->addCustomTag( '<link rel="alternate" type="application/rss+xml" title="' . $mtconf->getjconf('sitename') . ' - ' . $cat->cat_name . '" href="index.php?option=com_mtree&task=rss&type=new&cat_id=' . $cat_id . '" />' );
		}

		if ($cat->metadesc <> '')
		{
			$document->setDescription( htmlspecialchars($cat->metadesc) );
		}
		elseif( $cat_id == 0 )
		{
			$document->setDescription( JText::_( 'COM_MTREE_METADESC_ROOT' ) );
		}
		
		if ($cat->metakey <> '')
		{
			$document->setMetaData('keywords', htmlspecialchars($cat->metakey));
		}
		elseif( $cat_id == 0 )
		{
			$document->setMetaData( 'keywords', JText::_( 'COM_MTREE_METAKEY_ROOT' ) );
		}

		$cache = JFactory::getCache('com_mtree');
		$cache->call( 'showTree_cache', $cat, $limitstart, $option, $my );

	} else {
		return JError::raiseError(404,JText::_('COM_MTREE_ERROR_CATEGORY_NOT_FOUND'));
	}
}

function showTree_cache( $cat, $limitstart, $option, $my ) {
	global $Itemid, $savantConf, $mtconf;

	$database	= JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	if ( empty($cat->cat_id) ) {
		$cat_id = 0;
	} else {
		$cat_id = $cat->cat_id;
	}

	if ( isset($cat->cat_published) && $cat->cat_published == 0 && $cat_id > 0 ) {
		
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_CATEGORY_NOT_FOUND'));

	} else {

		# Page Navigation
		$database->setQuery( 'SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_published = 1 AND l.link_approved = 1 && cl.cat_id = ' . $database->quote($cat_id)
			. "\n AND ( l.publish_up = ".$database->Quote($nullDate)." OR l.publish_up <= '$now'  ) "
			. "\n AND ( l.publish_down = ".$database->Quote($nullDate)." OR l.publish_down >= '$now' ) "
			. "\n AND cl.link_id = l.link_id "
		);
		$total_links = $database->loadResult();

		jimport('joomla.html.pagination');

		# Retrieve categories
		$sql = 'SELECT cat.* FROM #__mt_cats AS cat ';
		$sql .= 'WHERE cat_published=1 && cat_approved=1 && cat_parent= ' . $database->quote($cat_id);

		if ( !$mtconf->get('display_empty_cat') ) { $sql .= ' && ( cat_cats > 0 || cat_links > 0 ) ';	}

		if( $mtconf->get('first_cat_order1') != '' )
		{
			$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
			if( $mtconf->get('second_cat_order1') != '' )
			{
				$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
			}
		}

		$database->setQuery( $sql );
		$cats = $database->loadObjectList("cat_id");

		$cat_desc = '';
		$related_categories = null;
		$cat_ids = array();
		
		foreach ( $cats AS $c ) {
			$cat_ids[] = $c->cat_id;
		}

		$sub_cats = array();
		
		# Only shows sub-cat if this is a root category
		if ( ($cat_id == 0 || $cat->cat_usemainindex == 1) && intval($mtconf->getTemParam('numOfSubcatsToDisplay')) > 0 && !empty($cat_ids)) {
			# Get all sub-cats
			$sql = "SELECT cat_id, cat_name, cat_cats, cat_links, cat_parent FROM #__mt_cats WHERE cat_parent IN (".implode(',',$cat_ids).") && cat_published='1' && cat_approved='1' ";

			if ( !$mtconf->get('display_empty_cat') ) { $sql .= " && ( cat_cats > 0 || cat_links > 0 ) ";	}
			
			if( $mtconf->get('first_cat_order1') != '' )
			{
				$sql .= "\nORDER BY cat_featured DESC, " . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
				if( $mtconf->get('second_cat_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
				}
			}
			
			$database->setQuery( $sql );
			$sub_cats_tmp = $database->loadObjectList();
			
			if(!empty($sub_cats_tmp)) {
				foreach($sub_cats_tmp AS $sub_cat) {
					if( isset($sub_cats[$sub_cat->cat_parent]) ) {
						if( $mtconf->getTemParam('numOfSubcatsToDisplay') > 0 && count($sub_cats[$sub_cat->cat_parent]) < $mtconf->getTemParam('numOfSubcatsToDisplay') ) {
							array_push($sub_cats[$sub_cat->cat_parent],$sub_cat);
						}
					} else {
						$sub_cats[$sub_cat->cat_parent] = array($sub_cat);
					}
					if(!isset($sub_cats_total[$sub_cat->cat_parent])) {
						$total_sub_cats = $cats[$sub_cat->cat_parent]->cat_cats;
						$sub_cats_total[$sub_cat->cat_parent] = (($total_sub_cats) ? $total_sub_cats : 0 );
					}
				}
			}
			if (isset($sub_cats)) {
				foreach($cat_ids AS $c) {
					if(!array_key_exists($c,$sub_cats)) {
						$sub_cats[$c] = array();
					}
				}
			}
			unset($sub_cats_tmp);

		} else {

			# Get related categories
			$database->setQuery( 'SELECT r.rel_id FROM #__mt_relcats AS r '
				.	'LEFT JOIN #__mt_cats AS c ON c.cat_id = r.rel_id '
				.	'WHERE r.cat_id = ' . $database->quote($cat_id) . ' AND c.cat_published = 1' );
			$related_categories = $database->loadColumn();

		}

		# Get subset of listings underneath each top level's 
		# (relative to the current category) categories
		if( 
			($cat_id == 0 || $cat->cat_usemainindex == 1) 
			&& 
			is_numeric($mtconf->getTemParam('numOfLinksToDisplay',3)) 
			&&
			$mtconf->getTemParam('numOfLinksToDisplay',3)!=0 
			&&
			!empty($cat_ids)
		)
		{
			$sql = "SELECT l.link_id, link_name, cl.cat_id FROM #__mt_links AS l "
				.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
				.	"\n WHERE link_published='1' && link_approved='1' && cl.cat_id IN (".implode(',',$cat_ids).')'
				.	"\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
				.	"\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) ";
			if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
				$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			} else {
				$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			}

			$database->setQuery( $sql );
			$cat_links_tmp = $database->loadObjectList();
			if(!empty($cat_links_tmp)) {
				foreach($cat_links_tmp AS $cat_link) {
					if(isset($cat_links[$cat_link->cat_id])) {
						if(
							(
							$mtconf->getTemParam('numOfLinksToDisplay',3) > 0 
							&& 
							count($cat_links[$cat_link->cat_id]) < $mtconf->getTemParam('numOfLinksToDisplay',3)
							)
							||
							$mtconf->getTemParam('numOfLinksToDisplay',3) == -1
						) {
							array_push($cat_links[$cat_link->cat_id],$cat_link);
						}
					} else {
						$cat_links[$cat_link->cat_id] = array($cat_link);
					}
				}
			}
			foreach($cat_ids AS $c) {
				if(!isset($cat_links) || !array_key_exists($c,$cat_links)) {
					$cat_links[$c] = array();
				}
			}

		}
		
		# Retrieve Links assign to the current category
		if( 
			($cat_id == 0 || $cat->cat_usemainindex == 1) 
			&&
			$mtconf->get('type_of_listings_in_index') != 'listcurrent'
		)
		{
			require_once( JPATH_SITE.'/components/com_mtree/listlisting.php');
			$listListing	= new mtListListing( $mtconf->get('type_of_listings_in_index') );
			$listListing->setLimitStart( $limitstart );
			$listListing->setLimit( $mtconf->get('type_of_listings_in_index_count') );
			
			$listListing->setSubcats( getSubCats_Recursive($cat_id) );
			$listListing->prepareQuery();
			$links = $listListing->getListings();

			$pageNav = $listListing->getPageNav();
		}
		else
		{
			
			$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cl.*, cat.*, u.username AS username, u.name AS owner, img.filename AS link_image FROM #__mt_links AS l"
				.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
				.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
				.	"\n LEFT JOIN #__mt_cats AS cat ON cl.cat_id = cat.cat_id "
				.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
				.	"\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
				.	"\n WHERE link_published='1' && link_approved='1' && cl.cat_id = " . $database->quote($cat_id) . ' '
				.	"\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
				.	"\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) ";

			if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
				$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			} else {
				$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			}
			$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');
			$database->setQuery( $sql );
			$links = $database->loadObjectList();
			
			$pageNav = new JPagination($total_links, $limitstart, $mtconf->get('fe_num_of_links'));
			
		}

		# Pathway
		$pathWay = new mtPathWay( $cat_id );

		if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {
			loadCustomTemplate(null,$savantConf,$cat->cat_template);
		}

		# Support Plugins
		if( isset($cat->cat_desc) && !empty($cat->cat_desc) ) {
			$cat->text = $cat->cat_desc;
		} else {
			$cat->text = '';
		}

		if($mtconf->get('cat_parse_plugin')) {
			$params = new JRegistry();
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onContentPrepare', array ('com_mtree.category', &$cat, & $params->params, 0));
			$cat->cat_desc = $cat->text;
		}

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );
		$savant->assign('user_addlisting', $mtconf->get('user_addlisting'));
		
		if (isset($cat->cat_allow_submission)) {
			$savant->assign('cat_allow_submission',$cat->cat_allow_submission);
		} else {
			$savant->assign('cat_allow_submission',0);
			$cat->cat_allow_submission = 0;
		}

		if (isset($cat->cat_show_listings)) {
			$savant->assign('cat_show_listings',$cat->cat_show_listings);
		} else {
			$cat->cat_show_listings = 0;
			$savant->assign('cat_show_listings',0);
		}

		if (isset($cat_links)) $savant->assign('cat_links', $cat_links);
		$savant->assign('Itemid', $Itemid);
		$savant->assign('cat', $cat);
		$savant->assign('cat_id', $cat_id);
		$savant->assign('tlcat_id', getTopLevelCatID($cat_id));
		$savant->assign('categories', $cats);
		if (isset($sub_cats)) $savant->assign('sub_cats', $sub_cats);
		if (isset($sub_cats_total)) $savant->assign('sub_cats_total', $sub_cats_total);
		$savant->assign('related_categories', $related_categories);
		$savant->assignRef('links', $links);
		if (isset($listListing)) $savant->assign('listListing', $listListing);

		$savant->assign('total_listing', $total_links);

		if ( $cat_id == 0 || $cat->cat_usemainindex == 1 ) {
			$savant->assign('display_listings_in_root', $mtconf->get('display_listings_in_root'));
			$savant->assign('cat_usemainindex', $cat->cat_usemainindex);
			$savant->display( 'page_index.tpl.php' );
		} else {
			$savant->display( 'page_subCatIndex.tpl.php' );
		}

	}

}

/***
* Search By
*/
function searchby( $option )
{
	global $mtconf, $savantConf, $Itemid;
	
	$database 	=& JFactory::getDBO();
	$uri 		=& JUri::getInstance();
	$nullDate	= $database->getNullDate();

	$value 		= JFactory::getApplication()->input->getString( 'value', '' );
	$cf_id 		= JFactory::getApplication()->input->getInt( 'cf_id', '' );
	$limitstart	= JFactory::getApplication()->input->getInt('limitstart', 0);
	$search_cat	= JFactory::getApplication()->input->getInt('cat_id', 0);
	if( $limitstart < 0 ) $limitstart = 0;

	$only_subcats_sql = '';
	if ( $search_cat > 0 ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
		$subcats[] = $search_cat;
		if ( !empty($subcats) ) {
			$only_subcats_sql = "\n AND cat.cat_id IN (" . implode( ", ", $subcats ) . ")";
		}
	}

	$jdate = JFactory::getDate();
	$now = $jdate->toSql();
	$tlcat_id = getTopLevelCatID($search_cat);
	
	# Load custom template
	loadCustomTemplate( $search_cat, $savantConf);

	if( $cf_id > 0 )
	{
		# Retrieve information about custom field
		$database->setQuery( 'SELECT * FROM #__mt_customfields AS cf'
			. ' WHERE cf.cf_id = ' . $database->Quote($cf_id) . ' AND published = 1 AND tag_search = 1 LIMIT 1');
		$customfield = $database->loadObject();
		
		if( is_null($customfield) )
		{
			return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		}
	}
	else
	{
		// List all taggable fields
		$database->setQuery(
			'SELECT * FROM #__mt_customfields WHERE published = 1 AND tag_search = 1 ORDER BY ordering ASC'
			. " LIMIT $limitstart, ".$mtconf->get('fe_num_of_searchby')
			);
		$raw_taggable_fields = $database->loadObjectList();

		$database->setQuery( 'SELECT COUNT(cf_id) FROM #__mt_customfields WHERE published = 1 AND tag_search = 1' );
		$total_taggable_fields = $database->loadResult();

		// Loop through each taggable fields and built a meaningful object for use in template
		$i=0;
		foreach( $raw_taggable_fields AS $tag )
		{
			$taggable_fields[$i]->value = $tag->caption;
			$taggable_fields[$i]->link  = JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$tag->cf_id.'&Itemid='.$Itemid);
			$taggable_fields[$i]->elementId  = 'searchbytags-'.JFilterOutput::stringURLUnicodeSlug($tag->caption);
			$i++;
		}
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_taggable_fields, $limitstart, $mtconf->get('fe_num_of_searchby'));
		
		# Savant Template
		$savant = new Savant2($savantConf);
		$savant->assignRef('pathway', $pathWay);
		$savant->assign('pageNav', $pageNav);
		$savant->assign('cf_id', $cf_id);
		$savant->assign('cat_id', $search_cat);
		$savant->assign('tlcat_id', $tlcat_id);
		$savant->assign('taggable_fields', $taggable_fields);
		$savant->assign('template', $mtconf->get('template'));
		$savant->display( 'page_searchBy.tpl.php' );
		return;		
	}
	
	if( empty($value) )
	{
		// List values from custom field
		
		// Is the field a core field?
		$database->setQuery('SELECT iscore FROM #__mt_customfields WHERE cf_id = ' . $database->Quote($cf_id) . ' LIMIT 1');
		$iscore = $database->loadResult();
		
		// Retrieve the custom field values
		if ( $iscore )
		{
			$database->setQuery('SELECT field_type FROM #__mt_customfields WHERE cf_id = ' . $database->Quote($cf_id) . ' LIMIT 1');
			$field_type = $database->loadResult();
			$core_name = substr($field_type,4);
			$database->setQuery('SELECT ' . $core_name . ' FROM #__mt_links WHERE ' . $core_name . ' != \'\'');
		} else {
			$database->setQuery('SELECT REPLACE(value,\'|\',\',\') FROM #__mt_cfvalues WHERE cf_id = ' . $database->Quote($cf_id));
		}
		$arrTags = $database->loadColumn();

		// Read through array of strings and return an array mapping tag with number of occurances
		$rawTags = array();
		foreach( $arrTags AS $tag )
		{
			$results = explode(',',$tag);
			$count = count($results);

			for($i=0;$i<$count;$i++)
			{
				$results[$i] = trim($results[$i]);
			}

			$rawTags = array_merge($rawTags,array_unique($results));
		}
		$rawTags = array_count_values($rawTags);
		$total = count($rawTags);
		arsort($rawTags);
		
		$rawTags = array_slice($rawTags, $limitstart, $mtconf->get('fe_num_of_searchbytags'));

		// Loop through each tag and built a meaningful object for use in template
		$i=0;
		foreach( $rawTags AS $tag => $items )
		{
			$tags[$i]->value = $tag;
			$tags[$i]->items = $items;
			$tags[$i]->link  = JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$cf_id.'&value='.$tag.'&Itemid='.$Itemid);
			$tags[$i]->elementId  = 'searchbytags-value-'.JFilterOutput::stringURLUnicodeSlug($tag);
			$i++;
		}

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_searchbytags'));
		
		# Savant Template
		$savant = new Savant2($savantConf);
		$savant->assignRef('pathway', $pathWay);
		$savant->assign('pageNav', $pageNav);
		$savant->assign('cf_id', $cf_id);
		$savant->assign('cat_id', $search_cat);
		$savant->assign('tlcat_id', $tlcat_id);
		$savant->assign('searchword', $value);
		$savant->assign('customfieldcaption', $customfield->caption);
		$savant->assign('tags', $tags);
		$savant->assign('template', $mtconf->get('template'));
		$savant->display( 'page_searchByTags.tpl.php' );
		
	} else {
		// Show listings matching value from a custom field
		
		# Retrieve links
		$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cl.*, cat.*, u.username AS username, u.name AS owner, img.filename AS link_image FROM #__mt_links AS l";
		if( !$customfield->iscore ) {
			$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = " . $database->Quote($cf_id);
		}
		$sql .=	"\n	LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 " 
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id " 
			.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			.	"\n WHERE " 
			. 	"\n link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 ";
		if( !$customfield->iscore ) {
			$sql .= "\n AND cfv.value LIKE " . $database->Quote('%'.$value.'%');
		} else {
			$sql .= "\n AND l.".substr($customfield->field_type,4)." LIKE " . $database->Quote('%'.$value.'%');
		}
		$sql .=	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );

		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_search_order1') . ' ' . $mtconf->get('first_search_order2') . ', ' . $mtconf->get('second_search_order1') . ' ' . $mtconf->get('second_search_order2');
		}

		$sql .=	"\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_all');
		$database->setQuery( $sql );
		$links = $database->loadObjectList();	

		# Get total
		$sql = "SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl";
			$sql .= ")";
			if( !$customfield->iscore ) {
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = " . $database->Quote($cf_id);
			}
			$sql .=	"\n	LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id " 
				.	"\n	WHERE " 
				.	"link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
				.	"\n AND cl.link_id = l.link_id "
				.	"\n AND cl.main = 1 ";
			if( !$customfield->iscore ) {
				$sql .= "\n AND cfv.value LIKE " . $database->Quote('%'.$value.'%');
			} else {
				$sql .= "\n AND l.".substr($customfield->field_type,4)." LIKE " . $database->Quote('%'.$value.'%');
			}
			$sql .= ( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
			$database->setQuery( $sql );

		$total = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_all'));

		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_SEARCH_BY', $customfield->caption, $value ));

		# Pathway
		$pathWay = new mtPathWay();

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

		$savant->assign('searchword', $value);
		$savant->assign('customfieldcaption', $customfield->caption);
		$savant->assign('cf_id', $cf_id);
		$savant->assign('cat_id', $search_cat);
		$savant->assign('tlcat_id', $tlcat_id);
		$savant->assign('total_listing', $total);
		$savant->display( 'page_searchByResults.tpl.php' );		
	}
}

/***
* Simple Search
*/
function search( $option ) {
	global $savantConf, $custom404, $mtconf;

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$uri 		=& JUri::getInstance();
	$nullDate	= $database->getNullDate();

	# Search word
	$post['searchword'] = JFactory::getApplication()->input->getString('searchword', null);

	$post['cat_id'] = JFactory::getApplication()->input->getInt('cat_id', 0);
	$post['search_cat'] = JFactory::getApplication()->input->getInt('search_cat', 0);

	// set Itemid id for links
	require( JPATH_ROOT.'/components/com_mtree/init.module.php');
	$itemid		= MTModuleHelper::getItemid();

	$intItemid = (int) str_replace('&Itemid=', '', $itemid);
	if( $intItemid > 0 ) {
		$uri->setVar('Itemid', $intItemid);
	}

	if( is_null($uri->getVar( 'searchword' )) && isset($post['searchword']) && !empty($post['searchword']) )
	{
		$uri->setVar('option', 'com_mtree');
		$uri->setVar('task', 'search');
		$uri->setVar('searchword', $post['searchword']);
		$uri->setVar('cat_id', $post['cat_id']);
		if( $post['search_cat'] )
		{
			$uri->setVar('search_cat', $post['search_cat']);
		}

		$app->redirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}

	# slashes cause errors, <> get stripped anyway later on. # causes problems.
	$badchars = array('#','>','<','\\'); 
	$searchword = trim(str_replace($badchars, '', JFactory::getApplication()->input->getString('searchword', null)));
	
	# if searchword enclosed in double quotes, strip quotes and do exact match
	if (substr($searchword,0,1) == '"' && substr($searchword, -1) == '"') { 
		$post['searchword'] = substr($searchword,1,-1);
	}
	else {
		$post['searchword'] = $searchword;
	}
	
	# limit searchword to 20 (configurable) characters
	$restriction = false;
	if ( JString::strlen( $searchword ) > $mtconf->get('limit_max_chars') ) {
		$searchword 	= JString::substr( $searchword, 0, ($mtconf->get('limit_max_chars')-1) );
		$restriction 	= true;
	}

	// searchword must contain a minimum of 3 (configurable) characters
	if ( $searchword && JString::strlen( $searchword ) < $mtconf->get('limit_min_chars') ) {
		$searchword 	= '';
		$restriction 	= true;
	}
	
	if($restriction)
	{
		$app->enqueueMessage(JText::sprintf('COM_MTREE_SEARCH_MESSAGE',$mtconf->get('limit_min_chars'),$mtconf->get('limit_max_chars')));
	}

	# Using Built in SEF feature in Joomla!
	if ( !isset($custom404) && $mtconf->getjconf('sef') ) {
		$searchword = urldecode($searchword);
	}

	# Search Category
	$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);
	
	# Redirect to category page if searchword is empty and a category is selected
	if(!empty($cat_id) && empty($searchword)) {
		$app->redirect( JRoute::_("index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=".$intItemid) );
	}
	
	$only_subcats_sql = '';
	if ( $cat_id > 0 ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $cat_id, true );
		$subcats[] = $cat_id;
		if ( !empty($subcats) ) {
			$only_subcats_sql = "\n AND cat.cat_id IN (" . implode( ", ", $subcats ) . ")";
		}
	}

	# Page Navigation
	$limitstart	= JFactory::getApplication()->input->getInt('limitstart', 0);
	if( $limitstart < 0 ) $limitstart = 0;
	
	$jdate = JFactory::getDate();
	$now = $jdate->toSql();
	
	$cats = array(0);
	
	# Construct WHERE
	$link_fields = array('link_name', 'link_desc', 'address', 'city', 'postcode', 'state', 'country', 'email', 'website', 'telephone', 'fax', 'metakey', 'metadesc', 'price' );

	$total = 0;
	$cats = array();

	if(!empty($searchword) || $searchword == '0') {
		$words = parse_words($searchword);
		
		foreach($words AS $key => $value) {
			$words[$key] = $database->escape( $value, true );
		}
		
		$database->setQuery("SELECT field_type,published,simple_search FROM #__mt_customfields WHERE iscore = 1");
		$searchable_core_fields = $database->loadObjectList('field_type');

		# Determine if there are custom fields that are simple searchable
		$database->setQuery("SELECT COUNT(*) FROM #__mt_customfields WHERE published = 1 AND simple_search = 1 AND iscore = 0");
		$searchable_custom_fields_count = $database->loadResult();
		
		$wheres0 = array();
		$wheres_cat = array();
		$wheres1 = array();
		foreach ($words as $word)
		{
			if( $post['search_cat'] == 1 )
			{
				$wheres_cat[] = "\nLOWER(cat.cat_name) LIKE '%$word%' OR LOWER(cat.cat_desc) LIKE '%$word%'";
			}

			foreach( $link_fields AS $lf ) {
				if ( 
					(substr($lf, 0, 5) == "link_" && array_key_exists('core'.substr($lf,5),$searchable_core_fields) && $searchable_core_fields['core'.substr($lf,5)]->published == 1 && $searchable_core_fields['core'.substr($lf,5)]->simple_search == 1)
					OR
					(array_key_exists('core'.$lf,$searchable_core_fields) && $searchable_core_fields['core'.$lf]->published == 1 && $searchable_core_fields['core'.$lf]->simple_search == 1)
				) {
					if(in_array($lf,array('metakey','metadesc','email'))) {
						$wheres0[] = "\n LOWER(l.$lf) LIKE '%$word%'";
					} else {
						$wheres0[] = "\n LOWER($lf) LIKE '%$word%'";
					}
				}
			}
			if($searchable_custom_fields_count > 0) {
				$wheres0[] = "\n" .' (cf.simple_search = 1 AND cf.published = 1 AND LOWER(cfv.value) LIKE \'%' . $word . '%\')';
			}
			$wheres1[] = "\n (" . implode( ' OR ', $wheres0 ) . ")";
			unset($wheres0);
		}
		$where = "(\n" . implode( "\nAND\n", $wheres1 ) . "\n)";
		$where_cat = '(' . implode( ') AND (', $wheres_cat ) . ')';

		# Retrieve categories
		if ( $limitstart == 0 && $post['search_cat'] == 1 ) {
			# Search Categories 
			$database->setQuery( "SELECT * FROM #__mt_cats AS cat" 
				.	"\n WHERE " . $where_cat
				.	"\n AND cat_published='1' AND cat_approved='1' "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			);
			$cats = $database->loadObjectList();
		}
		
		# Retrieve links
		$sql = 'SELECT ';
		if( !empty($searchable_custom_fields_count) ) {
			$sql .= 'DISTINCT ';
		}

		$sql .= 'l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cl.*, cat.*, u.username AS username, u.name AS owner, img.filename AS link_image FROM (#__mt_links AS l';
		if( !empty($searchable_custom_fields_count) ) {
			$sql .= ", #__mt_customfields AS cf";
		}
		$sql .= ")";
		if($searchable_custom_fields_count > 0) {
			$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = cf.cf_id";
		}
		$sql .=	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
			.	"\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 " 
			.	"\n LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id " 
			.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n	WHERE " 
			. 	"\n	link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 ";
		$sql .= "\n AND ".$where
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_search_order1') . ' ' . $mtconf->get('first_search_order2') . ', ' . $mtconf->get('second_search_order1') . ' ' . $mtconf->get('second_search_order2');
		}
	
		$sql .=	"\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_all');
		$database->setQuery( $sql );
		$links = $database->loadObjectList();

		# Get total
		$sql = "SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl";
			if($searchable_custom_fields_count > 0) {
				$sql .= ", #__mt_customfields AS cf";
			}
			$sql .= ")";
			if($searchable_custom_fields_count > 0) {
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = cf.cf_id";
			}
			$sql .=	"\n	LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id " 
				.	"\n	WHERE " 
				.	"link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
				.	"\n AND cl.link_id = l.link_id "
				.	"\n AND cl.main = 1 ";
			$sql .=	"\n AND ".$where
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
			$database->setQuery( $sql );

		$total = $database->loadResult();
	}

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_all'));

	# Set title
	if( empty($searchword) ) {
		setTitle(JText::_( 'COM_MTREE_PAGE_TITLE_SEARCH_RESULTS' ));
	} else {
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_SEARCH_RESULTS_FOR_KEYWORD', $searchword ));
	}

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

	$savant->assign('searchword', $searchword);
	$savant->assign('cat_id', $cat_id);
	$savant->assign('total_listing', $total);
	if ( $limitstart == 0 ) {
		$savant->assign('cats', $cats);	
		$savant->assign('categories', $cats);	
	}

	$savant->display( 'page_searchResults.tpl.php' );
}

/***
* Advanced Search
*/

function advsearch( $cat_id, $option ) {
	setTitle(JText::_( 'COM_MTREE_PAGE_TITLE_ADVANCED_SEARCH' ));
	advsearch_cache( $cat_id, $option );
}

function advsearch_cache( $cat_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database =& JFactory::getDBO();

	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mfields.class.php' );

	# Pathway
	$pathWay = new mtPathWay();

	# Get category's tree
	getCatsSelectlist( $cat_id, $cat_tree, 0 );

	if( !empty($cat_tree) ) {
		$cat_options[] = JHtml::_('select.option', $cat_id, '&nbsp;');
		foreach( $cat_tree AS $ct ) {
			$cat_options[] = JHtml::_('select.option', $ct["cat_id"], str_repeat("-",($ct["level"]*3)) .(($ct["level"]>0) ? "":''). $ct["cat_name"]);
		}
		$catlist = JHtml::_('select.genericlist', $cat_options, 'cat_id', '', 'value', 'text', '');
	}

	# Search condition
	$searchConditions[] = JHtml::_('select.option', 1, strtolower(JText::_( 'COM_MTREE_ANY' )));
	$searchConditions[] = JHtml::_('select.option', 2, strtolower(JText::_( 'COM_MTREE_ALL' )));
	$lists['searchcondition'] = JHtml::_('select.genericlist', $searchConditions, 'searchcondition', 'class="span2" size="1"', 'value', 'text', $mtconf->get('default_search_condition'));

	$cf_ids = getAssignedFieldsID($cat_id);

	# Load all CORE and custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, '".$cat_id."' AS cat_id FROM #__mt_customfields AS cf "
		.	"\nWHERE cf.published='1' && advanced_search = '1' "
		.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
		.	"\nORDER BY ordering ASC" );
	$fields = new mFields($database->loadObjectList());

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

	$total_fields = count($fields->fields);
	$hasCategoryCF = false;

	for($i=0;$i<$total_fields;$i++) {
		if( $fields->fields[$i]['fieldType'] == 'category' ) {
			$hasCategoryCF = true;
			break;
		}
	}

	if( !$hasCategoryCF ) {
		$savant->assignRef('catlist', $catlist);
	}

	$savant->assignRef('hasCategoryCF', $hasCategoryCF);
	$savant->assignRef('cat_id', $cat_id);
	$savant->assignRef('fields', $fields);
	$savant->assignRef('lists', $lists);
	$savant->display( 'page_advSearch.tpl.php' );

}

function listalpha( $cat_id, $alpha, $limitstart, $option ) {
	$database	=& JFactory::getDBO();
	
	$database->setQuery( 'SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1' );
	$cat_name = $database->loadResult();

	# Set title
	setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_LIST_ALPHA_BY_LISTINGS_AND_CATS', strtoupper($alpha), $cat_name ));

	listalpha_cache( $cat_id, $alpha, $limitstart, $option );
}

function listalpha_cache( $cat_id, $alpha, $limitstart, $option ) {
	global $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$nullDate	= $database->getNullDate();

	$where = array();
	
	# Number (0-9)
	if ( $alpha == '0' ) {
		for( $i=48; $i <= 57; $i++) {
			$cond_seq_link[] = "link_name LIKE '" . $database->escape( chr($i), true ) . "%'";
			$cond_seq_cat[] = "cat1.cat_name LIKE '" . $database->escape( chr($i), true ) . "%'";
		}
		$where[] = "(".implode(" OR ",$cond_seq_link).")";
		$where_cat[] = "(".implode(" OR ",$cond_seq_cat).")";

	# Alphabets (A-Z)
	} elseif ( 
		preg_match('/[a-z0-9]{1}[0-9]*/', $alpha) 
		OR 
		(
			$mtconf->get('alpha_index_additional_chars') <> '' 
			AND 
			!empty($alpha)
			AND
			JString::strpos(JString::strtolower($mtconf->get('alpha_index_additional_chars')),JString::strtolower($alpha)) !== false 
		)
	) {
		$where[] = "link_name LIKE '" . $database->escape( $alpha, true ) . "%' COLLATE utf8_swedish_ci ";
		$where_cat[] = "cat1.cat_name LIKE '" . $database->escape( $alpha, true ) . "%' COLLATE utf8_swedish_ci ";
	
	# Fall back to listlisting to show all listing, ordered alphabetically
	} else {
		$sort	= JFactory::getApplication()->input->getCmd('sort', $mtconf->get('all_listings_sort_by'));
		$my	=& JFactory::getUser();
		
		require_once( JPATH_SITE.'/components/com_mtree/listlisting.php');
		listlisting( $cat_id, $option, $my, 'listalpha', $sort, $limitstart );

		return;
	}

	if(!empty($where)) {
	
		# SQL condition to display category specific results
		$subcats = implode(", ",getSubCats_Recursive($cat_id));

		if ($subcats) $where[] = "cl.cat_id IN (" . $subcats . ")";
		if ($subcats) $where_cat[] = "cat1.cat_parent IN (" . $subcats . ")";

		// Get Total results - Links
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();

		$sql = "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) ";
		$where[] = "l.link_id = cl.link_id";
		$where[] = "cl.main = '1'";
		$where[] = "link_approved = '1'";
		$where[] = "link_published = '1'";
		$where[] = "( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  )";
		$where[] = "( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )";
	
		$sql .= (!empty( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");

		$database->setQuery( $sql );
		$total = $database->loadResult();

		// Get Links
		$link_sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cl.*, cat.*, u.username AS username, u.name AS owner, img.filename AS link_image FROM #__mt_links AS l"
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n LEFT JOIN #__mt_cats AS cat ON cl.cat_id = cat.cat_id "
			.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			.	"\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			.	"\n " . (!empty( $where ) ? " WHERE " . implode( ' AND ', $where ) : "")
			.	" AND l.link_id = cl.link_id AND cl.main = '1' ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$link_sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$link_sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		
		$link_sql .= "LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');

		# Shows categories if this is the first page. ie: $limitstart = 0
		$num_of_cats = 0;
		
		if ( $limitstart == 0 ) {
			
			$database->setQuery( "SELECT * FROM #__mt_cats AS cat1 WHERE "
				.	implode( ' AND ', $where_cat )	
				.	"AND cat_approved = '1' "
				.	"AND cat_published = '1' "
				.	($mtconf->getTemParam('onlyShowRootLevelCatInListalpha',0) ? "AND cat_parent = 0 " : "AND cat_parent >= 0 ")
				.	"ORDER BY cat_name ASC ");
			$categories = $database->loadObjectList();
			
			// Add parent category name to distinguish categories with same name
			$sql = 'SELECT DISTINCT cat1.cat_name FROM (#__mt_cats AS cat1, #__mt_cats AS cat2) ';
			$sql .= 'WHERE ' . implode( ' AND ', $where_cat ) . ' ';
			$sql .= 'AND cat1.cat_name = cat2.cat_name AND cat1.cat_id != cat2.cat_id ';
			$sql .= 'ORDER BY cat1.cat_name ASC';
			$database->setQuery( $sql );
			$same_name_cats = $database->loadColumn();
		
			if( !empty($same_name_cats) ) {
				$mtcat = new mtCats( $database );
				for( $i=0; $i<count($categories); $i++ ) {
					if( in_array( $categories[$i]->cat_name, $same_name_cats ) ) {
						if( $categories[$i]->cat_parent > 0 ) {
							$categories[$i]->cat_name .= ' (' . $mtcat->getName($categories[$i]->cat_parent) . ')';
						}
					}
				}
			}

		}

		# SQL - Links
		$database->setQuery( $link_sql );
		$links = $database->loadObjectList();

		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_links'));
	
		# Pathway
		$pathWay = new mtPathWay( 0 );

		# Load custom template
		loadCustomTemplate( $cat_id, $savantConf);

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

		if(!isset($categories)) {
			$savant->assign('categories', array());
		} else {
			$savant->assign('categories', $categories);
		}
		$savant->assign('alpha', urldecode($alpha));
		$savant->display( 'page_listAlpha.tpl.php' );
	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

function listlisting( $cat_id, $option, $my, $task, $sort, $limitstart ) {
	global $mtconf, $Itemid;

	$database	=& JFactory::getDBO();
	$document	=& JFactory::getDocument();
	$listListing	= new mtListListing( $task );
	$listListing->setLimitStart( $limitstart );
	
	if( !empty($sort) ) {
		$listListing->setSort( $sort );
	}
	
	if( $cat_id == 0 ) {
		$cat_name = JText::_( 'COM_MTREE_ROOT' );
	} else {
		$database->setQuery( 'SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1' );
		$cat_name = $database->loadResult();
	}

	JHtml::_('jquery.framework');

	if(in_array($task,array('listnew','listupdated')) && $mtconf->get('show_list' . substr($task,4) . 'rss') ) {
		$document->addCustomTag(
			'<link rel="alternate" type="application/rss+xml" title="' . $mtconf->getjconf('sitename') 
			. ' - ' 
			. (
				($task=='listnew')
				?
				JText::_( 'COM_MTREE_NEW_LISTING' )
				:
				JText::_( 'COM_MTREE_RECENTLY_UPDATED_LISTING' )
				) 
			. '" href="index.php?option=com_mtree&task=rss&type=' . substr($task,4) . '&Itemid=' . $Itemid . '" />'
		);
	}

	$cache = JFactory::getCache('com_mtree');
	$cache->call('listlisting_cache', $cat_id, $cat_name, $option, $listListing);
}

function listlisting_cache( $cat_id, $cat_name, $option, $listListing ) {
	global $savantConf, $Itemid, $mtconf;

	$database =& JFactory::getDBO();
	
	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mfields.class.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mAdvancedSearch.class.php' );

	$input = JFactory::getApplication()->input;
	$post = $_POST;
	$search_params	= $_REQUEST;

	if( !is_null($input->get('cfcat_id')) )
	{
		$cfcat_id = $input->getInt('cfcat_id');
		$search_params[$input->get('cfcat')] = $cfcat_id;
	} else {
		$cfcat_id	= 0;
	}
	
	$cf_ids = getAssignedFieldsID($cat_id);
	
	# Load all CORE and custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, '".$cat_id."' AS cat_id FROM #__mt_customfields AS cf "
		.	"\nWHERE cf.published='1' && (filter_search = '1' || advanced_search = '1')"
		.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
		.	" ORDER BY ordering ASC" 
		);
	$fields = new mFields($database->loadObjectList());
	$searchParams = $fields->loadSearchParams($search_params);
	$hasSearchParams = false;

	if( !empty($searchParams) || $cfcat_id > 0 )
	{
		$advsearch = new mAdvancedSearch( $database );
		if( !is_null($input->getInt('searchcondition')) && $input->getInt('searchcondition') == 1 ) {
				$advsearch->useOrOperator();
		} else {
			$advsearch->useAndOperator();
		}

		# Search Category
		if ( !is_null($input->get('cat_id')) ) {
			$search_cat = $input->getInt('cat_id');
		} else {
			$search_cat = 0;
		}

		if( $cfcat_id > 0 ) {
			$search_cat	= $cfcat_id;
		}

		$only_subcats_sql = '';

		if ( $search_cat > 0 && is_int($search_cat) )
		{
			// Use 'Category' type custom field value if specified.
			// Otherwise, it will use the current page's cat_id.
			if($cfcat_id > 0)
			{
				$search_cat = $cfcat_id;
			}
			$mtCats = new mtCats( $database );
			$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
			$subcats[] = $search_cat;
			if ( !empty($subcats) ) {
				$advsearch->limitToCategory( $subcats );
			}
		}

		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			$searchFields = $field->getSearchFields();

			if( isset($searchFields[0]) && isset($searchParams[$searchFields[0]]) && $searchParams[$searchFields[0]] != '' ) {

				foreach( $searchFields AS $searchField ) {
					if( isset($searchParams[$searchField]) )
					{
						$searchFieldValues[] = $searchParams[$searchField];
					}
				}
				if( !empty($searchFieldValues) && $searchFieldValues[0] != '' ) {
					if( is_array($searchFieldValues[0]) && empty($searchFieldValues[0][0]) ) {
						// Do nothing
					} else {
						$tmp_where_cond = call_user_func_array(array($field, 'getWhereCondition'),$searchFieldValues);
						if( !is_null($tmp_where_cond) ) {
							$advsearch->addCondition( $field, $searchFieldValues );
						} 
						if( !$hasSearchParams ) {
							$hasSearchParams = true;
						}
					}
				}
				unset($searchFieldValues);
			}

			$fields->next();
		}

		$limit		= JFactory::getApplication()->input->getInt('limit', $mtconf->get('fe_num_of_all'), 'get');
		$limitstart	= JFactory::getApplication()->input->getInt('limitstart', 0, 'get');
		if( $limitstart < 0 ) $limitstart = 0;
		
		$advsearch->setSort( $listListing->getSort() );
		$advsearch->search(1,1);

		// Total Results
		$total = $advsearch->getTotal();

		$links = $advsearch->loadResultList( $limitstart, $limit );

		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

	} // End of $searchParams
	else 
	{
		# Retrieve Links
		$listListing->setSubcats( getSubCats_Recursive($cat_id) );

		$listListing->prepareQuery();
		$links = $listListing->getListings();

		$pageNav = $listListing->getPageNav();
	}

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Get category object
	$database->setQuery( 'SELECT * FROM #__mt_cats '
		.	'WHERE cat_id=' . $database->quote($cat_id) . ' AND cat_published = 1 LIMIT 1' );
	$cat = $database->loadObject();

	# Savant template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );	
	
	$lists['sort'] = $listListing->getSortHTML();
	$savant->assign('lists', $lists);
	$savant->assignRef('filter_fields', $fields);
	$savant->assign('cat', $cat);
	$savant->assign('hasSearchParams', $hasSearchParams);

	$tlcat_id = getTopLevelCatID($cat_id);
	$savant->assign('tlcat_id', $tlcat_id);

	if( $hasSearchParams )
	{
		$savant->assign('header', JText::sprintf('COM_MTREE_LISTALL_SEARCH_RESULTS',$cat_name));

		# Set title
		setTitle( JText::sprintf('COM_MTREE_PAGE_TITLE_LISTALL_SEARCH_RESULTS', $cat_name), $cat_id );
	}
	else
	{
		$savant->assign('header', MText::sprintf('PAGE_HEADER_LIST_LISTINGS',$tlcat_id,$cat_name,MText::_($listListing->getHeaderLangKey(), $tlcat_id)));

		# Set title
		setTitle( MText::sprintf('PAGE_TITLE_LIST_LISTINGS', $tlcat_id, $cat_name, MText::_($listListing->getTitleLangKey(), $tlcat_id) ), $cat_id );
	}
	$savant->display( 'page_listListings.tpl.php' );
}

function getOwnerObject( $user_id )
{
	
	$db 	=& JFactory::getDBO();
	$db->setQuery( 'SELECT id, name, username, email FROM #__users WHERE id = ' . $db->quote($user_id) . ' LIMIT 1' );
	$owner = $db->loadObject();

	return $owner;
}

function getOwnerProfile( $user_id )
{
	// Profile Fields
	require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php';
	$owner_model = JModelLegacy::getInstance('User', 'UsersModel', array('ignore_request' => true));
	$owner_data = $owner_model->getItem((int)$user_id);

	JPluginHelper::importPlugin('user');
	$form = new JForm('com_users.profile');

	// Get the dispatcher.
	$dispatcher	= JDispatcher::getInstance();

	// Trigger the form preparation event.
	$dispatcher->trigger('onContentPrepareForm', array($form, $owner_data));

	// Trigger the data preparation event.
	$dispatcher->trigger('onContentPrepareData', array('com_users.profile', $owner_data));

	// Load the data into the form after the plugins have operated.
	$form->bind($owner_data);

	// Remove all description from these fields
	$all_profile_fields = $form->getFieldset('profile');

	foreach($all_profile_fields AS $field)
	{
		$form->setFieldAttribute(str_replace(array('profile_'),'',$field->id),'description','','profile');
	}
	
	return $form->getFieldset('profile');
}

function viewuserslisting( $user_id, $limitstart, $option ) {
	global $my;
	
	$database 	=& JFactory::getDBO();
	
	if( $user_id <= 0 )
	{
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=mypage') 
				)
			)
		);
	}

	# Get owner's info
	$owner = getOwnerObject($user_id);
	
	if( !empty($owner) ) {
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_LISTING_BY', $owner->username ) );
		viewuserslisting_cache( $owner, $limitstart, $option );
	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

function viewuserslisting_cache( $owner, $limitstart, $option ) {
	global $Itemid, $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();
	
	if ( $owner ) {
		
		if( $mtconf->get( 'display_pending_approval_listings_to_owners' ) && $my->id == $user_id ) {
			$show_approved_and_published_listings_only = false;
		} else {
			$show_approved_and_published_listings_only = true;
		}
		
		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' AND " : 'link_approved >= 0 AND ') 
			. "\n user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_links, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve Links
		$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.*, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			. "\n WHERE " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' AND " : 'link_approved >= 0 AND ') 
			. "\n user_id='".$user_id."' "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');
		$database->setQuery( $sql );
		$links = $database->loadObjectList();

		# Get total reviews
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		# Get total favourites
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = f.link_id"
			.	"\nWHERE f.user_id = '".$user_id."' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_favourites = $database->loadResult();
		
		# Get Owner Profile
		$user_profile_fields = getOwnerProfile($user_id);
		
		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('user_profile_fields', $user_profile_fields);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('total_favourites', $total_favourites);

		$savant->display( 'page_ownerListing.tpl.php' );

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

function viewusersreview( $user_id, $limitstart, $option ) {
	global $mtconf;

	$database	=& JFactory::getDBO();

	# Get owner's info
	$owner = getOwnerObject($user_id);
	
	if( count($owner) == 1 && $mtconf->get('show_review') ) {
		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_REVIEWS_BY', $owner->username ));
		viewusersreview_cache( $owner, $limitstart, $option );
	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}

}

function viewusersreview_cache( $owner, $limitstart, $option ) {
	global $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();

	if ( $owner ) {

		if( $mtconf->get( 'display_pending_approval_listings_to_owners' ) && $my->id == $user_id ) {
			$show_approved_and_published_listings_only = false;
		} else {
			$show_approved_and_published_listings_only = true;
		}

		$jdate = JFactory::getDate();
		$now = $jdate->toSql();

		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_reviews, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve reviews
		$database->setQuery( "SELECT r.*, l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cat.*, u.username, log.value AS rating, img.filename AS link_image FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nLEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = 1"
			.	"\n LEFT JOIN #__mt_cats AS cat ON cl.cat_id = cat.cat_id "
			.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id "
			.	"\nWHERE r.user_id = '".$user_id."' AND r.rev_approved = 1 AND l.link_published='1' AND link_approved='1'"
			.	"\nORDER BY r.rev_date DESC"
			.	"\nLIMIT $limitstart, " . $mtconf->get('fe_num_of_links')
			);
		$reviews = $database->loadObjectList();

		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# Get total links
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' " : 'link_approved >= 0 ') 
			. "\n AND user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		# Get total favourites
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = f.link_id"
			.	"\nWHERE f.user_id = '".$user_id."' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_favourites = $database->loadResult();

		# Get Owner Profile
		$user_profile_fields = getOwnerProfile($user_id);
		
		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $reviews, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('user_profile_fields', $user_profile_fields);
		$savant->assign('reviews', $reviews);
		$savant->assign('total_links', $total_links);
		$savant->assign('total_favourites', $total_favourites);

		$savant->display( 'page_usersReview.tpl.php' );

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

function viewusersfav( $user_id, $limitstart, $option ) {
	global $mtconf;

	$database	=& JFactory::getDBO();

	# Get owner's info
	$owner = getOwnerObject($user_id);
	
	if( count($owner) == 1 && $mtconf->get('show_favourite')) {
		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_FAVOURITES_BY', $owner->username ));
		viewusersfav_cache( $owner, $limitstart, $option );
	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}

}

function viewusersfav_cache( $owner, $limitstart, $option ) {
	global $Itemid, $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();

	if ( $owner ) {

		if( $mtconf->get( 'display_pending_approval_listings_to_owners' ) && $my->id == $user_id ) {
			$show_approved_and_published_listings_only = false;
		} else {
			$show_approved_and_published_listings_only = true;
		}

		# Page Navigation
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f "
			.	"\n LEFT JOIN #__mt_links AS l ON l.link_id = f.link_id "
			. "\n WHERE "
			. "\n	l.link_published='1' AND l.link_approved='1' AND f.user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_favourites = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_favourites, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve Links
		$sql = "SELECT DISTINCT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.*, img.filename AS link_image "
			. "FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_favourites AS f)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			. "\n WHERE link_published='1' AND link_approved='1' AND f.user_id='".$user_id."' AND f.link_id = l.link_id "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links') ;
		$database->setQuery( $sql );
		$links = $database->loadObjectList();
		
		# Get total reviews
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			. "\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		# Get total links
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' " : 'link_approved >= 0 ') 
			. "\n AND user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		# Get Owner Profile
		$user_profile_fields = getOwnerProfile($user_id);
		
		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('user_profile_fields', $user_profile_fields);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('total_links', $total_links);

		$savant->display( 'page_usersFavourites.tpl.php' );

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

/***
* Visit URL
*/

function visit( $link_id, $cf_id ) {
	global $mtconf;

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT website FROM #__mt_links"
		.	"\n	WHERE link_published='1' AND link_approved > 0 AND link_id='".$link_id."' " 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
	);

	$link = $database->loadObject();

	// Checks if the listing is an approved & published listing
	if (empty($link)) {
		
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {
		
		if( !empty($cf_id) ) {
			
			// Get custom field link
			$database->setQuery( 'SELECT value FROM #__mt_cfvalues WHERE cf_id = ' . $database->Quote($cf_id) . ' AND link_id = ' . $database->Quote($link_id) . ' LIMIT 1' );
			$url = $database->loadResult();

			// Update counter
			$database->setQuery( 'UPDATE #__mt_cfvalues SET counter = counter + 1 WHERE link_id = ' . $database->quote($link_id) . ' AND cf_id = ' . $database->quote($cf_id) . ' LIMIT 1');
			if (!$database->execute()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}
			
			if( !empty($url) ) {
				$app->redirect( 
					(
						substr($url,0,7) == 'http://' 
						|| 
						substr($url,0,8) == 'https://') 
						? 
						$url : 'http://'.$url 
					);
			} else {
				return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			}
			
		} else {
			if($mtconf->get('log_visit'))
			{
				$remote_addr = $_SERVER['REMOTE_ADDR'];
				$mtLog = new mtLog( $database, $remote_addr, $my->id, $link_id );
				$mtLog->logVisit();
			}

			# Update #__mt_links table
			$database->setQuery( 'UPDATE #__mt_links SET link_visited = link_visited + 1 WHERE link_id = ' . $database->quote($link_id) . ' LIMIT 1' );
			if (!$database->execute()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$app->redirect( (substr($link->website,0,7) == 'http://' || substr($link->website,0,8) == 'https://') ? $link->website : 'http://'.$link->website );
		}

	}

}

/***
* View Gallery
*/
function viewgallery( $link_id, $option ) {
	global $savantConf;
	
	$database 	=& JFactory::getDBO();
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );

	if($link === false)	{
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {
		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_GALLERY', $link->link_name ));
		
		$database->setQuery('SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $database->quote($link_id) . ' ORDER BY ordering ASC');
		$images = $database->loadObjectList();
		
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('images', $images);
		$savant->display( 'page_gallery.tpl.php' );
	}	
}

/***
* View Image
*/
function viewimage( $img_id, $option ) {
	global $savantConf;
	
	$database 	=& JFactory::getDBO();

	$database->setQuery('SELECT img_id, link_id, filename, ordering from #__mt_images WHERE img_id = ' . $database->quote($img_id) . ' LIMIT 1');
	$image = $database->loadObject();
	
	if(isset($image) && $image->link_id > 0) {
		$link = loadLink( $image->link_id, $savantConf, $fields, $params );
	} else {
		$link = false;
	}

	if($link === false)	{
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {
		$database->setQuery('SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $database->quote($image->link_id) . ' ORDER BY ordering ASC');
		$images = $database->loadObjectList();

		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_IMAGE', $link->link_name ));

		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('image', $image);
		$savant->assign('images', $images);
		$savant->display( 'page_image.tpl.php' );
	}	
}

/***
* View Reviews
*/
function viewreviews( $link_id, $limitstart, $option ) {
	global $savantConf, $mtconf;
	
	$database 	=& JFactory::getDBO();
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );
	$document	=& JFactory::getDocument();
	$my		=& JFactory::getUser();
	if($link === false)	{
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {
		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_VIEWREVIEWS', $link->link_name ));

		JHtml::_('jquery.framework');
		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js') . 'vote.js" type="text/javascript"></script>');
		
		# Predefine variables:
		$prevar = " <script type=\"text/javascript\"><!-- \n";
		$prevar .= "jQuery.noConflict();\n";
		$prevar .= "var mtoken=\"".JSession::getFormToken()."\";\n";
		$prevar .= "var JURI_ROOT=\"". JURI::root()."\";\n";
		$prevar .= "//--></script>";
		$document->addCustomTag($prevar);
		
		# Get reviews
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews AS r WHERE link_id = '".$link_id."' AND r.rev_approved = 1" );
		$total_reviews = $database->loadResult();
		
		$sql = "SELECT r.*, u.username, log.value AS rating FROM #__mt_reviews AS r"
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\n LEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\n WHERE r.link_id = '".$link_id."' AND r.rev_approved = 1 ";
		if( $mtconf->get('first_review_order1') != '' )
		{
			$sql .= "\n ORDER BY " . $mtconf->get('first_review_order1') . ' ' . $mtconf->get('first_review_order2') ;
			if( $mtconf->get('second_review_order1') != '' )
			{
				$sql .= ', ' . $mtconf->get('second_review_order1') . ' ' . $mtconf->get('second_review_order2');
				if( $mtconf->get('third_review_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('third_review_order1') . ' ' . $mtconf->get('third_review_order2');
				}
			}
		}
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_reviews');
		$database->setQuery( $sql );
		$reviews = $database->loadObjectList();
		
		# Add <br /> to all new lines & gather an array of review_ids
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# If the user is logged in, get all voted rev_ids
		if( $my->id > 0 ) {
			$database->setQuery( 'SELECT value, rev_id FROM #__mt_log WHERE log_type = \'votereview\' AND user_id = \''.$my->id.'\' AND link_id = \''.$link_id.'\' LIMIT '.$total_reviews );
			$voted_reviews = $database->loadObjectList( 'rev_id' );
		} else {
			$voted_reviews = array();
		}
		
		jimport('joomla.html.pagination');
		$reviewsNav = new JPagination($total_reviews, $limitstart, $mtconf->get('fe_num_of_reviews'));

		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('my', $my);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('voted_reviews', $voted_reviews);
		$savant->assign('reviews', $reviews);
		$savant->assign('reviewsNav', $reviewsNav);
		$savant->display( 'page_reviews.tpl.php' );
	}	
}
/***
* View Review
*/
function viewreview( $rev_id, $option ) {
	global $savantConf, $mtconf;
	
	$database 	=& JFactory::getDBO();
	$document	=& JFactory::getDocument();
	$my		=& JFactory::getUser();

	$sql = "SELECT r.*, u.username, log.value AS rating FROM #__mt_reviews AS r"
		.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\n LEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
		.	"\n WHERE r.rev_id = '".$rev_id."' AND r.rev_approved = 1 "
		.	"\n LIMIT 1";
	$database->setQuery( $sql );
	$reviews = $database->loadObjectList();

	if(empty($reviews)) {
		return JError::raiseError(404,JText::_('COM_MTREE_ERROR_REVIEW_NOT_FOUND'));
	} else {
		$link_id	= $reviews[0]->link_id;
		$link 		= loadLink( $link_id, $savantConf, $fields, $params );
		
		# Set title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_VIEWREVIEW', $reviews[0]->username, $link->link_name ));

		JHtml::_('jquery.framework');
		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js') . 'vote.js" type="text/javascript"></script>');
		
		# Predefine variables:
		$prevar = " <script type=\"text/javascript\"><!-- \n";
		$prevar .= "jQuery.noConflict();\n";
		$prevar .= "var mtoken=\"".JSession::getFormToken()."\";\n";
		$prevar .= "var JURI_ROOT=\"". JURI::root()."\";\n";
		$prevar .= "//--></script>";
		$document->addCustomTag($prevar);
		
		# Get reviews
		$total_reviews = 1;
		
		$sql = "SELECT r.*, u.username, log.value AS rating FROM #__mt_reviews AS r"
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\n LEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\n WHERE r.rev_id = '".$rev_id."' AND r.rev_approved = 1 ";
		$database->setQuery( $sql );
		$reviews = $database->loadObjectList();
		
		# Add <br /> to all new lines & gather an array of review_ids
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# If the user is logged in, get all voted rev_ids
		if( $my->id > 0 ) {
			$database->setQuery( 'SELECT value, rev_id FROM #__mt_log WHERE log_type = \'votereview\' AND user_id = \''.$my->id.'\' AND link_id = \''.$link_id.'\' LIMIT '.$total_reviews );
			$voted_reviews = $database->loadObjectList( 'rev_id' );
		} else {
			$voted_reviews = array();
		}
		
		jimport('joomla.html.pagination');
		$reviewsNav = new JPagination($total_reviews, 0, 1);

		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('my', $my);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('voted_reviews', $voted_reviews);
		$savant->assign('reviews', $reviews);
		$savant->assign('reviewsNav', $reviewsNav);
		$savant->display( 'page_review.tpl.php' );
	}
}

/***
* View Listing
*/
function viewlink( $link_id, $my, $limitstart, $option ) {
	global $savantConf, $mtconf;

	$app		= JFactory::getApplication('site');
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );
	$document	=& JFactory::getDocument();
	
	if($link === false)	{
		if( $mtconf->get('unpublished_message_cfid') > 0 )
		{
			$database =& JFactory::getDBO();
			$database->setQuery( 
				'SELECT l.*, u.username AS username FROM #__mt_links AS l '
				. 'LEFT JOIN #__users AS u ON u.id = l.user_id '
				. 'WHERE link_id = ' 
				. $database->quote($link_id) 
				. ' LIMIT 1' );
			$link = $database->loadObject();
			if( $link->link_published == 0 )
			{
				$database->setQuery( 
					'SELECT value FROM #__mt_cfvalues WHERE link_id = ' . $database->quote($link_id) 
					. ' AND cf_id = ' . $database->quote($mtconf->get('unpublished_message_cfid'))
					. ' LIMIT 1' );
				$unpublished_message = $database->loadResult();

				if( !empty($unpublished_message) ) {

					$params = new JRegistry( $link->attribs );
					$savant = new Savant2($savantConf);
					$fields = loadFields($link);

					assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
					
					$unpublished_message_cf = $fields->getFieldById($mtconf->get('unpublished_message_cfid'));
					$unpublished_message = $unpublished_message_cf->getOutput();
					
					$savant->assign('error_msg', JText::sprintf( 'COM_MTREE_THIS_LISTING_HAS_BEEN_UNPUBLISHED_FOR_THE_FOLLOWING_REASON', $unpublished_message ));
					$savant->assign('my', $my);
					$savant->display( 'page_errorListing.tpl.php' );
					
				} else {
					JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
				}
			} else {
				JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
			}
		}
		else
		{
			JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
		}
	} else {
	
		$uri = JUri::getInstance();

		# Set Page Title
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_VIEWLINK', $link->link_name ), null, $link_id);

		# Add META tags
		if ($mtconf->getjconf('MetaTitle')=='1') {
			$document->setMetadata( 'title', htmlspecialchars($link->link_name));
		}
		if ($mtconf->getjconf('MetaAuthor')=='1') {
			$document->setMetadata( 'author' , htmlspecialchars($link->owner) );
		}

		if ( !empty($link->metadesc) )
		{
			$document->setDescription( htmlspecialchars($link->metadesc) );
		}
		elseif( !empty($link->link_desc) )
		{
			$metadesc_maxlength = 300;
			
			// Get the first 300 characters
			$metadesc = JString::trim(strip_tags($link->link_desc));
			$metadesc = JString::str_ireplace("\r\n","",$metadesc);
			$metadesc = JString::substr($metadesc,0,$metadesc_maxlength);
			
			// Make sure the meta description is complete and is not truncated in the middle of a sentence.
			if( JString::strlen($link->link_desc) > $metadesc_maxlength && substr($metadesc,-1,1) != '.') {
				if( strrpos($metadesc,'.') !== false )
				{
					$metadesc = JString::substr($metadesc,0,JString::strrpos($metadesc,'.')+1);
				}
			}
			$document->setDescription( htmlspecialchars($metadesc) );
		}
		
		if ($link->metakey <> '') $document->setMetaData( 'keywords', $link->metakey );
		
		# Open Graph Protocol
		if( $mtconf->get('use_open_graph_protocol') )
		{
			$document->addCustomTag( '<meta property="og:title" content="'.htmlspecialchars($link->link_name).'"/>' );
			$document->addCustomTag( '<meta property="og:description" content="'.$document->getDescription().'"/>' );
			$document->addCustomTag( 
				'<meta property="og:url" content="' . 
				$uri->toString(array( 'scheme', 'host', 'port' )) . 
				JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id) . 
				'"/>'
			);
			$document->addCustomTag( 
				'<meta property="og:image" content="' .
				$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_listing_small_image') .
				$link->link_image .
				'"/>' 
			);
		}
		
		JHtml::_('jquery.framework');
		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js') . 'vote.js" type="text/javascript"></script>');
		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js') . 'jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>');

		# Predefine variables:
		$prevar = " <script type=\"text/javascript\"><!-- \n";
		$prevar .= "jQuery.noConflict();\n";
		$prevar .= "var mtoken=\"".JSession::getFormToken()."\";\n";
		$prevar .= "var JURI_ROOT=\"". JURI::root()."\";\n";
		$prevar .= "var ratingImagePath=\"".$mtconf->get('relative_path_to_rating_image')."\";\n";
		$prevar .= "var langRateThisListing=\"" . JText::_( 'COM_MTREE_RATE_THIS_LISTING' , true) . "\";\n";
		$prevar .= "var ratingText=new Array();\n";
		$prevar .= "ratingText[5]=\"" . JText::_( 'COM_MTREE_RATING_5', true) . "\";\n";
		$prevar .= "ratingText[4]=\"" . JText::_( 'COM_MTREE_RATING_4', true) . "\";\n";
		$prevar .= "ratingText[3]=\"" . JText::_( 'COM_MTREE_RATING_3', true) . "\";\n";
		$prevar .= "ratingText[2]=\"" . JText::_( 'COM_MTREE_RATING_2', true) . "\";\n";
		$prevar .= "ratingText[1]=\"" . JText::_( 'COM_MTREE_RATING_1', true) . "\";\n";
		$prevar .= "//--></script>";
		$document->addCustomTag($prevar);
		
		if( !empty($my->id) && !$mtconf->get('cache_registered_viewlink') ) {
			viewlink_cache( $link, $limitstart, $fields, $params, $my, $option );
		} else {
			$cache = &JFactory::getCache('com_mtree');
			$cache->call( 'viewlink_cache', $link, $limitstart, $fields, $params, $my, $option );
		}
	}
}

function viewlink_cache( $link, $limitstart, $fields, $params, $my, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$link_id	= $link->link_id;
	
	if ( !isset($link->link_id) || $link->link_id <= 0 ) {
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
	} else {

		# Increase 1 hit
		$cookiename = "mtlink_hit$link->link_id";
		$visited = JFactory::getApplication()->input->getInt( $cookiename, '0', 'COOKIE' );
		
		if (!$visited) {
			$database->setQuery( "UPDATE #__mt_links SET link_hits=link_hits+1 WHERE link_id='".$link_id."'" );
			$database->execute();
		}

		setcookie( $cookiename, '1', time()+$mtconf->get('hit_lag') );
	
		# Get reviews
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews AS r WHERE link_id = '".$link_id."' AND r.rev_approved = 1" );
		$total_reviews = $database->loadResult();
		
		$sql = "SELECT r.*, u.username, log.value AS rating FROM #__mt_reviews AS r"
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\n LEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\n WHERE r.link_id = '".$link_id."' AND r.rev_approved = 1 ";
		if( $mtconf->get('first_review_order1') != '' )
		{
			$sql .= "\n ORDER BY " . $mtconf->get('first_review_order1') . ' ' . $mtconf->get('first_review_order2') ;
			if( $mtconf->get('second_review_order1') != '' )
			{
				$sql .= ', ' . $mtconf->get('second_review_order1') . ' ' . $mtconf->get('second_review_order2');
				if( $mtconf->get('third_review_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('third_review_order1') . ' ' . $mtconf->get('third_review_order2');
				}
			}
		}
		$sql .= "\n LIMIT " . $mtconf->get('fe_num_of_reviews_in_listing_page');
		$database->setQuery( $sql );
		$reviews = $database->loadObjectList();

		# Add <br /> to all new lines & gather an array of review_ids
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# If the user is logged in, get all voted rev_ids
		if( $my->id > 0 ) {
			$database->setQuery( 'SELECT value, rev_id FROM #__mt_log WHERE log_type = \'votereview\' AND user_id = \''.$my->id.'\' AND link_id = \''.$link_id.'\' LIMIT '.$total_reviews );
			$voted_reviews = $database->loadObjectList( 'rev_id' );
		} else {
			$voted_reviews = array();
		}
		# Get image ids
		$database->setQuery("SELECT img_id AS id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC");
		$images = $database->loadObjectList();

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Load Parameters
		$params = new JRegistry( $link->attribs );
		$mtconf->set('show_rating',$params->def( 'show_rating', $mtconf->get('show_rating') ));
		$mtconf->set('show_review',$params->def( 'show_review', $mtconf->get('show_review') ));
		
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

		$savant->assign('my', $my);
		$savant->assign('reviews', $reviews);
		$savant->assign('images', $images);
		$savant->assign('voted_reviews', $voted_reviews);
		$savant->assign('total_reviews', ((isset($total_reviews)) ? $total_reviews : 0 ));
		$savant->assign('user_report_review',$mtconf->get('user_report_review'));
		
		if( $my->id > 0 && $mtconf->get('user_vote_review') == 1 ) {
			$savant->assign('show_review_voting', 1);
		} else {
			$savant->assign('show_review_voting', 0);
		}

		# Load associated listings
		$arrGetAssociatedListings = getAssociatedListings( $mtconf->getCategory(), $link->link_id, $limitstart );
		if( !is_null($arrGetAssociatedListings) && is_array($arrGetAssociatedListings) )
		{
			list( $links, $savant->links_fields, $savant->reviews_count, $pageNav ) = $arrGetAssociatedListings;
			
			$savant->assign('links_fields', $savant->links_fields);
			$savant->assign('reviews_count', $savant->reviews_count);
			$savant->assign('pageNav', $pageNav);
			$savant->assign('links', $links);
		}
		
		# Load Contact Owner Form
		if( $mtconf->get('contact_form_location') == 2 )
		{
			loadContactOwnerForm( $link->link_id, $savant );
		}
		
		$savant->assign('profilepicture_url', '');
		$savant->assign('user_profile_fields', array());
		
		# Load User Profile
		if( $mtconf->get('show_user_profile_in_listing_details') == 1 )
		{
			jimport( 'joomla.application.module.helper' );
			if( JPluginHelper::isEnabled( 'user', 'profilepicture' ) )
			{
				jimport('mosets.profilepicture.profilepicture');
				
				$savant->assign('profilepicture_url', '');
				
				// Profile Picture
				$profilepicture = new ProfilePicture($link->user_id);

				if( $profilepicture->exists() )
				{

					$savant->assign('profilepicture_url', $profilepicture->getURL(PROFILEPICTURE_SIZE_200));
				}
				else
				{
					$savant->assign('profilepicture_url', $profilepicture->getFillerURL(PROFILEPICTURE_SIZE_200));
				}
			}

			# User Profile Fields
			$savant->assign('user_profile_fields', getOwnerProfile($link->user_id));
		}
		
		# Display the page
		$savant->display( 'page_listing.tpl.php' );

	}
}

function getAssociatedListings($cat_id, $link_id, $limitstart) {
	$db =& JFactory::getDBO();

	if( !is_numeric($cat_id) ) {
		return null;
	}

	$db->setQuery('SELECT cat_id FROM #__mt_cats WHERE cat_association = ' . $cat_id . ' LIMIT 1');
	$cat_id2 = $db->loadResult();

	if( $cat_id2 > 0 )
	{
		$cat_id2_object = new mtCats( $db );
		$cat_id2_object->load($cat_id2);

		$db->setQuery( 
			'SELECT link_id2 '
			. ' FROM #__mt_links_associations AS lmap ' 
			. ' LEFT JOIN #__mt_cl AS cl ON cl.link_id = lmap.link_id2 AND cl.main = 1 '
			. ' LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id '
			. ' WHERE link_id1 = ' . $link_id 
			. ' AND cat.lft >= ' . $cat_id2_object->lft
			. ' AND cat.rgt <= ' . $cat_id2_object->rgt
			);
		$results = $db->loadObjectList();

		require_once( JPATH_SITE.'/components/com_mtree/listlisting.php');
		$listListing	= new mtListListing( 'listassociated' );
		$listListing->setLimitStart( $limitstart );

		# Retrieve Links
		$listListing->setSubcats( getSubCats_Recursive($cat_id2) );
		$listListing->setLinkId( $link_id );
		$listListing->prepareQuery();

		$links = $listListing->getListings();

		# Savant Template
		global $savantConf;
		$savant = new Savant2($savantConf);
		$pageNav = $listListing->getPageNav();
		assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );

		return array( $links, $savant->links_fields, $savant->reviews_count, $pageNav );
		
	} else {
		return null;
	}
}

function printlink( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );

	if (empty($link)) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {

		if ( file_exists( $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $mtconf->get('template') . '/print.css' ) ) {
			$document = JFactory::getDocument();
			$document->addCustomTag("<link href=\"". $mtconf->getjconf('live_site') ."/components/com_mtree/templates/".$mtconf->get('template')."/print.css\" rel=\"stylesheet\" type=\"text/css\"/>");
		}

		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_PRINT', $link->link_name ) );

		# Get image ids
		$database->setQuery("SELECT img_id AS id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC");
		$images = $database->loadObjectList();

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('images', $images);

		$savant->display( 'page_print.tpl.php' );
	}
}

/***
* Report Listing
*/
function report( $link_id, $option ) {
	global $savantConf;

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	
	if($link === false) {
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
	} else {
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_REPORT', $link->link_name ) );
		report_cache( $link, $fields, $params, $option );
	}
}

function report_cache( $link, $fields, $params, $option ) {
	global $savantConf, $mtconf;

	$my			=& JFactory::getUser();

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( empty($link) || $mtconf->get('user_report') == '-1' ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} elseif ( $mtconf->get('user_report') == 1 && $my->id < 1 ) {
		# User is not logged in
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=report&link_id='.$link->link_id) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_REPORT'
			)
		);
	} else {
		$user_fields_data = (array) JFactory::getApplication()->getUserState(
			'com_mtree.report.link_id'.$link->link_id.'.data', 
			array(
				'your_name'	=> '',
				'message'	=> '',
				'report_type'	=> ''
			)
		);

		$report_types = array(
			"1"=>JText::_( 'COM_MTREE_REPORT_PROBLEM_1' ), 
			"2"=>JText::_( 'COM_MTREE_REPORT_PROBLEM_2' ), 
			"3"=>JText::_( 'COM_MTREE_REPORT_PROBLEM_3' ), 
			"4"=>JText::_( 'COM_MTREE_REPORT_PROBLEM_4' )
			);

		// Generate CAPTCHA
		$captcha_html = '';
		if( $mtconf->get('use_captcha_report') )
		{
			$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

			if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => 'xnamespace'))) !== null)
			{
				$captcha_html .= $captcha->display('captcha', 'captcha'); 
			}				
		}
		
		$savant->assign('captcha_html', $captcha_html); 
		$savant->assign('report_types', $report_types);
		$savant->assign('user_fields_data', $user_fields_data);
		$savant->display( 'page_report.tpl.php' );
	}

}

function send_report( $link_id, $option ) {
	global $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();

	if ( $mtconf->get('show_report') == 0 ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} elseif ( $mtconf->get('user_report') == '-1' || ($mtconf->get('user_report') == 1 && $my->id  < 1) ) {
		# User is not logged in
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$user_fields_data['your_name']		= JFactory::getApplication()->input->getString('your_name', '');
		$user_fields_data['report_type']	= JFactory::getApplication()->input->getInt('report_type', 0);
		$user_fields_data['message']		= JFactory::getApplication()->input->get( 'message', '');
		$captcha_answer                 	= JFactory::getApplication()->input->getString( 'recaptcha_response_field', ''); 
		$report_type2				= "COM_MTREE_REPORT_PROBLEM_".$user_fields_data['report_type'];
		
		$app->setUserState('com_mtree.report.link_id'.$link_id.'.data', $user_fields_data);
		
		// Validate Captcha
		if( $mtconf->get('use_captcha_report') )
		{
			$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			$captcha = JCaptcha::getInstance($plugin);

			// Test the value.
			if (!$captcha->checkAnswer($captcha_answer))
			{
				$app->redirect( JRoute::_("index.php?option=$option&task=report&link_id=$link_id&Itemid=$Itemid"), $captcha->getError() );
			}
		}

		$uri = JUri::getInstance();
		$text = JText::sprintf( 
			'COM_MTREE_REPORT_EMAIL', 
			$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid",false), 
			$link->link_name,
			JText::_( $report_type2 ), 
			$link_id, 
			$user_fields_data['message']);

		$subject = JText::_( 'COM_MTREE_REPORT' )." - ".$mtconf->getjconf('sitename');

		if  (!validateInputs( '', $subject, $text ) ) {
			$document =& JFactory::getDocument();
			JError::raiseWarning( 0, $document->getError() );
			return false;
		}

		if( mosMailToAdmin( $subject, $text ) )
		{
			if( $my->id > 0 )  {
				# User is logged on, store user ID
				$database->setQuery( "INSERT INTO #__mt_reports "
					.	"( `link_id` , `user_id` , `subject` , `comment`, created ) "
					.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote( JText::_($report_type2) ) . ', ' . $database->quote($user_fields_data['message']) . ', ' . $database->quote($now) . ')');

			} else {
				# User is not logged on, store Guest name
				$database->setQuery( "INSERT INTO #__mt_reports "
					.	"( `link_id` , `guest_name` , `subject` , `comment`, created ) "
					.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($user_fields_data['your_name']) . ', ' . $database->quote( JText::_( $report_type2 ) ) . ', ' . $database->quote($user_fields_data['message']) . ', ' . $database->quote($now) . ')');

			}

			if (!$database->execute()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$app->setUserState('com_mtree.report.link_id'.$link_id.'.data', null);

			$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REPORT_HAVE_BEEN_SENT' ) );
		}
	}

}

/***
* Claim Listing
*/
function claim( $link_id, $option ) {
	global $savantConf;

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	
	if($link === false) {
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
	} else {
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_CLAIM', $link->link_name ) );
		claim_cache( $link, $fields, $params, $option );
	}
}

function claim_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my			=& JFactory::getUser();
	$page = 0;
	$jdate = JFactory::getDate();
	$now = $jdate->toSql();

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( $my->id <= 0 ) {
		
		# User is not logged in
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=claim&link_id='.$link->link_id) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_CLAIM'
			)
		);
	} elseif( $mtconf->get('show_claim') == 0 || empty($link) ) {
		
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {

		$savant->display( 'page_claim.tpl.php' );

	}
}

function send_claim( $link_id, $option ) {
	global $Itemid, $mtconf;
	
	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
	$my 		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	
	if ( $mtconf->get('show_claim') == 0 || $my->id <= 0 ) {
		
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {
		$database 	=& JFactory::getDBO();
		$my			=& JFactory::getUser();

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$message = JFactory::getApplication()->input->get( 'message', '', 'string');
		
		$uri = JUri::getInstance();
		$text = JText::sprintf( 'COM_MTREE_CLAIM_EMAIL', $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $link->link_name, $link_id, $message );

		$subject = JText::_( 'COM_MTREE_CLAIM' ) . ' - ' . $mtconf->getjconf('sitename');

		if( mosMailToAdmin( $subject, stripslashes ($text) ) )
		{
			# User is logged on, store user ID
			$database->setQuery( "INSERT INTO #__mt_claims "
				.	"( `link_id` , `user_id` , `comment`, `created` ) "
				.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

			if (!$database->execute()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_CLAIM_HAVE_BEEN_SENT' ) );
		}
	}

}

/***
* Delete Listing
*/
function deletelisting( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if ($mtconf->get('user_allowdelete') && $my->id == $link->user_id && $my->id > 0 ) {

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

		$savant->display( 'page_confirmDelete.tpl.php' );

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}

}

function confirmdelete( $link_id, $option ) {
	global $mtconf, $Itemid;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		. "\n link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	if ($mtconf->get('user_allowdelete') && $my->id == $link->user_id && $my->id > 0 ) {
		
		$link = new mtLinks( $database );
		$link->load( $link_id );
		
		if ( $mtconf->get('notifyadmin_delete') == 1 ) {

			// Get owner's email
			$database->setQuery( "SELECT email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
			$my_email = $database->loadResult();

			$subject = JText::_( 'COM_MTREE_ADMIN_NOTIFY_DELETE_SUBJECT' );
			$body = JText::sprintf( 'COM_MTREE_ADMIN_NOTIFY_DELETE_MSG', $link->link_name, $link->link_name, $link->link_id, $my->username, $my_email, $link->link_created );

			mosMailToAdmin( $subject, $body );
			
		}
		
		$link->updateLinkCount( -1 );
		$link->delLink();

		$cache = &JFactory::getCache('com_mtree');
		$cache->clean();

		$app->redirect( JRoute::_("index.php?option=$option&task=viewowner&user_id=".$my->id."&Itemid=$Itemid"), JText::_( 'COM_MTREE_LISTING_HAVE_BEEN_DELETED' ) );

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}

}

/***
* Review
*/
function writereview( $link_id, $option ) {
	global $savantConf;


	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if($link === false) {
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
	} else {
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_REVIEW', $link->link_name ) );
		writereview_cache( $link, $fields, $params, $option );
	}
}

function writereview_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();

	$user_fields_data = (array) JFactory::getApplication()->getUserState(
		'com_mtree.addreview.link_id'.$link->link_id.'.data', 
		array(
			'rev_title'	=> '',
			'rev_text'	=> '',
			'guest_name'	=> '',
			'rating'	=> ''
		)
	);

	if (empty($link) || $mtconf->get('show_review') == 0) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

		$savant->assign('user_fields_data', $user_fields_data);

		$rating_options = array(
			""=>JText::_( 'COM_MTREE_SELECT_YOUR_RATING' ), 
			"5"=>JText::_( 'COM_MTREE_RATING_5' ), 
			"4"=>JText::_( 'COM_MTREE_RATING_4' ), 
			"3"=>JText::_( 'COM_MTREE_RATING_3' ), 
			"2"=>JText::_( 'COM_MTREE_RATING_2' ), 
			"1"=>JText::_( 'COM_MTREE_RATING_1' )
			);
		
		$savant->assign('rating_options', $rating_options);
		
		$user_rev = null;
		$user_rating = 0;
		if( $my->id > 0 ) {

			# Check if this user has reviewed this listing previously
			$database->setQuery( "SELECT rev_id FROM #__mt_reviews WHERE link_id = '".$link->link_id."' AND user_id = '".$my->id."'" );
			$user_rev = $database->loadObjectList();

			# Check if this user has voted for this listing previously
			$database->setQuery( "SELECT value FROM #__mt_log WHERE link_id = '".$link->link_id."' AND user_id = '".$my->id."' AND log_type = 'vote' LIMIT 1" );
			$user_rating = $database->loadResult();
		}

		if ( count($user_rev) > 0 &&  $mtconf->get('user_review_once') == '1')
		{
			# This user has already reviewed this listing
			$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_CAN_ONLY_REVIEW_ONCE' ));
			$savant->display( 'page_errorListing.tpl.php' );
		}
		elseif
		( 
			$my->id < 1
			&&
			(
				$mtconf->get('user_review') == 1 
				||
				$mtconf->get('user_review') == 2
			)
			) {
			# User is not logged in
			JFactory::getApplication('site')->redirect(
				JRoute::_(
					'index.php?option=com_users&view=login&return='
					. base64_encode( 
						JRoute::_('index.php?option=com_mtree&task=writereview&link_id='.$link->link_id) 
					)
				),
				JText::sprintf( 
					'COM_MTREE_PLEASE_LOGIN_BEFORE_REVIEW'
				)
			);
		} elseif( $mtconf->get('user_review') == 2 && $my->id == $link->user_id ) {
			# Owner is trying to review own listing
			$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_RE_NOT_ALLOWED_TO_REVIEW_OWN_LISTING' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} elseif ( $mtconf->get('user_review') == -1 ) {
			# Display error when user_review is set to None (-1)
			$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_ARE_NOT_ALLOWED_TO_REVIEW' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} else {
			# OK. User is allowed to review
			
			// Generate CAPTCHA
			$captcha_html = '';
			if( $mtconf->get('use_captcha_review') )
			{
				$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

				if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => 'xnamespace'))) !== null)
				{
					$captcha_html .= $captcha->display('captcha', 'captcha'); 
				}				
			}

			$savant->assign('user_rating', (($user_rating>0)?$user_rating:0));
			$savant->assign('captcha_html', $captcha_html);
			$savant->display( 'page_writeReview.tpl.php' );

		}

	}
}

function addreview( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	# Get the review data
	$user_fields_data['rev_text']	= JFactory::getApplication()->input->getString( 'rev_text', '');
	$user_fields_data['rev_title'] 	= JFactory::getApplication()->input->getString( 'rev_title', '');
	$user_fields_data['guest_name']	= JFactory::getApplication()->input->getString( 'guest_name', '');
	$captcha_answer			= JFactory::getApplication()->input->getString( 'recaptcha_response_field', '');
	$remote_addr			= $_SERVER['REMOTE_ADDR'];

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if( 
		$mtconf->get('user_rating') == '-1' 
		|| 
		($mtconf->get('user_rating') == 1 && $my->id <= 0) 
		||
		($mtconf->get('user_rating') == 2 && $my->id > 0 && $my->id == $link->user_id )
		||
		$mtconf->get('allow_rating_during_review') == 0
	) {
		$rating = 0;
	}
	else
	{
		$user_fields_data['rating'] = JFactory::getApplication()->input->getInt('rating', 0);
		
	}

	$app->setUserState('com_mtree.addreview.link_id'.$link_id.'.data', $user_fields_data);

	$user_rev = array();
	if( $my->id > 0 ) {
		# Check if this user has reviewed this listing previously
		$database->setQuery( 'SELECT rev_id FROM #__mt_reviews WHERE link_id = ' . $database->quote($link->link_id) . ' AND user_id = ' . $database->quote($my->id) . ' LIMIT 1' );
		$user_rev = $database->loadObjectList();
	} elseif ( $my->id == 0 && $mtconf->get('user_review') == 0 ) {
		# Check log if this user's IP has been used to review this listing before
		$database->setQuery( 'SELECT rev_id FROM #__mt_log WHERE link_id = ' . $database->quote($link->link_id) . ' AND log_ip = ' . $database->quote($remote_addr) . ' AND log_type = \'review\' LIMIT 1' );
		$user_rev = $database->loadObjectList();
	}

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	// Validate Captcha
	if( $mtconf->get('use_captcha_review') )
	{
		$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
		$captcha = JCaptcha::getInstance($plugin);

		// Test the value.
		if (!$captcha->checkAnswer($captcha_answer))
		{
			$app->redirect( JRoute::_("index.php?option=$option&task=writereview&link_id=$link_id&Itemid=$Itemid"), $captcha->getError() );
		}
	}
	
	// Validate Inputs
	if ( !validateInputs( '', $user_fields_data['rev_title'], $user_fields_data['rev_text'] ) )
	{
		$document =& JFactory::getDocument();
		JError::raiseWarning( 0, $document->getError() );
		return false;
	}
	
	if ( count($user_rev) > 0 &&  $mtconf->get('user_review_once') == '1') {
		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );
		
		# This user has already reviewed this listing
		$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_CAN_ONLY_REVIEW_ONCE' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} elseif( $mtconf->get('user_review') == 2 && $my->id == $link->user_id ) {
		# Owner is trying to review own listing
		$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_RE_NOT_ALLOWED_TO_REVIEW_OWN_LISTING' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} elseif (empty($link) || $mtconf->get('show_review') == 0) {
		# Link does not exists, is not published or Show Review is disabled
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} elseif ( 
		$mtconf->get('user_review') == '-1' 
		|| 
		($mtconf->get('user_review') == 1 && $my->id  < 1) 
		|| 
		($mtconf->get('user_review') == 2 && $my->id  > 0 && $my->id == $link->user_id) 
		) {
		# Not accepting review / User is not logged in / Listing owners are not allowed to review
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} elseif ( $user_fields_data['rev_text'] == '' ) {
		# Review text is empty
		echo "<script> alert('".JText::_( 'COM_MTREE_PLEASE_FILL_IN_REVIEW' )."'); window.history.go(-1); </script>\n";
		exit();
		
	} elseif ( $user_fields_data['rev_title'] == '' ) {
		# Review title is empty
		echo "<script> alert('".JText::_( 'COM_MTREE_PLEASE_FILL_IN_TITLE' )."'); window.history.go(-1); </script>\n";
		exit();
		
	} elseif ( 
		$user_fields_data['rating'] == 0 
		&&
		$mtconf->get('require_rating_with_review') 
		&&
		$mtconf->get('allow_rating_during_review') 
		&&
		(
			$mtconf->get('user_rating') == '0'
			||
			($mtconf->get('user_rating') == '1' && $my->id > 0)
			||
			($mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id != $link->user_id)
		)
	) {
		# No rating given
		echo "<script> alert('".JText::_( 'COM_MTREE_PLEASE_FILL_IN_RATING' )."'); window.history.go(-1); </script>\n";
		exit();

	} else {
		# Everything is ok, add the review
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();
		
		if ( $mtconf->get('needapproval_addreview') == 1 ) {
			$rev_approved = 0;
		} else {
			$rev_approved = 1;
			
			// Clean cache only when a review is auto-approved
			$cache = &JFactory::getCache('com_mtree');
			$cache->clean();
		}

		if ( $my->id > 0 )
		{
			# User is logged on, store user ID
			$database->setQuery( 'INSERT INTO #__mt_reviews '
				. '( `link_id` , `user_id` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) '
				. 'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($user_fields_data['rev_title']) . ', ' . $database->quote($user_fields_data['rev_text']) . ', ' . $database->quote($now) . ', ' . $database->quote($rev_approved) . ')');
		}
		else
		{
			# User is not logged on, store Guest name
			$database->setQuery( 'INSERT INTO #__mt_reviews '
				. '( `link_id` , `guest_name` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) '
				. 'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($user_fields_data['guest_name']) . ', ' . $database->quote($user_fields_data['rev_title']) . ', ' . $database->quote($user_fields_data['rev_text']) . ', ' . $database->quote($now) . ', ' . $database->quote($rev_approved) . ')');
		}

		if (!$database->execute())
		{
			echo "<script> alert('".$database->stderr()."');</script>\n";
			exit();
		}
		$rev_id = $database->insertid();

		$mtLog = new mtLog( $database, $remote_addr, $my->id, $link_id, $rev_id );
		$mtLog->logReview();

		if( $user_fields_data['rating'] > 0 && $user_fields_data['rating'] <= 5 ) {

			$users_last_rating = $mtLog->getUserLastRating();

			# User has voted before. 
			# This review will update his vote and recalculate the listing rating while maintaining the number of votes.
			if( $mtconf->get('rate_once') && $users_last_rating > 0 ) {
				if($user_fields_data['rating'] <> $users_last_rating) {
					$new_rating = ((($link->link_rating * $link->link_votes) + ($user_fields_data['rating']-$users_last_rating) ) / $link->link_votes);
					# Update the new rating
					$database->setQuery( "UPDATE #__mt_links SET link_votes = link_votes + 1, link_rating = '$new_rating' WHERE link_id = '$link_id' ");
					if (!$database->execute()) {
						echo "<script> alert('".$database->stderr()."');</script>\n";
						exit();
					}
				}
				$mtLog->deleteVote();

			# User has not voted before. Simply add a new vote for the listing.
			} else {

				$new_rating = ((($link->link_rating * $link->link_votes) + $user_fields_data['rating']) / ++$link->link_votes);

				# Update #__mt_links table
				$database->setQuery( 'UPDATE #__mt_links '
					. ' SET link_rating = ' . $database->quote($new_rating)
					. ', link_votes = ' . $database->quote($link->link_votes)
					. ' WHERE link_id = ' . $database->quote($link_id));
				if (!$database->execute()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

			}

			$mtLog->logVote( $user_fields_data['rating'] );
		}

		# Notify Admin
		if ( $mtconf->get('notifyadmin_newreview') == 1 ) {
			
			$database->setQuery( "SELECT * FROM #__mt_links WHERE link_id = '".$link_id."' LIMIT 1" );
			$link = $database->loadObject();
			
			if ( $my->id > 0 ) {
				$database->setQuery( "SELECT name, username, email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
				$author = $database->loadObject();
				$author_name = $author->name;
				$author_username = $author->username;
				$author_email = $author->email;
			} else {
				$author_name = $user_fields_data['guest_name'];
				$author_username = JText::_( 'COM_MTREE_GUEST' );
				$author_email = '';
			}

			if ( $rev_approved == 0 ) {
				$subject = JText::sprintf( 'COM_MTREE_NEW_REVIEW_EMAIL_SUBJECT_WAITING_APPROVAL', $link->link_name );
				$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_REVIEW_MSG_WAITING_APPROVAL', $link->link_name, $user_fields_data['rev_title'], $author_name, $author_username, $author_email, stripslashes(html_entity_decode($user_fields_data['rev_text'])));
			} else {
				$uri = JUri::getInstance();
				$subject = JText::sprintf( 'COM_MTREE_NEW_REVIEW_EMAIL_SUBJECT_APPROVED', $link->link_name);
				$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_REVIEW_MSG_APPROVED', $link->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $user_fields_data['rev_title'], $author_name, $author_username, $author_email, stripslashes(html_entity_decode($user_fields_data['rev_text'])));
			}

			mosMailToAdmin( $subject, $msg );

		}

		# Notify listing owner if review is auto approved.
		if ( 
			$mtconf->get('notifyowner_review_added') == 1 
			&& 
			$mtconf->get('needapproval_addreview') == 0 
		)
		{
			// Notification can only be sent when this listing is 
			// owned by a registered user.
			$database->setQuery(
				'SELECT u.email FROM #__users AS u'
				. ' LEFT JOIN #__mt_links AS l ON u.id = l.user_id'
				. ' WHERE l.link_id = ' . $link_id . ' LIMIT 1'
				);
			$row = $database->loadObject();
			
			$uri = JUri::getInstance();
			$link_url = $uri->toString(array( 'scheme', 'host', 'port' )) 
				. JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid");

			if ( $row->email <> '' )
			{
				JFactory::getMailer()->sendMail( 
					$mtconf->getjconf('mailfrom'), 
					$mtconf->getjconf('fromname'), 
					$row->email, 
					JText::sprintf( 'COM_MTREE_REVIEW_ADDED_SUBJECT', $link->link_name ), 
					JText::sprintf( 'COM_MTREE_REVIEW_ADDED_MSG', $link->link_name, $link_url )
					);
			}
		}

		$app->setUserState('com_mtree.addreview.link_id'.$link_id.'.data', null);

		if ( $mtconf->get('needapproval_addreview') == 1 ) {
			$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REVIEW_WILL_BE_REVIEWED' ) );
		} else {
			$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REVIEW_HAVE_BEEN_SUCCESSFULLY_ADDED' ) );
		}

	}
}

/***
* Rating
*/
function addrating( $link_id, $option ) {
	$database =& JFactory::getDBO();

	# Get the rating
	$rating	= JFactory::getApplication()->input->getInt('rating', 0);

	$result = saverating( $link_id, $rating );

	$cache = &JFactory::getCache('com_mtree');
	$cache->clean();

	$return = (object) array(
		'status'		=> '',
		'message'		=> '',
		'total_votes'		=> '',
		'total_votes_string'	=> ''
	);

	if( $result ) {
		$database->setQuery( "SELECT link_votes FROM #__mt_links WHERE link_id = '".$link_id."' LIMIT 1" );
		$total_votes = $database->loadResult();
		$return = (object) array(
			'status'		=> 'OK',
			'message'		=> JText::_( 'COM_MTREE_THANKS_FOR_RATING' ),
			'total_votes'		=> $total_votes,
			'total_votes_text'	=> JText::sprintf( 'COM_MTREE_X_VOTES', $total_votes )
		);
	} else {
		$return->status = 'NA';
	}
	echo json_encode($return);
}

function saverating( $link_id, $rating ) {
	global $savantConf, $Itemid, $mtconf;

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	# User IP Address
	$vote_ip = $_SERVER['REMOTE_ADDR'];

	if (empty($link)) {
		# Link does not exists, or is not published
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;
	
	} elseif ($mtconf->get('user_rating') == '-1') {
		# Rating is disabled
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;
	
	} elseif ( 
		$mtconf->get('user_rating') == '1' && $my->id < 1
		||
		($mtconf->get('user_rating') == '2' && $my->id == 0)
	) {
		# User is not logged in
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;

	} elseif ( $mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id == $link->user_id ) {
		# Listing owners are not allowed to rate
		echo JText::_( 'COM_MTREE_YOU_ARE_NOT_ALLOWED_TO_RATE' );
		return false;
		
	} elseif ( $rating <= 0 || $rating > 5 ) {
		# Invalid rating. User did not fill in rating, or attempt misuse
		echo JText::_( 'COM_MTREE_PLEASE_SELECT_A_RATING' );
		return false;
		
	} elseif( $mtconf->get('user_rating') == 2 && $my->id == $link->user_id ) {
		# Owner is trying to vote own listing
		echo JText::_( 'COM_MTREE_YOU_RE_NOT_ALLOWED_TO_RATE_OWN_LISTING' );

	} else {

		# Everything is ok, add the rating
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();

		if ( $my->id < 1 ) $my->id = 0;

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link_id) . ' AND log_ip = ' . $database->quote($vote_ip) . ' AND log_type = \'vote\'' );
		} else {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link_id) . ' AND user_id = ' . $database->quote($my->id) . ' AND log_type = \'vote\'' );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$voted || ($voted && !$mtconf->get('rate_once')) ) {

			$mtLog = new mtLog( $database, $vote_ip, $my->id, $link_id );
			$mtLog->logVote( $rating );

			$new_rating = ((($link->link_rating * $link->link_votes) + $rating) / ++$link->link_votes);

			# Update #__mt_links table
			$database->setQuery( "UPDATE #__mt_links "
				.	" SET link_rating = '$new_rating', link_votes = '$link->link_votes' "
				.	"WHERE link_id = '$link_id' ");
			if (!$database->execute()) {
				echo $database->stderr();
				exit();
				return false;
			}

			return true;

		} else {
			return false;
		}

	}

}

function fav( $link_id, $action, $option ) {
	$database =& JFactory::getDBO();
	$result = savefav( $link_id, $action, $option );
	$return = (object) array(
		'status'	=> '',
		'message'	=> '',
		'total_fav'	=> '',
	);
	
	if( $result ) {
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE link_id = '".$link_id."'" );
		$total_fav = $database->loadResult();
		if( !is_numeric($total_fav) || $total_fav < 0 ) {
			$total_fav = 0;
		}
		
		$return->status = 'OK';
		$return->total_fav = $total_fav;
		if( $action == 1 ) {
			$return->message = '<a href="javascript:fav('.$link_id.',-1);">'.JText::_( 'COM_MTREE_REMOVE_FAVOURITE' ).'</a>';
		} else {
			$return->message = '<a href="javascript:fav('.$link_id.',1);">'.JText::_( 'COM_MTREE_ADD_AS_FAVOURITE' ).'</a>';
		}
	} else {
		$return->status = 'NA';
	}
	echo json_encode($return);
}

function savefav( $link_id, $action ) {
	global $savantConf, $Itemid, $mtconf;

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();

	if($mtconf->get('show_favourite') == 0) {
		return false;
	}

	$jdate = JFactory::getDate();
	$now = $jdate->toSql();
	$nullDate	= $database->getNullDate();

	if ( $my->id < 1 ) $my->id = 0;

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE user_id = '".$my->id."' AND link_id = '".$link_id."' LIMIT 1" );
	if( $action == 1 ) {
		# If user is adding a favourite, make sure the link has not been added to the user's favourite before
		if( $database->loadResult() > 0 ) {
			return false;
		}
	} else {
		# If user is removing a favourite, make sure he has the favourite
		if( $database->loadResult() < 1 ) {
			return false;
		}
	}

	# User IP Address
	$vote_ip = $_SERVER['REMOTE_ADDR'];

	if (empty($link)) {
		# Link does not exists, or is not published
		JError::raiseError(404,JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
		return false;

	} elseif ( $my->id < 1) {
		# User is not logged in
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;
	
	} elseif ( $action != -1 && $action != 1 ) {
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;
		
	} else {

		# Everything is ok, add the rating

		$mtLog = new mtLog( $database, $vote_ip, $my->id, $link_id );
		$mtLog->logFav($action);

		# Add favourite
		if( $action == 1 ) {
			$database->setQuery( "INSERT INTO #__mt_favourites "
				.	"(`user_id`, `link_id`, `fav_date`) "
				.	"VALUES ( "
				.	"'" . $my->id . "',"
				.	"'" . $link_id . "',"
				.	"'" . $now . "'"
				.	")");
		} else {
			$database->setQuery( "DELETE FROM #__mt_favourites WHERE user_id = '" . $my->id . "' AND link_id = '" . $link_id . "' LIMIT 1" );
		}
		if (!$database->execute()) {
			echo $database->stderr();
			return false;
		}

		return true;

	}

}

/***
* Vote Review - Process the vote and redirect to the listing with message
* @param int review id
* @param int review vote. 1 = helpful, -1 = not helpful
* @param string option
*/
function votereview( $rev_id, $rev_vote, $option ) {
	$database =& JFactory::getDBO();

	$database->setQuery( "SELECT * FROM #__mt_reviews WHERE rev_approved='1' AND rev_id='".$rev_id."' LIMIT 1" );
	$review = $database->loadObject();
	$result = savevotereview( $review, $rev_vote, $option );
	$return = (object) array(
		'status'	=> '',
		'message'	=> '',
		'helpful_text'	=> ''
	);
	
	if( $result ) {
		$return->status 	= 'OK';
		$return->message 	= JText::_( 'COM_MTREE_THANKS_FOR_YOUR_VOTE' );
		$return->helpful_text 	= JText::sprintf( 'COM_MTREE_PEOPLE_FIND_THIS_REVIEW_HELPFUL', (($rev_vote == 1)? $review->vote_helpful +1:$review->vote_helpful), ($review->vote_total +1) );
		
	} else {
		$return->status 	= 'NA';
	}
	echo json_encode($return);
}

/**
* Save the vote review to database
* @param object review object
* @param int review vote. 1 = helpful, -1 = not helpful
* @param string option
* @return TRUE=save is successful, FALSE=save is not successful or vote has been recorded in the past
*/
function savevotereview( $review, $rev_vote, $option ) {
	global $mtconf;

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	
	# User IP Address
	$vote_ip = $_SERVER['REMOTE_ADDR'];

	if (empty($review)) {
		# Review does not exists, or is not published
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;

	} elseif ( $mtconf->get('user_vote_review') == '0' ) {
		# Feature has been disabled
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;

	} elseif( $my->id < 1) {
		# User is not logged in
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;

	} elseif ( $rev_vote <> -1 && $rev_vote <> 1 ) {
		# Invalid review vote. User did not fill in rating, or attempt misuse
		JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		return false;
		
	} else {

		# Everything is ok, add the rating
		$jdate = JFactory::getDate();
		$now = $jdate->toSql();

		if ( $my->id < 1 ) $my->id = 0;

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE rev_id =' . $database->quote($review->rev_id) . ' AND log_ip = ' . $database->quote($vote_ip) . ' AND log_type = \'votereview\'' );
		} else {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE rev_id =' . $database->quote($review->rev_id) . ' AND user_id = ' . $database->quote($my->id) . ' AND log_type = \'votereview\'' );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$voted ) {

			# Update #__mt_log table
			$database->setQuery( 'INSERT INTO #__mt_log '
				. ' ( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id`, `rev_id`, `value` )'
				. ' VALUES ( ' . $database->quote($vote_ip) . ', ' . $database->quote('votereview') . ', ' . $database->quote($my->id) . ', ' . $database->quote($now) . ', ' . $database->quote($review->link_id) . ', ' . $database->quote($review->rev_id) . ', ' . $database->quote( ($rev_vote == -1) ? '-1':'1' ) . ')');
			if (!$database->execute()) {
				echo $database->stderr();
				return false;
			}

			# Update review
			$database->setQuery( 'UPDATE #__mt_reviews '
				. 'SET vote_total = vote_total + 1' . ( ($rev_vote == 1) ? ', vote_helpful = vote_helpful + 1 ':' ' )
				. 'WHERE rev_id = \''.$review->rev_id.'\' LIMIT 1'
				);
			if (!$database->execute()) {
				echo $database->stderr();
				return false;
			}

			return true;

		} else {
			return false;
		}

	}

}

/***
* Report Review
*/
function reportreview( $rev_id, $option ) {
	global $savantConf, $mtconf;
	
	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	
	if( $mtconf->get('user_report_review') == -1 || ($mtconf->get('user_report_review') == 1 && $my->id == 0) ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {
		$database->setQuery( "SELECT r.*, u.username, l.value AS rating FROM #__mt_reviews AS r "
			.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\nLEFT JOIN #__mt_log AS l ON l.user_id = r.user_id AND l.link_id = r.link_id AND log_type = 'vote'"
			.	"\nWHERE r.rev_id = '".$rev_id."' LIMIT 1" );
		$review = $database->loadObject();

		if( $review->link_id > 0 ) {
			$link = loadLink( $review->link_id, $savantConf, $fields, $params );

			setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_REPORT_REVIEW', $review->rev_title ));
			reportreview_cache( $review, $link, $fields, $params, $option );
		} else {
			return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		}
	}
}

function reportreview_cache( $review, $link, $fields, $params, $option ) {
	global $savantConf, $mtconf;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	$savant->assign('review', $review);

	if (empty($link)) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} else {
		$user_fields_data = (array) JFactory::getApplication()->getUserState(
			'com_mtree.reportreview.rev_id'.$review->rev_id.'.data', 
			array(
				'your_name'	=> '',
				'message'	=> ''
			)
		);

		// Generate CAPTCHA
		$captcha_html = '';
		if( $mtconf->get('use_captcha_reportreview') )
		{
			$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

			if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => 'xnamespace'))) !== null)
			{
				$captcha_html .= $captcha->display('captcha', 'captcha'); 
			}				
		}
		
		$savant->assign('captcha_html', $captcha_html); 
		$savant->assign('user_fields_data', $user_fields_data);
		$savant->display( 'page_reportReview.tpl.php' );
	}

}

function send_reportreview( $rev_id, $option ) {
	global $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	
	if( $mtconf->get('user_report_review') == -1 || ($mtconf->get('user_report_review') == 1 && $my->id == 0) ) {
		
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {

		$database->setQuery( "SELECT l.link_id, rev_title, rev_text, l.link_name FROM #__mt_reviews AS r "
			. "\n LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\n WHERE rev_id ='".$rev_id."' AND r.rev_approved = 1 AND l.link_published = 1 AND l.link_approved = 1" 
			. "\n LIMIT 1"
			);
		$link = $database->loadObject();

		if( count($link) == 1 && $link->link_id > 0 )
		{
			if( $my->id > 0 )
			{
				$user_fields_data['your_name'] = $my->name.' ('.$my->username.')';
			}
			else
			{
				$user_fields_data['your_name'] = JFactory::getApplication()->input->getString( 'your_name', '' );
			}

			$user_fields_data['message'] = JFactory::getApplication()->input->get( 'message', '', 'STRING' );
			$captcha_answer              = JFactory::getApplication()->input->getString( 'recaptcha_response_field', '' ); 
			
			$app->setUserState('com_mtree.reportreview.rev_id'.$rev_id.'.data', $user_fields_data);
			
			// Validate Captcha
			if( $mtconf->get('use_captcha_reportreview') )
			{
				$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
				$captcha = JCaptcha::getInstance($plugin);

				// Test the value.
				if (!$captcha->checkAnswer($captcha_answer))
				{
					$app->redirect( JRoute::_("index.php?option=$option&task=reportreview&rev_id=$rev_id&Itemid=$Itemid"), $captcha->getError() );
				}
			}
			
			$uri = JUri::getInstance();
			$text = JText::sprintf( 'COM_MTREE_REPORT_REVIEW_EMAIL', $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), $user_fields_data['your_name'], $user_fields_data['message'], $link->rev_title, $link->link_name, $link->rev_text, $link->rev_text );

			$subject = JText::_( 'COM_MTREE_REPORT_REVIEW' ) . ' - ' . $link->rev_title;

			if( mosMailToAdmin( $subject, $text ) )
			{
				if( $my->id > 0 )  {
					# User is logged on, store user ID
					$database->setQuery( 'INSERT INTO #__mt_reports '
						. '( `link_id` , `rev_id` , `user_id` , `comment`, created ) '
						. 'VALUES (' . $database->quote($link->link_id) . ', ' . $database->quote($rev_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($user_fields_data['message']) . ', ' . $database->quote($now) . ')');

				} else {
					# User is not logged on, store Guest name
					$database->setQuery( 'INSERT INTO #__mt_reports '
						. ' ( `link_id` , `rev_id` , `guest_name` , `comment`, created ) '
						. ' VALUES (' . $database->quote($link->link_id) . ', ' . $database->quote($rev_id) . ', ' . $database->quote($user_fields_data['your_name']) . ', ' . $database->quote($user_fields_data['message']) . ', ' . $database->quote($now) . ')');

				}

				if (!$database->execute()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

				$app->setUserState('com_mtree.reportreview.rev_id'.$rev_id.'.data', null);

				$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REPORT_HAVE_BEEN_SENT' ));
			}

		}

	}

}

/***
* Reply Review
*/
function replyreview( $rev_id, $option ) {
	global $savantConf, $mtconf;

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	
	$database->setQuery( "SELECT r.*, u.username, l.value AS rating FROM #__mt_reviews AS r "
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_log AS l ON l.user_id = r.user_id AND l.link_id = r.link_id AND log_type = 'vote'"
		.	"\nWHERE r.rev_id = '".$rev_id."' LIMIT 1" );
	$review = $database->loadObject();
	
	# Replying review are restricted to the listing owner only.
	if( isset($review) && $review->link_id > 0 && $my->id > 0 && $mtconf->get('owner_reply_review') ) {

		$link = loadLink( $review->link_id, $savantConf, $fields, $params );

		if( $link->user_id == $my->id ) {
			setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_REPLY_REVIEW', $review->rev_title ));
			replyreview_cache( $review, $link, $fields, $params, $option );
		} else {
			return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		}

	} else {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
}

function replyreview_cache( $review, $link, $fields, $params, $option ) {
	global $savantConf;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	$savant->assign('review', $review);

	if (empty($link)) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	} elseif ( !empty($review->ownersreply_text) ) {
		$savant->assign('error_msg', JText::_( 'COM_MTREE_YOU_CAN_ONLY_REPLY_A_REVIEW_ONCE' ));
		$savant->display( 'page_errorListing.tpl.php' );
	} else {
		$savant->display( 'page_replyReview.tpl.php' );
	}

}

function send_replyreview( $rev_id, $option ) {
	global $Itemid, $mtconf;
	
	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	
	$message = JFactory::getApplication()->input->get( 'message', '', 'RAW' );

	if ( !$mtconf->get('owner_reply_review') ) {

		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {

		if ( $message == '' ) {
			# Reply text is empty
			echo "<script> alert('".JText::_( 'COM_MTREE_PLEASE_FILL_IN_REPLY' )."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $mtconf->get('needapproval_replyreview') == 1 ) {
			$rr_approved = 0;
		} else {
			$rr_approved = 1;
		}

		$database->setQuery( "SELECT l.link_id, l.user_id AS link_owner_user_id, rev_title, rev_text, l.link_name, r.ownersreply_text FROM #__mt_reviews AS r "
			. "\n LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\n WHERE rev_id ='".$rev_id."' AND r.rev_approved = 1 AND l.link_published = 1 AND l.link_approved = 1" 
			. "\n LIMIT 1"
			);
		$link = $database->loadObject();

		if( count($link) == 1 && empty($link->ownersreply_text) && $link->link_id > 0 && $my->id > 0 && $link->link_owner_user_id == $my->id ) {

			# Notify Admin
			if ( $my->id > 0 ) {
				$database->setQuery( "SELECT name, username, email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
				$author = $database->loadObject();
				$author_name = $author->name;
				$author_username = $author->username;
				$author_email = $author->email;
			} else {
				$author_name = $guest_name;
				$author_username = JText::_( 'COM_MTREE_GUEST' );
				$author_email = '';
			}

			if ( $rr_approved == 0 ) {
				$subject = JText::sprintf( 'COM_MTREE_NEW_REVIEW_REPLY_EMAIL_SUBJECT_WAITING_APPROVAL', $link->link_name);
				$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_REVIEW_REPLY_MSG_WAITING_APPROVAL', $my->name, $message, $link->rev_title, $link->link_name, $link->rev_text );
			} else {
				$subject = JText::sprintf( 'COM_MTREE_NEW_REVIEW_REPLY_EMAIL_SUBJECT_APPROVED', $link->link_name);
				$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_REVIEW_REPLY_MSG_APPROVED', $my->name, $message, $link->rev_title, $author_name, $author_username, $author_email, $link->rev_text );
			}

			if( mosMailToAdmin( $subject, $msg ) )
			{
				$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_text = ' . $database->quote($message) . ', ownersreply_date = ' . $database->quote($now) . ', ownersreply_approved = ' . $database->quote($rr_approved) . ' WHERE rev_id = ' . $database->quote($rev_id) );

				if (!$database->execute()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

				$remote_addr = $_SERVER['REMOTE_ADDR'];
				$mtLog = new mtLog( $database, $remote_addr, $my->id, $link->link_id, $rev_id );
				$mtLog->logReplyReview();

				if ( $mtconf->get('needapproval_replyreview') == 1 ) {
					$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REPLY_REVIEW_WILL_BE_REVIEWED' ));
				} else {
					$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'COM_MTREE_REPLY_REVIEW_HAVE_BEEN_SUCCESSFULLY_ADDED' ));
				}
			}

		} else {

			return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

		}
	}

}

/***
* Recommend to Friend
*/

function recommend( $link_id, $option ) {
	global $savantConf;

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if($link === false) {
		JError::raiseError(404, JText::_('COM_MTREE_ERROR_LISTING_NOT_FOUND'));
	} else {
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_RECOMMEND', $link->link_name ) );
		recommend_cache( $link, $fields, $params, $option );
	}
}

function recommend_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();

	$page = 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( empty($link) || $mtconf->get('user_recommend') == -1 ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} elseif ( $mtconf->get('user_recommend') == '1' && $my->id < 1 ) {
		# Error. Please login before you can recommend
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=recommend&link_id='.$link->link_id) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_RECOMMEND'
			)
		);
	} else {
		$savant->display( 'page_recommend.tpl.php' );
	}

}

function send_recommend( $link_id, $option ) {
	global $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$my			=& JFactory::getUser();

	if ( $mtconf->get('show_recommend') == 0 || ($mtconf->get('user_recommend') == '1' && $my->id < 1) || $mtconf->get('user_recommend') == -1 ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {

		$your_name	= JFactory::getApplication()->input->get( 'your_name', '' );
		$your_email	= JFactory::getApplication()->input->get( 'your_email', '', 'RAW' );
		$friend_name	= JFactory::getApplication()->input->get( 'friend_name', '' );
		$friend_email	= JFactory::getApplication()->input->get( 'friend_email', '', 'RAW' );

		if (!$your_email || !$friend_email || (mt_is_email($your_email)==false) || (mt_is_email($friend_email)==false) ){
			echo "<script>alert (\"".JText::_( 'COM_MTREE_YOU_MUST_ENTER_VALID_EMAIL' )."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$uri = JUri::getInstance();
		$msg = JText::sprintf( 'COM_MTREE_RECOMMEND_MSG',
			$mtconf->getjconf('sitename'),
			$your_name,
			$your_email,
			$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id.'&Itemid='.$Itemid, false)
			);

		$subject = JText::sprintf( 'COM_MTREE_RECOMMEND_SUBJECT', $your_name);

		if  (!validateInputs( $friend_email, $subject, $msg ) ) {
			$document =& JFactory::getDocument();
			JError::raiseWarning( 0, $document->getError() );
			return false;
		} else {
			JFactory::getMailer()->sendMail( $your_email, $your_name, $friend_email, $subject, wordwrap($msg) );
			$app->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::sprintf( 'COM_MTREE_RECOMMEND_EMAIL_HAVE_BEEN_SENT', $friend_name) );
		}
	}
}

/***
* Contact Owner
*/

function contact( $link_id, $option ) {
	global $savantConf;

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if ( empty($link) )
	{
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
	}
	else
	{
		setTitle( JText::sprintf( 'COM_MTREE_PAGE_TITLE_CONTACT', $link->link_name ) );
		contact_cache( $link, $fields, $params, $option );
	}
}

function contact_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$page 		= 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if (
		empty($link)
		OR
		$mtconf->get( 'show_contact' ) == 0
		OR
		$mtconf->get( 'use_owner_email' ) == 0 && empty($link->email)
		OR
		$mtconf->get( 'user_contact' ) == -1
	) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} elseif ( $mtconf->get('user_contact') == '1' && $my->id < 1 ) {
		# Error. Please login before you can contact the owner
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=contact&link_id='.$link->link_id) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_CONTACT'
			)
		);
	} else {

		loadContactOwnerForm( $link->link_id, $savant );
		$savant->display( 'page_contactOwner.tpl.php' );
	}

}

function loadContactOwnerForm( $link_id, &$savant )
{
	global $mtconf;
	
	$form = getContactOwnerForm( $link_id );
	
	JPluginHelper::importPlugin('mtree');

	// Get the dispatcher.
	$dispatcher	= JDispatcher::getInstance();

	// Trigger the form preparation event.
	$result = $dispatcher->trigger('onMTreeListingContactPrepareForm', array($form));

	JHtml::_('behavior.keepalive');
	JHtml::_('behavior.formvalidation');
	JHtml::_('behavior.tooltip');

	// Generate CAPTCHA
	$captcha_html = '';
	if( $mtconf->get('use_captcha_contact') )
	{
		$captcha_html = getCaptchaHTML();
	}

	$savant->assign('captcha_html', $captcha_html);
	$savant->assign('form', $form);
}

function getCaptchaHTML()
{
	$captcha_html = '';
	
	$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

	if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => 'xnamespace'))) !== null)
	{
		$captcha_html .= $captcha->display('captcha', 'captcha'); 
	}
	
	return $captcha_html;
}

function getContactOwnerForm( $link_id )
{
	$data = (array) JFactory::getApplication()->getUserState(
		'com_mtree.contact.link_id'.$link_id.'.data', 
		array()
	);

	jimport('joomla.form.form');
	
	// Get the form.
	JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
	JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');

	$form = JForm::getInstance('com_mtree.contact', 'contact', array('control' => 'mtform'));
	
	foreach( $data AS $data_key => $data_value )
	{
		if( !empty($data_value) )
		{
			$form->setValue($data_key, null, $data_value);
		}
	}
	
	return $form;
}

function send_contact( $link_id, $option ) {
	global $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$document	=& JFactory::getDocument();

	$link = new mtLinks( $database );
	$link->load( $link_id );

	if ( 
		$mtconf->get('show_contact') == 0 
		OR
		($mtconf->get('user_contact') == '1' && $my->id < 1)
		OR
		$mtconf->get( 'use_owner_email' ) == 0 && empty($link->email)
		OR
		$mtconf->get( 'user_contact' ) == -1
	) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {
		$data		= JFactory::getApplication()->input->get('mtform', array(), 'array');
		$captcha_answer	= JFactory::getApplication()->input->getString( 'recaptcha_response_field', '' ); 
		$uri		=& JUri::getInstance();

		$app->setUserState('com_mtree.contact.link_id'.$link_id.'.data', $data);

		// Validate Captcha
		if( $mtconf->get('use_captcha_contact') )
		{
			$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			$captcha = JCaptcha::getInstance($plugin);

			// Test the value.
			if (!$captcha->checkAnswer($captcha_answer))
			{
				$app->redirect( JRoute::_("index.php?option=$option&task=contact&link_id=$link_id&Itemid=$Itemid"), $captcha->getError() );
			}
		}
		
		if (!$data['contact_email'] || (mt_is_email($data['contact_email'])==false) ){
			echo "<script>alert (\"".JText::_( 'COM_MTREE_YOU_MUST_ENTER_VALID_EMAIL' )."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$subject = JText::sprintf( 'COM_MTREE_CONTACT_SUBJECT', $mtconf->getjconf('sitename'), $link->link_name);
		
		if( empty($link->email) ) {
			$database->setQuery( 'SELECT email FROM #__users WHERE id = '.$link->user_id.' LIMIT 1' );
			$email = $database->loadResult();
		} else {
			$email = $link->email;
		}

		if  (!validateInputs( $email, $subject, $data['contact_message'] ) ) {
			JError::raiseWarning( 0, $document->getError() );
			return false;
		} else {
			// Mosets Tree plugins
			JPluginHelper::importPlugin('mtree');
			$dispatcher	= JDispatcher::getInstance();
			
			// Validation succeeded, continue with custom handlers
			$results	= $dispatcher->trigger('onMTreeListingContactValidate', array(&$email, &$subject, &$data));

			foreach ($results as $result) {
				if (JError::isError($result)) {
					JError::raiseWarning( 0, $document->getError() );
					return false;
				}
			}

			// Process the Mosets Tree plugins to integrate with other applications
			$dispatcher->trigger('onMTreeListingContactSubmit', array(&$email, &$subject, &$data));
			
			// Get BCC e-mails if any
			
			if( $mtconf->get('contact_bcc_email') == '' )
			{
				$bcc = null;
			}
			elseif ( strpos($mtconf->get('contact_bcc_email'),',') === false )
			{
				$bcc = array($mtconf->get('contact_bcc_email'));
			}
			else
			{
				$bcc = explode(',', $mtconf->get('contact_bcc_email'));
			}
			
			$message = JText::sprintf( 
				'COM_MTREE_CONTACT_MESSAGE', 
				$data['contact_name'], 
				$data['contact_email'], 
				$link->link_name, 
				$uri->toString(array( 'scheme', 'host', 'port' )) 
					. JRoute::_( 
						"index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", 
						false 
						), 
				$data['contact_message'] 
				);

			JFactory::getMailer()->sendMail(
				$data['contact_email'], 
				$data['contact_name'], 
				$email, 
				$subject, 
				wordwrap($message),
				0,
				null,
				$bcc 
				);
			
			$app->setUserState('com_mtree.contact.link_id'.$link_id.'.data', null);
			
			$app->redirect( 
				JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), 
				JText::_( 'COM_MTREE_CONTACT_EMAIL_HAVE_BEEN_SENT' )
				);
		}
	}

}

function mt_is_email($email){
	$rBool=false;

	if(preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email)){
		$rBool=true;
	}
	return $rBool;
}

/***
* Edit Listing
*/
function editlisting( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$document	=& JFactory::getDocument();
	
	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mfields.class.php' );

	# Get cat_id if user is adding new listing. 
	$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);

	// This var retrieve the link_id for adding listing
	$link_id_passfromurl = JFactory::getApplication()->input->getInt('link_id', 0);
	
	if ( $link_id_passfromurl > 0 && $cat_id == 0 ) {
		$database->setQuery( "SELECT cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id ='".$link_id_passfromurl."' AND cl.link_id = l.link_id" );
		$cat_id = $database->loadResult();
	}

	$link = new mtLinks( $database );

	# Do not allow Guest to edit listing
	if ( $link_id > 0 && $my->id <= 0 ) {
		$link->load( 0 );
	} else {
		$link->load( $link_id );
	}
	
	$cf_ids = array(1);
	$cf_ids = array_merge($cf_ids,getAssignedFieldsID($cat_id));
	
	$mtconf->setCategory( $cat_id );
	
	# Load all published CORE & custom fields
	$sql = "SELECT cf.*, " . ($link_id ? $link_id : 0) . " AS link_id, cfv.value AS value, cfv.attachment, cfv.counter FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id AND cfv.link_id = " . $link_id
		.	"\nWHERE cf.hidden ='0' AND cf.published='1'"
		.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
		.	"\nORDER BY ordering ASC";
	$database->setQuery($sql);

	$fields = new mFields();
	$fields->setCoresValue( $link->link_name, $link->link_desc, $link->address, $link->city, $link->state, $link->country, $link->postcode, $link->telephone, $link->fax, $link->email, $link->website, $link->price, $link->link_hits, $link->link_votes, $link->link_rating, $link->link_featured, $link->link_created, $link->link_modified, $link->link_visited, $link->publish_up, $link->publish_down, $link->metakey, $link->metadesc, $link->user_id, '' );
	$fields->loadFields($database->loadObjectList());

	$user_fields_data = (array)JFactory::getApplication()->getUserState('com_mtree.editlisting.data', array());
	if( !empty($user_fields_data) )
	{
		$fields->resetPointer();
		while( $fields->hasNext() )
		{
			$field = $fields->getField();
			if( isset($user_fields_data[$field->getName()]))
			{
				if( $field->isFile() )
				{
					// We do not save/hold attachments between redirects when a field 
					// validation fails. This line below prevent it from using a newly 
					// uploadfile file name. Instead it falls back to (correctly) use
					// pre-existing (if available) attachment.
					$fields->next();
					continue;
				}
				else
				{
					$fields->fields[$fields->getCurrentPointer()]['value'] = $user_fields_data[$field->getName()];
				}
			}
			$fields->next();
		}
	}

	$fields->setCatID($cat_id);
	
	# Load images
	$database->setQuery( "SELECT img_id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC" );
	$images = $database->loadObjectList();
	
	# Get current category's template
	$database->setQuery( "SELECT cat_name, cat_parent, cat_template, metakey, metadesc FROM #__mt_cats WHERE cat_id='".$cat_id."' AND cat_published='1' LIMIT 1" );
	$cat = $database->loadObject();
	
	if( $link->link_id == 0 )
	{
		if( $cat ) {
			setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_ADD_LISTING', $cat->cat_name));
		} else {
			setTitle(JText::_( 'COM_MTREE_PAGE_TITLE_ADD_LISTING' ));
		}
	} else {
		setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_EDIT_LISTING', $link->link_name));
	}

	JHtml::_('jquery.framework');
	$document->addCustomTag("<link href=\"" .$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_templates') . $mtconf->get('template') . '/' . 'editlisting.css' . "\" rel=\"stylesheet\" type=\"text/css\"/>");

	if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {
		loadCustomTemplate(null,$savantConf,$cat->cat_template);
	}

	# Get other categories
	$database->setQuery( "SELECT cl.cat_id FROM #__mt_cl AS cl WHERE cl.link_id = '$link_id' AND cl.main = '0'");
	$other_cats = $database->loadColumn();

	# Pathway
	$pathWay = new mtPathWay( $cat_id );
	$pw_cats = $pathWay->getPathWayWithCurrentCat( $cat_id );
	$pathWayToCurrentCat = '';
	$mtCats = new mtCats($database);
	$pathWayToCurrentCat = ' <a href="'.JRoute::_("index.php?option=com_mtree&task=listcats&Itemid=".$Itemid).'">'.JText::_( 'COM_MTREE_ROOT' )."</a>";
	foreach( $pw_cats AS $pw_cat ) {
		$pathWayToCurrentCat .= JText::_( 'COM_MTREE_ARROW' ) .' <a href="'.JRoute::_("index.php?option=com_mtree&task=listcats&cat_id=".$pw_cat."&Itemid=".$Itemid).'">'.$mtCats->getName($pw_cat)."</a>";
	}

	# Savant Template
	$savant = new Savant2($savantConf);

	assignCommonVar($savant);
	$savant->assign('pathway', $pathWay);
	$savant->assign('pathWayToCurrentCat',$pathWayToCurrentCat);
	$savant->assign('cat_id', (($link_id == 0) ? $cat_id : $link->cat_id ) );
	$savant->assign('other_cats', $other_cats );
	$savant->assignRef('link', $link);
	$savant->assignRef('fields',$fields);
	$savant->assignRef('images',$images);

	if( $mtconf->get('image_maxsize') > 1048576 ) {
		$savant->assign('image_size_limit', round(($mtconf->get('image_maxsize')/1048576),1) . 'MB' );
	} else {
		$savant->assign('image_size_limit', round($mtconf->get('image_maxsize')/1024) . 'KB' );
	}

	# Check permission
	if ( ($mtconf->get('user_addlisting') == 1 && $my->id < 1) || ($link_id > 0 && $my->id == 0) )
	{
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_(
						'index.php?option=com_mtree&task=addlisting'
						. (($link->link_id > 0)?'&link_id='.$link->link_id:'')
						. (($cat_id > 0)?'&cat_id='.$cat_id:'')
					) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_ADDLISTING'
			)
		);	
	}
	elseif( ($link_id > 0 && $my->id <> $link->user_id) || ($mtconf->get('user_allowmodify') == 0 && $link_id > 0) || ($mtconf->get('user_addlisting') == -1 && $link_id == 0) || ($mtconf->get('user_addlisting') == 1 && $my->id == 0) )
	{
		
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} else {
		// OK, you can edit
		$database->setQuery( "SELECT CONCAT('cust_',cf_id) as varname, caption As value, field_type, prefix_text_mod, suffix_text_mod FROM #__mt_customfields WHERE hidden <> '1' AND published = '1'" );
		$custom_fields = $database->loadObjectList('varname');
		$savant->assign('custom_fields', $custom_fields);

		# Load custom fields' value from #__mt_cfvalues to $link
		$database->setQuery( "SELECT CONCAT('cust_',cf_id) as varname, value FROM #__mt_cfvalues WHERE link_id = '".$link_id."'" );
		$cfvalues = $database->loadObjectList('varname');

		foreach( $custom_fields as $cfkey => $value )
		{
			if( isset($cfvalues[$cfkey]) ) {
				$savant->custom_data[$cfkey] = $cfvalues[$cfkey]->value;
			} else {
				$savant->custom_data[$cfkey] = '';
			}
		}

		// Get category's tree
		if($mtconf->get('allow_changing_cats_in_addlisting')) {
			getCatsSelectlist( $cat_id, $cat_tree, 1 );
			if ( $cat_id > 0 ) {
				$cat_options[] = JHtml::_('select.option', $cat->cat_parent, JText::_( 'COM_MTREE_ARROW_BACK' ));
				
			}
			
			if( $mtconf->get('allow_listings_submission_in_root') ) {
				$cat_options[] = JHtml::_('select.option', '0', JText::_( 'COM_MTREE_ROOT' ));
			}
			if(count($cat_tree)>0) {
				foreach( $cat_tree AS $ct ) {
					if( $ct["cat_allow_submission"] == 1 ) {
						$cat_options[] = JHtml::_('select.option', $ct["cat_id"], str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). $ct["cat_name"]);
					} else {
						$cat_options[] = JHtml::_('select.option', ($ct["cat_id"]*-1), str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). "(".$ct["cat_name"].")");
					}
				}
			}
			$catlist = JHtml::_('select.genericlist', $cat_options, 'new_cat_id', 'size=8', 'value', 'text', '', 'browsecat' );
			$savant->assignRef('catlist', $catlist );
		}
		
		// Give warning is there is already a pending approval for modification.
		if ( $link_id > 0 ) {
			$database->setQuery( "SELECT link_id FROM #__mt_links WHERE link_approved = '".(-1*$link_id)."'" );
			if ( $database->loadResult() > 0 ) {
				$savant->assign('warn_duplicate', 1);
			} else {
				$savant->assign('warn_duplicate', 0);
			}
		}
		
		JHtml::_('behavior.framework', true);

		JText::script('COM_MTREE_ADD_AN_IMAGE');
		JText::script('COM_MTREE_REMOVE');
		JText::script('COM_MTREE_SHOW_MAP', true);
		JText::script('COM_MTREE_REMOVE_MAP', true);
		JText::script('COM_MTREE_ENTER_AN_ADDRESS_AND_PRESS_LOCATE_IN_MAP_OR_MOVE_THE_RED_MARKER_TO_THE_LOCATION_IN_THE_MAP_BELOW');
		JText::script('COM_MTREE_LOCATE_IN_MAP', true);
		JText::script('COM_MTREE_LOCATING', true);
		JText::script('COM_MTREE_GEOCODER_NOT_OK', true);

		$savant->assign('pathWay', $pathWay);
		$savant->display( 'page_addListing.tpl.php' );
	}
}

function savelisting( $option ) {
	global $Itemid, $mtconf, $link_id;

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mfields.class.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.'/tools.mtree.php' );

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$app		= JFactory::getApplication('site');

	$raw_filenames = array();
	
	# Get cat_id / remove_image / link_image
	$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);
	
	$other_cats = JFactory::getApplication()->input->getString('other_cats', null);
	if( $other_cats == '' ) {
		$other_cats = array();
	} else {
		$other_cats = explode(',', $other_cats);
		JArrayHelper::toInteger($other_cats);
		$other_cats = array_slice($other_cats, 0, $mtconf->get('max_num_of_secondary_categories'));
	}
	
	# Check if any malicious user is trying to submit link
	if ( 
		($mtconf->get('user_addlisting') == 1 && $my->id < 1 && $link_id == 0) 
		|| 
		($mtconf->get('user_addlisting') == -1 && $link_id == 0)
		||
		($mtconf->get('user_allowmodify') == 0 && $link_id > 0) 
	)
	{
		JError::raiseError( 403, JText::_( 'JERROR_ALERTNOAUTHOR' ) );
	}
	else
	{
		# Allowed
		
		// This variable will be set to false when one or more fields 
		// does not validate
		$fields_validation = true;

		// Stores an array of error messages from invalidated fields.
		$fields_validation_errors = array();
		
		// Stores an array of queries that needs to be executed when
		// all fields validate and no errors occur. This allows a 
		// rollback behaviour to undo all database changing query such
		// as UPDATE, INSERT and DELETE.
		$transaction_queries = array();
		
		// Stores an array of files that needs to be deleted upon
		// validation
		$files_to_be_removed = array();
		
		// Stores an array of files that needs to be deleted upon
		// invalidation
		$files_to_be_removed_if_invalid = array();
		
		// Stores an array of files that needs to be copied
		$files_to_be_copied = array();
		
		// For the purpose of rollback, INSERT needs to be done to 
		// #__cfvalues_att to get an att_id. This variable stores
		// the ID and will be used for removal in case of rollback.
		$att_ids_to_be_removed = array();

		$cf_ids_to_be_removed = array();
		
		$keep_att_ids = array();
		
		// Mantain a list of user-entered data for the purpose of 
		// saving its state.
		$user_fields_data = array();
		
		// This symbol is used in the SQL to indicate a placeholder for the link_id.
		// When a listing is being submitted for modification approval, there is no 
		// link_id yet ($row has not been stored yet), so this placeholder is used 
		// and will be replaced later on once validation is passed and link_id is available.
		$link_id_symbol = '%LINK_ID%';

		$row = new mtLinks( $database );
		// $post = JFactory::getApplication()->input->get( 'post' );
		$post = $_POST;
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$isNew = ($row->link_id < 1) ? 1 : 0;
		
		$mtconf->setCategory( $row->cat_id );
		
		# Assignment for new record
		if ($isNew) {

			$jdate			= JFactory::getDate();
			$row->link_created 	= $jdate->toSql();
			$row->publish_up 	= $jdate->toSql();
			$row->ordering 		= 999;

			// Set an expire date for listing if enabled in configuration
			if( $mtconf->get('days_to_expire') > 0 )
			{
				$jdate->add(new DateInterval('P'.intval($mtconf->get('days_to_expire')).'D'));
	            		$row->publish_down  = $jdate->toSql(true); 
			}
			
			if ( $my->id > 0) {
				$row->user_id = $my->id;
			} else {
			/* Fix from  http://forum.mosets.com/showthread.php?p=80184
				$database->setQuery( 'SELECT id FROM #__users WHERE usertype = \'Super Administrator\' LIMIT 1' );
				$row->user_id = $database->loadResult();
			*/	
				jimport('joomla.access.access');
				$superUsers = JAccess::getUsersByGroup(8);
				$row->user_id = $superUsers[0];				
				
			}

			if( empty($row->alias) )
			{
				$row->alias = JFilterOutput::stringURLSafe($row->link_name);
			}

			// Approval for adding listing
			if ( $mtconf->get('needapproval_addlisting') ) {
				$row->link_approved = '0';
			} else {
				$row->link_approved = 1;
				$row->link_published = 1;
				$row->updateLinkCount( 1 );
				$cache = &JFactory::getCache('com_mtree');
				$cache->clean();
			}

		# Modification to existing record
		} else {
			
			# Validate that this user is the rightful owner
			$database->setQuery( "SELECT user_id FROM #__mt_links WHERE link_id = '".$row->link_id."'" );
			$user_id = $database->loadResult();

			if (  $user_id <> $my->id ) {
				JError::raiseError( 403, JText::_( 'JERROR_ALERTNOAUTHOR' ) );
			} else {

				// Get the name of the old photo and last modified date
				$sql="SELECT link_id, link_modified, link_created FROM #__mt_links WHERE link_id='".$row->link_id."'";
				$database->setQuery($sql);
				$old = $database->loadObject();

				// Retrive last modified date
				$old_modified = $old->link_modified;
				$link_created = $old->link_created;

				// $row->link_published = 1;
				$row->user_id = $my->id;
				
				// Get other info from original listing
				$database->setQuery( "SELECT * FROM #__mt_links WHERE link_id = '$row->link_id'" );
				$original = $database->loadObject();
				$original_link_id = $row->link_id;
				
				$row->link_modified = $row->getLinkModified( $original_link_id, $post );

				foreach( $original AS $k => $v ) {
					if( in_array($k,array('alias', 'link_hits', 'link_votes', 'link_rating', 'link_featured', 'link_created', 'link_visited', 'ordering', 'publish_down', 'publish_up', 'attribs', 'internal_notes', 'link_published', 'link_approved')) ) {
						$row->$k = $v;
					}
				}
				
				if( !isset($row->metadesc) && isset($original->metadesc) && !empty($original->metadesc) ) {
					$row->metadesc = $original->metadesc;
				}

				if( !isset($row->metakey) && isset($original->metakey) && !empty($original->metakey) ) {
					$row->metakey = $original->metakey;
				}

				// Remove any listing that is waiting for approval for this listing
				$database->setQuery( 'SELECT link_id FROM #__mt_links WHERE link_approved = \''.(-1*$row->link_id).'\' LIMIT 1' );
				$tmp_pending_link_id = $database->loadResult();

				if( $tmp_pending_link_id > 0 )
				{
					$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($tmp_pending_link_id) );
					$raw_filenames = array_merge($raw_filenames,$database->loadColumn());
					
					if(count($raw_filenames)) {
						foreach( $raw_filenames AS $attachment_to_be_removed )
						{
							$files_to_be_removed[] = $attachment_to_be_removed;
						}
					}

					$transaction_queries[] = "DELETE FROM #__mt_cfvalues WHERE link_id = '".$tmp_pending_link_id."'";
					$transaction_queries[] = "DELETE FROM #__mt_cfvalues_att WHERE link_id = '".$tmp_pending_link_id."'";
					$transaction_queries[] = "DELETE FROM #__mt_links WHERE link_id = '".$tmp_pending_link_id."' LIMIT 1";
					$transaction_queries[] = "DELETE FROM #__mt_cl WHERE link_id = '".$tmp_pending_link_id."'";

					$database->setQuery( "SELECT filename FROM #__mt_images WHERE link_id = '".$tmp_pending_link_id."'" );
					$tmp_pending_images = $database->loadColumn();

					if(count($tmp_pending_images)) {
						foreach($tmp_pending_images AS $tmp_pending_image) {
							$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $tmp_pending_image;
							$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $tmp_pending_image;
							$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $tmp_pending_image;
						}
					}
					$transaction_queries[] = "DELETE FROM #__mt_images WHERE link_id = '".$tmp_pending_link_id."'";
				}
				
				// Approval for modify listing
				if( $original->link_published && $original->link_approved )
				{
					if ( $mtconf->get('needapproval_modifylisting') ) {
						$row->link_approved = (-1 * $row->link_id);
						$row->link_id = null;
					} else {
						$row->link_approved = 1;
						$cache = &JFactory::getCache('com_mtree');
						$cache->clean();

						// Get old state (approved, published)
						$database->setQuery( "SELECT cat_id FROM #__mt_cl AS cl WHERE link_id ='".$row->link_id."' AND main = 1 LIMIT 1" );
						$old_state = $database->loadObject();
						if($row->cat_id <> $old_state->cat_id) {
							// @BUG Possbility of the count being incorrect when rollback is done
							// due to invalidate fields.
							$row->updateLinkCount( 1 );
							$row->updateLinkCount( -1, $old_state->cat_id );
						}
					}					
				}

			}

		} // End of $isNew

		# Load field type
		$database->setQuery('SELECT cf_id, field_type, hidden, published, iscore FROM #__mt_customfields');
		$fieldtype = $database->loadObjectList('cf_id');
		$hidden_cfs = array();
		foreach($fieldtype AS $ft) {
			if($ft->hidden && $ft->published) {
				$hidden_cfs[] = $ft->cf_id;
			}
			if($ft->iscore && $ft->hidden) {
				if( isset($original->{substr($ft->field_type,4)}) )
				{
					$row->{substr($ft->field_type,4)} = $original->{substr($ft->field_type,4)};
				} else {
					$row->{'link_'.substr($ft->field_type,4)} = $original->{'link_'.substr($ft->field_type,4)};
				}
			}
		}

		# Load original custom field values, for use in mosetstree plugins
		$sql="SELECT cf_id, value FROM #__mt_cfvalues WHERE link_id='".$row->link_id."' AND attachment <= 0";
		if( !empty($hidden_cfs) ) {
			$sql .= " AND cf_id NOT IN (" . implode(',',$hidden_cfs) . ")";
		}
		$database->setQuery($sql);
		$original_cfs = $database->loadAssocList('cf_id');
		if( !empty($original_cfs) )
		{
			foreach( $original_cfs AS $key_cf_id => $value )
			{
				$original_cfs[$key_cf_id] = $value['value'];
			}
		}
		
		# Erase all listing associations 
		$transaction_queries[] = "DELETE FROM #__mt_links_associations "
			.	"\n WHERE link_id2 = " . $database->Quote($row->link_id)
			.	"\n LIMIT 1 ";

		# Erase Previous Records, make way for the new data
		$sql="DELETE FROM #__mt_cfvalues WHERE link_id='".$row->link_id."' AND attachment <= 0";
		if( !empty($hidden_cfs) ) {
			$sql .= " AND cf_id NOT IN (" . implode(',',$hidden_cfs) . ")";
		}
		$transaction_queries[] = $sql;

		if( !empty($fieldtype) ) {
			$load_ft = array();
			foreach( $fieldtype AS $ft ) {
				$class_name = 'mFieldType_' . $ft->field_type;
				if( !class_exists($class_name) ) {
					$fieldtype_file = JPATH_ROOT . $mtconf->get('relative_path_to_fieldtypes') . $ft->field_type . '/'  . $ft->field_type . '.php';
					if( JFile::exists($fieldtype_file) )
					{
						require_once $fieldtype_file;
					}
				}
			}
		}

		# Collect all active custom field's id
		$active_cfs = array();
		$additional_cfs = array();
		$core_params = array();
		foreach($post AS $k => $v) {
			if( in_array($k,array('alias', 'link_hits', 'link_votes', 'link_rating', 'link_featured', 'link_created', 'link_visited', 'ordering', 'publish_down', 'publish_up', 'attribs', 'internal_notes', 'link_published', 'link_approved', 'metadesc', 'metakey')) ) {
				continue;
			}

			$v = JFactory::getApplication()->input->get( $k, '', 'RAW' );

			if ( substr($k,0,2) == "cf" && ( (!is_array($v) && (!empty($v) || $v == '0')) || (is_array($v) && !empty($v[0])) ) ) {
				if(strpos(substr($k,2),'_') === false && is_numeric(substr($k,2))) {
					// This custom field uses only one input. ie: cf17, cf23, cf2
					$active_cfs[intval(substr($k,2))] = $v;
					if( is_array($v) && array_key_exists(intval(substr($k,2)),$original_cfs) ) {
						$original_cfs[intval(substr($k,2))] = explode('|',$original_cfs[intval(substr($k,2))]);
						
					}
				} else {
					// This custom field uses more than one input. The date field is an example of cf that uses this. ie: cf13_0, cf13_1, cf13_2
					$ids = explode('_',substr($k,2));
					if(count($ids) == 2 && is_numeric($ids[0]) && is_numeric($ids[1]) ) {
						$additional_cfs[intval($ids[0])][intval($ids[1])] = $v;
					}
				}
			} elseif( substr($k,0,7) == 'keep_cf' ) {
				$cf_id = intval(substr($k,7));
				$keep_att_ids[] = $cf_id;

			# Perform parseValue on Core Fields
			} elseif( substr($k,0,2) != "cf" && isset($row->{$k}) ) {
				if(strpos(strtolower($k),'link_') === false) {
					$core_field_type = 'core' . $k;
				} else {
					$core_field_type = 'core' . str_replace('link_','',$k);
				}
				$class = 'mFieldType_' . $core_field_type;

				if(class_exists($class))
				{
					if(empty($core_params))
					{
						$database->setQuery('SELECT field_type, required_field, params FROM #__mt_customfields WHERE iscore = 1');
						$core_params = $database->loadObjectList('field_type');
					}
					$mFieldTypeObject = new $class(array('requiredField'=>$core_params[$core_field_type]->required_field,  'params'=>$core_params[$core_field_type]->params));
					$v = call_user_func(array(&$mFieldTypeObject, 'parseValue'),$v);
					$row->{$k} = $v;
					
					if( 
						( $mFieldTypeObject->isRequired() || !empty($v) )
						&&
						!call_user_func(array(&$mFieldTypeObject, 'validateValue'),$v) 
					)
					{
						$fields_validation = false;
						$error_msg = $mFieldTypeObject->getError();
						if( $error_msg != false )
						{
							array_push($fields_validation_errors, $error_msg);
						}
					}
					else
					{
						// Validation is passed. Store this to $user_fields_data
						$user_fields_data[$k] = $v;
					}
				}
			}
		}

		// All core fields validate. Commit database transactions.
		if( $fields_validation )
		{
			JFile::delete($files_to_be_removed);
			unset($files_to_be_removed);
			
			if( !empty($transaction_queries) )
			{
				foreach( $transaction_queries AS $transaction_query )
				{
					$database->setQuery($transaction_query);
					$database->execute();
				}
			}
		}
		// At least one core fields did not validate. Rollback transactions.
		else
		{
			// Since we are redirecting users back to edit screen
			// due to validation issue on core fields, we need to
			// loop through custom fields and pass it back as user 
			// state so that they are not left empty.
			foreach($post AS $k => $v)
			{
				if( substr($k,0,2) == "cf" || in_array($k,array('cat_id','other_cats','img_sort_hash')))
				{
					$user_fields_data[$k] = $v;
				}
			}

			$app->setUserState('com_mtree.editlisting.data', $user_fields_data);

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($fields_validation_errors); $i < $n && $i < 3; $i++) {
				if ($fields_validation_errors[$i] instanceof Exception) {
					$app->enqueueMessage($fields_validation_errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($fields_validation_errors[$i], 'warning');
				}
			}
			
			// Redirect back to the Edit Listing screen.
			$app->redirect(
				($isNew) ?
					JRoute::_(
						'index.php?option='.$option.'&task=addlisting&cat_id='.$cat_id.'&Itemid='.$itemid, 
						false
						)
					:
					JRoute::_(
						'index.php?option='.$option.'&task=editlisting&link_id='.$link_id.'&Itemid='.$itemid, 
						false
						)
				);
		}
		
		// Reset variables to continue checking on custom fields further down below
		$transaction_queries = array();
		$files_to_be_removed = array();
		$fields_validation = true;
		
		# At this point, all core fields are checked and validated.
		# (A)	If this is a new listing, we need to store the listing to 
		# 	#__mt_links to get a link_id to proceed further. 
		# (B)	If this is a modification to an existing listing, we delay
		#	the storage after we have validated the rest of the data,
		#	such as custom fields.
		if( $isNew )
		{
			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}			
		}
		if( !$isNew && $row->link_id > 0 ) {
			// Find if there are any additional categories assigned to the listinig
			if( $original_link_id <> $row->link_id ) {
				$database->setQuery( 'SELECT DISTINCT cat_id FROM #__mt_cl WHERE link_id = '.$database->Quote($original_link_id).' and main=\'0\' ' );
				$tmp_cats = $database->loadColumn();
				if( !empty($tmp_cats) ){
					foreach( $tmp_cats AS $tmp_cat_id ) {
						$transaction_queries[] = 'INSERT INTO #__mt_cl (`link_id`,`cat_id`,`main`) VALUES('.$database->Quote($row->link_id).','.$database->Quote($tmp_cat_id).',\'0\')';
					}
				}
				unset($tmp_cats);
			}
		}
		// }

		# Update "Also appear in these categories" aka other categories
		$cl_ids_to_be_assigned_with_link_id = array();
		
		if($mtconf->get('allow_user_assign_more_than_one_category'))
		{
			$mtCL = new mtCL_main0( $database );
			$mtCL->load( $row->link_id );
			$cl_insert_ids = $mtCL->update( $other_cats );

			if ( $mtconf->get('needapproval_modifylisting') )
			{
				$cl_ids_to_be_assigned_with_link_id = $cl_insert_ids;
			}
		}

		// $files_cfs is used to store attachment custom fields. 
		// This will be used in the next foreach loop to 
		// prevent it from storing it's value to #__mt_cfvalues 
		// table
		$file_cfs = array();

		// $file_values is used to store parsed data through 
		// mFieldType_* which will be done in the next foreach 
		// loop
		$file_values = array();

		$files = $_FILES;

		if( !empty($files) )
		{
			foreach($files AS $k => $v) {
				if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0) {
					$active_cfs[intval(substr($k,2))] = $v;
					$file_cfs[] = substr($k,2);
				}
			}
		}

		if( !empty($active_cfs) ) {

			$database->setQuery('SELECT cf_id, params, required_field FROM #__mt_customfields WHERE iscore = 0 AND cf_id IN (\'' . implode('\',\'',array_keys($active_cfs)). '\') LIMIT ' . count($active_cfs));
			$params = $database->loadObjectList('cf_id');

			foreach($active_cfs AS $cf_id => $v) {
				if(class_exists('mFieldType_'.$fieldtype[$cf_id]->field_type)) {
					$class = 'mFieldType_'.$fieldtype[$cf_id]->field_type;
				} else {
					$class = 'mFieldType';
				}

				$mFieldTypeObject = new $class(array('id'=>$cf_id,'requiredField'=>$params[$cf_id]->required_field,  'params'=>$params[$cf_id]->params,'linkId'=>$row->link_id));

				if( 
					( $mFieldTypeObject->isRequired() || !empty($v) )
					&&
					!call_user_func(array(&$mFieldTypeObject, 'validateValue'),$v) 
				)
				{
					$fields_validation = false;
					$error_msg = $mFieldTypeObject->getError();
					if( $error_msg != false )
					{
						array_push($fields_validation_errors, $error_msg);
					}
				}
				else
				{
					// Validation is passed. Store this to $user_fields_data
					$user_fields_data['cf'.$cf_id] = $v;
				}
				
				# Perform parseValue on Custom Fields
				if(array_key_exists($cf_id,$additional_cfs) && !empty($additional_cfs[$cf_id]) ) {
					$arr_v = $additional_cfs[$cf_id];
					array_unshift($arr_v, $v);
					$v = &$mFieldTypeObject->parseValue($arr_v);
					$active_cfs[$cf_id] = $v;
				} else {
					$v = &$mFieldTypeObject->parseValue($v);
				}
				
				if(in_array($cf_id,$file_cfs)) {
					$file_values[$cf_id] = $v;
				}

				if( (!empty($v) || $v == '0') && !in_array($cf_id,$file_cfs)) {
					# -- Now add the row
					if( $mtconf->get('needapproval_modifylisting') )
					{
						$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
							. ' VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($link_id_symbol) . ', ' . $database->quote((is_array($v)) ? implode("|",$v) : $v) . ')';
					}
					else
					{
						$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
							. ' VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote((is_array($v)) ? implode("|",$v) : $v) . ')';
					}
					$transaction_queries[] = $sql;
				}
				unset($mFieldTypeObject);
			} // End of foreach
		}
		
		# If this link is pending approval for modification, copy over hidden values
		if ( !$isNew && $mtconf->get('needapproval_modifylisting') && !empty($hidden_cfs) ) {
			$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
				. ' SELECT `cf_id`, \'' . $row->link_id . '\', `value` FROM #__mt_cfvalues WHERE link_id = ' . $original_link_id . ' AND cf_id IN (' . implode(',',$hidden_cfs) . ')';
			$transaction_queries[] = $sql;
		}
		
		# Remove all attachment except those that are kept
		$removed_attachments = array();
		
		$cf_ids_to_be_removed_if_validate = array();
		$att_ids_to_be_removed_if_validate = array();
		
		if(isset($keep_att_ids) && !empty($keep_att_ids) ) {
			$database->setQuery( 'SELECT cf_id, raw_filename FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')');
			$tmp_raw_filenames = $database->loadObjectList();

			$i=0;
			foreach($tmp_raw_filenames AS $tmp_raw_filename)
			{
				$removed_attachments[$tmp_raw_filename->cf_id] = $tmp_raw_filename->raw_filename;
				$raw_filenames[$i] = JPATH_SITE.$mtconf->get('relative_path_to_attachments') . $tmp_raw_filename->raw_filename;
				$i++;
			}

			$database->setQuery('SELECT att_id FROM #__mt_cfvalues_att WHERE link_id = \'' . $row->link_id . '\' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')');
			$att_ids_to_be_removed_if_validate = array_merge($att_ids_to_be_removed_if_validate, $database->loadColumn());
			
			$database->setQuery('SELECT id FROM #__mt_cfvalues WHERE link_id = \'' . $row->link_id . '\' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\') AND attachment > 0');
			$cf_ids_to_be_removed_if_validate = array_merge($cf_ids_to_be_removed_if_validate, $database->loadColumn());
			
		} else {
			$database->setQuery( 'SELECT cf_id, raw_filename FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) );
			$tmp_raw_filenames = $database->loadObjectList();
			
			$i=0;
			foreach($tmp_raw_filenames AS $tmp_raw_filename)
			{
				$removed_attachments[$tmp_raw_filename->cf_id] = $tmp_raw_filename->raw_filename;
				$raw_filenames[$i] = JPATH_SITE.$mtconf->get('relative_path_to_attachments') . $tmp_raw_filename->raw_filename;
				$i++;
			}

			$database->setQuery('SELECT att_id FROM #__mt_cfvalues_att WHERE link_id = \'' . $row->link_id . '\'');
			$tmp_ids = $database->loadColumn();
			if( !empty($tmp_ids) )
			{
				$att_ids_to_be_removed_if_validate = array_merge($att_ids_to_be_removed_if_validate, $tmp_ids);
				unset($tmp_ids);
			}

			$database->setQuery('SELECT id FROM #__mt_cfvalues WHERE link_id = \'' . $row->link_id . '\' AND attachment > 0');
			$tmp_ids = $database->loadColumn();
			if( !empty($tmp_ids) )
			{
				$cf_ids_to_be_removed_if_validate = array_merge($cf_ids_to_be_removed_if_validate, $tmp_ids);
				unset($tmp_ids);
			}
		}

		$att_ids_to_be_assigned_with_link_id = array();
		$cf_ids_to_be_assigned_with_link_id = array();

		// If this is a modification sent for approval, copies of attachments
		// that are kept and not overrided will be copied over to the new temporary listing.
		// An attachments can be uploaded as a new file ($file_cfs), so these attachments
		// are not copied over.
		
		$att_ids_that_needs_to_be_copid_for_temp_link = array_diff($keep_att_ids,$file_cfs);

		if(!$isNew && isset($att_ids_that_needs_to_be_copid_for_temp_link) && !empty($att_ids_that_needs_to_be_copid_for_temp_link) && $mtconf->get('needapproval_modifylisting') && $row->link_published == 1) {

			$database->setQuery( "SELECT * FROM #__mt_cfvalues_att WHERE link_id = '" . $original_link_id . "' AND cf_id IN ('" . implode("','",$att_ids_that_needs_to_be_copid_for_temp_link) . "')" );
			$listing_atts = $database->loadObjectList();

			foreach($listing_atts AS $listing_att) {
				$file_extension = pathinfo($listing_att->raw_filename);
				$file_extension = strtolower($file_extension['extension']);
			
				$database->setQuery( 
					'INSERT INTO #__mt_cfvalues_att (`link_id`,`cf_id`,`raw_filename`,`filename`,`filesize`,`extension`) '
					. 'VALUES (' . $database->Quote($row->link_id) . ', ' . $database->Quote($listing_att->cf_id). ', ' . $database->Quote($listing_att->raw_filename). ', ' . $database->Quote($listing_att->filename). ', ' . $database->Quote($listing_att->filesize). ', ' . $database->Quote($listing_att->extension). ')' );
				$database->execute();
				$att_id = $database->insertid();
				$att_ids_to_be_removed[] = $att_id;
				
				// needapproval_modifylisting is true, so, there is no link_id yet. The above query
				// will assign link_id = 0. The following codes will store this att_id and assign a 
				// link_id later in the execution when fields_validation is true.
				$att_ids_to_be_assigned_with_link_id[] = $att_id;
				
				$database->setQuery( 
					'INSERT INTO #__mt_cfvalues (`cf_id`,`link_id`,`value`,`attachment`) '
					. 'VALUES (' . $database->Quote($listing_att->cf_id). ', ' . $database->Quote($row->link_id) . ', ' . $database->Quote($listing_att->filename). ', 1)' );
				$database->execute();
				$cf_ids_to_be_assigned_with_link_id[] = $database->insertid();
				$cf_ids_to_be_removed[] = $database->insertid();

				$transaction_queries[] = 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $database->Quote($att_id . '.' . $file_extension) . ' WHERE att_id = ' . $database->Quote($att_id) . ' LIMIT 1';

				$files_to_be_copied[] = array(
					$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $listing_att->raw_filename,
					$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $att_id . "." . $file_extension 
				);
			}
		}

		jimport('joomla.filesystem.file');

		unset($raw_filenames);
		$raw_filenames = array();

		if( !empty($files) )
		{
			foreach($files AS $k => $v)
			{
				if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0 )
				{
					$cf_id = intval(substr($k,2));

					// If file is empty, it is probably been stripped through field type's parsevalue
					if( empty($file_values[$cf_id]) )
					{
						continue;
					}

					$file_extension = pathinfo($file_values[$cf_id]['name']);
					$file_extension = strtolower($file_extension['extension']);

					// Prevents certain file types from being uploaded. Defaults to prevent PHP file (php)
					if( in_array($file_extension,explode(',',$mtconf->get('banned_attachment_filetypes'))) ) {
						continue;
					}

					if(array_key_exists($cf_id,$file_values)) {
						$file = $file_values[$cf_id];
						if(!empty($file['data'])) {
							$data = $file['data'];
						} else {
							$fp = fopen($v['tmp_name'], "r");
							$data = fread($fp, $v['size']);
							fclose($fp);
						}
					} else {
						$file = $v;
						$fp = fopen($v['tmp_name'], "r");
						$data = fread($fp, $v['size']);
						fclose($fp);
					}

					$database->setQuery('SELECT att_id FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id = ' . $database->quote($cf_id));
					$tmp_ids = $database->loadColumn();
					if( !empty($tmp_ids) )
					{
						$att_ids_to_be_removed_if_validate = array_merge($att_ids_to_be_removed_if_validate, $tmp_ids);
						unset($tmp_ids);
					}

					$database->setQuery('SELECT id FROM #__mt_cfvalues WHERE cf_id = ' . $database->quote($cf_id) . ' AND link_id = ' . $database->quote($row->link_id) . ' AND attachment > 0' );
					$tmp_ids = $database->loadColumn();
					if( !empty($tmp_ids) )
					{
						$cf_ids_to_be_removed_if_validate = array_merge($cf_ids_to_be_removed_if_validate, $tmp_ids);
						unset($tmp_ids);
					}

					$database->setQuery( 'INSERT INTO #__mt_cfvalues_att (link_id, cf_id, raw_filename, filename, filesize, extension) '
						. ' VALUES('
						. $database->quote($row->link_id) . ', '
						. $database->quote($cf_id) . ', '
						. $database->quote($file['name']) . ', '
						. $database->quote($file['name']) . ', '
						. $database->quote($file['size']) . ', '
						. $database->quote($file['type']) . ')'
						);

					if($database->execute() !== false) {
						$att_id = $database->insertid();
						$att_ids_to_be_removed[] = $att_id;
						$att_ids_to_be_assigned_with_link_id[] = $att_id;

						$file_extension = strrchr($file['name'],'.');
						if( $file_extension === false ) {
							$file_extension = '';
						}

						if(JFile::write( JPATH_SITE.$mtconf->get('relative_path_to_attachments').$att_id.$file_extension, $data ))
						{
							$transaction_queries[] = 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $database->quote($att_id . $file_extension) . ' WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1';

							$database->setQuery('INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`, `attachment`) '
								. 'VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote($file['name']) . ',1)');
							$database->execute();
							$cf_ids_to_be_removed[] = $database->insertid();
							$cf_ids_to_be_assigned_with_link_id[] = $database->insertid();

						} else {
							// Move failed, remove record from previously INSERTed row in #__mt_cfvalues_att
							$transaction_queries[] = 'DELETE FROM #__mt_cfvalues_att WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1';
						}
					}
				}
			}
		}
		
		if( !empty($raw_filenames) )
		{
			$files_to_be_removed = array_merge($files_to_be_removed,$raw_filenames);
		}

		if(
			$mtconf->get('allow_imgupload')
			||
			(!$mtconf->get('allow_imgupload') && $mtconf->get('needapproval_modifylisting'))
		) {
			
			if($mtconf->get('allow_imgupload')) {
				$keep_img_ids = JFactory::getApplication()->input->get( 'keep_img', '', 'ARRAY' );
				JArrayHelper::toInteger($keep_img_ids, array());

			// If image upload is disabled, it will get the image IDs from database and make sure 
			// the images are not lost after approval
			} else {
				$database->setQuery('SELECT img_id FROM #__mt_images WHERE link_id = ' . $database->quote($original_link_id) );
				$keep_img_ids = $database->loadColumn();
			}
			
			$img_ids_to_be_removed = array();
			$img_ids_to_be_assigned_with_link_id = array();
			
			$redirectMsg = '';
			if(is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_small_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_medium_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_original_image'))) {

				// Duplicate listing images for approval
				if(!$isNew && $row->link_approved && !empty($keep_img_ids) && is_array($keep_img_ids) && $mtconf->get('needapproval_modifylisting')) {
					foreach($keep_img_ids AS $keep_img_id) {

						$database->setQuery('SELECT * FROM #__mt_images WHERE link_id = ' . $database->quote($original_link_id) . ' AND img_id = ' . $database->quote($keep_img_id) . ' LIMIT 1');
						$original_image = $database->loadObject();

						$pathinfo = pathinfo($original_image->filename);
						$file_extension = strtolower($pathinfo['extension']);

						$database->setQuery('INSERT INTO #__mt_images (link_id,filename,ordering) '
							.	"\n VALUES ('" . $row->link_id . "', '" . $pathinfo['filename'] . "_.".$pathinfo['extension']."', '" . $original_image->ordering . "')");
						$database->execute();

						// needapproval_modifylisting is true, so, there is no link_id yet. The above query
						// will assign link_id = 0. The following codes will store this img_id and assign a 
						// link_id later in the execution when fields_validation is true.
						$img_ids_to_be_assigned_with_link_id[] = $database->insertid();

						$new_img_ids[$keep_img_id] = $database->insertid();
						$img_ids_to_be_removed[] = $new_img_ids[$keep_img_id];
						
						$transaction_queries[] = "UPDATE #__mt_images SET filename = '" . $new_img_ids[$keep_img_id] .  '_.' . $pathinfo['extension'] . "' WHERE img_id = '" . $new_img_ids[$keep_img_id] . "' LIMIT 1";

						$files_to_be_copied[] = array( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $new_img_ids[$keep_img_id] .  '_.' . $pathinfo['extension'] );
						$files_to_be_copied[] = array( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $new_img_ids[$keep_img_id] .  '_.' . $pathinfo['extension'] );
						$files_to_be_copied[] = array( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $new_img_ids[$keep_img_id] .  '_.' . $pathinfo['extension'] );

					}
				}
		
				# Remove all images except those that are kept when modification does not require approval
				$image_filenames = array();
				
				// Store an array of img_ids that needs to be removed upon validation.
				$img_ids_to_be_removed_if_validate = array();
				
				if(
					!$mtconf->get('needapproval_modifylisting')
					||
					// Expression below allow modification to take effect immediately without going through approval when:
					//
					// 1) needapproval_modifylisting is set to Yes
					// 2) needapproval_addlisting is set to Yes
					// 3) Link is still awaiting approval
					//
					// A listing satisfying the above requirements is already in the approval queue for the initial 
					// submission. We want to allow subsequent modification to apply immediately because it would
					// be confusing to have 2 items in the awaiting approval queue for the same listing (one for 
					// the initial submission and another for the modification on a listing awaiting approval)
					(
						$mtconf->get('needapproval_modifylisting') && $mtconf->get('needapproval_addlisting') && !$row->link_approved
					)
				) {
					if(isset($keep_img_ids) && !empty($keep_img_ids)) {
						$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')' );
						$image_filenames = $database->loadColumn();
						
						$database->setQuery('SELECT img_id FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')');
						$tmp_img_ids = $database->loadColumn();
						if( !empty($tmp_img_ids) )
						{
							$img_ids_to_be_removed_if_validate = array_merge($img_ids_to_be_removed, $tmp_img_ids);
						}
						
					} else {
						$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\'' );
						$image_filenames = $database->loadColumn();

						$database->setQuery('SELECT img_id FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\'');
						$tmp_img_ids = $database->loadColumn();
						if( !empty($tmp_img_ids) )
						{
							$img_ids_to_be_removed_if_validate = array_merge($img_ids_to_be_removed, $tmp_img_ids);
						}
					}
				}
				if(!empty($image_filenames)) {
					foreach($image_filenames AS $image_filename) {
						$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $image_filename;
						$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $image_filename;
						$files_to_be_removed[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $image_filename;
					}
				}
				
				$files_exceed_limit = 0;
				$files_failed_minimum_dimension = 0;
				
				if( isset($files['image']) )
				{
					for($i=0;$i<count($files['image']['name']) && ($i<($mtconf->get('images_per_listing') - count($keep_img_ids)) || $mtconf->get('images_per_listing') == '0');$i++)
					{
						$image_dimension = getimagesize($files['image']['tmp_name'][$i]);

						if ( 
							$mtconf->get('image_maxsize') > 0 
							&& 
							$files['image']['size'][$i] > $mtconf->get('image_maxsize') 
						) {
							// Uploaded file exceed file limit
							$files_exceed_limit++;
						} elseif ( 
							(
								$mtconf->get('image_min_width') > 0 
								&&
								$image_dimension[0] < $mtconf->get('image_min_width')
							)
							||
							(
								$mtconf->get('image_min_height') > 0 
								&&
								$image_dimension[1] < $mtconf->get('image_min_height')
							)
							
						) {
							// Uploaded file does not meet minimum height or width or both.
							$files_failed_minimum_dimension++;
							
						} elseif ( 
							!empty($files['image']['name'][$i]) 
							&& 
							$files['image']['error'][$i] == 0 
							&&  
							$files['image']['size'][$i] > 0 
							) 
						{
							$file_extension = pathinfo($files['image']['name'][$i]);
							$file_extension = strtolower($file_extension['extension']);
							if( !in_array($file_extension,array('png','gif','jpg','jpeg')) ) {
								continue;
							}
							$mtImage = new mtImage();
							$mtImage->setMethod( $mtconf->get('resize_method') );
							$mtImage->setQuality( $mtconf->get('resize_quality') );
							$mtImage->setSize( $mtconf->get('resize_small_listing_size') );
							$mtImage->setTmpFile( $files['image']['tmp_name'][$i] );
							$mtImage->setType( $files['image']['type'][$i] );
							$mtImage->setName( $files['image']['name'][$i] );
							$mtImage->setSquare( $mtconf->get('squared_thumbnail') );

							if( !$mtImage->resize() )
							{
								continue;
							}
							
							$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') );
							$mtImage->saveToDirectory();
							
							$mtImage->setSize( $mtconf->get('resize_medium_listing_size') );
							$mtImage->setSquare(false);
							$mtImage->resize();
							$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') );
							$mtImage->saveToDirectory();
							move_uploaded_file($files['image']['tmp_name'][$i],$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i]);

							$database->setQuery( 'INSERT INTO #__mt_images (link_id, filename, ordering) '
								. ' VALUES(' . $database->quote($row->link_id) . ', ' . $database->quote($files['image']['name'][$i]) . ', \'9999\')');
							$database->execute();

							$img_id = $database->insertid();
							$img_ids_to_be_removed[] = $img_id;
							
							// If needapproval_modifylisting is true, there is no link_id yet, so the above query
							// will assign link_id = 0. The following codes will store this img_id and assign a 
							// link_id later in the execution when fields_validation is true.
							if ( $mtconf->get('needapproval_modifylisting') ) {
								$img_ids_to_be_assigned_with_link_id[] = $img_id;
							}

							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $img_id . '.' . $file_extension);
							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $img_id . '.' . $file_extension);
							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $img_id . '.' . $file_extension);

							$files_to_be_removed_if_invalid[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $img_id . '.' . $file_extension;
							$files_to_be_removed_if_invalid[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $img_id . '.' . $file_extension;
							$files_to_be_removed_if_invalid[] = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $img_id . '.' . $file_extension;

							$transaction_queries[] = 'UPDATE #__mt_images SET filename = ' . $database->quote($img_id . '.' . $file_extension) . ' WHERE img_id = ' . $database->quote($img_id);
						}
					}
				}
		
				if( $files_exceed_limit )
				{
					if( $mtconf->get('image_maxsize') > 1048576 ) {
						$image_upload_limit = round($mtconf->get('image_maxsize')/1048576) . 'MB';
					} else {
						$image_upload_limit = round($mtconf->get('image_maxsize')/1024) . 'KB';
					}
					JError::raise(
						E_NOTICE, 
						500, 
						JText::plural( 
							'COM_MTREE_IMAGE_IS_NOT_SAVED_BECAUSE_IT_EXCEEDED_FILE_SIZE_LIMIT', 
							$files_exceed_limit,
							$image_upload_limit
						)
					);
				}

				if( $files_failed_minimum_dimension > 0 )
				{
					JError::raise(
						E_NOTICE, 
						500, 
						JText::plural( 
							'COM_MTREE_IMAGE_FAILED_MINIMUM_DIMENSION_REQUIREMENT', 
							$files_failed_minimum_dimension, 
							$mtconf->get('image_min_width'), 
							$mtconf->get('image_min_height') 
						)
					);
				}
				
				$img_sort_hash = JFactory::getApplication()->input->get( 'img_sort_hash' );
				
				if(!empty($img_sort_hash))
				{
					parse_str($img_sort_hash,$arr_img_sort_hashes);
					$i=1;
					if(!empty($arr_img_sort_hashes['img']))
					{
						foreach($arr_img_sort_hashes['img'] AS $arr_img_sort_hash)
						{
							if(!empty($arr_img_sort_hash) && $arr_img_sort_hash > 0)
							{
								$sql = 'UPDATE #__mt_images SET ordering = ' . $database->quote($i) . ' WHERE img_id = ';
								if(isset($new_img_ids) && !empty($new_img_ids))
								{
									$sql .= $database->quote(intval($new_img_ids[$arr_img_sort_hash]));
								}
								else
								{
									$sql .= $database->quote(intval($arr_img_sort_hash));
								}
								$sql .= ' LIMIT 1';
								$transaction_queries[] = $sql;
								$i++;
							}
						}
					}
				}
				
				$images = new mtImages( $database );
				$images->reorder('link_id='.(isset($row->link_id)?$row->link_id:$original_link_id));
			} else {
				if( isset($files['image']) ) {
					$redirectMsg .= JText::_( 'COM_MTREE_IMAGE_DIRECTORIES_NOT_WRITABLE' );
				}
			}

		}

		// All custom fields validate. Commit database transactions.
		if( $fields_validation )
		{
			// Store core fields data.
			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}			
			
			// Remove img_ids
			if( !empty($img_ids_to_be_removed_if_validate) )
			{
				$database->setQuery( 'DELETE FROM #__mt_images WHERE img_id IN (' . implode(', ',$img_ids_to_be_removed_if_validate) . ')' );
				$database->execute();
			}
			
			// Assign link_id to imd_ids
			if( !empty($img_ids_to_be_assigned_with_link_id) )
			{
				$database->setQuery( 'UPDATE #__mt_images SET link_id = '.$row->link_id.' WHERE img_id IN (' . implode(', ',$img_ids_to_be_assigned_with_link_id) . ')' );
				$database->execute();
			}
			
			// Assign link_id to cl_ids
			if( !empty($cl_ids_to_be_assigned_with_link_id) )
			{
				$database->setQuery( 'UPDATE #__mt_cl SET link_id = '.$row->link_id.' WHERE cl_id IN (' . implode(', ',$cl_ids_to_be_assigned_with_link_id) . ')' );
				$database->execute();
			}

			// Assign link_id to cf_ids
			if( !empty($cf_ids_to_be_assigned_with_link_id) )
			{
				$database->setQuery( 'UPDATE #__mt_cfvalues SET link_id = '.$row->link_id.' WHERE id IN (' . implode(', ',$cf_ids_to_be_assigned_with_link_id) . ')' );
				$database->execute();
			}
			
			// Assign link_id to att_ids
			if( !empty($att_ids_to_be_assigned_with_link_id) )
			{
				$database->setQuery( 'UPDATE #__mt_cfvalues_att SET link_id = '.$row->link_id.' WHERE att_id IN (' . implode(', ',$att_ids_to_be_assigned_with_link_id) . ')' );
				$database->execute();
			}
			
			// Remove cf_ids
			if( !empty($cf_ids_to_be_removed_if_validate) )
			{
				$database->setQuery( 'DELETE FROM #__mt_cfvalues WHERE id IN (' . implode(', ',$cf_ids_to_be_removed_if_validate) . ')' );
				$database->execute();
			}
			
			// Remove att_ids
			if( !empty($att_ids_to_be_removed_if_validate) )
			{
				$database->setQuery( 'DELETE FROM #__mt_cfvalues_att WHERE att_id IN (' . implode(', ',$att_ids_to_be_removed_if_validate) . ')' );
				$database->execute();
			}
			
			JFile::delete($files_to_be_removed);
			unset($files_to_be_removed);
			
			if( !empty($files_to_be_copied) )
			{
				foreach($files_to_be_copied AS $file_to_be_copied)
				{
					JFile::copy($file_to_be_copied[0],$file_to_be_copied[1]);
				}
			}
			
			if( !empty($transaction_queries) )
			{
				foreach( $transaction_queries AS $transaction_query )
				{
					$transaction_query = str_ireplace( $link_id_symbol, $row->link_id, $transaction_query );
					$database->setQuery($transaction_query);
					$database->execute();
				}
			}
		}
		// At least one custom fields did not validate. Rollback transactions.
		else
		{
			$app->setUserState('com_mtree.editlisting.data', $user_fields_data);
			
			JFile::delete($files_to_be_removed_if_invalid);
			unset($files_to_be_removed_if_invalid);
			
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($fields_validation_errors); $i < $n && $i < 3; $i++) {
				if ($fields_validation_errors[$i] instanceof Exception) {
					$app->enqueueMessage($fields_validation_errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($fields_validation_errors[$i], 'warning');
				}
			}
			
			// Remove att_id
			if( !empty($att_ids_to_be_removed) )
			{
				foreach( $att_ids_to_be_removed AS $att_id_to_be_removed )
				{
					$database->setQuery( 'DELETE FROM #__mt_cfvalues_att WHERE att_id = ' . $database->Quote($att_id_to_be_removed) . ' LIMIT 1' );
					$database->execute();
				}
			}
			
			// Remove img_id
			if( !empty($img_ids_to_be_removed) )
			{
				foreach( $img_ids_to_be_removed AS $img_id_to_be_removed )
				{
					$database->setQuery( 'DELETE FROM #__mt_images WHERE img_id = ' . $database->Quote($img_id_to_be_removed) . ' LIMIT 1' );
					$database->execute();
				}
			}

			// Redirect back to the Edit Listing screen.
			if( $isNew )
			{
				$row->delLink();
				$app->redirect(
					JRoute::_(
						'index.php?option='.$option.'&task=addlisting&cat_id='.$cat_id.'&Itemid='.$itemid, 
						false
						)
					);
			}
			else
			{
				$app->redirect(
					JRoute::_(
						'index.php?option='.$option.'&task=editlisting&link_id='.$row->link_id.'&Itemid='.$itemid, 
						false
						)
					);
			}
		}
		$files_to_be_removed = array();
		$fields_validation = true;
		
		# Send e-mail notification to user/admin upon adding a new listing
		// Get owner's email
		if( $my->id > 0 ) {
			$database->setQuery( "SELECT email, name, username FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
			$author = $database->loadObject();
		} else {
			if( !empty($row->email) ) {
				$author->email = $row->email;
			} else {
				$author->email = JText::_( 'COM_MTREE_NOT_SPECIFIED' );
			}
			$author->username = JText::_( 'JNONE' );
			$author->name = JText::_( 'COM_MTREE_NON_REGISTERED_USER' );
		}

		$uri = JUri::getInstance();

		if ( $isNew ) {

			# To User
			if ( $mtconf->get('notifyuser_newlisting') == 1 && ( $my->id > 0 || 
					( !empty($author->email) && (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $author->email )==true))
	
			) ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = JText::sprintf( 'COM_MTREE_NEW_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL', $row->link_name);
					$msg = JText::_( 'COM_MTREE_NEW_LISTING_EMAIL_MSG_WAITING_APPROVAL' );
				} else {
					$subject = JText::sprintf( 'COM_MTREE_NEW_LISTING_EMAIL_SUBJECT_APPROVED', $row->link_name);
					$msg = JText::sprintf( 'COM_MTREE_NEW_LISTING_EMAIL_MSG_APPROVED', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"),$mtconf->getjconf('fromname'));
				}

				JFactory::getMailer()->sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $author->email, $subject, wordwrap($msg) );
			}

			# To Admin
			if ( $mtconf->get('notifyadmin_newlisting') == 1 ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = JText::sprintf( 'COM_MTREE_NEW_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL', $row->link_name);
					$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_LISTING_MSG_WAITING_APPROVAL', $row->link_name, $row->link_name, $row->link_id, $author->name, $author->username, $author->email);
				} else {
					$subject = JText::sprintf( 'COM_MTREE_NEW_LISTING_EMAIL_SUBJECT_APPROVED', $row->link_name);
					$msg = JText::sprintf( 'COM_MTREE_ADMIN_NEW_LISTING_MSG_APPROVED', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $author->username, $author->email);
				}

				mosMailToAdmin( $subject, $msg );

			}

		}

		# Send e-mail notification to user/admin upon modifying an existing listing
		# E-mail is sent for modifying published extension. Unpublished extension means that they are pending approval
		# and we don't want to know about the changes during this time.
		elseif( $row->link_published == 1 ) {

			# To User
			if ( $mtconf->get('notifyuser_modifylisting') == 1 && $my->id > 0 ) {
				
				if ( $row->link_approved < 0 ) {
					$subject = JText::sprintf( 'COM_MTREE_MODIFY_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL', $row->link_name);
					$msg = JText::sprintf( 'COM_MTREE_MODIFY_LISTING_EMAIL_MSG_WAITING_APPROVAL', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid") );
				} else {
					$subject = JText::sprintf( 'COM_MTREE_MODIFY_LISTING_EMAIL_SUBJECT_APPROVED', $row->link_name);
					$msg = JText::sprintf( 'COM_MTREE_MODIFY_LISTING_EMAIL_MSG_APPROVED', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"),$mtconf->getjconf('fromname'));
				}

				JFactory::getMailer()->sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $author->email, $subject, wordwrap($msg) );
			}

			# To Admin
			if ( $mtconf->get('notifyadmin_modifylisting') == 1 ) {

				$diff_desc = diff_main( $original->link_desc, $row->link_desc, true );
				diff_cleanup_semantic($diff_desc);
				$diff_desc = diff_prettyhtml( $diff_desc );

				$msg = "<style type=\"text/css\">\n";
				$msg .= "ins{text-decoration:underline}\n";
				$msg .= "del{text-decoration:line-through}\n";
				$msg .= "</style>";

				if ( $row->link_approved < 0 ) {
					
					$subject = JText::sprintf( 'COM_MTREE_MODIFY_LISTING_EMAIL_SUBJECT_WAITING_APPROVAL', $row->link_name);
					$msg .= nl2br(JText::sprintf( 'COM_MTREE_ADMIN_MODIFY_LISTING_MSG_WAITING_APPROVAL', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, ($row->link_approved * -1), $author->name, $author->username, $author->email, $diff_desc));

				} else {

					$subject = sprintf(JText::_( 'COM_MTREE_MODIFY_LISTING_EMAIL_SUBJECT_APPROVED' ), $row->link_name);
					$msg .= nl2br(JText::sprintf( 'COM_MTREE_ADMIN_MODIFY_LISTING_MSG_APPROVED', $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, ($row->link_approved * -1), $author->name, $author->username, $author->email, $diff_desc));

				}

				mosMailToAdmin( $subject, $msg, 1 );
			}

		}
		
		// Log new listing submission
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		if($isNew)
		{
			$mtLog = new mtLog( $database, $remote_addr, $my->id, $row->link_id );
			$mtLog->logSaveListingNew();
		} else {
			$mtLog = new mtLog( $database, $remote_addr, $my->id, $row->link_id );
			$mtLog->logSaveListingExisting();
		}
		
		// Fire mosetstree onAfterModifyListing plugin
		$dispatcher 	= & JDispatcher::getInstance();
		if( isset($original) )
		{
			JPluginHelper::importPlugin('mosetstree');
			$dispatcher->trigger('onAfterModifyListing', array((array)$original,$original_cfs,(array)$row,$active_cfs, $removed_attachments, $old->link_id, $cat_id) );
		}
		
		// Fire finder plugin
		if( 
			( $isNew && $row->link_approved && $row->link_published )
			||
			( !$isNew && !$mtconf->get('needapproval_modifylisting') )
			)
		{
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentAfterSave', array('com_mtree.listing', new JObject(array('link_id' => $row->link_id)), $isNew));
			
		}
		
		if( isset($original) && $original->link_published && $original->link_approved )
		{
			if( ($isNew && $mtconf->get('needapproval_addlisting')) ) {
				$redirect_url = "index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=$Itemid";
			} elseif (!$isNew && $mtconf->get('needapproval_modifylisting')) {
				$redirect_url = "index.php?option=$option&task=viewlink&link_id=$old->link_id&Itemid=$Itemid";
			} else {
				$redirect_url = "index.php?option=$option&task=viewlink&link_id=$row->link_id&Itemid=$Itemid";
			} 
		} else {
			if( $my->id > 0 ) {
				$redirect_url = "index.php?option=$option&task=mypage&Itemid=$Itemid";
			} else {
				$redirect_url = "index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=$Itemid";
			}
		}

		$app->setUserState('com_mtree.editlisting.data', null);
		// /*
		$app->redirect( 
			($app->getCfg('sef')?JRoute::_($redirect_url):$redirect_url),
			(
				($isNew) ? ( 
					($mtconf->get('needapproval_addlisting')) 
					? 
					JText::_( 'COM_MTREE_LISTING_WILL_BE_REVIEWED' ) 
					: 
					JText::_( 'COM_MTREE_LISTING_HAVE_BEEN_ADDED' )
				) 
				: 
				( 
					($mtconf->get('needapproval_modifylisting')) 
					? 
					JText::_( 'COM_MTREE_LISTING_MODIFICATION_WILL_BE_REVIEWED' ) 
					: 
					JText::_( 'COM_MTREE_LISTING_HAVE_BEEN_UPDATED' ) 
				) 
			)
			.
			(!empty($redirectMsg)?'<br /> '.$redirectMsg:'') 
		);
		// */
	}
}

/***
* Add Category
*/
function addcategory( $option ) {
	global $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	
	# Get cat_id / link_id
	$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);
	$link_id	= JFactory::getApplication()->input->getInt('link_id', 0);

	if ( $cat_id == 0 && $link_id > 0 ) {
		$database->setQuery( "SELECT cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id = cl.link_id AND cl.main = '1' AND link_id ='".$link_id."'" );
		$cat_parent = $database->loadResult();
	} else {
		$cat_parent = $cat_id;
	}

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_parent."' LIMIT 1" );
	$cat_name = $database->loadResult();

	setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_ADD_CAT', $cat_name));

	# Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonVar($savant);
	$savant->assign('pathway', $pathWay);
	$savant->assign('cat_parent', $cat_parent);

	if ( $mtconf->get('user_addcategory') == '1' && $my->id < 1 ) {
		# Error. Please login before you can add category
		JFactory::getApplication('site')->redirect(
			JRoute::_(
				'index.php?option=com_users&view=login&return='
				. base64_encode( 
					JRoute::_('index.php?option=com_mtree&task=addcategory&cat_id='.$cat_id) 
				)
			),
			JText::sprintf( 
				'COM_MTREE_PLEASE_LOGIN_BEFORE_ADDCATEGORY'
			)
		);
	} elseif( $mtconf->get('user_addcategory') == '-1' ) {
		# Add category is disabled
		JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
	} else {
		# OK. User is allowed to add category
		$savant->display( 'page_addCategory.tpl.php' );
	}

}

function addcategory2( $option ) {
	global $Itemid, $mtconf;

	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();

	# Get cat_parent
	$cat_parent	= JFactory::getApplication()->input->getInt('cat_parent', 0);

	# Check if any malicious user is trying to submit link
	if ( $mtconf->get('user_addcategory') == 1 && $my->id <= 0 ) {
		return JError::raiseError(404,JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));

	} elseif( $mtconf->get('user_addcategory') == '-1' ) {
		# Add category is disabled
		JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

	} else {
		# Allowed
		// $post = JFactory::getApplication()->input->get( 'post', null, null );
		$post = $_POST;
		$row = new mtCats( $database );
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		# Assignment for new record
		$jdate		= JFactory::getDate();
		$row->cat_created = $now;
		$row->alias 	= JFilterOutput::stringURLSafe($row->cat_name);

		// Required approval
		if ( $mtconf->get('needapproval_addcategory') ) {
			$row->cat_approved = '0';
		} else {
			$row->cat_approved = 1;
			$row->cat_published = 1;
			$cache = &JFactory::getCache('com_mtree');
			$cache->clean();
		}

		# OK. Store new category into database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( !$mtconf->get('needapproval_addcategory')) {
			$row->updateLftRgt();
			$row->updateCatCount( 1 );
		}

		$row->updateFieldsMap();
		
		$app->redirect( JRoute::_("index.php?option=$option&task=listcats&cat_id=$cat_parent&Itemid=$Itemid"), ( ($mtconf->get('needapproval_addcategory')) ?  JText::_( 'COM_MTREE_CATEGORY_WILL_BE_REVIEWED' ) : JText::_( 'COM_MTREE_CATEGORY_HAVE_BEEN_ADDED' )) );

	}
}

function att_download( $ordering, $filename, $link_id, $cf_id, $img_id, $size ) {
	global $Itemid, $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	if( $link_id > 0 && $cf_id > 0) {
		// Retrieve attachment's record in database
		$database->setQuery('SELECT cfva.*, cf.*, l.* FROM #__mt_cfvalues_att AS cfva, #__mt_customfields AS cf, #__mt_links AS l '
			. ' WHERE'
			. ' cfva.cf_id = cf.cf_id'
			. ' AND cfva.link_id = l.link_id'
			. ' AND cfva.link_id = ' . $database->quote($link_id) 
			. ' AND cfva.cf_id = ' . $database->quote($cf_id) 
			. ' LIMIT 1'
			);
		$attachment = $database->loadObject();
		$attachment->filedata = null;
		
		// Checks permission. We want to make sure that no attachments can be downloaded when:
		// (1) listing is not approved or published
		// (2) custom field is set up NOT to show in details and summary view
		// In both cases above, only link owner and administrator will have access to the attachment.
		// This prevents unauthorized users from downloading the attachments by guessing the URL
		if( $my->id != $attachment->user_id && strpos(strtolower($my->usertype),'administrator') === false )
		{
			if( 
				($attachment->link_published <= 0 || $attachment->link_approved <= 0)
				||
				($attachment->details_view == 0 && $attachment->summary_view == 0) 
			) {
				// Access denied.
				$app->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'COM_MTREE_YOU_ARE_NOT_AUTHORIZED_TO_ACCESS_THIS_ATTACHMENT' ) );
			}
		}
		
		if( !is_null($attachment) ) {
			$filepath = JPATH_SITE.$mtconf->get('relative_path_to_attachments').$attachment->raw_filename;
			$handle = fopen($filepath, 'rb');
			
			$attachment->filedata = fread( $handle, $attachment->filesize );
			fclose( $handle );
		} else {
			// No such attachment exists. User redirected with error.
			$app->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'COM_MTREE_YOU_ARE_NOT_AUTHORIZED_TO_ACCESS_THIS_ATTACHMENT' ) );
		}

	} else {
		// Insufficient argument passed. User redirected with error.
		$app->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'COM_MTREE_YOU_ARE_NOT_AUTHORIZED_TO_ACCESS_THIS_ATTACHMENT' ) );
	}


	if (!empty($attachment) && !empty($attachment->filedata)) {
		
		// Increase the counter
		$database->setQuery( 'UPDATE #__mt_cfvalues SET counter = counter + 1 WHERE link_id = ' . $database->quote($link_id) . ' && cf_id = ' . $database->quote($cf_id) . ' LIMIT 1' );
		$database->execute();
		
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header("Content-type: ".$attachment->extension);
		if($attachment->filesize>0) {
			header("Content-length: ".$attachment->filesize);
		}
		header('Content-Disposition: inline; filename="'.$attachment->filename.'";');
		header('Content-transfer-encoding: binary');
		header("Connection: close");

		echo $attachment->filedata;
		
		die();
	} else {
		$app->redirect(JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'COM_MTREE_YOU_ARE_NOT_AUTHORIZED_TO_ACCESS_THIS_ATTACHMENT' ));
	}
}

function mosMailToAdmin( $subject, $body, $mode=0) {
	global $mtconf;

	if ( strpos($mtconf->get('admin_email'),',') === false ) {
		$recipient_emails = array($mtconf->get('admin_email'));
	} else {
		$recipient_emails = explode(',', $mtconf->get('admin_email'));
	}
	for($i=0;$i<count($recipient_emails);$i++) {
		$recipient_emails[$i] = trim($recipient_emails[$i]);
	}
	
	// Input validation
	if  (!validateInputs( $recipient_emails, $subject, $body ) ) {
		$document =& JFactory::getDocument();
		JError::raiseWarning( 0, $document->getError() );
		return false;
	}
	
	JFactory::getMailer()->sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $recipient_emails, $subject, wordwrap($body), $mode );
	return true;
}

/**
 * Validates e-mail input. Method is modified based on com_contact's _validateInputs.
 *
 * @param String|Array	$email		Email address
 * @param String		$subject	Email subject
 * @param String		$body		Email body
 * @return Boolean
 * @access public
 * @since 2.1
 */
function validateInputs( $email, $subject, $body ) {
	global $mtconf;

	$document =& JFactory::getDocument();

	// Prevent form submission if one of the banned text is discovered in the email field
	if(false === checkText($email, $mtconf->get('banned_email') )) {
		$document->setError( JText::sprintf( 'COM_MTREE_MESGHASBANNEDTEXT', 'Email') );
		return false;
	}

	// Prevent form submission if one of the banned text is discovered in the subject field
	if(false === checkText($subject, $mtconf->get('banned_subject'))) {
		$document->setError( JText::sprintf( 'COM_MTREE_MESGHASBANNEDTEXT', 'Subject') );
		return false;
	}

	// Prevent form submission if one of the banned text is discovered in the text field
	if(false === checkText( $body, $mtconf->get('banned_text') )) {
		$document->setError( JText::sprintf( 'COM_MTREE_MESGHASBANNEDTEXT', 'Message') );
		return false;
	}

	// test to ensure that only one email address is entered
	if( is_string($email) )
	{
		$check = explode( '@', $email );
		if ( strpos( $email, ';' ) || strpos( $email, ',' ) || strpos( $email, ' ' ) || count( $check ) > 2 ) {
			$document->setError( JText::_( 'COM_MTREE_YOU_CANNOT_ENTER_MORE_THAN_ONE_EMAIL_ADDRESS', true ) );
			return false;
		}
	}

	return true;
}

function checkText($text, $list) {
	if(empty($list) || empty($text)) return true;
	$array = explode(';', $list);
	foreach ($array as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if ( JString::stristr($text, $value) !== false ) {
			return false;
		}
	}
	return true;
}

function listallcats( $cat_id, $option )
{
	global $mtconf, $savantConf, $Itemid;
	
	$database	=& JFactory::getDBO();
	
	# Retrieve parent category first
	$sql = 'SELECT cat_name, lft, rgt FROM #__mt_cats AS cat WHERE cat_id = ' . $cat_id . ' LIMIT 1';
	$database->setQuery( $sql );
	$parent_category = $database->loadObject();
	
	if( empty($parent_category) )
	{
		return false;
	}
	
	# Retrieve categories
	$sql = 'SELECT cat.* FROM #__mt_cats AS cat ';
	$sql .= 'WHERE cat_published=1 && cat_approved=1 ';
	$sql .= ' && lft >= ' . $parent_category->lft;
	$sql .= ' && rgt <= ' . $parent_category->rgt;

	if ( !$mtconf->get('display_empty_cat') ) 
	{ 
		$sql .= ' && ( cat_cats > 0 || cat_links > 0 ) ';
	}

	$sql .= ' ORDER BY lft ASC';

	$database->setQuery( $sql );
	$cats = $database->loadObjectList("cat_id");	
	
	# Set title
	setTitle(JText::sprintf( 'COM_MTREE_PAGE_TITLE_ALL_CATEGORIES', $parent_category->cat_name ));

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $tmp=null, $pathWay, $pageNav );

	if (isset($cat_links)) $savant->assign('cat_links', $cat_links);
	$savant->assign('Itemid', $Itemid);
	$savant->assign('cat_id', $cat_id);
	$savant->assign('categories', $cats);
	$savant->assign('header', JText::sprintf('COM_MTREE_HEADER_ALL_CATEGORIES',$parent_category->cat_name));
	$savant->display( 'page_listCategories.tpl.php' );
}

?>