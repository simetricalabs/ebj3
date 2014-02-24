<?php
/**
 * @package    Mosets Tree
 *
 * @copyright  Copyright (C) 2012-2013 Mosets Consulting. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

class MText
{
	public static function getTLCatKey( $string, $tl_cat_id )
	{
		$lang = JFactory::getLanguage();

		$key = 'COM_MTREE_TL_CAT_ID_'.$tl_cat_id.'_'.$string;
		if ($lang->hasKey($key))
		{
			return $key;
		}
		else
		{
			return 'COM_MTREE_'.$string;
		}
	}
	
	public static function _($string, $tl_cat_id=0, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return JText::_(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes, $script);
	}
	
	public static function alt($string, $alt, $tl_cat_id = 0, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return JText::alt(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes, $script);
	}
	
	public static function plural($string, $tl_cat_id=0, $n)
	{
		return JText::plural(self::getTLCatKey($string,$tl_cat_id),$n);
	}
	
	public static function sprintf($string, $tl_cat_id=0)
	{
		$args = func_get_args();

		$args = array_slice($args,2);
		array_unshift($args, self::getTLCatKey($string,$tl_cat_id));

		return call_user_func_array(array('JText','sprintf'), $args);
	}
	
	public static function printf($string, $tl_cat_id=0)
	{
		return JText::printf(self::getTLCatKey($string,$tl_cat_id));
	}
	
	public static function script($string = null, $tl_cat_id=0, $jsSafe = false, $interpretBackSlashes = true)
	{
		return JText::script(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes);
	}
}
?>