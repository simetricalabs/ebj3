<?php
/**
 * @version		$Id: 2_0_8.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_0_8 extends mUpgrade {
	function upgrade() {
		global $database;
		
		// Added new internal config for maximum image's size
		$database->setQuery('INSERT INTO #__mt_config (`varname`, `groupname`, `value`, `default`, `configcode`, `ordering`, `displayed`) VALUES ("image_maxsize", "image", "3145728", "3145728", "text", "10300", "1")');
		$database->execute();
		$this->printStatus( 'Added new internal config for maximum image\'s size.' );
		
		// Update coredesc class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_coredesc extends mFieldType {\r\n	var $name = \'link_desc\';\r\n	function parseValue($value) {\r\n		$params[\'stripAllTagsBeforeSave\'] = $this->getParam(\'stripAllTagsBeforeSave\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		if($params[\'stripAllTagsBeforeSave\']) {\r\n			$value = $this->stripTags($value,$params[\'allowedTags\']);\r\n		}\r\n		return $value;		\r\n	}\r\n	function getInputHTML() {\r\n		global $mtconf;\r\n		\r\n		$inBackEnd = (substr(dirname($_SERVER[\'PHP_SELF\']),-13) == \'administrator\') ? true : false;\r\n		if( ($inBackEnd AND $mtconf->get(\'use_wysiwyg_editor_in_admin\')) || (!$inBackEnd AND $mtconf->get(\'use_wysiwyg_editor\')) ) {\r\n			ob_start();\r\n			editorArea( \'editor1\',  $this->getValue() , $this->getInputFieldName(1), \'100%\', $this->getSize(), \'75\', \'25\' );\r\n			$html = ob_get_contents();\r\n			ob_end_clean();\r\n		} else {\r\n			$html = \'<textarea class=\"inputbox\" name=\"\' . $this->getInputFieldName(1) . \'\" style=\"width:95%;height:\' . $this->getSize() . \'px\">\' . htmlspecialchars($this->getValue()) . \'</textarea>\';\r\n		}\r\n		return $html;\r\n	}\r\n	function getSearchHTML() {\r\n		return \'<input class=\"inputbox\" type=\"text\" name=\"\' . $this->getName() . \'\" size=\"30\" />\';\r\n	}\r\n	function getOutput($view=1) {\r\n		$params[\'parseUrl\'] = $this->getParam(\'parseUrl\',1);\r\n		$params[\'summaryChars\'] = $this->getParam(\'summaryChars\',255);\r\n		$params[\'stripSummaryTags\'] = $this->getParam(\'stripSummaryTags\',1);\r\n		$params[\'stripDetailsTags\'] = $this->getParam(\'stripDetailsTags\',1);\r\n		$params[\'parseMambots\'] = $this->getParam(\'parseMambots\',0);\r\n		$params[\'allowedTags\'] = $this->getParam(\'allowedTags\',\'u,b,i,a,ul,li,pre,br,blockquote\');\r\n		$params[\'showReadMore\'] = $this->getParam(\'showReadMore\',0);\r\n		$params[\'whenReadMore\'] = $this->getParam(\'whenReadMore\',0);\r\n		$params[\'txtReadMore\'] = $this->getParam(\'txtReadMore\',(( $GLOBALS[\'_VERSION\']->RELEASE == \'1.0\' )?_READ_MORE:JTEXT::_( \'Read More...\' )));\r\n		\r\n		$html = $this->getValue();\r\n		\r\n		// Details view\r\n		if($view == 1) {\r\n			global $mtconf;\r\n			if($params[\'stripDetailsTags\']) {\r\n				$html = $this->stripTags($html,$params[\'allowedTags\']);\r\n			}\r\n			if($params[\'parseUrl\']) {\r\n				$regex = \'/http:\\\\/\\\\/(.*?)(\\\\s|$)/i\';\r\n				$html = preg_replace_callback( $regex, array($this,\'linkcreator\'), $html );\r\n			}\r\n			if (!$mtconf->get(\'use_wysiwyg_editor\') && $params[\'stripDetailsTags\'] && !in_array(\'br\',explode(\',\',$params[\'allowedTags\'])) && !in_array(\'p\',explode(\',\',$params[\'allowedTags\'])) ) {\r\n				$html = nl2br(trim($html));\r\n			}\r\n			if($params[\'parseMambots\']) {\r\n				$this->parseMambots($html);\r\n			}\r\n		// Summary view\r\n		} else {\r\n			global $Itemid;\r\n			$html = preg_replace(\'@{[\\\\/\\\\!]*?[^<>]*?}@si\', \'\', $html);\r\n			if($params[\'stripSummaryTags\']) {\r\n				$html = strip_tags( $html );\r\n			}\r\n			if($params[\'summaryChars\'] > 0) {\r\n				$trimmed_desc = trim($this->html_substr($html,0,$params[\'summaryChars\']));\r\n			} else {\r\n				$trimmed_desc = \'\';\r\n			}\r\n			if ($this->strlen_utf8($html) > $params[\'summaryChars\']) {\r\n				$html = $trimmed_desc;\r\n				$html .= \' <b>...</b>\';\r\n			}\r\n			if( $params[\'showReadMore\'] && ($params[\'whenReadMore\'] == 1 || ($params[\'whenReadMore\'] == 0 && $this->strlen_utf8($html) > $params[\'summaryChars\'])) ) {\r\n				if(!empty($trimmed_desc)) {\r\n					$html .= \'<br />\';\r\n				}\r\n				$html .= \' <a href=\"\' . JRoute::_(\'index.php?option=com_mtree&task=viewlink&link_id=\' . $this->getLinkId() . \'&Itemid=\' . $Itemid) . \'\" class=\"readon\">\' . $params[\'txtReadMore\'] . \'</a>\';\r\n			}\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "coredesc" LIMIT 1');
		$database->execute();

		// Update coredesc version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "21" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated coredesc field type to version 2.' );

		// Update mfile class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_mFile extends mFieldType_file {\r\n	function getJSValidation() {\r\n		$fileExtensions = $this->getParam(\'fileExtensions\',\'\');\r\n		if(is_array($fileExtensions)) {\r\n			$fileExtensions = implode(\'|\',$fileExtensions);\r\n		} else {\r\n			$fileExtensions = trim($fileExtensions);\r\n		}\r\n		if(!empty($fileExtensions)) {\r\n			$js = \'\';\r\n			$js .= \'} else if (!hasExt(form.\' .$this->getInputFieldName(1) . \'.value,\\\\\'\' . $fileExtensions . \'\\\\\')) {\'; \r\n			$js .= \'alert(\"\' . $this->getCaption() . \': Please select files with these extension(s) - \' . str_replace(\'|\',\', \',$fileExtensions) . \'.\");\';\r\n			return $js;\r\n		} else {\r\n			return null;\r\n		}\r\n	}\r\n}"  WHERE field_type = "mfile" LIMIT 1');
		$database->execute();

		// Update mfile version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "2" WHERE ft_id = "48" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated mfile field type to version 2.' );

		// Update videoplayer class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_videoplayer extends mFieldType_file {\r\n\r\n	function getOutput() {\r\n		$html =\'\';\r\n		$filename = $this->getValue();\r\n		$format = $this->getParam(\'format\');\r\n		$id = $format.$filename;\r\n		$width = $this->getParam(\'width\');\r\n		$height = $this->getParam(\'height\');\r\n		$autoplay = $this->getParam(\'autoplay\',false);\r\n		if($autoplay) {\r\n			$autoplay = \'true\';\r\n		} else {\r\n			$autoplay = \'false\';\r\n		}\r\n		switch($format) {\r\n			case \'mov\':\r\n				$html .= \'<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"\' . $width . \'\" height=\"\' . $height. \'\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0\" align=\"middle\">\';\r\n				$html .= \'<param name=\"src\" value=\"\' . $this->getDataAttachmentURL() . \'\" />\';\r\n				$html .= \'<embed src=\"\' . $this->getDataAttachmentURL() . \'\" type=\"video/quicktime\" width=\"\' . $width . \'\" height=\"\' . $height . \'\" pluginspage=\"http://www.apple.com/quicktime/download/\" align=\"middle\" autoplay=\"\' . $autoplay . \'\" />\';\r\n				$html .= \'</object>\';\r\n				break;\r\n			case \'divx\':\r\n				$html .= \'\';\r\n				$html .= \'<object classid=\"clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616\" width=\"\' . $width . \'\" height=\"\' . $height . \'\" codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\">\';\r\n				$html .= \'<param name=\"src\" value=\"\' . $this->getDataAttachmentURL() . \'\" />\';\r\n				$html .= \'<param name=\"autoPlay\" value=\"\' . $autoplay . \'\" />\';\r\n				$html .= \'<embed src=\"\' . $this->getDataAttachmentURL() . \'\" type=\"video/divx\" width=\"\' . $width . \'\" height=\"\' . $height . \'\" autoPlay=\"\' . $autoplay . \'\" pluginspage=\"http://go.divx.com/plugin/download/\" />\';\r\n				$html .= \'</object>\';\r\n				break;\r\n			case \'windowsmedia\':\r\n				$html .= \'<object classid=\"CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6\" id=\"\' . $id . \'\" width=\"\' . $width . \'\" height=\"\' . $height . \'\" type=\"application/x-oleobject\">\';\r\n				$html .= \'<param name=\"URL\" value=\"\' . $this->getDataAttachmentURL() . \'\" />\';\r\n				$html .= \'<param name=\"wmode\" value=\"opaque\" />\';\r\n				$html .= \'<param name=\"ShowControls\" value=\"1\" />\';\r\n				$html .= \'<param name=\"autoStart\" value=\"\' . (($autoplay==\'true\')?\'1\':\'0\') . \'\" />\';\r\n				$html .= \'<embed src=\"\' . $this->getDataAttachmentURL() . \'\" type=\"application/x-mplayer2\" width=\"\' . $width . \'\" height=\"\' . $height . \'\" wmode=\"opaque\" border=\"0\" autoStart=\"\' . (($autoplay == \'true\')?\'1\':\'0\') . \'\" />\';\r\n				$html .= \'</object>\';\r\n				break;\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "videoplayer" LIMIT 1');
		$database->execute();

		// Update videoplayer version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "45" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated videoplayer field type to version 3.' );
		
		updateVersion(2,0,8);
		$this->updated = true;
		return true;
	}
}
?>