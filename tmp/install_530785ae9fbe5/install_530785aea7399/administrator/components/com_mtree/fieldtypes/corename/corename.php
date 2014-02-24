<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corename extends mFieldType {
	var $name = 'link_name';
	function getOutput($view=1) {
		$params['maxSummaryChars'] = intval($this->getParam('maxSummaryChars',55));
		$params['maxDetailsChars'] = intval($this->getParam('maxDetailsChars',0));
		$value = $this->getValue();
		$output = '';
		if($view == 1 AND $params['maxDetailsChars'] > 0 AND JString::strlen($value) > $params['maxDetailsChars']) {
			$output .= JString::substr($value,0,$params['maxDetailsChars']);
			$output .= '...';
		} elseif($view == 2 AND $params['maxSummaryChars'] > 0 AND JString::strlen($value) > $params['maxSummaryChars']) {
			$output .= JString::substr($value,0,$params['maxSummaryChars']);
			$output .= '...';
		} else {
			$output = $value;
		}
		return $output;
	}
}
?>