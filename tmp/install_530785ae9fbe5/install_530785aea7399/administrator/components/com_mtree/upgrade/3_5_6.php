<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2014 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_5_6 extends mUpgrade
{
	function upgrade() {
		$database = JFactory::getDBO();

		// Adds demo mode
		$database->setQuery(
			'INSERT INTO `#__mt_config` (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`, `overridable_by_category`) '
			. ' VALUES '
			. ' ( \'demo_mode\',  \'core\',  \'\',  \'0\',  \'yesno\',  \'0\',  \'0\',  \'0\');'
		);
		$database->execute();

		updateVersion(3,5,6);
		$this->updated = true;
		return true;
	}
}
?>