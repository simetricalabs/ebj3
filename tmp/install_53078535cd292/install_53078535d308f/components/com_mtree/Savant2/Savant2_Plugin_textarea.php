<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* 
* Outputs a single <textarea> element.
* 
* $Id$
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @package Savant2
* 
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as
* published by the Free Software Foundation; either version 2.1 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
*/

class Savant2_Plugin_textarea extends Savant2_Plugin {
	
	/**
	* 
	* Outputs a single <textarea> element.
	* 
	* @access public
	* 
	* @param string $name The HTML "name=" value.
	* 
	* @param string $text The initial value of the textarea element.
	* 
	* @param int $rows How many rows tall should the area be?
	* 
	* @param int $cols The many columns wide should the area be?
	* 
	* @param string $attr Any "extra" HTML code to place within the
	* checkbox element.
	* 
	* @return string
	* 
	*/
	
	function plugin()
	{
		list($name, $text, $rows, $cols, $attr) = array_merge(func_get_args(), array(null, null, null, null));

		// start the tag
		$html = '<textarea name="' . htmlspecialchars($name) . '"';
		$html .= ' rows="' . htmlspecialchars($rows) . '"';
		$html .= ' cols="' . htmlspecialchars($cols) . '"';
		
		// add extra attributes
		if (is_array($attr)) {
			// add from array
			foreach ($attr as $key => $val) {
				$key = htmlspecialchars($key);
				$val = htmlspecialchars($val);
				$html .= " $key=\"$val\"";
			}
		} elseif (! is_null($attr)) {
			// add from scalar
			$html .= " $attr";
		}
		
		// add the default text, close the tag, and return
		$html .= '>' . htmlspecialchars($text) . '</textarea>';
		return $html;
	}
}

?>