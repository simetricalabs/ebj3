<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_7 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// (1) Remove allow_owner_review_own_listing config as it duplicate user_review (value 2) config.
		// (2) Remove allow_owner_rate_own_listing config as it duplicate user_rate (value 2) config.
		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` IN (\'allow_owner_review_own_listing\', \'allow_owner_rate_own_listing\');');
		$database->execute();

		// Add new config to control advanced search results sort
		$database->setQuery(
			'INSERT INTO `#__mt_config` (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`, `overridable_by_category`) '
			. ' VALUES ( \'note_simple_search\',  \'search\',  \'\',  \'\',  \'note\',  \'2100\',  \'1\',  \'1\'), '
			. ' ( \'note_advanced_search\',  \'search\',  \'\',  \'\',  \'note\',  \'2200\',  \'1\',  \'1\'), '
			. ' ( \'advanced_search_sort_by\',  \'search\',  \'\',  \'-link_featured\',  \'sort\',  \'2250\',  \'1\',  \'1\');'
			);
		$database->execute();

		// Make Notify tab visible.
		$database->setQuery('UPDATE `#__mt_configgroup` SET `displayed` =  \'1\', `overridable_by_category` =  \'1\' WHERE  `groupname` =  \'notify\'');
		$database->execute();

		updateVersion(3,0,7);
		$this->updated = true;
		return true;
	}
}
?>