<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2009 - 2010 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');

/**
 * Renders a list of taggable fields
 *
 * @author 	Lee Cher Yeong <mtree@mosets.com>
 * @package 	Mosets Tree
 * @subpackage	Parameter
 * @since	2.1
 */

class JFormFieldTagFields extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Tag Fields';
	
	/**
	* Maximum length of a caption before it's cut off
	*
	* @access	protected
	* @var		int
	*/
	protected $_max_caption_length = 23;
	
	function getInput()
	{
		$db		= & JFactory::getDBO();
		$db->setQuery( 'SELECT cf_id AS value, caption AS text FROM #__mt_customfields WHERE published = 1 AND tag_search = 1' );
		$fields		= $db->loadObjectList();
		
		if( !is_array($this->value) ) {
			$value = array($this->value);
		}
		
		$html = '';
		
		$i = 0;
		foreach( $fields AS $field )
		{
			if( JString::strlen($fields[$i]->text) > ($this->_max_caption_length -3) )
			{
				$fields[$i]->text = JString::substr( $fields[$i]->text, 0, ($this->_max_caption_length -3) ) . '...';
			}
			$i++;
		}
		$html .= JHtml::_('select.genericlist',  $fields, $this->name, '', 'value', 'text', $value[0], $this->id);

		return $html;
	}
}
