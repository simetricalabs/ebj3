<?php
/**
 * @version		$Id: helper.php 1936 2013-07-01 13:11:28Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTStatsHelper {
	
	function getTotalLinks() {
		$db 		=& JFactory::getDBO();
		$jdate 		= JFactory::getDate();
		$now 		= $jdate->toSql();
		$nullDate	= $db->getNullDate();

		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_links '
				. "\n WHERE link_published='1' && link_approved='1' "
				. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
				. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) " );
		return $db->loadResult();
	}
	
	function getTotalCategories() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_cats WHERE cat_published=1 && cat_approved=1' );
		return $db->loadResult();
	}
	
}