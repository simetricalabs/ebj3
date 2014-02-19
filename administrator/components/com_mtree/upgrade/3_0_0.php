<?php
/**
 * @version	$Id: 3_0_0.php 1972 2013-07-16 09:24:13Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_0 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Create #__fields_map table
		$database->setQuery('CREATE TABLE `#__mt_fields_map` ( `cf_id` int(10) unsigned NOT NULL, `cat_id` int(10) unsigned NOT NULL, PRIMARY KEY (`cf_id`,`cat_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		$database->execute();

		$database->setQuery('ALTER TABLE `#__mt_fieldtypes` ADD `is_file` INT( 3 ) NOT NULL DEFAULT \'0\' AFTER `use_placeholder`');
		$database->execute();

		// Add 'overridable_by_category' column to #__mt_config
		$database->setQuery('ALTER TABLE `#__mt_config` ADD `overridable_by_category` SMALLINT NOT NULL DEFAULT \'0\'');
		$database->execute();

		// Add ft_caption, ft_website and ft_class field to #__mt_fieldtypes
		$database->setQuery('ALTER TABLE `#__mt_fieldtypes` ADD `ft_version` VARCHAR( 64 ) NOT NULL AFTER `ft_caption`, ADD `ft_website` VARCHAR( 255 ) NOT NULL AFTER `ft_version`,	ADD `ft_desc` TEXT NOT NULL AFTER `ft_website`');
		$database->execute();
		
		// Remove ft_class column from #__mt_fieldtypes
		$database->setQuery('ALTER TABLE `#__mt_fieldtypes` DROP `ft_class`');
		$database->execute();

		// Add alias field to #__mt_customfields
		$database->setQuery('ALTER TABLE `#__mt_customfields` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `caption`');
		$database->execute();
		
		// Add metadata colum to #__mt_cats
		$database->setQuery('ALTER TABLE `#__mt_cats` ADD `metadata` TEXT NOT NULL AFTER `cat_show_listings`');
		$database->execute();
		
		// Add 'overridable_by_category' column to #__mt_configgroup
		$database->setQuery('ALTER TABLE `#__mt_configgroup` ADD `overridable_by_category` SMALLINT NOT NULL DEFAULT \'0\'');
		$database->execute();
		
		// Add show_map column to #__mt_links
		$database->setQuery('ALTER TABLE `#__mt_links` ADD `show_map` TINYINT UNSIGNED NOT NULL DEFAULT \'1\' AFTER `price`');
		$database->execute();

		// Alter database to support categories & listings associations
		$database->setQuery('CREATE TABLE `#__mt_links_associations` (`link_id1` INT UNSIGNED NOT NULL, `link_id2` INT UNSIGNED NOT NULL ) ENGINE = MYISAM');
		$database->execute();

		$database->setQuery('ALTER TABLE `#__mt_links_associations` ADD INDEX ( `link_id1` , `link_id2` )');
		$database->execute();
		
		$database->setQuery('ALTER TABLE `#__mt_cats` ADD `cat_association` INT DEFAULT NULL AFTER `cat_image`');
		$database->execute();

		$database->setQuery('ALTER TABLE `#__mt_customfields` ADD `filter_search` TINYINT NOT NULL DEFAULT \'0\' AFTER `tag_search`');
		$database->execute();

		$database->setQuery('ALTER TABLE `#__mt_customfields` ADD `placeholder_text` VARCHAR( 255 ) NOT NULL AFTER `suffix_text_display`');
		$database->execute();

		$database->setQuery('ALTER TABLE `#__mt_fieldtypes` ADD `use_placeholder` INT( 3 ) NOT NULL DEFAULT \'0\' AFTER `use_columns`');
		$database->execute();
		
		# Updating js, img, attachments path to /media/com_mtree
		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/js/jquery-1.8.3.min.js\', value = \'/media/com_mtree/js/jquery-1.8.3.min.js\' WHERE varname = \'relative_path_to_js_library\' LIMIT 1');
		$database->execute();
				
		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/images/cats/s/\' WHERE varname = \'relative_path_to_cat_small_image\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/images/cats/o/\' WHERE varname = \'relative_path_to_cat_original_image\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/images/listings/s/\' WHERE varname = \'relative_path_to_listing_small_image\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/images/listings/m/\' WHERE varname = \'relative_path_to_listing_medium_image\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/images/listings/o/\' WHERE varname = \'relative_path_to_listing_original_image\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `default` = \'/media/com_mtree/attachments/\', `value` = \'/media/com_mtree/attachments/\' WHERE varname = \'relative_path_to_attachments\' LIMIT 1');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `varname` = \'resize_small_listing_size\' WHERE varname = \'resize_listing_size\' LIMIT 1');
		$database->execute();

		# Add new config for rating images, and javascripts
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_rating_image\', \'core\', \'\', \'/media/com_mtree/images/\', \'\', 0, 0, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_js\', \'core\', \'\', \'/media/com_mtree/js/\', \'\', 0, 0, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_images\', \'core\', \'\', \'/media/com_mtree/images/\', \'\', 0, 0, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_templates\', \'core\', \'\', \'/components/com_mtree/templates/\', \'\', 0, 0, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_fieldtypes\', \'core\', \'\', \'/administrator/components/com_mtree/fieldtypes/\', \'\', 0, 0, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'relative_path_to_fieldtypes_media\', \'core\', \'\', \'media/com_mtree/fieldtypes/\', \'\', 0, 0, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_link_slug_type_hybrid_separator\', \'sef\', \'-\', \'-\', \'\', 125, 0, 0)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_searchbytags\', \'listing\', \'\', \'100\', \'text\', 999, 0, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_alpha\', \'listing\', \'\', \'20\', \'text\', 6725, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_viewreviews\', \'sef\', \'reviews\', \'reviews\', \'text\', 1655, 1, 0)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_viewreview\', \'sef\', \'review\', \'review\', \'text\', 1660, 1, 0)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_listings\', \'sef\', \'listings\', \'listings\', \'text\', 2850, 1, 0)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'image_required\', \'image\', \'\', \'0\', \'yesno\', 10150, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'max_num_of_secondary_categories\', \'listing\', \'3\', \'3\', \'text\', 3580, 1, 1)');
		$database->execute();

		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` IN (\'params_xml_filename\', \'map\', \'cat_image_dir\', \'listing_image_dir\')');
		$database->execute();

		// Add 'use_open_graph_protocol' config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'use_open_graph_protocol\', \'core\', \'1\', \'1\', \'yesno\', 0, 0, 1)');
		$database->execute();
		
		// Add 'fe_num_of_reviews_in_listing_page' config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_reviews_in_listing_page\', \'listing\', \'3\', \'3\', \'text\', 5610, 0, 1)');
		$database->execute();
		
		// Add 'fe_num_of_all' config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_all\', \'listing\', \'20\', \'20\', \'text\', 6050, 1, 1)');
		$database->execute();
		
		// Add 'fe_num_of_associated' config
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_associated\', \'listing\', \'20\', \'20\', \'text\', 6750, 0, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'all_listings_sort_by\', \'listing\', \'\', \'-link_created\', \'sort\', 1, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'all_listings_sort_by_options\', \'listing\', \'\', \'-link_created|-link_modified\', \'sort_options\', 2, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'show_listing_badge_new\', \'listing\', \'\', \'1\', \'yesno\', 3610, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'show_listing_badge_featured\', \'listing\', \'\', \'1\', \'yesno\', 3620, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'show_listing_badge_popular\', \'listing\', \'\', \'1\', \'yesno\', 3630, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'display_all_listings_link\', \'category\', \'1\', \'1\', \'yesno\', 3400, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'display_categories\', \'category\', \'1\', \'1\', \'yesno\', 3200, 1, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'display_filters\', \'category\', \'1\', \'1\', \'yesno\', 3500, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_all\', \'sef\', \'all\', \'all\', \'text\', 1850, 1, 0)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'sef_listallcats\', \'sef\', \'all-categories\', \'all-categories\', \'text\', 2750, 1, 0)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'owner_default_page\', \'feature\', \'viewuserslisting\', \'viewuserslisting\', \'owner_default_page\', 4650, 1, 0)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'contact_bcc_email\', \'feature\', \'\', \'\', \'text\', 4810, 1, 1)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'type_of_listings_in_index\', \'category\', \'\', \'listcurrent\', \'type_of_listings_in_index\', 3150, 1, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'use_captcha_review\', \'captcha\', \'1\', \'0\', \'yesno\', \'1000\', \'1\', \'0\')');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'use_captcha_contact\', \'captcha\', \'1\', \'0\', \'yesno\', \'2000\', \'1\', \'0\')');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'use_captcha_report\', \'captcha\', \'1\', \'0\', \'yesno\', \'3000\', \'1\', \'0\')');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'use_captcha_reportreview\', \'captcha\', \'1\', \'0\', \'yesno\', \'4000\', \'1\', \'0\')');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'type_of_listings_in_index_count\', \'category\', \'3\', \'3\', \'text\', 3175, 1, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'contact_form_location\', \'feature\', \'1\', \'1\', \'feature_locations\', 4750, 1, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'show_user_profile_in_listing_details\', \'listing\', \'0\', \'0\', \'yesno\', 2500, 1, 1)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'load_js_framework_frontend\', \'core\', \'1\', \'1\', \'yesno\', 0, 0, 0)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'image_min_width\', \'image\', \'600\', \'600\', \'text\', 1060, 1, 0)');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'image_min_height\', \'image\', \'600\', \'600\', \'text\', 1070, 1, 0)');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_config` VALUES (\'fe_num_of_associated\', \'listing\', \'\', \'10\', \'text\', 5550, 1, 1)');
		$database->execute();

		$database->setQuery('DELETE FROM `#__mt_config` WHERE `varname` IN (\'img_impath\',\'img_netpbmpath\');');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `displayed` = \'1\' WHERE `varname` IN(\'alpha_index_additional_chars\',\'days_to_expire\');');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_config` SET `overridable_by_category` = \'1\' WHERE `varname` IN(\'resize_small_listing_size\',\'resize_medium_listing_size\', \'resize_cat_size\');');
		$database->execute();

		// Delete Google Maps Key config
		$database->setQuery('DELETE FROM `#__mt_config` WHERE varname IN (\'gmaps_api_key\', \'map_control\') LIMIT 2');
		$database->execute();

		// Make sure the root category is approved
		$database->setQuery('UPDATE `#__mt_cats` SET `cat_approved` = \'1\' WHERE `cat_id` =0');
		$database->execute();

		// Remove #__mt_searchlog, #__mt_fieldtypes_att and #__mt_fieldtypes_info table
		$database->setQuery('DROP TABLE `#__mt_fieldtypes_att`, `#__mt_fieldtypes_info`, `#__mt_searchlog`;');
		$database->execute();

		$database->setQuery("UPDATE `#__mt_configgroup` SET `overridable_by_category` = 1 WHERE `groupname` IN ('main','category','listing','search','ratingreview','feature','image','rss')");
		$database->execute();

		# Add 'captcha' config group
		$database->setQuery('INSERT INTO `#__mt_configgroup` (`groupname`, `ordering`, `displayed`, `overridable_by_category`) VALUES (\'captcha\', \'690\', \'1\', \'0\')');
		$database->execute();

		$database->setQuery("UPDATE `#__mt_fieldtypes` SET `use_placeholder` = 1 WHERE `field_type` IN ('coreprice','coreaddress','corecity','corestate','corecountry','corepostcode','coretelephone','corefax','coreemail','corewebsite','corename','coredesc','weblinknewwin','multilinetextbox','coremetakey','coremetadesc','mtags','memail','mnumber','enhancedtext')");
		$database->execute();

		$database->setQuery("UPDATE `#__mt_fieldtypes` SET `is_file` = 1 WHERE `field_type` IN ('mfile','videoplayer','image','audioplayer')");
		$database->execute();
		
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `field_type` = \'youtube\', `ft_caption` = \'Youtube\' WHERE `field_type` =\'onlinevideo\' LIMIT 1');
		$database->execute();
		
		// Allow Postcode field to accept elements
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `use_elements` = \'1\' WHERE `field_type` =\'corepostcode\'');
		$database->execute();
		
		// Renamed weblinknewwin to mweblink
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `field_type` = \'mweblink\' WHERE `field_type` =\'weblinknewwin\'');
		$database->execute();
		
		$database->setQuery('UPDATE `#__mt_customfields` SET `field_type` = \'mweblink\' WHERE `field_type` IN (\'enhancedweblink\', \'weblinknewwin\')');
		$database->execute();
		
		// Renamed checkboxwithimage & checkbox to mcheckbox
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `field_type` = \'mcheckbox\', `ft_caption` = \'Checkbox\', `taggable` = \'1\' WHERE `field_type` IN (\'checkbox\',\'checkboxwithimage\')');
		$database->execute();

		$database->setQuery('UPDATE `#__mt_customfields` SET `field_type` = \'mcheckbox\' WHERE `field_type` IN (\'checkbox\',\'checkboxwithimage\')');
		$database->execute();

		// Renamed enhancedtext to mtext
		$database->setQuery('UPDATE `#__mt_fieldtypes` SET `field_type` = \'mtext\', `ft_caption` = \'Text\' WHERE `field_type` =\'enhancedtext\'');
		$database->execute();

		// Renamed multilinetextbox & enhancedtext to mtext
		$database->setQuery('UPDATE `#__mt_customfields` SET `field_type` = \'mtext\' WHERE `field_type` IN (\'enhancedtext\',\'text\',\'multilinetextbox\')');
		$database->execute();
		
		// Renamed enhancedtext & text to mtext
		$database->setQuery('UPDATE `#__mt_customfields` SET `field_type` = \'mtext\' WHERE `field_type` IN (\'enhancedtext\',\'text\')');
		$database->execute();

		// Removed Digg & Enhanced Weblink & Multi-line textbox field type
		$database->setQuery('DELETE FROM `#__mt_fieldtypes` WHERE `field_type` IN (\'digg\', \'enhancedweblink\',\'multilinetextbox\')');
		$database->execute();

		// Add Terms & Conditions fieldtype
		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES (\'termsandconditions\', \'Terms & Conditions\', \'3.0.0\', \'\', \'\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\'), (\'captcha\', \'Captcha\', \'3.0.0\', \'http://www.mosets.com/\', \'\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\')');
		$database->execute();
		
		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(70, \'timezone\', \'Time Zone\', \'3.0.0\', \'\', \'Displays list of time zones.\', 0, 0, 0, 0, 0, 1, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(73, \'monthyear\', \'Month & Year\', \'3.0.0\', \'\', \'Similar to Date field but for selecting month and year only.\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(74, \'listingid\', \'Listing ID\', \'3.0.0\', \'http://www.mosets.com\', \'Listing ID\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(75, \'associatedlisting\', \'Associated Listing\', \'3.0.0\', \'http://www.mosets.com\', \'Associated Listing\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(76, \'category\', \'Category\', \'3.0.0\', \'http://www.mosets.com/tree\', \'Category\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(77, \'directory\', \'Directory\', \'3.0.0\', \'http://www.mosets.com/tree\', \'Directory\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(85, \'vanityurl\', \'Vanity URL\', \'3.0.0\', \'http://www.mosets.com/\', \'\', 0, 1, 0, 1, 0, 1, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(86, \'texteditor\', \'Text Editor\', \'1.0\', \'http://www.mosets.com/\', \'\', 0, 0, 0, 1, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(87, \'termsandconditions\', \'Terms & Conditions\', \'3.0.0\', \'http://www.mosets.com/\', \'\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(88, \'checkboxwithtext\', \'Checkbox with Text\', \'3.0.0\', \'http://www.mosets.com\', \'\', 1, 0, 0, 0, 0, 1, 0);');
		$database->execute();

		$database->setQuery('INSERT INTO `#__mt_fieldtypes` (`ft_id`, `field_type`, `ft_caption`, `ft_version`, `ft_website`, `ft_desc`, `use_elements`, `use_size`, `use_columns`, `use_placeholder`, `is_file`, `taggable`, `iscore`) VALUES(89, \'captcha\', \'Captcha\', \'3.0.0\', \'http://www.mosets.com/\', \'\', 0, 0, 0, 0, 0, 0, 0);');
		$database->execute();

		$this->populate_fields_map();
		
		$this->copy_images_and_attachments_from_tmp_to_media();
		
		JFolder::delete(JPATH_ROOT.'/tmp/media');
		
		updateVersion(3,0,0);
		$this->updated = true;
		return true;
	}
	
	function preflight()
	{
		$this->copy_images_and_attachments_to_tmp();
	}

	/**
	 * Populate #__fields_map table with default fields & cats relationship
	 */
	function populate_fields_map()
	{
		$this->db->setQuery( 'SELECT cat_id FROM #__mt_cats' );
		$cat_ids = $this->db->loadColumn();
		
		$this->db->setQuery( 'SELECT cf_id FROM #__mt_customfields' );
		$cf_ids = $this->db->loadColumn();

		foreach( $cf_ids AS $cf_id )
		{
			$arr_insert_values = array();
			foreach( $cat_ids AS $cat_id )
			{
				$arr_insert_values[] = '('.$cf_id.', '.$cat_id.')';
			}
			$this->db->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES ' . implode(', ',$arr_insert_values) . ';' );
			$this->db->execute();
		}
	}
	
	/**
	 * Copy listing images and attachment to temporary folder to prevent it from being
	 * removed during normal upgrade.
	 */
	function copy_images_and_attachments_to_tmp()
	{
		$directories = array(
			array(
				JPATH_ROOT.'/components/com_mtree/img/listings/s',
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/s'
			),
			array(
				JPATH_ROOT.'/components/com_mtree/img/listings/m',
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/m'
			),
			array(
				JPATH_ROOT.'/components/com_mtree/img/listings/o',
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/o'
			),
			array(
				JPATH_ROOT.'/components/com_mtree/img/cats/s',
				JPATH_ROOT.'/tmp/media/com_mtree/images/cats/s'
			),
			array(
				JPATH_ROOT.'/components/com_mtree/img/cats/o',
				JPATH_ROOT.'/tmp/media/com_mtree/images/cats/o'
			),
			array(
				JPATH_ROOT.'/components/com_mtree/attachments',
				JPATH_ROOT.'/tmp/media/com_mtree/attachments'
			)
		);

		$this->copy_folders( $directories );

		return true;
	}
		
	function copy_images_and_attachments_from_tmp_to_media()
	{
		$directories = array(
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/s',
				JPATH_ROOT.'/media/com_mtree/images/listings/s'
			),
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/m',
				JPATH_ROOT.'/media/com_mtree/images/listings/m'
			),
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/images/listings/o',
				JPATH_ROOT.'/media/com_mtree/images/listings/o'
			),
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/images/cats/s',
				JPATH_ROOT.'/media/com_mtree/images/cats/s'
			),
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/images/cats/o',
				JPATH_ROOT.'/media/com_mtree/images/cats/o'
			),
			array(
				JPATH_ROOT.'/tmp/media/com_mtree/attachments',
				JPATH_ROOT.'/media/com_mtree/attachments'
			)		
		);

		$this->copy_folders( $directories );

		return true;
	}
	
	function copy_folders( $arrFolders )
	{
		// Copy media files to new directories
		foreach( $arrFolders AS $directory )
		{
			if( JFolder::exists($directory[0]) )
			{
				$files = JFolder::files($directory[0]);

				if( !JFolder::exists($directory[1]) )
				{
					JFolder::create($directory[1]);
				}
				
				foreach( $files AS $file )
				{
					if( in_array($file,array('index.html')) )
					{
						continue;
					}
					
					JFile::copy( $directory[0] . '/' . $file, $directory[1] . '/' . $file);
				}
				
			}
		}

		return true;		
	}
}
?>