<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');


$cat_id	= JFactory::getApplication()->input->getInt('cat_id', 0);
$task2	= JFactory::getApplication()->input->getCmd('task2', '');

switch($task2){
	case 'spiderurl':
		spiderurl($option);
		break;
	case 'checklinkcomplete':
		checklinkcomplete();
		break;
		
	case 'checklink':
		checklink();
		break;
		
	case 'categories.list':
		echo categoriesList($cat_id);
		break;
}

function categoriesList( $cat_id ) {
	global $mtconf;
	
	$database = JFactory::getDBO();

	# Get pathway
	$mtPathWay = new mtPathWay($cat_id);

	$pathway = $mtPathWay->printPathWayFromCat_withCurrentCat($cat_id,0);
	$return[] = (object) array(
		'type'		=> 'pathway',
		'cat_id'	=> $cat_id,
		'cat_name'	=> $mtPathWay->getCatName(),
		'text'		=> $pathway
		);
	
	$sql = 'SELECT cat_id, cat_name FROM #__mt_cats AS cat WHERE cat_parent = ' . $database->quote($cat_id) . ' && cat_published = 1 && cat_approved = 1 ';
	if( $mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
		if( $mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
		}
	}
	$database->setQuery( $sql );
	$cats = $database->loadObjectList();

	if($cat_id > 0) {
		$database->setQuery( 'SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' && cat_published = 1 && cat_approved = 1 LIMIT 1');
		$browse_cat_parent = $database->loadResult();

		$database->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($browse_cat_parent) . ' && cat_published = 1 && cat_approved = 1 LIMIT 1');
		$browse_cat_parent_object = $database->loadObject();
		
		$return[] = (object) array(
			'type'		=> 'back',
			'cat_id'	=> $browse_cat_parent_object->cat_id,
			'cat_name'	=> $browse_cat_parent_object->cat_name,
			'text'		=> JText::_( 'COM_MTREE_ARROW_BACK' )
			);
	}
	
	if(!empty($cats)) {
		foreach( $cats as $key => $cat )
		{
			$return[] = (object) array(
				'type'		=> 'category',
				'cat_id'	=> $cat->cat_id,
				'cat_name'	=> $cat->cat_name,
				'text'		=> $cat->cat_name
			);
		}
	}

	return json_encode($return);
}

function spiderurl( $option ) {
	global $database, $mtconf;

	$url	= JFactory::getApplication()->input->get( 'url', '');
	$start	= JFactory::getApplication()->input->getInt( 'start', 0);
	$return = (object) array(
		'status'	=> '',
		'message'	=> '',
		'metakey'	=> '',
		'metadesc'	=> ''
		);
	$error = 0;

	if ( !empty($url) && substr($url, 0, 7) <> "http://" ) {
		$url = "http://".$url;
	}

	if ( empty($url) || $start) {
		$return->status = 'NOTFOUND';
		$return->message = JText::_( 'COM_MTREE_UNABLE_TO_GET_METATAGS' );
	} else {
		if(ini_get('allow_url_fopen')) {
			$metatags = get_meta_tags( $url ) or $error = 1;
		} else {
			$error = 1;
			$return->status = 'NOTFOUND';
		}
		if ( !$error ) {
			$return->status = 'OK';
			if ( !empty($metatags['keywords']) ) {
				$return->metakey = $metatags['keywords'];
			}
			if ( !empty($metatags['description']) ) {
				$return->metadesc = $metatags['description'];
			}
			$return->message = "<img src=\"..".$mtconf->get('relative_path_to_images')."accept.png\" style=\"position:relative;top:3px\" /> " . JText::_( 'COM_MTREE_SPIDER_HAS_BEEN_UPDATED' );
		} else {
			$return->status = 'NOTFOUND';
			$return->message = "<img src=\"..".$mtconf->get('relative_path_to_images')."exclamation.png\" style=\"position:relative;top:3px\" /> " . JText::_( 'COM_MTREE_UNABLE_TO_GET_METATAGS' );
		}
	}
	echo json_encode($return);
}

function checklink(){
	global $database;
	
	$database->setQuery( 'SELECT id, link_id, field, link_name, domain, path FROM #__mt_linkcheck WHERE check_status = \'0\' LIMIT 1');
	$link = $database->loadObject();

	$database->setQuery( 'UPDATE #__mt_linkcheck SET check_status=1 WHERE id = ' . $database->quote($link->id) . ' LIMIT 1' );
	$database->execute();

	if( count($link) == 1 ) {
		$output = $link->id;
		$output .= '|'.$link->link_id;
		$output .= '|'.$link->field;
		$output .= '|'.$link->link_name;
		$output .= '|'.$link->domain;
		$output .= '|'.$link->path;

		$fp = @fsockopen($link->domain, 80, $errno, $errstr, 5);
		if (!$fp) {
		  // $output .= "Server unreachable: $errstr ($errno)";
		 	$output .= "HTTP/1.1 Unable to connect to the server";
			$database->setQuery( 'UPDATE #__mt_linkcheck SET check_status= \'-1\' WHERE id = ' . $database->quote($link->id) . ' LIMIT 1' );
			$database->execute();
		
		} else {
			$request = "HEAD ".$link->path." HTTP/1.1\r\n";
			$request .= "Host: ".$link->domain."\r\n";
			$request .= "Connection: close\r\n";
			$request .= "Accept-Encoding: gzip\r\n";
			$request .= "Accept-Charset: iso-8859-1, utf-8, utf-16, *;q=0.1\r\n";
			$request .= "\r\n";
			fwrite($fp, $request);
			
			$response = fgets($fp, 256);
			$output .= '|'.trim($response);
			$output .= '|'.trim($response);
			while (!feof($fp)) {
				 $output .= "\n".trim(fgets($fp, 256));
			}
			$response = explode(' ',$response);
			
			//}
			fclose($fp);
			$database->setQuery( 'UPDATE #__mt_linkcheck SET check_status=2, status_code=' . $database->quote($response[1]) . ' WHERE id = ' . $database->quote($link->id) . ' LIMIT 1' );
			$database->execute();
		}
		echo $output;
	}
}

function checklinkcomplete() {
	global $database, $mtconf;
	
	$jdate		= JFactory::getDate();
	$now		= $jdate->toSql();
	
	$database->setQuery('UPDATE #__mt_config SET value = '.$database->Quote($now).' WHERE varname = \'linkchecker_last_checked\' LIMIT 1');
	$database->execute();
}
?>