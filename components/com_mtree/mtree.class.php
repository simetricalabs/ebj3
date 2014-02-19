<?php
/**
 * @version		$Id: mtree.class.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mtree {

	function mtree() {
		$this->_config = new stdClass();

		$this->_config->mt_template = $mt_template;
		$this->_config->mt_map = $mt_map;
		$this->_config->mt_show_map = $mt_show_map;
		$this->_config->mt_show_print = $mt_show_print;
		$this->_config->mt_show_recommend = $mt_show_recommend;
		$this->_config->mt_show_rating = $mt_show_rating;
		$this->_config->mt_show_review = $mt_show_review;
		$this->_config->mt_show_visit = $mt_show_visit;
		$this->_config->mt_show_contact = $mt_show_contact;
		$this->_config->mt_use_owner_email = $mt_use_owner_email;
		$this->_config->mt_show_report = $mt_show_report;
		$this->_config->mt_show_claim = $mt_show_claim;
		$this->_config->mt_show_ownerlisting = $mt_show_ownerlisting;
		$this->_config->mt_show_email = $mt_show_email;
		$this->_config->mt_fe_num_of_subcats = $mt_fe_num_of_subcats;
		$this->_config->mt_fe_num_of_links = $mt_fe_num_of_links;
		$this->_config->mt_fe_num_of_reviews = $mt_fe_num_of_reviews;
		$this->_config->mt_fe_num_of_popular = $mt_fe_num_of_popular;
		$this->_config->mt_fe_num_of_new = $mt_fe_num_of_new;
		$this->_config->mt_fe_total_new = $mt_fe_total_new;
		$this->_config->mt_fe_num_of_updated = $mt_fe_num_of_updated;
		$this->_config->mt_fe_num_of_mostrated = $mt_fe_num_of_mostrated;
		$this->_config->mt_fe_num_of_toprated = $mt_fe_num_of_toprated;
		$this->_config->mt_fe_num_of_mostreview = $mt_fe_num_of_mostreview;
		$this->_config->mt_fe_num_of_favourite = $mt_fe_num_of_favourite;
		$this->_config->mt_rate_once = $mt_rate_once;
		$this->_config->mt_min_votes_for_toprated= $mt_min_votes_for_toprated;
		$this->_config->mt_min_votes_to_show_rating= $mt_min_votes_to_show_rating;
		$this->_config->mt_user_review_once = $mt_user_review_once;
		$this->_config->mt_user_vote_review = $mt_user_vote_review;
		$this->_config->mt_owner_reply_review = $mt_owner_reply_review;
	
		$this->_config->mt_show_report_review = $mt_show_report_review;		
		$this->_config->mt_user_rating = $mt_user_rating;
		$this->_config->mt_user_review = $mt_user_review;
		$this->_config->mt_user_report = $mt_user_report;
		$this->_config->mt_user_recommend = $mt_user_recommend;
		$this->_config->mt_user_addlisting = $mt_user_addlisting;
		$this->_config->mt_user_addcategory = $mt_user_addcategory;
		$this->_config->mt_user_allowmodify = $mt_user_allowmodify;
		$this->_config->mt_user_allowdelete = $mt_user_allowdelete;
		$this->_config->mt_needapproval_addlisting = $mt_needapproval_addlisting;
		$this->_config->mt_needapproval_modifylisting = $mt_needapproval_modifylisting;
		$this->_config->mt_needapproval_addcategory = $mt_needapproval_addcategory;
		$this->_config->mt_needapproval_addreview = $mt_needapproval_addreview;
		$this->_config->mt_needapproval_replyreview = $mt_needapproval_replyreview;

		$this->_config->mt_link_new = $mt_link_new;
		$this->_config->mt_link_popular = $mt_link_popular;
		$this->_config->mt_hit_lag = $mt_hit_lag;
		$this->_config->mt_notifyuser_newlisting = $mt_notifyuser_newlisting;
		$this->_config->mt_notifyadmin_newlisting = $mt_notifyadmin_newlisting;
		$this->_config->mt_notifyuser_modifylisting= $mt_notifyuser_modifylisting;
		$this->_config->mt_notifyadmin_modifylisting = $mt_notifyadmin_modifylisting;
		$this->_config->mt_notifyuser_approved = $mt_notifyuser_approved;
		$this->_config->mt_notifyuser_review_approved = $mt_notifyuser_review_approved;
		$this->_config->mt_notifyadmin_delete = $mt_notifyadmin_delete;
		$this->_config->mt_use_internal_notes = $mt_use_internal_notes;
		// $this->_config->mt_allow_html = $mt_use_internal_notes;
		$this->_config->mt_search_link_name = $mt_search_link_name;
		$this->_config->mt_search_link_desc = $mt_search_link_desc;
		$this->_config->mt_search_address = $mt_search_address;
		$this->_config->mt_search_city = $mt_search_city;
		$this->_config->mt_search_postcode = $mt_search_postcode;
		$this->_config->mt_search_state = $mt_search_state;
		$this->_config->mt_search_country = $mt_search_country;
		$this->_config->mt_search_email = $mt_search_email;
		$this->_config->mt_search_website = $mt_search_website;
		$this->_config->mt_search_telephone = $mt_search_telephone;
		$this->_config->mt_search_fax = $mt_search_fax;

		$this->_config->mt_display_empty_cat = $mt_display_empty_cat;
		$this->_config->mt_display_alpha_index = $mt_display_alpha_index;
		$this->_config->mt_allow_listings_submission_in_root = $mt_allow_listings_submission_in_root;
		$this->_config->mt_display_listings_in_root = $mt_display_listings_in_root;
		$this->_config->mt_display_cat_count_in_root = $mt_display_cat_count_in_root;
		$this->_config->mt_display_listing_count_in_root = $mt_display_listing_count_in_root;
		$this->_config->mt_display_cat_count_in_subcat = $mt_display_cat_count_in_subcat;
		$this->_config->mt_display_listing_count_in_subcat = $mt_display_listing_count_in_subcat;

	}

	function getCfg( $varname ) {
		if (isset( $this->_config->$varname )) {
			return $this->_config->$varname;
		} else {
			return null;
		}
	}

}

class mtLog {
	/** @var string Current log date */
	var $now=null;
	/** @var database Internal database class pointer */
	var $db=null;
	/** @var IP Internet Protocol address of the user */
	var $ip=null;
	/** @var Integer User ID */
	var $user_id=null;
	/** @var Integer Link ID */
	var $link_id=null;
	/** @var Integer Review ID */
	var $rev_id=null;

	function mtLog( $database, $ip='', $user_id=0, $link_id=0, $rev_id=0 ) {
		$jdate			= JFactory::getDate();
		$this->db		= $database;
		$this->now		= $jdate->toSql();
		$this->ip		= $ip;
		$this->user_id	= $user_id;
		$this->link_id	= $link_id;
		$this->rev_id	= $rev_id;
	}

	function setUserID( $user_id ) {
		$this->user_id=$user_id;
	}

	function getUserID() {
		if( $this->user_id > 0 ) return $this->user_id;
		else return 0;
	}

	function setLinkID( $link_id ) {
		$this->link_id=$link_id;
	}

	function getLinkID() {
		if( $this->link_id > 0 ) return $this->link_id;
		else return 0;
	}

	function setRevID( $rev_id ) {
		$this->rev_id=$rev_id;
	}

	function getRevID() {
		if( $this->rev_id > 0 ) return $this->rev_id;
		else return 0;
	}

	function setIP( $ip ) {
		$this->ip=$ip;
	}

	function getIP() {
		if( !empty($this->ip) ) return $this->ip;
		else return '';
	}

	function getUserLastRating() {
		if(empty($this->user_id)) {
			$this->db->setQuery( 'SELECT value FROM #__mt_log WHERE link_id = ' . $this->db->quote($this->link_id) . ' AND log_ip = ' . $this->db->quote($this->ip) . ' AND log_type = \'vote\' LIMIT 1' );
		} else {
			$this->db->setQuery( 'SELECT value FROM #__mt_log WHERE link_id = ' . $this->db->quote($this->link_id) . ' AND user_id = ' . $this->db->quote($this->user_id) . ' AND log_type = \'vote\' LIMIT 1' );
		}
		$user_rating = $this->db->loadResult();
		if( $user_rating > 0 ) {
			return $user_rating;
		} else {
			return 0;
		}
	}

	function logSaveListingNew() {
		$this->db->setQuery( 'INSERT INTO #__mt_log '
			.	'( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )'
			.	'VALUES ( '
			.	$this->db->quote($this->getIP()) . ', '
			.	'\'submitnewlisting\', '
			.	$this->db->quote($this->getUserID()) . ', '
			.	$this->db->quote($this->now) . ', '
			.	$this->db->quote($this->getLinkID())// . ', '
			.	')' );
		if (!$this->db->execute()) {
			return false;
		} else {
			return true;
		}
	}

	function logSaveListingExisting() {
		$this->db->setQuery( 'INSERT INTO #__mt_log '
			.	'( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )'
			.	'VALUES ( '
			.	$this->db->quote($this->getIP()) . ', '
			.	'\'modifylisting\', '
			.	$this->db->quote($this->getUserID()) . ', '
			.	$this->db->quote($this->now) . ', '
			.	$this->db->quote($this->getLinkID())// . ', '
			.	')' );
		if (!$this->db->execute()) {
			return false;
		} else {
			return true;
		}
	}
		
	function logReview() {
		$this->db->setQuery( 'INSERT INTO #__mt_log '
			.	'( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` , `rev_id` )'
			.	'VALUES ( '
			.	$this->db->quote($this->getIP()) . ', '
			.	'\'review\', '
			.	$this->db->quote($this->getUserID()) . ', '
			.	$this->db->quote($this->now) . ', '
			.	$this->db->quote($this->getLinkID()) . ', '
			.	$this->db->quote($this->getRevID())
			.	')' );
		if (!$this->db->execute()) {
			return false;
		} else {
			return true;
		}
	}

	function logReplyReview() {
		$this->db->setQuery( 'INSERT INTO #__mt_log '
			.	'( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` , `rev_id` )'
			.	'VALUES ( '
			.	$this->db->quote($this->getIP()) . ', '
			.	"'replyreview',"
			.	$this->db->quote($this->getUserID()) . ', '
			.	$this->db->quote($this->now) . ', '
			.	$this->db->quote($this->getLinkID()) . ', '
			.	$this->db->quote($this->getRevID())
			.	')');
		if (!$this->db->execute()) {
			return false;
		} else {
			return true;
		}
	}

	function logVote( $rating ) {
		
		if( $rating <= 0 ) $rating = 0;

		$this->db->setQuery( 'INSERT INTO #__mt_log '
			.	'( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` , `rev_id`, `value` )'
			.	'VALUES ( '
			.	$this->db->quote($this->getIP()) . ', '
			.	"'vote',"
			.	$this->db->quote($this->getUserID()) . ', '
			.	$this->db->quote($this->now) . ', '
			.	$this->db->quote($this->getLinkID()) . ', '
			.	$this->db->quote($this->getRevID()) . ', '
			.	$this->db->quote($rating)
			.	')');
		if (!$this->db->execute()) { return false; } 
		else { return true; }

	}

	function deleteVote() {

		if( $this->getUserID() > 0 && $this->getLinkID() > 0 ) {
			$this->db->setQuery( 'DELETE FROM #__mt_log WHERE user_id = ' . $this->db->quote($this->getUserID()) . ' AND link_id = ' . $this->db->quote($this->getLinkID()) . ' AND log_type = \'vote\'' );
			if (!$this->db->execute()) { return false; } 
			else { return true; }
		}

	}

	function logVisit() {

		if ( $this->user_id == 0 ) {
			$this->db->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$this->link_id."' AND log_ip = " . $this->db->quote($this->ip) . " AND log_type = 'visit'" );
		} else {
			$this->db->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$this->link_id."' AND user_id = '".$this->user_id."' AND log_type = 'visit'" );
		}
		
		$counted = false;
		$counted = ($this->db->loadResult() <> '') ? true : false;
				
		if( !$counted ) {
			$this->db->setQuery( "INSERT INTO #__mt_log "
				.	"( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )"
				.	"VALUES ( "
				.	$this->db->quote($this->getIP()) . ', '
				.	"'visit',"
				.	"'" . $this->getUserID() . "',"
				.	"'" . $this->now . "',"
				.	"'" . $this->getLinkID() . "'"
				.	")");
			if (!$this->db->execute()) {
				return false;
			} else {
				return true;
			}
		}

	}

	function logFav( $action ) {
		
		$this->db->setQuery( "INSERT INTO #__mt_log "
			.	"( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id` )"
			.	"VALUES ( "
			.	$this->db->quote($this->getIP()) . ', '
			.	"'" . ( ($action == 1) ? 'addfav' : 'removefav' ) . "',"
			.	"'" . $this->getUserID() . "',"
			.	"'" . $this->now . "',"
			.	"'" . $this->getLinkID() . "'"
			.	")");
		if (!$this->db->execute()) { return false; } 
		else { return true; }

	}

}

?>
