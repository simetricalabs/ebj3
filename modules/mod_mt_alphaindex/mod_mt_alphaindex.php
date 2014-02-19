<?php
/**
 * @version	$Id: mod_mt_alphaindex.php 1867 2013-04-09 14:17:27Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT.'/components/com_mtree/init.module.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/mfields.class.php' );
require_once( dirname(__FILE__).'/helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass		= $params->get( 'moduleclass',		'mainlevel'	);
$direction		= $params->get( 'direction',		'vertical'	);
$show_number		= $params->get( 'show_number',		1	);
$display_total_links	= $params->get( 'display_total_links',	0	);
$show_empty		= $params->get( 'show_empty',		0	);
$seperator		= $params->get( 'seperator',		'&nbsp;'	);
$moduleclass_sfx	= $params->get( 'moduleclass_sfx',	''	);

$list	= modMTAlphaindexHelper::getList($params);

if( $direction == 'horizontal' ) {
	require(JModuleHelper::getLayoutPath('mod_mt_alphaindex', $direction));
} else {
	require(JModuleHelper::getLayoutPath('mod_mt_alphaindex'));
}
?>