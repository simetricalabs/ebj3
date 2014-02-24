<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/linkchecker.mtree.html.php' );

$task2			= JFactory::getApplication()->input->getCmd( 'task2', '');
$updateurl 		= JFactory::getApplication()->input->get( 'updateurl', '');
$newurl 		= JFactory::getApplication()->input->get( 'newurl', '');
$field 			= JFactory::getApplication()->input->get( 'field', '');
$config_num_of_links	= JFactory::getApplication()->input->getInt( 'linkchecker_num_of_links', 0);
$config_seconds		= JFactory::getApplication()->input->getInt( 'linkchecker_seconds');

switch( $task2 ) {
	case 'linkchecker2':
		linkChecker2( $option, $updateurl, $field, $newurl, $config_num_of_links, $config_seconds );
		break;
	default:
		linkChecker( $option );
		break;
}

function linkChecker2( $option, $updateurl, $field, $newurl, $config_num_of_links, $config_seconds ) {

	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();

	$updated=0;
	if(count($updateurl)>0 && !empty($updateurl)){
		foreach($updateurl AS $id => $link_id) {
			if( !empty($newurl[$id]) && $newurl[$id] != 'http://' && !empty($field[$id])) {
				$database->setQuery( 'UPDATE #__mt_links SET '.$field[$id].' = ' . $database->quote($newurl[$id]) . ' WHERE link_id = ' . $database->quote($link_id) . ' LIMIT 1' );
				if($database->execute()){
					$updated++;  
				}
			}	
		}
	}
	
	if( $config_num_of_links > 0 ) {
		$database->setQuery('UPDATE #__mt_config SET value = ' . $database->quote($config_num_of_links) . ' WHERE varname =\'linkchecker_num_of_links\' LIMIT 1');
		$database->execute();
	}
	
	if( $config_seconds > 0 ) {
		$database->setQuery('UPDATE #__mt_config SET value = ' . $database->quote($config_seconds) . ' WHERE varname =\'linkchecker_seconds\' LIMIT 1');
		$database->execute();
	}
	
	$app->redirect( "index.php?option=$option&task=linkchecker", sprintf("%d links has been succesfully updated.",$updated) );

}

function linkChecker( $option ) {
	$database = JFactory::getDBO();
	
	$database->setQuery( 'SELECT link_id, link_name, website FROM #__mt_links WHERE website LIKE \'http://%\'' );
	$links = $database->loadObjectList( 'link_id' );
	
	$database->setQuery( 'SELECT l.link_id, l.link_name, cfv.value AS website FROM #__mt_cfvalues AS cfv '
		. ' LEFT JOIN #__mt_customfields AS cf ON cf.cf_id = cfv.cf_id'
		. ' LEFT JOIN #__mt_links AS l ON l.link_id = cfv.link_id'
		. ' WHERE cf.field_type = \'weblink\''
		. ' AND cfv.value LIKE \'http://%\'' );
	array_merge( $links, $database->loadObjectList( 'link_id' ) );

	$database->setQuery('TRUNCATE TABLE #__mt_linkcheck');
	$database->execute();

	foreach( $links AS $link ){
		$value = $link->website;
		if(!empty($value)){
			if( substr($value,0,7) == 'http://' ) {
				$website = substr($value,7);
			} else {
				$website = $value;
			}
			if( strpos($website,'/') !== false ) {
				$domain = substr($website,0,strpos($website,'/'));
				$path = substr($website,strpos($website,'/'));
			} else {
				$domain = $website;
				$path = '/';
			}
			$database->setQuery( 'INSERT INTO #__mt_linkcheck (`link_id`, `link_name`,`domain`,`path`) VALUES('.$database->quote($link->link_id).', '.$database->quote($link->link_name) .', ' . $database->quote($domain) . ', ' . $database->quote($path) . ')' );
			$database->execute();
		}
	}
	$database->setQuery( 'SELECT COUNT(*) FROM #__mt_linkcheck' );
	$count = $database->loadResult();
	
	$database->setQuery( 'SELECT varname, value FROM #__mt_config WHERE groupname = \'linkchecker\'' );
	$config = $database->loadObjectList('varname');
	
	HTML_mtlinkchecker::linkChecker( $option, $count, $config );
}
?>