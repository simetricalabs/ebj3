<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( JPATH_ROOT.'/administrator/components/com_mtree/admin.mtree.class.php');
require_once( dirname(__FILE__).'/helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$class_sfx		= $params->get( 'class_sfx' );
$layout			= $params->get( 'layout',		'default' );
$subcat_class		= $params->get( 'subcat_class',		'sublevel' );
$currentcat_class	= $params->get( 'currentcat_class',	'sublevel' );
$show_totalcats		= $params->get( 'show_totalcats',	0 );
$show_totallisting	= $params->get( 'show_totallisting',	0 );
$show_empty_cat		= $params->get( 'show_empty_cat',	$mtconf->get('display_empty_cat') );
$moduleclass_sfx	= $params->get( 'moduleclass_sfx',	'' );

# Try to retrieve current category
$link_id	= JFactory::getApplication()->input->getInt('link_id');
$cat_id		= JFactory::getApplication()->input->getInt('cat_id');;

if ($show_empty_cat == -1) $show_empty_cat = $mtconf->get('display_empty_cat');

$spacer = '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'dtree/empty.gif" align="left" vspace="0" hspace="0" />';

$cache = JFactory::getCache('mod_mt_browse');

$itemid		= $moduleHelper->getItemid();
$cat		= $cache->call( array('modMTBrowseHelper','getCategory'), $cat_id, $link_id );
$cats		= $cache->call( array('modMTBrowseHelper', 'getList'), $cat->cat_id, $show_empty_cat );
$pathway	= $cache->call( array('modMTBrowseHelper', 'getPathWay'),  $cat->cat_id );

if( $cat->cat_id == 0 ) {
	$cat = null;
}

$root		= new stdClass();
$root->link = JRoute::_( 'index.php?option=com_mtree&task=listcats&cat_id=0' . $itemid );
$root->name = JText::_( 'MOD_MT_BROWSE_ROOT' );

require(JModuleHelper::getLayoutPath('mod_mt_browse',$layout));
?>