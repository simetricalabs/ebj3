<?php
/**
 * @version	$Id: videoplayer.php 1411 2012-04-04 09:58:11Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_videoplayer extends mFieldType_file {

	function getOutput()
	{
		$document	=& JFactory::getDocument();
		
		$width		= $this->getParam('width', '640');
		$height		= $this->getParam('height', '264');
		$autoplay	= $this->getParam('autoplay',false);

		$document->addCustomTag("<link href=\"" . $this->getFieldTypeAttachmentURL('video-js.min.css') . "\" rel=\"stylesheet\" type=\"text/css\"/>");
		$document->addCustomTag("<script src=\"" . $this->getFieldTypeAttachmentURL('video.min.js') . "\" language=\"JavaScript\"/></script>");
		
		$html		='';
		$html .= '<video';
		$html .= ' id="videoplayer'.$this->getId().'"';
		$html .= ' class="video-js vjs-default-skin"';
		$html .= ' controls';
		$html .= ($autoplay?' autoplay':'');
		$html .= ' preload="auto"';
		$html .= ' width="'.$width.'"';
		$html .= ' height="'.$height.'"';
		$html .= ' >';
		$html .= '<source src="'.$this->getDataAttachmentURL().'" type="video/mp4" />';
		$html .= '</video>';

		return $html;
	}
	
	function getJSValidationFunction()
	{
		return 'function(){return(hasExt(arguments[0].value,\'mov|mp4|flv|h264|mpg|mpeg4|3gpp\'))}';
	}
	
	function getJSValidationMessage()
	{
		return JText::_( 'FLD_VIDEOPLAYER_PLEASE_SELECT_A_VIDEO_FILE_WITH_ONE_OF_THESE_EXTENSIONS' );
	}
}
?>