<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_image extends mFieldType_file
{
	var $mimeTypes = array('image/png','image/gif','image/jpeg','image/pjpeg');
	
	function validateValue($value)
	{
		global $mtconf;

		// Check file's MIME type
		if( !in_array($value['type'],$this->mimeTypes) )
		{
			$this->setError(JText::_( 'FLD_IMAGE_ERROR_1' ));
			return false;
		}

		// Attempt to check the image size to verify that this is 
		// an image file.
		$imageSize = getimagesize($value['tmp_name']);
		if( $imageSize === false )
		{
			$this->setError(JText::_( 'FLD_IMAGE_ERROR_1' ));
			return false;
		}

		// Check that the uploaded image has equal or more height
		// & width.
		$params['size'] = intval(trim($this->getParam('size',0)));
		if($params['size'] <= 0)
		{
			$size = $mtconf->get('resize_small_listing_size');
		}
		else
		{
			$size = $params['size'];
		}
		
		if( $imageSize[0] < $size || $imageSize[1] < $size )
		{
			$this->setError(JText::sprintf( 'FLD_IMAGE_ERROR_2', $size ));
			return false;
		}
		
		// Checks if the file is within the allowed file size limit.
		$params['maxFileSize'] = intval(trim($this->getParam('maxFileSize', 3145728)));
		if( $value['size'] > $params['maxFileSize'] )
		{
			// File is larger than the specified limit.
			// Remove the file and return empty string.
			$this->setError(JText::sprintf( 'FLD_IMAGE_ERROR_3', $params['maxFileSize'] ));
			return false;
		}

		return true;		
	}
	
	function parseValue($value)
	{
		global $mtconf;

		$params['size'] = intval(trim($this->getParam('size')));
	
		if($params['size'] <= 0)
		{
			$size = $mtconf->get('resize_small_listing_size');
		}
		else
		{
			$size = $params['size'];
		}
		
		$mtImage = new mtImage();
		$mtImage->setMethod( $mtconf->get('resize_method') );
		$mtImage->setQuality( $mtconf->get('resize_quality') );
		$mtImage->setSize( $size );
		$mtImage->setTmpFile( $value['tmp_name'] );
		$mtImage->setType( $value['type'] );
		$mtImage->setName( $value['name'] );
		$mtImage->setSquare(false);
		$mtImage->resize();
		$value['data'] = $mtImage->getImageData();
		$value['size'] = strlen($value['data']);
		
		return $value;
	}
	
	function getJSValidationFunction()
	{
		return 'function(){return(hasExt(arguments[0].value,\'gif|png|jpg|jpeg\'))}';
	}
	
	function getJSValidationMessage()
	{
		return JText::_( 'FLD_IMAGE_PLEASE_SELECT_AN_IMAGE_WITH_ONE_OF_THESE_EXTENSIONS' );
	}

	function getOutput($view=1)
	{
		$html = '';
		$html .= '<img src="' . $this->getDataAttachmentURL() . '" />';
		return $html;
	}
	
	function getInputHTML()
	{
		$html = '';
		if( $this->attachment > 0 )
		{
			$html .= '<label for="' . $this->getKeepFileName() . '" class="checkbox">';
			$html .= $this->getKeepFileCheckboxHTML($this->attachment);
			$html .= '<img src="' . $this->getDataAttachmentURL() . '" hspace="5" vspace="0" />';
			$html .= '<input'.($this->isRequired() ? ' required':'').' type="file" id="' . $this->getInputFieldID() . '" name="' . $this->getInputFieldName(1) . '" />';
			$html .= '</label>';
		} else {
			$html .= '<input'.($this->isRequired() ? ' required':'').' type="file" id="' . $this->getInputFieldID() . '" name="' . $this->getInputFieldName(1) . '" />';
		}
		
		return $html;
	}
	
}
?>