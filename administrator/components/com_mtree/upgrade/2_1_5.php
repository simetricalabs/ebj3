<?php
/**
 * @version		$Id: 2_1_5.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2010 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_5 extends mUpgrade {
	function upgrade() {
		$database =& JFactory::getDBO();
		
		$database->setQuery('ALTER TABLE `#__mt_links` CHANGE `lat` `lat` FLOAT( 10, 6 ) NOT NULL COMMENT \'Latitude\'');
		$database->execute();
		
		$database->setQuery('ALTER TABLE `#__mt_links` CHANGE `lng` `lng` FLOAT( 10, 6 ) NOT NULL COMMENT \'Longitude\'');
		$database->execute();
		
		updateVersion(2,1,5);
		$this->updated = true;
		return true;
	}
}
?>