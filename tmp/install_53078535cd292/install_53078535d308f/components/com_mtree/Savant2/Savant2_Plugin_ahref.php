<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* 
* Outputs an HTML <a href="">...</a> tag.
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

class Savant2_Plugin_ahref extends Savant2_Plugin {

	/**
	* 
	* Output an HTML <a href="">...</a> tag.
	* 
	* @access public
	* 
	* @param string|array $href A string URL for the resulting tag.  May
	* also be an array with any combination of the keys 'scheme',
	* 'host', 'path', 'query', and 'fragment' (c.f. PHP's native
	* parse_url() function).
	* 
	* @param string $text The displayed text of the link.
	* 
	* @param string|array $attr Any extra attributes for the <a> tag.
	* 
	* @return string The <a href="">...</a> tag.
	* 
	*/
	
	function plugin()
	{
		list($href, $text, $attr) = array_merge(func_get_args(), array(null));

		$html = '<a href="';
		
		if (is_array($href)) {
			
			// add the HREF from an array
			$tmp = '';
			
			if (isset($href['scheme'])) {
				$tmp .= $href['scheme'] . ':';
				if (strtolower($href['scheme']) != 'mailto') {
					$tmp .= '//';
				}
			}
			
			if (isset($href['host'])) {
				$tmp .= $href['host'];
			}
			
			if (isset($href['path'])) {
				$tmp .= $href['path'];
			}
			
			if (isset($href['query'])) {
				$tmp .= '?' . $href['query'];
			}
			
			$tmp = JRoute::_($tmp);

			if (isset($href['fragment'])) {
				$tmp .= '#' . $href['fragment'];
			}
		
			$html .= $tmp;
			
		} else {
		
			// add the HREF from a scalar
			$html .= JRoute::_($href);
			
		}
		
		$html .= '"';
		
		// add attributes
		if (is_array($attr)) {
			// from array
			foreach ($attr as $key => $val) {
				$key = htmlspecialchars($key);
				$val = htmlspecialchars($val);
				$html .= " $key=\"$val\"";
			}
		} elseif (! is_null($attr)) {
			// from scalar
			$html .= " $attr";
		}
		
		// set the link text, close the tag, and return
		$html .= '>' . $text . '</a>';
		return $html;
	}
}
?>