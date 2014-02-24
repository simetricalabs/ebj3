<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This layout only insert javascript to close a modal windows
 */

if (empty($displayData->refreshAfter))
{
	$timeout = 1500;
}
else if ($displayDaya->refreshAfter == 'now')
{
	$timeout = 0;
}
else
{
	$timeout = $displayData->refreshAfter;
}

// where to send parent?
$refreshTo = empty($displayData->refreshTo) ? 'window.parent.location.href' : $displayData->refreshTo;

// modal title
$modalTitle = empty($displayData->modalTitle) ? JText::_('COM_SH404SEF_PLEASE_WAIT', true) : JText::_($displayData->modalTitle, true);

// close a modal window
if (empty($timeout))
{
	JFactory::getDocument()->addScriptDeclaration('window.parent.location.href=' . $refreshTo);
}
else
{
	JFactory::getDocument()
		->addScriptDeclaration(
			'
			shlBootstrap.setModalTitleFromModal("' . $modalTitle . '");
			setTimeout( function() {
			window.parent.location.href=window.parent.location.href;
				}, ' . $timeout . ');
		');
}
