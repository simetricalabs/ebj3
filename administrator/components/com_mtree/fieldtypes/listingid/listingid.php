<?php
/**
 * @version	$Id$
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_listingId extends mFieldType {

	function getInputHTML() {
		$showInFrontEndEdit = $this->getParam('showInFrontEndEdit',1);
		$showInBackEndEdit = $this->getParam('showInBackEndEdit',1);
		$html = '';
		if( ($this->inBackEnd() && $showInBackEndEdit) || (!$this->inBackEnd() && $showInFrontEndEdit) ) {
			$html .= '<input type="text" name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldName(1) . '" value="' . $this->getLinkId() . '" disabled />';
		} else {
			$html .= '<input type="hidden" name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldName(1) . '" value="' . $this->getLinkId() . '" disabled />';
		}
		return $html;
	}
	
	/**
	* Return the formatted output
	* @param int Type of output to return. Especially useful when you need to display expanded 
	*		 information in detailed view and use can use this display a summarized version
	*		 for summary view. $view = 1 for Normal/Details View. $view = 2 for Summary View.
	* @return str The formatted value of the field
	*/
	function getOutput($view=1) {
		$prefixCode = trim($this->getParam('prefixCode',''));
		$suffixCode = trim($this->getParam('suffixCode',''));
		$html = '';
		$html .= $prefixCode . $this->getLinkId() . $suffixCode;
		return $html;
	}
	
	function getValue() {
		return $this->getLinkId();
	}

	function hasValue() { return true; }
	
	function parseValue( $value ) { return $this->getLinkId; }
	
}
?>