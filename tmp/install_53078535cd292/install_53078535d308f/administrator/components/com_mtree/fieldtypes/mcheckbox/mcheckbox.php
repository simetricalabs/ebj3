<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_mcheckbox extends mFieldType_checkbox {
	
	function getInputHTML()
	{
		$columns	= $this->getParam('columns',0);
		$useCaptions	= $this->getParam('useCaptions',0);
		$checkbox_values= $this->getInputValue();
		
		$i = 0;
		$html = '';

		$html .= '<ul>';
		foreach($this->arrayFieldElements AS $fieldElement) {
			if($columns > 0)
			{
				$html .= '<div style="width:' . floor(100 / $columns) . '%;float:left;">';
			}
			$html .= '<li>';
			$html .= '<label for="' . $this->getInputFieldName(1) . '_' . $i . '" class="checkbox">';
			$html .= '<input type="checkbox"'
				. ($this->isRequired() ? ' required':'')
				. $this->getDataValidatorAttr()
				. ' name="' . $this->getInputFieldName(1) . '[]"'
				. ' value="'.$fieldElement.'"'
				. ' id="' . $this->getInputFieldName(1) . '_' . $i . '" ';

			if( in_array($fieldElement,$checkbox_values) )
			{
				$html .= 'checked ';
			}
			
			$html .= '/>';
			
			if( $useCaptions != 0 ) {
				$html .= $this->getCheckboxLabel($i,true);
			} else {
				$html .= $this->getCheckboxLabel($i,false);
			}
			
			$html .= '</label>';
			$html .= '</li>';
			// '<br>';
			if($columns > 0) {
				$html .= '</div>';
			}
			$i++;
		}
		$html .= '</ul>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$i = 0;
		$html = '';
		
		if( $this->getSearchValue() !== false ) {
			$checkbox_values = $this->getSearchValue();
		} else {
			$checkbox_values = array();
		}
		
		$html .= '<ul>';
		// $html .= '<ul style="margin:0;padding:0;list-style-type:none">';
		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<li';
			if( $showSearchValue && in_array($fieldElement,$checkbox_values) ) {
				$html .= ' class="active"';
			}
			$html .= '>';
			$html .= '<label for="' . $idprefix . $this->getName() . '_' . $i . '" class="checkbox">';
			$html .= '<input type="checkbox" name="' . $this->getName();
			$html .= '[]" value="'.htmlspecialchars($fieldElement);
			$html .= '" id="' . $idprefix . $this->getName() . '_' . $i . '" ';
			if( $showSearchValue && in_array($fieldElement,$checkbox_values) ) {
				$html .= 'checked ';
			}
			$html .= '/>';
			$html .= $this->getCheckboxLabel($i);
			$html .= '</label>';
			$html .= '</li>';
			$i++;
		}
		$html .= '</ul>';
		
		return $html;
	}

	function getOutput($view=1) {
		$useCaptions	= $this->getParam('useCaptions',0);
		$arrayValue 	= explode('|',$this->value);
		$arrOutput 	= array();
		
		foreach( $arrayValue AS $value ) {
			if(array_search($value,$this->arrayFieldElements) !== false) {
				$output = '';
				if( $this->tagSearch )
				{
					$output .= '<a';
					$output .= ' class="tag"';
					$output .= ' href="';
					$output .= JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$this->getId().'&value='.urlencode($value));
					$output .= '">';
					$output .= $this->getCheckboxLabel(array_search($value,$this->arrayFieldElements),(($useCaptions == 1)?true:false));
					$output .= '</a>';
				}
				else
				{
					$output .= $this->getCheckboxLabel(array_search($value,$this->arrayFieldElements),(($useCaptions == 1)?true:false));
				}
				$arrOutput[] = $output;
			}
		}
		
		$html = '';
		switch($view)
		{
			# Details view
			case '1':
				$html = '';
				switch($this->getParam('dvOutput','h'))
				{
					case 'ul':
						$html .= '<ul><li>' . implode('</li><li>',$arrOutput). '</li></ul>';
						break;
					case 'ol':
						$html .= '<ol><li>' . implode('</li><li>',$arrOutput). '</li></ol>';
						break;
					case 'v':
						$html .= implode('<br />',$arrOutput);
						break;
					case 'h':
					default:
						$html .= implode('&nbsp;',$arrOutput);
				}
				break;
			# Summary view
			case '2':
				$html .= implode('&nbsp;',$arrOutput);
				break;
		}
		return $html;
	}
	
	function getCheckboxLabel($i,$showCaption=true) {
		global $mtconf;
		
		$captions 		= $this->getParam('captions'	, ''	);
		$showImages 	= $this->getParam('showImages'	, 1		);
		$useCaptions 	= $this->getParam('useCaptions'	, 0		);
		$alts 			= $this->getParam('alts'		, ''	);
		$titles 		= $this->getParam('titles'		, ''	);
		$live_site 		= $mtconf->getjconf('live_site'			);

		if( $showCaption && $useCaptions != 0 ) {
			$useCaptions = true;
		} else {
			$useCaptions = false;
		}

		$arrImages = $this->getParam('images','');
		$arrAlts = $this->getParam('alts','');
		$arrTitles = $this->getParam('titles','');
		
		if( !is_array($arrImages) ) {
			$arrImages = explode("|",$arrImages);
		}
		if( !is_array($arrAlts) ) {
			$arrAlts = explode("|",$arrAlts);
		}
		if( !is_array($arrTitles) ) {
			$arrTitles = explode("|",$arrTitles);
		}
		
		// if(is_string($arrImages)) {
		// 	$arrImages = array($arrImages);
		// }
		// if(is_string($arrAlts)) {
		// 	$arrAlts = array($arrAlts);
		// }
		// if(is_string($arrTitles)) {
		// 	$arrTitles = array($arrTitles);
		// }
		if(substr($live_site,-1)=='/') {
			$live_site = substr($live_site,0,-1);
		}
		
		$arrCaptions = array();
		if($useCaptions && is_string($captions) ) {
			$arrCaptions = explode('|',$captions);
		} elseif( is_array($captions) ) {
			$arrCaptions = $captions;
		}
		
		if(
			is_numeric($i) && isset($arrImages[$i]) && !empty($arrImages[$i])
			&&
			(
				($showImages == 1)
				||
				($showImages == 2 && !$this->inBackEnd())
			)
		) {
			$html = '';
			$html .= '<img';
			$html .= ' src="' . trim(str_replace('{live_site}',$live_site,$arrImages[$i])) . '"';
			if(isset($arrAlts[$i]) && !empty($arrAlts[$i])) {
				$html .= ' alt="'.htmlspecialchars(trim($arrAlts[$i])).'"';
			} else {
				$html .= ' alt="'.htmlspecialchars(trim($this->arrayFieldElements[$i])).'"';
			}
			if(isset($arrTitles[$i]) && !empty($arrTitles[$i])) {
				$html .= ' title="'.htmlspecialchars(trim(strip_tags($arrTitles[$i]))).'"';
			}
			$html .= ' />';
			if($useCaptions && isset($arrCaptions[$i]) && !empty($arrCaptions[$i])) {
				$html .= $arrCaptions[$i];
			}
			return $html;
		} elseif( isset($arrCaptions[$i]) && !empty($arrCaptions[$i]) ) {
			return $arrCaptions[$i];
		} else {
			return $this->arrayFieldElements[$i];
			
		}
	}
}
?>