<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_9 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// Remove fe_num_of_searchresults config
		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` = \'fe_num_of_searchresults\' LIMIT 1;');
		$database->execute();

		updateVersion(3,0,9);
		$this->updated = true;
		return true;
	}
}
?>
