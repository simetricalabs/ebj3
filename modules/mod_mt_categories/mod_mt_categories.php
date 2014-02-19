<?php
/**
 * @version	$Id: mod_mt_categories.php 1967 2013-07-16 05:04:58Z cy $
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
$class_sfx		= $params->get( 'class_sfx', '' );
$moduleclass_sfx	= $params->get( 'moduleclass_sfx', '' );
$show_totalcats		= $params->get( 'show_totalcats', 0 );
$show_totallisting	= $params->get( 'show_totallisting', 0 );
$back_symbol		= htmlspecialchars($params->get( 'back_symbol', '<<' ));

# Try to retrieve current category
$link_id	= JFactory::getApplication()->input->getInt('link_id');
$cat_id		= JFactory::getApplication()->input->getInt('cat_id');

$cache =& JFactory::getCache('mod_mt_categories');

$cat_id		= $cache->call( array('modMTCategoriesHelper','getCategoryId'), $link_id, $cat_id );
$categories	= $cache->call( array('modMTCategoriesHelper','getCategories'), $params, $cat_id, $link_id );
$back_category	= $cache->call( array('modMTCategoriesHelper','getBackCategory'), $params, $cat_id, $link_id, empty($categories) );

if( !is_null($back_category) && $back_category->cat_id == 0 ) { 
	$back_category->cat_name = JText::_('COM_MTREE_ROOT');
}

require(JModuleHelper::getLayoutPath('mod_mt_categories'));
?>