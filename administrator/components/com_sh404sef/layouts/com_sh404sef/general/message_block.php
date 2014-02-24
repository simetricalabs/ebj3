<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This layout displays message or error, insde a bootstrap alert box
 */

if (!empty($displayData->message))
{
	echo ShlHtmlBs_Helper::alert($displayData->message, $type = 'success', $dismiss = true);
}
$error = $displayData->getError();
if (!empty($error))
{
	echo ShlHtmlBs_Helper::alert($error, $type = 'error', $dismiss = true);
}
