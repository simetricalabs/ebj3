<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2010 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class modMTLastupdateHelper {

	public static function getLastUpdate( $params ) {
		$db = JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$date_format = $params->get( 'date_format', 'j F, Y' );

		$jdate = JFactory::getDate();
		$now = $jdate->toSql();

		$db->setQuery( 'SELECT MAX(link_modified) FROM #__mt_links '
				. "\n WHERE link_published='1' && link_approved='1' "
				. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
				. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) ");
		$last_update = $db->loadResult();

		return JHtml::_('date', $last_update, $date_format);
	}
}