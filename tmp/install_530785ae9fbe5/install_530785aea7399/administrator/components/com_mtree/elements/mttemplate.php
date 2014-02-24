<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

/**
 * Renders a list of Mosets Tree templates
 *
 * @author 	Lee Cher Yeong <mtree@mosets.com>
 * @package 	Mosets Tree
 * @subpackage	FormField
 * @since	3.5
 */

class JFormFieldMTTemplate extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'MTTemplate';

	/**
	 * Method to get the templates options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		$templateDirs	= JFolder::folders(JPATH_ROOT . '/components/com_mtree/templates');
		$templates[] = JHtml::_('select.option', '', ( (!empty($parent_template)) ? 'Default ('.$parent_template.')' : 'Default' ) );

		foreach($templateDirs as $templateDir) {
			if ( $templateDir <> "index.html") $templates[] = JHtml::_('select.option', $templateDir, $templateDir );
		}

		return $templates;
	}
}
