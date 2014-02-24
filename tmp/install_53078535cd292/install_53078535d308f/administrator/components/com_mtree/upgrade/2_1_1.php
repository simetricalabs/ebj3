<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_1 extends mUpgrade {
	function upgrade() {
		global $mtconf;
		
		$database =& JFactory::getDBO();
		
		// Insert a hidden config to configure days to expire
		$database->setQuery('INSERT INTO `#__mt_config` (`varname`,`groupname`,`value`,`default`,`configcode`,`ordering`,`displayed`) VALUES (\'days_to_expire\', \'listing\', \'0\', \'0\', \'text\', \'6800\', \'0\')');
		$database->execute();
		
		// Insert hidden configs to configure the number of rss items
		$database->setQuery('INSERT INTO `#__mt_config` (`varname`,`groupname`,`value`,`default`,`configcode`,`ordering`,`displayed`) VALUES (\'rss_new_limit\', \'rss\', \'40\', \'40\', \'text\', \'220\', \'0\'), (\'rss_updated_limit\', \'rss\', \'40\', \'40\', \'text\', \'240\', \'0\')');
		$database->execute();

		// Insert hidden configs to make max & min search characters configurable
		$database->setQuery('INSERT INTO `#__mt_config` (`varname`,`groupname`,`value`,`default`,`configcode`,`ordering`,`displayed`) VALUES (\'limit_max_chars\', \'search\', \'20\', \'20\', \'text\', \'2160\', \'0\'), (\'limit_min_chars\', \'search\', \'3\', \'3\', \'text\', \'2170\', \'0\')');
		$database->execute();
		
		$this->printStatus( 'Added new configurations.' );
		
		// Rename htaccess.txt to .htaccess in attachments directory
		jimport('joomla.filesystem.file');
	    if(!JFile::move(JPATH_SITE.$mtconf->get('relative_path_to_attachments').'htaccess.txt', JPATH_SITE.$mtconf->get('relative_path_to_attachments').'.htaccess' )) {
			$this->printStatus( 'Failed to rename '.$mtconf->get('relative_path_to_attachments').'htaccess.txt. To fix this, rename htacess.txt to <strong>.htaccess</strong> at this location '.JPATH_SITE.$mtconf->get('relative_path_to_attachments').'htaccess.txt. This is an important steps to protect your attachment from being downloaded by unauthorized users.', -1 );
		}
		
		updateVersion(2,1,1);
		$this->updated = true;
		return true;
	}
}
?>