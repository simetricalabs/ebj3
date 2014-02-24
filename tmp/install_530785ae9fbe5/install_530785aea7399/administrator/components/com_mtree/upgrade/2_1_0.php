<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_0 extends mUpgrade {
	function upgrade() {
		global $mtconf;
		
		$database =& JFactory::getDBO();

		$do = JRequest::getCmd('do');
		
		switch( $do )
		{
			default:
				// Insert att_id column, in preparation to move attachments to filesystem.
				$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` DROP PRIMARY KEY");
				$database->execute();

				$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` ADD `raw_filename` VARCHAR( 255 ) NOT NULL AFTER `cf_id` ;");
				$database->execute();

				$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` ADD `att_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;");
				$database->execute();
				
				// Check to make sure the new attachments directory is writable. Otherwise abort upgrade.
				if( !$this->attachments_is_writable() && $this->get_total_attachments() > 0 ) {
					printRow( 'Please make sure this ' . JPATH_SITE . '/components/com_mtree/attachments/' . ' directory is writable. This upgrade has been aborted. Refresh this page once you have made the directory writable.', 0 );
					$this->updated = false;
					return true;
				}
				
			case 'move_attachments':
				$this->move_attachments();
				if( $this->hasMovableAttachments() ) {
					$this->updated = false;
					$this->continue_url = JRoute::_("index.php?option=com_mtree&task=upgrade&do=move_attachments");
					$this->continue_message = 'Processing upgrade: Moving ' . $this->hasMovableAttachments() . ' attachments...';
					return false;
				}
				break;
				
			case 'continue':
				break;
		}
		
		// Drop filedata column in #__mt_cfvalues_att
		$database->setQuery( 'ALTER TABLE #__mt_cfvalues_att DROP `filedata`' );
		$database->execute();
		
		// Add columns for aliases
		$database->setQuery("ALTER TABLE `#__mt_cats` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `cat_name` ;");
		$database->execute();

		// Add columns for titles
		$database->setQuery("ALTER TABLE `#__mt_cats` ADD `title` VARCHAR( 255 ) NOT NULL AFTER `alias` ;");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_cats` ADD INDEX `alias` ( `alias` )");
		$database->execute();

		$database->setQuery("ALTER TABLE `#__mt_links` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `link_name` ;");
		$database->execute();
		
		$aliased = $this->populate_category_alias();
		$this->printStatus( 'Created default alias for ' . $aliased . ' categories.' );

		$aliased = $this->populate_listing_alias();
		$this->printStatus( 'Created default alias for ' . $aliased . ' listings.' );
		
		// Updating database schema
		$database->setQuery("UPDATE `#__mt_config` SET `varname` = 'fe_num_of_favourite' WHERE varname = 'fe_num_of_mostfavoured' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `varname` = 'fe_num_of_new' WHERE varname = 'fe_num_of_newlisting' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `varname` = 'fe_num_of_popular' WHERE varname = 'fe_num_of_popularlisting' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `varname` = 'fe_num_of_updated' WHERE varname = 'fe_num_of_recentlyupdated' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `varname` = 'fe_total_new' WHERE `varname` = 'fe_total_newlisting' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `configcode` = 'text' WHERE `varname` = 'template' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("UPDATE `#__mt_config` SET `ordering` = '4175' WHERE `varname` = 'show_favourite' LIMIT 1 ;");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_archived_reviews` ADD `send_email` TINYINT NOT NULL , ADD `email_message` MEDIUMTEXT NOT NULL ;");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_reviews` DROP INDEX `link_id` , ADD INDEX `link_id` ( `link_id` , `rev_approved` , `rev_date` ) ;");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_favourites` ADD INDEX `link_id` ( `link_id` ); ");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_reviews` ADD INDEX `user_id` ( `user_id` , `rev_approved` , `rev_date` )");
		$database->execute();

		$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` ADD INDEX `primary2` ( `link_id` , `cf_id` )");
		$database->execute();

		$database->setQuery("ALTER TABLE `#__mt_cfvalues` ADD `counter` INT NOT NULL DEFAULT '0';");
		$database->execute();
		
		$database->setQuery("ALTER TABLE `#__mt_links` ADD `lat` FLOAT NOT NULL COMMENT 'Latitude',
ADD `lng` FLOAT NOT NULL COMMENT 'Longitude',
ADD `zoom` TINYINT UNSIGNED NOT NULL COMMENT 'Map''s zoom level';");
		$database->execute();
		
		// Update #__mt_archived_log character set & collation
		$database->setQuery("ALTER TABLE `#__mt_archived_log` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_archived_log` CHANGE `log_ip` `log_ip` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `log_type` `log_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_archived_reviews character set & collation
		$database->setQuery("ALTER TABLE `#__mt_archived_reviews` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_archived_reviews` CHANGE `guest_name` `guest_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `rev_title` `rev_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `rev_text` `rev_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `admin_note` `admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ownersreply_text` `ownersreply_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ownersreply_admin_note` `ownersreply_admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_archived_users character set & collation
		$database->setQuery("ALTER TABLE `#__mt_archived_users` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_archived_users` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `username` `username` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `usertype` `usertype` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `activation` `activation` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `params` `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_links character set & collation
		$database->setQuery("ALTER TABLE `#__mt_links` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_links` CHANGE `link_name` `link_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `link_desc` `link_desc` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `link_template` `link_template` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `attribs` `attribs` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `metakey` `metakey` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `metadesc` `metadesc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `internal_notes` `internal_notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `address` `address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `city` `city` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `state` `state` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `country` `country` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `postcode` `postcode` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `telephone` `telephone` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `fax` `fax` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `email` `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `website` `website` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
		$database->execute();

		// Update #__mt_cats character set & collation
		$database->setQuery("ALTER TABLE `#__mt_cats` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
				
		$database->setQuery("ALTER TABLE `#__mt_cats` CHANGE `cat_name` `cat_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `cat_desc` `cat_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `cat_image` `cat_image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	CHANGE `cat_template` `cat_template` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `metakey` `metakey` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `metadesc` `metadesc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_cfvalues character set & collation
		$database->setQuery("ALTER TABLE `#__mt_cfvalues` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
				
		$database->setQuery("ALTER TABLE `#__mt_cfvalues` CHANGE `value` `value` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_cfvalues_att character set & collation
		$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
				
		$database->setQuery("ALTER TABLE `#__mt_cfvalues_att` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `extension` `extension` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_cl character set & collation
		$database->setQuery("ALTER TABLE `#__mt_cl` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();

		// Update #__mt_claims character set & collation
		$database->setQuery("ALTER TABLE `#__mt_claims` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_claims` CHANGE `comment` `comment` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `admin_note` `admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_clone_owners character set & collation
		$database->setQuery("ALTER TABLE `#__mt_clone_owners` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();

		// Update #__mt_configgroup character set & collation
		$database->setQuery("ALTER TABLE `#__mt_configgroup` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_configgroup` CHANGE `groupname` `groupname` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_customfields character set & collation
		$database->setQuery("ALTER TABLE `#__mt_customfields` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_customfields` CHANGE `field_type` `field_type` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `caption` `caption` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `default_value` `default_value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `field_elements` `field_elements` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `prefix_text_mod` `prefix_text_mod` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `suffix_text_mod` `suffix_text_mod` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `prefix_text_display` `prefix_text_display` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `suffix_text_display` `suffix_text_display` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `search_caption` `search_caption` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `params` `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_favourites character set & collation
		$database->setQuery("ALTER TABLE `#__mt_favourites` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		
		// Update #__mt_fieldtypes character set & collation
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes` CHANGE `field_type` `field_type` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ft_caption` `ft_caption` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ft_class` `ft_class` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_fieldtypes_att character set & collation
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes_att` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes_att` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `extension` `extension` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_fieldtypes_info character set & collation
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes_info` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes_info` CHANGE `ft_version` `ft_version` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ft_website` `ft_website` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ft_desc` `ft_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_images character set & collation
		$database->setQuery("ALTER TABLE `#__mt_images` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_images` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_linkcheck character set & collation
		$database->setQuery("ALTER TABLE `#__mt_linkcheck` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_linkcheck` CHANGE `field` `field` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `link_name` `link_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `domain` `domain` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `path` `path` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_log character set & collation
		$database->setQuery("ALTER TABLE `#__mt_log` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_log` CHANGE `log_ip` `log_ip` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `log_type` `log_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_relcats character set & collation
		$database->setQuery("ALTER TABLE `#__mt_relcats` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();

		// Update #__mt_reports character set & collation
		$database->setQuery("ALTER TABLE `#__mt_reports` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_reports` CHANGE `guest_name` `guest_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `subject` `subject` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `comment` `comment` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `admin_note` `admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Update #__mt_reviews character set & collation
		$database->setQuery("ALTER TABLE `#__mt_reviews` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_reviews` CHANGE `guest_name` `guest_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `rev_title` `rev_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `rev_text` `rev_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `admin_note` `admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ownersreply_text` `ownersreply_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `ownersreply_admin_note` `ownersreply_admin_note` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `email_message` `email_message` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_searchlog character set & collation
		$database->setQuery("ALTER TABLE `#__mt_searchlog` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_searchlog` CHANGE `search_text` `search_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_templates character set & collation
		$database->setQuery("ALTER TABLE `#__mt_templates` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
		$database->setQuery("ALTER TABLE `#__mt_templates` CHANGE `tem_name` `tem_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		CHANGE `params` `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();

		// Update #__mt_config character set & collation
		$database->setQuery("ALTER TABLE `#__mt_config` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$database->execute();
				
		$database->setQuery("ALTER TABLE `#__mt_config` CHANGE `varname` `varname` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `groupname` `groupname` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `value` `value` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `default` `default` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `configcode` `configcode` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$database->execute();
		
		// Add tag_search coloum to search table
		$database->setQuery("ALTER TABLE `#__mt_customfields` ADD `tag_search` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `simple_search` ;");
		$database->execute();
		
		// Add Tag custom field
		$database->setQuery("INSERT INTO `#__mt_customfields` VALUES ('', 'mtags', 'Tags', '', 40, '', '', '', '', '', 0, 25, 0, 0, 1, 0, 0, 0, 1, 1, 1, '', '', 0);");
		$database->execute();
		
		// Add taggable column to fieldtype table
		$database->setQuery("ALTER TABLE `#__mt_fieldtypes` ADD `taggable` TINYINT( 3 ) NOT NULL DEFAULT '0' AFTER `use_columns` ;");
		$database->execute();
		
		// Set Country, State & City field type to taggable
		$database->setQuery("UPDATE `#__mt_fieldtypes` SET `taggable` = '1' WHERE `ft_id` IN (4,5,6) ;");
		$database->execute();

		$database->setQuery("UPDATE `#__mt_customfields` SET `tag_search` = '1' WHERE `field_type` IN ('corecity','corestate','corecountry','coremetakey') ;");
		$database->execute();
		
		// Set coremetakey to taggable
		$database->setQuery("UPDATE `#__mt_fieldtypes` SET `taggable` = '1' WHERE `field_type` IN ('coremetakey');");
		$database->execute();
		
		// Add Tag fieldtype custom field
		$database->setQuery("INSERT INTO `#__mt_fieldtypes` VALUES ('', 'mtags', 'Tags', 'class mFieldType_mTags extends mFieldType_tags {\r\n\r\n}', 0, 1, 0, 1, 0);");
		$database->execute();
		$ft_id_mtags = $database->insertid();
		
		// Add mtags params.xml
		$database->setQuery("INSERT INTO `#__mt_fieldtypes_att` VALUES ('', ".$ft_id_mtags.", 'params.xml', 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d226d617843686172732220747970653d2274657874222064656661756c743d22383022206c6162656c3d224d61782e206368617261637465727322206465736372697074696f6e3d22546865206d6178696d756d206e756d626572206f66206368617261637465727320616c6c6f77656420696e2074686973206669656c642e222f3e0a093c2f706172616d733e0a3c2f6d6f73706172616d733e, 205, 'text/xml', 1);");
		$database->execute();
		
		// Updates description params.xml
		$database->setQuery("UPDATE `#__mt_fieldtypes_att` SET `filedata` =  0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d2273756d6d61727943686172732220747970653d2274657874222064656661756c743d2232353522206c6162656c3d224e756d626572206f662053756d6d617279206368617261637465727322202f3e0a09093c706172616d206e616d653d226d617843686172732220747970653d2274657874222064656661756c743d223330303022206c6162656c3d224d61782e206368617261637465727322206465736372697074696f6e3d22546865206d6178696d756d206e756d626572206f66206368617261637465727320616c6c6f77656420696e2074686973206669656c642e204465736372697074696f6e207468617420666f6573206f7665722074686973206c696d69742077696c6c206265207472696d6d65642e222f3e0a09093c706172616d206e616d653d22737472697053756d6d617279546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2053756d6d617279207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773207468617420636f756c6420706f74656e7469616c6c7920616666656374207768656e2076696577696e672061206c697374206f66206c697374696e67732e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22737472697044657461696c73546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2044657461696c73207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773206578636570742074686f73652074686174206172652073706563696669656420696e2027416c6c6f7765642074616773272e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22706172736555726c2220747970653d22726164696f222064656661756c743d223122206c6162656c3d2250617273652055524c206173206c696e6b20696e2044657461696c732076696577223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a0a09093c706172616d206e616d653d227374726970416c6c546167734265666f7265536176652220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c2074616773206265666f72652073746f72696e6720746f20646174616261736522206465736372697074696f6e3d224966205759535957494720656469746f7220697320656e61626c656420696e207468652066726f6e742d656e642c2074686973206665617475726520616c6c6f7720796f7520746f20737472697020616e7920706f74656e7469616c6c79206861726d66756c20636f6465732e20596f752063616e207374696c6c20616c6c6f7720736f6d6520746167732077697468696e206465736372697074696f6e206669656c642c2077686963682063616e206265207370656369666965642062656c6f772e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22616c6c6f776564546167732220747970653d2274657874222064656661756c743d22752c622c692c612c756c2c6c692c7072652c626c6f636b71756f74652c7374726f6e672c656d22206c6162656c3d22416c6c6f776564207461677322206465736372697074696f6e3d22456e7465722074686520746167206e616d65732073657065726174656420627920636f6d6d612e205468697320706172616d6574657220616c6c6f7720796f7520746f2061636365707420736f6d652048544d4c2074616773206576656e20696620796f75206861766520656e61626c65207374726970696e67206f6620616c6c2048544d4c20746167732061626f76652e22202f3e0a09093c706172616d206e616d653d2270617273654d616d626f74732220747970653d22726164696f222064656661756c743d223022206c6162656c3d225061727365204d616d626f747322206465736372697074696f6e3d22456e61626c696e6720746869732077696c6c207061727365206d616d626f747320636f646573732077697468696e20746865206465736372697074696f6e206669656c64223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d2273686f77526561644d6f72652220747970653d22726164696f222064656661756c743d223022206c6162656c3d2253686f77202671756f743b52656164204d6f72652e2e2e2671756f743b22206465736372697074696f6e3d2253686f77202671756f743b52656164204d6f72652e2e2671756f743b2069662061206465736372697074696f6e207465787420636c697070656420696e2053756d6d61727920566965772e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d227768656e526561644d6f72652220747970653d226c697374222064656661756c743d223022206c6162656c3d225768656e20746f2073686f77202671756f743b52656164204d6f72652e2e2671756f743b22206465736372697074696f6e3d225468697320616c6c6f7720796f7520746f20736574207768656e20746f2073686f7720746865202671756f743b52656164204d6f72652e2e2671756f743b206c696e6b2e223e0a0909093c6f7074696f6e2076616c75653d2230223e5768656e206465736372697074696f6e20697320636c69707065643c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e416c6c207468652074696d653c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22747874526561644d6f72652220747970653d2274657874222064656661756c743d2252656164204d6f72652e2e2e22206c6162656c3d2252656164204d6f7265207465787422206465736372697074696f6e3d22456e74657220746865202671756f743b52656164204d6f72652e2e2671756f743b20746578742e22202f3e0a093c2f706172616d733e0a3c2f6d6f73706172616d733e WHERE ft_id = '21' LIMIT 1;");
		$database->execute();
		
		// Updating MT's configs
		$database->setQuery("INSERT INTO `#__mt_configgroup` (`groupname` ,`ordering` ,`displayed` )VALUES ('sef', '685', '1');");
		$database->execute();
		
		$database->setQuery("INSERT INTO `#__mt_configgroup` (`groupname` ,`ordering` ,`displayed` )VALUES ('email', '999', '0');");
		$database->execute();
		
		$database->setQuery("INSERT INTO `#__mt_config` (`varname` ,`groupname` ,`value` ,`default` ,`configcode` ,`ordering` ,`displayed` )
		VALUES ('note_map', 'feature', '', '', 'note', '3925', '1'), ('note_other_features', 'feature', '', '', 'note', '4170', '1'), ('gmaps_api_key', 'feature', '', '', 'text', '3955', '1'), ('map_default_lat', 'feature', '12.554563528593656', '12.554563528593656', 'text', '3985', '0'), ('map_default_lng', 'feature', '18.984375', '18.984375', 'text', '3986', '0'),('map_default_zoom', 'feature', '1', '1', 'text', '3987', '0'), 
		('map_control', 'feature', 'GSmallMapControl,GMapTypeControl', 'GSmallMapControl,GMapTypeControl', 'text', '3988', '0'),  ('notifyowner_review_added', 'notify', '1', '1', 'yesno', '9900', '1'),
		('load_css', 'core', '1', '1', 'yesno', '0', '0'),
		('small_image_click_target_size', 'admin', '', 'o', 'text', '13000', '0'),
		('rss_secret_token', 'rss', '', '', 'text', '', '0'),
		('show_category_rss', 'rss', '1', '1', 'yesno', '', '1'),
		('log_visit', 'feature', '1', '1', 'yesno', '', '0'),
		('banned_email', 'email', '', '', 'text', '', '0'),
		('banned_text', 'email', '', '', 'text', '', '0'),
		('banned_subject', 'email', '', '', 'text', '', '0'),
		('default_search_condition', 'search', '2', '2', 'text', '', '0'),
		('reset_created_date_upon_approval', 'core', '1', '1', 'yesno', '', '0'),
		('fe_total_updated', 'listing', '60', '60', 'text', '6050', '0'),
		('fe_total_popular', 'listing', '60', '60', 'text', '5750', '0'),
		('fe_total_favourite', 'listing', '60', '60', 'text', '6150', '0'),
		('fe_total_mostrated', 'listing', '60', '60', 'text', '6350', '0'),
		('fe_total_toprated', 'listing', '60', '60', 'text', '6450', '0'),
		('fe_total_mostreview', 'listing', '60', '60', 'text', '6550', '0'),
		('relative_path_to_attachments', 'core', '/components/com_mtree/attachments/', '/components/com_mtree/attachments/', 'text', '', '0'),
		 ('unpublished_message_cfid', 'listing', '0', '0', 'text', '6600', '0'),
		 ('cache_registered_viewlink', 'main', '0', '0', 'yesno', '0', '0'),
		 ('display_pending_approval_listings_to_owners', 'listing', '0', '0', 'yesno', '4000', '0'),
		('sef_link_slug_type', 'sef', '1', '1', 'sef_link_slug_type', '100', '1'),
		('sef_details', 'sef', 'details', 'details', 'text', '175', '0'),
		('sef_image', 'sef', 'image', 'image', 'text', '200', '1'),
		('sef_gallery', 'sef', 'gallery', 'gallery', 'text', '300', '1'),
		('sef_review', 'sef', 'review', 'review', 'text', '400', '1'),
		('sef_replyreview', 'sef', 'replyreview', 'replyreview', 'text', '500', '1'),
		('sef_reportreview', 'sef', 'reportreview', 'reportreview', 'text', '600', '1'),
		('sef_recommend', 'sef', 'recommend', 'recommend', 'text', '800', '1'),
		('sef_print', 'sef', 'print', 'print', 'text', '850', '1'),
		('sef_contact', 'sef', 'contact', 'contact', 'text', '900', '1'),
		('sef_report', 'sef', 'report', 'report', 'text', '1000', '1'),
		('sef_claim', 'sef', 'claim', 'claim', 'text', '1100', '1'),
		('sef_visit', 'sef', 'visit', 'visit', 'text', '1200', '1'),
		('sef_category_page', 'sef', 'page', 'page', 'text', '1300', '1'),
		('sef_delete', 'sef', 'delete', 'delete', 'text', '1400', '1'),
		('sef_reviews_page', 'sef', 'reviews', 'reviews', 'text', '1500', '1'),
		('sef_addlisting', 'sef', 'add', 'add', 'text', '1600', '1'),
		('sef_editlisting', 'sef', 'edit', 'edit', 'text', '1650', '1'),
		('sef_addcategory', 'sef', 'add-category', 'add-category', 'text', '1700', '1'),
		('sef_mypage', 'sef', 'my-page', 'my-page', 'text', '1800', '1'),
		('sef_new', 'sef', 'new', 'new', 'text', '1900', '1'),
		('sef_updated', 'sef', 'updated', 'updated', 'text', '2000', '1'),
		('sef_favourite', 'sef', 'most-favoured', 'most-favoured', 'text', '2100', '1'),
		('sef_featured', 'sef', 'featured', 'featured', 'text', '2200', '1'),
		('sef_popular', 'sef', 'popular', 'popular', 'text', '2300', '1'),
		('sef_mostrated', 'sef', 'most-rated', 'most-rated', 'text', '2400', '1'),
		('sef_toprated', 'sef', 'top-rated', 'top-rated', 'text', '2500', '1'),
		('sef_mostreview', 'sef', 'most-reviewed', 'most-reviewed', 'text', '2600', '1'),
		('sef_listalpha', 'sef', 'list-alpha', 'list-alpha', 'text', '2700', '1'),
		('sef_owner', 'sef', 'owner', 'owner', 'text', '2800', '1'),
		('sef_favourites', 'sef', 'favourites', 'favourites', 'text', '2900', '1'),
		('sef_reviews', 'sef', 'reviews', 'reviews', 'text', '3000', '1'),
		('sef_searchby', 'sef', 'search-by', 'search-by', 'text', '3050', '1'),
		('sef_search', 'sef', 'search', 'search', 'text', '3100', '1'),
		('sef_advsearch', 'sef', 'advanced-search', 'advanced-search', 'text', '3200', '1'),
		('sef_advsearch2', 'sef', 'advanced-search-results', 'advanced-search-results', 'text', '3300', '1'),
		('sef_rss', 'sef', 'rss', 'rss', 'text', '3400', '1'),
		('sef_rss_new', 'sef', 'new', 'new', 'text', '3500', '1'),
		('sef_rss_updated', 'sef', 'updated', 'updated', 'text', '3600', '1'),
		('sef_space', 'sef', '-', '-', 'text', '3700', '1'),
		('use_map', 'feature', '1', '1', 'yesno', '3950', '1'),
		('map_default_country', 'feature', '', '', 'text', '3960', '1'),
		('map_default_state', 'feature', '', '', 'text', '3970', '1'),
		('map_default_city', 'feature', '', '', 'text', '3980', '1'),
		('note_sef_translations', 'sef', '', '', 'note', '150', '1'),
		('show_image_rss', 'rss', '1', '1', 'yesno', '250', '0');");
		$database->execute();

		$database->setQuery("DELETE FROM `#__mt_config` WHERE `varname` IN ('language','relative_path_to_attachment_php','fulltext_search') LIMIT 3;");
		$database->execute();
		
		// Update corecity class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corecity extends mFieldType {\r\n	var $name = \'city\';\r\n}"  WHERE field_type = "corecity" LIMIT 1');
		$database->execute();

		// Update corecountry class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corecountry extends mFieldType {\r\n	var $name = \'country\';\r\n}"  WHERE field_type = "corecountry" LIMIT 1');
		$database->execute();

		// Update corestate class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corestate extends mFieldType {\r\n	var $name = \'state\';\r\n}"  WHERE field_type = "corestate" LIMIT 1');
		$database->execute();

		// Update digg class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldtype_digg extends mFieldType { \r\n    var $numOfSearchFields = 0; \r\n    var $numOfInputFields = 0; \r\n\r\n    function getOutput($view=1) { \r\n        global $mtconf, $Itemid; \r\n        $html = \'\'; \r\n        $html .= \'<script type=\"text/javascript\">\'; \r\n        $html .= \'digg_url=\\\\\'\'; \r\n        $uri =& JUri::getInstance(); \r\n        if(substr($mtconf->getjconf(\'live_site\'),0,16) == \'http://localhost\') { \r\n            // Allow for debugging \r\n            $html .= str_replace(\'http://localhost\',\'http://127.0.0.1\',$uri->toString(array( \'scheme\', \'host\', \'port\' ))); \r\n        } else { \r\n            $html .= $uri->toString(array( \'scheme\', \'host\', \'port\' )); \r\n        } \r\n        $html .= JRoute::_(\'index.php?option=com_mtree&task=viewlink&link_id=\'.$this->getLinkId().\'&Itemid=\'.$Itemid, false) .\'\\\\\';\'; \r\n        // Display the compact version when displayed in Summary view \r\n        if($view==2) { \r\n            $html .= \'digg_skin = \\\\\'compact\\\\\';\'; \r\n        } \r\n        $html .= \'</script>\'; \r\n        $html .= \'<script src=\"http://digg.com/tools/diggthis.js\" type=\"text/javascript\"></script>\'; \r\n        return $html; \r\n    } \r\n}"  WHERE field_type = "digg" LIMIT 1');
		$database->execute();

		// Update digg version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "54" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated digg field type to version 2.' );


		// Update audioplayer class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_audioplayer extends mFieldType_file {\r\n	function getJSValidation() {\r\n\r\n		$js = \'\';\r\n		$js .= \'} else if (!hasExt(form.\' . $this->getName() . \'.value,\\\\\'mp3\\\\\')) {\'; \r\n		$js .= \'alert(\"\' . addslashes($this->getCaption()) . \': Please select a mp3 file.\");\';\r\n		return $js;\r\n	}\r\n	function getOutput() {\r\n		$id = $this->getId();\r\n		$params[\'text\'] = $this->getParam(\'textColour\');\r\n		$params[\'displayfilename\'] = $this->getParam(\'displayfilename\',1);\r\n		$params[\'slider\'] = $this->getParam(\'sliderColour\');\r\n		$params[\'loader\'] = $this->getParam(\'loaderColour\');\r\n		$params[\'track\'] = $this->getParam(\'trackColour\');\r\n		$params[\'border\'] = $this->getParam(\'borderColour\');\r\n		$params[\'bg\'] = $this->getParam(\'backgroundColour\');\r\n		$params[\'leftbg\'] = $this->getParam(\'leftBackgrounColour\');\r\n		$params[\'rightbg\'] = $this->getParam(\'rightBackgrounColour\');\r\n		$params[\'rightbghover\'] = $this->getParam(\'rightBackgroundHoverColour\');\r\n		$params[\'lefticon\'] = $this->getParam(\'leftIconColour\');\r\n		$params[\'righticon\'] = $this->getParam(\'rightIconColour\');\r\n		$params[\'righticonhover\'] = $this->getParam(\'rightIconHoverColour\');\r\n		\r\n		$html = \'\';\r\n		$html .= \'<script language=\"JavaScript\" src=\"\' . $this->getFieldTypeAttachmentURL(\'audio-player.js\'). \'\"></script>\';\r\n		$html .= \"\\\\n\" . \'<object type=\"application/x-shockwave-flash\" data=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\'). \'\" id=\"audioplayer\' . $id . \'\" height=\"24\" width=\"290\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"movie\" value=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\') . \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"FlashVars\" value=\"\';\r\n		$html .= \'playerID=\' . $id;\r\n		$html .= \'&amp;soundFile=\' . urlencode($this->getDataAttachmentURL());\r\n		foreach( $params AS $key => $value ) {\r\n			if(!empty($value)) {\r\n				$html .= \'&amp;\' . $key . \'=0x\' . $value;\r\n			}\r\n		}\r\n		$html .= \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"quality\" value=\"high\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"menu\" value=\"false\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"wmode\" value=\"transparent\">\';\r\n		$html .= \"\\\\n\" . \'</object>\';\r\n		if($params[\'displayfilename\']) {\r\n			$html .= \"\\\\n<br />\";\r\n			$html .= \"\\\\n\" . \'<a href=\"\' . $this->getDataAttachmentURL() . \'\" target=\"_blank\">\';\r\n			$html .= $this->getValue();\r\n			$html .= \'</a>\';\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "audioplayer" LIMIT 1');
		$database->execute();

		// Update audioplayer version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "24" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated audioplayer field type to version 3.' );

		// Update coredesc class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coredesc extends mFieldType {\r\n	var $name = \'link_desc\';\r\n	function parseValue($value) {\r\n		$params[\'maxChars\'] = intval($this->getParam(\'maxChars\',3000));\r\n		$params[\'stripAllTagsBeforeSave\'] = $this->getParam(\'stripAllTagsBeforeSave\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,blockquote,strong,em\');\r\n		if($params[\'stripAllTagsBeforeSave\']) {\r\n			$value = $this->stripTags($value,$params[\'allowedTags\']);\r\n		}\r\n		if($params[\'maxChars\'] > 0) {\r\n			$value = JString::substr( $value, 0, $params[\'maxChars\']);\r\n		}\r\n		return $value;\r\n	}\r\n	function getInputHTML() {\r\n		global $mtconf;\r\n		\r\n		if( ($this->inBackEnd() AND $mtconf->get(\'use_wysiwyg_editor_in_admin\')) || (!$this->inBackEnd() AND $mtconf->get(\'use_wysiwyg_editor\')) ) {\r\n			$editor = JFactory::getEditor();\r\n			$html = $editor->display( $this->getInputFieldName(1), $this->getValue() , \'100%\', \'250\', \'75\', \'25\', array(\'pagebreak\', \'readmore\') );\r\n		} else {\r\n			$html = \'<textarea class=\"inputbox\" name=\"\' . $this->getInputFieldName(1) . \'\" style=\"width:95%;height:\' . $this->getSize() . \'px\">\' . htmlspecialchars($this->getValue()) . \'</textarea>\';\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'<input class=\"inputbox\" type=\"text\" name=\"\' . $this->getName() . \'\" size=\"30\" />\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'parseUrl\'] = $this->getParam(\'parseUrl\',1);\r\n		$params[\'summaryChars\'] = $this->getParam(\'summaryChars\',255);\r\n		$params[\'stripSummaryTags\'] = $this->getParam(\'stripSummaryTags\',1);\r\n		$params[\'stripDetailsTags\'] = $this->getParam(\'stripDetailsTags\',1);\r\n		$params[\'parseMambots\'] = $this->getParam(\'parseMambots\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,blockquote,strong,em\');\r\n		$params[\'showReadMore\'] = $this->getParam(\'showReadMore\',0);\r\n		$params[\'whenReadMore\'] = $this->getParam(\'whenReadMore\',0);\r\n		$params[\'txtReadMore\'] = $this->getParam(\'txtReadMore\',JTEXT::_( \'Read More...\' ));\r\n		\r\n		$html = $this->getValue();\r\n		$output = \'\';\r\n		\r\n		// Details view\r\n		if($view == 1) {\r\n			global $mtconf;\r\n			$output = $html;\r\n			if($params[\'stripDetailsTags\']) {\r\n				$output = $this->stripTags($output,$params[\'allowedTags\']);\r\n			}\r\n			if($params[\'parseUrl\']) {\r\n				$regex = \'/http:\\\\/\\\\/(.*?)(\\\\s|$)/i\';\r\n				$output = preg_replace_callback( $regex, array($this,\'linkcreator\'), $output );\r\n			}\r\n			if (!$mtconf->get(\'use_wysiwyg_editor\') && $params[\'stripDetailsTags\'] && !in_array(\'br\',explode(\',\',$params[\'allowedTags\'])) && !in_array(\'p\',explode(\',\',$params[\'allowedTags\'])) ) {\r\n				$output = nl2br(trim($output));\r\n			}\r\n			if($params[\'parseMambots\']) {\r\n				$this->parseMambots($output);\r\n			}\r\n		// Summary view\r\n		} else {\r\n			$html = preg_replace(\'@{[\\\\/\\\\!]*?[^<>]*?}@si\', \'\', $html);\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = strip_tags( $html );\r\n			}\r\n			if($params[\'summaryChars\'] > 0) {\r\n				$trimmed_desc = trim(JString::substr($html,0,$params[\'summaryChars\']));\r\n			} else {\r\n				$trimmed_desc = \'\';\r\n			}\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = htmlspecialchars( $html );\r\n				$trimmed_desc = htmlspecialchars( $trimmed_desc );\r\n			}\r\n			if (JString::strlen($html) > $params[\'summaryChars\']) {\r\n				$output .= $trimmed_desc;\r\n				$output .= \' <b>...</b>\';\r\n			} else {\r\n				$output = $html;\r\n			}\r\n			if( $params[\'showReadMore\'] && ($params[\'whenReadMore\'] == 1 || ($params[\'whenReadMore\'] == 0 && JString::strlen($html) > $params[\'summaryChars\'])) ) {\r\n				if(!empty($trimmed_desc)) {\r\n					$output .= \'<br />\';\r\n				}\r\n				$output .= \' <a href=\"\' . JRoute::_(\'index.php?option=com_mtree&task=viewlink&link_id=\' . $this->getLinkId()) . \'\" class=\"readon\">\' . $params[\'txtReadMore\'] . \'</a>\';\r\n			}\r\n		}\r\n		return $output;\r\n	}\r\n}"  WHERE field_type = "coredesc" LIMIT 1');
		$database->execute();

		// Update coredesc params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D2273756D6D61727943686172732220747970653D2274657874222064656661756C743D2232353522206C6162656C3D224E756D626572206F662053756D6D617279206368617261637465727322202F3E0A09093C706172616D206E616D653D226D617843686172732220747970653D2274657874222064656661756C743D223330303022206C6162656C3D224D61782E206368617261637465727322206465736372697074696F6E3D22546865206D6178696D756D206E756D626572206F66206368617261637465727320616C6C6F77656420696E2074686973206669656C642E204465736372697074696F6E207468617420666F6573206F7665722074686973206C696D69742077696C6C206265207472696D6D65642E222F3E0A09093C706172616D206E616D653D22737472697053756D6D617279546167732220747970653D22726164696F222064656661756C743D223122206C6162656C3D22537472697020616C6C2048544D4C207461677320696E2053756D6D617279207669657722206465736372697074696F6E3D2253657474696E67207468697320746F207965732077696C6C2072656D6F766520616C6C2074616773207468617420636F756C6420706F74656E7469616C6C7920616666656374207768656E2076696577696E672061206C697374206F66206C697374696E67732E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22737472697044657461696C73546167732220747970653D22726164696F222064656661756C743D223122206C6162656C3D22537472697020616C6C2048544D4C207461677320696E2044657461696C73207669657722206465736372697074696F6E3D2253657474696E67207468697320746F207965732077696C6C2072656D6F766520616C6C2074616773206578636570742074686F73652074686174206172652073706563696669656420696E2027416C6C6F7765642074616773272E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22706172736555726C2220747970653D22726164696F222064656661756C743D223122206C6162656C3D2250617273652055524C206173206C696E6B20696E2044657461696C732076696577223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A0A09093C706172616D206E616D653D227374726970416C6C546167734265666F7265536176652220747970653D22726164696F222064656661756C743D223122206C6162656C3D22537472697020616C6C2048544D4C2074616773206265666F72652073746F72696E6720746F20646174616261736522206465736372697074696F6E3D224966205759535957494720656469746F7220697320656E61626C656420696E207468652066726F6E742D656E642C2074686973206665617475726520616C6C6F7720796F7520746F20737472697020616E7920706F74656E7469616C6C79206861726D66756C20636F6465732E20596F752063616E207374696C6C20616C6C6F7720736F6D6520746167732077697468696E206465736372697074696F6E206669656C642C2077686963682063616E206265207370656369666965642062656C6F772E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22616C6C6F776564546167732220747970653D2274657874222064656661756C743D22752C622C692C612C756C2C6C692C7072652C626C6F636B71756F74652C7374726F6E672C656D22206C6162656C3D22416C6C6F776564207461677322206465736372697074696F6E3D22456E7465722074686520746167206E616D65732073657065726174656420627920636F6D6D612E205468697320706172616D6574657220616C6C6F7720796F7520746F2061636365707420736F6D652048544D4C2074616773206576656E20696620796F75206861766520656E61626C65207374726970696E67206F6620616C6C2048544D4C20746167732061626F76652E22202F3E0A09093C706172616D206E616D653D2270617273654D616D626F74732220747970653D22726164696F222064656661756C743D223022206C6162656C3D225061727365204D616D626F747322206465736372697074696F6E3D22456E61626C696E6720746869732077696C6C207061727365206D616D626F747320636F646573732077697468696E20746865206465736372697074696F6E206669656C64223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D2273686F77526561644D6F72652220747970653D22726164696F222064656661756C743D223022206C6162656C3D2253686F77202671756F743B52656164204D6F72652E2E2E2671756F743B22206465736372697074696F6E3D2253686F77202671756F743B52656164204D6F72652E2E2671756F743B2069662061206465736372697074696F6E207465787420636C697070656420696E2053756D6D61727920566965772E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D227768656E526561644D6F72652220747970653D226C697374222064656661756C743D223022206C6162656C3D225768656E20746F2073686F77202671756F743B52656164204D6F72652E2E2671756F743B22206465736372697074696F6E3D225468697320616C6C6F7720796F7520746F20736574207768656E20746F2073686F7720746865202671756F743B52656164204D6F72652E2E2671756F743B206C696E6B2E223E0A0909093C6F7074696F6E2076616C75653D2230223E5768656E206465736372697074696F6E20697320636C69707065643C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E416C6C207468652074696D653C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22747874526561644D6F72652220747970653D2274657874222064656661756C743D2252656164204D6F72652E2E2E22206C6162656C3D2252656164204D6F7265207465787422206465736372697074696F6E3D22456E74657220746865202671756F743B52656164204D6F72652E2E2671756F743B20746578742E22202F3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 2733 WHERE ft_id = 21 AND filename = 'params.xml' LIMIT 1");
		
		// Update coredesc version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "21" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coredesc field type to version 2.' );

		// Update corefeatured class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corefeatured extends mFieldType {\r\n	var $name = \'link_featured\';\r\n	var $numOfInputFields = 0;\r\n	function getOutput() {\r\n		$featured = $this->getValue();\r\n		$html = \'\';\r\n		if($featured) {\r\n			$html .= JText::_( \'Yes\' );\r\n		} else {\r\n			$html .= JText::_( \'No\' );\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		$html = \'<select name=\"\' . $this->getSearchFieldName(1) . \'\" class=\"inputbox text_area\" size=\"1\">\';\r\n		$html .= \'<option value=\"-1\" selected=\"selected\">\' . JText::_( \'Any\' ) . \'</option>\';\r\n		$html .= \'<option value=\"1\">\' . JText::_( \'FEATURED ONLY\' ) . \'</option>\';\r\n		$html .= \'<option value=\"0\">\' . JText::_( \'NON FEATURED ONLY\' ) . \'</option>\';\r\n		$html .= \'</select>\';\r\n		return $html;\r\n	}\r\n	\r\n	function getWhereCondition() {\r\n		$args = func_get_args();\r\n\r\n		$fieldname = $this->getName();\r\n		\r\n		if(  is_numeric($args[0]) ) {\r\n			switch($args[0]) {\r\n				case -1:\r\n					return null;\r\n					break;\r\n				case 1:\r\n					return $fieldname . \' = 1\';\r\n					break;\r\n				case 0:\r\n				return $fieldname . \' = 0\';\r\n					break;\r\n			}\r\n		} else {\r\n			return null;\r\n		}\r\n	}\r\n}"  WHERE field_type = "corefeatured" LIMIT 1');
		$database->execute();

		// Update corefeatured version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "14" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corefeatured field type to version 2.' );

		// Update corename class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corename extends mFieldType {\r\n	var $name = \'link_name\';\r\n	function getOutput($view=1) {\r\n		$params[\'maxSummaryChars\'] = intval($this->getParam(\'maxSummaryChars\',55));\r\n		$params[\'maxDetailsChars\'] = intval($this->getParam(\'maxDetailsChars\',0));\r\n		$value = $this->getValue();\r\n		$output = \'\';\r\n		if($view == 1 AND $params[\'maxDetailsChars\'] > 0 AND JString::strlen($value) > $params[\'maxDetailsChars\']) {\r\n			$output .= JString::substr($value,0,$params[\'maxDetailsChars\']);\r\n			$output .= \'...\';\r\n		} elseif($view == 2 AND $params[\'maxSummaryChars\'] > 0 AND JString::strlen($value) > $params[\'maxSummaryChars\']) {\r\n			$output .= JString::substr($value,0,$params[\'maxSummaryChars\']);\r\n			$output .= \'...\';\r\n		} else {\r\n			$output = $value;\r\n		}\r\n		return $output;\r\n	}\r\n}"  WHERE field_type = "corename" LIMIT 1');
		$database->execute();

		// Update corename version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "20" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corename field type to version 2.' );

		// Update coreprice class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coreprice extends mFieldType_number {\r\n	var $name = \'price\';\r\n	function getOutput() {\r\n		$price = $this->getValue();\r\n		$displayFree = $this->getParam(\'displayFree\',1);\r\n		if($price == 0 && $displayFree == 1) {\r\n			return JText::_( \'FREE\' );\r\n		} else {\r\n			return $price;\r\n		}\r\n	}\r\n}"  WHERE field_type = "coreprice" LIMIT 1');
		$database->execute();

		// Update coreprice version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "2" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coreprice field type to version 2.' );

		// Update coreuser class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coreuser extends mFieldType {\r\n	var $name = \'user_id\';\r\n	var $numOfSearchFields = 0;\r\n	var $numOfInputFields = 0;\r\n	\r\n	function getOutput() {\r\n		$html = \'<a href=\"\' . JRoute::_(\'index.php?option=com_mtree&amp;task=viewowner&amp;user_id=\' . $this->getValue(1)) . \'\">\';\r\n		$html .= $this->getValue(2);\r\n		$html .= \'</a>\';\r\n		return $html;\r\n	}\r\n}\r\n"  WHERE field_type = "coreuser" LIMIT 1');
		$database->execute();

		// Update coreuser version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "19" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coreuser field type to version 2.' );

		// Update corewebsite class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corewebsite extends mFieldType_weblink {\r\n	var $name = \'website\';\r\n\r\n	function getOutput() {\r\n		$maxUrlLength = $this->getParam(\'maxUrlLength\',60);\r\n		$text = $this->getParam(\'text\',\'\');\r\n		$openNewWindow = $this->getParam(\'openNewWindow\',1);\r\n		$useMTVisitRedirect = $this->getParam(\'useMTVisitRedirect\',1);\r\n		$hideProtocolOutput = $this->getParam(\'hideProtocolOutput\',1);\r\n	\r\n		$html = \'\';\r\n		$html .= \'<a href=\"\';\r\n		if($useMTVisitRedirect) {\r\n			global $Itemid;\r\n			$html .= JRoute::_(\'index.php?option=com_mtree&task=visit&link_id=\' . $this->getLinkId() . \'&Itemid=\' . $Itemid);\r\n		} else {\r\n			$html .= $this->getValue();\r\n		}\r\n		$html .= \'\"\';\r\n		if( $openNewWindow == 1 ) {\r\n			$html .= \' target=\"_blank\"\';\r\n		}\r\n		$html .= \'>\';\r\n		if(!empty($text)) {\r\n			$html .= $text;\r\n		} else {\r\n			$value = $this->getValue();\r\n			if(strpos($value,\'://\') !== false && $hideProtocolOutput) {\r\n				$value = substr($value,(strpos($value,\'://\')+3));\r\n\r\n				// If $value has a single slash and this is at the end of the string, we can safely remove this.\r\n				if( substr($value,-1) == \'/\' && substr_count($value,\'/\') == 1 )\r\n				{\r\n					$value = substr($value,0,-1);\r\n				}\r\n			}\r\n			if( empty($maxUrlLength) || $maxUrlLength == 0 ) {\r\n				$html .= $value;\r\n			} else {\r\n				$html .= substr($value,0,$maxUrlLength);\r\n				if( strlen($value) > $maxUrlLength ) {\r\n					$html .= $this->getParam(\'clippedSymbol\');\r\n				}\r\n			}\r\n		}\r\n		$html .= \'</a>\';\r\n		return $html;\r\n	}\r\n	\r\n	function getInputHTML() {\r\n		$showGo = $this->getParam(\'showGo\',1);\r\n		$showSpider = $this->getParam(\'showSpider\',0);\r\n		$inBackEnd = (substr(dirname($_SERVER[\'PHP_SELF\']),-13) == \'administrator\') ? true : false;\r\n		$html = \'\';\r\n		$html .= \'<input class=\"text_area inputbox\" type=\"text\" name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" size=\"\' . ($this->getSize()?$this->getSize():\'30\') . \'\" value=\"\' . htmlspecialchars($this->getValue()) . \'\" />\';\r\n		if($showGo && $inBackEnd) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript:window.open(\"index3.php?option=com_mtree&task=openurl&url=\"+escape(document.getElementById(\"website\").value))\\\\\'\';\r\n			$html .= \'value=\"\' . JText::_( \'Go\' ) . \'\" />\';\r\n		}\r\n		\r\n		if($showSpider && $inBackEnd) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript: \';\r\n			$html .= \'jQuery(\"#spiderwebsite\").html(\"\' . JText::_( \'SPIDER PROGRESS\' ) . \'\");\';\r\n			$html .= \'jQuery.ajax({\r\n			  type: \"POST\",\r\n			  url: mosConfig_live_site+\"/administrator/index2.php\",\r\n			  data: \"option=com_mtree&task=ajax&task2=spiderurl&url=\"+document.getElementById(\"website\").value+\"&no_html=1\",\r\n			  dataType: \"script\"\r\n			});\';\r\n			$html .= \'\\\\\'\';\r\n			$html .= \'value=\"\' . JText::_( \'SPIDER\' ) . \'\" />\';\r\n			$html .= \'<span id=\"spider\' . $this->getInputFieldName(1) . \'\" style=\"margin-left:5px;background-color:white\"></span>\';\r\n		}\r\n		return $html;\r\n	}\r\n	\r\n}"  WHERE field_type = "corewebsite" LIMIT 1');
		$database->execute();
		
		// Update corewebsite version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "11" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corewebsite field type to version 2.' );

		// Update corecountry class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corecountry extends mFieldType {\r\n	var $name = \'country\';\r\n	function getInputHTML() {\r\n		if( !empty($this->arrayFieldElements[0]) )\r\n		{\r\n			$html = \'<select name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" class=\"inputbox text_area\">\';\r\n			$html .= \'<option value=\"\">&nbsp;</option>\';\r\n			foreach($this->arrayFieldElements AS $fieldElement) {\r\n				$html .= \'<option value=\"\'.$fieldElement.\'\"\';\r\n				if( $fieldElement == $this->getValue() ) {\r\n					$html .= \' selected\';\r\n				}\r\n				$html .= \'>\' . $fieldElement . \'</option>\';\r\n			}\r\n			$html .= \'</select>\';\r\n			return $html;\r\n		} else {\r\n			return \'<input class=\"inputbox text_area\" type=\"text\" name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" size=\"\' . ($this->getSize()?$this->getSize():\'30\') . \'\" value=\"\' . htmlspecialchars($this->getValue()) . \'\" />\';\r\n		}\r\n	}\r\n	function getSearchHTML() {\r\n		$html = \'<select name=\"\' . $this->getName() . \'\" class=\"inputbox text_area\">\';\r\n		$html .= \'<option value=\"\">&nbsp;</option>\';\r\n		foreach($this->arrayFieldElements AS $fieldElement) {\r\n			$html .= \'<option value=\"\'.$fieldElement.\'\"\';\r\n			$html .= \'>\' . $fieldElement . \'</option>\';\r\n		}\r\n		$html .= \'</select>\';\r\n		return $html;\r\n	}\r\n	function getOutput() {\r\n		if( $this->tagSearch && $this->hasValue() )\r\n		{\r\n			$html = \'\';\r\n			$html .= \'<a class=\"tag\" href=\"\';\r\n			$html .= JRoute::_(\'index.php?option=com_mtree&task=searchby&cf_id=\'.$this->getId().\'&value=\'.$this->getValue());\r\n			$html .= \'\">\';\r\n			$html .= $this->getValue();\r\n			$html .= \'</a>\';\r\n			return $html;\r\n		} else {\r\n			return $this->getValue();\r\n		}\r\n	}\r\n}"  WHERE field_type = "corecountry" LIMIT 1');
		$database->execute();
		
		// Update corecountry version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "6" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corecountry field type to version 2.' );
		
		// Update image class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_image extends mFieldType_file {\r\n	function parseValue($value) {\r\n		global $mtconf;\r\n		$params[\'size\'] = intval(trim($this->getParam(\'size\')));\r\n		if($params[\'size\'] <= 0) {\r\n			$size = $mtconf->get(\'resize_listing_size\');\r\n		} else {\r\n			$size = intval($params[\'size\']);\r\n		}\r\n		$mtImage = new mtImage();\r\n		$mtImage->setMethod( $mtconf->get(\'resize_method\') );\r\n		$mtImage->setQuality( $mtconf->get(\'resize_quality\') );\r\n		$mtImage->setSize( $size );\r\n		$mtImage->setTmpFile( $value[\'tmp_name\'] );\r\n		$mtImage->setType( $value[\'type\'] );\r\n		$mtImage->setName( $value[\'name\'] );\r\n		$mtImage->setSquare(false);\r\n		$mtImage->resize();\r\n		$value[\'data\'] = $mtImage->getImageData();\r\n		$value[\'size\'] = strlen($value[\'data\']);\r\n		\r\n		return $value;\r\n	}\r\n	function getJSValidation() {\r\n		$js = \'\';\r\n		$js .= \'} else if (!hasExt(form.\' .$this->getInputFieldName(1) . \'.value,\\\\\'gif|png|jpg|jpeg\\\\\')) {\'; \r\n		$js .= \'alert(\"\' . addslashes($this->getCaption()) . \': Please select an image with one of these extensions - gif,png,jpg,jpeg.\");\';\r\n		return $js;\r\n	}\r\n	function getOutput() {\r\n		$html = \'\';\r\n		$html .= \'<img src=\"\' . $this->getDataAttachmentURL() . \'\" />\';\r\n		return $html;\r\n	}\r\n	function getInputHTML() {\r\n		$html = \'\';\r\n		if( $this->attachment > 0 ) {\r\n			$html .= $this->getKeepFileCheckboxHTML($this->attachment);\r\n			$html .= \'<label for=\"\' . $this->getKeepFileName() . \'\"><img src=\"\' . $this->getDataAttachmentURL() . \'\" hspace=\"5\" vspace=\"0\" /></label>\';\r\n			$html .= \'</br >\';\r\n		}\r\n		$html .= \'<input class=\"inputbox\" type=\"file\" name=\"\' . $this->getInputFieldName(1) . \'\" />\';\r\n		return $html;\r\n	}\r\n	\r\n}"  WHERE field_type = "image" LIMIT 1');
		$database->execute();

		// Update image version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "25" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated image field type to version 3.' );

		// Update multilineTextbox class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_multilineTextbox extends mFieldType {\r\n	function parseValue($value) {\r\n		$params[\'stripAllTagsBeforeSave\'] = $this->getParam(\'stripAllTagsBeforeSave\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		if($params[\'stripAllTagsBeforeSave\']) {\r\n			$value = $this->stripTags($value,$params[\'allowedTags\']);\r\n		}\r\n		return $value;		\r\n	}\r\n	function getInputHTML() {\r\n		$params[\'cols\'] = $this->getParam(\'cols\',60);\r\n		$params[\'rows\'] = $this->getParam(\'rows\',6);\r\n		$params[\'style\'] = $this->getParam(\'style\',\'\');\r\n		$html = \'\';\r\n		$html .= \'<textarea name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" class=\"inputbox\"\';\r\n		$html .= \' cols=\"\' . $params[\'cols\'] . \'\" rows=\"\' . $params[\'rows\'] . \'\"\';\r\n		if(!empty($params[\'style\'])) {\r\n			$html .=  \' style=\"\' . $params[\'style\'] . \'\"\';\r\n		}\r\n		$html .=  \'>\' . $this->getValue() . \'</textarea>\';\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'<input class=\"inputbox\" type=\"text\" name=\"\' . $this->getName() . \'\" size=\"30\" />\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'parseUrl\'] = $this->getParam(\'parseUrl\',1);\r\n		$params[\'summaryChars\'] = $this->getParam(\'summaryChars\',255);\r\n		$params[\'stripSummaryTags\'] = $this->getParam(\'stripSummaryTags\',1);\r\n		$params[\'stripDetailsTags\'] = $this->getParam(\'stripDetailsTags\',1);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n	\r\n		$html = $this->getValue();\r\n	\r\n		// Details view\r\n		if($view == 1) {\r\n			if($params[\'stripDetailsTags\']) {\r\n				$html = $this->stripTags($html,$params[\'allowedTags\']);\r\n			}\r\n			if($params[\'parseUrl\'] AND $view == 0) {\r\n				$regex = \'/http:\\\\/\\\\/(.*?)(\\\\s|$)/i\';\r\n				$html = preg_replace_callback( $regex, array($this,\'linkcreator\'), $html );\r\n			}\r\n		// Summary view\r\n		} else {\r\n			$html = preg_replace(\'@{[\\\\/\\\\!]*?[^<>]*?}@si\', \'\', $html);\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = strip_tags( $html );\r\n			} else {\r\n			}\r\n			$trimmed_desc = trim(JString::substr($html,0,$params[\'summaryChars\']));\r\n			if (JString::strlen($html) > $params[\'summaryChars\']) {\r\n				$html = $trimmed_desc . \' <b>...</b>\';\r\n			}\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "multilineTextbox" LIMIT 1');
		$database->execute();

		// Update multilineTextbox version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "26" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated multilineTextbox field type to version 2.' );

		// Update corecreated params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D227374617274596561722220747970653D2274657874222064656661756C743D2222206C6162656C3D225374617274207965617222206465736372697074696F6E3D22456E74657220746865207374617274696E672079656172206F72206561726C69657374207965617220617661696C61626C6520666F722073656C656374696F6E2E204966206C65667420656D7074792C2069742077696C6C2064656661756C7420746F2037302079656172732061676F2066726F6D207468652063757272656E7420796561722E22202F3E0A09093C706172616D206E616D653D22656E64596561722220747970653D2274657874222064656661756C743D2222206C6162656C3D22456E64207965617222206465736372697074696F6E3D22456E74657220746865206C61746573742079656172206F7220617661696C61626C6520666F722073656C656374696F6E2E204966206C65667420656D7074792C207468652063757272656E7420796561722077696C6C20626520757365642E22202F3E0A09093C706172616D206E616D653D2264617465466F726D61742220747970653D226C697374222064656661756C743D2222206C6162656C3D224461746520466F726D617422203E0A0909093C6F7074696F6E2076616C75653D2225592D256D2D2564223E323030372D30362D30313C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2225642E256D2E2559223E30312E30362E323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D222564202542202559223E3031204A756E6520323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2225642F256D2F2559223E30312F30362F323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D22256D2F25642F2559223E30362F30312F323030373C2F6F7074696F6E3E0A09093C2F706172616D3E09090A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 784 WHERE ft_id = 22 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Update coremodified params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D227374617274596561722220747970653D2274657874222064656661756C743D2222206C6162656C3D225374617274207965617222206465736372697074696F6E3D22456E74657220746865207374617274696E672079656172206F72206561726C69657374207965617220617661696C61626C6520666F722073656C656374696F6E2E204966206C65667420656D7074792C2069742077696C6C2064656661756C7420746F2037302079656172732061676F2066726F6D207468652063757272656E7420796561722E22202F3E0A09093C706172616D206E616D653D22656E64596561722220747970653D2274657874222064656661756C743D2222206C6162656C3D22456E64207965617222206465736372697074696F6E3D22456E74657220746865206C61746573742079656172206F7220617661696C61626C6520666F722073656C656374696F6E2E204966206C65667420656D7074792C207468652063757272656E7420796561722077696C6C20626520757365642E22202F3E0A09093C706172616D206E616D653D2264617465466F726D61742220747970653D226C697374222064656661756C743D2222206C6162656C3D224461746520466F726D617422203E0A0909093C6F7074696F6E2076616C75653D2225592D256D2D2564223E323030372D30362D30313C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2225642E256D2E2559223E30312E30362E323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D222564202542202559223E3031204A756E6520323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2225642F256D2F2559223E30312F30362F323030373C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D22256D2F25642F2559223E30362F30312F323030373C2F6F7074696F6E3E0A09093C2F706172616D3E09090A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 784 WHERE ft_id = 15 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Update mfile params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D7320616464706174683D222F61646D696E6973747261746F722F636F6D706F6E656E74732F636F6D5F6D747265652F656C656D656E7473223E200A09093C706172616D206E616D653D2266696C65457874656E73696F6E732220747970653D22706970656474657874222064656661756C743D2222206C6162656C3D2241636365707461626C652066696C6520657874656E73696F6E7322206465736372697074696F6E3D22456E746572207468652061636365707461626C652066696C652074797065206F6620657874656E73696F6E20666F722074686973206669656C642E20496620796F752068617665206D6F7265207468616E206F6E6520657874656E73696F6E2C20706C656173652073657065726174652074686520657874656E73696F6E207769746820612062617220277C272E204578616D706C653A20276769667C706E677C6A70677C6A70656727206F722027706466272E20506C6561736520646F206E6F74207374617274206F7220656E64207468652076616C756520776974682061206261722E2022202F3E0A09093C706172616D206E616D653D2273686F77436F756E7465722220747970653D22726164696F222064656661756C743D223122206C6162656C3D2253686F7720636F756E746572223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22757365496D6167652220747970653D2274657874222064656661756C743D2222206C6162656C3D2255736520496D61676522206465736372697074696F6E3D22456E746572207468652055524C20746F2074686520696D61676520796F7520776F756C64206C696B6520746F2075736520746F206C696E6B20746F20616E2075706C6F616465642066696C652E20596F752063616E20757365207B6C6976655F736974657D20617320746865207265706C6163656D656E7420666F72207468652076616C7565206F662073697465277320646F6D61696E2E2069653A207B6C6976655F736974657D2F696D616765732F736176655F66322E706E6722202F3E0A09093C706172616D206E616D653D2273686F77546578742220747970653D2274657874222064656661756C743D2222206C6162656C3D2253686F77205465787422202F3E0A09093C706172616D206E616D653D2273686F7746696C656E616D652220747970653D22726164696F222064656661756C743D223122206C6162656C3D2253686F772046696C656E616D6522206465736372697074696F6E3D225468697320616C6C6F777320796F7520746F2068696465207468652066696C656E616D65206C696E6B2E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 1162 WHERE ft_id = 48 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Update corewebsite params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D226F70656E4E657757696E646F772220747970653D22726164696F222064656661756C743D223122206C6162656C3D224F70656E204E65772057696E646F7722206465736372697074696F6E3D224F70656E2061206E65772077696E646F77207768656E20746865206C696E6B20697320636C69636B65642E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D227573654D54566973697452656469726563742220747970653D22726164696F222064656661756C743D223122206C6162656C3D2255736520696E7465726E616C20726564697265637422206465736372697074696F6E3D225573696E6720696E7465726E65742072656469726563742077696C6C206272696E672076697369746F7273207468726F75676820616E20696E7465726E616C2055524C206265666F7265207265646972656374696E67207468656D20746F207468652061637475616C20776562736974652E205468697320616C6C6F7773204D6F73657473205472656520746F206B65657020747261636B206F6620746865206869747320616E64206869646573207468652061637475616C792055524C2066726F6D2076697369746F722E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22746578742220747970653D2274657874222064656661756C743D2222206C6162656C3D224C696E6B205465787422206465736372697074696F6E3D22557365207468697320706172616D6574657220746F207370656369667920746865206C696E6B20746578742E204966206C65667420656D7074792C207468652066756C6C2055524C2077696C6C20626520646973706C6179656420617320746865206C696E6B277320746578742E22202F3E0A09093C706172616D206E616D653D226D617855726C4C656E6774682220747970653D2274657874222064656661756C743D22363022206C6162656C3D224D61782E2055524C204C656E67746822206465736372697074696F6E3D22456E74657220746865206D6178696D756D2055524C2773206C656E677468206265666F726520697420697320636C697070656422202F3E0A09093C706172616D206E616D653D22636C697070656453796D626F6C2220747970653D2274657874222064656661756C743D222E2E2E22206C6162656C3D22436C69707065642073796D626F6C22202F3E0A0A09093C706172616D206E616D653D226869646550726F746F636F6C4F75747075742220747970653D22726164696F222064656661756C743D223122206C6162656C3D22486964652027687474703A2F2F2720647572696E67206F7574707574223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D2273686F77476F2220747970653D22726164696F222064656661756C743D223122206C6162656C3D2253686F7720476F20627574746F6E22206465736372697074696F6E3D225468697320476F20627574746F6E2077696C6C20626520617661696C61626C6520696E20746865206261636B2D656E642045646974204C697374696E67207061676520746F20616C6C6F772061646D696E206120666173742077617920746F206F70656E20746865206C697374696E67277320776562736974652E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D2273686F775370696465722220747970653D22726164696F222064656661756C743D223022206C6162656C3D2253686F772053706964657220627574746F6E22206465736372697074696F6E3D225768656E20656E61626C65642C20612053706964657220627574746F6E2077696C6C20626520617661696C61626C65206E65787420746F20746865207765627369746520696E707574206669656C6420696E206261636B2D656E642E205768656E2074686520627574746F6E20697320636C69636B65642C2069742077696C6C20636865636B20746865207765627369746520696E20746865206261636B67726F756E20616E6420706F70756C61746520746865206C697374696E672773204D455441204B65797320616E64204D455441204465736372697074696F6E206669656C642E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 1948 WHERE ft_id = 11 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Update weblinknewwin params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D226F70656E4E657757696E646F772220747970653D22726164696F222064656661756C743D223122206C6162656C3D224F70656E204E65772057696E646F7722206465736372697074696F6E3D224F70656E2061206E65772077696E646F77207768656E20746865206C696E6B20697320636C69636B65642E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D22746578742220747970653D2274657874222064656661756C743D2222206C6162656C3D224C696E6B205465787422206465736372697074696F6E3D22557365207468697320706172616D6574657220746F207370656369667920746865206C696E6B20746578742E204966206C65667420656D7074792C207468652066756C6C2055524C2077696C6C20626520646973706C6179656420617320746865206C696E6B277320746578742E22202F3E0A09093C706172616D206E616D653D226D617855726C4C656E6774682220747970653D2274657874222064656661756C743D22363022206C6162656C3D224D61782E2055524C204C656E67746822206465736372697074696F6E3D22456E74657220746865206D6178696D756D2055524C2773206C656E677468206265666F726520697420697320636C697070656422202F3E0A09093C706172616D206E616D653D22636C697070656453796D626F6C2220747970653D2274657874222064656661756C743D222E2E2E22206C6162656C3D22436C69707065642073796D626F6C22202F3E0A09093C706172616D206E616D653D22757365496E7465726E616C52656469726563742220747970653D22726164696F222064656661756C743D223022206C6162656C3D2255736520696E7465726E616C20726564697265637422206465736372697074696F6E3D225573696E6720696E7465726E616C2072656469726563742077696C6C2068696465207468652061637475616C2064657374696E6174696F6E2055524C20616E642075736520616E20696E7465726E616C2055524C20746F20726564697265637420757365727320746F207468652061637475616C2055524C2E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D226869646550726F746F636F6C4F75747075742220747970653D22726164696F222064656661756C743D223122206C6162656C3D22486964652027687474703A2F2F2720647572696E67206F7574707574223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A09093C706172616D206E616D653D2273686F77476F2220747970653D22726164696F222064656661756C743D223022206C6162656C3D2253686F7720476F20627574746F6E22206465736372697074696F6E3D225468697320476F20627574746F6E2077696C6C20626520617661696C61626C6520696E20746865206261636B2D656E642045646974204C697374696E67207061676520746F20616C6C6F772061646D696E206120666173742077617920746F206F70656E20746865206C697374696E67277320776562736974652E223E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A09093C2F706172616D3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 1464 WHERE ft_id = 23 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Change Admin Icon to Mosets icon
		$database->setQuery("UPDATE #__components SET admin_menu_img='../components/com_mtree/img/icon-16-mosetstree.png' WHERE admin_menu_link='option=com_mtree'");
		$database->execute();

		updateVersion(2,1,0);
		$this->updated = true;
		return true;
	}
	
	function populate_listing_alias() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT link_id, link_name FROM #__mt_links' );
		$listings = $db->loadObjectList();
		
		$aliased = 0;
		
		if( !empty($listings) )
		{
			foreach( $listings AS $listing )
			{
				$alias = JFilterOutput::stringURLSafe($listing->link_name);
				$db->setQuery( 'UPDATE #__mt_links SET alias = ' . $db->quote($alias) . ' WHERE link_id = ' . $db->quote($listing->link_id) . ' LIMIT 1' );
				$db->execute();
				$aliased++;
			}
		}
		return $aliased;
	}
		
	function populate_category_alias() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats' );
		$categories = $db->loadObjectList();
		
		$aliased = 0;
		
		if( !empty($categories) )
		{
			foreach( $categories AS $cat )
			{
				$alias = JFilterOutput::stringURLSafe($cat->cat_name);
				$db->setQuery( 'UPDATE #__mt_cats SET alias = ' . $db->quote($alias) . ' WHERE cat_id = ' . $db->quote($cat->cat_id) . ' LIMIT 1' );
				$db->execute();
				$aliased++;
			}
		}
		return $aliased;
	}
	
	function move_attachments() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT * FROM #__mt_cfvalues_att WHERE raw_filename = \'\' LIMIT 0, 50' );
		$atts = $db->loadObjectList();

		foreach( $atts AS $att )
		{
			$file_extension = strrchr($att->filename,'.');
			if( $file_extension === false ) {
				$file_extension = '';
			}
			
			$filepath = JPATH_COMPONENT_SITE.'/attachments/'.$att->att_id.$file_extension;
			$handle = fopen($filepath, 'w');
			if (fwrite($handle, $att->filedata, $att->filesize) === FALSE) {
				printRow( 'Cannot write to file at ' . $filepath, 1 );
		    } else {
				printRow( 'Attachment saved to ' . $filepath );
				$db->setQuery( 'UPDATE #__mt_cfvalues_att SET raw_filename = \'' . $att->att_id.$file_extension . '\' WHERE att_id = '.$att->att_id.' LIMIT 1' );
				$db->execute();
			}
		}
	}
	
	function attachments_is_writable() {
		global $mtconf;
		if( is_writable( JPATH_SITE . '/components/com_mtree/attachments/' ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	function get_total_attachments() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_cfvalues_att' );
		return $db->loadResult();
	}
	
	function hasMovableAttachments() {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_cfvalues_att WHERE raw_filename = \'\'' );
		return $db->loadResult();
	}
}
?>