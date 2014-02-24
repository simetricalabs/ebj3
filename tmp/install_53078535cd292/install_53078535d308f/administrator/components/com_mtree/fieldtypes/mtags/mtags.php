<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_mTags extends mFieldType_tags {

	function sortTags( &$rawTags )
	{
		$sort_by	= $this->getParam('sort_by','alpha');

		switch( $sort_by )
		{
			case 'alpha':
			default:
				ksort($rawTags, SORT_STRING);
				break;

			case 'freq':
//				$rawTags = array_reverse(asort($rawTags, SORT_NUMERIC));
				asort($rawTags, SORT_NUMERIC);
				$rawTags = array_reverse($rawTags);
				break;
		}

	}

}
?>