<?php
/**
 * @version	$Id: init.php 1967 2013-07-16 05:04:58Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die;

global $mtconf;

if(!isset($mtconf))
{
	if( !isset($database) )
	{
		$database = JFactory::getDBO();
	}
	
	require( JPATH_ROOT.'/administrator/components/com_mtree/config.mtree.class.php');
	$mtconf	= new mtConfig($database);

}

$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);
if( $cat_id != 0 )
{ 
	$mtconf->setCategory($cat_id);
}
?>