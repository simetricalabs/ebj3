<?php
/**
 * @version	$Id: corefeatured.php 2118 2013-10-19 07:03:32Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corefeatured extends mFieldType {
	var $name = 'link_featured';
	var $numOfInputFields = 0;
	function getOutput() {
		$featured = $this->getValue();
		$html = '';
		if($featured) {
			$html .= JText::_( 'JYES' );
		} else {
			$html .= JText::_( 'JNO' );
		}
		return $html;
	}
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false ) {
		$searchValue = $this->getSearchValue();

		$options = array(
			''	=> '',
			'1'	=> JText::_( 'FLD_COREFEATURED_FEATURED_ONLY' ),
			'0'	=> JText::_( 'FLD_COREFEATURED_NON_FEATURED_ONLY' )
		);
		
		$html = '';
		$html = '<select name="' . $this->getSearchFieldName(1) . '">';
		foreach( $options AS $key => $value ) {
			$html .= '<option value="'.$key.'"';
			if( 
				($showSearchValue && $searchValue !== false && $key == $searchValue)
				||
				($searchValue === false && $key === '')
			 ) {
				$html .= ' selected=selected';
			}
			$html .= '>';
			$html .= $value;
			$html .= '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	function getWhereCondition() {
		$args = func_get_args();

		$fieldname = $this->getName();
		
		if(  is_numeric($args[0]) ) {
			switch($args[0]) {
				case -1:
				case '':
					return null;
					break;
				case 1:
					return $fieldname . ' = 1';
					break;
				case 0:
				return $fieldname . ' = 0';
					break;
			}
		} else {
			return null;
		}
	}
}
?>