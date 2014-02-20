<?php
/**
 * @version	$Id: coredesc.php 2011 2013-08-02 11:10:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coredesc extends mFieldType {
	var $name = 'link_desc';
	function parseValue($value) {
		$params['maxChars'] = intval($this->getParam('maxChars',3000));
		$params['stripAllTagsBeforeSave'] = $this->getParam('stripAllTagsBeforeSave',0);
		$params['allowedTags'] = $this->getParam('allowedTags','u,b,i,a,ul,li,pre,blockquote,strong,em');
		if($params['stripAllTagsBeforeSave']) {
			$value = $this->stripTags($value,$params['allowedTags']);
		}
		if($params['maxChars'] > 0) {
			$value = JString::substr( $value, 0, $params['maxChars']);
		}
		return $value;
	}
	
	function getInputHTML()
	{
		global $mtconf;
		
		$value = $this->getInputValue();

		if( $this->isUsingWysiwygEditor() ) {
			$editor = &JFactory::getEditor();
			$html = $editor->display( $this->getInputFieldName(1), $value , '100%', '250', '75', '25', array('pagebreak', 'readmore'), $this->getInputFieldId(1) );
		} else {
			$html = '<textarea'
				. ($this->isRequired() ? ' required':'')
				. $this->getDataValidatorAttr()
				. ' class="'.($this->isRequired() ? ' required':'').'"'
				. ' name="' . $this->getInputFieldName(1).'"'
				. ' id="' . $this->getInputFieldId(1) . '"'
				. ' style="width:42%;height:' . $this->getSize() . 'px">' 
				. htmlspecialchars($value)
				. '</textarea>';
		}
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false ) {
		$html = '';
		$html .= '<input type="text" name="' . $this->getName();
		
		if( $showSearchValue && $this->getSearchValue() !== false ) {
			$html .= '" value="'.$this->getSearchValue();
		}
		
		if( $showPlaceholder && $this->getPlaceholderText() !== false ) {
			$html .= '" placeholder="'.htmlspecialchars($this->getPlaceholderText());
		}
		
		$html .= '" size="30"';
		$html .= ' />';
		return $html;
	}
	function getOutput($view=1) {
		$params['parseUrl'] = $this->getParam('parseUrl',1);
		$params['summaryChars'] = $this->getParam('summaryChars',255);
		$params['stripSummaryTags'] = $this->getParam('stripSummaryTags',1);
		$params['convertSpecialCharsInSummary'] = $this->getParam('convertSpecialCharsInSummary',1);
		$params['stripDetailsTags'] = $this->getParam('stripDetailsTags',1);
		$params['parseMambots'] = $this->getParam('parseMambots',0);
		$params['allowedTags'] = $this->getParam('allowedTags','u,b,i,a,ul,li,pre,blockquote,strong,em');
		$params['showReadMore'] = $this->getParam('showReadMore',0);
		$params['whenReadMore'] = $this->getParam('whenReadMore',0);
		$params['txtReadMore'] = $this->getParam('txtReadMore',JText::_( 'FLD_COREDESC_READ_MORE' ));
		
		$html = $this->getValue();
		$output = '';
		
		// Details view
		if($view == 1) {
			global $mtconf;
			$output = $html;
			if($params['stripDetailsTags']) {
				$output = $this->stripTags($output,$params['allowedTags']);
			}
			if($params['parseUrl']) {
				$regex = '/http:\/\/(.*?)(\s|$)/i';
				$output = preg_replace_callback( $regex, array($this,'linkcreator'), $output );
			}
			if (!$mtconf->get('use_wysiwyg_editor') && $params['stripDetailsTags'] && !in_array('br',explode(',',$params['allowedTags'])) && !in_array('p',explode(',',$params['allowedTags'])) ) {
				$output = nl2br(trim($output));
			}
			if($params['parseMambots']) {
				$this->parseMambots($output);
			}
		// Summary view
		} else {
			$html = preg_replace('@{[\/\!]*?[^<>]*?}@si', '', $html);
			if($params['stripSummaryTags']) {
				$html = strip_tags( $html );
			}
			if($params['summaryChars'] > 0) {
				$trimmed_desc = trim(JString::substr($html,0,$params['summaryChars']));
			} else {
				$trimmed_desc = '';
			}
			if($params['convertSpecialCharsInSummary']) {
				$html = htmlspecialchars( $html );
				$trimmed_desc = htmlspecialchars( $trimmed_desc );
			}
			if (JString::strlen($html) > $params['summaryChars']) {
				$output .= $trimmed_desc;
				$output .= ' <b>...</b>';
			} else {
				$output = $html;
			}
			if( $params['showReadMore'] && ($params['whenReadMore'] == 1 || ($params['whenReadMore'] == 0 && JString::strlen($html) > $params['summaryChars'])) ) {
				if(!empty($trimmed_desc)) {
					$output .= '<br />';
				}
				$output .= ' <a href="' . JRoute::_('index.php?option=com_mtree&task=viewlink&link_id=' . $this->getLinkId()) . '" class="readon">' . $params['txtReadMore'] . '</a>';
			}
		}
		return $output;
	}

	function getJSOnInit() {
		if( $this->isRequired() )
		{
			return 'jQuery(\'#'.$this->getInputFieldId(1).'\').attr(\'required\',true)';
		}
		return null;
	}

	function getJSOnSave()
	{
		if( $this->isUsingWysiwygEditor() )
		{
			return JFactory::getEditor()->save($this->getInputFieldId(1));
		}
		return null;
	}
	
	function isUsingWysiwygEditor()
	{
		global $mtconf;
		
		if( ($this->inBackEnd() AND $mtconf->get('use_wysiwyg_editor_in_admin')) || (!$this->inBackEnd() AND $mtconf->get('use_wysiwyg_editor')) ) {
			return true;
		}
		return false;
	}
}
?>