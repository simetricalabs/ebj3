<?php
/**
 * @version	$Id: mod_mt_filter.php 1281 2011-12-02 10:24:06Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/mfields.class.php' );
require_once( dirname(__FILE__).'/helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

$moduleclass_sfx= $params->get( 'moduleclass_sfx' );
$filter_button	= intval( $params->get( 'filter_button', 1 ) );
$reset_button	= intval( $params->get( 'reset_button', 1 ) );
$cat_id		= intval( $params->get( 'cat_id', 0 ) );
$cf_ids		= $params->get( 'fields' );
$itemid		= MTModuleHelper::getItemid();
$intItemid	= str_replace('&Itemid=','',$itemid);

$db 		= JFactory::getDBO();
$post 		= $_REQUEST;
$search_params	= $post;

# Load all CORE and custom fields
$db->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, '".$cat_id."' AS cat_id FROM #__mt_customfields AS cf "
	.	"\nWHERE cf.hidden ='0' AND cf.published='1' && filter_search = '1'"
	.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
	.	" ORDER BY ordering ASC" 
	);
$filter_fields = new mFields($db->loadObjectList());
$searchParams = $filter_fields->loadSearchParams($search_params);
$hasSearchParams = true;

require(JModuleHelper::getLayoutPath('mod_mt_filter'));
?>