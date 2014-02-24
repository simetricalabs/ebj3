<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

function mtUpdateLinkCount( $cat_id, $inc ) {
	$database = JFactory::getDBO();

	$mtPathWay = new mtPathWay( $cat_id );
	$cat_parent_ids = implode(',',$mtPathWay->getPathWayWithCurrentCat());

	//echo "cat_parent_ids: ".$cat_parent_ids ;

	if ($inc < 0) {
		$database->setQuery("UPDATE #__mt_cats SET cat_links = (cat_links - ABS($inc)) WHERE cat_id IN ($cat_parent_ids)");
	} else {
		$database->setQuery("UPDATE #__mt_cats SET cat_links = (cat_links + ABS($inc)) WHERE cat_id IN ($cat_parent_ids)");
	}

	if (!$database->execute()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		return false;
	}

	return true;

}

function smartCountUpdate( $cat_id, $cat_links, $cat_cats ) {
	$database = JFactory::getDBO();

	// Add $old_cat_parent to $old_ancestors array first
	$new_ancestors = mtPathWay::getPathWay( $cat_id );
	$new_ancestors[] = $cat_id;

	if ( $cat_links < 0 ) {
		$cat_links_sym = "-";
	}	else {
		$cat_links_sym = "+";
	}

	if ( $cat_cats < 0 ) {
		$cat_cats_sym = "-";
	}	else {
		$cat_cats_sym = "+";
	}

	if ( count($new_ancestors) > 0 ) {
		foreach($new_ancestors AS $new_ancestor) {
			if ($new_ancestor > 0) {
				$database->setQuery( "UPDATE #__mt_cats SET cat_cats = (cat_cats $cat_cats_sym ABS(" . intval($cat_cats) . ")), cat_links = (cat_links $cat_links_sym ABS(" . intval($cat_links) . ")) WHERE cat_id = $new_ancestor" );
				$database->execute();
			}
		}
	}

	return true;

}

function smartCountUpdate_catMove( $old_cat_parent, $new_cat_parent, $cat_links, $cat_cats ) {
	$database = JFactory::getDBO();

	// Add $old_cat_parent to $old_ancestors array first
	$old_ancestors = mtPathWay::getPathWay( $old_cat_parent );
	$old_ancestors[] = $old_cat_parent;

	$new_ancestors = mtPathWay::getPathWay( $new_cat_parent );
	$new_ancestors[] = $new_cat_parent;

	if ( count($old_ancestors) > 0 ) {
		foreach($old_ancestors AS $old_ancestor) {
			if ($old_ancestor > 0) {
				$database->setQuery( "UPDATE #__mt_cats SET cat_cats = cat_cats - " . intval($cat_cats) . ", cat_links = cat_links - " . intval($cat_links) . " WHERE cat_id = $old_ancestor" );
				$database->execute();
			}
		}
	}

	if ( count($new_ancestors) > 0 ) {
		foreach($new_ancestors AS $new_ancestor) {
			if ($new_ancestor > 0) {
				$database->setQuery( "UPDATE #__mt_cats SET cat_cats = cat_cats + " . intval($cat_cats) . ", cat_links = cat_links + " . intval($cat_links) . " WHERE cat_id = $new_ancestor" );
				$database->execute();
			}
		}
	}

	return true;

}

function tellDateTime( $datetime ) {
	global $mtconf;

	if ( $datetime == '0000-00-00 00:00:00' ) {
		return JText::_( 'COM_MTREE_NEVER' );
	}

	$time_now = time()+$mtconf->getjconf('offset')*60*60;
	$time_str = strtotime( $datetime );

	$day_str = mktime( 0, 0, 0, date("m",$time_str),date("d",$time_str),date("Y",$time_str));
	$day_now = mktime( 0, 0, 0, date("m",$time_now),date("d",$time_now),date("Y",$time_now));

	# Today's date
	if ($day_now == $day_str) {
		return date("g:ia",$time_str);
	
	# This year's date
	} elseif ( date("Y",$day_now) == date("Y",$day_str) ) {
		return date("M j",$time_str);

	# Last year's date
	} else {
		return date("M j, Y",$time_str);
	}

	return ($day_now - $day_str);

}

function detect_ImageLibs() {
	$imageLibs=array();

	# Initialization - To allow Windows machine to do proper detection
	if(substr(PHP_OS, 0, 3) == 'WIN') {
		return array();
	}

	#Detect GD1/GD2
	$imageLibs['gd2'] = detect_GD_library();
	
	return $imageLibs;
}

function detect_GD_library() {
	$GDfuncList = get_extension_funcs('gd');
	
	ob_start();
	@phpinfo(INFO_MODULES);
	$output=ob_get_contents();
	ob_end_clean();
	$matches[1]='';
	if(preg_match("/GD Version[ \t]*(<[^>]+>[ \t]*)+([^<>]+)/s",$output,$matches)){
			$gdversion = $matches[2];
	}

	if( $GDfuncList ){
		if( trim($gdversion) == '1.6.2 or higher' ) {
			return false;
		} else {
			return 'GD Version '.$gdversion;
		}
	} else {
		return false;
	}
}

// Random listing generator
function generate_random_listings( $quantity=100 ) {
	$database = JFactory::getDBO();

	include_once( $mtconf->getjconf('absolute_path') . '/components/com_mtree/nonsense/nonsense.php' );
	$non = new Nonsense();

	$database->setQuery( "SELECT cat_id FROM #__mt_cats" );
	$categories = $database->loadColumn();
	$categories[] = 0;

	for ($i=0; $i<$quantity; $i++) {

		$non->word(rand(1,3));
		$link_name = ucfirst($non->output);

		$non->sentence(rand(1,3));
		$link_desc = $non->output;

		$user_id = rand(62,64);
		
		$cat_id = $categories[rand(0,(count($categories) -1))];

		$non->word(rand(1,3));
		$meta_keywords = $non->output;
		$non->sentence(1);
		$meta_desc = $non->output;
		//$non->word(rand(0,8));
		//$notes = $non->output;
		$notes = '';

		$non->word(rand(3,5));
		$address = $non->output;

		$non->word(1);
		$city = ucfirst($non->output);
		$non->word(1);
		$states = array("Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho ", "State", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky ", "Louisiana ", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma ", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming");
		$state = $states[ rand( 0, (count($states)-1) )];
		$non->word(1);
		$country = ucfirst($non->output);

		$non->word(1);
		$alias = $non->output;
		$non->word(1);
		$domain = $non->output;
		$non->word(1);
		$website = $non->output;

		for ($j=1; $j<=10; $j++) {
			$non->word(rand(1,9));
			$custom[$j] = $non->output;
		}

		$sql = "INSERT INTO `mos_mt_links` (`link_name`, `link_desc`, `user_id`, `link_hits`, `link_votes`, `link_rating`, `link_featured`, `link_published`, `link_approved`, `link_template`, `attribs`, `metakey`, `metadesc`, `internal_notes`, `ordering`, `link_created`, `publish_up`, `publish_down`, `link_modified`, `address`, `city`, `state`, `country`, `postcode`, `telephone`, `fax`, `email`, `website`, `price`, `cust_1`, `cust_2`, `cust_3`, `cust_4`, `cust_5`, `cust_6`, `cust_7`, `cust_8`, `cust_9`, `cust_10`) "
			."\nVALUES ('$link_name', '$link_desc', $user_id, ".rand(0,1000).", ".rand(0,300).", '".(rand(0,500)/100)."', 0, 1, 1, '', '', '$meta_keywords', '$meta_desc', '$notes', 0, '2004-0".rand(1,9)."-".rand(0,2).rand(0,9)." 00:00:00', '2004-0".rand(1,9)."-".rand(0,2).rand(0,9)." 00:00:00', '0000-00-00 00:00:00', '200".rand(3,4)."-01-01 00:00:00', '$address', '$city', '$state', '$country', '".rand(1000,10000)."', '".rand(10000000, 99999999)."', '".rand(10000000, 99999999)."', '".$alias."@".$domain.".com', 'http://www.".$website.".com/', '".(rand(0,100000)/100)."', '$custom[1]', '$custom[2]', '$custom[3]', '$custom[4]', '$custom[5]', '$custom[6]', '$custom[7]', '$custom[8]', '$custom[9]', '$custom[10]')";

		$database->setQuery($sql);
		$database->execute();

		$link_id = $database->insertid();

		$database->setQuery( "INSERT INTO `mos_mt_cl` (link_id, cat_id, main) VALUES('".$link_id."','".$cat_id."','1')" );
		$database->execute();
		//echo "<p />".$sql;
	}

}

function generate_random_reviews( $quantity=10, $link_ids='' ) {
	global $database, $mtconf;

	include_once( $mtconf->get('absolute_path') . '/components/com_mtree/nonsense/nonsense.php' );
	$non = new Nonsense();

	$database->setQuery( "SELECT link_id FROM #__mt_links" );
	$links = $database->loadColumn();

	$database->setQuery( "SELECT id FROM #__users" );
	$users = $database->loadColumn();

	if ( is_array($link_ids) ) {
		//$link_id = 7182;
		foreach( $link_ids AS $link_id ) {
			
			$qty = rand(1,$quantity);

			for ($i=0; $i<$qty; $i++) {

				//$link_id = $links[rand(0,(count($links) -1))];

				$non->word(rand(1,5));
				$rev_title = ucfirst($non->output);
				$non->sentence(rand(1,20));
				$rev_text = $non->output;
				$user_id = $users[rand(0,(count($users) -1))];

				$sql = "INSERT INTO `#__mt_reviews` ( `rev_id` , `link_id` , `user_id` , `guest_name` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) "
					.	"VALUES ('', '".$link_id."', '".$user_id."', '', '".$rev_title."', '".$rev_text."', '2004-0".rand(1,9)."-0".rand(1,9)."', '1')";

				$database->setQuery($sql);
				$database->execute();

			} // End for
		} // End foreach
	
	} // End if

}

class mtTree {

	function rebuild($parent, $left) { 
		$database = JFactory::getDBO();

		 // the right value of this node is the left value + 1 
		 $right = $left+1; 

		 // get all children of this node 
		 $database->setQuery("SELECT cat_id FROM #__mt_cats WHERE cat_parent = " . intval($parent));
		 $cat_ids = $database->loadColumn();

		 foreach( $cat_ids AS $cat_id ) {
			 // recursively build other sub categories
			 $right = $this->rebuild($cat_id, $right); 
		 }

		 // we've got the left value, and now that we've processed 
		 // the children of this node we also know the right value 
		$database->setQuery("UPDATE #__mt_cats SET lft=$left, rgt=$right WHERE cat_id=" . intval($parent) . " LIMIT 1");
		$database->execute();
		/*
		if ( $database->getNumRows() <= 0 ) {
			$database->setQuery("INSERT INTO #__mt_cats SET lft=$left, rgt=$right, cat_id=$parent ");
			$database->execute();
		}
		*/
		
		 // return the right value of this node + 1 
		return $right+1; 
	} 

}


?>