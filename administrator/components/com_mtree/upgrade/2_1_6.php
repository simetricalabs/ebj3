<?php
/**
 * @version		$Id: 2_1_6.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright		(C) 2010 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_6 extends mUpgrade {
	function upgrade() {
		$database =& JFactory::getDBO();
		
		$database->setQuery('INSERT INTO `#__mt_config` (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`) VALUES (\'banned_attachment_filetypes\', \'main\', \'php\', \'php\', \'text\', \'12000\', \'0\')');
		$database->execute();
		
		updateVersion(2,1,6);
		$this->updated = true;
		return true;
	}
}
?>