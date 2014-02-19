<?php
/**
 * @version		$Id: 2_0_9.php 1972 2013-07-16 09:24:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_0_9 extends mUpgrade {
	function upgrade() {
		$database =& JFactory::getDBO();
		
		// Update audioplayer class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_audioplayer extends mFieldType_file {\r\n	function getJSValidation() {\r\n\r\n		$js = \'\';\r\n		$js .= \'} else if (!hasExt(form.\' . $this->getName() . \'.value,\\\\\'mp3\\\\\')) {\'; \r\n		$js .= \'alert(\"\' . addslashes($this->getCaption()) . \': Please select a mp3 file.\");\';\r\n		return $js;\r\n	}\r\n	function getOutput() {\r\n		$id = $this->getId();\r\n		$params[\'text\'] = $this->getParam(\'textColour\');\r\n		$params[\'displayfilename\'] = $this->getParam(\'displayfilename\',1);\r\n		$params[\'slider\'] = $this->getParam(\'sliderColour\');\r\n		$params[\'loader\'] = $this->getParam(\'loaderColour\');\r\n		$params[\'track\'] = $this->getParam(\'trackColour\');\r\n		$params[\'border\'] = $this->getParam(\'borderColour\');\r\n		$params[\'bg\'] = $this->getParam(\'backgroundColour\');\r\n		$params[\'leftbg\'] = $this->getParam(\'leftBackgrounColour\');\r\n		$params[\'rightbg\'] = $this->getParam(\'rightBackgrounColour\');\r\n		$params[\'rightbghover\'] = $this->getParam(\'rightBackgroundHoverColour\');\r\n		$params[\'lefticon\'] = $this->getParam(\'leftIconColour\');\r\n		$params[\'righticon\'] = $this->getParam(\'rightIconColour\');\r\n		$params[\'righticonhover\'] = $this->getParam(\'rightIconHoverColour\');\r\n		\r\n		$html = \'\';\r\n		$html .= \'<script language=\"JavaScript\" src=\"\' . $this->getFieldTypeAttachmentURL(\'audio-player.js\'). \'\"></script>\';\r\n		$html .= \"\\\\n\" . \'<object type=\"application/x-shockwave-flash\" data=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\'). \'\" id=\"audioplayer\' . $id . \'\" height=\"24\" width=\"290\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"movie\" value=\"\' . $this->getFieldTypeAttachmentURL(\'player.swf\') . \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"FlashVars\" value=\"\';\r\n		$html .= \'playerID=\' . $id;\r\n		$html .= \'&amp;soundFile=\' . urlencode($this->getDataAttachmentURL());\r\n		foreach( $params AS $key => $value ) {\r\n			if(!empty($value)) {\r\n				$html .= \'&amp;\' . $key . \'=0x\' . $value;\r\n			}\r\n		}\r\n		$html .= \'\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"quality\" value=\"high\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"menu\" value=\"false\">\';\r\n		$html .= \"\\\\n\" . \'<param name=\"wmode\" value=\"transparent\">\';\r\n		$html .= \"\\\\n\" . \'</object>\';\r\n		if($params[\'displayfilename\']) {\r\n			$html .= \"\\\\n<br />\";\r\n			$html .= \"\\\\n\" . \'<a href=\"\' . $this->getDataAttachmentURL() . \'\" target=\"_blank\">\';\r\n			$html .= $this->getValue();\r\n			$html .= \'</a>\';\r\n		}\r\n		return $html;\r\n	}\r\n}"  WHERE field_type = "audioplayer" LIMIT 1');
		$database->execute();

		// Update audioplayer version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "24" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated audioplayer field type to version 3.' );

		// Update image class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_image extends mFieldType_file {\r\n	function parseValue($value) {\r\n		global $mtconf;\r\n		$params[\'size\'] = intval(trim($this->getParam(\'size\')));\r\n		if($params[\'size\'] <= 0) {\r\n			$size = $mtconf->get(\'resize_listing_size\');\r\n		} else {\r\n			$size = intval($params[\'size\']);\r\n		}\r\n		$mtImage = new mtImage();\r\n		$mtImage->setMethod( $mtconf->get(\'resize_method\') );\r\n		$mtImage->setQuality( $mtconf->get(\'resize_quality\') );\r\n		$mtImage->setSize( $size );\r\n		$mtImage->setTmpFile( $value[\'tmp_name\'] );\r\n		$mtImage->setType( $value[\'type\'] );\r\n		$mtImage->setName( $value[\'name\'] );\r\n		$mtImage->setSquare(false);\r\n		$mtImage->resize();\r\n		$value[\'data\'] = $mtImage->getImageData();\r\n		$value[\'size\'] = strlen($value[\'data\']);\r\n		\r\n		return $value;\r\n	}\r\n	function getJSValidation() {\r\n		$js = \'\';\r\n		$js .= \'} else if (!hasExt(form.\' .$this->getInputFieldName(1) . \'.value,\\\\\'gif|png|jpg|jpeg\\\\\')) {\'; \r\n		$js .= \'alert(\"\' . addslashes($this->getCaption()) . \': Please select an image with one of these extensions - gif,png,jpg,jpeg.\");\';\r\n		return $js;\r\n	}\r\n	function getOutput() {\r\n		$html = \'\';\r\n		$html .= \'<img src=\"\' . $this->getDataAttachmentURL() . \'\" />\';\r\n		return $html;\r\n	}\r\n	function getInputHTML() {\r\n		$html = \'\';\r\n		if( $this->attachment > 0 ) {\r\n			$html .= $this->getKeepFileCheckboxHTML($this->attachment);\r\n			$html .= \'<label for=\"\' . $this->getKeepFileName() . \'\"><img src=\"\' . $this->getDataAttachmentURL() . \'\" hspace=\"5\" vspace=\"0\" /></label>\';\r\n			$html .= \'</br >\';\r\n		}\r\n		$html .= \'<input class=\"inputbox\" type=\"file\" name=\"\' . $this->getInputFieldName(1) . \'\" />\';\r\n		return $html;\r\n	}\r\n	\r\n}"  WHERE field_type = "image" LIMIT 1');
		$database->execute();

		// Update image version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "25" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated image field type to version 3.' );

		// Update mfile class code
		$database->setQuery('UPDATE #__mt_fieldtypes SET ft_class = "class mFieldType_mFile extends mFieldType_file {\r\n	function getJSValidation() {\r\n		$fileExtensions = $this->getParam(\'fileExtensions\',\'\');\r\n		if(is_array($fileExtensions)) {\r\n			$fileExtensions = implode(\'|\',$fileExtensions);\r\n		} else {\r\n			$fileExtensions = trim($fileExtensions);\r\n		}\r\n		if(!empty($fileExtensions)) {\r\n			$js = \'\';\r\n			$js .= \'} else if (!hasExt(form.\' .$this->getInputFieldName(1) . \'.value,\\\\\'\' . $fileExtensions . \'\\\\\')) {\'; \r\n			$js .= \'alert(\"\' . addslashes($this->getCaption()) . \': Please select files with these extension(s) - \' . str_replace(\'|\',\', \',$fileExtensions) . \'.\");\';\r\n			return $js;\r\n		} else {\r\n			return null;\r\n		}\r\n	}\r\n}"  WHERE field_type = "mfile" LIMIT 1');
		$database->execute();

		// Update mfile version number
		$database->setQuery('UPDATE #__mt_fieldtypes_info SET ft_version = "3" WHERE ft_id = "48" LIMIT 1');
		$database->execute();
		$this->printStatus( 'Updated mfile field type to version 3.' );
		
		// Update jQuery path to point to new version 1.2.6
		$database->setQuery('UPDATE #__mt_config SET `value` = \'/components/com_mtree/js/jquery-1.2.6.min.js\',
		`default` = \'/components/com_mtree/js/jquery-1.2.6.min.js\' WHERE varname = \'relative_path_to_js_library\' LIMIT 1');
		$database->execute();
		$this->printStatus( 'Update configuration to use new jQuery path.' );
		
		updateVersion(2,0,9);
		$this->updated = true;
		return true;
	}
}
?>