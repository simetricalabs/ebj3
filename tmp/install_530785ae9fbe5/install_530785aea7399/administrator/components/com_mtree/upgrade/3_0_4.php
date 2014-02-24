<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_4 extends mUpgrade {

	function upgrade() {
		$database =& JFactory::getDBO();

		// Upgraded copy of MT 3.0 does not have 'fe_num_of_associated' setting visible in Configuration.
		// The query below sets the config to visible.
		$database->setQuery('UPDATE `#__mt_config` SET `displayed` = \'1\' WHERE `varname` IN(\'fe_num_of_associated\');');
		$database->execute();

		// Clean up #__mt_images caused by bug from r1996
		$database->setQuery('DELETE FROM `#__mt_images` WHERE link_id = 0;');
		$database->execute();

		# The next 13 queries are rehash of the same set of upgrade SQL queries from 3_0_2.php.
		# It wasn't executed properly due to a syntax error in the first ALTER TABLE below.
		# It's now fixed and re-executed below.
		
		// #__mt_fieldtypes table wasn't properly upgrade in 3.0.0. 
		// Queries below fixes them by adding 'is_file' column.
		$database->setQuery('ALTER TABLE `#__mt_fieldtypes` ADD `is_file` TINYINT UNSIGNED NOT NULL DEFAULT \'0\' AFTER `use_placeholder`');
		$database->execute();

		// Set default for is_file column
		$database->setQuery("UPDATE `#__mt_fieldtypes` SET `is_file` = 1 WHERE `field_type` IN ('mfile','videoplayer','image','audioplayer')");
		$database->execute();

		// The following is the continuation of the fix above, by adding new fieldtypes.
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

		# End of 3_0_2.php query re-hash.
		
		updateVersion(3,0,4);
		$this->updated = true;
		return true;
	}
}
?>