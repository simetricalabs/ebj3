<?php
/**
 * @version	$Id: mod_mt_staticcats.php 2010 2013-08-02 09:32:44Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( dirname(__FILE__).'/helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

$class_sfx		= $params->get( 'class_sfx',		'' );
$moduleclass_sfx 	= $params->get( 'moduleclass_sfx' );
$show_totalcats		= $params->get( 'show_totalcats',	0 );
$show_totallisting	= $params->get( 'show_totallisting', 0 );

$categories		= modMTStaticcatsHelper::getCategories( $params );
$cat_id			= modMTStaticcatsHelper::getCategoryId();

require(JModuleHelper::getLayoutPath('mod_mt_staticcats'));
?>