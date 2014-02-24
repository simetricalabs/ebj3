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

defined('JPATH_PLATFORM') or die;

$fileName = JPATH_ADMINISTRATOR . '/components/com_sh404sef/pagination_' . Sh404sefHelperGeneral::getJoomlaVersionPrefix() . '.php';

if(JFile::exists($fileName))
{
	include_once $fileName;
}