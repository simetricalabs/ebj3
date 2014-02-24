<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2010 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_4 extends mUpgrade {
	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Add [/url];[/link] to banned_text config to block common spam
		$database->setQuery('UPDATE `#__mt_config` SET value = \'[/url];[/link]\' WHERE varname = \'banned_text\' AND value = \'\' LIMIT 1');
		$database->execute();
		
		// Update corerating class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corerating extends mFieldType_number {\r\n	var $name = \'link_rating\';\r\n	var $numOfSearchFields = 2;\r\n	var $numOfInputFields = 0;\r\n	function getOutput($view=1) {\r\n		global $mtconf;\r\n		\r\n		$params[\'outputType\'] 	= intval($this->getParam(\'outputType\',1));\r\n		$rating = round($this->getValue(),2);\r\n		$star = floor($rating);\r\n		\r\n		switch($params[\'outputType\'])\r\n		{\r\n			// Stars\r\n			case 1:\r\n				$html = \'\';\r\n				// Print stars\r\n				for( $i=0; $i<$star; $i++) {\r\n					$html .= \'<img src=\"\'.JUri::base().\'components/com_mtree/img/star_10.png\" width=\"14\" height=\"14\" hspace=\"1\" class=\"star\" alt=\"★\" />\';\r\n				}\r\n\r\n				if( ($rating-$star) >= 0.5 && $star > 0 ) {\r\n					$html .= \'<img src=\"\'.JUri::base().\'components/com_mtree/img/star_05.png\" width=\"14\" height=\"14\" hspace=\"1\" class=\"star\" alt=\"½\" />\';\r\n					$star += 1;\r\n				}\r\n\r\n				// Print blank stars\r\n				for( $i=$star; $i<5; $i++) {\r\n					$html .= \'<img src=\"\'.JUri::base().\'components/com_mtree/img/star_00.png\" width=\"14\" height=\"14\" hspace=\"1\" class=\"star\" alt=\"\" />\';\r\n				}\r\n				return $html;\r\n				break;\r\n				\r\n			// Value\r\n			case 2:\r\n				return $rating;\r\n				break;\r\n		}\r\n	}\r\n	function getJSValidation() {\r\n		return null;\r\n	}\r\n}"  WHERE field_type = "corerating" LIMIT 1');
		$database->execute();
		
		// Get the field type ID of corerating
		$database->setQuery('SELECT ft_id FROM #__mt_fieldtypes WHERE field_type = "corerating" LIMIT 1');
		$corerating_ft_id = $database->loadResult();
		
		// Update corerating params.xml
		$database->setQuery('INSERT INTO #__mt_fieldtypes_att (`ft_id`, `filename`, `filedata`, `filesize`, `extension`, `ordering`) VALUES( ' . $corerating_ft_id . ', \'params.xml\',  0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D226F7574707574547970652220747970653D22726164696F222064656661756C743D223122206C6162656C3D224F7574707574205479706522206465736372697074696F6E3D2253686F77732074686520726174696E67206173207374617273206F72206E756D657269632076616C75652E223E0A0909093C6F7074696F6E2076616C75653D2231223E53746172733C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2232223E56616C75653C2F6F7074696F6E3E0A09093C2F706172616D3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, \'273\', \'text/xml\', 1)');
		$database->execute();
		
		updateVersion(2,1,4);
		$this->updated = true;
		return true;
	}
}
?>