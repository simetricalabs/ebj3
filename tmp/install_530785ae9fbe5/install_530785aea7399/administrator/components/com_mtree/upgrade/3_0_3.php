<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_3 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// New MT 3.0 installation prior to 3.0.3, does not have 'alpha_index_additional_chars' config visible.
		$database->setQuery('UPDATE `#__mt_config` SET `displayed` = \'1\' WHERE `varname` IN(\'alpha_index_additional_chars\');');
		$database->execute();

		updateVersion(3,0,3);
		$this->updated = true;
		return true;
	}
}
?>