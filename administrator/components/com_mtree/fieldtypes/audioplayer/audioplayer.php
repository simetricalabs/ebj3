<?php
/**
 * @version	$Id: audioplayer.php 1270 2011-11-24 02:09:12Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_audioplayer extends mFieldType_file {
	function getJSValidationFunction() {
		return 'function(){return hasExt(document.mtForm.' . $this->getName() . '.value,\'mp3\')}';
	}

	function getJSValidationMessage() {
		return JText::_( 'FLD_AUDIOPLAYER_PLEASE_SELECT_AN_AUDIO_FILE' );
	}
	
	function getOutput() {
		$id = $this->getId();
		$params['text'] = $this->getParam('textColour');
		$params['displayfilename'] = $this->getParam('displayfilename',1);
		$params['slider'] = $this->getParam('sliderColour');
		$params['loader'] = $this->getParam('loaderColour');
		$params['track'] = $this->getParam('trackColour');
		$params['border'] = $this->getParam('borderColour');
		$params['bg'] = $this->getParam('backgroundColour');
		$params['leftbg'] = $this->getParam('leftBackgrounColour');
		$params['rightbg'] = $this->getParam('rightBackgrounColour');
		$params['rightbghover'] = $this->getParam('rightBackgroundHoverColour');
		$params['lefticon'] = $this->getParam('leftIconColour');
		$params['righticon'] = $this->getParam('rightIconColour');
		$params['righticonhover'] = $this->getParam('rightIconHoverColour');
		
		$html = '';
		$html .= '<script language="JavaScript" src="' . $this->getFieldTypeAttachmentURL('audio-player.js'). '"></script>';
		$html .= "\n" . '<object type="application/x-shockwave-flash" data="' . $this->getFieldTypeAttachmentURL('player.swf'). '" id="audioplayer' . $id . '" height="24" width="290">';
		$html .= "\n" . '<param name="movie" value="' . $this->getFieldTypeAttachmentURL('player.swf') . '">';
		$html .= "\n" . '<param name="FlashVars" value="';
		$html .= 'playerID=' . $id;
		$html .= '&amp;soundFile=' . urlencode($this->getDataAttachmentURL());
		foreach( $params AS $key => $value ) {
			if(!empty($value)) {
				$html .= '&amp;' . $key . '=0x' . $value;
			}
		}
		$html .= '">';
		$html .= "\n" . '<param name="quality" value="high">';
		$html .= "\n" . '<param name="menu" value="false">';
		$html .= "\n" . '<param name="wmode" value="transparent">';
		$html .= "\n" . '</object>';
		if($params['displayfilename']) {
			$html .= "\n<br />";
			$html .= "\n" . '<a href="' . $this->getDataAttachmentURL() . '" target="_blank">';
			$html .= $this->getValue();
			$html .= '</a>';
		}
		return $html;
	}
}
?>