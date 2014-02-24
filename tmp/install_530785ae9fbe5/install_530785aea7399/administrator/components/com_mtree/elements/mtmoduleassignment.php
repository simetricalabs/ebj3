<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die();

JFormHelper::loadFieldClass('list');

/**
 * Renders a list of fields
 *
 * @author 	Lee Cher Yeong <mtree@mosets.com>
 * @package 	Mosets Tree
 * @subpackage	Parameter
 * @since	3.0
 */

class JFormFieldMTModuleAssignment extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Module Assignment';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] = JHtml::_('select.option', '0', JText::_('COM_MODULES_OPTION_MENU_ALL'));
		$options[] = JHtml::_('select.option', '-', JText::_('COM_MODULES_OPTION_MENU_NONE'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_MODULES_OPTION_MENU_INCLUDE'));
		$options[] = JHtml::_('select.option', '-1', JText::_('COM_MODULES_OPTION_MENU_EXCLUDE'));

		return $options;
	}
}
