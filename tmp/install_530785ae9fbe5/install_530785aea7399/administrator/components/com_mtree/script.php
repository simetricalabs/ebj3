<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2010 - 2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

// defined('_JEXEC') or die('Restricted access');

include_once( 'administrator/components/com_mtree/config.mtree.class.php' );

class Com_MtreeInstallerScript {

	function preflight( $type, $parent )
	{
		if( !$this->hasMinimumJoomlaVersion ($parent) )
		{
			Jerror::raiseWarning(null, 'This version of Mosets Tree requires Joomla! '.$this->getMinimumJoomlaVersion($parent).' or newer to run.');
			return false;
		}

		require_once( dirname(__FILE__).'/administrator/components/com_mtree/upgrade.php' );
		runScriptPreflight();
	}

	function hasMinimumJoomlaVersion( $parent )
	{
		$jversion = new JVersion();

		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;

		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			return false;
		} else {
			return true;
		}
	}

	function getMinimumJoomlaVersion( $parent )
	{
		if( !isset($this->minimum_joomla_release) )
		{
			$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;
		}

		return $this->minimum_joomla_release;
	}

	function update($parent)
	{
		require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade.php' );
		runScriptUpgrade();
	}
	
	function install($parent)
	{
		$my	= JFactory::getUser();
		$database = JFactory::getDBO();
		$mtconf = mtFactory::getConfig();
	
		// Assign current user's email as Mosets Tree admin
		$database->setQuery("UPDATE #__mt_config SET value='" . $my->email . "' WHERE varname='admin_email' LIMIT 1");
		$database->execute();

		// Rename htaccess.txt to .htaccess in attachments directory
		jimport('joomla.filesystem.file');
		if(
			!JFile::move(
				JPATH_SITE.$mtconf->get('relative_path_to_attachments').'htaccess.txt',
				JPATH_SITE.$mtconf->get('relative_path_to_attachments').'.htaccess'
			)
		) {
			$htaccess_rename_status = false;
		} else {
			$htaccess_rename_status = true;
		}
	
		// Check if this is a new installation by checking the number of available categories
		// If this is a new installation, populate #__mt_cats with sample categories.
		$database->setQuery("SELECT COUNT(*) FROM #__mt_cats");
		$num_of_cats = $database->loadResult();
	
		if( !is_null($num_of_cats) && $num_of_cats == 0 )
		{
			$isNew = true;
			$this->loadDefaultCategories();
			$this->loadSampleData(JPATH_ADMINISTRATOR.'/components/com_mtree/sample-mt-data.sql');

			// Run populate_fields_map() from 3_0_0 to populate #__mt_fields_map table.
			require_once(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade.class.php');
			require_once(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/3_0_0.php');

			$upgrade = new mUpgrade_3_0_0;
			$upgrade->populate_fields_map();
			
		} else {
			$isNew = false;
		}
		?>
		<div>
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m" style="overflow:hidden;padding-bottom:12px;">
				<div style="padding: 20px;border-right:1px solid #ccc;float:left">
				<img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>logo.png" alt="Mosets Tree" style="float:left;padding-right:15px;" />
				</div>
				<div style="margin-left:350px;">
					<h2 style="margin-bottom:0;">Mosets Tree <?php 
					if( $isNew ) { 
						echo $mtconf->get('version'); 
					}
					?></h2>
					<strong>A flexible directory component for Joomla!</strong>
					<br /><br />
					&copy; Copyright 2005-<?php echo date('Y'); ?> by Mosets Consulting. <a href="http://www.mosets.com/">www.mosets.com</a><br />
					<?php if( $isNew ) { ?>
					<input type="button" value="Go to Mosets Tree now" onclick="location.href='index.php?option=com_mtree'" style="margin-top:13px;cursor:pointer;width:200px;font-weight:bold" />
					<?php } else{ ?>
					<input type="button" value="Click here to complete the upgrade" onclick="location.href='index.php?option=com_mtree&amp;task=upgrade'" style="margin-top:13px;cursor:pointer;width:350px;font-weight:bold" />
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
		return true;
	}

	/**
	 * Load a set of sample data.
	 */
	function loadSampleData( $sqlfile )
	{
		if( empty($sqlfile) || !JFile::exists($sqlfile) )
		{
			return false;
		}
		
		$buffer = file_get_contents($sqlfile);
		
		if( empty($buffer) )
		{
			return false;
		}
		
		$db = JFactory::getDbo();

		$arrSql = self::_splitQueries($buffer);

		foreach( $arrSql AS $sql )
		{
			$sql = trim($sql);

			if( $sql != '' && $sql{0} != '#' )
			{
				$db->setQuery( $sql );
				$db->execute();
			}
		}
	}
	
	/**
	 * Load a standard set of categories so that a default install does not seem empty.
	 */
	function loadDefaultCategories()
	{
		$database = JFactory::getDBO();
	
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (1, 'Arts', 'arts', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 10, 2, 9);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (2, 'Computers', 'computers', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 9, 10, 17);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (3, 'Health', 'health', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 8, 18, 25);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (4, 'Recreation', 'recreation', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 7, 26, 33);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (5, 'Science', 'science', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 6, 34, 41);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (6, 'Business', 'business', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 5, 42, 49);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (7, 'Games', 'games', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 4, 50, 57);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (8, 'Movies', 'movies', '', '', 1, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 3, 4);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (9, 'Reference', 'reference', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 58, 65);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (10, 'Shopping', 'shopping', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 66, 73);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (11, 'Sports', 'sports', '', '', 0, 0, 3, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 74, 81);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (12, 'Television', 'television', '', '', 1, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 5, 6);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (13, 'Music', 'music', '', '', 1, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 7, 8);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (14, 'Companies', 'companies', '', '', 6, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 43, 44);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (15, 'Finance', 'finance', '', '', 6, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 45, 46);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (16, 'Employment', 'employment', '', '', 6, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 47, 48);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (17, 'Internet', 'internet', '', '', 2, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 11, 12);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (18, 'Programming', 'programming', '', '', 2, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 13, 14);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (19, 'Software', 'software', '', '', 2, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 15, 16);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (20, 'Gambling', 'gambling', '', '', 7, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 51, 52);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (21, 'Roleplaying', 'roleplaying', '', '', 7, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 53, 54);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (22, 'Console', 'console', '', '', 7, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 55, 56);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (23, 'Fitness', 'fitness', '', '', 3, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 19, 20);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (24, 'Medicine', 'medicine', '', '', 3, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 21, 22);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (25, 'Alternative', 'alternative', '', '', 3, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 23, 24);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (74, 'Food', 'food', '', '', 4, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 27, 28);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (56, 'Outdoor', 'outdoor', '', '', 4, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 29, 30);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (55, 'Travel', 'travel', '', '', 4, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 31, 32);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (26, 'Education', 'education', '', '', 9, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 59, 60);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (66, 'Libraries', 'libraries', '', '', 9, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 61, 62);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (31, 'Maps', 'maps', '', '', 9, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 63, 64);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (32, 'Biology', 'biology', '', '', 5, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 35, 36);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (33, 'Psychology', 'psychology', '', '', 5, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 37, 38);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (34, 'Physics', 'physics', '', '', 5, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 39, 40);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (35, 'Autos', 'autos', '', '', 10, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 67, 68);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (36, 'Clothing', 'clothing', '', '', 10, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 69, 70);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (37, 'Gifts', 'gifts', '', '', 10, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 71, 72);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (38, 'Basketball', 'basketball', '', '', 11, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 3, 75, 76);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (39, 'Football', 'football', '', '', 11, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 2, 77, 78);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (40, 'Golf', 'golf', '', '', 11, 0, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 1, 1, '', '', '', 1, 79, 80);");
		$database->execute();
		$database->setQuery("INSERT IGNORE INTO `#__mt_cats` VALUES (0, 'Root', 'root', '', '', -1, 1, 0, 0, '', 0, 1, '2007-06-01 00:00:00', 1, '', 0, 0, 1, '', '', '', 0, 1, 82);");
		$database->execute();

		$database->setQuery("UPDATE IGNORE `#__mt_cats` SET `cat_id` = '0' WHERE `cat_parent` =-1 LIMIT 1;");	
		$database->execute();
		
		// $this->loadSampleDataScript('sample-30beta.sql');
	}
	
	function loadSampleDataScript($sqlfile='sample.sql')
	{
		$db = JFactory::getDBO();
		
		$sqlfile_path = JPATH_ADMINISTRATOR.'/components/com_mtree/'.$sqlfile;
		
		if (!file_exists($sqlfile_path))
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_FILENOTFOUND', $sqlfile_path));

			return false;
		}

		$buffer = file_get_contents($sqlfile_path);
		$queries = $db->splitSQL($buffer);

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query{0} != '#' && $query{0} != '-')
			{
				$db->setQuery($query);
				
				if (!$db->execute())
				{
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
				
					return false;
				}
			}
		}
		
		return true;
	}

	function getHtaccessRenameRow($htaccess_rename_status)
	{
		$msg = '';
		if(!$htaccess_rename_status)
		{
			global $mtconf;
		
			$msg .= '<tr>';
			$msg .= '<td>';
			$msg .= 'Mosets Tree was unable to rename htaccess.txt to .htaccess at '.JPATH_SITE.$mtconf->get('relative_path_to_attachments').'. Please manually rename this file before using Mosets Tree.';
			$msg .= '</td>';
			$msg .= '<td>';
			$msg .= '<b><font color="red">Warning</font></b>';
			$msg .= '</td>';
			$msg .= '</tr>';
		}
		return $msg;
	}

	function getWritableRow($dir)
	{
		$msg = '<tr>';
		$msg .= '<td>';
		$msg .= $dir;
		$msg .= '</td>';
		$msg .= '<td>';
		$msg .= (is_writable( $dir ) ? '<b><font color="green">Writeable</font></b>' : '<b><font color="red">Unwriteable</font></b>');
		$msg .= '</td>';
		$msg .= '</tr>';
		return $msg;
	}
	
	/**
	 * Method to split up queries from a schema file into an array.
	 *
	 * @param	string	$sql SQL schema.
	 *
	 * @return	array	Queries to perform.
	 * @access	protected
	 */
	function _splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			if ($sql[$i] == ";" && !$in_string) {
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
			$queries[] = $sql;
		}

		return $queries;
	}
	
	function uninstall($parent)
	{
		$date = JFactory::getDate();
		
		$msg = '<table width="100%" border="0" cellpadding="8" cellspacing="0"><tr>';
		$msg .= '<td width="100%" align="left" valign="top"><center><h3>Mosets Tree</h3><h4>A powerful directory component for Joomla!</h4><font class="small">&copy; Copyright 2004-'.$date->format('Y').' by Lee Cher Yeong. <a href="http://www.mosets.com/">http://www.mosets.com/</a><br/></font></center><br />';

		$msg .= "<fieldset style=\"border: 1px dashed #C0C0C0;\"><legend>Details</legend>";

		$msg .= "<font color=#339900>OK</font> &nbsp; Mosets Tree Uninstalled Successfully</fieldset>";
		$msg .='<br /><br /></td></tr></table>';

		echo $msg;
	}
}
?>