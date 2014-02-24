<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 0.8
* @copyright (C) 2004 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_editor extends Savant2_Plugin {
	function plugin($name, $text = '', $hiddenField, $width=350, $height=200, $cols=10, $rows=45)
	{
		list($name, $text, $hiddenField, $width, $height, $cols, $rows) = array_merge(func_get_args(), array(null));

		editorArea( $name,  $text, $hiddenField, $width, $height, $cols, $rows );
	}
}
?>