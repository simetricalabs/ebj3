<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_6 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// MT 3.0.0 update does not correctly update attachment's new path
		$database->setQuery('UPDATE `#__mt_config` SET `value` = \'/media/com_mtree/attachments/\', `default` = \'/media/com_mtree/attachments/\' WHERE `varname` = \'relative_path_to_attachments\';');
		$database->execute();

		updateVersion(3,0,6);
		$this->updated = true;
		return true;
	}
}
?>