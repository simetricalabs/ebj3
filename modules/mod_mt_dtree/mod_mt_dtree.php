<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( JPATH_ROOT.'/administrator/components/com_mtree/admin.mtree.class.php');
require_once (dirname(__FILE__).'/helper.php');

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass_sfx	= $params->get( 'moduleclass_sfx' );
$root_image		= $params->get( 'root_image', $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'dtree/base.gif' );
$cat_image		= $params->get( 'cat_image', $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'dtree/folder.gif' );
$show_totalcats		= $params->get( 'show_totalcats', 0 );
$show_totallisting	= $params->get( 'show_totallisting', 0 );
$closesamelevel		= $params->get( 'closesamelevel', 1 );
$width			= $params->get( 'width', 159 );
$root_catid		= $params->get( 'root_catid', 0 );
$show_empty_cat		= $params->get( 'show_empty_cat', $mtconf->get('display_empty_cat') );
$show_listings		= $params->get( 'show_listings', 0 );

if ($show_empty_cat == -1) $show_empty_cat = $mtconf->get('display_empty_cat');

# Try to retrieve current category
$link_id	= JFactory::getApplication()->input->getInt('link_id');
$cat_id		= JFactory::getApplication()->input->getInt('cat_id');

$cache = JFactory::getCache('mod_mt_browse');

$uri		= JUri::getInstance();
$itemid		= MTModuleHelper::getItemid();
$cat_id		= $cache->call( array('modMTDtreeHelper','getCategoryId'), $link_id, $cat_id );
$categories	= $cache->call( array('modMTDtreeHelper','getCategories'), $params );
$listings	= $cache->call( array('modMTDtreeHelper','getListings'), $params, $categories );

$link_id	= JFactory::getApplication()->input->getInt('link_id');

require(JModuleHelper::getLayoutPath('mod_mt_dtree'));
?>