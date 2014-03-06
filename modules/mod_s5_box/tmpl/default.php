<?php
/**
* @title		Shape 5 Box Module
* @version		1.0
* @package		Joomla
* @website		http://www.shape5.com
* @copyright	Copyright (C) 2009 Shape 5 LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$mainframe =& JFactory::getDocument();
$url = JURI::root().'modules/mod_s5_box/';

$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
$is_ieany = "no";
if(preg_match("/msie/", $br)) {
$is_ieany = "yes";
} 
					$document = &JFactory::getDocument();  

					$mainframe->addCustomTag('<style type="text/css">.s5boxhidden{display:none;} </style>');
					$mainframe->addCustomTag('<script language="javascript" type="text/javascript" >var s5_boxeffect = "'.$s5_boxeffect.'";</script>');
					if ((JRequest::getVar('option') == 'com_jomsocial') || (JRequest::getVar('option') == 'com_community') ) {  } else {
					$mainframe->addCustomTag('<script src="'.$url.'js/jquery.min.js" type="text/javascript"></script>');}
					$mainframe->addCustomTag('<script src="'.$url.'js/jquery.no.conflict.js" type="text/javascript"></script>');
					$mainframe->addCustomTag('<script src="'.$url.'js/jquery.colorbox.js" type="text/javascript"></script>');
					$mainframe->addCustomTag('<link rel="stylesheet" href="'.$url.'css/colorbox.css" type="text/css" />');
					if ($is_ieany == "yes" ) {
					$mainframe->addCustomTag('<link rel="stylesheet" href="'.$url.'css/colorbox-ie.css" type="text/css" />');
					}
					$mainframe->addCustomTag('<script type="text/javascript">jQuery(document).ready(function(){ jQuery(".s5box_register").colorbox({width:"25%", inline:true, href:"#s5box_register"});jQuery(".s5box_login").colorbox({width:"25%", inline:true, href:"#s5box_login"});jQuery(".s5box_one").colorbox({width:"'.$s5boxwidth1.'%", inline:true, href:"#s5box_one"});jQuery(".s5box_two").colorbox({width:"'.$s5boxwidth2.'%", inline:true, href:"#s5box_two"});jQuery(".s5box_three").colorbox({width:"'.$s5boxwidth3.'%", inline:true, href:"#s5box_three"});jQuery(".s5box_four").colorbox({width:"'.$s5boxwidth4.'%", inline:true, href:"#s5box_four"});jQuery(".s5box_five").colorbox({width:"'.$s5boxwidth5.'%", inline:true, href:"#s5box_five"});jQuery(".s5box_six").colorbox({width:"'.$s5boxwidth6.'%", inline:true, href:"#s5box_six"});jQuery(".s5box_seven").colorbox({width:"'.$s5boxwidth7.'%", inline:true, href:"#s5box_seven"});jQuery(".s5box_eight").colorbox({width:"'.$s5boxwidth8.'%", inline:true, href:"#s5box_eight"});jQuery(".s5box_nine").colorbox({width:"'.$s5boxwidth9.'%", inline:true, href:"#s5box_nine"});jQuery(".s5box_ten").colorbox({width:"'.$s5boxwidth10.'%", inline:true, href:"#s5box_ten"});});</script>');

?>

<?php if (JModuleHelper::getModules('login')){?><div class="s5boxhidden"><div id="s5box_login">
<?php 
	$s5login_modules = &JModuleHelper::getModules( 'login' );
	foreach ($s5login_modules as $s5login) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5login, $_options );	} 
  ?></div></div><?php } ?>
  
<?php if (JModuleHelper::getModules('register')){?><div class="s5boxhidden"><div id="s5box_register">
<?php 
	$s5register_modules = &JModuleHelper::getModules( 'register' );
	foreach ($s5register_modules as $s5register) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5register, $_options );	} 
  ?></div></div><?php } ?>

<?php if (JModuleHelper::getModules('s5_box1')){?><div class="s5boxhidden"><div id="s5box_one">
<?php 
	$s5box1_modules = &JModuleHelper::getModules( 's5_box1' );
	foreach ($s5box1_modules as $s5box1) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box1, $_options );	} 
  ?></div></div><?php } ?>
	
<?php if (JModuleHelper::getModules('s5_box2')){?><div class="s5boxhidden"><div id="s5box_two">
<?php 
	$s5box2_modules = &JModuleHelper::getModules( 's5_box2' );
	foreach ($s5box2_modules as $s5box2) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box2, $_options );	} 
 ?></div></div><?php } ?>
	
<?php if (JModuleHelper::getModules('s5_box3')){?><div class="s5boxhidden"><div id="s5box_three">
<?php 
	$s5box3_modules = &JModuleHelper::getModules( 's5_box3' );
	foreach ($s5box3_modules as $s5box3) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box3, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box4')){?><div class="s5boxhidden"><div id="s5box_four">
<?php 
$s5box4_modules = &JModuleHelper::getModules( 's5_box4' );
	foreach ($s5box4_modules as $s5box4) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box4, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box5')){?><div class="s5boxhidden"><div id="s5box_five">
<?php 
	$s5box5_modules = &JModuleHelper::getModules( 's5_box5' );
	foreach ($s5box5_modules as $s5box5) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box5, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box6')){?><div class="s5boxhidden"><div id="s5box_six">
<?php 
	$s5box6_modules = &JModuleHelper::getModules( 's5_box6' );
	foreach ($s5box6_modules as $s5box6) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box6, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box7')){?><div class="s5boxhidden"><div id="s5box_seven">
<?php 
	$s5box7_modules = &JModuleHelper::getModules( 's5_box7' );
	foreach ($s5box7_modules as $s5box7) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box7, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box8')){?><div class="s5boxhidden"><div id="s5box_eight">
<?php
	$s5box8_modules = &JModuleHelper::getModules( 's5_box8' );
	foreach ($s5box8_modules as $s5box8) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box8, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box9')){?><div class="s5boxhidden"><div id="s5box_nine">
<?php
	$s5box9_modules = &JModuleHelper::getModules( 's5_box9' );
	foreach ($s5box9_modules as $s5box9) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box9, $_options );	} 
?></div></div><?php } ?>
<?php if (JModuleHelper::getModules('s5_box10')){?><div class="s5boxhidden"><div id="s5box_ten">
<?php
	$s5box10_modules = &JModuleHelper::getModules( 's5_box10' );
	foreach ($s5box10_modules as $s5box10) {
	$_options = array( 'style' => 'rounded' );
	echo JModuleHelper::renderModule( $s5box10, $_options );	} 
?></div></div><?php } ?>
