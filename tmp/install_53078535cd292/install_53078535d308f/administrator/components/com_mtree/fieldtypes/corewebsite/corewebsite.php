<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corewebsite extends mFieldType_weblink {
	var $name = 'website';
	
	function getOutput($view=1) {
		$maxUrlLength = $this->getParam('maxUrlLength',60);
		$text = $this->getParam('text','');
		$openNewWindow = $this->getParam('openNewWindow',1);
		$useMTVisitRedirect = $this->getParam('useMTVisitRedirect',1);
		$hideProtocolOutput = $this->getParam('hideProtocolOutput',1);
		$useNofollow = $this->getParam('useNofollow',0);

		$html = '';
		$html .= '<a href="';
		
		if($useMTVisitRedirect) {
			global $Itemid;
			$html .= JRoute::_('index.php?option=com_mtree&task=visit&link_id=' . $this->getLinkId() . '&Itemid=' . $Itemid);
		} else {
			$html .= $this->getValue();
		}
		
		$html .= '"';
		
		if( $openNewWindow == 1 ) {
			$html .= ' target="_blank"';
		}

		if($useNofollow)
		{
			$html .= ' rel="nofollow"';
		}

		$html .= '>';

		if(!empty($text)) {
			$html .= $text;
		} else {
			$value = $this->getValue();
			if(strpos($value,'://') !== false && $hideProtocolOutput) {
				$value = substr($value,(strpos($value,'://')+3));

				// If $value has a single slash and this is at the end of the string, we can safely remove this.
				if( substr($value,-1) == '/' && substr_count($value,'/') == 1 )
				{
					$value = substr($value,0,-1);
				}
			}
			if( empty($maxUrlLength) || $maxUrlLength == 0 ) {
				$html .= $value;
			} else {
				$html .= substr($value,0,$maxUrlLength);
				if( strlen($value) > $maxUrlLength ) {
					$html .= $this->getParam('clippedSymbol');
				}
			}
		}
		$html .= '</a>';
		return $html;
	}
	
	function getInputHTML() {
		$showGo = $this->getParam('showGo',1);
		$showSpider = $this->getParam('showSpider',0);
		$inBackEnd = (substr(dirname($_SERVER['PHP_SELF']),-13) == 'administrator') ? true : false;
		$html = '';
		$html .= '<input type="url"'
			. ' name="' . $this->getInputFieldName(1) . '"'
			. ($this->isRequired() ? ' required':'')
			. $this->getDataValidatorAttr()
			. ' id="' . $this->getInputFieldID(1) . '"'
			. ' size="' . ($this->getSize()?$this->getSize():'30') . '"'
			. ' value="' . htmlspecialchars($this->getInputValue()) . '"'
			. ' />';
		if($showGo && $inBackEnd) {
			$html .= '&nbsp;';
			$html .= '<button type="button" class="btn" onclick=\'';
			$html .= 'javascript:window.open("index.php?option=com_mtree&task=openurl&url="+escape(document.getElementById("cf'.$this->getId().'").value))\'';
			$html .= '>';
			$html .= '<i class="icon-out-2"></i> ';
			$html .= JText::_( 'FLD_COREWEBSITE_GO' );
			$html .= '</button>';
		}
		
		if($showSpider && $inBackEnd) {
			$html .= '&nbsp;';
			$html .= '<button type="button" class="btn" onclick=\'';
			$html .= 'javascript: ';
			$html .= 'jQuery("#spiderwebsite").html("' . JText::_( 'FLD_COREWEBSITE_SPIDER_PROGRESS' ) . '");';
			$html .= 'jQuery.getJSON(
				JURI_ROOT
				+"/administrator/index.php?option=com_mtree&task=ajax&task2=spiderurl&url="
				+document.getElementById("cf'.$this->getId().'").value
				+"&no_html=1"
				+"&format=json"
				+(jQuery("input[name=\"is_admin\"]").val()?"&is_admin=1":""), 
				function(data) {
					if(data.message){
						jQuery("#spiderwebsite").html(data.message);
					}
					if(data.status == "OK"){
						jQuery("#publishing_metakey").val(data.metakey);
						jQuery("#publishing_metadesc").val(data.metadesc);
					}
				}
			);';
			$html .= '\' ';
			$html .= '>';
			$html .= JText::_( 'FLD_COREWEBSITE_SPIDER' );
			$html .= '</button>';
			$html .= '<span id="spider' . $this->getInputFieldName(1) . '" style="margin-left:5px;background-color:white"></span>';
		}
		return $html;
	}
	
}
?>