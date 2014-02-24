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

class Sh404sefHelperHtml extends Sh404sefHelperHtmlBase
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	array	$request current page request variables
	 *
	 * @return	void
	 */
	public static function addSubmenu($request)
	{
		$c = $request->getCmd('c', '');
		$view = $request->getCmd('view', '');
		$layout = $request->getCmd('layout', '');
		$tmpl = $request->getCmd('tmpl', '');
		$shajax = $request->getCmd('shajax', '');
		$format = $request->getCmd('format', 'html');
		$enabledDefault = empty($tmpl) && empty($shajax) && $format == 'html';

		// make sure the language file is loaded
		$language = JFactory::getLanguage();
		$language->load('com_sh404sef.sys');

		// now we can create the sub menu items
		$enabled = $enabledDefault && (($c != '' && $c != 'default') || !empty($view) || !empty($layout));
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$homeLink = $enabled ? '<button class="btn btn-success btn-block" type="button">' . JText::_('COM_SH404SEF_CONTROL_PANEL') . '</button>'
				: JText::_('COM_SH404SEF_CONTROL_PANEL');
		}
		else
		{
			$homeLink = JText::_('COM_SH404SEF_CONTROL_PANEL');
		}
		self::_addMenuEntry($homeLink, 'index.php?option=com_sh404sef&c=default', $enabled);
		$enabled = $enabledDefault && ($view != 'urls' || $layout != 'default');
		self::_addMenuEntry(JText::_('COM_SH404SEF_URL_MANAGER'), 'index.php?option=com_sh404sef&c=urls&layout=default&view=urls', $enabled);
		$enabled = $enabledDefault && ($view != 'aliases');
		self::_addMenuEntry(JText::_('COM_SH404SEF_ALIASES_MANAGER'), 'index.php?option=com_sh404sef&c=aliases&layout=default&view=aliases', $enabled);
		$enabled = $enabledDefault && ($view != 'pageids');
		self::_addMenuEntry(JText::_('COM_SH404SEF_PAGEID_MANAGER'), 'index.php?option=com_sh404sef&c=pageids&layout=default&view=pageids', $enabled);
		$enabled = $enabledDefault && ($view != 'urls' || $layout != 'view404');
		self::_addMenuEntry(JText::_('COM_SH404SEF_404_REQ_MANAGER'), 'index.php?option=com_sh404sef&c=urls&layout=view404&view=urls', $enabled);
		$enabled = $enabledDefault && ($view != 'metas');
		self::_addMenuEntry(JText::_('COM_SH404SEF_TITLE_METAS_MANAGER'), 'index.php?option=com_sh404sef&c=metas&layout=default&view=metas', $enabled);
		$enabled = $enabledDefault && ($view != 'analytics');
		self::_addMenuEntry(JText::_('COM_SH404SEF_ANALYTICS_MANAGER'), 'index.php?option=com_sh404sef&c=analytics&layout=default&view=analytics',
			$enabled);
		$enabled = $enabledDefault && ($view != 'default' || $layout != 'info');
		self::_addMenuEntry(JText::_('COM_SH404SEF_DOCUMENTATION'), 'index.php?option=com_sh404sef&layout=info&view=default&task=info', $enabled);
	}
}
