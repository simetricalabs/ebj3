<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_8 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// Allow Number fieldtype to be taggable
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `taggable` =  \'1\' WHERE  `field_type` = \'mnumber\' LIMIT 1;');
		$database->execute();

		// Make Text field type's size editable
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `use_size` =  \'1\' WHERE  `field_type` = \'mtext\' LIMIT 1;');
		$database->execute();
		
		updateVersion(3,0,8);
		$this->updated = true;
		return true;
	}
}
?>