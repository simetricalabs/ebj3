<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_0_6 extends mUpgrade {
	function upgrade() {
		global $database;

		// Added new internal config for customizable RSS's title separator
		$database->setQuery('INSERT INTO #__mt_config (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`) VALUES ("rss_title_separator", "core", " - ", " - ", "text", "", "0")');
		$database->execute();
		$this->printStatus( 'Added new internal config for customizable RSS\'s title separator' );

		// Update jQuery to 1.2.3
		$database->setQuery('UPDATE #__mt_config SET value = "/components/com_mtree/js/jquery-1.2.3.min.js" WHERE varname = "relative_path_to_js_library" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated jQuery to 1.2.3' );

		// Added new internal config(cat_parse_plugin) to toggle parsing of plugin in category view (listcats)
		$database->setQuery('INSERT INTO #__mt_config (`varname` ,`groupname` ,`value` ,`default` ,`configcode` ,`ordering` ,`displayed` ) VALUES ("cat_parse_plugin", "category", "1", "1", "yesno", "3400", "0")');
		$database->execute();
		$this->printStatus( 'Added new internal config(cat_parse_plugin) to toggle parsing of plugin in category view (listcats)' );

		// Update audioplayer class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_audioplayer extends mFieldType_file {\r\n	function getJSValidation() {\r\n\r\n		$js = \'\';\r\n		$js .= \'} else if (!hasExt(form.\' . $this->getName() . \'.value,\\\\\'mp3\\\\\')) {\'; \r\n		$js .= \'alert(\"\' . $this->getCaption() . \': Please select a mp3 file.\");\';\r\n		return $js;\r\n	}\r\n	function getOutput() {\r\n		$id = $this->getId();\r\n		$params[\'text\'] = $this->getParam(\'textColour\');\r\n		$params[\'displayfilename\'] = $this->getParam(\'displayfilename\',1);\r\n		$params[\'slider\'] = $this->getParam(\'sliderColour\');\r\n		$params[\'loader\'] = $this->getParam(\'loaderColour\');\r\n		$params[\'track\'] = $this->getParam(\'trackColour\');\r\n		$params[\'border\'] = $this->getParam(\'borderColour\');\r\n		$params[\'bg\'] = $this->getParam(\'backgroundColour\');\r\n		$params[\'leftbg\'] = $this->getParam(\'leftBackgrounColour\');\r\n		$params[\'rightbg\'] = $this->getParam(\'rightBackgrounColour\');\r\n		$params[\'rightbghover\'] = $this->getParam(\'rightBackgroundHoverColour\');\r\n		$params[\'lefticon\'] = $this->getParam(\'leftIconColour\');\r\n		$params[\'righticon\'] = $this->getParam(\'rightIconColour\');\r\n		$params[\'righticonhover\'] = $this->getParam(\'rightIconHoverColour\');\r\n		\r\n		$html = \'\';\r\n		$html .= \'<script language=\"JavaScript\" src=\"\' . $this->getFieldTypeAttachmentURL(\'audio-player.js\'). \'\"></script>\';\r\n		$html .= \"\\\\n\" . \'<object type=\"application/x-shockwave-flash\" data=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\'). \'\" id=\"audioplayer\' . $id . \'\" height=\"24\" width=\"290\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"movie\" value=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\') . \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"FlashVars\" value=\"\';\r\n		$html .= \'playerID=\' . $id;\r\n		$html .= \'&amp;soundFile=\' . urlencode($this->getDataAttachmentURL());\r\n		foreach( $params AS $key => $value ) {\r\n			if(!empty($value)) {\r\n				$html .= \'&amp;\' . $key . \'=0x\' . $value;\r\n			}\r\n		}\r\n		$html .= \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"quality\" value=\"high\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"menu\" value=\"false\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"wmode\" value=\"transparent\">\';\r\n		$html .= \"\\\\n\" . \'</object>\';\r\n		if($params[\'displayfilename\']) {\r\n			$html .= \"\\\\n<br />\";\r\n			$html .= \"\\\\n\" . \'<a href=\"\' . $this->getDataAttachmentURL() . \'\" target=\"_blank\">\';\r\n			$html .= $this->getValue();\r\n			$html .= \'</a>\';\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "audioplayer" LIMIT 1');
		$database->execute();

		// Update audioplayer version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "24" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated audioplayer field type to version 2.' );

		// Update corecreated class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corecreated extends mFieldType_date {\r\n	var $name = \'link_created\';\r\n	var $numOfInputFields = 0;\r\n}"  WHERE field_type = "corecreated" LIMIT 1');
		$database->execute();

		// Update corecreated version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "22" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corecreated field type to version 2.' );

		// Update corefeatured class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corefeatured extends mFieldType {\r\n	var $name = \'link_featured\';\r\n	var $numOfInputFields = 0;\r\n	function getOutput() {\r\n\r\n		$featured = $this->getValue();\r\n		$html = \'\';\r\n		if($featured) {\r\n			$html .= $_MT_LANG->YES;\r\n		} else {\r\n			$html .= $_MT_LANG->NO;\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n\r\n		$html = \'<select name=\"\' . $this->getSearchFieldName(1) . \'\" class=\"inputbox text_area\" size=\"1\">\';\r\n		$html .= \'<option value=\"-1\" selected=\"selected\">\' . $_MT_LANG->ANY . \'</option>\';\r\n		$html .= \'<option value=\"1\">\' . $_MT_LANG->FEATURED_ONLY . \'</option>\';\r\n		$html .= \'<option value=\"0\">\' . $_MT_LANG->NON_FEATURED_ONLY . \'</option>\';\r\n		$html .= \'</select>\';\r\n		return $html;\r\n	}\r\n	\r\n	function getWhereCondition() {\r\n		$args = func_get_args();\r\n\r\n		$fieldname = $this->getName();\r\n		\r\n		if(  is_numeric($args[0]) ) {\r\n			switch($args[0]) {\r\n				case -1:\r\n					return null;\r\n					break;\r\n				case 1:\r\n					return $fieldname . \' = 1\';\r\n					break;\r\n				case 0:\r\n				return $fieldname . \' = 0\';\r\n					break;\r\n			}\r\n		} else {\r\n			return null;\r\n		}\r\n	}\r\n}"  WHERE field_type = "corefeatured" LIMIT 1');
		$database->execute();

		// Update corefeatured version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "14" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corefeatured field type to version 2.' );

		// Update coremodified class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coremodified extends mFieldType_date {\r\n	var $name = \'link_modified\';\r\n	var $numOfInputFields = 0;\r\n	function getOutput() {\r\n\r\n		$value = $this->getValue();\r\n		if($value == \'0000-00-00 00:00:00\') {\r\n			return $_MT_LANG->NEVER;\r\n		} else {\r\n			return $value;\r\n		}\r\n	}\r\n}\r\n"  WHERE field_type = "coremodified" LIMIT 1');
		$database->execute();

		// Update coremodified version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "15" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coremodified field type to version 2.' );

		// Update corename class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corename extends mFieldType {\r\n	var $name = \'link_name\';\r\n	function getOutput($view=1) {\r\n		$params[\'maxSummaryChars\'] = intval($this->getParam(\'maxSummaryChars\',55));\r\n		$params[\'maxDetailsChars\'] = intval($this->getParam(\'maxDetailsChars\',0));\r\n		$value = $this->getValue();\r\n		$output = \'\';\r\n		if($view == 1 AND $params[\'maxDetailsChars\'] > 0 AND $this->strlen_utf8($value) > $params[\'maxDetailsChars\']) {\r\n			$output .= $this->html_cutstr($value,$params[\'maxDetailsChars\']);\r\n			$output .= \'...\';\r\n		} elseif($view == 2 AND $params[\'maxSummaryChars\'] > 0 AND $this->strlen_utf8($value) > $params[\'maxSummaryChars\']) {\r\n			$output .= $this->html_cutstr($value,$params[\'maxSummaryChars\']);\r\n			$output .= \'...\';\r\n		} else {\r\n			$output = $value;\r\n		}\r\n		return $output;\r\n	}\r\n}"  WHERE field_type = "corename" LIMIT 1');
		$database->execute();

		// Update corename version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "20" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corename field type to version 2.' );

		// Update coreprice class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coreprice extends mFieldType_number {\r\n	var $name = \'price\';\r\n	function getOutput() {\r\n		$price = $this->getValue();\r\n		$displayFree = $this->getParam(\'displayFree\',1);\r\n		if($price == 0 && $displayFree == 1) {\r\n\r\n			return $_MT_LANG->FREE;\r\n		} else {\r\n			return $price;\r\n		}\r\n	}\r\n}"  WHERE field_type = "coreprice" LIMIT 1');
		$database->execute();

		// Update coreprice version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "2" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coreprice field type to version 2.' );

		// Update corerating class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corerating extends mFieldType_number {\r\n	var $name = \'link_rating\';\r\n	var $numOfSearchFields = 2;\r\n	var $numOfInputFields = 0;\r\n	function getOutput($view=1) {\r\n		return round($this->getValue(),2);\r\n	}\r\n	function getJSValidation() {\r\n		return null;\r\n	}\r\n}"  WHERE field_type = "corerating" LIMIT 1');
		$database->execute();

		// Update corerating version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "1" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corerating field type to version 2.' );

		// Update corevisited class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corevisited extends mFieldType_number {\r\n	var $name = \'link_visited\';\r\n	var $numOfInputFields = 0;\r\n	function getJSValidation() {\r\n		return null;\r\n	}\r\n}\r\n"  WHERE field_type = "corevisited" LIMIT 1');
		$database->execute();

		// Update corevisited version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "16" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corevisited field type to version 2.' );

		// Update corewebsite class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corewebsite extends mFieldType_weblink {\r\n	var $name = \'website\';\r\n\r\n	function getOutput() {\r\n		$maxUrlLength = $this->getParam(\'maxUrlLength\',60);\r\n		$text = $this->getParam(\'text\',\'\');\r\n		$openNewWindow = $this->getParam(\'openNewWindow\',1);\r\n		$useMTVisitRedirect = $this->getParam(\'useMTVisitRedirect\',1);\r\n	\r\n		$html = \'\';\r\n		$html .= \'<a href=\"\';\r\n		if($useMTVisitRedirect) {\r\n			global $Itemid;\r\n			$html .= JRoute::_(\'index.php?option=com_mtree&task=visit&link_id=\' . $this->getLinkId() . \'&Itemid=\' . $Itemid);\r\n		} else {\r\n			$html .= $this->getValue();\r\n		}\r\n		$html .= \'\"\';\r\n		if( $openNewWindow == 1 ) {\r\n			$html .= \' target=\"_blank\"\';\r\n		}\r\n		$html .= \'>\';\r\n		if(!empty($text)) {\r\n			$html .= $text;\r\n		} else {\r\n			if( empty($maxUrlLength) || $maxUrlLength == 0 ) {\r\n				$html .= $this->getValue();\r\n			} else {\r\n				$html .= substr($this->getValue(),0,$maxUrlLength);\r\n				if( strlen($this->getValue()) > $maxUrlLength ) {\r\n					$html .= $this->getParam(\'clippedSymbol\');\r\n				}\r\n			}\r\n		}\r\n		$html .= \'</a>\';\r\n		return $html;\r\n	}\r\n	\r\n	function getInputHTML() {\r\n\r\n		$showGo = $this->getParam(\'showGo\',1);\r\n		$showSpider = $this->getParam(\'showSpider\',0);\r\n		$inBackEnd = (substr(dirname($_SERVER[\'PHP_SELF\']),-13) == \'administrator\') ? true : false;\r\n		$html = \'\';\r\n		$html .= \'<input class=\"text_area inputbox\" type=\"text\" name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" size=\"\' . $this->getSize() . \'\" value=\"\' . htmlspecialchars($this->getValue()) . \'\" />\';\r\n		if($showGo && $inBackEnd) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript:window.open(\"index3.php?option=com_mtree&task=openurl&url=\"+escape(document.getElementById(\"website\").value))\\\\\'\';\r\n			$html .= \'value=\"\' . $_MT_LANG->GO . \'\" />\';\r\n		}\r\n		\r\n		if($showSpider && $inBackEnd) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript: \';\r\n			$html .= \'jQuery(\"#spiderwebsite\").html(\"\' . $_MT_LANG->SPIDER_PROGRESS . \'\");\';\r\n			$html .= \'jQuery.ajax({\r\n			  type: \"POST\",\r\n			  url: mosConfig_live_site+\"/administrator/index2.php\",\r\n			  data: \"option=com_mtree&task=ajax&task2=spiderurl&url=\"+document.getElementById(\"website\").value+\"&no_html=1\",\r\n			  dataType: \"script\"\r\n			});\';\r\n			$html .= \'\\\\\'\';\r\n			$html .= \'value=\"\' . $_MT_LANG->SPIDER . \'\" />\';\r\n			$html .= \'<span id=\"spider\' . $this->getInputFieldName(1) . \'\" style=\"margin-left:5px;background-color:white\"></span>\';\r\n		}\r\n		return $html;\r\n	}\r\n	\r\n}"  WHERE field_type = "corewebsite" LIMIT 1');
		$database->execute();

		// Update corewebsite version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "11" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated corewebsite field type to version 2.' );

		// Changing date field type name from 'date' to 'mdate'
		$database->setQuery('UPDATE jos_mt_customfields SET field_type = "mdate" WHERE field_type = "date"');
		$database->execute();
		$database->setQuery('UPDATE jos_mt_fieldtypes SET field_type = "mdate" WHERE field_type = "date"');
		$database->execute();
		
		// Update mdate class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_mDate extends mFieldType_date {\r\n}"  WHERE field_type = "mdate" LIMIT 1');
		$database->execute();

		// Update mdate version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "1.00" WHERE ft_id = "47" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated mdate field type to version 2.' );

		// Update coreprice params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3C6D6F73706172616D7320747970653D226D6F64756C65223E0A093C706172616D733E0A09093C706172616D206E616D653D22646973706C6179467265652220747970653D22726164696F222064656661756C743D223122206C6162656C3D22446973706C61792046726565207768656E207072696365206973203022206465736372697074696F6E3D2253657474696E67207468697320746F205965732077696C6C20646973706C61792074686520746578742046726565207768656E2074686520707269636520697320302E30302E223E0A0909093C6F7074696F6E2076616C75653D2231223E5965733C2F6F7074696F6E3E0A0909093C6F7074696F6E2076616C75653D2230223E4E6F3C2F6F7074696F6E3E0A09093C2F706172616D3E0A093C2F706172616D733E0A3C2F6D6F73706172616D733E, filesize = 313 WHERE ft_id = 2 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		updateVersion(2,0,6);
		$this->updated = true;
		return true;
	}
}
?>