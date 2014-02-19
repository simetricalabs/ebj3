<?php
s5_mootools_call();
//Calls jquery javascript
if($version->RELEASE >= '3.0'){
JHtml::_('jquery.framework');
}
$doc=JFactory::getDocument();
if($version->RELEASE <= '2.5'){
$doc->addScript($s5_directory_path.'/js/jquery/jquery.min.js');
} ?>
<script type="text/javascript">//<![CDATA[
if(jQuery.easing.easeOutExpo==undefined){
document.write('<script src="<?php echo$s5_directory_path; ?>/js/jquery/jquery-ui.min.js"><\/script>');
}
//]]></script>
<?php
$doc->addScript($s5_directory_path.'/js/jquery/jquery-noconflict.js');
?>

<?php if($version->RELEASE >= '3.0'){ ?>
	<link href="<?php echo $s5_directory_path ?>/css/bootstrap/bootstrap-default.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $s5_directory_path ?>/css/bootstrap/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
	<?php if ($s5_bootstrap == "dark") { ?>
		<link href="<?php echo $s5_directory_path ?>/css/bootstrap/bootstrap-dark-overrides.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	<?php if($s5_language_direction == "1") { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $s5_directory_path ?>/css/bootstrap/bootstrap-rtl.css" />
	<?php } ?>
<?php } ?>

<?php if($version->RELEASE >= '3.0' && $s5_font_awesome == "enabled"){ ?>
	<link rel="stylesheet" href="<?php echo $s5_directory_path ?>/css/font-awesome/css/font-awesome.min.css">
	<?php if ($browser == "ie7" || $browser == "ie8") { ?>
	<link rel="stylesheet" href="<?php echo $s5_directory_path ?>/css/font-awesome-ie7.min.css">
	<?php } ?>
<?php } ?>

<!-- Css and js addons for vertex features -->	
<?php if(($s5_responsive == "enabled" || $s5_responsive_cookie == "desktop") && $s5_fonts_responsive_mobile_bar != "Arial" && $s5_fonts_responsive_mobile_bar != "Tahoma" && $s5_fonts_responsive_mobile_bar != "Helvetica" && $s5_fonts_responsive_mobile_bar != "Sans-Serif") { ?>
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo $s5_fonts_responsive_mobile_bar_with_style; ?>" />
<?php } ?>
<?php require(dirname(__FILE__)."/../css/s5_vertex_addons.php"); ?>
<?php require(dirname(__FILE__)."/../js/s5_vertex_addons.php"); ?>

<?php if ($s5_show_menu == "show") { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/s5_flex_menu.js"></script>
<link rel="stylesheet" href="<?php echo $s5_directory_path ?>/css/s5_flex_menu.css" type="text/css" />
<?php } ?>
	
<link rel="stylesheet" href="<?php echo $LiveSiteUrl ?>templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $LiveSiteUrl ?>templates/system/css/general.css" type="text/css" />

<link href="<?php echo $s5_directory_path ?>/css/template_default.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $s5_directory_path ?>/css/template.css" rel="stylesheet" type="text/css" />

<?php if($mobile==true){ ?>
<link href="<?php echo $s5_directory_path ?>/css/mobile_device.css" rel="stylesheet" type="text/css" />
<?php } ?>

<link href="<?php echo $s5_directory_path ?>/css/com_content.css" rel="stylesheet" type="text/css" />

<link href="<?php echo $s5_directory_path ?>/css/editor.css" rel="stylesheet" type="text/css" />

<?php if($s5_thirdparty == "enabled") { ?>
<link href="<?php echo $s5_directory_path ?>/css/thirdparty.css" rel="stylesheet" type="text/css" />
<?php } ?>

<?php if($s5_language_direction == "1") { ?>
	<link href="<?php echo $s5_directory_path ?>/css/template_rtl.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $s5_directory_path ?>/css/editor_rtl.css" rel="stylesheet" type="text/css" />
	<?php if($mobile==true){ ?>
	<link href="<?php echo $s5_directory_path ?>/css/mobile_device_rtl.css" rel="stylesheet" type="text/css" />
	<?php } ?>
<?php } ?>

<?php if($s5_fonts != "Arial" && $s5_fonts != "Helvetica" && $s5_fonts != "Sans-Serif" && $s5_fonts != "Tahoma") { ?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo $s5_fonts_with_style;?>" />
<?php } ?>

<?php if ($s5_multibox  == "yes") { ?>
<link href="<?php echo $s5_directory_path ?>/css/multibox/multibox.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $s5_directory_path ?>/css/multibox/ajax.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/overlay.js"></script>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/multibox.js"></script>
<?php } ?>


<link href="<?php echo $s5_directory_path ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<?php if($s5_font_resizer == "yes" && $mobile==false) { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/s5_font_adjuster.js"></script>
<?php } ?>


<?php 
// Disable responsive calls for IE7 and IE8 if it is disabled in the template configuration.
if (($browser == "ie7" || $browser == "ie8") && $s5_responsive_ie == "disabled") {
	$s5_responsive = "disabled";
} ?>

<?php if($s5_responsive == "enabled" || $s5_responsive_cookie == "desktop") { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $s5_directory_path ?>/css/s5_responsive_bars.css" />
	<link href="<?php echo $s5_directory_path ?>/css/s5_responsive_hide_classes.css" rel="stylesheet" type="text/css" />
<?php } ?>

<?php if ($s5_responsive == "enabled") { ?>

	<link rel="stylesheet" type="text/css" href="<?php echo $s5_directory_path ?>/css/s5_responsive.css" />
	<?php if($s5_language_direction == "1") { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $s5_directory_path ?>/css/s5_responsive_rtl.css" />
	<?php } ?>

	<?php 
	// Media query script for IE7 and IE8. Must be called after media query css.
	if ($browser == "ie7" || $browser == "ie8") { ?>
	<script type="text/javascript">
	var s5_max_body_width = 0;
	<?php if ($s5_max_body_width != ""){ ?>
	s5_max_body_width = <?php echo $s5_max_body_width; ?>;
	<?php } ?> 
	var s5_fixed_fluid = "<?php echo $s5_fixed_fluid; ?>";
	var s5_responsive_column_increase = "<?php echo $s5_responsive_column_increase; ?>";
	var s5_responsive_columns_small_tablet = "<?php echo $s5_responsive_columns_small_tablet; ?>";
	var s5_responsive_hide_tablet = "<?php echo @implode(",",$s5_responsive_tablet_hide); ?>";
	var s5_responsive_hide_mobile = "<?php echo @implode(",",$s5_responsive_mobile_hide); ?>";
	var s5_right_width_orig = <?php echo $s5_right_width; ?>;
	var s5_right_inset_width_orig = <?php echo $s5_right_inset_width; ?>;
	var s5_left_width_orig = <?php echo $s5_left_width; ?>;
	var s5_left_inset_width_orig = <?php echo $s5_left_inset_width; ?>;
	</script>
	<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/s5_responsive_ie.js"></script>
	<?php } ?>

<?php } ?>

<link rel="stylesheet" type="text/css" href="<?php echo $s5_directory_path ?>/css/custom.css" />