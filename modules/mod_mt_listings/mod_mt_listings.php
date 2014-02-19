<?php
/**
 * @version	$Id: mod_mt_listings.php 1967 2013-07-16 05:04:58Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( JPATH_ROOT.'/administrator/components/com_mtree/admin.mtree.class.php');
require_once( dirname(__FILE__).'/helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

// Retrieve current category and link's ID
$cat_id 	= JFactory::getApplication()->input->getInt( 'cat_id', 0 );
$link_id 	= JFactory::getApplication()->input->getInt( 'link_id', 0 );

// Get params
$moduleclass_sfx	= $params->get( 'moduleclass_sfx' );
$listingclass		= $params->get( 'listingclass', '' );
$type			= $params->get( 'type', 1 ); // Default is new listing
$count			= $params->get( 'count', 5 );
$show_from_cat_id	= $params->get( 'show_from_cat_id', 0 );
$only_subcats		= $params->get( 'only_subcats', 0 );
$shuffle_listing	= $params->get( 'shuffle_listing', 1 );
$show_more		= $params->get( 'show_more', 1 );
$caption_showmore	= $params->get( 'caption_showmore', 'Show more...' );
$show_website		= $params->get( 'show_website', 0 );
$show_category		= $params->get( 'show_category', 1 );
$show_rel_data		= $params->get( 'show_rel_data', 1 );
$show_images		= $params->get( 'show_images', 1 );
$dropdown_select_text	= $params->get( 'dropdown_select_text', JText::_( 'MOD_MT_LISTINGS_FIELD_DROPDOWN_SELECT_TEXT_DEFAULT_VALUE' ) );
$dropdown_width		= $params->get( 'dropdown_width', 200 );
$layout			= $params->get( 'layout', 'default' );

$tiles_flow		= $params->get( 'tiles_flow', 'horizontal' );
$hide_name		= $params->get( 'hide_name', '0' );
$name_alignment		= $params->get( 'name_alignment', 'left' );
$image_size		= $params->get( 'image_size', '50px' );
$tile_width		= $params->get( 'tile_width', '' );
$tile_height		= $params->get( 'tile_height', '' );

// Determine tile's width if not explicitly given
if( empty($tile_width) )
{
	switch($tiles_flow)
	{
		default:
		case 'horizontal':
			$tile_width = '50%';
			break;
		case 'vertical':
			$tile_width = '100%';
			break;
	}
}

// Provides unique ID to module for custom styling.
$uniqid = uniqid('mod_mt_listings');

// Disable show more when showing random listing
if( $type == 8 ) {
	$show_more = 0;
}

$cache		=& JFactory::getCache('mod_mt_listings'.'_catid'.$cat_id.'_linkid'.$link_id);
$limit_cat_to 	= $cache->call(array('modMTListingsHelper','getCatIdFilter'), $params, $cat_id, $link_id);
$listings 	= $cache->call(array('modMTListingsHelper','getList'), $params, $limit_cat_to);

$itemid	= MTModuleHelper::getItemid();
$ltask	= modMTListingsHelper::getModuleTask( $type );

if( $layout != 'dropdown' )
{
	$fields	= modMTListingsHelper::getFields( $params, $listings );

	$show_more_link = '';
	if( !empty($ltask) )
	{
		$show_more_link = JRoute::_( 'index.php?option=com_mtree&task='.$ltask.'&' . (($only_subcats) ? 'cat_id='.$cat_id : (($show_from_cat_id) ? 'cat_id='.$show_from_cat_id : '') ). $itemid);
	}

}

require(JModuleHelper::getLayoutPath('mod_mt_listings', $layout));
?>