<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date		2014-01-23
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperOgp
{
	protected static $_definitions = null;

	public static function getDefinitions()
	{
		if (is_null(self::$_definitions))
		{
			self::$_definitions = ShlSystem_Xml::fromFile(sh404SEF_ADMIN_ABS_PATH . 'helpers/ogp.xml');
		}

		return self::$_definitions;
	}

	/**
	 * Method to create a select list of possible Open Graph object types
	 *
	 * @access  public
	 * @param int ID of current item
	 * @param string name of select list
	 * @param boolean if true, changing selected item will submit the form (assume is an "adminForm")
	 * @param boolean, if true, a line 'Select all' is inserted at the start of the list
	 * @param string the "Select all" to be displayed, if $addSelectAll is true
	 * @return  string HTML output
	 */
	public static function buildOpenGraphTypesList($current, $name, $autoSubmit = false, $addSelectDefault = false, $selectDefaultTitle = '',
		$customSubmit = '')
	{
		// build html options
		$data = array();
		foreach (self::getDefinitions() as $typeDef)
		{
			$data[] = array('id' => $typeDef['name'], 'title' => JText::_($typeDef['title']));
		}

		// add select all option
		if ($addSelectDefault)
		{
			$selectDefault = array('id' => SH404SEF_OPTION_VALUE_USE_DEFAULT, 'title' => $selectDefaultTitle);
			array_unshift($data, $selectDefault);
		}

		// use helper to build html
		$list = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit, $addSelectAll = false, $selectAllTitle = '', $customSubmit);

		// return list
		return $list;
	}
}
