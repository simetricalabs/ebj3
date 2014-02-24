<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_5_2 extends mUpgrade
{
	function upgrade() {
		$database = JFactory::getDBO();

		// Removed load_js_framework_frontend config
		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` IN (\'load_js_framework_frontend\')');
		$database->execute();
		// Adds support for optional loading of Bootstrap CSS
		$database->setQuery(
			'INSERT INTO `#__mt_config` (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`, `overridable_by_category`) '
			. ' VALUES '
			. ' ( \'load_bootstrap_css\',  \'core\',  \'1\',  \'1\',  \'yesno\',  \'0\',  \'0\',  \'0\');'
			);
		$database->execute();

		updateVersion(3,5,2);
		$this->updated = true;
		return true;
	}
}
?>