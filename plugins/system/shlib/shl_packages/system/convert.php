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

class ShlSystem_Convert
{

	public static function hexToDecimal($originalHex)
	{

		if (!extension_loaded('bcmath'))
		{
			throw new ShlException(__METHOD__ . ': Using ShlSystem_Convert::hexToDecimal without BCMATH extension', 500);
		}

		$dec = hexdec(substr($originalHex, -4));
		$originalHex = substr($originalHex, 0, -4);
		$running = 1;
		while (!empty($originalHex))
		{
			$hex = hexdec(substr($originalHex, -4));
			$running = bcmul($running, 65536);
			$dec1 = bcmul($running, $hex);
			$dec = bcadd($dec1, $dec);
			$originalHex = substr($originalHex, 0, -4);
		}
		return $dec;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 * Copied over from Joomla lib, for access reasons
	 *
	 * @param array $array  The array to convert to JavaScript object notation
	 * @return  string  JavaScript object notation representation of the array
	 * @since 1.5
	 */
	public static function arrayToJSObject($array = array())
	{
		// Initialize variables
		$object = '{';

		// Iterate over array to build objects
		foreach ((array) $array as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}
			if (!is_array($v) && !is_object($v))
			{
				$object .= ' ' . $k . ': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'" . $v . "'";
				$object .= ',';
			}
			else
			{
				$object .= ' ' . $k . ': ' . self::arrayToJSObject($v) . ',';
			}
		}
		if (substr($object, -1) == ',')
		{
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}

}
