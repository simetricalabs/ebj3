<?php
/**
 * @version	$Id: corecity.php 1109 2011-05-26 10:54:42Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_directory extends mFieldType {
	var $numOfSearchFields = 0; 
	var $numOfInputFields = 0; 
	
	/**
	* Return the formatted output
	* @param int Type of output to return. Especially useful when you need to display expanded 
	*		 information in detailed view and use can use this display a summarized version
	*		 for summary view. $view = 1 for Normal/Details View. $view = 2 for Summary View.
	* @return str The formatted value of the field
	*/
	function getOutput($view=1) {
		$html = '';
		$html .= '<a href="';
		$html .= JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$this->getDirectoryId());
		$html .= '">';
		$html .= $this->getDirectoryName();
		$html .= '</a>';
		return $html;
	}

	function getValue($arg=null) {
		return $this->getDirectoryId();
	}

	function hasValue() { return true; }
	
	function parseValue( $value ) { return $this->getDirectoryId(); }

}

?>