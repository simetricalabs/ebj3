<?php
/**
 * @version	$Id$
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_associatedListing extends mFieldType
{
	function getInputHTML()
	{
		$database =& JFactory::getDBO();

		$assoc_link 	= $this->getAssocLink();
		$cat_id 	= $this->getAssocLink('cat_id');

		if( $cat_id > 0 )
		{
			$database->setQuery( 'SELECT lft, rgt FROM #__mt_cats WHERE cat_id = ' . $cat_id . ' LIMIT 1' );
			$assoc_cat = $database->loadObject();

			$database->setQuery(
				"SELECT l.link_id, l.link_name FROM #__mt_links AS l "
				.	"\n LEFT JOIN #__mt_cl AS cl ON l.link_id = cl.link_id  AND cl.main = 1"
				.	"\n LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id "
				.	"\n WHERE cat.lft >= " . $assoc_cat->lft . ' AND cat.rgt <= ' . $assoc_cat->rgt

				);
			$assoc_links = $database->loadObjectList();

			$html = '<select'.($this->isRequired() ? ' required':'').' name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldID(1) . '">';
			$html .= '<option value="">&nbsp;</option>';
			foreach($assoc_links AS $assoc_link) {
				$html .= '<option value="'.htmlspecialchars($assoc_link->link_id).'"';
				if( $assoc_link->link_id == $this->getInputValue() )
				{
					$html .= ' selected';
				}
				$html .= '>' . $assoc_link->link_name . '</option>';
			}
			$html .= '</select>';
			return $html;	
		}
		else {
			return false;
		}
	}
	
	/**
	* Return the formatted output
	* @param int Type of output to return. Especially useful when you need to display expanded 
	*		 information in detailed view and use can use this display a summarized version
	*		 for summary view. $view = 1 for Normal/Details View. $view = 2 for Summary View.
	* @return str The formatted value of the field
	*/
	function getOutput($view=1) {
		$prefixCode = trim($this->getParam('prefixCode',''));
		$suffixCode = trim($this->getParam('suffixCode',''));

		$assoc_link_id = $this->getAssocLink('link_id');

		$html = '';
		
		if( !empty($assoc_link_id) )
		{
			$html .= $prefixCode;
			$html .= '<a href="'.JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$this->getAssocLink('link_id')).'">';
			$html .= $this->getAssocLink('link_name');
			$html .= '</a>';
			$html .= $suffixCode;
		}

		return $html;
	}

	function getValue() {
		return $this->getAssocLink('link_id');
	}

	function hasValue() { 
		$assoc_link_id = $this->getAssocLink('link_id');
		if( !empty($assoc_link_id) ) {
			return true;
		} else {
			return false;
		}
	}

	function parseValue( $value ) {
		$database =& JFactory::getDBO();
		
		if( !empty($value) )
		{
			$database->setQuery(
				"INSERT INTO #__mt_links_associations (link_id1, link_id2) "
				.	"\n VALUES( " 
				.	$database->Quote($value) . ", " . $database->Quote($this->getLinkId())
				.	"\n )" 
				);
			$database->execute();
		}
		
		return '';
	}

}
?>