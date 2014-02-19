<?php
/**
 * @version		$Id: mtree.tools.php 1967 2013-07-16 05:04:58Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

/***
* Load Link
*/
function loadLink( $link_id, &$savantConf, &$fields, &$params ) {
	global $_MAMBOTS, $mtconf;

	$database	= JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	# Get all link data
	$database->setQuery( "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username AS username, u.name AS owner, u.email AS owner_email, cl.cat_id AS cat_id, c.cat_name AS cat_name, c.cat_association, img.filename AS link_image, img.img_id FROM (#__mt_links AS l, #__mt_cl AS cl)"
		. "\n LEFT JOIN #__users AS u ON u.id = l.user_id"
		. "\n LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id"
		. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= c.lft AND tlcat.rgt >= c.rgt AND tlcat.cat_parent =0 "
		. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
		. "\n WHERE link_published='1' AND link_approved > 0 AND l.link_id='".$link_id."' " 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id"
		. "\n LIMIT 1"
	);
	$link = $database->loadObject();
	
	if(count($link)==0) return false;
	
	$mtconf->setCategory($link->cat_id);
	
	# Use owner's email address is listing e-mail is not available
	if ( $mtconf->get('use_owner_email') && empty($link->email) && $link->user_id > 0 ) {
		$link->email = $link->owner_email;
	}

	# Load link's template
	if ( empty($link->link_template) ) {
		// Get link's template
		$database->setQuery( "SELECT cat_template FROM #__mt_cats WHERE cat_id='".$link->cat_id."' LIMIT 1" );
		$cat_template = $database->loadResult();

		if ( !empty($cat_template) ) {
			loadCustomTemplate(null,$savantConf,$cat_template);
		}
	} else {
		loadCustomTemplate(null,$savantConf,$link->link_template);
	}
	
	# Load fields
	$fields = loadFields( $link );
	
	# Load custom fields' value from #__mt_cfvalues to $link
	$database->setQuery( "SELECT CONCAT('cust_',cf_id) as varname, value FROM #__mt_cfvalues WHERE link_id = '".$link_id."'" );
	$cfvalues = $database->loadObjectList('varname');
	foreach( $cfvalues as $cfkey => $cfvalue )
	{
		$link->$cfkey = $cfvalue->value;
	}

	# Parameters
	$params = new JRegistry( $link->attribs );
	$params->def( 'show_review', $mtconf->get('show_review'));
	$params->def( 'show_rating', $mtconf->get('show_rating' ));

	return $link;

}

/**
* Return 
* @param object #__mt_links object list results
* @param int Fields' filter type. Setting this to 0 will return all published field types.
*			 $view = 1 for Normal/Details View. $view = 2 for Summary View.
* @return mFields The formatted value of the field
*/
function loadFields( $link, $view=1 ) {
	global $mtconf;

	$database = JFactory::getDBO();

	require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/mfields.class.php' );
	
	// Must always includes listing name
	$cf_ids = array(1);
	$cf_ids = array_merge($cf_ids,getAssignedFieldsID($link->cat_id));
	
	# Load all published CORE & custom fields
	$sql = "SELECT cf.*, cfv.link_id, cfv.value, cfv.attachment, cfv.counter FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id AND cfv.link_id = " . $link->link_id
		.	"\nWHERE cf.published = '1' "
		.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'');
	switch( $view ) {
		case 1:
			$sql .= "&& details_view = '1' ";
			break;
		case 2:
			$sql .= "&& summary_view = '1' ";
			break;
		default:
			break;			
	}
	$sql .= "ORDER BY ordering ASC";
	$database->setQuery($sql);

	$fields = new mFields();
	$fields->setCoresValue( $link->link_name, $link->link_desc, $link->address, $link->city, $link->state, $link->country, $link->postcode, $link->telephone, $link->fax, $link->email, $link->website, $link->price, $link->link_hits, $link->link_votes, $link->link_rating, $link->link_featured, $link->link_created, $link->link_modified, $link->link_visited, $link->publish_up, $link->publish_down, $link->metakey, $link->metadesc, $link->user_id, $link->username );
	$fields->loadFields($database->loadObjectList());

	$fields->resetPointer();
	while( $fields->hasNext() ) {
		$field = $fields->getField();
		
		$fields->fields[$fields->pointer]['catId'] = $link->cat_id;
		$fields->fields[$fields->pointer]['catName'] = $link->cat_name;
		$fields->fields[$fields->pointer]['topLevelCatId'] = $link->tlcat_id;
		$fields->fields[$fields->pointer]['topLevelCatName'] = $link->tlcat_name;

		if($field->getLinkId() == 0) {
			$fields->fields[$fields->pointer]['linkId'] = $link->link_id;
		}
		
		$fields->next();
	}
	
	return $fields;	
}

/***
* assignCommonVar
* 
* Assign comman Savant2 variable to all template
*/

function assignCommonVar( &$savant ) {
	global $option, $Itemid, $mtconf, $task;
	$my = JFactory::getUser();

	$savant->assign('option', $option);
	$savant->assign('task', $task);
	if( isset($Itemid) ) {
		$savant->assign('Itemid', $Itemid);
	} else {
		$savant->assign('Itemid', '');
	}
	$savant->assign('user_id',$my->id);
	$savant->assign('my',$my);
	$savant->assign('form_action', $mtconf->getjconf('live_site').'/index.php');
	$savant->assign('mtconf',$mtconf->getVarArray());
	$savant->assign('jconf',$mtconf->getJVarArray());
	$savant->assignRef('config',$mtconf);
}

/***
* assignCommonViewlinkVar
* 
* Assign comman Savant2 variable to viewlink or any similar pages
*/

function assignCommonViewlinkVar( &$savant, &$link, &$fields, &$pathWay, &$params ) {
	global $option, $Itemid, $mtconf;
	
	$database	= JFactory::getDBO();
	$my		= JFactory::getUser();
	
	$top_level_cat_id = getTopLevelCatID($link->cat_id);
	$database->setQuery( 'SELECT * FROM #__mt_cats WHERE cat_id = ' . $top_level_cat_id . ' LIMIT 1');
	$top_level_cat = $database->loadObject();
	
	# Check to see if listing categories has association	
	if( $top_level_cat->cat_association > 0 ) {
		
		// Get the name/caption of the associated category.
		$database->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats where cat_id = '.$top_level_cat->cat_association.' LIMIT 1');
		$assoc_cat = $database->loadObject();
		
		// Now get the associated listings name.
		$database->setQuery( 
			'SELECT DISTINCT link_id2, l.link_id, l.link_name FROM #__mt_links_associations AS links_assoc '
		.	"\n LEFT JOIN #__mt_links AS l ON links_assoc.link_id1 = l.link_id "
		.	"\n WHERE links_assoc.link_id2 = " . $link->link_id
			);
		$links_assoc = $database->loadObjectList('link_id2');
		
		$fields->setAssocLink(
			array(
				'cat_name'	=> $assoc_cat->cat_name,
				'cat_id'	=> $assoc_cat->cat_id,
				'link_id'	=> (isset($links_assoc[$link->link_id]->link_id))?$links_assoc[$link->link_id]->link_id:null,
				'link_name'	=> (isset($links_assoc[$link->link_id]->link_name))?$links_assoc[$link->link_id]->link_name:null
			)
		);
	}
	
	# Get total favourites
	$total_favourites = 0;
	$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE link_id = '".$link->link_id."'" );
	$total_favourites = $database->loadResult();
	
	# Is this the user's favourite extension?
	$is_user_favourite = 0;
	if( $my->id > 0 && $total_favourites > 0 ) {
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE user_id = '".$my->id."' AND link_id = '".$link->link_id."' LIMIT 1" );
		if( $database->loadResult() > 0 ) {
			$is_user_favourite = 1;
		}
	}
	
	# Get the total number of images
	$total_images = 0;
	$database->setQuery( "SELECT COUNT(*) FROM #__mt_images WHERE link_id = '".$link->link_id."'" );
	$total_images = $database->loadResult();
	
	# Show actions-ratign-fav box?
	$show_actions_rating_fav = 0;
	if( $mtconf->get('show_favourite') || $mtconf->get('show_rating') ) {
		$show_actions_rating_fav++;
	} 
	
	$savant->assign('show_actions', false);
	$actions = array('map','ownerlisting','print','recommend','report','visit','review','claim','contact');
	foreach( $actions AS $action ) {
		$params->def( 'show_'.$action, $mtconf->get('show_'.$action) );
		if ( $params->get( 'show_'.$action ) ) {
			switch($action) {
				case 'contact':
					if($link->email <> '' || ($mtconf->get( 'use_owner_email' ) == 1 && $link->user_id > 0) ) {
						$show_actions_rating_fav++;
						$savant->assign('show_actions', true);
					} else {
						continue;
					}
					break;
				default:
					$show_actions_rating_fav++;
					$savant->assign('show_actions', true);
					break;
			}
			break;
		}
	}
	$savant->assign('show_actions_rating_fav', $show_actions_rating_fav);
	
	assignCommonVar($savant);
	$savant->assign('total_images', $total_images);
	$savant->assign('total_favourites', $total_favourites);
	$savant->assign('is_user_favourite', $is_user_favourite);
	$savant->assign('pathway', $pathWay);
	$savant->assign('link', $link);
	$savant->assign('link_id', $link->link_id);
	$savant->assign('min_votes_to_show_rating', $mtconf->get('min_votes_to_show_rating'));
	$savant->assign('fields', $fields);
	$savant->assign('tlcat_id', $top_level_cat_id);

	$savant->assign('mt_show_review', $params->get( 'show_review' ));
	$savant->assign('mt_show_rating', $params->get( 'show_rating' ));

	if( 
		$mtconf->get('user_rating') == '-1' 
		|| 
		($mtconf->get('user_rating') == 1 && $my->id <= 0) 
		||
		($mtconf->get('user_rating') == 2 && $my->id > 0 && $my->id == $link->user_id)
	) { // -1:none, 0:public, 1:registered user only
		$savant->assign('allow_rating', 0);
	} else {
		$savant->assign('allow_rating', 1);
	}

	// Plugins support
	$link->id 	= $link->link_id;
	$link->title = $link->link_name;
	$link->created_by = $link->user_id;

	$dispatcher	=& JDispatcher::getInstance();
	JPluginHelper::importPlugin('content');
	$savant->assign('mambotAfterDisplayTitle', $dispatcher->trigger('onContentAfterTitle', array ('com_mtree.listing', & $link, & $params, 0)));
	$savant->assign('mambotBeforeDisplayContent', $dispatcher->trigger('onContentBeforeDisplay', array ('com_mtree.listing', & $link, & $params, 0)));
	$savant->assign('mambotAfterDisplayContent', $dispatcher->trigger('onContentAfterDisplay', array ('com_mtree.listing', & $link, & $params, 0)));

	return true;
}

/***
* assignCommonListlinksVar
* 
* Assign comman Savant2 variable to list links or any similar pages
*/

function assignCommonListlinksVar( &$savant, &$links, &$pathWay, &$pageNav ) {
	global $task, $Itemid, $my, $cat_id, $mtconf;
	
	require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/mfields.class.php' );

	$database = JFactory::getDBO();
	$top_level_cat_ids = array();

	// Must always includes listing name
	$cf_ids = array(1);

	if( !empty($links) )
	{
		foreach( $links AS $link ) {
			if( !in_array($link->tlcat_id,$top_level_cat_ids) ) {
				if( empty($link->tlcat_id) ) {
					$link->tlcat_id = 0;
				} 

				$top_level_cat_ids[] = $link->tlcat_id;
				$cf_ids = array_merge($cf_ids,getAssignedFieldsID($link->tlcat_id));
			}
		}
	}

	# Load custom fields' caption
	if( $task == 'advsearch' ) {
		$database->setQuery( "SELECT CONCAT( 'cust_', cf_id ) AS name, caption AS value, field_type FROM #__mt_customfields WHERE published = 1 AND advanced_search = 1" );
		$custom_fields = $database->loadObjectList( "name" );
	} else {
		$database->setQuery( "SELECT CONCAT( 'cust_', cf_id ) AS name, caption AS value, field_type FROM #__mt_customfields" );
		$custom_fields = $database->loadObjectList( "name" );
	}
	
	$links_fields = array();
	$arrayLinkId = array();
	if( count($links) > 0 ) {
		foreach( $links AS $link ) {
			$arrayLinkId[] = intval($link->link_id);
		}
	}
	
	$tmp_fields = array();
	if( count($arrayLinkId) > 0 ) {
		$sql = "SELECT cf.*, cfv.link_id, cfv.value, cfv.attachment, cfv.counter FROM #__mt_customfields AS cf "
			.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id "
			.	"\nAND cfv.link_id IN (" . implode(',',$arrayLinkId). ") "
			.	"\nWHERE cf.published = '1' AND cf.summary_view = '1' "
			.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
			.	"\nORDER BY cf.ordering ASC, link_id DESC";
		$database->setQuery($sql);
		$tmp_fields = $database->loadObjectList();
	}
	
	// Custom fields that do not require value, appear only once with a NULL value to link_id.
	// This loop clone the custom fields for each link
	foreach( $tmp_fields AS $key => $value ) {
		if(is_null($value->link_id)) {
			foreach($arrayLinkId AS $linkId) {
				$tmp_value = $value;
				$tmp_value = (PHP_VERSION < 5) ? $value : clone($value);
				$tmp_value->link_id = $linkId;
				$tmp_fields[] = $tmp_value;
				unset($tmp_value);
			}
		}
	}
	
	// Check to see if listing categories has association.
	if( isset($links[0]) )
	{
		$top_level_cat_id = getTopLevelCatID($links[0]->cat_id);

		if( !is_null($top_level_cat_id) )
		{
			$database->setQuery( 'SELECT cat_association FROM #__mt_cats where cat_id = '.$top_level_cat_id.' LIMIT 1');
			$top_level_cat = $database->loadObject();

			if( isset($top_level_cat) && $top_level_cat->cat_association > 0 ) {

				// Get the name/caption of the associated category.
				$database->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats where cat_id = '.$top_level_cat->cat_association.' LIMIT 1');
				$assoc_cat = $database->loadObject();

				// Now get the associated listings name.
				$database->setQuery( 
					'SELECT DISTINCT link_id2, l.link_id, l.link_name FROM #__mt_links_associations AS links_assoc '
				.	"\n LEFT JOIN #__mt_links AS l ON links_assoc.link_id1 = l.link_id "
				.	"\n WHERE links_assoc.link_id2 IN (" . implode(',',$arrayLinkId). ") "
					);
				$links_assoc = $database->loadObjectList('link_id2');
			}			
		}
	}

	usort($tmp_fields,"customFieldsSort");
	if( count($links) > 0 ) {
		foreach( $links AS $link ) {
			$tmp_link_id = $link->link_id;
			$links_fields[$link->link_id] = new mFields();
			$data = null;
			$i=0;

			foreach( $tmp_fields AS $key => $value ) {
				if( $value->link_id == $tmp_link_id ) {
					$data[$key*28] = $value;
					unset( $tmp_fields[$key] );
				}				
				$i++;
			}
			$links_fields[$link->link_id]->setCoresValue( $link->link_name, $link->link_desc, $link->address, $link->city, $link->state, $link->country, $link->postcode, $link->telephone, $link->fax, $link->email, $link->website, $link->price, $link->link_hits, $link->link_votes, $link->link_rating, $link->link_featured, $link->link_created, $link->link_modified, $link->link_visited, $link->publish_up, $link->publish_down, $link->metakey, $link->metadesc, $link->user_id, $link->username );
			$links_fields[$link->link_id]->loadFields($data);

			while( $links_fields[$link->link_id]->hasNext() ) {
				$field = $links_fields[$link->link_id]->getField();

				$links_fields[$link->link_id]->fields[$links_fields[$link->link_id]->pointer]['catId'] = $link->cat_id;
				$links_fields[$link->link_id]->fields[$links_fields[$link->link_id]->pointer]['catName'] = $link->cat_name;
				$links_fields[$link->link_id]->fields[$links_fields[$link->link_id]->pointer]['topLevelCatId'] = $link->tlcat_id;
				$links_fields[$link->link_id]->fields[$links_fields[$link->link_id]->pointer]['topLevelCatName'] = $link->tlcat_name;

				if($field->getLinkId() == 0) {
					$fields->fields[$links_fields[$link->link_id]->pointer]['linkId'] = $link->link_id;
				}

				$links_fields[$link->link_id]->next();
			}
			
			if( isset($links_assoc[$link->link_id]) ) {
				$links_fields[$link->link_id]->setAssocLink(
					array(
						'cat_name'	=> $assoc_cat->cat_name,
						'cat_id'	=> $assoc_cat->cat_id,
						'link_id'	=> $links_assoc[$link->link_id]->link_id,
						'link_name'	=> $links_assoc[$link->link_id]->link_name
					)
				);
			}
		}
	}

	# Mambots
	if($mtconf->get('cat_parse_plugin')) {
		applyMambots( $links );
	}

	assignCommonVar($savant);
	$savant->assignRef('pathway', $pathWay);
	$savant->assign('pageNav', $pageNav);
	$savant->assignRef('links', $links);;
	$savant->assignRef('links_fields', $links_fields);
	$savant->assign('reviews_count', getReviews($links));
	$savant->assign('min_votes_to_show_rating', $mtconf->get('min_votes_to_show_rating'));
	$savant->assign('custom_fields', $custom_fields);
	$savant->assign('cat_id', $cat_id);
	$savant->assign('template', $mtconf->get('template'));

	return true;
}

/***
* customFieldsSort
*/
function customFieldsSort($a,$b) {
	if ($a->ordering == $b->ordering) {
        return 0;
    }
    return ($a->ordering < $b->ordering) ? -1 : 1;
}

/***
* getSubCats_Recursive
*
* Recursively retrieves list of categories ID which is the children of of a $cat_id.
* This list will include $cat_id as well.
*/
function getSubCats_Recursive( $cat_id, $published_only=true ) {
	$database = JFactory::getDBO();

	$mtCats = new mtCats( $database );

	if ( $cat_id > 0 ) {
		$subcats = $mtCats->getSubCats_Recursive( $cat_id, $published_only );
	}
	$subcats[] = $cat_id;

	return $subcats;

}

/***
* getCatsSelectlist
*
*/
function getCatsSelectlist( $cat_id=0, &$cat_tree, $max_level=0 ) {
	$database	= JFactory::getDBO();
	$tmp_mtconf	= new mtConfig($database);

	$tmp_mtconf->setCategory($cat_id);

	static $level = 0;

	$sql = "SELECT *, '".$level."' AS level FROM #__mt_cats AS cat WHERE cat_published=1 && cat_approved=1 && cat_parent= " . $database->quote($cat_id);
	if( $tmp_mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $tmp_mtconf->get('first_cat_order1') . ' ' . $tmp_mtconf->get('first_cat_order2');
		if( $tmp_mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $tmp_mtconf->get('second_cat_order1') . ' ' . $tmp_mtconf->get('second_cat_order2');
		}
	}
	$database->setQuery( $sql );
	$cat_ids = $database->loadObjectList();
	
	if ( count($cat_ids) > 0 ) {

		$level++;
		
		if( $max_level == 0 || $level <= $max_level) {
			foreach( $cat_ids AS $cid ) {
				
				$cat_tree[] = array("level" => $cid->level, "cat_id" => $cid->cat_id, "cat_name" => $cid->cat_name, "cat_allow_submission" => $cid->cat_allow_submission ) ;

				if ( $cid->cat_cats > 0 ) {
					$children_ids = getCatsSelectlist( $cid->cat_id, $cat_tree, $max_level );
					$cat_ids = array_merge( $cat_ids, $children_ids );
				}
			}
		}
		$level--;
	}
	return $cat_ids;

}

/***
* loadCustomTemplate
*
* If $cat_id has been assigned a custom template, $savantConf will be updated. Otherwise,
* no changes is done, and it will load default template.
*/
function loadCustomTemplate( $cat_id=null, &$savantConf, $template='') {
	global $mtconf;

	$database	=& JFactory::getDBO();

	if(!empty($template)) {
		$templateDir = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template;
		if ( is_dir( $templateDir ) ) {
			$savantConf["template_path"] = $templateDir . '/';
			$mtconf->setTemplate($template);
		}
	} else {
		$mtCats = new mtCats( $database );
		$mtCats->load( $cat_id );
		if ( !empty($mtCats->cat_template) ) {
			$savantConf['template_path'] = $mtconf->getjconf('absolute_path')."/components/com_mtree/templates/".$mtCats->cat_template."/";
			$mtconf->setTemplate($mtCats->cat_template);
			
		}
	}
}

/***
* Apply Mambot to list of link objects and also enforce the max char for summary text in listcats
*/
function applyMambots( &$links ) {
	global $mtconf;

	JPluginHelper::importPlugin('content');
	// $_MAMBOTS->loadBotGroup( 'content' );

	for( $i=0; $i<count($links); $i++ ) {
		// Load Parameters
		$params = new JRegistry($links[$i]->attribs);
	
		$links[$i]->text = substr($links[$i]->link_desc,0,255);
		
		if  ((strlen($links[$i]->link_desc)) > 255) {
			$links[$i]->text .= ' <b>...</b>';
		}

		$links[$i]->id = $links[$i]->link_id;
		$links[$i]->title = $links[$i]->link_name;
		$links[$i]->created_by = $links[$i]->user_id;

		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onContentPrepare', array ('com_mtree.category', & $links[$i], & $params->params, 0));
		}

}

function mtAppendPathWay( $option, $task, $cat_id=0, $link_id=0, $cf_id=0, $img_id=0, $rev_id=0, $user_id=0 ) {

	$database	= JFactory::getDBO();
	$pathway	= JFactory::getApplication()->getPathway();
	$tlcat_id	= 0;
	
	if( $link_id > 0 ) {
		$mtLink = new mtLinks( $database );
		$mtLink->load( $link_id );
	} elseif( $img_id > 0 ) {
		$database->setQuery('SELECT link_id FROM #__mt_images WHERE img_id = \'' . $img_id . '\' LIMIT 1');
		$link_id = $database->loadResult();
		if( !is_null($link_id) ) {
			$mtLink = new mtLinks( $database );
			$mtLink->load( $link_id );
		}
	} elseif( $rev_id > 0 ) {
		$database->setQuery('SELECT * FROM #__mt_reviews WHERE rev_id = \'' . $rev_id . '\' LIMIT 1');
		$review = $database->loadObject();
		if( !is_null($review) ) {
			$link_id = $review->link_id;
			$mtLink = new mtLinks( $database );
			$mtLink->load( $link_id );
		}
	} elseif( $user_id > 0 ) {
		$database->setQuery('SELECT * FROM #__users WHERE id = \'' . $user_id . '\' LIMIT 1');
		$user = $database->loadObject();
	}
	
	// Always start MT's pathway with categories
	if ( $cat_id > 0 )
	{
		mtAppendCategoriesPathWay($cat_id);
		$tlcat_id = getTopLevelCatID($cat_id);
	}
	elseif( $link_id > 0 )
	{
		mtAppendCategoriesPathWay($mtLink->getCatID());
	}
	
	// If the current page is part of a listing, add a path to the listing
	if( 
		in_array(
			$task,
			array(
				'viewlink',
				'writereview',
				'rate',
				'recommend',
				'viewgallery',
				'viewimage',
				'editlisting',
				'contact',
				'report',
				'viewreviews',
				'viewreview',
				'reportreview',
				'replyreview',
				'deletelisting',
				'claim'
			)
		) 
	) {
		$pathway->addItem( $mtLink->link_name, "index.php?option=$option&task=viewlink&link_id=$link_id");

		// Additional levels inside listing tasks
		switch( $task )
		{
			case 'viewimage':
				$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_VIEWGALLERY'), "index.php?option=$option&task=viewgallery&link_id=$link_id");
				break;
			case 'viewreview':
				$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_VIEWREVIEWS'), "index.php?option=$option&task=viewreviews&link_id=$link_id");
				$pathway->addItem( $review->rev_title, "index.php?option=$option&task=viewreview&rev_id=$rev_id");
				break;
			case 'reportreview':
			case 'replyreview':
				$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_VIEWREVIEWS'), "index.php?option=$option&task=viewreviews&link_id=$link_id");
				$pathway->addItem( $review->rev_title, "index.php?option=$option&task=viewreview&rev_id=$rev_id");
				break;
		}
	}
	
	if(
		!in_array(
			$task,
			array(
				'searchby',
				'listcats',
				'viewlink',
				'advsearch2',
				''
			)
		)
	) {
		if( MText::_( 'PATHWAY_'.strtoupper($task), $tlcat_id ) != 'COM_MTREE_PATHWAY_'.strtoupper($task) ) {
			$pathway->addItem( MText::_( 'PATHWAY_'.strtoupper($task), $tlcat_id ) );
		}
	}
	
	switch( $task ) {
		case "advsearch2":
			$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_ADVSEARCH' ), 'index.php?option=com_mtree&task=advsearch&cat_id='.$cat_id );
			$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_'.strtoupper($task) ) );
			break;
		case "searchby":
			$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_SEARCHBY' ), 'index.php?option=com_mtree&task=searchby' );
			if( $cf_id > 0 )
			{
				$database->setQuery('SELECT caption FROM #__mt_customfields WHERE cf_id = \'' . $cf_id . '\' LIMIT 1');
				$cf_caption = $database->loadResult();
				if( !is_null($cf_caption) ) {
					$pathway->addItem( $cf_caption, 'index.php?option=com_mtree&task=searchby&cf_id='.$cf_id );
					$cf_value = JFactory::getApplication()->input->getString('value', '');
					if( !empty($cf_value) )
					{
						$pathway->addItem( $cf_value, 'index.php?option=com_mtree&task=searchby&cf_id='.$cf_id.'&value='.$cf_value );
					}
				}
			}
			break;
		case "viewowner":
		case "viewuserslisting":
		case "viewusersreview":
		case "viewusersfav":
			$pathway->addItem( $user->username, 'index.php?option=com_mtree&task=viewowner&user_id='.$user->id );
			switch( $task ) {
				case "viewowner":
				case "viewuserslisting":
					$pathway->addItem( MText::_('PATHWAY_USERS_LISTINGS', $tlcat_id) );
					break;
				case "viewusersreview":
					$pathway->addItem( JText::_('COM_MTREE_PATHWAY_USERS_REVIEWS') );
					break;
				case "viewusersfav":
					$pathway->addItem( JText::_('COM_MTREE_PATHWAY_USERS_FAVOURITES') );
					break;
			}
			break;
	}
}

function mtAppendCategoriesPathWay( $cat_id ) {
	$app		= JFactory::getApplication();
	$pathway	= $app->getPathway();
	$mtPathWay	= new mtPathWay($cat_id);
	
	$cids = $mtPathWay->getPathWayWithCurrentCat();

	$menus		= $app->getMenu();
	$menu		= $menus->getActive();

	// $menu is null when no Itemid is passed through or not available
	if( !is_null($menu) )
	{
		$menu_item_cat_id = 0;
		if( isset($menu->query['cat_id']) )
		{
			$menu_item_cat_id = $menu->query['cat_id'];
		}

		$key = array_search($menu_item_cat_id,$cids);
		if( $key !== false )
		{
			$cids = array_slice($cids,($key+1));
		}

		if ( isset($cids) && is_array($cids) && count($cids) > 0 ) {
			foreach( $cids AS $cid ) {
				if( $cid == 0 )
				{
					$pathway->addItem( JText::_( 'COM_MTREE_PATHWAY_ROOT' ), "index.php?option=com_mtree&task=listcats&cat_id=$cid" );
				}
				else
				{
					$pathway->addItem( $mtPathWay->getCatName($cid), "index.php?option=com_mtree&task=listcats&cat_id=$cid" );
				}
			}
		}
	}
}

function getReviews( $links ) {
	$database = JFactory::getDBO();
	
	$link_ids = array();
	
	if ( count( $links ) > 0 ) {
		foreach( $links AS $l ) {
			$link_ids[] = intval($l->link_id);
		}

		if ( count($link_ids) > 0 ) {
			# Get total reviews for each links
			$database->setQuery( "SELECT r.link_id, COUNT( * ) AS total FROM #__mt_cl AS cl "
				.	"\n LEFT JOIN #__mt_reviews AS r ON cl.link_id = r.link_id "
				.	"\n WHERE cl.link_id IN ('".implode("','",$link_ids)."') AND r.rev_approved = '1' AND cl.main = '1'"
				.	"\n GROUP BY r.link_id"	
				);
			$reviews = $database->loadObjectList('link_id');
			foreach( $links AS $link ) {
				if(!array_key_exists($link->link_id,$reviews)) {
					$reviews[$link->link_id] = new stdClass();
					$reviews[$link->link_id]->link_id = $link->link_id;
					$reviews[$link->link_id]->total = 0;
				}
			}
			return $reviews;
		} else {
			return array(0);
		}
	} else {
		return false;
	}
}

function parse_words($text, $minlength=1){
	if($text=='0') {
		return array('0');
	} else {
		$result = array();
		if(@preg_match_all('/"(?P<string>[^"\\\\]{' . $minlength . ',}(?:\\\\.[^"\\\\]*)*)"|(?P<words>[^ "]{' . $minlength . ',})/', $text, $regs)) {
			if(($result = @array_unique(@array_filter($regs['words']) + @array_filter($regs['string']))) !== NULL) {
				@ksort($result, SORT_NUMERIC);
			}
		}
		$arrText = explode(' ',$text);
		foreach($arrText AS $aText) {
			if($aText == '0') {
				array_unshift($result,'0');
				break;
			}
		}
		return($result);		
	}
}

/**
 * Load fields that are assigned to category with $cat_id.
 * If no assigment is available, it will attempt to look up to top most parent 
 * category and inherit its fields map. This means that only top level category
 * can be used for fields assignment only.
 *
 * @param	int	Category ID
 * @return	array	An array of custom field IDs
 */
function getAssignedFieldsID($cat_id) {
	$cf_id = array();
	
	$top_level_cat_id = getTopLevelCatID($cat_id);

	if( !is_null($top_level_cat_id) )
	{
		$db = JFactory::getDBO();
		
		// Load applicable fields that are assigned to $top_level_cat_id
		$db->setQuery( "SELECT cf_id FROM #__mt_fields_map WHERE cat_id = " . $top_level_cat_id );
		$cf_id = $db->loadColumn();
		
	}

	return $cf_id;
}

/**
 * Find the top level category (if it itself is not one)
 *
 * @param	int	Category ID
 * @return	int	Top level category's ID. null if none is found.
 */
function getTopLevelCatID($cat_id) {
	$db = JFactory::getDBO();
	
	$cat = new mtCats( $db );
	$cat->load( $cat_id );

	if( $cat->cat_parent == 0 || $cat_id == 0)
	{
		return $cat_id;
	}
	else
	{
		// Get the top most level category
		$db->setQuery("SELECT cat_id FROM #__mt_cats "
		.	"\nWHERE lft < " . $cat->lft . " && rgt > " . $cat->rgt . " && cat_parent >= 0"
		.	"\nORDER BY lft ASC LIMIT 1");
		$top_level_cat_id = $db->loadResult();

		if( !empty($top_level_cat_id) )
		{
			return $top_level_cat_id;
		} else {
			return null;
		}
	}
}

function setTitle($title='', $cat_id=null, $link_id=null)
{
	$document	= JFactory::getDocument();
	$app		= JFactory::getApplication('site');

	// Check if a Custom Page Title is given from menu parameters
	$menus		= $app->getMenu();
	$menu		= $menus->getActive();

	$menu_link_page_title = '';
	
	if( isset($menu->params) )
	{
		$menu_link_page_title = $menu->params->get('page_title');
		
	}
	
	if( $menu && !empty($menu_link_page_title) )
	{
		parse_str($menu->link,$menulink);
		switch($menulink['view'])
		{
			case 'listcats':
			case 'toplisting':
			case 'addcategory':
			case 'addlisting':
			case 'advsearch':
			case 'listalpha':
				if( $cat_id == $menulink['cat_id'] )
				{
					$title = $menu_link_page_title;
				}
				break;
			
			case 'mypage':
			default:
				$title = $menu_link_page_title;
				break;
		}
	}

	// Set the Page Title
	if (empty($title)) {
		$title = $app->getCfg('sitename');
	}
	elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
		$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
	}
	elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
		$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
	}
	$document->setTitle($title);
}

?>