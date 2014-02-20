<?php
/**
 * @version	$Id: monthyear.php 2011 2013-08-02 11:10:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_monthYear extends mFieldType {
	var $numOfInputFields = 2;
	var $numOfSearchFields = 0;
	
	function parseValue( $value ) { 
		if ( is_array($value) && is_numeric($value[0]) && is_numeric($value[1]) ) {
			return $value[1] . str_pad($value[0],2,'0',STR_PAD_LEFT);
		} else {
			return '';
		}
	}
	
	function getOutput() {
		$dateFormat = $this->getParam('dateFormat','%m %Y');
		$value = $this->getValue();
		
		setlocale(LC_TIME, JFactory::getLanguage()->getLocale());
		
		return strftime($dateFormat,mktime(0,0,0,intval(substr($value,4,2)),1,intval(substr($value,0,4))));
	}
	
	function getInputHTML() {
		$startYear = $this->getParam('startYear',(date('Y')-70));
		$endYear = $this->getParam('endYear',date('Y'));
		$value = $this->getInputValue();
		
		setlocale(LC_TIME, JFactory::getLanguage()->getLocale());
		
		if(empty($value)) {
			$monthValue = 0;
			$yearValue = 0;
		} else {
			$monthValue = intval(substr($value,4,2));
			$yearValue = intval(substr($value,0,4));
		}
		
		$html = '';
		$html .= '<select name="' . $this->getInputFieldName(1) . '" style="width:150px">';
		$html .= '<option value="">&nbsp;</option>';
		for($month=1;$month<=12;$month++) {
			$html .= '<option value="' . $month . '"';
			if( $month == $monthValue ) {
				$html .= ' selected';
			}
			$html .= '>' . strftime('%B', mktime(0, 0, 0, $month)) . '</option>';
		}
		$html .= '</select>';
		
		$html .= '<select name="' . $this->getInputFieldName(2) . '" style="width:120px">';
		$html .= '<option value="">&nbsp;</option>';
		for($year=$endYear;$year>=$startYear;$year--) {
			$html .= '<option value="' . $year . '"';
			if( $year == $yearValue ) {
				$html .= ' selected';
			}
			$html .= '>' . $year . '</option>';
		}
		$html .= '</select>';		
		return $html;
	}
	
	function getWhereCondition() {
		return null;
	}
}
?>