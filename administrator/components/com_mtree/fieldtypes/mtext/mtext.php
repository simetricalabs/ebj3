<?php
/**
 * @version	$Id: mtext.php 2011 2013-08-02 11:10:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_mtext extends mFieldType {
	function getOutput($view=1) {
		global $mtconf;

		$params['maxSummaryChars'] 	= intval($this->getParam('maxSummaryChars',55));
		$params['maxDetailsChars'] 	= intval($this->getParam('maxDetailsChars',0));
		$params['parseUrl'] 		= intval($this->getParam('parseUrl',1));

		$value = $this->getValue();
		$output = '';

		if($view == 1 AND $params['maxDetailsChars'] > 0 AND JString::strlen($value) > $params['maxDetailsChars']) {
			$output .= $this->insertBreaks(JString::substr($value,$params['maxDetailsChars']),$view);
			$output .= '...';
		} elseif($view == 2 AND $params['maxSummaryChars'] > 0 AND JString::strlen($value) > $params['maxSummaryChars']) {							
			$output .= $this->insertBreaks(JString::substr($value,0,$params['maxSummaryChars']),$view);
			$output .= '...';
		} else {
			$output = $this->insertBreaks($value,$view);
		}
		
		if($view == 1 AND $params['parseUrl']) {
			$regex = '/http:\/\/(.*?)(\s|$)/i';
			$output = preg_replace_callback( $regex, array($this,'linkcreator'), $output );
		}
		
		return $output;
	}
	function insertBreaks($text,$view) {
		$params['preserveNewline'] = $this->getParam('preserveNewline',1);
		switch($params['preserveNewline']) {
			case 1:
				if($view == 1) {
					$text = nl2br($text);
				}
				break;
			case 2:
				if($view == 2) {
					$text = nl2br($text);
				}
				break;
			case 3:
				$text = nl2br($text);
				break;
			default:
				$text = nl2br($text);
		}
		return $text;
	}
	function getInputHTML() {
		$params['inputType'] = $this->getParam('inputType',1);
		$params['cols'] = $this->getParam('cols',50);
		$params['style'] = $this->getParam('style','');

		switch($params['inputType'])
		{
			case 2:
				$html = '<textarea'
					. ($this->isRequired() ? ' required':'')
					. $this->getDataValidatorAttr()
					. ' class="'.($this->isRequired() ? ' required':'').'"';
				$html .= ' name="' . $this->getInputFieldName(1) . '"';
				$html .= ' id="' . $this->getInputFieldId(1) . '"';
				$html .= ' cols="' . $params['cols'] . '"';
				
				if($this->size < 2)
				{
					$html .= ' rows="2"';
				}
				else
				{
					$html .= ' rows="'.$this->getSize().'"';
				}
				
				if(!empty($params['style']))
				{
					$html .=  ' style="' . $params['style'] . '"';
				}
				
				$html .= ' >';
				$html .= htmlspecialchars($this->getInputValue());
				$html .= '</textarea>';
				
				break;
			default:
				$html = '<input'
					. ($this->isRequired() ? ' required':'')
					. $this->getDataValidatorAttr()
					. ' class="'.($this->isRequired() ? ' required':'').'"';
				$html .= ' type="text"';
				$html .= ' name="' . $this->getInputFieldName(1) . '"';
				$html .= ' id="' . $this->getInputFieldId(1) . '"';
				$html .= ' size="' . $this->getSize() . '"';
				$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';

				if(!empty($params['style']))
				{
					$html .=  ' style="' . $params['style'] . '"';
				}
				
				$html .= ' />';
				break;
		}
		return $html;
	}
	
}
?>