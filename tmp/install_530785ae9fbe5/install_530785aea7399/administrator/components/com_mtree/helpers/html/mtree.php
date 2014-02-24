<?php
/**
 * @copyright	Copyright (C) 2010 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package	Mosets Tree
 */
abstract class JHtmlMtree
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function featured($value = 0, $i, $prefix='', $canChange = true, $checkbox='cb')
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	$prefix.'featured','COM_MTREE_UNFEATURED',	'COM_MTREE_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png',	$prefix.'unfeatured','COM_MTREE_FEATURED',	'COM_MTREE_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html	= '<a href="javascript:void(0);" onclick="return listItemTask(\''.$checkbox.''.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
}