<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTTagCloudHelper {
	public static function getTags($cf_id) {
		$db = JFactory::getDBO();
		
		if ( modMTTagCloudHelper::isCore($cf_id) )
		{
			$db->setQuery('SELECT field_type FROM #__mt_customfields WHERE cf_id = ' . $db->Quote($cf_id) . ' LIMIT 1');
			$field_type = $db->loadResult();
			$core_name = substr($field_type,4);
			$db->setQuery('SELECT ' . $core_name . ' FROM #__mt_links WHERE ' . $core_name . ' != \'\'');
		} else {
			$db->setQuery('SELECT REPLACE(value,\'|\',\',\') FROM #__mt_cfvalues WHERE cf_id = ' . $db->Quote($cf_id));
		}
		$tags = $db->loadColumn();
		
		return $tags;
	}

	public static function sortTags( $arrTags, $sort_by )
	{
		switch( $sort_by )
		{
			case 'alpha':
			default:
				ksort($arrTags, SORT_STRING);
				break;

			case 'freq':
				asort($arrTags, SORT_NUMERIC);
				$arrTags = array_reverse($arrTags);
				break;
		}
		return $arrTags;
	}

	/**
	 * Read through array of strings and return an array mapping tag with number of occurances
	 *
	 * @access	public
	 * @param	array	$arrTags	Array of strings, where each strings are comma separated values
	 * @return	array	An array of results mapping keywords to the number of occurances
	 * @since	1.0
	 */
	public static function parse($arrTags)
	{
		$rawRank = array();
		foreach( $arrTags AS $tag )
		{
			$rawRank = array_merge($rawRank,modMTTagCloudHelper::explodeTrim($tag));
		}
		$rawRank = array_count_values($rawRank);
		arsort($rawRank);
		
		return $rawRank;
	}
	
	/**
	 * Method to explode a string by comma and then trim each exploded tags.
	 *
	 * @access	public
	 * @param	string	$str	A string consisting comma separated values
	 * @return	array	An array of results
	 * @since	2.1
	 */
	public static function explodeTrim($str)
	{
		if( empty($str) ) return array();
		
		$results = explode(',',$str);
		$count = count($results);
		
		for($i=0;$i<$count;$i++)
		{
			$results[$i] = trim($results[$i]);
		}
		
		return array_unique($results);
	}

	public static function isCore($cf_id) {
		$db = JFactory::getDBO();
		
		$db->setQuery('SELECT iscore FROM #__mt_customfields WHERE cf_id = ' . $db->Quote($cf_id) . ' LIMIT 1');
		return $db->loadResult();
	}
}
?>