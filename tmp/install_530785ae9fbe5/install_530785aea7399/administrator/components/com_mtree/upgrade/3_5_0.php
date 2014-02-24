<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_5_0 extends mUpgrade {

	function upgrade() {
		$database = JFactory::getDBO();

		// Removed relative_path_to_js_library config
		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` IN (\'relative_path_to_js_library\')');
		$database->execute();

		$database->setQuery('UPDATE  `#__mt_config` SET  `configcode` =  \'text\' WHERE  `varname` =  \'linkchecker_num_of_links\'');
		$database->execute();

		updateVersion(3,5,0);
		$this->updated = true;
		return true;
	}
	
	function preflight()
	{

	}

}
?>