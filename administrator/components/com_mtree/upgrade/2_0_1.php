<?php
/**
 * @version		$Id: 2_0_1.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_0_1 extends mUpgrade {
	function upgrade() {
		global $database;
		
		$this->addColumn('reviews', 'vote_helpful', 'INT UNSIGNED NOT NULL DEFAULT \'0\'');
		$this->addColumn('reviews', 'vote_total', 'INT UNSIGNED NOT NULL DEFAULT \'0\'');
		$this->addColumn('reviews', 'ownersreply_text', 'TEXT NOT NULL');
		$this->addColumn('reviews', 'ownersreply_date', 'DATETIME NOT NULL');
		$this->addColumn('reviews', 'ownersreply_approved', 'TINYINT NOT NULL DEFAULT \'0\'');
		$this->addColumn('reviews', 'ownersreply_admin_note', 'MEDIUMTEXT NOT NULL');
		$this->addColumn('reviews', 'send_email', 'TINYINT NOT NULL DEFAULT \'0\'');
		$this->addColumn('reviews', 'email_message', 'MEDIUMTEXT NOT NULL');
		$this->addRows('fieldtypes_info',array(array('23', '1.00', 'http://www.mosets.com/', ''), array('26', '1.00', 'http://www.mosets.com/', '')));
		
		// Update corewebsite field
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corewebsite extends mFieldType_weblink {\r\n	var $name = \'website\';\r\n}" WHERE ft_id = "11" LIMIT 1');
		$database->execute();
		// $database->setQuery('INSERT INTO #__mt_fieldtypes VALUES (11, "corewebsite", "Website", "class mFieldType_corewebsite extends mFieldType_weblink {\r\n	var $name = \'website\';\r\n}", 0, 0, 0, 1);');
		// $database->execute();
		$database->setQuery("INSERT INTO #__mt_fieldtypes_att VALUES (115, 11, 'params.xml', 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d226f70656e4e657757696e646f772220747970653d22726164696f222064656661756c743d223122206c6162656c3d224f70656e204e65772057696e646f7722206465736372697074696f6e3d224f70656e2061206e65772077696e646f77207768656e20746865206c696e6b20697320636c69636b65642e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22746578742220747970653d2274657874222064656661756c743d2222206c6162656c3d224c696e6b205465787422206465736372697074696f6e3d22557365207468697320706172616d6574657220746f207370656369667920746865206c696e6b20746578742e204966206c65667420656d7074792c207468652066756c6c2055524c2077696c6c20626520646973706c6179656420617320746865206c696e6b277320746578742e22202f3e0a09093c706172616d206e616d653d226d617855726c4c656e6774682220747970653d2274657874222064656661756c743d22363022206c6162656c3d224d61782e2055524c204c656e67746822206465736372697074696f6e3d22456e74657220746865206d6178696d756d2055524c2773206c656e677468206265666f726520697420697320636c697070656422202f3e0a09093c706172616d206e616d653d22636c697070656453796d626f6c2220747970653d2274657874222064656661756c743d222e2e2e22206c6162656c3d22436c69707065642073796d626f6c22202f3e0a093c2f706172616d733e0a3c2f6d6f73706172616d733e, 694, 'text/xml', 1)");
		$database->execute();
		
		// Update weblinknewwin field
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_weblinkNewWin extends mFieldType_weblink {\r\n\r\n}" WHERE field_type = "weblinknewwin" LIMIT 1');
		$database->execute();
		$database->setQuery("UPDATE #__mt_fieldtypes_att SET filedata = 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d226f70656e4e657757696e646f772220747970653d22726164696f222064656661756c743d223122206c6162656c3d224f70656e204e65772057696e646f7722206465736372697074696f6e3d224f70656e2061206e65772077696e646f77207768656e20746865206c696e6b20697320636c69636b65642e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22746578742220747970653d2274657874222064656661756c743d2222206c6162656c3d224c696e6b205465787422206465736372697074696f6e3d22557365207468697320706172616d6574657220746f207370656369667920746865206c696e6b20746578742e204966206c65667420656d7074792c207468652066756c6c2055524c2077696c6c20626520646973706c6179656420617320746865206c696e6b277320746578742e22202f3e0a09093c706172616d206e616d653d226d617855726c4c656e6774682220747970653d2274657874222064656661756c743d22363022206c6162656c3d224d61782e2055524c204c656e67746822206465736372697074696f6e3d22456e74657220746865206d6178696d756d2055524c2773206c656e677468206265666f726520697420697320636c697070656422202f3e0a09093c706172616d206e616d653d22636c697070656453796d626f6c2220747970653d2274657874222064656661756c743d222e2e2e22206c6162656c3d22436c69707065642073796d626f6c22202f3e0a093c2f706172616d733e0a3c2f6d6f73706172616d733e, filesize = 694 WHERE ft_id = 23 AND filename = 'params.xml' LIMIT 1");
		$database->execute();
		$database->setQuery("DELETE FROM #__mt_fieldtypes_att WHERE ft_id = 23 AND filename = 'application_double.png' LIMIT 1");
		$database->execute();
		
		// Update onlinevideo field
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_onlinevideo extends mFieldType {\r\n\r\n	function getOutput() {\r\n		$html =\'\';\r\n		$id = $this->getVideoId();\r\n		$videoProvider = $this->getParam(\'videoProvider\',\'youtube\');\r\n		switch($videoProvider) {\r\n			case \'youtube\':\r\n				$html .= \'<object width=\"425\" height=\"350\">\';\r\n				$html .= \'<param name=\"movie\" value=\"http://www.youtube.com/v/\' . $id . \'\"></param>\';\r\n				$html .= \'<param name=\"wmode\" value=\"transparent\"></param>\';\r\n				$html .= \'<embed src=\"http://www.youtube.com/v/\' . $id . \'\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed>\';\r\n				$html .= \'</object>\';\r\n				break;\r\n			case \'googlevideo\':\r\n				$html .= \'<embed style=\"width:400px; height:326px;\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=\' . $id . \'\">\';\r\n				$html .= \'</embed>\';\r\n				break;\r\n			/*\r\n			case \'metacafe\':\r\n				$html .= \'<embed src=\"http://www.metacafe.com/fplayer/\' . $id . \'.swf\" width=\"400\" height=\"345\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed>\';\r\n				break;\r\n			case \'ifilm\':\r\n				$html .= \'<embed width=\"448\" height=\"365\" src=\"http://www.ifilm.com/efp\" quality=\"high\" bgcolor=\"000000\" name=\"efp\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" flashvars=\"flvbaseclip=\' . $id . \'&amp;\"></embed>\';\r\n				break;\r\n			*/\r\n		}\r\n		return $html;\r\n	}\r\n	\r\n	function getVideoId() {\r\n		$videoProvider = $this->getParam(\'videoProvider\',\'youtube\');\r\n		$value = $this->getValue();\r\n		$id = null;\r\n		if(empty($value)) {\r\n			return null;\r\n		}\r\n		$url = parse_url($value);\r\n	    parse_str($url[\'query\'], $query);\r\n		switch($videoProvider) {\r\n			case \'youtube\':\r\n				if (isset($query[\'v\'])) {\r\n			        $id = $query[\'v\'];\r\n			    }\r\n				break;\r\n			case \'googlevideo\':\r\n			    if (isset($query[\'docid\'])) {\r\n			        $id = $query[\'docid\'];\r\n			    }\r\n				break;\r\n		}\r\n		return $id;\r\n	}\r\n	\r\n	function getInputHTML() {\r\n		$videoProvider = $this->getParam(\'videoProvider\',\'youtube\');\r\n		$youtubeInputDescription = $this->getParam(\'youtubeInputDescription\',\'Enter the full URL of the Youtube video page.<br />ie: <b>http://youtube.com/watch?v=OHpANlSG7OI</b>\');\r\n		$googlevideoInputDescription = $this->getParam(\'googlevideoInputDescription\',\'Enter the full URL of the Google video page.<br />ie: <b>http://video.google.com/videoplay?docid=832064557062572361</b>\');\r\n		$html = \'\';\r\n		$html .= \'<input class=\"text_area\" type=\"text\" name=\"\' . $this->getInputFieldName(1) . \'\" id=\"\' . $this->getInputFieldName(1) . \'\" size=\"\' . $this->getSize() . \'\" value=\"\' . htmlspecialchars($this->getValue()) . \'\" />\';\r\n		switch($videoProvider) {\r\n			case \'youtube\':\r\n				if(!empty($youtubeInputDescription)) {\r\n					$html .= \'<br />\' . $youtubeInputDescription;\r\n				}\r\n				break;\r\n			case \'googlevideo\':\r\n				if(!empty($googlevideoInputDescription)) {\r\n					$html .= \'<br />\' . $googlevideoInputDescription;\r\n				}\r\n		}\r\n		return $html;\r\n	}\r\n	\r\n	function getSearchHTML() {\r\n		$checkboxLabel = $this->getParam(\'checkboxLabel\',\'Contains video\');\r\n		return \'<input class=\"text_area\" type=\"checkbox\" name=\"\' . $this->getSearchFieldName(1) . \'\" id=\"\' . $this->getSearchFieldName(1) . \'\" />&nbsp;<label for=\"\' . $this->getName() . \'\">\' . $checkboxLabel . \'</label>\';\r\n	}\r\n	\r\n	function getWhereCondition() {\r\n		if( func_num_args() == 0 ) {\r\n			return null;\r\n		} else {\r\n			return \'(cfv#.value <> \'\')\';\r\n		}\r\n	}\r\n}" WHERE ft_id = "29" LIMIT 1');
		$database->execute();
		$database->setQuery("DELETE FROM #__mt_fieldtypes_att WHERE ft_id = 29");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_fieldtypes_att VALUES ('', 29, 'params.xml', 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0d0a093c706172616d733e0d0a09093c706172616d206e616d653d22766964656f50726f76696465722220747970653d226c697374222064656661756c743d2222206c6162656c3d22566964656f2050726f7669646572223e0d0a0909093c6f7074696f6e2076616c75653d22796f7574756265223e596f75747562653c2f6f7074696f6e3e0d0a0909093c6f7074696f6e2076616c75653d22676f6f676c65766964656f223e476f6f676c6520566964656f3c2f6f7074696f6e3e0d0a0909093c212d2d203c6f7074696f6e2076616c75653d226d65746163616665223e4d657461636166653c2f6f7074696f6e3e202d2d3e0d0a0909093c212d2d203c6f7074696f6e2076616c75653d226966696c6d223e6946696c6d3c2f6f7074696f6e3e202d2d3e0d0a09093c2f706172616d3e0d0a09093c706172616d206e616d653d22636865636b626f784c6162656c2220747970653d2274657874222064656661756c743d22436f6e7461696e7320766964656f22206c6162656c3d22536561726368277320636865636b626f78206c6162656c22202f3e0d0a09093c706172616d206e616d653d22796f7574756265496e7075744465736372697074696f6e2220747970653d2274657874222064656661756c743d22456e746572207468652066756c6c2055524c206f662074686520596f757475626520766964656f20706167652e266c743b6272202f2667743b69653a20266c743b622667743b687474703a2f2f796f75747562652e636f6d2f77617463683f763d4f4870414e6c5347374f49266c743b2f622667743b22206c6162656c3d22496e707574206465736372697074696f6e22202f3e0d0a09093c706172616d206e616d653d22676f6f676c65766964656f496e7075744465736372697074696f6e2220747970653d2274657874222064656661756c743d22456e746572207468652066756c6c2055524c206f662074686520476f6f676c6520766964656f20706167652e266c743b6272202f2667743b69653a20266c743b622667743b687474703a2f2f766964656f2e676f6f676c652e636f6d2f766964656f706c61793f646f6369643d383332303634353537303632353732333631266c743b2f622667743b22206c6162656c3d22496e707574206465736372697074696f6e22202f3e0d0a093c2f706172616d733e0d0a3c2f6d6f73706172616d733e, 905, 'text/xml', 1);");
		$database->execute();
		
		// Update corename field
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_corename extends mFieldType {\r\n	var $name = \'link_name\';\r\n	function getOutput($view=1) {\r\n		$params[\'maxSummaryChars\'] = intval($this->getParam(\'maxSummaryChars\',55));\r\n		$params[\'maxDetailsChars\'] = intval($this->getParam(\'maxDetailsChars\',0));\r\n		$value = $this->getValue();\r\n		$output = \'\';\r\n		if($view == 1 AND $params[\'maxDetailsChars\'] > 0 AND $this->strlen_utf8($value) > $params[\'maxDetailsChars\']) {\r\n			$output .= $this->html_cutstr($value,$params[\'maxDetailsChars\']);\r\n			$output .= \'...\';\r\n		} elseif($view == 2 AND $params[\'maxSummaryChars\'] > 0 AND $this->strlen_utf8($value) > $params[\'maxSummaryChars\']) {\r\n			$output .= $this->html_cutstr($value,$params[\'maxSummaryChars\']);\r\n			$output .= \'...\';\r\n		} else {\r\n			$output = $value;\r\n		}\r\n		return $output;\r\n	}\r\n	\r\n	function strlen_utf8($str)	{ return strlen(utf8_decode($this->utf8_html_entity_decode($str)));	}\r\n	function utf8_replaceEntity($result){\r\n		$value = intval($result[1]);\r\n		$string = \'\';\r\n		$len = round(pow($value,1/8));\r\n		for($i=$len;$i>0;$i--){\r\n		    $part = ($value AND (255>>2)) | pow(2,7);\r\n		    if ( $i == 1 ) $part |= 255<<(8-$len);\r\n		    $string = chr($part) . $string;\r\n		    $value >>= 6;\r\n		}\r\n		return $string;\r\n	}\r\n	function utf8_html_entity_decode($string){ return preg_replace_callback(\'/&#([0-9]+);/u\',array($this,\'utf8_replaceEntity\'),$string);	}\r\n	function html_cutstr($str, $len) {\r\n		if (!preg_match(\'/\\&#[0-9]*;.*/i\', $str)) {\r\n			return substr($str, 0, $len);\r\n		}\r\n		$chars = 0;\r\n		$start = 0;\r\n		for($i=0; $i < strlen($str); $i++) {\r\n			if ($chars >= $len) {\r\n				break;\r\n			}\r\n		    $str_tmp = substr($str, $start, $i-$start);\r\n		    if (preg_match(\'/\\&#[0-9]*;.*/i\', $str_tmp)) {\r\n				$chars++;\r\n		        $start = $i;\r\n		    }\r\n		}\r\n		$rVal = substr($str, 0, $start);\r\n		if (strlen($str) > $start)\r\n		return $rVal;\r\n	}\r\n}" WHERE ft_id = "20" LIMIT 1');
		$database->execute();
		
		// Update coredesc field
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = \'class mFieldType_coredesc extends mFieldType {\r\n	var $name = \'\'link_desc\'\';\r\n	function stripTags($value) {\r\n		$params[\'\'allowedTags\'\'] = $this->getParam(\'\'allowedTags\'\',\'\'u,b,i,a,ul,li,pre,br,blockquote\'\');\r\n		if(!empty($params[\'\'allowedTags\'\'])) {\r\n			$tmp = explode(\'\',\'\',$params[\'\'allowedTags\'\']);\r\n			array_walk($tmp,\'\'trim\'\');\r\n			$allowedTags = \'\'<\'\' . implode(\'\'><\'\',$tmp) . \'\'>\'\';\r\n		} else {\r\n			$allowedTags = \'\'\'\';\r\n		}\r\n		return strip_tags( $value, $allowedTags );\r\n	}\r\n	function parseValue($value) {\r\n		$params[\'\'stripAllTagsBeforeSave\'\'] = $this->getParam(\'\'stripAllTagsBeforeSave\'\',0);\r\n		if($params[\'\'stripAllTagsBeforeSave\'\']) {\r\n			$value = $this->stripTags($value);\r\n		}\r\n		return $value;		\r\n	}\r\n	function getInputHTML() {\r\n		global $mtconf;\r\n		\r\n		$inBackEnd = (substr(dirname($_SERVER[\'\'PHP_SELF\'\']),-13) == \'\'administrator\'\') ? true : false;\r\n		if( ($inBackEnd AND $mtconf->get(\'\'use_wysiwyg_editor_in_admin\'\')) || (!$inBackEnd AND $mtconf->get(\'\'use_wysiwyg_editor\'\')) ) {\r\n			ob_start();\r\n			editorArea( \'\'editor1\'\',  $this->getValue() , $this->getInputFieldName(1), \'\'100%\'\', $this->getSize(), \'\'75\'\', \'\'25\'\' );\r\n			$html = ob_get_contents();\r\n			ob_end_clean();\r\n		} else {\r\n			$html = \'\'<textarea class="inputbox" name="\'\' . $this->getInputFieldName(1) . \'\'" style="width:95%;height:\'\' . $this->getSize() . \'\'px">\'\' . htmlspecialchars($this->getValue()) . \'\'</textarea>\'\';\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'\'<input class="inputbox" type="text" name="\'\' . $this->getName() . \'\'" size="30" />\'\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'\'parseUrl\'\'] = $this->getParam(\'\'parseUrl\'\',1);\r\n		$params[\'\'summaryChars\'\'] = $this->getParam(\'\'summaryChars\'\',255);\r\n		$params[\'\'stripSummaryTags\'\'] = $this->getParam(\'\'stripSummaryTags\'\',1);\r\n		$params[\'\'stripDetailsTags\'\'] = $this->getParam(\'\'stripDetailsTags\'\',1);\r\n		$params[\'\'parseMambots\'\'] = $this->getParam(\'\'parseMambots\'\',0);\r\n		\r\n		$html = $this->getValue();\r\n		\r\n		// Details view\r\n		if($view == 1) {\r\n			global $mtconf;\r\n			if($params[\'\'stripDetailsTags\'\']) {\r\n				$html = $this->stripTags($html);\r\n			}\r\n			if($params[\'\'parseUrl\'\'] AND $view == 0) {\r\n				$regex = \'\'/http:\\/\\/(.*?)(\\s|$)/i\'\';\r\n				$html = preg_replace_callback( $regex, array($this,\'\'linkcreator\'\'), $html );\r\n			}\r\n			if (!$mtconf->get(\'\'use_wysiwyg_editor\'\')) {\r\n				$html = nl2br(trim($html));\r\n			}\r\n			if($params[\'\'parseMambots\'\']) {\r\n				$this->parseMambots($html);\r\n			}\r\n		// Summary view\r\n		} else {\r\n			$html = preg_replace(\'\'@{[\\/\\!]*?[^<>]*?}@si\'\', \'\'\'\', $html);\r\n			if($params[\'\'stripSummaryTags\'\']) {\r\n				$html = strip_tags( $html );\r\n			}\r\n			$trimmed_desc = $this->html_cutstr($html,$params[\'\'summaryChars\'\']);\r\n			if  ($this->strlen_utf8($html) > $params[\'\'summaryChars\'\']) {\r\n				$html = $trimmed_desc . \'\' <b>...</b>\'\';\r\n			}\r\n		}\r\n		return $html;\r\n	}\r\n	function parseMambots( &$html ) {\r\n		global $_MAMBOTS, $mtconf;\r\n\r\n		$_MAMBOTS->loadBotGroup( \'\'content\'\' );\r\n\r\n		// Load Parameters\r\n		$params =& new mosParameters( \'\'\'\' );\r\n		$link = new stdclass;\r\n		$link->text = $html;\r\n		\r\n		$link->id = 1;\r\n		$link->title = \'\'\'\';\r\n		$page = 0;\r\n		$results = $_MAMBOTS->trigger( \'\'onPrepareContent\'\', array( &$link, &$params, $page ), true );\r\n		$html = $link->text;\r\n\r\n		return true;\r\n	}\r\n	function linkcreator( $matches )\r\n	{	\r\n		$url = \'\'http://\'\';\r\n		$append = \'\'\'\';\r\n\r\n		if ( in_array(substr($matches[1],-1), array(\'\'.\'\',\'\')\'\')) ) {\r\n			$url .= substr($matches[1], 0, -1);\r\n			$append = substr($matches[1],-1);\r\n\r\n		# Prevent cutting off breaks <br />\r\n		} elseif( substr($matches[1],-3) == \'\'<br\'\' ) {\r\n			$url .= substr($matches[1], 0, -3);\r\n			$append = substr($matches[1],-3);\r\n\r\n		} elseif( substr($matches[1],-1) == \'\'>\'\' ) {\r\n			$regex = \'\'/<(.*?)>/i\'\';\r\n			preg_match( $regex, $matches[1], $tags );\r\n			if( !empty($tags[1]) ) {\r\n				$append = \'\'<\'\'.$tags[1].\'\'>\'\';\r\n				$url .= $matches[1];\r\n				$url = str_replace( $append, \'\'\'\', $url );\r\n			}\r\n		} else {\r\n			$url .= $matches[1];\r\n		}\r\n\r\n		return \'\'<a href="\'\'.$url.\'\'" target="_blank">\'\'.$url.\'\'</a>\'\'.$append.\'\' \'\';\r\n	}\r\n	function strlen_utf8($str)	{ return strlen(utf8_decode($this->utf8_html_entity_decode($str)));	}\r\n	function utf8_replaceEntity($result){\r\n		$value = intval($result[1]);\r\n		$string = \'\'\'\';\r\n		$len = round(pow($value,1/8));\r\n		for($i=$len;$i>0;$i--){\r\n		    $part = ($value AND (255>>2)) | pow(2,7);\r\n		    if ( $i == 1 ) $part |= 255<<(8-$len);\r\n		    $string = chr($part) . $string;\r\n		    $value >>= 6;\r\n		}\r\n		return $string;\r\n	}\r\n	function utf8_html_entity_decode($string){ return preg_replace_callback(\'\'/&#([0-9]+);/u\'\',array($this,\'\'utf8_replaceEntity\'\'),$string); }\r\n	function html_cutstr($str, $len) {\r\n		if (!preg_match(\'\'/\\&#[0-9]*;.*/i\'\', $str)) {\r\n			return substr($str,0,$len);\r\n		}\r\n\r\n		$chars = 0;\r\n		$start = 0;\r\n		for($i=0; $i < strlen($str); $i++) {\r\n			if ($chars >= $len) {\r\n				break;\r\n			}\r\n		    $str_tmp = substr($str, $start, $i-$start);\r\n		    if (preg_match(\'\'/\\&#[0-9]*;.*/i\'\', $str_tmp)) {\r\n				$chars++;\r\n		        $start = $i;\r\n		    }\r\n		}\r\n		$rVal = substr($str, 0, $start);\r\n		if (strlen($str) > $start)\r\n		return $rVal;\r\n	}\r\n}\' WHERE field_type = \'coredesc\' LIMIT 1');
		$database->execute();
		$database->setQuery("DELETE FROM #__mt_fieldtypes_att WHERE ft_id = 21");
		$database->execute();
		$database->setQuery("INSERT INTO #__mt_fieldtypes_att VALUES ('', 21, 'params.xml', 0x3c6d6f73706172616d7320747970653d226d6f64756c65223e0a093c706172616d733e0a09093c706172616d206e616d653d2273756d6d61727943686172732220747970653d2274657874222064656661756c743d2232353522206c6162656c3d224e756d626572206f662053756d6d617279206368617261637465727322202f3e0a09093c706172616d206e616d653d22737472697053756d6d617279546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2053756d6d617279207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773207468617420636f756c6420706f74656e7469616c6c7920616666656374207768656e2076696577696e672061206c697374206f66206c697374696e67732e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22737472697044657461696c73546167732220747970653d22726164696f222064656661756c743d223122206c6162656c3d22537472697020616c6c2048544d4c207461677320696e2044657461696c73207669657722206465736372697074696f6e3d2253657474696e67207468697320746f207965732077696c6c2072656d6f766520616c6c2074616773206578636570742074686f73652074686174206172652073706563696669656420696e2027416c6c6f7765642074616773272e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22706172736555726c2220747970653d22726164696f222064656661756c743d223122206c6162656c3d2250617273652055524c206173206c696e6b20696e2044657461696c732076696577223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a0a09093c706172616d206e616d653d227374726970416c6c546167734265666f7265536176652220747970653d22726164696f222064656661756c743d223022206c6162656c3d22537472697020616c6c2048544d4c2074616773206265666f72652073746f72696e6720746f20646174616261736522206465736372697074696f6e3d224966205759535957494720656469746f7220697320656e61626c656420696e207468652066726f6e742d656e642c2074686973206665617475726520616c6c6f7720796f7520746f20737472697020616e7920706f74656e7469616c6c79206861726d66756c20636f6465732e20596f752063616e207374696c6c20616c6c6f7720736f6d6520746167732077697468696e206465736372697074696f6e206669656c642c2077686963682063616e206265207370656369666965642062656c6f772e223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a09093c706172616d206e616d653d22616c6c6f776564546167732220747970653d2274657874222064656661756c743d22752c622c692c612c756c2c6c692c7072652c626c6f636b71756f746522206c6162656c3d22416c6c6f776564207461677322206465736372697074696f6e3d22456e7465722074686520746167206e616d65732073657065726174656420627920636f6d6d612e205468697320706172616d6574657220616c6c6f7720796f7520746f2061636365707420736f6d652048544d4c2074616773206576656e20696620796f75206861766520656e61626c65207374726970696e67206f6620616c6c2048544d4c20746167732061626f76652e22202f3e0a09093c706172616d206e616d653d2270617273654d616d626f74732220747970653d22726164696f222064656661756c743d223022206c6162656c3d225061727365204d616d626f747322206465736372697074696f6e3d22456e61626c696e6720746869732077696c6c207061727365206d616d626f747320636f646573732077697468696e20746865206465736372697074696f6e206669656c64223e0a0909093c6f7074696f6e2076616c75653d2230223e4e6f3c2f6f7074696f6e3e0a0909093c6f7074696f6e2076616c75653d2231223e5965733c2f6f7074696f6e3e0a09093c2f706172616d3e0a093c2f706172616d733e0a3c2f6d6f73706172616d733e, 1822, 'text/xml', 1)");
		$database->execute();
		
		updateVersion(2,0,1);
		return true;
	}
}
?>