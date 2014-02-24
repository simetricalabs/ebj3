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
require_once (dirname(__FILE__).'/helper.php');

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass_sfx	= $params->get( 'moduleclass_sfx' );
$caption		= $params->get( 'caption', '%s' );

$last_update		= modMTLastupdateHelper::getLastUpdate( $params );

require(JModuleHelper::getLayoutPath('mod_mt_lastupdate'));