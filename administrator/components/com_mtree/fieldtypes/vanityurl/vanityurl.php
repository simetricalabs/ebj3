<?php
/**
 * @version	$Id$
 * @package	Mosets Tree
 * @copyright	(C) 2012-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_vanityUrl extends mFieldType {
	var $username_placeholder = '{username}';
	
	function getJSValidationFunction() {
		return 'function(){return(/^[a-zA-Z0-9._-]+$/i.test(arguments[0].value)==true)}';
	}

	function getJSValidationMessage() {
		return JText::_( 'FLD_VANITYURL_PLEASE_ENTER_A_VALID_USERNAME' );
	}

	function getInputHTML()
	{
		$showGo 	= $this->getParam('showGo',1);
		$urlFormat 	= $this->getParam('urlFormat','http://www.twitter.com/'.$this->username_placeholder);
		
		$html = '';
		$html .= '<input'
			. ' type="text"'
			. ' name="' . $this->getInputFieldName(1) . '"'
			. ($this->isRequired() ? ' required':'')
			. $this->getDataValidatorAttr()
			. ' id="' . $this->getInputFieldID(1) . '"'
			. ' size="' . ($this->getSize()?$this->getSize():'30') . '"';
		$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';
		$html .= ' />';
		if($showGo && $this->inBackEnd())
		{
			$html .= '&nbsp;';
			$html .= '<button class="btn" onclick=\'';
			$html .= 'javascript:var url="'.$urlFormat.'";url=url.replace("'.$this->username_placeholder.'",document.getElementById("' . $this->getInputFieldID(1) . '").value);window.open("index.php?option=com_mtree&task=openurl&url="+escape(url))\'';
			$html .= '>';
			$html .= JText::_( 'COM_MTREE_GO' );
			$html .= '</button>';
		}
		return $html;
	}
	
	function getUrl()
	{
		$urlFormat 	= $this->getParam('urlFormat','http://www.twitter.com/'.$this->username_placeholder);
		return str_replace($this->username_placeholder,$this->getValue(),$urlFormat);
	}
	
	function getOutput() 
	{
		$openNewWindow	= $this->getParam('openNewWindow',1);
		$urlFormat 	= $this->getParam('urlFormat','http://www.twitter.com/'.$this->username_placeholder);
		$displayFormat	= $this->getParam('displayFormat','@'.$this->username_placeholder);
		$showGo 	= $this->getParam('showGo',0);
		$title		= $this->getParam('title','');
		$image		= $this->getParam('image','');
		$useNofollow	= $this->getParam('useNofollow',0);
		$useGA 		= $this->getParam('useGA',0);
		$gaPageTrackDirectory	= $this->getParam('gaPageTrackDirectory','/outgoing/');

		$displayText = str_replace($this->username_placeholder,$this->getValue(),$displayFormat);
		
		$html = '';
		$html .= '<a href="' . $this->getUrl() . '"';
		
		if( $openNewWindow == 1 )
		{
			$html .= ' target="_blank"';
		}
		
		if( !empty($title) )
		{
			$html .= ' title="' . str_replace($this->username_placeholder,$this->getValue(),$title) . '"';
		}
		
		if($useNofollow)
		{
			$html .= ' rel="nofollow"';
		}
		
		if($useGA)
		{
			$html .= ' onClick="javascript: pageTracker._trackPageview(\'' . $gaPageTrackDirectory . $this->striphttp($this->getValue()) . '\');"';
		}
		$html .= '>';

		if(!empty($image))
		{
			global $mtconf;
			$live_site = $mtconf->getjconf('live_site');
			$html .= '<img src="' . trim(str_replace('{live_site}',$live_site,$image)) . '"';
			$html .= ' alt="' . $displayText . '"';
			$html .= ' />';
		}
		else
		{
			$html .= $displayText;
		}

		$html .= '</a>';
		return $html;
	}
	
	function parseValue($value)
	{
		$params['maxChars'] = intval($this->getParam('maxChars',15));
		$value = JString::substr($value,0,$params['maxChars']);
		
		// Allow alphanumeric with dashes, underscores and spaces
		$pattern = '/[^a-zA-Z0-9._-]/';

		return preg_replace($pattern, '', $value);
	}
}
?>