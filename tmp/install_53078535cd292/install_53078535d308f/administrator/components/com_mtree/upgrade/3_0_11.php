<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_11 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// Insert new 'fe_num_of_searchby' config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_searchby\', \'listing\', \'\', \'100\', \'text\', 9990, 0, 1)');
		$database->query();

		updateVersion(3,0,11);
		$this->updated = true;
		return true;
	}
}
?>
