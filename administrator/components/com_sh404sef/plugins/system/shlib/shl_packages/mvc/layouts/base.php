<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2013
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.8.369
 * @date		2013-12-21
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die;

if (defined(JVERSION) && version_compare(JVERSION, '3', 'ge'))
{
	class ShlMvcLayout_Base extends JLayoutBase
	{
	}
}
else
{

	/**
	 * Base class for rendering a display layout
	 *
	 * @since       0.2.1
	 */
	class ShlMvcLayout_Base implements ShlMvcLayout
	{

		/**
		 * Method to render the layout.
		 *
		 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
		 *
		 * @return  string  The necessary HTML to display the layout
		 *
		 * @since   0.2.1
		 */
		public function render($displayData)
		{
			return '';
		}

		/**
		 * Method to escape output.
		 *
		 * @param   string  $output  The output to escape.
		 *
		 * @return  string  The escaped output.
		 *
		 * @since   0.2.1
		 */
		protected function escape($output)
		{
			return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
		}
	}
}
