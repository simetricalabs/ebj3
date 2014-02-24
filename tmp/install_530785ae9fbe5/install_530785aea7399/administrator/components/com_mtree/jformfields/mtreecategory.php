<?php
/**
 * @package	Mosets Tree
 * @subpackage	JFormFields
 * @copyright	Copyright (C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of Mosets Tree categories
 *
 * @package	Mosets Tree
 * @subpackage	JFormFields
 * @since	3.0
 */
class JFormFieldMtreeCategory extends JFormFieldList
{
	/**
	 * @var		string	The form field type.
	 * @since	3.0
	 */
	public $type = 'MtreeCategory';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	3.0
	 */
	protected function getInput()
	{
		$db = JFactory::getDBO();
		
		$db->setQuery( 'SELECT cat.cat_id, cat.cat_name, (COUNT(parent.cat_name) - 1) AS depth '
			. ' FROM #__mt_cats AS cat, #__mt_cats AS parent '
			. ' WHERE cat.cat_approved=1 AND cat.cat_published=1 '
			. ' AND cat.lft BETWEEN parent.lft AND parent.rgt '
			. ' GROUP BY cat.cat_id'
			. ' ORDER BY cat.lft ASC' 
			);
		$categories = $db->loadObjectList();
		$total_categories = count($categories);
		
		for( $i=0; $i<$total_categories; $i++ )
		{
			$categories[$i]->cat_name = str_repeat('- ',max(0,($categories[$i]->depth -1))) . $categories[$i]->cat_name;
		}

		$options = JHtml::_('select.genericlist', $categories, $this->name, '', 'cat_id', 'cat_name', $this->value );
		
		return $options;
	}
}
?>