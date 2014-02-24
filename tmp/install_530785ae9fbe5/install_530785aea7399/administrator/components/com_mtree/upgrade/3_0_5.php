<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_5 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Insert new sef_associated_listing_page config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES(\'sef_associated_listing_page\', \'sef\', \'apage\', \'apage\', \'text\', 1350, 1, 0)');
		$database->execute();

		updateVersion(3,0,5);
		$this->updated = true;
		return true;
	}
}
?>