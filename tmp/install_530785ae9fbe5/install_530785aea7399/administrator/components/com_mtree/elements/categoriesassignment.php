<?php
/**
 * @version	$Id: categoriesassignment.php 979 2011-01-05 06:57:47Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2010 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('checkboxes');

/**
 * Renders a list of fields
 *
 * @author 	Lee Cher Yeong <mtree@mosets.com>
 * @package 	Mosets Tree
 * @subpackage	Parameter
 * @since	3.0
 */

class JFormFieldCategoriesAssignment extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Categories Assignment';

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes '.(string) $this->element['class'].'"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="'.$this->id.'"'.$class.'>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '<ul>';
		foreach ($options as $i => $option) {

			// Initialize some option attributes.
			$checked	= ((in_array((string) $option->value, (array) $this->value) || empty($this->value) ) ? ' checked="checked"' : '');
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
			$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';

			// Initialize some JavaScript option attributes.
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<li>';
			$html[] = '<input type="checkbox" id="'.$this->id.$i.'" name="'.$this->name.'"' .
				' value="'.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'
				.$checked.$class.$onclick.$disabled.'/>';

			$html[] = '<label for="'.$this->id.$i.'"'.$class.'>'.JText::_($option->text).'</label>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}
	
	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		$db		= & JFactory::getDBO();
		$db->setQuery( 'SELECT * FROM #__mt_cats WHERE cat_approved = 1 AND cat_parent <= 0 ORDER BY lft ASC' );
		$categories	= $db->loadObjectList();

		foreach ($categories as $category)
		{
			$tmp = JHtml::_('select.option', (string) $category->cat_id, trim((string) $category->cat_name), 'value', 'text');
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
