<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_year extends mFieldType {
	var $numOfSearchFields = 2;
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$searchValue = $this->getSearchValue();

		$startYear = $this->getParam('startYear',(date('Y')-70));
		$endYear = $this->getParam('endYear',date('Y'));
		
		$options = array(
			''	=> '',
			'1'	=> JText::_( 'FLD_YEAR_EXACTLY' ),
			'2'	=> JText::_( 'FLD_YEAR_AFTER' ),
			'3'	=> JText::_( 'FLD_YEAR_BEFORE' ),
		);
		
		$html = '<select name="' . $this->getSearchFieldName(2) . '">';
		foreach( $options AS $key => $value ) {
			$html .= '<option value="'.$key.'"';
			if( 
				$showSearchValue 
				&&
				isset($searchValue[$this->getSearchFieldName(2)])
				&& 
				$searchValue[$this->getSearchFieldName(2)] == $key 
			) {
				$html .= ' selected=selected';
			}
			$html .= '>';
			$html .=  $value;
			$html .=  '</option>';
		}
		$html .= '</select>';
		$html .= '&nbsp;';

		$html .= '<select name="' . $this->getInputFieldName(1) . '">';
		$html .= '<option value="">&nbsp;</option>';
		for($year=$endYear;$year>=$startYear;$year--) {
			$html .= '<option value="' . $year . '"';
			if( 
				$showSearchValue 
				&& 
				isset($searchValue[$this->getSearchFieldName(1)])
				&&
				$searchValue[$this->getSearchFieldName(1)] == $year 
			) {
				$html .= ' selected=selected';
			}
			$html .= '>';
			$html .= $year;
			$html .= '</option>';
		}
		$html .= '</select>';		

		return $html;
	}

	function getInputHTML() {
		$startYear = $this->getParam('startYear',(date('Y')-70));
		$endYear = $this->getParam('endYear',date('Y'));
		$value = $this->getInputValue();
		
		$html = '';
		$html .= '<select name="' . $this->getInputFieldName() . '">';
		$html .= '<option value="">&nbsp;</option>';
		for($year=$endYear;$year>=$startYear;$year--) {
			$html .= '<option value="' . $year . '"';
			if( $year == $value ) {
				$html .= ' selected';
			}
			$html .= '>' . $year . '</option>';
		}
		$html .= '</select>';		
		return $html;
	}
	
	function getWhereCondition() {
		$args = func_get_args();
		$fieldname = 'cfv#.value';
		if( ($args[1] >= 1 || $args[1] <= 3) && is_numeric($args[0]) ) {
			switch($args[1]) {
				case 1:
					return $fieldname . ' = \'' . $args[0] . '\'';
					break;
				case 2:
					return $fieldname . ' > \'' . $args[0] . '\'';
					break;
				case 3:
					return $fieldname . ' < \'' . $args[0] . '\'';
					break;
			}
		} else {
			return null;
		}
	}	
}
?>