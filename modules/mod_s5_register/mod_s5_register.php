<?php
/*** @title		Shape 5 Register Module* @version		3.0* @package		Joomla* @website		http://www.shape5.com* @copyright	Copyright (C) 2011 Shape 5 LLC. All rights reserved.* @license		GNU/GPL, see LICENSE.php* Joomla! is free software. This version may have been modified pursuant* to the GNU General Public License, and as distributed it includes or* is derivative of works licensed under the GNU General Public License or* other free or open source software licenses.* See COPYRIGHT.php for copyright notices and details.*/// no direct access
error_reporting(0);
defined('_JEXEC') or die('Restricted access');$params->def('class_sfx','');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
jimport( 'joomla.application.component.view');
$modules_params=$params;
$modules_params->get('captcha');
$form   =& JForm::getInstance('registration',JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'models'.DS.'forms'.DS.'registration.xml');
$captcha = ''; 
foreach ($form->getFieldsets() as $fieldset){
	$fields = $form->getFieldset($fieldset->name);
	foreach ($fields as $field){
		if($field->type == 'captcha' ){
			$captcha = $field->input;	
		}
	}	
}
require(JModuleHelper::getLayoutPath('mod_s5_register'));?>
