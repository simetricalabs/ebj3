<?php
/**
 * @version	$Id: mtfields.php 979 2011-01-05 06:57:47Z cy $
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
 * @since	2.2
 */

class JFormFieldMTFields extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Fields';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		$db		= & JFactory::getDBO();
		$db->setQuery( 'SELECT * FROM #__mt_customfields WHERE published = 1' );
		$fields		= $db->loadObjectList();

		foreach ($fields as $field)
		{
			$tmp = JHtml::_('select.option', (string) $field->cf_id, trim((string) $field->caption), 'value', 'text');
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
