<?php
/**
 * @version		$Id: admin.mtree.class.php 2107 2013-10-16 07:43:32Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mtPathWay {

	var $cat_id = null;

	function mtPathWay( $cat_id=0 ) {
		$this->cat_id = $cat_id;
	}

	/***
	* Generates a pathway based from $link_id, if provided. Otherwise, it will lookup
	* this class cat_id ($this->cat_id) to generate the pathway
	*/
	function printPathWayFromLink( $link_id=0, $url='' ) {

		$database =& JFactory::getDBO();

		# Get Cat ID
		if ( $link_id > 0 ) {
			$database->setQuery( "SELECT cl.cat_id FROM #__mt_links AS l, #__mt_cl AS cl WHERE l.link_id = cl.link_id AND l.link_id = " . $database->quote($link_id) . " AND cl.main = '1'" );
			$this->cat_id = $database->loadResult();
		}

		# Generate the pathway array
		$path = $this->getPathWay( $this->cat_id );
		
		if ( $url <> '' ) {
			echo '<a href="'.$url.'">'.JText::_( 'COM_MTREE_ROOT' ).'</a> ';
		} else {
			echo JText::_( 'COM_MTREE_ROOT' );
		}
		
		if( count($path) > 0 ) {
			foreach($path AS $cat_id) {
				$database->setQuery('SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) .' LIMIT 1');
				$cat_name = $database->loadResult();
				if ( $url <> '' ) {
					echo JText::_( 'COM_MTREE_ARROW' ).'<a href="'.$url.'&cat_id='.$cat_id.'">'.$cat_name.'</a> ';
				} else {
					echo JText::_( 'COM_MTREE_ARROW' ).$cat_name;
				}
			}
		}

		if ($link_id > 0) {
			if ($this->cat_id > 0) {
				if ( $url <> '' ) {
					echo JText::_( 'COM_MTREE_ARROW' ).'<a href="'.$url.'&cat_id='.$this->cat_id.'">'.$this->getCatName().'</a> ';
				} else {
					echo JText::_( 'COM_MTREE_ARROW' ).$this->getCatName();
				}
			}
			$database->setQuery('SELECT link_name FROM #__mt_links WHERE link_id = ' . $database->quote($link_id));
			//echo $database->loadResult();
		} else {

			if ( $this->cat_id > 0 ) {
				# Print current directory
				echo JText::_( 'COM_MTREE_ARROW' ).$this->getCatName();
			}

		}
	}

	function printPathWayWithCurrentCat( $link_id=0, $url ) {

		$database =& JFactory::getDBO();

		$path = $this->getPathWay();
		
		echo "<a href=".$url.">".JText::_( 'COM_MTREE_ROOT' )."</a> > ";

		foreach($path AS $cat_id) {
			$database->setQuery('SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1');
			$cat_name = $database->loadResult();
			echo '<a href="'.$url.'&cat_id='.$cat_id.'">'.$cat_name.'</a> > ';
		}

		if ($link_id > 0) {
			if ($this->cat_id > 0) {
				echo '<a href="'.$url.'&cat_id='.$this->cat_id.'">'.$this->getCatName().'</a> > ';
			}
			$database->setQuery('SELECT link_name FROM #__mt_links WHERE link_id=' . $database->quote($link_id));
			echo $database->loadResult();
		} else {
			# Print current directory
			echo '<a href="'.$url.'&cat_id='.$this->cat_id.'">'.$this->getCatName().'</a>';
			//echo $this->getCatName();
		}
	}

	function printPathWayFromCat( $cat_id=0, $withlink=0 ) {
		
		$database =& JFactory::getDBO();

		$old_cat_id = $this->cat_id ;
		$this->cat_id = $cat_id;
		$path = $this->getPathWay( $cat_id );
		
		$return = '';

		if ( $withlink ) {
			$return .= '<a href="index.php?option=com_mtree&task=listcats&cat_id='.$cat_id.'">'.JText::_( 'COM_MTREE_ROOT' ).'</a> '.JText::_( 'COM_MTREE_ARROW' );
		} else {
			$return .= JText::_( 'COM_MTREE_ROOT' )." ";
		}

		if( !is_null($path) )
		{
			foreach($path AS $cat_id) {
				$database->setQuery('SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1');
				$cat_name = $database->loadResult();
				if ( $withlink ) {
					$return .= '<a href="index.php?option=com_mtree&task=listcats&cat_id='.$cat_id.'">'.$cat_name.'</a> '.JText::_( 'COM_MTREE_ARROW' );
				} else {
					$return .= JText::_( 'COM_MTREE_ARROW' ).$cat_name;
				}
			}
		}
		
		$this->cat_id = $old_cat_id;
		
		return $return;

	}

	function printPathWayFromCat_withCurrentCat( $cat_id=0, $withlink=0 ) {


		$return = '';
		$return .= $this->printPathWayFromCat( $cat_id, $withlink );

		if ( $cat_id <> 0 ) {
			if ( $withlink ) {
				$return .= '<a href="index.php?option=com_mtree&task=listcats&cat_id='.$cat_id.'">'.$this->getCatName( $cat_id ).'</a> '.JText::_( 'COM_MTREE_ARROW' );
			} else {
				$return .= JText::_( 'COM_MTREE_ARROW' );
				$return .= $this->getCatName( $cat_id );
			}
		}

		return $return;

	}

	function getPathWay( $node=0 ) {
		$database = JFactory::getDBO();
		
		$path = array();
		if ( $node > 0 ) {
			global $cache_paths;
			global $cache_lft_rgt;
			global $cache_cat_names;
			if ( !isset($cache_paths) || empty($cache_paths) || empty($cache_paths[$node]) ) {
				if(isset($cache_paths) && array_key_exists($node,$cache_paths)) {
					return $cache_paths[$node]; 
				} else {
					$database->setQuery("SELECT cat_name, cat_id, lft, rgt FROM #__mt_cats WHERE cat_id = ".$database->quote((($node <= 0) ? $this->cat_id : $node)));
					$left_right = $database->loadObject();
				}
				// if (!empty($left_right)) {
				if (!empty($left_right->lft)) {
					$database->setQuery("SELECT cat_id FROM #__mt_cats WHERE lft < $left_right->lft AND rgt > $left_right->rgt AND cat_id > 0 AND cat_parent >= 0 ORDER BY lft ASC");
					$cache_paths[$node] = $database->loadColumn();
					if( isset($cache_lft_rgt) && array_key_exists($left_right->lft,$cache_lft_rgt) ) {
						$cache_lft_rgt[$left_right->lft] = array_merge($cache_lft_rgt[$left_right->lft],array($left_right->rgt => $cache_paths[$node]));
					} else {
						$cache_lft_rgt[$left_right->lft] = array($left_right->rgt => $cache_paths[$node]);
					}
					if( isset($cache_cat_names) && !array_key_exists($left_right->cat_id,$cache_cat_names) ) {
						$cache_cat_names[$left_right->cat_id] = $left_right->cat_name;
					}
				}

			}
			return $cache_paths[$node]; 
		}
		return $path;
		
	}
	
	function getPathWayWithCurrentCat() {
		$path = $this->getPathWay( $this->cat_id );
		$path[] = $this->cat_id;
		return $path;
	}

	function getCatName( $cat_id = '' ) {
		$database =& JFactory::getDBO();

		# look up the name for this category
		if ( $cat_id == '' ) {
			$database->setQuery("SELECT cat_name FROM #__mt_cats WHERE cat_id = " . $database->quote($this->cat_id) . " LIMIT 1");
			return $database->loadResult();
		} else {
			global $cache_cat_names;
			if ( empty($cache_cat_names) || !array_key_exists($cat_id,$cache_cat_names) ) {
				$database->setQuery("SELECT cat_name FROM #__mt_cats WHERE cat_id = " . $database->quote($cat_id) . " LIMIT 1");
				$cache_cat_names[$cat_id] = $database->loadResult();
			}
			return $cache_cat_names[$cat_id];
		}
	}
	
	function getCatAlias( $cat_id ) {
		static $cat_aliases = array();

		if( array_key_exists($cat_id,$cat_aliases) ) {
			return $cat_aliases[$cat_id];
		} else {
			$db = JFactory::getDBO();
			$db->setQuery( 'SELECT alias FROM #__mt_cats WHERE cat_id = ' . $db->quote($cat_id) . ' LIMIT 1' );
			$cat_aliases[$cat_id] = $db->loadResult();
			return $cat_aliases[$cat_id];
		}
	}
}

/**
* Link Categories Table class
*/
class mtCats extends JTable {
	var $cat_id=null;
	var $cat_name=null;
	var $alias=null;
	var $title=null;
	var $cat_desc=null;
	var $cat_parent=null;
	var $cat_links=null;
	var $cat_cats=null;
	var $cat_image=null;
	var $cat_featured=null;
	var $cat_published=1;
	var $cat_created=null;
	var $cat_approved=1;
	var $cat_template=null;
	var $cat_usemainindex=null;
	var $cat_allow_submission=null;
	var $cat_show_listings=null;
	var $metakey=null;
	var $metadesc=null;
	var $ordering=null;
	var $lft=null;
	var $rgt=null;

	function __construct( &$_db ) {
		parent::__construct( '#__mt_cats', 'cat_id', $_db );
	}

	/***
	 * Return true if the given category is a child
	 */
	 function isChild( $child_id ) {
		$this->_db->setQuery( 'SELECT lft, rgt FROM #__mt_cats WHERE cat_id = ' . $this->_db->quote($child_id) . ' LIMIT 1' );
		$child = $this->_db->loadObject();

		if( $child->lft > $this->lft && $child->rgt < $this->rgt ) {
			return true;
		} else {
			return false;
		}
	 }

	/***
	* Update parent cat - increase categories count
	*/
	function updateCatCount( $inc=1 ) {
		if ($this->cat_id <= 0) {
			return false;
		} else {
		
			$cat_parent_ids = mtPathWay::getPathWay( $this->cat_id );
			
			if ( !empty($cat_parent_ids) ) {
				
				$cat_parent_ids2 = implode(',',$cat_parent_ids);
				//echo $cat_parent_ids2;
				if ($inc < 0) {
					$this->_db->setQuery('UPDATE #__mt_cats SET cat_cats = (cat_cats - ABS(' . intval($inc) . ')) WHERE cat_id IN (' . $cat_parent_ids2 . ')');
				} else {
					$this->_db->setQuery('UPDATE #__mt_cats SET cat_cats = (cat_cats + ABS(' . intval($inc) . ')) WHERE cat_id IN (' . $cat_parent_ids2 . ')');
				}

				if (!$this->_db->execute()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					return false;
				}

			}
			return true;
		}
	}

	function updateLftRgt() {
		
		# Retrieve parent's rgt value
		$this->_db->setQuery( "SELECT rgt FROM #__mt_cats WHERE cat_id = $this->cat_parent LIMIT 1" );
		$parent_rgt = $this->_db->loadResult();
		
		if ( is_null($parent_rgt) && $this->cat_parent == 0 ) {
			$this->_db->setQuery( "SELECT rgt FROM #__mt_cats WHERE cat_parent = -1 LIMIT 1" );
			$parent_rgt = $this->_db->loadResult();

			if ( is_null($parent_rgt) ) {
				$this->_db->setQuery( "SELECT rgt FROM #__mt_cats WHERE cat_name = 'Root' LIMIT 1" );
				$parent_rgt = $this->_db->loadResult();

				if ( is_null($parent_rgt) ) {
					$this->_db->setQuery( "SELECT (MAX(rgt) +1) FROM #__mt_cats" );
					$parent_rgt = $this->_db->loadResult();
				}

			}

		}

		# Update all category's lft and rgt (+ 2) to the right of this category
		$this->_db->setQuery("UPDATE #__mt_cats SET lft = lft+2 WHERE lft >= $parent_rgt");
		if (!$this->_db->execute()) {
				echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				return false;
		}

		$this->_db->setQuery("UPDATE #__mt_cats SET rgt = rgt+2 WHERE rgt >= $parent_rgt");
		if (!$this->_db->execute()) {
				echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				return false;
		}

		# Update this category's lft and rgt
		$this->_db->setQuery("UPDATE #__mt_cats SET lft = ".$parent_rgt.", rgt = ".($parent_rgt+1)." WHERE cat_id = $this->cat_id");
		if (!$this->_db->execute()) {
				echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				return false;
		}
		$this->lft = $parent_rgt;
		$this->rgt = $parent_rgt+1;

		return true;

	}

	/***
	* This function will first make sure if this is a top level category.
	* If it is a top level category, a default fields mapping will be done,
	* by assigning all Root-assigned fields to it.
	*
	* If the category is not a top level category, no assignment is done.
	*/
	function updateFieldsMap()
	{
		if( $this->cat_parent == 0 )
		{
			$this->_db->setQuery( "INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) "
				. " SELECT cf_id, '".$this->cat_id."' FROM #__mt_fields_map WHERE cat_id = " . $this->cat_parent
				);
			$this->_db->execute();
		}
		return true;
	}
	
	/***
	* Delete individual Category
	*/
	function deleteCat( $cat_id ) {
		global $mtconf;
		
		# Delete all the links
		$this->delCatLinks( $cat_id );

		# Delete all related categories
		$this->_db->setQuery( "DELETE FROM #__mt_relcats WHERE cat_id = '".$cat_id."'" );
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		# Remove the photos
		$this->_db->setQuery( "SELECT cat_image FROM #__mt_cats WHERE cat_id = '".$cat_id."'" );
		$cat_image = $this->_db->loadResult();
		if ( $cat_image <> '' ) {
			unlink( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image').$cat_image );
			unlink( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image').$cat_image );
		}

		# Delete all fields map
		$this->_db->setQuery( "DELETE FROM #__mt_fields_map WHERE cat_id = '".$cat_id."'" );
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}
		
		# Delete this cat
		$this->_db->setQuery("DELETE FROM #__mt_cats WHERE cat_id = '".$cat_id."'");

		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;

	}
	
	/***
	* Delete Category recursively
	*/
	function deleteCats( $cat_id ) {

		# Find lft & rgt value of this category
		$this->_db->setQuery( "SELECT lft, rgt, cat_approved FROM #__mt_cats WHERE cat_id = $cat_id LIMIT 1" );
		$lr = $this->_db->loadObject();		

		if ( $lr->cat_approved == 0 OR ($lr->lft == 0 AND $lr->rgt == 0) ) {

			$cat_ids = array( $cat_id );

		} else {

			#Look up all categories committed for deletion
			$this->_db->setQuery( "SELECT cat_id FROM #__mt_cats WHERE lft >= $lr->lft AND rgt <= $lr->rgt" );
			$cat_ids = $this->_db->loadColumn();
		
		}

		# Remove categories
		foreach( $cat_ids AS $cid ) {
			$this->deleteCat( $cid );
		}

		# Update all categories to the right
		$this->_db->setQuery(" UPDATE #__mt_cats SET lft = lft-".(count($cat_ids)*2)." WHERE lft > $lr->rgt");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		$this->_db->setQuery(" UPDATE #__mt_cats SET rgt = rgt-".(count($cat_ids)*2)." WHERE rgt > $lr->rgt");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;

	}

	/***
	* Delete all links for a category
	*/
	function delCatLinks( $cat_id ) {

		$this->_db->setQuery("SELECT l.link_id FROM #__mt_links AS l, #__mt_cl AS cl WHERE cl.link_id = l.link_id AND cl.cat_id = '".$cat_id."' AND cl.main = '1'");
		$link_ids = $this->_db->loadColumn();

		# Remove all hard listing
		foreach($link_ids AS $link_id) {
			mtLinks::delLink( $link_id );
		}

		# Remove all soft listing
		$this->_db->setQuery("DELETE FROM #__mt_cl WHERE cat_id = '".$cat_id."'");
		$this->_db->execute();

		return true;
	}

	/***
	* Publish Category
	*/
	function publishCat( $publish, $cat_id=0 ) {
		
		# Determine which Cat ID to use. If none, return false.
		if ( $this->cat_id > 0 ) {
			$cid = $this->cat_id;
		} elseif ( $cat_id > 0) {
			$cid = $cat_id;
		} else {
			return false;
		}

		$this->_db->setQuery("UPDATE #__mt_cats SET cat_published = " . $this->_db->quote($publish) . " WHERE cat_id = " . $this->_db->quote($cid) );
	
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
			return true;
		}

	}

	/***
	* Approve Category
	*/
	function approveCat( $cat_id=0 ) {
		
		# Determine which Cat ID to use. If none, return false.
		if ( $this->cat_id > 0 ) {
			$cid = $this->cat_id;
		} elseif ( $cat_id > 0) {
			$cid = $cat_id;
		} else {
			return false;
		}

		$this->_db->setQuery("UPDATE #__mt_cats SET cat_approved = '1' WHERE cat_id = '".$cid."'");
	
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
			return true;
		}

	}

	/***
	* Set Featured for category
	*/
	function setFeaturedCat( $featured=1, $cat_id=0 ) {
		if ($cat_id == 0 && $this->cat_id > 0) {
			$this->_db->setQuery("UPDATE #__mt_cats SET cat_featured = '".intval($featured)."' WHERE cat_id = '".$this->cat_id."'");
		} else {
			$this->_db->setQuery("UPDATE #__mt_cats SET cat_featured = '".intval($featured)."' WHERE cat_id = '".$cat_id."'");
		}
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
			return true;
		}
	}

	/***
	* Set all sub categories to use the same template
	*/
	function updateSubCatsTemplate() {
		$mtPathway = new mtPathway();
		
		$subcats = $this->getSubCats_Recursive( $this->cat_id );

		$this->_db->setQuery( "UPDATE $this->_tbl SET cat_template = " . $this->_db->quote($this->cat_template) . " WHERE cat_id IN(". implode(',',$subcats) .") " );
		$this->_db->execute();
	}

	/***
  	* Return an array with all sub categories (including owner)
	*/
	function getSubCats_Recursive( $cat_id='', $published_only=false ) {
		
		$subcats = array();
		$left_right = null;
		
		// Find current category's lft and rgt value
		$this->_db->setQuery("SELECT lft, rgt FROM #__mt_cats WHERE cat_id = $cat_id LIMIT 1");
		$left_right = $this->_db->loadObject();
		
		if ( !empty($left_right)) {
			// Find all subcategories
			if ( $published_only ) {
				$sql = "SELECT cat_id FROM #__mt_cats WHERE lft BETWEEN $left_right->lft AND $left_right->rgt AND cat_published = 1";
			} else {
				$sql = "SELECT cat_id FROM #__mt_cats WHERE lft BETWEEN $left_right->lft AND $left_right->rgt";
			}

			$this->_db->setQuery($sql);
			$subcats = $this->_db->loadColumn();
		}

		return $subcats;

	}

	/***
	* Copy Category
	*/
	function copyCategory( $cat_id, $dest, $copy_subcats, $copy_relcats, $copy_listings, $copy_reviews, $reset_hits, $reset_rating, $increment=null ) {
		global $mtconf;
		static $copied_cat_ids;

		$database =& JFactory::getDBO();

		# Get original cat's info
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE cat_id = '".$cat_id."' LIMIT 1" );
		$org_cat = $this->_db->loadObject();

		# Get $dest (New cat parent)'s lft & rgt

		if ( is_null($increment) ) {
			$this->_db->setQuery( "SELECT lft, rgt FROM $this->_tbl WHERE cat_id = '".$dest."' LIMIT 1" );
			$new_cat = $this->_db->loadObject();

			$inc = $new_cat->rgt - $org_cat->lft;

			$copied_cat_ids = array();
		} else {
			$inc = $increment;
		}

		# Change cat_parent
		$org_cat->cat_parent = $dest;

		# Copy cat
		$this->_db->setQuery( 'INSERT INTO ' . $this->_tbl 
			. ' (cat_name, alias, title, cat_desc, cat_parent, cat_links, cat_cats, cat_featured, cat_association, cat_published, cat_created, cat_approved, cat_template, cat_usemainindex, cat_allow_submission, cat_show_listings, metakey, metadesc, ordering, lft, rgt) '
			. ' VALUES(' . $this->_db->quote($org_cat->cat_name).', '
			. $this->_db->quote($org_cat->alias) . ', '
			. $this->_db->quote($org_cat->title) . ', '
			. $this->_db->quote($org_cat->cat_desc) . ', '
			. $this->_db->quote($org_cat->cat_parent) . ', '
			. (($copy_listings)?$org_cat->cat_links:'0') . ', '
			. $this->_db->quote($org_cat->cat_cats) . ', '
			. $this->_db->quote($org_cat->cat_featured) . ', '
			. $this->_db->quote($org_cat->cat_association) . ', '
			. $this->_db->quote($org_cat->cat_published) . ', '
			. $this->_db->quote($org_cat->cat_created) . ', '
			. $this->_db->quote($org_cat->cat_approved) . ', '
			. $this->_db->quote($org_cat->cat_template) . ', '
			. $this->_db->quote($org_cat->cat_usemainindex) . ', '
			. $this->_db->quote($org_cat->cat_allow_submission) . ', '
			. $this->_db->quote($org_cat->cat_show_listings) . ', '
			. $this->_db->quote($org_cat->metakey) . ', '
			. $this->_db->quote($org_cat->metadesc) . ', '
			. $this->_db->quote($org_cat->ordering) . ', '
			. $this->_db->quote($org_cat->lft + $inc) . ', '
			. $this->_db->quote($org_cat->rgt + $inc) . ')'
			);
		$this->_db->execute();

		$new_cat_parent = $this->_db->insertid();
		$copied_cat_ids[] = $new_cat_parent;

		# Copy image
		$file_s = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image') . $org_cat->cat_image;
		$file_o = $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image') . $org_cat->cat_image;
		if ( $org_cat->cat_image && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_small_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_original_image')) ) {
			if( 
				copy( $file_s, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image').$new_cat_parent."_".(substr($org_cat->cat_image, strpos( $org_cat->cat_image, "_" )+1 )) ) 
				&&
				copy( $file_o, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image').$new_cat_parent."_".(substr($org_cat->cat_image, strpos( $org_cat->cat_image, "_" )+1 )) ) 
			) {
				$this->_db->setQuery( 'UPDATE #__mt_cats SET cat_image = ' . $this->_db->quote($new_cat_parent."_".(substr($org_cat->cat_image, strpos( $org_cat->cat_image, "_" )+1 ))) . ' WHERE cat_id = ' . $this->_db->quote($new_cat_parent) );
				$this->_db->execute();
			}
		}

		# Copy Related Categories
		$this->_db->setQuery( "SELECT rel_id FROM #__mt_relcats WHERE cat_id = '".$cat_id."'" );
		$rel_ids = $this->_db->loadColumn();

		if ( count( $rel_ids ) > 0 && $copy_relcats == 1 ) {
			foreach( $rel_ids AS $rel_id ) {
				$this->_db->setQuery( "INSERT INTO #__mt_relcats ( cat_id, rel_id ) VALUES ( '$new_cat_parent', '$rel_id' )" );
				$this->_db->execute();
			}
		}

		# Copy listings
		if ( $copy_listings == 1 ) {

			$this->_db->setQuery( "SELECT l.link_id FROM #__mt_links AS l, #__mt_cl AS cl WHERE l.link_id = cl.link_id AND cl.cat_id ='".$cat_id."' AND cl.main = '1'" );
			$listings = $this->_db->loadColumn();

			if ( count($listings) > 0 ) {
				foreach( $listings AS $listing ) {
					
					$l = new mtLinks( $database );
					$l->copyLink( $listing, $new_cat_parent, $reset_hits, $reset_rating, $copy_reviews, 1);

				}
			}
			
			# Copy soft listing / CL mapping
			/*
			$this->_db->setQuery( "INSERT INTO #__mt_cl( link_id, cat_id, main ) SELECT link_id, '".$new_cat_parent."', '0' FROM #__mt_cl WHERE cat_id = '".$cat_id."' AND main = '0'" );
			$this->_db->execute();
			echo $this->_db->getQuery();
			*/
		}

		# Copy Sub categories
		$this->_db->setQuery( "SELECT cat_id FROM $this->_tbl WHERE cat_parent = '".$cat_id."'" );
		$subcats = $this->_db->loadColumn();

		if ( count($subcats) > 0 && $copy_subcats == 1 ) {
			foreach( $subcats AS $subcat ) {
				$this->copyCategory( $subcat, $new_cat_parent, $copy_subcats, $copy_relcats, $copy_listings, $copy_reviews, $reset_hits, $reset_rating, $inc );
			}
		}
		
		return $copied_cat_ids;

	}

	/***
	* Move Category up/down the order
	*/
	function order( $move ) {
		$this->width = (int) $this->rgt - $this->lft + 1;
		
		// Get the primary keys of child nodes.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_key);
		$query->from($this->_tbl);
		$query->where('lft BETWEEN '.(int) $this->lft.' AND '.(int) $this->rgt);
		$this->_db->setQuery($query);
		$children = $this->_db->loadColumn();
		
		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		
		// Get sibling data
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_key.', cat_parent, lft, rgt');
		$query->from($this->_tbl);

		switch( $move ) {
			case -1: // Moving up
				$query->where('rgt = '.(((int) $this->lft) -1) );

				break;
			case 1: // Moving down
				$query->where('lft = '.(((int) $this->rgt) +1) );
				break;
		}

		$this->_db->setQuery($query, 0, 1);
		$sibling = $this->_db->loadObject();

		// Do some simple calculations.
		$sibling->width = (int) $sibling->rgt - $sibling->lft + 1;

		// Shift left and right values for the node and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		switch( $move ) {
			case -1:
				$query->set('lft = lft - '.(int) $sibling->width);
				$query->set('rgt = rgt - '.(int) $sibling->width);
				break;
			case 1:
				$query->set('lft = lft + '.(int) $sibling->width);
				$query->set('rgt = rgt + '.(int) $sibling->width);
				break;
		}
		$query->where('lft BETWEEN '.(int) $this->lft.' AND '.(int) $this->rgt);
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->execute())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		switch( $move ) {
			case -1:
				$query->set('lft = lft + '.(int) $this->width);
				$query->set('rgt = rgt + '.(int) $this->width);
				break;
			case 1:
				$query->set('lft = lft - '.(int) $this->width);
				$query->set('rgt = rgt - '.(int) $this->width);
				break;
		}
		$query->where('lft BETWEEN '.(int) $sibling->lft.' AND '.(int) $sibling->rgt);
		$query->where($this->_tbl_key.' NOT IN ('.implode(',', $children).')');
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->execute())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		return true;
	}

	/***
	* Returns total number of sub categories
	*/
	function getNumOfCats( $cat_id ) {
		if ($cat_id <= 0) {
			// Counting number of cats for root
			$this->_db->setQuery("SELECT COUNT(*) as total FROM #__mt_cats WHERE cat_parent = '0'");
			return $this->_db->loadResult();
		} else {
			return $this->cat_cats;
		}
	}

	/***
	* Returns total number of related categories
	*/
	function getNumOfRelCats( $cat_id=0 ) {
		if ($cat_id <= 0) {
			// Counting number of cats for root
			$cat_id = $this->cat_id;
		}

		$this->_db->setQuery("SELECT COUNT(*) as total FROM #__mt_relcats WHERE cat_id = '$cat_id'");
		return $this->_db->loadResult();
	}

	/***
	* Returns total number of links, Recursively
	*/
	function getNumOfLinks( $cat_id ) {
		if ($cat_id <= 0) {
			// Counting number of cats for root
			$this->_db->setQuery("SELECT COUNT(*) as total FROM #__mt_links AS l, #__mt_cl AS cl WHERE l.link_id = cl.link_id AND cl.cat_id = '0'");
			return $this->_db->loadResult();
		} else {
			return $this->cat_links;
		}
	}

	/***
	* Returns total number of links for this category ONLY
	*/
	function getNumOfLinks_NoRecursive( $cat_id=0, $approved=1 ) {
		$this->_db->setQuery("SELECT COUNT(*) as total FROM #__mt_links AS l, #__mt_cl AS cl WHERE l.link_id = cl.link_id AND cl.cat_id = '$cat_id' AND l.link_approved = '$approved'");
		return $this->_db->loadResult();
	}

	/***
	* Returns Parent category
	*/
	function getParent( $cat_id=0 ) {
		
		if ($this->cat_id > 0 && $cat_id == 0) {
			return $this->cat_parent;
		} else {
			$this->_db->setQuery("SELECT cat_parent FROM #__mt_cats WHERE cat_id = '$cat_id'");
			return $this->_db->loadResult();
		}
	}

	function getName( $cat_id=0 ) {
		if ($cat_id == 0) {
			return JText::_( 'COM_MTREE_ROOT' );
		} else {
			$this->_db->setQuery("SELECT cat_name FROM #__mt_cats WHERE cat_id = '$cat_id' LIMIT 1");
			return $this->_db->loadResult();
		}
	}

}

class mtCL extends JTable {

	var $cl_id=null;
	var $link_id=null;
	var $cat_id=null;
	var $main=null;
	
	function __construct( &$_db ) {
		parent::__construct( '#__mt_cl', 'cl_id', $_db );
	}
	
	function update( $cat_id, $link_id ) {
		$this->_db->setQuery( "SELECT cat_id FROM #__mt_links AS l, #__mt_cl AS cl WHERE l.link_id = cl.link_id AND l.link_id = $link_id AND cl.main = 1" );
		$ori = $this->_db->loadObject();
		$this->_db->setQuery( "UPDATE #__mt_cl SET cat_id = $cat_id WHERE cat_id = $ori->cat_id AND link_id = $link_id" );
		$this->_db->execute();
	}
	
}

class mtCL_main0 {

	var $_db=null;
	var $link_id=null;
	var $cat_id=null;
	var $other_cat_ids=null;

	function mtCL_main0( &$db ) {
		$this->_db = $db;
	}

	function load( $link_id ) {
		$this->link_id = $link_id;

		$this->_db->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = '$link_id' AND main = '0'" );
		$this->other_cat_ids = $this->_db->loadColumn();

		$this->_db->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = '$link_id' AND main = '1' LIMIT 1" );
		$this->cat_id = $this->_db->loadResult();

		return true;
	}
	
	function update( $new_other_cat_ids ) {
		$insert_ids = array();
		
		$new_other_cat_ids = array_diff( $new_other_cat_ids, (array("0"=>$this->cat_id)) );

		// Get the new other cats
		$removes = array_diff( $this->other_cat_ids, $new_other_cat_ids );
		foreach( $removes AS $remove ) {
			$this->_db->setQuery( "DELETE FROM #__mt_cl WHERE link_id = ".$this->_db->Quote($this->link_id)." AND cat_id = ".$this->_db->Quote($remove)." AND main = '0'" );
			$this->_db->execute();
			mtUpdateLinkCount( $remove, -1 );
		}

		// Get the soon to be removed cats
		$adds = array_diff( $new_other_cat_ids, $this->other_cat_ids );

		foreach( $adds AS $add ) {
			if ( !is_null($add) && is_numeric($add) ) {
				$this->_db->setQuery( "INSERT INTO #__mt_cl ( link_id, cat_id, main )  VALUES(".$this->_db->Quote($this->link_id).", ".$this->_db->Quote($add).", '0')" );
				$this->_db->execute();
				$insert_ids[] = $this->_db->insertid();
				mtUpdateLinkCount( $add, 1 );
			}
		}
		
		return $insert_ids;

	}
	
}

/**
* Links Table class
*/
class mtLinks extends JTable {
	var $link_id=null;
	var $link_name=null;
	var $alias=null;
	var $link_desc=null;
	var $user_id=null;
	var $cat_id=null;
	var $link_hits=null;
	var $link_votes=null;
	var $link_rating=null;
	var $link_featured=null;
	var $link_published=null;
	var $link_approved=null;
	var $link_template=null;
	var $attribs=null;
	var $metakey=null;
	var $metadesc=null;
	var $internal_notes=null;
	var $ordering=null;
	var $link_created=null;
	var $publish_up=null;
	var $publish_down=null;
	var $link_modified=null;
	var $link_visited=null;
	var $address=null;
	var $city=null;
	var $state=null;
	var $country=null;
	var $postcode=null;
	var $telephone=null;
	var $fax=null;
	var $email=null;
	var $website=null;
	var $price=null;
	var $show_map=null;
	var $lat=null;
	var $lng=null;
	var $zoom=null;

	function __construct( &$_db ) {
		parent::__construct( '#__mt_links', 'link_id', $_db );
	}

	function store( $updateNulls=false ) {
		$database =& JFactory::getDBO();
		
		$k = $this->_tbl_key;
		$cl = new mtCL( $database );

		if( $this->$k ) {
			if( isset($this->cat_id) && isset($this->link_id) )
			{
				$cl->update( $this->cat_id, $this->link_id );
				unset( $this->cat_id);
				$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
			} else { 
				$ret = true;
			}
		} else {
			// Store a new map to #__mt_cl
			$cl->cat_id = $this->cat_id;
			$cl->main = 1;

			unset( $this->cat_id);
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );

			$cl->link_id = $this->link_id;
			if (!$cl->store()) {
				echo "<script> alert('".$cl->getError().": $this->link_id"." - $cl->link_id "."'); window.history.go(-1); </script>\n";
				exit();
			}

		}
		if( !$ret ) {
			$this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
			return false;
		} else {
			return true;
		}
	}

	function load( $oid=null, $reset = true ) {
		static $cached_query_results = array();
		
		$k = $this->_tbl_key;

		if ($oid !== null) {
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if ($oid === null) {
			return false;
		}

		if ($reset)
		{
			$this->reset();
		}
		
		if( array_key_exists($oid,$cached_query_results) ) {
			return $this->bind($cached_query_results[$oid]);
		} else {
			$db = JFactory::getDBO();

			$query = 'SELECT l.*, cl.cat_id AS cat_id'
			. ' FROM '.$this->_tbl.' AS l, #__mt_cl AS cl'
			. ' WHERE l.'.$this->_tbl_key.' = cl.link_id AND l.'.$this->_tbl_key.' = '.$db->Quote($oid).' AND main = 1';
			$db->setQuery( $query );

			if ($result = $db->loadAssoc( )) {
				$cached_query_results[$oid] = $result;
				return $this->bind($result);
			}
			else
			{
				$this->setError( $db->getErrorMsg() );
				return false;
			}			
		}
	}
	
	/***
	* Update parent cat - increase links count
	*
	* A simple routine to increase or decrease a category's link 
	* count. If no category is specified, it will default to the 
	* link's parent category. The update bubbles up to top level 
	* category.
	*/
	function updateLinkCount( $inc=1, $cat_id = null ) {
		
		if ( !isset($this->cat_id) && is_null($cat_id) ) {
			return false;
		} elseif ( is_null($cat_id) && $this->cat_id >= 0 ) {
			$cat_id = $this->cat_id;
		} 

		$mtPathWay = new mtPathWay( $cat_id );
		$cat_parent_ids = implode(',',$mtPathWay->getPathWayWithCurrentCat());

		if ($inc < 0) {
			$this->_db->setQuery('UPDATE #__mt_cats SET cat_links = (cat_links - ABS(' . intval($inc) . ")) WHERE cat_id IN ($cat_parent_ids)");
		} else {
			$this->_db->setQuery('UPDATE #__mt_cats SET cat_links = (cat_links + ABS(' . intval($inc) . ")) WHERE cat_id IN ($cat_parent_ids)");
		}

		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;

	}

	/***
	* Publish Link
	*/
	function publishLink( $publish, $link_id=0 )
	{
		# Determine which Link ID to use. If none, return false.
		if ( $this->link_id > 0 ) {
			$lid = $this->link_id;
		} elseif ( $link_id > 0) {
			$lid = $link_id;
		} else {
			return false;
		}

		$this->_db->setQuery("UPDATE #__mt_links SET link_published = " . $this->_db->quote((int)$publish) . " WHERE link_id = '".$lid."'");
	
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
			$dispatcher	=& JDispatcher::getInstance();
			JPluginHelper::importPlugin('finder');
			$dispatcher->trigger('onContentChangeState', array('com_mtree.listing', array($lid), (int)$publish));
			
			return true;
		}

	}

	/***
	* Set Featured value for Links
	*/
	function setFeaturedLink( $featured=1, $link_id=0 ) {
		if ($link_id == 0 && $this->link_id > 0) {
			$this->_db->setQuery("UPDATE #__mt_links SET link_featured = " . $this->_db->quote($featured) . " WHERE link_id = '".$this->link_id."'");
		} else {
			$this->_db->setQuery("UPDATE #__mt_links SET link_featured = " . $this->_db->quote($featured) . " WHERE link_id = '".$link_id."'");
		}
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
			return true;
		}
	}

	/***
	* Delete link
	*/
	function delLink( $link_id=0 ) {
		global $mtconf;

		jimport('joomla.filesystem.file');

		if ($link_id == 0 && $this->link_id > 0) {
			$link_id = $this->link_id;
		}

		# Remove all reviews
		$this->_db->setQuery("DELETE FROM #__mt_reviews WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		# Remove all votes
		$this->_db->setQuery("DELETE FROM #__mt_log WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		# Remove CL mapping
		$this->_db->setQuery("DELETE FROM #__mt_cl WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}
		
		# Remove custom fields' value
		$this->_db->setQuery("DELETE FROM #__mt_cfvalues WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}
		
		# Delete attachments
		$this->_db->setQuery( 'SELECT CONCAT(' . $this->_db->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $this->_db->quote($link_id) );
		$raw_filenames = $this->_db->loadColumn();
		if( !empty($raw_filenames) )
		{
			if( !empty($raw_filenames) )
			{
				JFile::delete($raw_filenames);
			}
		}
		
		# Remove custom field data attachment
		$this->_db->setQuery("DELETE FROM #__mt_cfvalues_att WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}

		# Remove images
		$this->_db->setQuery( "SELECT filename, ordering FROM #__mt_images WHERE link_id = '" . $link_id . "'" );
		$listing_images = $this->_db->loadObjectList();
		if(count($listing_images)) {
			foreach($listing_images AS $listing_image) {
				JFile::delete($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $listing_image->filename);
				JFile::delete($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $listing_image->filename);
				JFile::delete($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $listing_image->filename);
			}
			$this->_db->setQuery("DELETE FROM #__mt_images WHERE link_id = '".$link_id."'");
			if (!$this->_db->execute()) {
				echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				return false;
			}
		}		

		# Remove the link itself
		$this->_db->setQuery("DELETE FROM #__mt_links WHERE link_id = '".$link_id."'");
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		}
		
		$object_listing = new JObject(array('link_id' => $link_id));
		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentAfterDelete', array('com_mtree.listing', &$object_listing));
		
		return true;

	}

	/***
	* Approve Link
	* As long as a link is approved, a notification mail is sent the to user regardless of the listing is
	* published or not
	*/
	function approveLink( $link_id=0 ) {
		global $mtconf;
		
		$app		= JFactory::getApplication('site');
		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

		$jdate		= JFactory::getDate();
		$now		= $jdate->toSql();

		# Determine which Link ID to use. If none, return false.
		if ( $this->link_id > 0 ) {
			$lid = $this->link_id;
		} elseif ( $link_id > 0) {
			$lid = $link_id;
		} else {
			return false;
		}

		# Check if this is an approval to modification or new listing
		$this->_db->setQuery( "SELECT link_approved FROM #__mt_links WHERE link_id = '".$lid."'");
		$link_approved = $this->_db->loadResult();

		// Approval to modification
		if ( $link_approved < 0 ) {

			$original_link_id = (-1 * $link_approved);
			//$this->_tbl_key = $original_link_id;
			$this->link_approved = 1;

			// Insert new listing
			$new_link_id = $this->link_id;
			$this->link_id = $original_link_id;
			
			# Delete attachments
			$this->_db->setQuery( 'SELECT CONCAT(' . $this->_db->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $this->_db->quote($original_link_id) );
			$raw_filenames = $this->_db->loadColumn();
			if( !empty($raw_filenames) )
			{
				if( !empty($raw_filenames) )
				{
					jimport('joomla.filesystem.file');
					JFile::delete($raw_filenames);
				}
			}
			
			$this->_db->setQuery( "DELETE FROM #__mt_cfvalues WHERE link_id = '" . $original_link_id . "'" );
			$this->_db->execute();
			$this->_db->setQuery( "UPDATE #__mt_cfvalues SET link_id = '" . $original_link_id . "' WHERE link_id = '" . $lid . "'" );
			$this->_db->execute();
			$this->_db->setQuery( "DELETE FROM #__mt_cfvalues_att WHERE link_id = '" . $original_link_id . "'" );
			$this->_db->execute();
			$this->_db->setQuery( "UPDATE #__mt_cfvalues_att SET link_id = '" . $original_link_id . "' WHERE link_id = '" . $lid . "'" );
			$this->_db->execute();
			# Remove images
			$this->_db->setQuery( "SELECT filename FROM #__mt_images WHERE link_id = '" . $original_link_id . "'" );
			$listing_images = $this->_db->loadColumn();
			if(count($listing_images)) {
				foreach($listing_images AS $listing_image) {
					unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $listing_image);
					unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $listing_image);
					unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $listing_image);
				}
				$this->_db->setQuery( "DELETE FROM #__mt_images WHERE link_id = '" . $original_link_id . "'" );
				if (!$this->_db->execute()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					return false;
				}
			}
			$this->_db->setQuery( "SELECT img_id, filename FROM #__mt_images WHERE link_id = '" . $lid . "'" );
			$current_listing_images = $this->_db->loadObjectList();
			if(count($current_listing_images)) {
				$file_extension = pathinfo($current_listing_images[0]->filename);
				$file_extension = strtolower($file_extension['extension']);
				foreach($current_listing_images AS $current_listing_image) {
					rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $current_listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $current_listing_image->img_id . '.' . $file_extension);
					rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $current_listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $current_listing_image->img_id . '.' . $file_extension);
					rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $current_listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $current_listing_image->img_id . '.' . $file_extension);
					$this->_db->setQuery( "UPDATE #__mt_images SET filename = '" . $current_listing_image->img_id . '.' . $file_extension . "' WHERE img_id = '" . $current_listing_image->img_id . "'" );
					$this->_db->execute();
				}
			}
			// $this->_db->setQuery( "DELETE FROM #__mt_images WHERE link_id = '" . $original_link_id . "'" );
			// $this->_db->execute();
			$this->_db->setQuery( "UPDATE #__mt_images SET link_id = '" . $original_link_id . "' WHERE link_id = '" . $lid . "'" );
			$this->_db->execute();
			
			$this->_db->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = '".$original_link_id."' AND main = '1'" );
			$ori_cat_id = $this->_db->loadResult();

			if ( $ori_cat_id <> $this->cat_id ) {
				$this->_db->setQuery( "UPDATE #__mt_cl SET cat_id = '".$this->cat_id."' WHERE link_id = '".$original_link_id."' AND main = '1'" );
				$this->_db->execute();
			}

			$this->store();

			// Remove *new* unused listing
			$this->_db->setQuery( "DELETE FROM #__mt_links WHERE link_id = '".$new_link_id."'" );
			$this->_db->execute();
			$this->_db->setQuery( "DELETE FROM #__mt_cl WHERE link_id = '".$new_link_id."'" );
			$this->_db->execute();
			
			// Fire finder plugin
			$dispatcher->trigger('onContentAfterDelete', array('com_mtree.listing', new JObject(array('link_id' => $new_link_id))));
			$dispatcher->trigger('onContentAfterDelete', array('com_mtree.listing', new JObject(array('link_id' => $this->link_id))));
			$dispatcher->trigger('onContentAfterSave', array('com_mtree.listing', $this, false));
			// Send approval notification to user
			if ( $mtconf->get('notifyuser_approved') == 1 ) {

				$this->_db->setQuery( 
					"SELECT u.email FROM #__mt_links AS l"
					.	"\nLEFT JOIN #__users AS u ON u.id = l.user_id"
					.	"\nWHERE l.link_id = '".$this->link_id."' LIMIT 1"
				);
				$email = $this->_db->loadResult();

				if ( $email <> '' ) {
					$subject	= JText::_( 'COM_MTREE_UPDATE_LISTING_APPROVED_SUBJECT' );
					$body 		= JText::sprintf( 'COM_MTREE_UPDATE_LISTING_APPROVED_MSG', $this->link_name );
					JFactory::getMailer()->sendMail( $MailFrom, $FromName, $email, $subject, $body );
				}

			}

			return true;

		// Approval to new listing
		} else {
			
			# Approve and reset the created date. This ensure the approved listing is shown in Latest Listings
			$sql = "UPDATE #__mt_links SET link_approved = '1'";
			if( $mtconf->get('reset_created_date_upon_approval') ) {
				$sql .= ", link_created = ". $this->_db->Quote($now);
			}
			$sql .= " WHERE link_id = '".$lid."'";
			$this->_db->setQuery( $sql );

			if (!$this->_db->execute()) {
				echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				return false;
			}

			$this->_db->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = '".$lid."' AND main = '1'" );
			$ori_cat_id = $this->_db->loadResult();

			if ( $ori_cat_id <> $this->cat_id ) {
				$this->_db->setQuery( "UPDATE #__mt_cl SET cat_id = '".$this->cat_id."' WHERE link_id = '".$lid."' AND main = '1'" );
				$this->_db->execute();
			}

			// Send approval notification to user
			if ( $mtconf->get('notifyuser_approved') == 1 ) {
				
				// If the directory has been configured to accept listings from non-registered user,
				// use the email value instead for the mail. Otherwise, the mail will go to the
				// default owner which is the admin.
				if( $mtconf->get('user_addlisting') == 0 && !empty($this->email) ) {
					$email = $this->email;
				} else {
					$this->_db->setQuery( 
						"SELECT u.email FROM #__mt_links AS l"
						.	"\nLEFT JOIN #__users AS u ON u.id = l.user_id"
						.	"\nWHERE l.link_id = '".$this->link_id."' LIMIT 1"
					);
					$email = $this->_db->loadResult();
				}
				if ( $email <> '' ) {
					$subject = JText::_( 'COM_MTREE_NEW_LISTING_APPROVED_SUBJECT' );
					$body = JText::sprintf( 'COM_MTREE_NEW_LISTING_APPROVED_MSG', $this->link_name );
					JFactory::getMailer()->sendMail( $MailFrom, $FromName, $email, $subject, $body );

				}

			}

			// Fire finder plugin
			$dispatcher->trigger('onContentAfterSave', array('com_mtree.listing', new JObject(array('link_id' => $lid)), false));
			
			//$this->store(); //Using this breaks Approve & Publish

		}

	}
	
	/***
	* Copy Listing
	*/
	function copyLink( $listing, $new_cat_parent, $reset_hits, $reset_rating, $copy_reviews, $copy_secondary_cats) {
		global $mtconf;

		// Get original listing info
		$this->_db->setQuery( "SELECT * FROM #__mt_links WHERE link_id = " . $this->_db->quote($listing) );
		$org_listing = $this->_db->loadObject();

		// Change cat_id
		$org_listing->cat_id = $new_cat_parent;

		if ( $reset_hits == 1 ) {
			$org_listing->link_hits = 0;
		}

		if ( $reset_rating == 1 ) {
			$org_listing->link_rating = 0;
			$org_listing->link_votes = 0;
		}

		# Copy listing
		$this->_db->setQuery( 'INSERT INTO #__mt_links 
			( 
				link_name, 
				alias,
				link_desc, 
				user_id, 
				link_hits, 
				link_votes, 
				link_rating, 
				link_featured, 
				link_published, 
				link_approved, 
				link_template, 
				attribs, 
				metakey, 
				metadesc, 
				ordering, 
				link_created, 
				publish_up,
				publish_down,
				link_modified, 
				address, 
				city, 
				state, 
				country, 
				postcode, 
				telephone, 
				fax, 
				email, 
				website,
				price,
				lat,
				lng,
				zoom
			) '
			. 'VALUES( '
				. $this->_db->quote($org_listing->link_name) . ', '
				. $this->_db->quote($org_listing->alias) . ', '
				. $this->_db->quote($org_listing->link_desc) . ', '
				. $this->_db->quote($org_listing->user_id) . ', '
				. $this->_db->quote($org_listing->link_hits) . ', '
				. $this->_db->quote($org_listing->link_votes) . ', '
				. $this->_db->quote($org_listing->link_rating) . ', '
				. $this->_db->quote($org_listing->link_featured) . ', '
				. $this->_db->quote($org_listing->link_published) . ', '
				. $this->_db->quote($org_listing->link_approved) . ', '
				. $this->_db->quote($org_listing->link_template) . ', '
				. $this->_db->quote($org_listing->attribs) . ', '
				. $this->_db->quote($org_listing->metakey) . ', '
				. $this->_db->quote($org_listing->metadesc) . ', '
				. $this->_db->quote($org_listing->ordering) . ', '
				. $this->_db->quote($org_listing->link_created) . ', '
				. $this->_db->quote($org_listing->publish_up) . ', '
				. $this->_db->quote($org_listing->publish_down) . ', '
				. $this->_db->quote($org_listing->link_modified) . ', '
				. $this->_db->quote($org_listing->address) .  ', '
				. $this->_db->quote($org_listing->city) . ', '
				. $this->_db->quote($org_listing->state) . ', '
				. $this->_db->quote($org_listing->country) . ', '
				. $this->_db->quote($org_listing->postcode) . ', '
				. $this->_db->quote($org_listing->telephone) . ', '
				. $this->_db->quote($org_listing->fax) . ', '
				. $this->_db->quote($org_listing->email) . ', '
				. $this->_db->quote($org_listing->website) . ', '
				. $this->_db->quote($org_listing->price) . ', '
				. $this->_db->quote($org_listing->lat) . ', '
				. $this->_db->quote($org_listing->lng) . ', '
				. $this->_db->quote($org_listing->zoom)
				. ')'
				);
		$this->_db->execute();

		$new_listing_id = $this->_db->insertid();
		
		# Copy listing's custom fields' value
		$this->_db->setQuery( 'INSERT INTO #__mt_cfvalues (`cf_id`,`link_id`,`value`,`attachment`) '
			. 'SELECT cf_id, ' . $new_listing_id . ', value, attachment FROM #__mt_cfvalues WHERE link_id = ' . $listing);
		$this->_db->execute();
		
		# Copy listing's custom fields' attachment
		$this->_db->setQuery( "SELECT * FROM #__mt_cfvalues_att WHERE link_id = '" . $listing . "'" );
		$listing_atts = $this->_db->loadObjectList();

		foreach($listing_atts AS $listing_att) {
			$file_extension = pathinfo($listing_att->raw_filename);
			$file_extension = strtolower($file_extension['extension']);
			
			$this->_db->setQuery( 
				'INSERT INTO #__mt_cfvalues_att (`link_id`,`cf_id`,`raw_filename`,`filename`,`filesize`,`extension`) '
				. 'VALUES (' . $new_listing_id . ', ' . $this->_db->Quote($listing_att->cf_id). ', ' . $this->_db->Quote($listing_att->raw_filename). ', ' . $this->_db->Quote($listing_att->filename). ', ' . $this->_db->Quote($listing_att->filesize). ', ' . $this->_db->Quote($listing_att->extension). ')' );
			$this->_db->execute();
			$att_id = $this->_db->insertid();
			
			$this->_db->setQuery( 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $this->_db->Quote($att_id . '.' . $file_extension) . ' WHERE att_id = ' . $this->_db->Quote($att_id) . ' LIMIT 1' );
			$this->_db->execute();
			
			copy( 
				$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $listing_att->raw_filename,
				$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $att_id . "." . $file_extension 
			);
		}

		# Copy listing's images
		$this->_db->setQuery( "SELECT filename, ordering FROM #__mt_images WHERE link_id = '" . $listing . "'" );
		$listing_images = $this->_db->loadObjectList();
		if(
			count($listing_images) 
			&& is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_small_image')) 
			&& is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_medium_image')) 
			&& is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_original_image')) 
		) {
			foreach($listing_images AS $listing_image) {
				$file_extension = pathinfo($listing_image->filename);
				$file_extension = strtolower($file_extension['extension']);
				$this->_db->setQuery( 'INSERT INTO #__mt_images (`link_id`,`filename`,`ordering`) '
					. "VALUES (" . $new_listing_id . ", '" . $listing_image->filename . "', '" . $listing_image->ordering . "')");
				$this->_db->execute();
				$img_id = $this->_db->insertid();
				copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $img_id . "." . $file_extension );
				copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $img_id . "." . $file_extension );
				copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $listing_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $img_id . "." . $file_extension );
				$this->_db->setQuery( "UPDATE #__mt_images SET filename = '" . $img_id . "." . $file_extension . "' WHERE img_id = '".$img_id."' LIMIT 1" );
				$this->_db->execute();
			}
		}

		# Create CL mapping
		$this->_db->setQuery( "INSERT INTO #__mt_cl ( link_id, cat_id, main )  VALUES( '".$new_listing_id."', '".$org_listing->cat_id."', '1' )" );
		$this->_db->execute();
		if($copy_secondary_cats == 1) {
			$this->_db->setQuery( 'INSERT INTO #__mt_cl (`link_id`,`cat_id`,`main`) '
				. 'SELECT '.$new_listing_id.', cat_id, main FROM #__mt_cl WHERE link_id = '.$listing.' AND main = 0 ' );
			$this->_db->execute();
		}

		# Copy Reviews
		$this->_db->setQuery( "SELECT rev_id FROM #__mt_reviews WHERE link_id = '".$listing."'" );
		$reviews = $this->_db->loadColumn();

		if ( count($reviews) > 0 && $copy_reviews == 1 ) {
			foreach( $reviews AS $review ) {
				// Get original review
				$this->_db->setQuery( "SELECT * FROM #__mt_reviews WHERE rev_id = '".$review."'" );
				$org_review = $this->_db->loadObject();

				// Change link_id
				$org_review->link_id = $new_listing_id;

				// Copy Review
				$this->_db->setQuery( "INSERT INTO #__mt_reviews ( link_id, user_id, guest_name, rev_title, rev_text, rev_date, rev_approved, admin_note, vote_helpful, vote_total ) VALUES ( '$org_review->link_id', '$org_review->user_id', '".mysql_escape_string($org_review->guest_name)."', '".mysql_escape_string($org_review->rev_title)."', '".mysql_escape_string($org_review->rev_text)."', '$org_review->rev_date', '$org_review->rev_approved', '$org_review->admin_note', '$org_review->vote_helpful', '$org_review->vote_total' )" );
				$this->_db->execute();

			}
		}

	}
	
	/***
	* getLinkModified
	*
	* Return the link_modified data based on whether the trigger_modified_listing is set and the 
	* required fields are updated. If no required fields are configured, the current date is 
	* returned. Otherwise, it will check against all submitted data with the original data. If
	* there are changes, the current date is returned. If no modificatin is done to the required 
	* field, null is returned.
	*/
	function getLinkModified( $original_link_id, $formpost ) {
		global $mtconf;
		
		$jdate		= JFactory::getDate();
		$now		= $jdate->toSql();
		
		$postdata_cf = array();
		foreach( $formpost AS $k => $v ) {
			if ( substr($k,0,2) == "cf" ) {
				$postdata_cf[$k] = $v;
			}
		}
		
		// if trigger_modified_listing is not empty, loop all fields from in the listing (core & custom) to find updated fields
		$trigger_modified_listing = $mtconf->get( 'trigger_modified_listing' );
		if( !empty($trigger_modified_listing) ) {
			$array_trigger_fields = explode(',',$mtconf->get( 'trigger_modified_listing' ));
			JArrayHelper::toInteger($array_trigger_fields, array());
			
			// $originalRow = new mtLinks( $this->_db );
			// $originalRow->load( $original_link_id );
			$this->_db->setQuery( 'SELECT * FROM #__mt_links WHERE link_id = ' . $original_link_id . ' LIMIT 1');
			$originalRow = $this->_db->loadObject();
			
			$this->_db->setQuery( 'SELECT cf_id, value FROM #__mt_cfvalues WHERE link_id = ' . $original_link_id . ' AND cf_id IN (\'' . implode('\',\'',$array_trigger_fields) . '\')');
			$cfvalues = $this->_db->loadObjectList('cf_id');

			foreach( $cfvalues AS $cfvalue_k => $cfvalue_v ) {
				$originalRow->$cfvalue_k = $cfvalue_v->value;
			}
			
			$postdata = get_class_vars(get_class($this));
			$postdata = array_merge($postdata, $postdata_cf);
			foreach( $postdata AS $k => $v ) {
				if ( substr($k,0,2) == "cf" ) {
					if(is_array($v)) {
						$v = implode('|',$v);
					}
				} else {
					$v = $this->$k;
				}
				if ( substr($k,0,2) == "cf" ) {
					if( in_array( substr($k,2), $array_trigger_fields ) ) {
						// Check if this custom field value has changed?
						if( isset($originalRow->{substr($k,2)}) ) {
							if( $v == $originalRow->{substr($k,2)} ) {
								// No change
							} else {
								return $now;
							}
						}
						// If changed, update link_modified
					}
				} elseif( in_array($k, $array_trigger_fields) && substr($k,0,1) <> '_' ) {
					// Check if this core field value has changed?
					if( $v == $originalRow->$k ) {
						// No change
					} else {
						return $now;
					}
				}
			}
			return $originalRow->link_modified;
		} else {
			return $now;
		}
	}

	/***
	*	Return template in use. If listing is not assigned with template, 
	* it will look for it in its parent category. Otherwise the function
	* return false
	*/
	function getTemplate() {
		
		if ( $this->link_template <> '' ) {
		
			return $this->link_template;

		} else {
			
			$cat = new mtCats( $this->_db );
			$cat->load( $this->cat_id );
			
			if ( $cat->cat_template <> '' ) {
				return $cat->cat_template;
			} else {
				return false;
			}

		}

	}

	function getCatID() {
		$this->_db->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = $this->link_id AND main = 1 LIMIT 1" );
		return $this->_db->loadResult();
	}

	function getCatName() {

		$this->_db->setQuery( "SELECT c.cat_id, cat_name FROM #__mt_cl AS cl, #__mt_cats AS c WHERE cl.cat_id = c.cat_id AND link_id = $this->link_id AND main = 1 LIMIT 1" );
		$cat = $this->_db->loadObject();

		if ( !isset($cat->cat_id) || $cat->cat_id == 0 ) {
			return JText::_( 'COM_MTREE_ROOT' );
		} else {

			return $cat->cat_name;

		}
	}

}

/***
* Custom Fields
*/
class mtCustomFields extends JTable {
	var $cf_id=null;
	var $field_type=null;
	var $caption=null;
	var $alias=null;
	var $default_value=null;
	var $size=null;
	var $columns=null;
	var $field_elements=null;
	var $prefix_text_mod=null;
	var $suffix_text_mod=null;
	var $prefix_text_display=null;
	var $suffix_text_display=null;
	var $placeholder_text=null;
	var $cat_id=null;
	var $ordering=null;
	var $hidden=null;
	var $required_field=null;
	var $published=null;
	var $hide_caption=null;
	var $advanced_search=null;
	var $simple_search=null;
	var $tag_search=null;
	var $filter_search=null;
	var $details_view=null;
	var $summary_view=null;
	var $search_caption=null;
	var $params=null;
	var $iscore=null;
	
	function __construct( &$_db ) {
		parent::__construct( '#__mt_customfields', 'cf_id', $_db );
	}

}

/**
* Reviews Table class
*/
class mtReviews extends JTable {
	var $rev_id=null;
	var $link_id=null;
	var $user_id=null;
	var $guest_name=null;
	var $rev_title=null;
	var $rev_text=null;
	var $rev_date=null;
	var $rev_approved=null;
	var $admin_note=null;
	var $vote_helpful=null;
	var $vote_total=null;
	var $ownersreply_text=null;
	var $ownersreply_date=null;
	var $ownersreply_approved=null;
	var $ownersreply_admin_note=null;

	function __construct( &$_db ) {
		parent::__construct( '#__mt_reviews', 'rev_id', $_db );
	}

	/***
	* Approve Review
	*/
	function approveReview( $approve=1, $rev_id=0 ) {
		global $mtconf;

		$app		= JFactory::getApplication('site');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

		# Determine which Review ID to use. If none, return false.
		if ( $this->rev_id > 0 ) {
			$rid = (int) $this->rev_id;
		} elseif ( $rev_id > 0) {
			$rid = (int) $rev_id;
		} else {
			return false;
		}

		$this->_db->setQuery("UPDATE #__mt_reviews SET rev_approved = '" . intval($approve) . "' WHERE rev_id = '".$rid."'");
	
		if (!$this->_db->execute()) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			return false;
		} else {
	
			$this->_db->setQuery("UPDATE #__mt_reviews SET admin_note = '' WHERE rev_id = '".$rid."'");
			$this->_db->execute();
			
			if( $approve == 1 )
			{
				// Send approval notification to reviewer
				if ( $mtconf->get('notifyuser_review_approved') == 1 ) {

					$this->_db->setQuery( 
						"SELECT u.email, l.link_name, l.link_id FROM #__mt_reviews AS r"
						.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
						.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
						.	"\nWHERE r.rev_id = '".$rid."' LIMIT 1"
					);
					$row = $this->_db->loadObject();

					$link_url = getMTUrl( 'index.php?option=com_mtree&task=viewlink&link_id=' . $row->link_id );

					if ( $row->email <> '' ) {
						$subject = JText::_( 'COM_MTREE_REVIEW_APPROVED_SUBJECT' );
						$body = JText::sprintf( 'COM_MTREE_REVIEW_APPROVED_MSG', $row->link_name, $link_url );
						JFactory::getMailer()->sendMail( $MailFrom, $FromName, $row->email, $subject, $body );
					}
				}

				// Send notification to listing owner
				if ( $mtconf->get('notifyowner_review_added') == 1 ) {
					// Notification can only be sent when this listing is owned by a registered user
					$this->_db->setQuery(
						'SELECT u.email, l.link_name, l.link_id FROM #__mt_reviews AS r'
						. ' LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id'
						. ' LEFT JOIN #__users AS u ON u.id = l.user_id'
						. ' WHERE r.rev_id = ' . $rid . ' LIMIT 1'
						);
					$row = $this->_db->loadObject();

					$link_url = getMTUrl( 'index.php?option=com_mtree&task=viewlink&link_id=' . $row->link_id );

					if ( $row->email <> '' ) {
						$subject = JText::sprintf( 'COM_MTREE_REVIEW_ADDED_SUBJECT', $row->link_name );
 						$body = JText::sprintf( 'COM_MTREE_REVIEW_ADDED_MSG', $row->link_name, $link_url );
						JFactory::getMailer()->sendMail( $MailFrom, $FromName, $row->email, $subject, $body );
					}
				}
			}

			return true;
		}

	}

}

/**
* Votes Table class
*/
/*
class mtVotes extends JTable {
	var $vote_ip=null;
	var $user_id=null;
	var $vote_date=null;
	var $link_id=null;

	function mtVotes( &$db ) {
		$this->JTable( '#__mt_votes', 'vote_ip', $db );
	}
}
*/

/**
* Related Categories Table class
*/
class mtRelCats extends JTable {
	var $cat_id=null;
	var $rel_id=null;

	function __construct( &$_db ) {
		parent::__construct( '#__mt_relcats', 'cat_id', $_db );
	}
	
	function setcatid( $cat_id ) {
		$this->cat_id = $cat_id;
	}

	/**
	* Updates the related categories
	* @param array Source array for related categories
	* @return boolean TRUE if update is successful, FALSE if not.
	*/
	function update( $related_cats ) {

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE cat_id = '$this->cat_id'" );
		$this->_db->execute();
		
		if ( !empty($related_cats) ) {

			$sql = "INSERT INTO $this->_tbl (cat_id, rel_id) VALUES ";
			
			foreach( $related_cats AS $related_cat ) {
				$values[] = "($this->cat_id, $related_cat)";
			}
			
			$sql .= implode(', ', $values);
			$this->_db->setQuery( $sql );
			if (!$this->_db->execute()) {
				return false;
			} else {
				return true;
			}
			
		}
	} // End function

}

/**
* Related Categories Table class
*/
class mtClaims extends JTable {
	var $claim_id=null;
	var $user_id=null;
	var $link_id=null;
	var $comment=null;
	var $admin_note=null;
}

class mtReports extends JTable {
	var $report_id=null;
	var $user_id=null;
	var $guest_name=null;
	var $link_id=null;
	var $subject=null;
	var $comment=null;
	var $report_date=null;
	var $admin_note=null;
}

/**
* Images Table
*/
class mtImages extends JTable {
	var $img_id=null;
	var $link_id=null;
	var $filename=null;
	var $ordering=null;

	function __construct( &$_db ) {
		parent::__construct( '#__mt_images', 'img_id', $_db );
	}
}

/**
* Image class
*/

class mtImage {

	var $tmpFile=null;
	var $imageName=null;
	var $outputType=null;
	var $type=null;
	var $size=null;
	var $directory=null;
	var $method=null;
	var $quality=80;
	var $errorMsg=null;
	var $square=false;

	//function mtImage( $src_path, $imageName, $dest_dir ) {
	function mtImage( $listing_image=null, $dest_dir=null ) {
		if( !is_null($listing_image) ) {
			$this->tmpFile = $listing_image['tmp_name'];
			$this->imageName = $listing_image['name'];
			$this->type = $listing_image['type'];
			$this->size = $listing_image['size'];
		}
		if(!is_null($dest_dir)) $this->directory = $dest_dir;
	}
	
	function setTmpFile( $tmpFile ) {
		$this->tmpFile = $tmpFile;
	}

	function setType( $type ) {
		$this->type = $type;
	}

	function setDirectory( $directory ) {
		$this->directory = $directory;
	}

	function setName( $imageName ) {
		$this->imageName = $imageName;
	}

	function setMethod( $method ) {
		$this->method = $method;
	}

	function setQuality( $quality ) {
		if ( is_numeric($quality) && $quality >= 0 && $quality <= 100 ) {
			$this->quality = $quality;
		}
	}
	
	/**
	 * Set the type of image to output upon saving. By default it outputs
	 * to the source image type.
	 */
	function setOutputType($type) {
		$type = strtolower($type);
		if( in_array($type,array('gif','png','jpg')) ) {
			$this->outputType = $type;
		}
	}
	
	function setSize( $size ) {
		$this->size = $size;
	}

	function setSquare( $bool=false ) {
		$this->square = $bool;
	}
	
	function setErrorMsg( $errorMsg ) {
		$this->errorMsg = $errorMsg;
	}

	function getOutputType() {
		if( !is_null($this->outputType) ) {
			return $this->outputType;
		} else {
			return '';
		}
	}

	function getErrorMsg() {
		return $this->errorMsg;
	}

	function check() {

		# Is destination directory writable?
		if( !is_writable($this->directory) ) { 
			$this->setErrorMsg(JText::sprintf( 'COM_MTREE_IMAGE_DIR_NOT_WRITABLE', $this->directory ));
			return false;
		}

		# Is there a specified source path?
		if ( $this->tmpFile == '' ) {
			$this->setErrorMsg(JText::_( 'COM_MTREE_IMAGE_NOT_SPECIFIED' ));
			return false;
		}

		# Uploaded ?
		if ( !is_uploaded_file($this->tmpFile) ) {
			$this->setErrorMsg(JText::_( 'COM_MTREE_IMAGE_NOT_VALID' ));
			return false;
		} 

		# No duplicate
		if ( file_exists($this->directory.$this->imageName) ) {
			$this->setErrorMsg(JText::_( 'COM_MTREE_DUPLICATE_IMAGE' ));
			return false;
		}
		
		return true;

	}

	function getImageData() {
		return $this->imageData;
	}
	
	function getimagesize_remote($image_url) {
	    $handle = fopen ($image_url, "rb");
	    $contents = '';
	    if ($handle) {
	    do {
	        $count += 1;
	        $data = fread($handle, 8192);
	        if (strlen($data) == 0) {
	            break;
	       }
	    $contents .= $data;
	    } while(true);
	    } else { return false; }
	    fclose ($handle);

	    $im = ImageCreateFromString($contents);
	    if (!$im) { return false; }
	    $gis[0] = ImageSX($im);
	    $gis[1] = ImageSY($im);
	    $gis[3] = "width={$gis[0]} height={$gis[1]}";
	    ImageDestroy($im);
	    return $gis;
	}
	
	function saveToDirectory() {
		if($fp = fopen($this->directory . $this->imageName,'w')) {
			if(fwrite($fp,$this->imageData) === false) {
				echo '<br />Unable to write to file: ' . $this->directory . $this->imageName;
			}
		} else {
			echo '<br />Unable to open for writing: ' . $this->directory . $this->imageName;
		}
	}
	
	function resize()	{
		global $mtconf;

		$imagetype = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
		
		if(substr($this->tmpFile,0,7) == 'http://') {
			$imginfo = getimagesize_remote($this->tmpFile);
		} else {
			$imginfo = getimagesize($this->tmpFile);
		}
		$imginfo[2] = $imagetype[$imginfo[2]];

		# GD can only handle GIF, JPG & PNG images
		if ($imginfo[2] != 'JPG' && $imginfo[2] != 'PNG' && $imginfo[2] != 'GIF' && ($this->method == 'gd1' || $this->method == 'gd2')) {
			return false;
		}

		# height/width
		$srcWidth = $imginfo[0];
		$srcHeight = $imginfo[1];

		# Generate new width/height
		$ratio = max($srcWidth, $srcHeight) / $this->size;
		$ratio = max($ratio, 1.0);
		$destWidth = (int)($srcWidth / $ratio);
		$destHeight = (int)($srcHeight / $ratio);

		$offWidth = 0;
		$offHeight = 0;
		if($this->square && $srcWidth > $this->size && $srcHeight > $this->size) {
			if($srcWidth > $srcHeight) {
				$offWidth = ($srcWidth - $srcHeight) / 2;
				$offHeight = 0;
				$srcWidth = $srcHeight;
				$destHeight = $destWidth;
			} elseif($srcHeight > $srcWidth) {
				$offWidth = 0;
				$offHeight = ($srcHeight - $srcWidth) / 2;
				$srcHeight = $srcWidth;
				$destWidth = $destHeight;
			}
		}

		# Method for thumbnails creation
		switch ($this->method) {

			case "gd1" :
				if (!function_exists('imagecreatefromjpeg')) {
						die('GD image library not installed!');
				}
				if ($imginfo[2] == 'JPG')
					$src_img = imagecreatefromjpeg($this->tmpFile);
				else
					$src_img = imagecreatefrompng($this->tmpFile);
				if (!$src_img){
					$ERROR = $lang_errors['invalid_image'];
					return false;
				}
				$dst_img = imagecreate($destWidth, $destHeight);
				imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
				ob_start();
				imagejpeg($dst_img, null, $this->quality);
				$this->imageData = ob_get_contents();
				ob_end_clean();
				imagedestroy($src_img);
				imagedestroy($dst_img);
				break;

			case "gd2" :
				if (!function_exists('imagecreatefromjpeg')) {
					return false;
					// die('GD image library not installed!');
				}
				if (!function_exists('imagecreatetruecolor')) {
					return false;
					// die('GD2 image library does not support truecolor thumbnailing!');
				}

				switch($imginfo[2]) {
					case 'JPG':
						$src_img = imagecreatefromjpeg($this->tmpFile);
						$dst_img = imagecreatetruecolor($destWidth, $destHeight);
						break;

					case 'PNG':
						$src_img = imagecreatefrompng($this->tmpFile);
						$dst_img = $this->imagecreatetruecolortransparent($destWidth, $destHeight);
						break;
					
					case 'GIF':
						$src_img = imagecreatefromgif($this->tmpFile);
						$dst_img = imagecreatetruecolor($destWidth, $destHeight);
						$colorTransparent = imagecolortransparent($src_img);
						imagepalettecopy($src_img, $dst_img);
						imagefill($dst_img, 0, 0, $colorTransparent);
						imagecolortransparent($dst_img, $colorTransparent);
						imagetruecolortopalette($dst_img, true, 256);
						break;
				}
				
				imagecopyresampled($dst_img, $src_img, 0, 0, $offWidth, $offHeight, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);

				ob_start();
				if( is_null($this->outputType) )
				{
					$outputType = strtolower($imginfo[2]);
				} else {
					$outputType = $this->outputType;
				}
				switch($outputType) {
					case 'gif':
						imagegif($dst_img);
						break;
					case 'png':
						imagepng($dst_img);
						break;
					case 'jpg':
					default:
						imagejpeg($dst_img, null, $this->quality);
						break;
				}
				$this->imageData = ob_get_contents();
				ob_end_clean();
				
				imagedestroy($src_img);
				imagedestroy($dst_img);
				break;
			case "netpbm":
				if($mtconf->get('img_netpbmpath')) { 
					if(!is_dir($mtconf->get('img_netpbmpath')))	{ 
							echo "NetPbm path incorrect";
							die; 
					} 
				} 
				
				if ($imginfo[2] == 'PNG') { 
					$cmd = $mtconf->get('img_netpbmpath') . "pngtopnm $this->tmpFile | " . $mtconf->get('img_netpbmpath') . "pnmscale -xysize $destWidth ".(int)$destHeight." | " . $mtconf->get('img_netpbmpath') . "pnmtopng > " . JPATH_ROOT.'/media/'.$this->imageName; 
				}	else if ($imginfo[2] == 'JPG')	{ 
					$cmd = $mtconf->get('img_netpbmpath') . "jpegtopnm $this->tmpFile | " . $mtconf->get('img_netpbmpath') . "pnmscale -xysize $destWidth ".(int)$destHeight." | " . $mtconf->get('img_netpbmpath') . "pnmtojpeg -quality=$this->quality > ".JPATH_ROOT.'/media/'.$this->imageName;
				}	else if ($imginfo[2] == 'GIF') { 
					$cmd = $mtconf->get('img_netpbmpath') . "giftopnm $this->tmpFile | " . $mtconf->get('img_netpbmpath') . "pnmscale -xysize $destWidth ".(int)$destHeight." | " . $mtconf->get('img_netpbmpath') . "ppmquant 256 | " . $mtconf->get('img_netpbmpath') . "ppmtogif > " . JPATH_ROOT.'/media/'.$this->imageName; 
				}
				exec($cmd);
				break;
			case "imagemagick":

				$tmp_name = substr(strrchr($this->directory.$this->imageName, "/"), 1);
				copy($this->tmpFile, JPATH_ROOT.'/media/'.$tmp_name);
				$uploadfile = JPATH_ROOT.'/media/'.$tmp_name;
				$cmd = $mtconf->get('img_impath')."convert -resize ".$destWidth."x".(int)$destHeight." $uploadfile ".JPATH_ROOT.'/media/'.$this->imageName;
				exec($cmd);
				unlink($uploadfile);

				break;
		}
		
		if( $this->method == 'netpbm' || $this->method == 'imagemagick' ) {
			$filename = JPATH_ROOT.'/media/'.$this->imageName;
			$handle = fopen($filename, "r");
			$this->imageData = fread($handle, filesize($filename));
			fclose($handle);
			unlink(JPATH_ROOT.'/media/'.$this->imageName);
		}

		return true;
	}
	
	function imagecreatetruecolortransparent($x,$y) {
		$i = imagecreatetruecolor($x,$y);
		$b = imagecreatefromstring(base64_decode($this->blankpng()));
		imagealphablending($i,false);
		imagesavealpha($i,true);
		imagecopyresized($i,$b,0,0,0,0,$x,$y,imagesx($b),imagesy($b));
		return $i;
	}

	function blankpng() {
		$c  = "iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29m";
		$c .= "dHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCg";
		$c .= "dyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAAN";
		$c .= "egcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQ";
		$c .= "oHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAA";
		$c .= "DXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII=";
		return $c;
	}
	
}

/***
* Mosets Tree Display Class
*/

class mtDisplay {

	var $captions=null;
	var $data=null;

	function mtDisplay() {
		// Empty Constructor
	}

	function add( $caption, $data ) {
		$this->captions[] = $caption;
		$this->data[] = $data;
	}

	function display() {
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="0" style="border: 1px solid #C0C0C0">
		<?php
		for( $i=0; $i<count($this->captions); $i++ ) {

			if ( $this->captions[$i] == JText::_( 'COM_MTREE_NAME' ) ) {
				echo "<tr>";
				echo '<td colspan="2" align="center">' . $this->data[$i] . "</td>";
				echo "</tr>";
			} else {
				echo "<tr>";
				echo '<td width="50%" align="right" style="border-top: 1px solid #C0C0C0">' . $this->captions[$i] . ': </td>';
				echo '<td width="50%" style="background-color: white;border-top: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">&nbsp;&nbsp;' . $this->data[$i] . "</td>";
				echo "</tr>";
			}
		}
		?>
		</table>
		<?php
	}

}
?>