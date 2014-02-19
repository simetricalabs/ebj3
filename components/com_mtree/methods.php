<?php
/**
 * @package    Mosets Tree
 *
 * @copyright  Copyright (C) 2012 Mosets Consulting. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

class MText extends JText
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
		return parent::_(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes, $script);
	}
	
	public static function alt($string, $alt, $tl_cat_id = 0, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return parent::alt(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes, $script);
	}
	
	public static function plural($string, $tl_cat_id=0, $n)
	{
		return parent::plural(self::getTLCatKey($string,$tl_cat_id),$n);
	}
	
	public static function sprintf($string, $tl_cat_id=0)
	{
		$args = func_get_args();

		$args = array_slice($args,2);
		array_unshift($args, self::getTLCatKey($string,$tl_cat_id));

		return call_user_func_array(array('MText','parent::sprintf'), $args);
	}
	
	public static function printf($string, $tl_cat_id=0)
	{
		return parent::printf(self::getTLCatKey($string,$tl_cat_id));
	}
	
	public static function script($string = null, $tl_cat_id=0, $jsSafe = false, $interpretBackSlashes = true)
	{
		return parent::script(self::getTLCatKey($string,$tl_cat_id), $jsSafe, $interpretBackSlashes);
	}
}
?>