<?php
/**
 * @version	$Id: corecity.php 1109 2011-05-26 10:54:42Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.'/components/com_mtree/mtree.tools.php');

class mFieldType_category extends mFieldType {
	var $numOfInputFields = 0; 
	
	/**
	* Return the formatted output
	* @param int Type of output to return. Especially useful when you need to display expanded 
	*		 information in detailed view and use can use this display a summarized version
	*		 for summary view. $view = 1 for Normal/Details View. $view = 2 for Summary View.
	* @return str The formatted value of the field
	*/
	function getOutput($view=1) {
		$html = '';
		$html .= '<a href="';
		$html .= JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$this->getCatId());
		$html .= '">';
		$html .= $this->getCatName();
		$html .= '</a>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' )
	{
		$cat_id = $this->getParam( 'cat_id', 0 );

		getCatsSelectlist( $cat_id, $cat_tree, 1 );
		$value = '';
		$catlist = '';
		
		if( !empty($cat_tree) ) {
			$cat_options[] = JHtml::_('select.option', $this->getCatId(), '&nbsp;');
			foreach( $cat_tree AS $ct ) {
				$cat_options[] = JHtml::_('select.option', $ct["cat_id"], str_repeat("-",($ct["level"]*3)) .(($ct["level"]>0) ? "":''). $ct["cat_name"]);
			}
			
			if( $showSearchValue ) {
				$value = $this->getSearchValue();
			}
			$catlist = JHtml::_(
				'select.genericlist', 
				$cat_options, 
				'cfcat_id', 
				'', 
				'value', 
				'text', 
				$this->getSearchValue()
			);
		} else {
			return null;
		}
		
		$return = $catlist;

		// This element identify itself as a category field
		$return .= '<input type="hidden" name="cfcat" value="'.$this->getName().'" />';

		return $return;
	}
	
	function getWhereCondition() {
		return null;
	}

	function getValue($arg=null) {
		return $this->getCatId();
	}

	function hasValue() { return true; }
	
	function parseValue( $value ) { return $this->getCatId(); }

}

?>