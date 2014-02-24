<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_mFile extends mFieldType_file
{
	function validateValue($value)
	{
		$params['maxSize'] = intval(trim($this->getParam('maxSize')));
		$fileExtensions = $this->getParam('fileExtensions','gif|png|jpg|jpeg|pdf|zip');

		$fileExtensions = explode('|',$fileExtensions);
		$path_parts = pathinfo($value['name']);

		if( !in_array($path_parts['extension'],$fileExtensions) )
		{
			$this->setError(JText::sprintf( 'FLD_MFILE_ERROR_1', implode(', ',$fileExtensions) ));
			return false;
		}

		if($params['maxSize'] == 0)
		{
			return true;
		}
		else
		{
			// Checks if the file exceed the file size limit
			if( $value['size'] > $params['maxSize'] )
			{
				// File is larger than the specified limit.
				// Remove the file and return empty string.
				$this->setError(JText::sprintf( 'FLD_MFILE_ERROR_2', $params['maxSize'] ));
				return false;
			}
			else
			{
				// File is within specified limit.
				return true;
			}
		}
		
		return true;
		
	}

	function parseValue($value)
	{
		$params['maxSize'] = intval(trim($this->getParam('maxSize')));
		$fileExtensions = $this->getParam('fileExtensions','');

		$fileExtensions = explode('|',$fileExtensions);
		$path_parts = pathinfo($value['name']);
		
		if( !in_array($path_parts['extension'],$fileExtensions) )
		{
			return '';
		}

		if($params['maxSize'] == 0)
		{
			return $value;
		}
		else
		{
			// Checks if the file exceed the file size limit
			if( $value['size'] > $params['maxSize'] )
			{
				// File is larger than the specified limit.
				// Remove the file and return empty string.
				return '';
			}
			else
			{
				// File is within specified limit.
				return $value;
			}
		}
		
		return $value;
	}
	
	function getOutput($view=1)
	{
		global $mtconf;
	
		$html = '';
		$showCounter 	= $this->getParam('showCounter',1);
		$useImage	= $this->getParam('useImage','');
		$showFilename	= $this->getParam('showFilename',1);
		$showText	= $this->getParam('showText', JText::_('FLD_MFILE_DOWNLOAD'));
		$linkClassSuffix= $this->getParam('linkClassSuffix', 'btn btn-small');
		
		if(!empty($this->value))
		{
			$html .= '<a';
			$html .= ' class="'.$linkClassSuffix.'"';
			$html .= ' href="' . $this->getDataAttachmentURL() . '"';
			$html .= ' target="_blank"';
			$html .= '>';

			if( !empty($useImage) )
			{
				$live_site = $mtconf->getjconf('live_site');
				$html .= '<img src="' . trim(str_replace('{live_site}',$live_site,$useImage)) . '"';
				$html .= ' alt=""';
				$html .= ' /> ';
			} 

			$text = '';
			
			if( !empty($showText) )
			{
				$text .= $showText . ' ';
			}
			
			if( $showFilename == 1 )
			{
				
				$text .= $this->getValue();
			}
			
			if( empty($text) )
			{
				$text .= JText::_('FLD_MFILE_DOWNLOAD');
			}
			
			$html .= $text;
			$html .= '</a>';
		}

		$append_html = array();
		if( $showCounter ) {
			$append_html[] = JText::sprintf('FLD_FILE_NUMBER_OF_VIEWS', $this->counter);
		}

		if( !empty($append_html) ) {
			$html .= ' (' . implode(', ',$append_html) . ')';
		}
		return $html;
	}
	
	function getJSValidationFunction()
	{
		$fileExtensions = $this->getParam('fileExtensions','');
		
		if(is_array($fileExtensions))
		{
			$fileExtensions = implode('|',$fileExtensions);
		}
		else
		{
			$fileExtensions = trim($fileExtensions);
		}
		return 'function(){return hasExt(arguments[0].value,\'' . $fileExtensions . '\')}';
	}

	function getJSValidationMessage()
	{
		$fileExtensions = $this->getParam('fileExtensions','');
		
		if(is_array($fileExtensions))
		{
			$fileExtensions = implode('|',$fileExtensions);
		}
		else
		{
			$fileExtensions = trim($fileExtensions);
		}
		return JText::sprintf('FLD_MFILE_ERROR_3', $this->getCaption(), str_replace('|',', ',$fileExtensions));
	}
}
?>