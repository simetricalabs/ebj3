<?php
/**
 * @version	$Id: mod_mt_categories_expanding.php 2010 2013-08-02 09:32:44Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
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
$moduleclass_sfx		= $params->get( 'moduleclass_sfx',		'' );
$class_sfx			= $params->get( 'class_sfx' );
$primary_order			= $params->get( 'primary_order',		$mtconf->get('first_cat_order1') );
$primary_sort			= $params->get( 'primary_sort',			$mtconf->get('first_cat_order2') );
$secondary_order		= $params->get( 'secondary_order',		$mtconf->get('second_cat_order1') );
$secondary_sort			= $params->get( 'secondary_sort',		$mtconf->get('second_cat_order2') );
$show_empty_cat			= $params->get( 'show_empty_cat',		$mtconf->get('display_empty_cat') );
$show_totalcats			= $params->get( 'show_totalcats',		0 );
$show_totallisting		= $params->get( 'show_totallisting',		0 );
$hide_active_cat_count		= $params->get( 'hide_active_cat_count',	1 );
$task				= $params->get( 'task',	'listcats' );
$expand_level_1_categories	= $params->get( 'expand_level_1_categories',	0 );

if ($show_empty_cat == -1) $show_empty_cat	= $mtconf->get('display_empty_cat');
if ($primary_order == -1) $primary_order	= $mtconf->get('first_cat_order1');
if ($primary_sort == -1) $primary_sort		= $mtconf->get('first_cat_order2');
if ($secondary_order == -1) $secondary_order	= $mtconf->get('second_cat_order1');
if ($secondary_sort == -1) $secondary_sort	= $mtconf->get('second_cat_order2');

# Try to retrieve current category
$link_id	= JFactory::getApplication()->input->getInt('link_id');
$cat_id		= JFactory::getApplication()->input->getInt('cat_id');;

$cache =& JFactory::getCache('mod_mt_categories_expanding');
$cat_id = $cache->call(array('modMTCategoriesExpandingHelper','getCategoryId'), $cat_id, $link_id);
$categories = $cache->call(array('modMTCategoriesExpandingHelper','getCategories'), $params, $cat_id);

$itemid		= MTModuleHelper::getItemid();

require(JModuleHelper::getLayoutPath('mod_mt_categories_expanding'));
?>