<?php


session_start();


/*


* File: CaptchaSecurityImages.php


* Author: Simon Jarvis


* Copyright: 2006 Simon Jarvis


* Date: 03/08/06


* Updated: 07/02/07


* Requirements: PHP 4/5 with GD and FreeType libraries


* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php


* 


* This program is free software; you can redistribute it and/or 


* modify it under the terms of the GNU General Public License 


* as published by the Free Software Foundation; either version 2 


* of the License, or (at your option) any later version.


* 


* This program is distributed in the hope that it will be useful, 


* but WITHOUT ANY WARRANTY; without even the implied warranty of 


* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 


* GNU General Public License for more details: 


* http://www.gnu.org/licenses/gpl.html


*


*/





define( '_JEXEC', 1 );


if(!defined('DS')){


 define('DS',DIRECTORY_SEPARATOR);


}


define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../..' ));


require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );


require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );


JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;


$mainframe =& JFactory::getApplication('site');


$mainframe->initialise();


JPluginHelper::importPlugin('system');


JDEBUG ? $_PROFILER->mark('afterInitialise') : null;


$mainframe->triggerEvent('onAfterInitialise');


class CaptchaSecurityImages {


	var $font = 'monofont.ttf';


	function generateCode($characters) {


		/* list all possible characters, similar looking characters and vowels have been removed */


		$possible = '23456789bcdfghjkmnpqrstvwxyz';


		$code = '';


		$i = 0;


		while ($i < $characters) { 


			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);


			$i++;


		}


		return $code;


	}


	function CaptchaSecurityImages($width='90',$height='30',$characters='6') {

		if($_SESSION['security_code']!='')
			 $code = $_SESSION['security_code'];
		else 
			$code = $this->generateCode($characters);

        
		/* font size will be 75% of the image height */


		$font_size = $height * 0.75;


		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');


		/* set the colours */


		$background_color = imagecolorallocate($image, 255, 255, 255);


		$text_color = imagecolorallocate($image, 38, 38, 38);


		$noise_color = imagecolorallocate($image, 198, 198, 198);


		/* generate random dots in background */


		for( $i=0; $i<($width*$height)/3; $i++ ) {


			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);


		}


		/* generate random lines in background */


		for( $i=0; $i<($width*$height)/150; $i++ ) {


			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);


		}


		/* create textbox and add text */


		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');


		$x = ($width - $textbox[4])/2;


		$y = ($height - $textbox[5])/2;


		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');


		/* output captcha image to browser */


		header('Content-Type: image/jpeg');


		imagejpeg($image);


		imagedestroy($image);


		$_SESSION['security_code'] = $code;


		$app = &JFactory::getApplication();


		$app->setUserState('security_code',$code);


	}


}


$width = isset($_GET['width']) ? $_GET['width'] : '90';


$height = isset($_GET['height']) ? $_GET['height'] : '30';


$characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '6';


$captcha = new CaptchaSecurityImages($width,$height,$characters);


?>