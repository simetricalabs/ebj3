<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_1 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Overridable_by_category wasn't properly updated in 3.0.0. The following 2 SQL queries 
		// resets and fix this.
		$database->setQuery('UPDATE `#__mt_config` SET `overridable_by_category` = \'1\'');
		$database->execute();
		
		$database->setQuery('UPDATE `#__mt_config` SET `overridable_by_category` = \'0\' WHERE varname IN (\'admin_use_explorer\',\'small_image_click_target_size\',\'explorer_tree_level\',\'hit_lag\',\'linkchecker_last_checked\',\'linkchecker_num_of_links\',\'linkchecker_seconds\',\'link_new\',\'link_popular\',\'name\',\'relative_path_to_js_library\',\'relative_path_to_rating_image\',\'resize_method\',\'resize_quality\',\'use_internal_notes\',\'use_wysiwyg_editor\',\'use_wysiwyg_editor_in_admin\',\'version\',\'major_version\',\'minor_version\',\'dev_version\',\'squared_thumbnail\',\'relative_path_to_cat_small_image\',\'relative_path_to_cat_original_image\',\'relative_path_to_listing_small_image\',\'relative_path_to_listing_medium_image\',\'relative_path_to_listing_original_image\',\'banned_text\',\'banned_subject\',\'banned_email\',\'load_css\',\'reset_created_date_upon_approval\',\'relative_path_to_attachments\',\'sef_link_slug_type\',\'sef_image\',\'sef_gallery\',\'sef_review\',\'sef_replyreview\',\'sef_reportreview\',\'sef_recommend\',\'sef_print\',\'sef_contact\',\'sef_report\',\'sef_claim\',\'sef_visit\',\'sef_category_page\',\'sef_delete\',\'sef_reviews_page\',\'sef_addlisting\',\'sef_editlisting\',\'sef_addcategory\',\'sef_mypage\',\'sef_new\',\'sef_updated\',\'sef_favourite\',\'sef_featured\',\'sef_popular\',\'sef_mostrated\',\'sef_toprated\',\'sef_mostreview\',\'sef_listalpha\',\'sef_listings\',\'sef_favourites\',\'sef_reviews\',\'sef_searchby\',\'sef_search\',\'sef_advsearch\',\'sef_advsearch2\',\'sef_rss\',\'sef_rss_new\',\'sef_rss_updated\',\'sef_space\',\'note_sef_translations\',\'sef_details\',\'relative_path_to_js\',\'relative_path_to_images\',\'relative_path_to_templates\',\'relative_path_to_fieldtypes\',\'relative_path_to_fieldtypes_media\',\'sef_link_slug_type_hybrid_separator\',\'use_open_graph_protocol\',\'sef_viewreviews\',\'sef_viewreview\',\'image_min_width\',\'image_min_height\',\'sef_all\',\'sef_listallcats\',\'owner_default_page\',\'sef_owner\',\'use_captcha_review\',\'use_captcha_contact\',\'use_captcha_report\',\'use_captcha_reportreview\',\'load_js_framework_frontend\',\'banned_attachment_filetypes\')');
		$database->execute();
		
		// Update sef_link_slug_type_hybrid_separator ordering
		$database->setQuery('UPDATE `#__mt_config` SET `ordering` = \'125\' WHERE `varname` =  \'sef_link_slug_type_hybrid_separator\'');
		$database->execute();

		// Insert the missing sef_listings config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES(\'sef_listings\', \'sef\', \'listings\', \'listings\', \'text\', 2850, 1, 0)');
		$database->execute();

		// If is using M2 template, revert to kinabalu. M2 is no longer supported in MT 3.0
		$database->setQuery('SELECT value FROM `#__mt_config` WHERE `varname` = \'template\'');
		$template = $database->loadResult();

		if( $template == 'm2' )
		{
			$database->setQuery('UPDATE `#__mt_config` SET `value` = \'kinabalu\' WHERE `varname` = \'template\'');
			$database->execute();
		}
		
		updateVersion(3,0,1);
		$this->updated = true;
		return true;
	}
}
?>