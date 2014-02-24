CREATE TABLE IF NOT EXISTS `#__mt_archived_log` (
  `log_id` int(10) unsigned NOT NULL,
  `log_ip` varchar(255) NOT NULL default '',
  `log_type` varchar(32) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `link_id` int(11) NOT NULL default '0',
  `rev_id` int(11) NOT NULL default '0',
  `value` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`log_id`),
  KEY `link_id2` (`link_id`,`log_ip`),
  KEY `link_id1` (`link_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `log_type` (`log_type`),
  KEY `log_ip` (`log_ip`,`user_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_archived_reviews` (
  `rev_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `guest_name` varchar(255) NOT NULL default '',
  `rev_title` varchar(255) NOT NULL default '',
  `rev_text` text NOT NULL,
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `rev_approved` tinyint(4) NOT NULL default '1',
  `admin_note` mediumtext NOT NULL,
  `vote_helpful` int(10) unsigned NOT NULL default '0',
  `vote_total` int(10) unsigned NOT NULL default '0',
  `ownersreply_text` text NOT NULL,
  `ownersreply_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ownersreply_approved` tinyint(4) NOT NULL default '0',
  `ownersreply_admin_note` mediumtext NOT NULL,
  `send_email` tinyint(4) NOT NULL,
  `email_message` mediumtext NOT NULL,
  PRIMARY KEY  (`rev_id`),
  KEY `link_id` (`link_id`,`rev_approved`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_archived_users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `username` varchar(25) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_cats` (
  `cat_id` int(11) NOT NULL auto_increment,
  `cat_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_parent` int(11) NOT NULL default '0',
  `cat_links` int(11) NOT NULL default '0',
  `cat_cats` int(11) NOT NULL default '0',
  `cat_featured` tinyint(4) NOT NULL default '0',
  `cat_image` varchar(255) NOT NULL,
  `cat_association` int(11) DEFAULT NULL,
  `cat_published` tinyint(4) NOT NULL default '0',
  `cat_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `cat_approved` tinyint(4) NOT NULL default '0',
  `cat_template` varchar(255) NOT NULL default '',
  `cat_usemainindex` tinyint(4) NOT NULL default '0',
  `cat_allow_submission` tinyint(4) NOT NULL default '1',
  `cat_show_listings` tinyint(3) unsigned NOT NULL default '1',
  `metadata` text NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_id` (`cat_id`,`cat_published`,`cat_approved`),
  KEY `cat_parent` (`cat_parent`,`cat_published`,`cat_approved`,`cat_cats`,`cat_links`),
  KEY `dtree` (`cat_published`,`cat_approved`),
  KEY `lft_rgt` (`lft`,`rgt`),
  KEY `func_getPathWay` (`lft`,`rgt`,`cat_id`,`cat_parent`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_cfvalues` (
  `id` int(11) NOT NULL auto_increment,
  `cf_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `value` mediumtext NOT NULL,
  `attachment` int(10) unsigned NOT NULL default '0',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cf_id` (`cf_id`,`link_id`),
  KEY `link_id` (`link_id`),
  KEY `value` (`value`(8))
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_cfvalues_att` (
  `att_id` int(10) unsigned NOT NULL auto_increment,
  `link_id` int(10) unsigned NOT NULL,
  `cf_id` int(10) unsigned NOT NULL,
  `raw_filename` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `extension` varchar(255) NOT NULL,
  PRIMARY KEY  (`att_id`),
  KEY `primary2` (`link_id`,`cf_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_cl` (
  `cl_id` int(11) NOT NULL auto_increment,
  `link_id` int(11) NOT NULL default '0',
  `cat_id` int(11) NOT NULL default '0',
  `main` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`cl_id`),
  KEY `link_id` (`link_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_claims` (
  `claim_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `comment` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `admin_note` mediumtext NOT NULL,
  PRIMARY KEY  (`claim_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_clone_owners` (
  `user_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_config` (
  `varname` varchar(100) NOT NULL,
  `groupname` varchar(50) NOT NULL,
  `value` mediumtext NOT NULL,
  `default` mediumtext NOT NULL,
  `configcode` mediumtext NOT NULL,
  `ordering` smallint(6) NOT NULL,
  `displayed` smallint(5) unsigned NOT NULL default '1',
  `overridable_by_category` smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY  (`varname`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__mt_config` VALUES('admin_email', 'main', '', '', 'text', 500, 1, 1);
INSERT INTO `#__mt_config` VALUES('template', 'main', 'kinabalu', 'kinabalu', 'text', 200, 0, 1);
INSERT INTO `#__mt_config` VALUES('admin_use_explorer', 'admin', '1', '1', 'yesno', 11500, 1, 0);
INSERT INTO `#__mt_config` VALUES('small_image_click_target_size', 'admin', '', 'o', 'text', 13000, 0, 0);
INSERT INTO `#__mt_config` VALUES('allow_changing_cats_in_addlisting', 'listing', '1', '1', 'yesno', 3550, 1, 1);
INSERT INTO `#__mt_config` VALUES('allow_imgupload', 'image', '1', '1', 'yesno', 10100, 1, 1);
INSERT INTO `#__mt_config` VALUES('image_required', 'image', '', '0', 'yesno', 10150, 1, 1);
INSERT INTO `#__mt_config` VALUES('allow_listings_submission_in_root', 'listing', '0', '0', 'yesno', 3500, 1, 1);
INSERT INTO `#__mt_config` VALUES('allow_rating_during_review', 'ratingreview', '1', '1', 'yesno', 2650, 1, 1);
INSERT INTO `#__mt_config` VALUES('allow_user_assign_more_than_one_category', 'listing', '1', '1', 'yesno', 3575, 1, 1);
INSERT INTO `#__mt_config` VALUES ('max_num_of_secondary_categories', 'listing', '3', '3', 'text', '3580', '1', '1');
INSERT INTO `#__mt_config` VALUES('alpha_index_additional_chars', 'listing', '', '', 'text', 3410, 1, 1);
INSERT INTO `#__mt_config` VALUES('type_of_listings_in_index', 'category', 'listcurrent', '', 'type_of_listings_in_index', '3150', '1', '1');
INSERT INTO `#__mt_config` VALUES('type_of_listings_in_index_count', 'category', '3', '3', 'text', '3175', '1', '1');
INSERT INTO `#__mt_config` VALUES('display_categories', 'category', '1', '1', 'yesno', '3200', '1', '1');
INSERT INTO `#__mt_config` VALUES('display_empty_cat', 'category', '1', '1', 'yesno', 3300, 1, 1);
INSERT INTO `#__mt_config` VALUES('display_all_listings_link', 'category', '1', '1', 'yesno', 3400, 1, 1);
INSERT INTO `#__mt_config` VALUES('display_filters', 'category', '1', '1', 'yesno', 3500, 1, 1);
INSERT INTO `#__mt_config` VALUES('display_listings_in_root', 'listing', '1', '1', 'yesno', 3600, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_listing_badge_new', 'listing', '1', '1', 'yesno', 3610, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_listing_badge_featured', 'listing', '1', '1', 'yesno', 3620, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_listing_badge_popular', 'listing', '1', '1', 'yesno', 3630, 1, 1);
INSERT INTO `#__mt_config` VALUES('explorer_tree_level', 'admin', '4', '9', 'text', 11600, 1, 0);
INSERT INTO `#__mt_config` VALUES('fe_num_of_featured', 'listing', '20', '20', 'text', 6700, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_links', 'listing', '20', '20', 'text', 5500, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_associated', 'listing', '', '10', 'text', 5550, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_favourite', 'listing', '20', '20', 'text', 6100, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_mostrated', 'listing', '20', '20', 'text', 6300, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_mostreview', 'listing', '20', '20', 'text', 6500, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_new', 'listing', '20', '20', 'text', 5800, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_popular', 'listing', '20', '20', 'text', 5700, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_updated', 'listing', '20', '20', 'text', 6000, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_reviews', 'listing', '20', '20', 'text', 5600, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_reviews_in_listing_page', 'listing', '3', '3', 'text', 5610, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_toprated', 'listing', '20', '20', 'text', 6400, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_searchby', 'listing', '100', '100', 'text', 9990, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_searchbytags', 'listing', '100', '100', 'text', 9999, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_alpha', 'listing', '20', '20', 'text', 6725, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_num_of_all', 'listing', '20', '20', 'text', 6050, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_new', 'listing', '100', '60', 'text', 5900, 1, 1);
INSERT INTO `#__mt_config` VALUES('all_listings_sort_by', 'listing', '', '-link_created', 'sort', 1, 1, 1);
INSERT INTO `#__mt_config` VALUES('all_listings_sort_by_options', 'listing', '', '-link_created|-link_modified', 'sort_options', 2, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_cat_order1', 'category', 'cat_name', 'cat_name', 'cat_order', 1400, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_cat_order2', 'category', 'asc', 'asc', 'sort_direction', 1500, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_listing_order1', 'listing', 'link_featured', 'link_rating', 'listing_order', 1800, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_listing_order2', 'listing', 'desc', 'desc', 'sort_direction', 1900, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_review_order1', 'ratingreview', 'rev_date', 'vote_helpful', 'review_order', 2900, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_review_order2', 'ratingreview', 'desc', 'desc', 'sort_direction', 3000, 1, 1);
INSERT INTO `#__mt_config` VALUES ('note_simple_search',  'search',  '',  '',  'note',  '2100',  '1',  '1'	);
INSERT INTO `#__mt_config` VALUES('first_search_order1', 'search', 'link_featured', 'link_featured', 'listing_order', 2150, 1, 1);
INSERT INTO `#__mt_config` VALUES('first_search_order2', 'search', 'desc', 'desc', 'sort_direction', 2151, 1, 1);
INSERT INTO `#__mt_config` VALUES ('note_advanced_search',  'search',  '',  '',  'note',  '2200',  '1',  '1');
INSERT INTO `#__mt_config` VALUES ('advanced_search_sort_by',  'search',  '',  '-link_featured',  'sort',  '2250',  '1',  '1');
INSERT INTO `#__mt_config` VALUES('hit_lag', 'main', '86400', '86400', 'text', 9000, 1, 0);
INSERT INTO `#__mt_config` VALUES('images_per_listing', 'image', '8', '10', 'text', 10200, 1, 1);
INSERT INTO `#__mt_config` VALUES('image_min_width', 'image', '', '300', 'text', '1060', '1', '0');
INSERT INTO `#__mt_config` VALUES('image_min_height', 'image', '', '300', 'text', '1070', '1', '0');
INSERT INTO `#__mt_config` VALUES('linkchecker_last_checked', 'linkchecker', '', '', 'text', 300, 0, 0);
INSERT INTO `#__mt_config` VALUES('linkchecker_num_of_links', 'linkchecker', '10', '10', 'text', 100, 0, 0);
INSERT INTO `#__mt_config` VALUES('linkchecker_seconds', 'linkchecker', '1', '1', 'text', 200, 0, 0);
INSERT INTO `#__mt_config` VALUES('link_new', 'main', '10', '7', 'text', 8800, 1, 0);
INSERT INTO `#__mt_config` VALUES('link_popular', 'main', '10', '120', 'text', 8900, 1, 0);
INSERT INTO `#__mt_config` VALUES('min_votes_for_toprated', 'ratingreview', '1', '1', 'text', 1500, 1, 1);
INSERT INTO `#__mt_config` VALUES('min_votes_to_show_rating', 'ratingreview', '0', '0', 'text', 1600, 1, 1);
INSERT INTO `#__mt_config` VALUES('name', 'core', 'Mosets Tree', 'Mosets Tree', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('needapproval_addcategory', 'main', '1', '1', 'yesno', 8500, 1, 1);
INSERT INTO `#__mt_config` VALUES('needapproval_addlisting', 'main', '1', '1', 'yesno', 8300, 1, 1);
INSERT INTO `#__mt_config` VALUES('needapproval_addreview', 'ratingreview', '1', '1', 'yesno', 2700, 1, 1);
INSERT INTO `#__mt_config` VALUES('needapproval_modifylisting', 'main', '0', '1', 'yesno', 8400, 1, 1);
INSERT INTO `#__mt_config` VALUES('needapproval_replyreview', 'ratingreview', '0', '1', 'yesno', 8500, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_notify_admin', 'notify', '', '', 'note', 9099, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_notify_owner', 'notify', '', '', 'note', 9450, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_rating', 'ratingreview', '', '', 'note', 1000, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_review', 'ratingreview', '', '', 'note', 2500, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_rss_custom_fields', 'rss', '', '', 'note', 300, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyadmin_delete', 'notify', '1', '1', 'yesno', 9300, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyadmin_modifylisting', 'notify', '1', '1', 'yesno', 9200, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyadmin_newlisting', 'notify', '1', '1', 'yesno', 9100, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyadmin_newreview', 'notify', '1', '1', 'yesno', 9400, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyuser_approved', 'notify', '1', '1', 'yesno', 9700, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyuser_modifylisting', 'notify', '1', '1', 'yesno', 9600, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyuser_newlisting', 'notify', '1', '1', 'yesno', 9500, 1, 1);
INSERT INTO `#__mt_config` VALUES('notifyuser_review_approved', 'notify', '1', '1', 'yesno', 9800, 1, 1);
INSERT INTO `#__mt_config` VALUES('optional_email_sent_to_reviewer', 'ratingreview', '', '', 'note', 10010, 1, 1);
INSERT INTO `#__mt_config` VALUES('owner_reply_review', 'ratingreview', '1', '1', 'yesno', 8000, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_1_message', 'ratingreview', '', '', 'predefined_reply', 10120, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_1_title', 'ratingreview', '', '', 'predefined_reply_title', 10110, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_2_message', 'ratingreview', '', '', 'predefined_reply', 10140, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_2_title', 'ratingreview', '', '', 'predefined_reply_title', 10130, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_3_message', 'ratingreview', '', '', 'predefined_reply', 10160, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_3_title', 'ratingreview', '', '', 'predefined_reply_title', 10150, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_4_message', 'ratingreview', '', '', 'predefined_reply', 10180, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_4_title', 'ratingreview', '', '', 'predefined_reply_title', 10170, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_5_message', 'ratingreview', '', '', 'predefined_reply', 10200, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_5_title', 'ratingreview', '', '', 'predefined_reply_title', 10190, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_bcc', 'ratingreview', '', '', 'text', 10040, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_for_approved_or_rejected_review', 'ratingreview', '', '', 'note', 10100, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_from_email', 'ratingreview', '', '', 'text', 10030, 1, 1);
INSERT INTO `#__mt_config` VALUES('predefined_reply_from_name', 'ratingreview', '', '', 'text', 10020, 1, 1);
INSERT INTO `#__mt_config` VALUES('rate_once', 'ratingreview', '1', '0', 'yesno', 1400, 1, 1);
INSERT INTO `#__mt_config` VALUES('require_rating_with_review', 'ratingreview', '1', '1', 'yesno', 2675, 1, 1);
INSERT INTO `#__mt_config` VALUES('resize_cat_size', 'image', '80', '80', 'text', 1300, 1, 1);
INSERT INTO `#__mt_config` VALUES('resize_small_listing_size', 'image', '100', '100', 'text', 1000, 1, 1);
INSERT INTO `#__mt_config` VALUES('resize_medium_listing_size', 'image', '600', '600', 'text', 1050, 1, 1);
INSERT INTO `#__mt_config` VALUES('resize_method', 'image', 'gd2', 'gd2', 'resize_method', 800, 1, 0);
INSERT INTO `#__mt_config` VALUES('resize_quality', 'image', '80', '80', 'text', 900, 1, 0);
INSERT INTO `#__mt_config` VALUES('rss_address', 'rss', '0', '0', 'yesno', 400, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_cat_name', 'rss', '0', '0', 'yesno', 310, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_cat_url', 'rss', '0', '0', 'yesno', 320, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_city', 'rss', '0', '0', 'yesno', 500, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_country', 'rss', '0', '0', 'yesno', 800, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_custom_fields', 'rss', '', '', 'text', 1500, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_email', 'rss', '0', '0', 'yesno', 900, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_fax', 'rss', '0', '0', 'yesno', 1200, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_link_rating', 'rss', '0', '0', 'yesno', 340, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_link_votes', 'rss', '0', '0', 'yesno', 330, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_metadesc', 'rss', '0', '0', 'yesno', 1400, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_metakey', 'rss', '0', '0', 'yesno', 1300, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_postcode', 'rss', '0', '0', 'yesno', 600, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_state', 'rss', '0', '0', 'yesno', 700, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_telephone', 'rss', '0', '0', 'yesno', 1100, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_website', 'rss', '0', '0', 'yesno', 1000, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_lat', 'rss', '0', '0', 'yesno', 1410, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_lng', 'rss', '0', '0', 'yesno', 1420, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_zoom', 'rss', '0', '0', 'yesno', 1430, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_cat_order1', 'category', '', '', 'cat_order', 1600, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_cat_order2', 'category', 'asc', 'asc', 'sort_direction', 1700, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_listing_order1', 'listing', 'link_name', 'link_votes', 'listing_order', 2000, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_listing_order2', 'listing', 'asc', 'desc', 'sort_direction', 2100, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_review_order1', 'ratingreview', '', 'vote_total', 'review_order', 4000, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_review_order2', 'ratingreview', 'desc', 'desc', 'sort_direction', 5000, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_search_order1', 'search', 'link_hits', 'link_hits', 'listing_order', 2152, 1, 1);
INSERT INTO `#__mt_config` VALUES('second_search_order2', 'search', 'desc', 'desc', 'sort_direction', 2153, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_claim', 'feature', '1', '1', 'yesno', 4500, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_contact', 'feature', '1', '1', 'yesno', 4700, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_listnewrss', 'rss', '1', '1', 'yesno', 100, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_listupdatedrss', 'rss', '1', '1\n', 'yesno', 200, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_map', 'feature', '0', '0', 'yesno', 4100, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_ownerlisting', 'feature', '1', '1', 'yesno', 4600, 1, 1);
INSERT INTO `#__mt_config` VALUES('owner_default_page', 'feature', 'viewuserslisting', 'viewuserslisting', 'owner_default_page', 4650, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_print', 'feature', '1', '0', 'yesno', 4200, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_rating', 'ratingreview', '1', '1', 'yesno', 1100, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_recommend', 'feature', '1', '1', 'yesno', 5100, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_report', 'feature', '1', '1', 'yesno', 4900, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_review', 'ratingreview', '1', '1', 'yesno', 2600, 1, 1);
INSERT INTO `#__mt_config` VALUES('show_visit', 'feature', '1', '1', 'yesno', 4300, 1, 1);
INSERT INTO `#__mt_config` VALUES('third_review_order1', 'ratingreview', '', 'rev_date', 'review_order', 6000, 1, 1);
INSERT INTO `#__mt_config` VALUES('third_review_order2', 'ratingreview', 'desc', 'desc', 'sort_direction', 7000, 1, 1);
INSERT INTO `#__mt_config` VALUES('trigger_modified_listing', 'listing', '', '', 'text', 3900, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_addcategory', 'main', '1', '1', 'user_access', 8000, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_addlisting', 'main', '1', '1', 'user_access', 7900, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_allowdelete', 'main', '1', '1', 'yesno', 8200, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_allowmodify', 'main', '1', '1', 'yesno', 8100, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_contact', 'feature', '0', '0', 'user_access', 4800, 1, 1);
INSERT INTO `#__mt_config` VALUES('contact_bcc_email', 'feature', '', '', 'text', 4810, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_rating', 'ratingreview', '2', '1', 'user_access2', 1300, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_recommend', 'feature', '0', '0', 'user_access', 5200, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_report', 'feature', '1', '0', 'user_access', 5000, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_report_review', 'ratingreview', '1', '1', 'user_access', 9000, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_review', 'ratingreview', '2', '1', 'user_access2', 2800, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_review_once', 'ratingreview', '1', '1', 'yesno', 9000, 1, 1);
INSERT INTO `#__mt_config` VALUES('user_vote_review', 'ratingreview', '1', '1', 'yesno', 10000, 1, 1);
INSERT INTO `#__mt_config` VALUES('use_internal_notes', 'admin', '1', '1', 'yesno', 11900, 1, 0);
INSERT INTO `#__mt_config` VALUES('use_owner_email', 'feature', '1', '0', 'yesno', 5300, 1, 1);
INSERT INTO `#__mt_config` VALUES('use_wysiwyg_editor', 'main', '0', '0', 'yesno', 11000, 1, 0);
INSERT INTO `#__mt_config` VALUES('use_wysiwyg_editor_in_admin', 'admin', '0', '0', 'yesno', 12000, 1, 0);
INSERT INTO `#__mt_config` VALUES('version', 'core', '3.5.6', '3.5.6', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('major_version', 'core', '3', '3', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('minor_version', 'core', '5', '5', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('dev_version', 'core', '6', '6', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('squared_thumbnail', 'image', '1', '1', 'yesno', 1025, 1, 0);
INSERT INTO `#__mt_config` VALUES('show_favourite', 'feature', '1', '1', 'yesno', 4175, 1, 1);
INSERT INTO `#__mt_config` VALUES('relative_path_to_cat_small_image', 'core', '', '/media/com_mtree/images/cats/s/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_cat_original_image', 'core', '', '/media/com_mtree/images/cats/o/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_listing_small_image', 'core', '', '/media/com_mtree/images/listings/s/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_listing_medium_image', 'core', '', '/media/com_mtree/images/listings/m/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_listing_original_image', 'core', '', '/media/com_mtree/images/listings/o/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_rating_image', 'core', '', '/media/com_mtree/images/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_js', 'core', '', '/media/com_mtree/js/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_images', 'core', '', '/media/com_mtree/images/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_templates', 'core', '', '/components/com_mtree/templates/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_fieldtypes', 'core', '', '/administrator/components/com_mtree/fieldtypes/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('relative_path_to_fieldtypes_media', 'core', '', 'media/com_mtree/fieldtypes/', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('rss_title_separator', 'rss', ' - ', ' - ', 'text', 0, 0, 1);
INSERT INTO `#__mt_config` VALUES('cat_parse_plugin', 'category', '1', '1', 'yesno', 3400, 0, 1);
INSERT INTO `#__mt_config` VALUES('image_maxsize', 'image', '819200', '3145728', 'text', 10300, 1, 1);
INSERT INTO `#__mt_config` VALUES('banned_text', 'email', '[/url];[/link]', '', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('banned_subject', 'email', '', '', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('banned_email', 'email', '', '', '', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('notifyowner_review_added', 'notify', '1', '1', 'yesno', 9900, 1, 1);
INSERT INTO `#__mt_config` VALUES('unpublished_message_cfid', 'listing', '0', '0', 'text', 6600, 0, 1);
INSERT INTO `#__mt_config` VALUES('load_css', 'core', '1', '1', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('load_bootstrap_css', 'core', '1', '1', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('load_bootstrap_framework', 'core', '1', '1', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('demo_mode', 'core', '', '0', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('rss_secret_token', 'rss', '', '', 'text', 0, 0, 1);
INSERT INTO `#__mt_config` VALUES('show_category_rss', 'rss', '1', '1', 'yesno', 0, 1, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_updated', 'listing', '60', '60', 'text', 6050, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_popular', 'listing', '60', '60', 'text', 5750, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_favourite', 'listing', '60', '60', 'text', 6150, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_mostrated', 'listing', '60', '60', 'text', 6350, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_toprated', 'listing', '60', '60', 'text', 6450, 0, 1);
INSERT INTO `#__mt_config` VALUES('fe_total_mostreview', 'listing', '60', '60', 'text', 6550, 0, 1);
INSERT INTO `#__mt_config` VALUES('default_search_condition', 'search', '2', '2', 'text', 0, 0, 1);
INSERT INTO `#__mt_config` VALUES('reset_created_date_upon_approval', 'core', '1', '1', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('cache_registered_viewlink', 'main', '0', '0', 'yesno', 0, 0, 1);
INSERT INTO `#__mt_config` VALUES('relative_path_to_attachments', 'core', '/media/com_mtree/attachments/', '/media/com_mtree/attachments/', 'text', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('sef_link_slug_type', 'sef', '3', '3', 'sef_link_slug_type', 100, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_link_slug_type_hybrid_separator', 'sef', '-', '-', 'text', 125, 0, 0);
INSERT INTO `#__mt_config` VALUES('sef_image', 'sef', 'image', 'image', 'text', 200, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_gallery', 'sef', 'gallery', 'gallery', 'text', 300, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_review', 'sef', 'review', 'review', 'text', 400, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_replyreview', 'sef', 'reply-review', 'reply-review', 'text', 500, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_reportreview', 'sef', 'report-review', 'report-review', 'text', 600, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_recommend', 'sef', 'recommend', 'recommend', 'text', 800, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_print', 'sef', 'print', 'print', 'text', 850, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_contact', 'sef', 'contact', 'contact', 'text', 900, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_report', 'sef', 'report', 'report', 'text', 1000, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_claim', 'sef', 'claim', 'claim', 'text', 1100, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_visit', 'sef', 'visit', 'visit', 'text', 1200, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_category_page', 'sef', 'page', 'page', 'text', 1300, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_associated_listing_page', 'sef', 'apage', 'apage', 'text', 1350, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_delete', 'sef', 'delete', 'delete', 'text', 1400, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_reviews_page', 'sef', 'reviews', 'reviews', 'text', 1500, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_addlisting', 'sef', 'add', 'add', 'text', 1600, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_editlisting', 'sef', 'edit', 'edit', 'text', 1650, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_viewreviews', 'sef', 'reviews', 'reviews', 'text', 1655, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_viewreview', 'sef', 'review', 'review', 'text', 1660, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_addcategory', 'sef', 'add-category', 'add-category', 'text', 1700, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_mypage', 'sef', 'my-page', 'my-page', 'text', 1800, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_all', 'sef', 'all', 'all', 'text', 1850, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_new', 'sef', 'new', 'new', 'text', 1900, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_updated', 'sef', 'updated', 'updated', 'text', 2000, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_favourite', 'sef', 'most-favoured', 'most-favoured', 'text', 2100, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_featured', 'sef', 'featured', 'featured', 'text', 2200, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_popular', 'sef', 'popular', 'popular', 'text', 2300, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_mostrated', 'sef', 'most-rated', 'most-rated', 'text', 2400, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_toprated', 'sef', 'top-rated', 'top-rated', 'text', 2500, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_mostreview', 'sef', 'most-reviewed', 'most-reviewed', 'text', 2600, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_listalpha', 'sef', 'list-alpha', 'list-alpha', 'text', 2700, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_listallcats', 'sef', 'all-categories', 'all-categories', 'text', 2750, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_owner', 'sef', 'owner', 'owner', 'text', 2800, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_listings', 'sef', 'listings', 'listings', 'text', 2850, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_favourites', 'sef', 'favourites', 'favourites', 'text', 2900, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_reviews', 'sef', 'reviews', 'reviews', 'text', 3000, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_searchby', 'sef', 'search-by', 'search-by', 'text', 3050, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_search', 'sef', 'search', 'search', 'text', 3100, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_advsearch', 'sef', 'advanced-search', 'advanced-search', 'text', 3200, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_advsearch2', 'sef', 'advanced-search-results', 'advanced-search-results', 'text', 3300, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_rss', 'sef', 'rss', 'rss', 'text', 3400, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_rss_new', 'sef', 'new', 'new', 'text', 3500, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_rss_updated', 'sef', 'updated', 'updated', 'text', 3600, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_space', 'sef', '-', '-', 'text', 3700, 1, 0);
INSERT INTO `#__mt_config` VALUES('note_sef_translations', 'sef', '', '', 'note', 150, 1, 0);
INSERT INTO `#__mt_config` VALUES('sef_details', 'sef', 'details', 'details', 'text', 175, 0, 0);
INSERT INTO `#__mt_config` VALUES('show_image_rss', 'rss', '1', '1', 'yesno', 250, 0, 1);
INSERT INTO `#__mt_config` VALUES('use_map', 'feature', '0', '0', 'yesno', 3950, 1, 1);
INSERT INTO `#__mt_config` VALUES('map_default_country', 'feature', '', '', 'text', 3960, 1, 1);
INSERT INTO `#__mt_config` VALUES('map_default_state', 'feature', '', '', 'text', 3970, 1, 1);
INSERT INTO `#__mt_config` VALUES('map_default_city', 'feature', '', '', 'text', 3980, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_map', 'feature', '', '', 'note', 3925, 1, 1);
INSERT INTO `#__mt_config` VALUES('note_other_features', 'feature', '', '', 'note', 4170, 1, 1);
INSERT INTO `#__mt_config` VALUES('map_default_lat', 'feature', '12.554563528593656', '12.554563528593656', 'text', 3985, 0, 1);
INSERT INTO `#__mt_config` VALUES('map_default_lng', 'feature', '18.984375', '18.984375', 'text', 3986, 0, 1);
INSERT INTO `#__mt_config` VALUES('map_default_zoom', 'feature', '1', '1', 'text', 3987, 0, 1);
INSERT INTO `#__mt_config` VALUES('display_pending_approval_listings_to_owners', 'listing', '1', '0', 'yesno', 4000, 0, 1);
INSERT INTO `#__mt_config` VALUES('days_to_expire', 'listing', '0', '0', 'text', 6800, 1, 1);
INSERT INTO `#__mt_config` VALUES('rss_new_limit', 'rss', '40', '40', 'text', 220, 0, 1);
INSERT INTO `#__mt_config` VALUES('rss_updated_limit', 'rss', '40', '40', 'text', 240, 0, 1);
INSERT INTO `#__mt_config` VALUES('limit_max_chars', 'search', '20', '20', 'text', 2160, 0, 1);
INSERT INTO `#__mt_config` VALUES('limit_min_chars', 'search', '3', '3', 'text', 2170, 0, 1);
INSERT INTO `#__mt_config` VALUES('banned_attachment_filetypes', 'main', 'php', 'php', 'text', 12000, 0, 0);
INSERT INTO `#__mt_config` VALUES('use_open_graph_protocol', 'core', '1', '1', 'yesno', 0, 0, 0);
INSERT INTO `#__mt_config` VALUES('use_captcha_review', 'captcha', '0', '0', 'yesno', '1000', '1', '0');
INSERT INTO `#__mt_config` VALUES('use_captcha_contact', 'captcha', '0', '0', 'yesno', '2000', '1', '0');
INSERT INTO `#__mt_config` VALUES('use_captcha_report', 'captcha', '0', '0', 'yesno', '3000', '1', '0');
INSERT INTO `#__mt_config` VALUES('use_captcha_reportreview', 'captcha', '0', '0', 'yesno', '4000', '1', '0');
INSERT INTO `#__mt_config` VALUES('contact_form_location', 'feature', '1', '1', 'feature_locations', '4750', '1', '1');
INSERT INTO `#__mt_config` VALUES('show_user_profile_in_listing_details', 'listing', '0', '0', 'yesno', '2500', '1', '1');

CREATE TABLE IF NOT EXISTS `#__mt_configgroup` (
  `groupname` varchar(50) NOT NULL,
  `ordering` smallint(6) NOT NULL,
  `displayed` smallint(6) NOT NULL,
  `overridable_by_category` smallint(6) NOT NULL,
  PRIMARY KEY  (`groupname`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

INSERT IGNORE INTO `#__mt_configgroup` VALUES ('main', 100, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('category', 250, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('listing', 300, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('search', 400, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('ratingreview', 450, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('feature', 500, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('notify', 600, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('image', 650, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('rss', 675, 1, 1);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('captcha', 690, 1, 0);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('admin', 700, 1, 0);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('linkchecker', 800, 0, 0);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('core', 999, 0, 0);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('email', 999, 0, 0);
INSERT IGNORE INTO `#__mt_configgroup` VALUES ('sef', 685, 1, 0);

CREATE TABLE IF NOT EXISTS `#__mt_customfields` (
  `cf_id` int(11) NOT NULL auto_increment,
  `field_type` varchar(36) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `default_value` varchar(255) NOT NULL,
  `size` smallint(9) NOT NULL,
  `field_elements` text NOT NULL,
  `prefix_text_mod` varchar(255) NOT NULL,
  `suffix_text_mod` varchar(255) NOT NULL,
  `prefix_text_display` varchar(255) NOT NULL,
  `suffix_text_display` varchar(255) NOT NULL,
  `placeholder_text` varchar(255) NOT NULL,
  `cat_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL,
  `hidden` tinyint(4) NOT NULL default '0',
  `required_field` tinyint(4) NOT NULL default '0',
  `published` tinyint(4) NOT NULL default '1',
  `hide_caption` tinyint(4) NOT NULL default '0',
  `advanced_search` tinyint(4) NOT NULL default '0',
  `simple_search` tinyint(4) NOT NULL default '0',
  `tag_search` tinyint(3) unsigned NOT NULL default '0',
  `filter_search` tinyint(3) unsigned NOT NULL default '0',
  `details_view` tinyint(3) unsigned NOT NULL default '1',
  `summary_view` tinyint(3) unsigned NOT NULL default '0',
  `search_caption` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cf_id`),
  KEY `published_ordering` (`published`,`ordering`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

INSERT IGNORE INTO `#__mt_customfields` VALUES (1, 'corename', 'Name', 'name', '', 50, '', '', '', '', '', '', 0, 1, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (2, 'coredesc', 'Description', 'description', '', 250, '', '', '', '', '', '', 0, 2, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (3, 'coreuser', 'Owner', 'owner', '', 0, '', '', '', '', '', '', 0, 3, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (4, 'coreaddress', 'Address', 'address', '', 0, '', '', '', '', '', '', 0, 4, 0, 0, 1, 0, 0, 1, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (5, 'corecity', 'City', 'city', '', 0, '', '', '', '', '', '', 0, 5, 0, 0, 1, 0, 0, 0, 1, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (6, 'corestate', 'State', 'state', '', 0, '', '', '', '', '', '', 0, 6, 0, 0, 1, 0, 0, 0, 1, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (7, 'corecountry', 'Country', 'country', '', 0, '', '', '', '', '', '', 0, 7, 0, 0, 1, 0, 0, 0, 1, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (8, 'corepostcode', 'Postcode', 'postcode', '', 0, '', '', '', '', '', '', 0, 8, 0, 0, 1, 0, 0, 0, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (9, 'coretelephone', 'Telephone', 'telephone', '', 0, '', '', '', '', '', '', 0, 9, 0, 0, 1, 0, 0, 0, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (10, 'corefax', 'Fax', 'fax', '', 0, '', '', '', '', '', '', 0, 10, 0, 0, 1, 0, 0, 0, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (11, 'coreemail', 'E-mail', 'email', '', 0, '', '', '', '', '', '', 0, 11, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (12, 'corewebsite', 'Website', 'website', '', 0, '', '', '', '', '', '', 0, 12, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (13, 'coreprice', 'Price', 'price', '', 0, '', '', '', '', '', '', 0, 13, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (14, 'corehits', 'Hits', 'hits', '', 0, '', '', '', '', '', '', 0, 14, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (15, 'corevotes', 'Votes', 'votes', '', 10, '', '', '', '', '', '', 0, 15, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (16, 'corerating', 'Rating', 'rating', '', 0, '', '', '', '', '', '', 0, 16, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (17, 'corefeatured', 'Featured', 'featured', '', 0, '', '', '', '', '', '', 0, 17, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (18, 'corecreated', 'Created', 'created', '', 0, '', '', '', '', '', '', 0, 18, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (19, 'coremodified', 'Modified', 'modified', '', 0, '', '', '', '', '', '', 0, 19, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (20, 'corevisited', 'Visited', 'visited', '', 0, '', '', '', '', '', '', 0, 20, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (21, 'corepublishup', 'Publish up', 'publish-up', '', 0, '', '', '', '', '', '', 0, 21, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (22, 'corepublishdown', 'Publish down', 'publish-down', '', 0, '', '', '', '', '', '', 0, 22, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (24, 'mfile', 'File', 'file', '', 30, '', '', '', '', '', '', 0, 28, 0, 0, 1, 0, 1, 0, 0, 0, 1, 0, '', '', 0);
INSERT IGNORE INTO `#__mt_customfields` VALUES (26, 'coremetakey', 'Meta Keys', 'meta-keys', '', 0, '', '', '', '', '', '', 0, 23, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (27, 'coremetadesc', 'Meta Description', 'meta-description', '', 0, '', '', '', '', '', '', 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', '', 1);
INSERT IGNORE INTO `#__mt_customfields` VALUES (28, 'mtags', 'Tags', '', '', 40, '', '', '', '', '', '', 0, 25, 0, 0, 1, 0, 0, 0, 1, 0, 1, 1, '', '', 0);

CREATE TABLE IF NOT EXISTS `#__mt_favourites` (
  `fav_id` int(11) NOT NULL auto_increment,
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fav_date` datetime NOT NULL,
  PRIMARY KEY  (`fav_id`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE `#__mt_fields_map` (
  `cf_id` int(10) unsigned NOT NULL,
  `cat_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cf_id`,`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__mt_fieldtypes` (
  `ft_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` varchar(36) NOT NULL,
  `ft_caption` varchar(255) NOT NULL,
  `ft_version` varchar(64) NOT NULL,
  `ft_website` varchar(255) NOT NULL,
  `ft_desc` text NOT NULL,
  `use_elements` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_size` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_columns` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_placeholder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_file` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `taggable` tinyint(3) NOT NULL DEFAULT '0',
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ft_id`),
  UNIQUE KEY `field_type` (`field_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(1, 'corerating', 'Rating', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(2, 'coreprice', 'Price', '', '', '', 0, 0, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(3, 'coreaddress', 'Address', '', '', '', 0, 1, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(4, 'corecity', 'City', '', '', '', 1, 1, 0, 1, 0, 1, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(5, 'corestate', 'State', '', '', '', 1, 1, 0, 1, 0, 1, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(6, 'corecountry', 'Country', '', '', '', 1, 0, 0, 1, 0, 1, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(7, 'corepostcode', 'Postcode', '', '', '', 1, 1, 0, 1, 0, 1, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(8, 'coretelephone', 'Telephone', '', '', '', 0, 1, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(9, 'corefax', 'Fax', '', '', '', 0, 1, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(10, 'coreemail', 'E-mail', '', '', '', 0, 1, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(11, 'corewebsite', 'Website', '', '', '', 0, 0, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(12, 'corehits', 'Hits', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(13, 'corevotes', 'Votes', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(14, 'corefeatured', 'Featured', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(15, 'coremodified', 'Modified', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(16, 'corevisited', 'Visited', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(17, 'corepublishup', 'Publish Up', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(18, 'corepublishdown', 'Publish Down', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(19, 'coreuser', 'Owner', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(20, 'corename', 'Name', '', '', '', 0, 0, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(21, 'coredesc', 'Description', '', '', '', 0, 0, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(22, 'corecreated', 'Created', '', '', '', 0, 0, 0, 0, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(23, 'mweblink', 'Web link', '', '', '', 1, 1, 1, 1, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(24, 'audioplayer', 'Audio Player', '3.0.0', '', 'Audio Player allows users to upload audio files and play the music from within the listing page. Provides basic playback options such as play, pause and volumne control. Made possible by http://www.1pixelout.net/code/audio-player-wordpress-plugin/.', 0, 0, 0, 0, 1, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(25, 'image', 'Image', '', '', '', 0, 0, 0, 0, 1, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(26, 'mtext', 'Text', '', '', '', 0, 1, 0, 1, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(29, 'youtube', 'Youtube', '', '', '', 0, 1, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(30, 'coremetakey', 'Meta Keys', '', '', '', 0, 0, 0, 1, 0, 1, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(31, 'coremetadesc', 'Meta Description', '', '', '', 0, 0, 0, 1, 0, 0, 1);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(32, 'mtags', 'Tags', '', '', '', 0, 1, 0, 1, 0, 1, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(45, 'videoplayer', 'Video Player', '', '', '', 0, 0, 0, 0, 1, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(46, 'year', 'Year', '', '', '', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(47, 'mdate', 'Date', '', '', '', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(48, 'mfile', 'File', '', '', '', 0, 0, 0, 0, 1, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(50, 'memail', 'E-mail', '', '', '', 0, 1, 0, 1, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(51, 'mnumber', 'Number', '', '', '', 0, 1, 0, 1, 0, 1, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(60, 'mskype', 'Skype', '', '', '', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(61, 'mcheckbox', 'Checkbox', '', '', '', 1, 0, 0, 0, 0, 1, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(70, 'timezone', 'Time Zone', '3.0.0', '', 'Displays list of time zones.', 0, 0, 0, 0, 0, 1, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(73, 'monthyear', 'Month & Year', '3.0.0', '', 'Similar to Date field but for selecting month and year only.', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(74, 'termsandconditions', 'Terms & Conditions', '3.0.0', '', '', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(75, 'associatedlisting', 'Associated Listing', '3.0.0', 'http://www.mosets.com', 'Associated Listing', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(76, 'category', 'Category', '3.0.0', 'http://www.mosets.com/tree', 'Category', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(77, 'directory', 'Directory', '3.0.0', 'http://www.mosets.com/tree', 'Directory', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(78, 'listingid', 'Listing ID', '3.0.0', 'http://www.mosets.com', 'Listing ID', 0, 0, 0, 0, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(79, 'vanityurl', 'Vanity URL', '3.0.0', 'http://www.mosets.com/', '', 0, 1, 0, 1, 0, 1, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(80, 'texteditor', 'Text Editor', '1.0', 'http://www.mosets.com/', '', 0, 0, 0, 1, 0, 0, 0);
INSERT IGNORE INTO `#__mt_fieldtypes` VALUES(81, 'captcha', 'Captcha', '3.0.0', 'http://www.mosets.com/', '', 0, 0, 0, 0, 0, 0, 0);

CREATE TABLE IF NOT EXISTS `#__mt_images` (
  `img_id` int(11) NOT NULL auto_increment,
  `link_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`img_id`),
  KEY `link_id_ordering` (`link_id`,`ordering`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_linkcheck` (
  `id` int(11) NOT NULL auto_increment,
  `link_id` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `path` text NOT NULL,
  `status_code` smallint(5) unsigned NOT NULL,
  `check_status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_links` (
  `link_id` int(11) NOT NULL auto_increment,
  `link_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `link_desc` mediumtext NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `link_hits` int(11) NOT NULL default '0',
  `link_votes` int(11) NOT NULL default '0',
  `link_rating` decimal(7,6) unsigned NOT NULL default '0.000000',
  `link_featured` smallint(6) NOT NULL default '0',
  `link_published` tinyint(4) NOT NULL default '0',
  `link_approved` int(4) NOT NULL default '0',
  `link_template` varchar(255) NOT NULL,
  `attribs` text NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `internal_notes` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `link_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `link_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `link_visited` int(11) NOT NULL default '0',
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `price` double(12,2) NOT NULL default '0.00',
  `show_map` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `lat` float(10,6) NOT NULL COMMENT 'Latitude',
  `lng` float(10,6) NOT NULL COMMENT 'Longitude',
  `zoom` tinyint(3) unsigned NOT NULL COMMENT 'Map''s zoom level',
  PRIMARY KEY  (`link_id`),
  KEY `link_rating` (`link_rating`),
  KEY `link_votes` (`link_votes`),
  KEY `link_name` (`link_name`),
  KEY `publishing` (`link_published`,`link_approved`,`publish_up`,`publish_down`),
  KEY `count_listfeatured` (`link_published`,`link_approved`,`link_featured`,`publish_up`,`publish_down`,`link_id`),
  KEY `count_viewowner` (`link_published`,`link_approved`,`user_id`,`publish_up`,`publish_down`),
  KEY `mylisting` (`user_id`,`link_id`),
  FULLTEXT KEY `link_name_desc` (`link_name`,`link_desc`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_links_associations` (
  `link_id1` int(10) unsigned NOT NULL,
  `link_id2` int(10) unsigned NOT NULL,
  KEY `link_id1` (`link_id1`,`link_id2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mt_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_ip` varchar(255) NOT NULL default '',
  `log_type` varchar(32) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `link_id` int(11) NOT NULL default '0',
  `rev_id` int(11) NOT NULL default '0',
  `value` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`log_id`),
  KEY `link_id2` (`link_id`,`log_ip`),
  KEY `link_id1` (`link_id`,`user_id`),
  KEY `log_type` (`log_type`),
  KEY `log_ip` (`log_ip`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_relcats` (
  `cat_id` int(11) NOT NULL default '0',
  `rel_id` int(11) NOT NULL default '0',
  KEY `cat_id` (`cat_id`,`rel_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_reports` (
  `report_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `link_id` int(11) NOT NULL,
  `rev_id` int(10) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL,
  `comment` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `admin_note` mediumtext NOT NULL,
  PRIMARY KEY  (`report_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_reviews` (
  `rev_id` int(11) NOT NULL auto_increment,
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `guest_name` varchar(255) NOT NULL default '',
  `rev_title` varchar(255) NOT NULL default '',
  `rev_text` text NOT NULL,
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `rev_approved` tinyint(4) NOT NULL default '1',
  `admin_note` mediumtext NOT NULL,
  `vote_helpful` int(10) unsigned NOT NULL default '0',
  `vote_total` int(10) unsigned NOT NULL default '0',
  `ownersreply_text` text NOT NULL,
  `ownersreply_date` datetime NOT NULL,
  `ownersreply_approved` tinyint(4) NOT NULL default '0',
  `ownersreply_admin_note` mediumtext NOT NULL,
  `send_email` tinyint(3) unsigned NOT NULL,
  `email_message` mediumtext NOT NULL,
  PRIMARY KEY  (`rev_id`),
  KEY `link_id` (`link_id`,`rev_approved`,`rev_date`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__mt_templates` (
  `tem_id` int(11) NOT NULL auto_increment,
  `tem_name` varchar(255) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`tem_id`),
  UNIQUE KEY `tem_name` (`tem_name`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

INSERT IGNORE INTO `#__mt_templates` VALUES (1, 'kinabalu', '');
