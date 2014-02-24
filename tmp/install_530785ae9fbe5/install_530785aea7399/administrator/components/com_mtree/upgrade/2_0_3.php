<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_0_3 extends mUpgrade {
	function upgrade() {
		global $database;
		
		$this->db2dir();
		
		// Update multilineTextbox field's params.xml
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d22726f77732220747970653d2274657874222064656661756c743d223622206c6162656c3d22526f777322202f3e0a09093c706172616d206e616d653d22636f6c732220747970653d2274657874222064656661756c743d22363022206c6162656c3d22436f6c756d6e7322202f3e0a09093c706172616d206e616d653d227374796c652220747970653d2274657874222064656661756c743d2222206c6162656c3d225374796c6522206465736372697074696f6e3d225468652074657874626f782062792064656661756c74206973207374796c6564206279207468652027696e707574626f78272043535320636c6173732e20596f752063616e2073706563696679206164646974696f6e616c207374796c65206865726522202f3e0a0a09093c706172616d206e616d653d22407370616365722220747970653d22737061636572222064656661756c743d2222206c6162656c3d2222206465736372697074696f6e3d2222202f3e0a0a09093c706172616d206e616d653d2273756d6d61727943686172732220747970653d2274657874222064656661756c743d2232353522206c6162656c3d224e756d626572206f662053756d6d617279206368617261637465727322202f3e0a09093c706172616d206e616d653d22737472697053756d6d617279546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2053756d6d617279207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773207468617420636f756c6420706f74656e7469616c6c7920616666656374207768656e2076696577696e672061206c697374206f66206c697374696e67732e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22737472697044657461696c73546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2044657461696c73207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773206578636570742074686f73652074686174206172652073706563696669656420696e2027416c6c6f7765642074616773272e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22706172736555726c2220747970653d22726164696f222064656661756c743d223122206c6162656c3d2250617273652055524c206173206c696e6b20696e2044657461696c732076696577223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a0a09093c706172616d206e616d653d227374726970416c6c546167734265666f7265536176652220747970653d22726164696f222064656661756c743d223022206c6162656c3d22537472697020616c6c2048544d4c2074616773206265666f72652073746f72696e6720746f20646174616261736522206465736372697074696f6e3d224966205759535957494720656469746f7220697320656e61626c656420696e207468652066726f6e742d656e642c2074686973206665617475726520616c6c6f7720796f7520746f20737472697020616e7920706f74656e7469616c6c79206861726d66756c20636f6465732e20596f752063616e207374696c6c20616c6c6f7720736f6d6520746167732077697468696e206465736372697074696f6e206669656c642c2077686963682063616e206265207370656369666965642062656c6f772e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22616c6c6f776564546167732220747970653d2274657874222064656661756c743d22752c622c692c612c756c2c6c692c7072652c626c6f636b71756f746522206c6162656c3d22416c6c6f776564207461677322206465736372697074696f6e3d22456e7465722074686520746167206e616d65732073657065726174656420627920636f6d6d612e205468697320706172616d6574657220616c6c6f7720796f7520746f2061636365707420736f6d652048544d4c2074616773206576656e20696620796f75206861766520656e61626c65207374726970696e67206f6620616c6c2048544d4c20746167732061626f76652e22202f3e0a09090a093c2f706172616d733e0a3c2f6d6f73706172616d733e, filesize = 1967 WHERE ft_id = 26 AND filename = 'params.xml' LIMIT 1");
		$database->execute();

		// Update metadesc & metakeys class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coremetakey extends mFieldType {\r\n	var $name = \'metakey\';\r\n	var $numOfInputFields = 0;\r\n}" WHERE field_type = "coremetakey" LIMIT 1');
		$database->execute();
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coremetadesc extends mFieldType {\r\n	var $name = \'metadesc\';\r\n	var $numOfInputFields = 0;\r\n}" WHERE field_type = "coremetadesc" LIMIT 1');
		$database->execute();

		// Update year class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_year extends mFieldType {\r\n	var $numOfSearchFields = 2;\r\n	function getSearchHTML() {\r\n\r\n		$startYear = $this->getParam(\'startYear\',(date(\'Y\')-70));\r\n		$endYear = $this->getParam(\'endYear\',date(\'Y\'));\r\n		\r\n		$html = \'<select name=\"\' . $this->getSearchFieldName(2) . \'\" class=\"inputbox\" size=\"1\">\';\r\n		$html .= \'<option value=\"1\" selected=\"selected\">\' . $_MT_LANG->EXACTLY . \'</option>\';\r\n		$html .= \'<option value=\"2\">\' . $_MT_LANG->AFTER . \'</option>\';\r\n		$html .= \'<option value=\"3\">\' . $_MT_LANG->BEFORE . \'</option>\';\r\n		$html .= \'</select>\';\r\n		$html .= \'&nbsp;\';\r\n\r\n		$html .= \'<select name=\"\' . $this->getInputFieldName(1) . \'\" class=\"inputbox\">\';\r\n		$html .= \'<option value=\"\">&nbsp;</option>\';\r\n		for($year=$endYear;$year>=$startYear;$year--) {\r\n			$html .= \'<option value=\"\' . $year . \'\">\' . $year . \'</option>\';\r\n		}\r\n		$html .= \'</select>\';		\r\n\r\n		return $html;\r\n	}\r\n\r\n	function getInputHTML() {\r\n		$startYear = $this->getParam(\'startYear\',(date(\'Y\')-70));\r\n		$endYear = $this->getParam(\'endYear\',date(\'Y\'));\r\n		$value = $this->getValue();\r\n		\r\n		$html = \'\';\r\n		$html .= \'<select name=\"\' . $this->getInputFieldName() . \'\" class=\"inputbox\">\';\r\n		$html .= \'<option value=\"\">&nbsp;</option>\';\r\n		for($year=$endYear;$year>=$startYear;$year--) {\r\n			$html .= \'<option value=\"\' . $year . \'\"\';\r\n			if( $year == $value ) {\r\n				$html .= \' selected\';\r\n			}\r\n			$html .= \'>\' . $year . \'</option>\';\r\n		}\r\n		$html .= \'</select>\';		\r\n		return $html;\r\n	}\r\n	\r\n	function getWhereCondition() {\r\n		$args = func_get_args();\r\n		$fieldname = \'cfv#.value\';\r\n		if( ($args[1] >= 1 || $args[1] <= 3) && is_numeric($args[0]) ) {\r\n			switch($args[1]) {\r\n				case 1:\r\n					return $fieldname . \' = \\\\\'\' . $args[0] . \'\\\\\'\';\r\n					break;\r\n				case 2:\r\n					return $fieldname . \' > \\\\\'\' . $args[0] . \'\\\\\'\';\r\n					break;\r\n				case 3:\r\n					return $fieldname . \' < \\\\\'\' . $args[0] . \'\\\\\'\';\r\n					break;\r\n			}\r\n		} else {\r\n			return null;\r\n		}\r\n	}	\r\n}" WHERE ft_id = "46" LIMIT 1');
		$database->execute();

		// Update mfile class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_mFile extends mFieldType_file {\r\n	function getJSValidation() {\r\n		$fileExtensions = trim($this->getParam(\'fileExtensions\',\'\'));\r\n		if(!empty($fileExtensions)) {\r\n			$js = \'\';\r\n			$js .= \'} else if (!hasExt(form.\' .$this->getInputFieldName(1) . \'.value,\\\\\'\' . $fileExtensions . \'\\\\\')) {\'; \r\n			$js .= \'alert(\"\' . $this->getCaption() . \': Please select files with these extension(s) - \' . str_replace(\'|\',\', \',$fileExtensions) . \'.\");\';\r\n			return $js;\r\n		} else {\r\n			return null;\r\n		}\r\n	}\r\n}" WHERE ft_id = "48" LIMIT 1');
		$database->execute();

		// Update coredesc class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coredesc extends mFieldType {\r\n	var $name = \'link_desc\';\r\n	function parseValue($value) {\r\n		$params[\'stripAllTagsBeforeSave\'] = $this->getParam(\'stripAllTagsBeforeSave\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		if($params[\'stripAllTagsBeforeSave\']) {\r\n			$value = $this->stripTags($value,$params[\'allowedTags\']);\r\n		}\r\n		return $value;		\r\n	}\r\n	function getInputHTML() {\r\n		global $mtconf;\r\n		\r\n		$inBackEnd = (substr(dirname($_SERVER[\'PHP_SELF\']),-13) == \'administrator\') ? true : false;\r\n		if( ($inBackEnd AND $mtconf->get(\'use_wysiwyg_editor_in_admin\')) || (!$inBackEnd AND $mtconf->get(\'use_wysiwyg_editor\')) ) {\r\n			ob_start();\r\n			editorArea( \'editor1\',  $this->getValue() , $this->getInputFieldName(1), \'100%\', $this->getSize(), \'75\', \'25\' );\r\n			$html = ob_get_contents();\r\n			ob_end_clean();\r\n		} else {\r\n			$html = \'<textarea class=\"inputbox\" name=\"\' . $this->getInputFieldName(1) . \'\" style=\"width:95%;height:\' . $this->getSize() . \'px\">\' . htmlspecialchars($this->getValue()) . \'</textarea>\';\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'<input class=\"inputbox\" type=\"text\" name=\"\' . $this->getName() . \'\" size=\"30\" />\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'parseUrl\'] = $this->getParam(\'parseUrl\',1);\r\n		$params[\'summaryChars\'] = $this->getParam(\'summaryChars\',255);\r\n		$params[\'stripSummaryTags\'] = $this->getParam(\'stripSummaryTags\',1);\r\n		$params[\'stripDetailsTags\'] = $this->getParam(\'stripDetailsTags\',1);\r\n		$params[\'parseMambots\'] = $this->getParam(\'parseMambots\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		\r\n		$html = $this->getValue();\r\n		\r\n		// Details view\r\n		if($view == 1) {\r\n			global $mtconf;\r\n			if($params[\'stripDetailsTags\']) {\r\n				$html = $this->stripTags($html,$params[\'allowedTags\']);\r\n			}\r\n			if($params[\'parseUrl\'] AND $view == 0) {\r\n				$regex = \'/http:\\/\\/(.*?)(\\s|$)/i\';\r\n				$html = preg_replace_callback( $regex, array($this,\'linkcreator\'), $html );\r\n			}\r\n			if (!$mtconf->get(\'use_wysiwyg_editor\') && $params[\'stripDetailsTags\'] && !in_array(\'br\',explode(\',\',$params[\'allowedTags\'])) && !in_array(\'p\',explode($params[\'allowedTags\'])) ) {\r\n				$html = nl2br(trim($html));\r\n			}\r\n			if($params[\'parseMambots\']) {\r\n				$this->parseMambots($html);\r\n			}\r\n		// Summary view\r\n		} else {\r\n			$html = preg_replace(\'@{[\\/\\!]*?[^<>]*?}@si\', \'\', $html);\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = strip_tags( $html );\r\n			}\r\n			// $trimmed_desc = $this->html_cutstr($html,$params[\'summaryChars\']);\r\n			$trimmed_desc = $this->html_substr($html,0,$params[\'summaryChars\']);\r\n			if ($this->strlen_utf8($html) > $params[\'summaryChars\']) {\r\n				$html = $trimmed_desc . \' <b>...</b>\';\r\n			}\r\n		}\r\n		return $html;\r\n	}\r\n}" WHERE ft_id = "21" LIMIT 1');
		$database->execute();

		// Update corewebsite class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corewebsite extends mFieldType_weblink {\r\n	var $name = \'website\';\r\n\r\n	function getOutput() {\r\n		$maxUrlLength = $this->getParam(\'maxUrlLength\',60);\r\n		$text = $this->getParam(\'text\',\'\');\r\n		$openNewWindow = $this->getParam(\'openNewWindow\',1);\r\n		$useMTVisitRedirect = $this->getParam(\'useMTVisitRedirect\',1);\r\n	\r\n		$html = \'\';\r\n		$html .= \'<a href=\"\';\r\n		if($useMTVisitRedirect) {\r\n			global $Itemid;\r\n			$html .= JRoute::_(\'index.php?option=com_mtree&task=visit&link_id=\' . $this->getLinkId() . \'&Itemid=\' . $Itemid);\r\n		} else {\r\n			$html .= $this->getValue();\r\n		}\r\n		$html .= \'\"\';\r\n		if( $openNewWindow == 1 ) {\r\n			$html .= \' target=\"_blank\"\';\r\n		}\r\n		$html .= \'>\';\r\n		if(!empty($text)) {\r\n			$html .= $text;\r\n		} else {\r\n			if( empty($maxUrlLength) || $maxUrlLength == 0 ) {\r\n				$html .= $this->getValue();\r\n			} else {\r\n				$html .= substr($this->getValue(),0,$maxUrlLength);\r\n				if( strlen($this->getValue()) > $maxUrlLength ) {\r\n					$html .= $this->getParam(\'clippedSymbol\');\r\n				}\r\n			}\r\n		}\r\n		$html .= \'</a>\';\r\n		return $html;\r\n	}\r\n	\r\n	function getInputHTML() {\r\n\r\n		$showGo = $this->getParam(\'showGo\',1);\r\n		$showSpider = $this->getParam(\'showSpider\',0);\r\n		$inBackEnd = (substr(dirname($_SERVER[\'PHP_SELF\']),-13) == \'administrator\') ? true : false;\r\n		$html = \'\';\r\n		$html .= \'<input class=\"text_area\" type=\"text\" name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" size=\"\' . $this->getSize() . \'\" value=\"\' . htmlspecialchars($this->getValue()) . \'\" />\';\r\n		if($showGo) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript:window.open(\"index3.php?option=com_mtree&task=openurl&url=\"+escape(document.getElementById(\"website\").value))\\\\\'\';\r\n			$html .= \'value=\"\' . $_MT_LANG->GO . \'\" />\';\r\n		}\r\n		\r\n		if($showSpider && $inBackEnd) {\r\n			$html .= \'&nbsp;\';\r\n			$html .= \'<input type=\"button\" class=\"button\" onclick=\\\\\'\';\r\n			$html .= \'javascript: \';\r\n			$html .= \'jQuery(\"#spiderwebsite\").html(\"\' . $_MT_LANG->SPIDER_PROGRESS . \'\");\';\r\n			$html .= \'jQuery.ajax({\r\n			  type: \"POST\",\r\n			  url: mosConfig_live_site+\"/administrator/index2.php\",\r\n			  data: \"option=com_mtree&task=ajax&task2=spiderurl&url=\"+document.getElementById(\"website\").value+\"&no_html=1\",\r\n			  dataType: \"script\"\r\n			});\';\r\n			$html .= \'\\\\\'\';\r\n			$html .= \'value=\"\' . $_MT_LANG->SPIDER . \'\" />\';\r\n			$html .= \'<span id=\"spider\' . $this->getInputFieldName(1) . \'\" style=\"margin-left:5px;background-color:white\"></span>\';\r\n		}\r\n		return $html;\r\n	}\r\n	\r\n}" WHERE ft_id = "11" LIMIT 1');
		$database->execute();
		
		// Update multilineTextbox class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_multilineTextbox extends mFieldType {\r\n	function parseValue($value) {\r\n		$params[\'stripAllTagsBeforeSave\'] = $this->getParam(\'stripAllTagsBeforeSave\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		if($params[\'stripAllTagsBeforeSave\']) {\r\n			$value = $this->stripTags($value,$params[\'allowedTags\']);\r\n		}\r\n		return $value;		\r\n	}\r\n	function getInputHTML() {\r\n		$params[\'cols\'] = $this->getParam(\'cols\',60);\r\n		$params[\'rows\'] = $this->getParam(\'rows\',6);\r\n		$params[\'style\'] = $this->getParam(\'style\',\'\');\r\n		$html = \'\';\r\n		$html .= \'<textarea name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" class=\"inputbox\"\';\r\n		$html .= \' cols=\"\' . $params[\'cols\'] . \'\" rows=\"\' . $params[\'rows\'] . \'\"\';\r\n		if(!empty($params[\'style\'])) {\r\n			$html .=  \' style=\"\' . $params[\'style\'] . \'\"\';\r\n		}\r\n		$html .=  \'>\' . $this->getValue() . \'</textarea>\';\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'<input class=\"inputbox\" type=\"text\" name=\"\' . $this->getName() . \'\" size=\"30\" />\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'parseUrl\'] = $this->getParam(\'parseUrl\',1);\r\n		$params[\'summaryChars\'] = $this->getParam(\'summaryChars\',255);\r\n		$params[\'stripSummaryTags\'] = $this->getParam(\'stripSummaryTags\',1);\r\n		$params[\'stripDetailsTags\'] = $this->getParam(\'stripDetailsTags\',1);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n	\r\n		$html = $this->getValue();\r\n	\r\n		// Details view\r\n		if($view == 1) {\r\n			if($params[\'stripDetailsTags\']) {\r\n				$html = $this->stripTags($html,$params[\'allowedTags\']);\r\n			}\r\n			if($params[\'parseUrl\'] AND $view == 0) {\r\n				$regex = \'/http:\\/\\/(.*?)(\\s|$)/i\';\r\n				$html = preg_replace_callback( $regex, array($this,\'linkcreator\'), $html );\r\n			}\r\n		// Summary view\r\n		} else {\r\n			$html = preg_replace(\'@{[\\/\\!]*?[^<>]*?}@si\', \'\', $html);\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = strip_tags( $html );\r\n			} else {\r\n			}\r\n			$trimmed_desc = trim($this->html_substr($html,0,$params[\'summaryChars\']));\r\n			if ($this->strlen_utf8($html) > $params[\'summaryChars\']) {\r\n				$html = $trimmed_desc . \' <b>...</b>\';\r\n			}\r\n		}\r\n		return $html;\r\n	}\r\n}" WHERE ft_id = "26" LIMIT 1');
		$database->execute();
	
		// Delete config: show_email
		$database->setQuery('DELETE FROM #__mt_config WHERE varname = "show_email" LIMIT 1');
		$database->execute();

		// Insert new config - show_favourite
		$database->setQuery("INSERT INTO #__mt_config VALUES ('show_favourite', 'feature', '1', '1', 'yesno', 4000, 1)");
		$database->execute();

		// Re-order needapproval_replyreview
		$database->setQuery("UPDATE #__mt_config SET ordering = '8500' WHERE varname = 'needapproval_replyreview' LIMIT 1");
		$database->execute();

		// Add 5 configs for image storage for listings and categories - relative_path_to_cat* & relative_path_to_listings*
		$database->setQuery("INSERT INTO #__mt_config ( `varname` , `groupname` , `value` , `default` , `configcode` , `ordering` , `displayed` ) VALUES ('relative_path_to_cat_small_image', 'core', '', '/components/com_mtree/img/cats/s/', '', '', '0')");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_config ( `varname` , `groupname` , `value` , `default` , `configcode` , `ordering` , `displayed` ) VALUES ('relative_path_to_cat_original_image', 'core', '', '/components/com_mtree/img/cats/o/', '', '', '0')");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_config ( `varname` , `groupname` , `value` , `default` , `configcode` , `ordering` , `displayed` ) VALUES ('relative_path_to_listing_small_image', 'core', '', '/components/com_mtree/img/listings/s/', '', '', '0')");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_config ( `varname` , `groupname` , `value` , `default` , `configcode` , `ordering` , `displayed` ) VALUES ('relative_path_to_listing_medium_image', 'core', '', '/components/com_mtree/img/listings/m/', '', '', '0')");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_config ( `varname` , `groupname` , `value` , `default` , `configcode` , `ordering` , `displayed` ) VALUES ('relative_path_to_listing_original_image', 'core', '', '/components/com_mtree/img/listings/o/', '', '', '0')");
		$database->execute();
		
		$this->addColumn('reports', 'rev_id', 'int(10) unsigned NOT NULL default \'0\'', 'link_id');
	
		updateVersion(2,0,3);
		$this->updated = true;
		return true;
	}
	
	function db2dir() {
		global $database, $mosConfig_absolute_path, $mosConfig_dbprefix;
		$this->printStatus('Starting to transfer images from database to filesystem.',2);

		// Add new column (cat_image) to #__mt_cats
		$this->addColumn('cats', 'cat_image', 'VARCHAR( 255 ) NOT NULL', 'cat_featured');
		// $database->setQuery("ALTER TABLE #__mt_cats ADD cat_image  AFTER cat_featured");
		// $database->execute();

		// Migrates categories' images
		$cat_images_count = 0;
		$database->setQuery( "SHOW TABLE STATUS LIKE '" . $mosConfig_dbprefix . "mt_cats_images'" );
		$table = $database->loadObject();
		if(isset($table->Name) && $table->Name == $mosConfig_dbprefix.'mt_cats_images') {
			$database->setQuery("SELECT COUNT(*) FROM #__mt_cats_images");
			$cat_images_count = $database->loadResult();
		}
		
		if($cat_images_count>0) {
			$database->setQuery("SELECT * FROM #__mt_cats_images");
			$cat_images = $database->loadObjectList();
			foreach($cat_images AS $cat_image) {
				$file_extension = pathinfo($cat_image->filename);
				$file_extension = strtolower($file_extension['extension']);
				$this->makeFile($cat_image->small_filedata,$mosConfig_absolute_path . '/components/com_mtree/img/cats/s/' . $cat_image->cat_id . '.' . $file_extension);
				$this->makeFile($cat_image->original_filedata,$mosConfig_absolute_path . '/components/com_mtree/img/cats/o/' . $cat_image->cat_id . '.' . $file_extension);
				$database->setQuery("UPDATE #__mt_cats SET cat_image = '" . $cat_image->cat_id . '.' . $file_extension . "' WHERE cat_id = " . $cat_image->cat_id . " LIMIT 1");
				$database->execute();
			}
			$this->printStatus('Finish transfering ' . $cat_images_count . ' categories\' images.',2);
		} else {
			$this->printStatus('No category images to transfer.',0);
		}

		// Drop #__mt_cats_images
		$database->setQuery("DROP TABLE IF EXISTS #__mt_cats_images");
		$database->execute();

		// Migrates listings' images
		$images_count = 0;
		$database->setQuery( "SHOW TABLE STATUS LIKE '" . $mosConfig_dbprefix . "mt_images'" );
		$table = $database->loadObject();
		if(isset($table->Name) && $table->Name == $mosConfig_dbprefix.'mt_images') {
			$database->setQuery( 'SHOW COLUMNS FROM #__mt_images LIKE "small_filedata"' );
			$tmp = $database->loadResult();
			if ( $tmp == 'small_filedata' ) {
				$database->setQuery("SELECT COUNT(*) FROM #__mt_images");
				$images_count = $database->loadResult();
			}
		}
		
		if($images_count>0) {
			$database->setQuery("SELECT img_id FROM #__mt_images");
			$images = $database->loadObjectList();
			foreach($images AS $image) {
				$database->setQuery("SELECT * FROM #__mt_images WHERE img_id = '" . $image->img_id . "' LIMIT 1");
				$listing_image = $database->loadObject();
				$file_extension = pathinfo($listing_image->filename);
				$file_extension = strtolower($file_extension['extension']);
				$this->makeFile($listing_image->small_filedata,$mosConfig_absolute_path . '/components/com_mtree/img/listings/s/' . $listing_image->img_id . '.' . $file_extension);
				$this->makeFile($listing_image->medium_filedata,$mosConfig_absolute_path . '/components/com_mtree/img/listings/m/' . $listing_image->img_id . '.' . $file_extension);
				$this->makeFile($listing_image->original_filedata,$mosConfig_absolute_path . '/components/com_mtree/img/listings/o/' . $listing_image->img_id . '.' . $file_extension);
				$database->setQuery("UPDATE #__mt_images SET filename = '" . $listing_image->img_id . '.' . $file_extension . "' WHERE img_id = " . $listing_image->img_id . " LIMIT 1");
				$database->execute();
			}
			$this->printStatus('Finish transfering ' . $images_count . ' listings\' images.',2);
		} else {
			$this->printStatus('No listing images to transfer.',0);
		}

		// Drop deprecated columns
		$database->setQuery( 'SHOW COLUMNS FROM #__mt_images LIKE "small_filedata"' );
		$tmp = $database->loadResult();
		if ( $tmp == 'small_filedata' ) {
			$database->setQuery("ALTER TABLE #__mt_images DROP `small_filedata`, DROP `small_filesize`, DROP `medium_filedata`, DROP `medium_filesize`, DROP `original_filedata`, DROP `original_filesize`,  DROP `extension`");
			$database->execute();
		}
		$this->printStatus('Completed image transfer!',1);
	}
	function makeFile($filedata,$filepath) {
		if($fp = fopen($filepath,'w')) {
			if(fwrite($fp,$filedata) === false) {
				$this->printStatus('Unable to write to file: ' . $filepath, 2);
				break;
			}
		} else {
			$this->printStatus('Unable to open for writing: ' . $filepath, 2);
			break;
		}
	}
}
?>