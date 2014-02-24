<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2013
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.8.369
 * @date				2013-12-21
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Utilities to load/parse xml
 *
 * @since	0.2.8
 *
 */
class ShlSystem_Xml
{
	public static function fromFile($input, $class = null)
	{
		$xml = self::_xml($input, $class, 'file');
		return $xml;
	}

	public static function fromString($input, $class = null)
	{
		$xml = self::_xml($input, $class, 'string');
		return $xml;
	}

	private static function _xml($input, $class = null, $type)
	{
		// Disable libxml errors and allow to fetch error information as needed
		$errorSetting = libxml_use_internal_errors(true);

		$xml = $type == 'file' ? simplexml_load_file($input, $class) : simplexml_load_string($file, $class);

		libxml_use_internal_errors($errorSetting);

		if ($xml === false)
		{
			foreach (libxml_get_errors() as $error)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $error);
			}
		}
		return $xml;
	}
}
