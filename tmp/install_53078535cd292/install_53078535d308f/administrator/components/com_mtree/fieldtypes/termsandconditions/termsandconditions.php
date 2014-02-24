<?php
/**
 * @version	$Id: checkboxwithimage.php 1387 2012-03-27 11:48:15Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

class mFieldType_termsAndConditions extends mFieldType_checkbox {
	var $fieldElement = 1;
	
	function getInputHTML()
	{
		$text		= $this->getParam('text','I have read and agree to the <a href="{article_url}" target="_blank">{article_title}</a>');
		$id		= $this->getParam('id',0);
		
		$display_text = $text;
		if( $id > 0 )
		{
			if( strpos($text,'{article_url}') !== false )
			{
				$display_text = str_replace(
					'{article_url}',
					JRoute::_(ContentHelperRoute::getArticleRoute($id)),
					$display_text
					);
			}
			
			if( strpos($text,'{article_title}') !== false )
			{
				$db = JFactory::getDBO();
				$db->setQuery(
					'SELECT title' .
					' FROM #__content' .
					' WHERE id = '.(int) $id
				);
				$title = $db->loadResult();
				$display_text = str_replace(
					'{article_title}',
					$title,
					$display_text
					);
			}
		}
		
		$checkbox_values= $this->getInputValue();

		$fieldElement = $this->fieldElement;
		$html = '';

		$html .= '<ul>';

		$html .= '<li>';
		$html .= '<label for="' . $this->getInputFieldName(1) . '" class="checkbox">';
		$html .= '<input type="checkbox"'
			. ($this->isRequired() ? ' required':'')
			. $this->getDataValidatorAttr()
			. ' name="' . $this->getInputFieldName(1) . '"'
			. ' value="'.$fieldElement.'"'
			. ' id="' . $this->getInputFieldName(1) . '" ';

		if( in_array($fieldElement,$checkbox_values) )
		{
			$html .= 'checked ';
		}
		
		$html .= '/>';
		$html .= $display_text;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '</ul>';
		return $html;
	}
	
	function getSearchHTML($showSearchValue=false, $showPlaceholder=false, $idprefix='_search') {
		if( $this->getSearchValue() !== false ) {
			$checkbox_values = $this->getSearchValue();
		} else {
			$checkbox_values = array();
		}
		
		$i = 0;
		$html = '';

		$html .= '<input type="checkbox" name="' . $this->getName();
		$html .= '[]" value="'.$this->fieldElement;
		$html .= '" id="' . $idprefix . $this->getName();
		$html .= '_' . $i . '" ';
		if( $showSearchValue && in_array($this->fieldElement,$checkbox_values) ) {
			$html .= 'checked ';
		}
		$html .= '/>';
		// $html .= '<label for="' . $idprefix . $this->getName() . '_' . $i . '">';
		// $html .= JText::_( 'JYES' );
		// $html .= '</label>';

		return $html;
	}

	function getOutput($view=1) {
		if( $this->value == '1' )
		{
			return JText::_( 'JYES' );
		}
		else
		{
			return JText::_( 'JNO' );
		}
	}
}
?>