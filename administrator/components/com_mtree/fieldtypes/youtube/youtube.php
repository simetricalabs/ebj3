<?php
/**
 * @version	$Id: youtube.php 2116 2013-10-19 06:43:46Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_youtube extends mFieldType {

	function getOutput() {
		$html ='';
		$id = $this->getVideoId();

		$params['youtubeWidth'] = $this->getParam('youtubeWidth',560);
		$params['youtubeHeight'] = $this->getParam('youtubeHeight',315);

		$html .='<iframe';
		$html .= ' class="youtube-player"';
		$html .= ' type="text/html"';
		$html .= ' width="'.$params['youtubeWidth'].'"';
		$html .= ' height="'.$params['youtubeHeight'].'"';
		$html .= ' src="http://www.youtube.com/embed/'.$id.'"';
		$html .= ' allowfullscreen';
		$html .= ' frameborder="0"';
		$html .= '>';
		$html .= '</iframe>';

		return $html;
	}
	
	function getVideoId() {
		$value = $this->getValue();
		$id = null;
		
		if(empty($value))
		{
			return null;
		}
		$url = parse_url($value);
		
		if( $url['host'] == 'youtu.be' )
		{
			$id = substr($url['path'],1);
		}
		else
		{
			parse_str($url['query'], $query);
			if (isset($query['v'])) {
		        	$id = $query['v'];
			}
		}

		return $id;
	}
	
	function getInputHTML() {
		$youtubeInputDescription = $this->getParam('youtubeInputDescription','Enter the full URL of the Youtube video page.<br />ie: <b>http://youtube.com/watch?v=OHpANlSG7OI</b>');

		$html = '';
		$html .= '<input type="text" name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldName(1) . '" size="' . $this->getSize() . '" value="' . htmlspecialchars($this->getInputValue()) . '" />';
		
		if(!empty($youtubeInputDescription))
		{
			$html .= '<p>' . $youtubeInputDescription . '</p>';
		}

		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false )
	{
		$checkboxLabel = $this->getParam('checkboxLabel','Contains video');
		$checkbox_value = $this->getSearchValue();
		
		$html = '';
		$html .= '<label for="' . $this->getName() . '" class="checkbox">';
		$html .= '<input type="checkbox" name="' . $this->getSearchFieldName(1) . '"';
		$html .=' value="1"';
		$html .=' id="' . $this->getSearchFieldName(1) . '"';
		if( $showSearchValue && $checkbox_value == 1 ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= '&nbsp;';
		$html .= $checkboxLabel;
		$html .= '</label>';
		return $html;
	}
	
	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			return '(cfv#.value <> \'\')';
		}
	}
}
?>