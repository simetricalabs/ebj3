<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_5_4 extends mUpgrade
{
	function upgrade() {
		$database = JFactory::getDBO();

		// Adds support for optional loading of Bootstrap Framework
		$database->setQuery(
			'INSERT INTO `#__mt_config` (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`, `overridable_by_category`) '
			. ' VALUES '
			. ' ( \'load_bootstrap_framework\',  \'core\',  \'1\',  \'1\',  \'yesno\',  \'0\',  \'0\',  \'0\');'
		);
		$database->execute();

		// Alter price column to use DOUBLE(12,2)
		$database->setQuery('ALTER TABLE `#__mt_links` CHANGE `price` `price` DOUBLE(12,2)  NOT NULL  DEFAULT \'0.00\';');
		$database->execute();

		updateVersion(3,5,4);
		$this->updated = true;
		return true;
	}
}
?>