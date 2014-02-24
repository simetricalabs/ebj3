<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

function startprint( $cat_id ) {
	$database = JFactory::getDBO();
	$database->setQuery("SELECT * FROM #__mt_cats WHERE cat_parent = -1 LIMIT 1");
	$root = $database->loadObject();
	echo '<h1>Mosets Tree Diagnosis</h1>';
	echo '<pre align="left">';
	printd(0, $root->cat_name, $root->cat_id, $root->cat_links, $root->cat_cats, $root->lft, $root->rgt);
	getsubcats( 0 );
	echo "</pre>";
}

function getsubcats( $cat_id ) {
	$database = JFactory::getDBO();

	static $level = 0;

	$database->setQuery( 'SELECT cat_id, cat_name, cat_cats, cat_links, lft, rgt FROM #__mt_cats WHERE cat_parent = ' . $database->quote($cat_id) . ' ORDER BY lft' );
	$cats = $database->loadObjectList();
	$level++;

	foreach( $cats AS $cat ) {
		printd($level, $cat->cat_name, $cat->cat_id, $cat->cat_links, $cat->cat_cats, $cat->lft, $cat->rgt);
		getsubcats( $cat->cat_id );

	}
	$level--;
}

function printd($level, $cat_name, $cat_id, $links_count, $cats_count, $lft, $rgt) {
	echo "<br /> &nbsp;".str_repeat("&nbsp;",($level)*4).str_repeat("-",($level)).$cat_name ." <small>[$cat_id] <font color=\"#C0C0C0\">$cats_count,$links_count</font></small> (<font color=blue>".$lft."</font>;<font color=green>".$rgt."</font>)";
}
?>