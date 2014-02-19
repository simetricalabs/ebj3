<?php
/**
 * @version		$Id: 2_1_3.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_3 extends mUpgrade {
	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Adds support for lat, lng and zoom field to RSS feed
		$database->setQuery('INSERT INTO `#__mt_config` (`varname`,`groupname`,`value`,`default`,`configcode`,`ordering`,`displayed`) VALUES (\'rss_lat\', \'rss\', \'\', \'\', \'yesno\', \'1410\', \'1\'), (\'rss_lng\', \'rss\', \'\', \'\', \'yesno\', \'1420\', \'1\'), (\'rss_zoom\', \'rss\', \'\', \'\', \'yesno\', \'1430\', \'1\')');
		$database->execute();
		
		// Set Number field type to support size
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `use_size` = \'1\' WHERE `#__mt_fieldtypes`.`field_type` = \'mnumber\' LIMIT 1 ;');
		$database->execute();
		
		$this->printStatus( 'Added new configurations.' );
		
		updateVersion(2,1,3);
		$this->updated = true;
		return true;
	}
}
?>