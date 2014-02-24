<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

if( !function_exists('runScriptPreflight') )
{
	require_once( 'upgrade.class.php' );

	function runScriptPreflight()
	{
		$version = getMtVersionFromConfig();
		
		if( !is_null($version) )
		{
			preflight(
				$version['major_version']->value,
				$version['minor_version']->value,
				$version['dev_version']->value
				);
		}
		else
		{
			return false;
		}
	}

	function preflight($major,$minor,$dev)
	{
		$nextUpgradeVersion = getNextUpgradeVersion($major,$minor,$dev);
		$currentUpgradeVersion = array($major,$minor,$dev);

		if( $nextUpgradeVersion !== false )
		{
			$current_dir = dirname(__FILE__);
			
			require($current_dir.'/upgrade/'.implode('_',$nextUpgradeVersion) . '.php');
			$className = 'mUpgrade_' . implode('_',$nextUpgradeVersion);
			$upgrade = new $className;
			return $upgrade->preflight();

			return true;
		}
		else
		{
			return false;
		}
	}

	function runScriptUpgrade()
	{
		$database = JFactory::getDBO();

		$database->setQuery('SELECT value FROM #__mt_config WHERE varname =\'version\' LIMIT 1');
		$version = $database->loadResult();

		// If current version is 2.00
		if(in_array($version,array('2.00','-1','2.0.0',''))) {
			upgrade(2,0,0);
		}

		$version = getMtVersionFromConfig();

		// A fix for 2.0.1/3/4. Appears that 2.0.1/3/4's mtree.xml file does not update dev_version to 1/3/4
		if(in_array($version['version']->value,array('2.0.1','2.0.3','2.0.4'))) {
			$version['major_version']->value = 2;
			$version['minor_version']->value = 0;
			$version['dev_version']->value = substr($version['version']->value,-1,1);
		}

		JToolBarHelper::title( JText::_('Mosets Tree Upgrader') );
		printStartTable();

		if( upgrade($version['major_version']->value,$version['minor_version']->value,$version['dev_version']->value) === false) {
			// printRow('You\'re currently at version ' . $version['major_version']->value . '.' . $version['minor_version']->value . '.' . $version['dev_version']->value);
			// printRow('No more upgrades needed.',2);
		} else {
			printRow('Upgrades Completed!',2);
			printRow('<a href="index.php?option=com_mtree">&lt; Back to Mosets Tree</a>',2);
		}

		printEndTable();	
	}

	function upgrade($major,$minor,$dev) {
		$updated = false;
		$nextUpgradeVersion = getNextUpgradeVersion($major,$minor,$dev);
		$currentUpgradeVersion = array($major,$minor,$dev);
		while($nextUpgradeVersion !== false) {
			printStartTable('Upgrade: Mosets Tree ' . $currentUpgradeVersion[0] . '.' . $currentUpgradeVersion[1] . '.' . $currentUpgradeVersion[2] . ' - ' . implode('.',$nextUpgradeVersion));

			$className = 'mUpgrade_' . implode('_',$nextUpgradeVersion);
			if( !class_exists($className) )
			{
				require(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/'.implode('_',$nextUpgradeVersion) . '.php');
			}

			$upgrade = new $className;
			$upgrade->upgrade();

			if($upgrade->updated() === true) {
				printRow('Successfully upgraded to <b>Mosets Tree ' . implode('.',$nextUpgradeVersion) .'</b>.');
			} elseif( $upgrade->updated() === false ) {
				$document= JFactory::getDocument();
				$document->addCustomTag('<meta http-equiv="Refresh" content="1; URL='.$upgrade->continue_url.'">');
				if( isset($upgrade->continue_message) ) {
					printRow($upgrade->continue_message);
					printRow('<a href="'.$upgrade->continue_url.'">Click here to continue if page does not reload.</a>');
				} else {
					printRow('processing upgrade...');
				}
				return false;
			} else {
				printRow('No update required for <b>Mosets Tree ' . implode('.',$nextUpgradeVersion) .'</b>.');
			}
			if(!$updated) $updated = $upgrade->updated();
			$currentUpgradeVersion = array($nextUpgradeVersion[0],$nextUpgradeVersion[1],$nextUpgradeVersion[2]);
			$nextUpgradeVersion = getNextUpgradeVersion($nextUpgradeVersion[0],$nextUpgradeVersion[1],$nextUpgradeVersion[2]);
			printEndTable();
		}

		return $updated;

	}

	function getNextUpgradeVersion($major,$minor,$dev) {
		
		$current_dir = dirname(__FILE__);

		// Look if there is a next $dev version
		if(
			file_exists(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/'.$major . '_' . $minor . '_' .($dev +1) . '.php')
			||
			file_exists($current_dir.'/upgrade/'.$major . '_' . $minor . '_' .($dev +1) . '.php')
		) {
			return array($major,$minor,($dev +1));
		// Look if there is a next $minor version
		} elseif(
			file_exists(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/'.$major . '_' . ($minor +1) . '_0.php')
			||
			file_exists($current_dir.'/upgrade/'.$major . '_' . ($minor +1) . '_0.php')
		) {
			return array($major,($minor +1),0);
		// Look if there is a next x.5 minor version
		} elseif(
			($minor < 5)
			&&
			(
				file_exists(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/'.$major . '_' . (5) . '_0.php')
				||
				file_exists($current_dir.'/upgrade/'.$major . '_' . (5) . '_0.php')
			)
		) {
			return array($major,(5),0);
		// Look if there is a next $major version
		} elseif(
			file_exists(JPATH_ADMINISTRATOR.'/components/com_mtree/upgrade/'.($major +1) . '_0_0.php')
			||
			file_exists($current_dir.'/upgrade/'.($major +1) . '_0_0.php')
		) {
			return array(($major +1),0,0);
		} else {
			return false;
		}
		return true;
	}

	function updateVersion($major,$minor,$dev) {
		$database = JFactory::getDBO();

		$database->setQuery('SELECT value FROM #__mt_config WHERE varname = \'major_version\' LIMIT 1');
		if($database->loadResult() == '') {
			addRows('config',array(array('major_version', 'core', $major, '', 'text', 0, 0)));
		} else {
			$database->setQuery('UPDATE #__mt_config SET value = \'' . $major . '\' WHERE varname = \'major_version\' LIMIT 1');
			$database->execute();
		}

		$database->setQuery('SELECT value FROM #__mt_config WHERE varname = \'minor_version\' LIMIT 1');
		if($database->loadResult() == '') {
			addRows('config',array(array('minor_version', 'core', $minor, '', 'text', 0, 0)));
		} else {
			$database->setQuery('UPDATE #__mt_config SET value = \'' . $minor . '\' WHERE varname = \'minor_version\' LIMIT 1');
			$database->execute();
		}

		$database->setQuery('SELECT value FROM #__mt_config WHERE varname = \'dev_version\' LIMIT 1');
		if($database->loadResult() == '') {
			addRows('config',array(array('dev_version', 'core', $dev, '', 'text', 0, 0)));
		} else {
			$database->setQuery('UPDATE #__mt_config SET value = \'' . $dev . '\' WHERE varname = \'dev_version\' LIMIT 1');
			$database->execute();
		}

		$database->setQuery('UPDATE #__mt_config SET value = \'' . $major . '.' . $minor . '.' . $dev . '\' WHERE varname = \'version\' LIMIT 1');
		$database->execute();
	}

	function addRows($table, $rows) {
		$database = JFactory::getDBO();
		$db_prefix = JFactory::getApplication()->getCfg('dbprefix');

		if(!is_array($rows) || empty($rows) || !isset($rows[0])) {
			return false;
		} else {
			$sql = 'INSERT INTO `#__mt_' . $table . '` VALUES ';
			$value = array();
			if(is_array($rows[0])) {
				foreach($rows AS $row) {
					// echo '<br />Table: '. $table . ' row:' . $row; var_dump($row);
					$values[] = "('" . implode("','",$row) . "')";
				}
			} else {
				$values[] = '(\'' . implode('\',\'',$rows) . '\')';
			}
			$sql .= implode(', ',$values);
			$database->setQuery( $sql );
			if ( $database->execute() ) {
				if(is_array($rows[0])) {
					$affected_rows = count($rows);
				} else {
					$affected_rows = 1;
				}
				printRow($affected_rows . ' rows added to table: ' . $db_prefix . 'mt_' . $table);
				return true;
			} else {
				printRow('Error adding rows to table: ' . $db_prefix . 'mt_' . $table . '. Error Message: ' . $database->getErrorMsg(), 0);
				// echo '<pre align="left">' . $database->getQuery() . '</pre>';
				return false;
			}
		}
	}

	function createTable($table, $create_definitions, $drop_table_if_exists=false, $engine='MyISAM') {

		$database = JFactory::getDBO();
		$db_prefix = JFactory::getApplication()->getCfg('dbprefix');

		$safe_to_create = false;

		$database->setQuery( "SHOW TABLE STATUS LIKE '" . $db_prefix . "mt_" . $table . "'" );
		$database->execute();
		if($database->getNumRows() == 1) {
			$table_exists = true;
		} else {
			$table_exists = false;
		}
		if($drop_table_if_exists && $table_exists) {
			$database->setQuery( "DROP TABLE `" . $db_prefix . "mt_" . $table . "`" );
			$database->execute();
			$safe_to_create = true;
		} elseif(!$table_exists) {
			$safe_to_create = true;
		}
		if($safe_to_create && count($create_definitions) > 0) {
			$sql = 'CREATE TABLE `#__mt_' . $table . '` (';
			$sql .= implode(',',$create_definitions);
			$sql .= ')';
			if(!empty($engine)) {
				$sql .= ' ENGINE=' . $engine . ';';
			}
			$database->setQuery( $sql );
			if ( $database->execute() ) {
				printRow('Created table: ' . $db_prefix . 'mt_' . $table);
				return true;
			} else {
				printRow( $database->getErrorMsg(), -1);
				return false;
			}
			// echo '<pre align="left">' . $database->getQuery() . '</pre>';
		} else {
			printRow('table: ' . $db_prefix . 'mt_' . $table . ' already exists.', 0);
			return false;
		}
		return false;
	}
	function changeColumnType($table, $column_name, $new_column_data_type, $new_column_definition) {
		$database = JFactory::getDBO();

		$database->setQuery( 'DESCRIBE #__mt_' . $table . ' ' . $column_name );
		$tmp = $database->loadObject();
		if( strtolower($tmp->Type) <> strtolower($new_column_data_type) ) {
			$database->setQuery( "ALTER TABLE #__mt_" . $table . " CHANGE `" . $column_name . "` `" . $column_name . "` " . strtoupper($new_column_data_type) . " " . $new_column_definition );
			if ( $database->execute() ) {
				printRow('Updated column:' . $column_name . ' to ' . strtoupper($new_column_data_type) . ' type.');
				return true;
			} else {
				printRow( $database->getErrorMsg(), -1);
				return false;
			}
		} else {
			printRow('Skipped column modification:' . $column_name . ' appears to be using the new column type and column definition.', 0);
			return false;
		}
	}

	function addIndex($table, $index_name, $fields) {
		$database = JFactory::getDBO();
		$db_prefix = JFactory::getApplication()->getCfg('dbprefix');

		$database->setQuery( 'SHOW INDEX FROM #__mt_' . $table . ' WHERE Key_name = "' . $index_name . '" ' );
		$tmp = $database->loadObjectList();
		if( count($tmp) == 0 && count($fields) > 0 ) {
			$database->setQuery( 'ALTER TABLE #__mt_' . $table . ' ADD INDEX `' . $index_name . '` ( `' . implode('` , `',$fields) . '` )' );
			if ( $database->execute() ) {
				printRow('Added index:' . $index_name . ' to table: ' . $db_prefix . 'mt_' . $table );
				return true;
			} else {
				printRow($database->getErrorMsg(). -1);
				return false;
			}
		} else {
			printRow('Skipped index insertion:' . $index_name . ' already exists.', 0 );
			return false;
		}
	}

	function getMtVersionFromConfig()
	{
		$database = JFactory::getDBO();

		try
		{
			$database->setQuery('SELECT value, varname FROM #__mt_config WHERE varname LIKE \'%version\' AND groupname = \'core\' LIMIT 4');
			$version = $database->loadObjectList('varname');	
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		return $version;
	}

	function addColumn($table, $column_name, $column_info='', $after='') {
		$database = JFactory::getDBO();
		$db_prefix = JFactory::getApplication()->getCfg('dbprefix');;

		$database->setQuery( 'SHOW COLUMNS FROM #__mt_' . $table . ' LIKE "' . $column_name . '"' );
		$tmp = $database->loadResult();
		if ( $tmp == $column_name ) {
			printRow('Skipped column:' . $column_name . ' already exists.', 0 );
			return false;
		} else {
			$sql = 'ALTER TABLE #__mt_' . $table . ' ADD `' . $column_name . '` ' . $column_info;
			if(!empty($after)) {
				$sql .= ' AFTER `' . $after .'`';
			}
			$database->setQuery( $sql );
			if( $database->execute() ) {
				printRow('Added column:' . $column_name . ' to table: ' . $db_prefix . 'mt_' . $table );
				return true;
			} else {
				printRow($database->getErrorMsg(). -1);
				return false;
			}
		}
	}
	function printRow( $msg, $status=1 ) {
		if( $status == 1 OR $status == 0 ) {
			echo '<tr><td><b>'.(($status)?'<span style="color:green">OK</span>':'Skipped').'</b> - '.$msg.'</td></tr>';
		} elseif( $status == 2 ) {
			echo '<tr><td><strong>'.$msg.'</strong></td></tr>';
		}
	}

	function printStartTable($header='') {
		echo '<table class="adminform">';
		if(!empty($header)) {
			echo '<tr><th>' . $header . '</th></tr>';
		}
	}

	function printEndTable() {
		echo '</table>';	
	}	
}
?>