<style type="text/css"> 

<?php if ($s5_module_ft_size != "default") { ?>
.module_round_box_outer, #s5_above_body, #s5_below_body {
font-size:<?php echo $s5_module_ft_size; ?>em !important;
}
<?php } ?>

<?php if ($s5_component_ft_size != "default") { ?>
#s5_component_wrap {
font-size:<?php echo $s5_component_ft_size; ?>em !important;
}
<?php } ?>

<?php if ($s5_images_width != "") { ?>
.s5_module_box_1 img, .item-page img, .blog-featured img, .blog img {
width:<?php echo $s5_images_width; ?>% !important;
-webkit-box-sizing: border-box !important; /* Safari/Chrome, other WebKit */
-moz-box-sizing: border-box !important;    /* Firefox, other Gecko */
box-sizing: border-box !important;         /* Opera/IE 8+ */
}
ul.actions img {
width:auto !important;
}
<?php } ?>

/* MAX IMAGE WIDTH */

img {
<?php if ($s5_auto_height == "yes") { ?>
height:auto !important;
<?php } ?>
<?php if ($s5_max_width == "yes") { ?>
max-width:100% !important;
-webkit-box-sizing: border-box !important; /* Safari/Chrome, other WebKit */
-moz-box-sizing: border-box !important;    /* Firefox, other Gecko */
box-sizing: border-box !important;         /* Opera/IE 8+ */
<?php } ?>
}

.full_width {
width:100% !important;
-webkit-box-sizing: border-box !important; /* Safari/Chrome, other WebKit */
-moz-box-sizing: border-box !important;    /* Firefox, other Gecko */
box-sizing: border-box !important;         /* Opera/IE 8+ */
}

<?php if ($browser == "ie7") { ?>
img.ie7, img.boxed, img.boxed_black, img.padded {
max-width:95% !important;
}
<?php } ?>

<?php if($s5_responsive_cookie == "desktop") { ?>
#s5_menu_wrap {
display:block !important;
}
<?php } ?>

<?php if ($browser == "ie7" || $browser == "ie8") { ?>
#s5_drop_down_container {
overflow:visible !important;
}
<?php } ?>

.S5_submenu_itemTablet{
background:none !important;
}

<?php if ($s5_columns_fixed_fluid == "%") { ?>
#s5_right_wrap, #s5_left_wrap, #s5_right_inset_wrap, #s5_left_inset_wrap, #s5_right_top_wrap, #s5_left_top_wrap, #s5_right_bottom_wrap, #s5_left_bottom_wrap {
width:100% !important;
}
<?php if ($s5_pos_right_inset == "published" && $s5_pos_right_inset == "published") { ?>
#s5_right_wrap {
width:<?php echo ($s5_right_width/($s5_right_width + $s5_right_inset_width))*100; ?>% !important;
}
#s5_right_inset_wrap {
width:<?php echo ($s5_right_inset_width/($s5_right_width + $s5_right_inset_width))*100; ?>% !important;
}
<?php } ?>
<?php if ($s5_pos_left_inset == "published" && $s5_pos_left_inset == "published") { ?>
#s5_left_wrap {
width:<?php echo ($s5_left_width/($s5_left_width + $s5_left_inset_width))*100; ?>% !important;
}
#s5_left_inset_wrap {
width:<?php echo ($s5_left_inset_width/($s5_left_width + $s5_left_inset_width))*100; ?>% !important;
}
<?php } ?>
#s5_right_column_wrap {
width:<?php echo $s5_right_column_width/2; ?>% !important;
margin-left:-<?php echo ($s5_right_column_width/2) + ($s5_left_column_width/2); ?>% !important;
}
#s5_left_column_wrap {
width:<?php echo $s5_left_column_width/2; ?>% !important;
}
#s5_center_column_wrap_inner {
margin-right:<?php echo $s5_center_column_margin_right ?>% !important;
margin-left:<?php echo $s5_center_column_margin_left ?>% !important;
}
<?php } ?>

<?php if ($s5_responsive == "enabled" && $browser != "ie7") { ?>
#s5_responsive_modile_drop_down_wrap input {
width:96% !important;
}
#s5_responsive_mobile_drop_down_search input {
width:100% !important;
}
<?php } ?>

<?php if ($s5_responsive == "enabled" && $browser == "ie7") { ?>
#s5_responsive_modile_drop_down_wrap input {
width:90% !important;
}
#s5_responsive_mobile_drop_down_search input {
width:93% !important;
}
<?php } ?>

<?php if ($s5_responsive_cookie == "desktop") { ?>
	body {
	height:100% !important;
	position:relative !important;
	padding-bottom:48px !important;
	}
<?php } ?>

<?php if ($s5_responsive == "enabled" && ($s5_responsive_cookie  == "not_set" || $s5_responsive_cookie  == "mobile" || $s5_scrolltotop  == "yes")) { ?>
@media screen and (max-width: 750px){
	body {
	height:100% !important;
	position:relative !important;
	padding-bottom:48px !important;
	}
}
<?php } ?>

<?php if ($s5_menu_onclick == "enabled" && $s5_responsive == "enabled") { ?>
@media screen and (max-width: 970px){
	#subMenusContainer .S5_subtext {
	width:85%;
	}
}
<?php } ?>

<?php if ($s5_responsive == "enabled" || $s5_responsive_cookie == "desktop") { ?>

	#s5_responsive_mobile_bottom_bar, #s5_responsive_mobile_top_bar {
	background:#<?php echo $s5_responsive_mobile_bar_stop; ?>;
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>-moz-linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%, #<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* FF3.6+ */
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>-webkit-gradient(linear, left top, left bottom, color-stop(0%,#<?php echo $s5_responsive_mobile_bar_start; ?>), color-stop(100%,#<?php echo $s5_responsive_mobile_bar_stop; ?>)); /* Chrome,Safari4+ */
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>-webkit-linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%,#<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* Chrome10+,Safari5.1+ */
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>-o-linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%,#<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* Opera11.10+ */
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>-ms-linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%,#<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* IE10+ */
	background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%,#<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* W3C */
	<?php if ($browser == "ie9") { ?>
	filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#<?php echo $s5_responsive_mobile_bar_start; ?>', EndColorStr='#<?php echo $s5_responsive_mobile_bar_stop; ?>'); /* IE9 */
	<?php } ?>
	<?php if ($browser == "ie7" || $browser == "ie8") { ?>
	-pie-background: <?php if ($s5_responsive_mobile_bar_static == "enabled") { ?>url(<?php echo $s5_directory_path ?>/images/vertex/responsive/mobile_static_<?php echo $s5_responsive_mobile_bar_style; ?>.png), <?php } ?>linear-gradient(top, #<?php echo $s5_responsive_mobile_bar_start; ?> 0%,#<?php echo $s5_responsive_mobile_bar_stop; ?> 100%); /* IE8 */
	behavior: url(<?php echo $s5_directory_path ?>/css/PIE.htc);
	<?php } ?>
	font-family: <?php echo $s5_fonts_responsive_mobile_bar ?> !important;
	}
	
	.s5_responsive_mobile_drop_down_inner, .s5_responsive_mobile_drop_down_inner input, .s5_responsive_mobile_drop_down_inner button, .s5_responsive_mobile_drop_down_inner .button, #s5_responsive_mobile_drop_down_search .validate {
	font-family: <?php echo $s5_fonts_responsive_mobile_bar ?> !important;
	}
	
	.s5_responsive_mobile_drop_down_inner button:hover, .s5_responsive_mobile_drop_down_inner .button:hover {
	background:#<?php echo $s5_responsive_mobile_bar_stop; ?> !important;
	}
	
	#s5_responsive_mobile_drop_down_menu, #s5_responsive_mobile_drop_down_menu a, #s5_responsive_mobile_drop_down_login a {
	font-family: <?php echo $s5_fonts_responsive_mobile_bar ?> !important;
	color:#<?php echo $s5_responsive_mobile_bar_font_color; ?> !important;
	}
	
	#s5_responsive_mobile_bar_active, #s5_responsive_mobile_drop_down_menu .current a, .s5_responsive_mobile_drop_down_inner .s5_mod_h3, .s5_responsive_mobile_drop_down_inner .s5_h3_first {
	color:#<?php echo $s5_responsive_mobile_bar_active_color; ?> !important;
	}
	
	.s5_responsive_mobile_drop_down_inner button, .s5_responsive_mobile_drop_down_inner .button {
	background:#<?php echo $s5_responsive_mobile_bar_active_color; ?> !important;
	}
	
	#s5_responsive_mobile_drop_down_menu .active ul li, #s5_responsive_mobile_drop_down_menu .current ul li a, #s5_responsive_switch_mobile a, #s5_responsive_switch_desktop a, #s5_responsive_modile_drop_down_wrap {
	color:#<?php echo $s5_responsive_mobile_bar_font_color; ?> !important;
	}
	
	#s5_responsive_mobile_toggle_click_menu span {
	border-right:solid 1px #<?php echo $s5_responsive_mobile_bar_start; ?>;
	}

	#s5_responsive_mobile_toggle_click_menu {
	border-right:solid 1px #<?php echo $s5_responsive_mobile_bar_stop; ?>;
	}

	#s5_responsive_mobile_toggle_click_search span, #s5_responsive_mobile_toggle_click_register span, #s5_responsive_mobile_toggle_click_login span, #s5_responsive_mobile_scroll a {
	border-left:solid 1px #<?php echo $s5_responsive_mobile_bar_start; ?>;
	}

	#s5_responsive_mobile_toggle_click_search, #s5_responsive_mobile_toggle_click_register, #s5_responsive_mobile_toggle_click_login, #s5_responsive_mobile_scroll {
	border-left:solid 1px #<?php echo $s5_responsive_mobile_bar_stop; ?>;
	}

	.s5_responsive_mobile_open, .s5_responsive_mobile_closed:hover, #s5_responsive_mobile_scroll:hover {
	background:#<?php echo $s5_responsive_mobile_bar_start; ?>;
	}

	#s5_responsive_mobile_drop_down_menu .s5_responsive_mobile_drop_down_inner, #s5_responsive_mobile_drop_down_register .s5_responsive_mobile_drop_down_inner, #s5_responsive_mobile_drop_down_login .s5_responsive_mobile_drop_down_inner, #s5_responsive_mobile_drop_down_search .s5_responsive_mobile_drop_down_inner {
	background:#<?php echo $s5_responsive_mobile_bar_start; ?>;
	}

<?php } ?>

<?php if ($s5_responsive_tablet_hide != "" && $s5_responsive == "enabled"){ ?>

	@media screen and (min-width:580px) and (max-width: 970px){
	
		<?php echo @implode(",",$s5_responsive_tablet_hide); ?> {
			display:none;
		}
	
	}
	
<?php } ?>

<?php if ($s5_responsive_mobile_hide != "" && $s5_responsive == "enabled"){ ?>

	@media screen and (max-width: 579px){
	
		<?php echo @implode(",",$s5_responsive_mobile_hide); ?> {
			display:none;
		}
	
	}
	
<?php } ?>

<?php if ($s5_max_body_width != ""){ ?>

	.s5_wrap {
	max-width:<?php echo $s5_max_body_width; ?>px !important;
	}
	
<?php } ?>

<?php if ($s5_columns_fixed_fluid == "px" && $s5_responsive_column_increase == "enabled" && $s5_responsive == "enabled" && $s5_fixed_fluid == "%"){ ?>

	<?php if ($s5_max_body_width >= "1300"){ ?>

	@media screen and (min-width: 1300px){
	
		#s5_right_top_wrap {
		width:<?php echo $s5_right_column_width * 1.3; ?>px !important;
		}
		#s5_right_inset_wrap {
		width:<?php echo $s5_right_inset_width * 1.3; ?>px !important;
		}
		#s5_right_wrap {
		width:<?php echo $s5_right_width * 1.3; ?>px !important;
		}
		#s5_right_bottom_wrap {
		width:<?php echo $s5_right_column_width * 1.3 ?>px !important;
		}
		#s5_left_top_wrap {
		width:<?php echo $s5_left_column_width * 1.3; ?>px !important;
		}
		#s5_left_inset_wrap {
		width:<?php echo $s5_left_inset_width * 1.3; ?>px !important;
		}
		#s5_left_wrap {
		width:<?php echo $s5_left_width * 1.3; ?>px !important;
		}
		#s5_left_bottom_wrap {
		width:<?php echo $s5_left_column_width * 1.3; ?>px !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_column_width * 1.3; ?>px !important;
		margin-left:-<?php echo ($s5_right_column_width + $s5_left_column_width) * 1.3; ?>px !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_column_width * 1.3; ?>px !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_center_column_margin_left * 1.3; ?>px !important;
		margin-right:<?php echo $s5_center_column_margin_right * 1.3; ?>px !important;
		}
	
	}
	
	<?php } ?>
	
	<?php if ($s5_max_body_width >= "1900"){ ?>
	
	@media screen and (min-width: 1900px){
	
		#s5_right_top_wrap {
		width:<?php echo $s5_right_column_width * 1.6; ?>px !important;
		}
		#s5_right_inset_wrap {
		width:<?php echo $s5_right_inset_width * 1.6; ?>px !important;
		}
		#s5_right_wrap {
		width:<?php echo $s5_right_width * 1.6; ?>px !important;
		}
		#s5_right_bottom_wrap {
		width:<?php echo $s5_right_column_width * 1.6; ?>px !important;
		}
		#s5_left_top_wrap {
		width:<?php echo $s5_left_column_width * 1.6; ?>px !important;
		}
		#s5_left_inset_wrap {
		width:<?php echo $s5_left_inset_width * 1.6; ?>px !important;
		}
		#s5_left_wrap {
		width:<?php echo $s5_left_width * 1.6; ?>px !important;
		}
		#s5_left_bottom_wrap {
		width:<?php echo $s5_left_column_width * 1.6; ?>px !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_column_width * 1.6; ?>px !important;
		margin-left:-<?php echo ($s5_right_column_width + $s5_left_column_width) * 1.6; ?>px !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_column_width * 1.6; ?>px !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_center_column_margin_left * 1.6; ?>px !important;
		margin-right:<?php echo $s5_center_column_margin_right * 1.6; ?>px !important;
		}
	
	}
	
	<?php } ?>
	
	<?php if ($s5_max_body_width >= "2500"){ ?>
	
	@media screen and (min-width: 2500px){
	
		#s5_right_top_wrap {
		width:<?php echo $s5_right_column_width * 1.9; ?>px !important;
		}
		#s5_right_inset_wrap {
		width:<?php echo $s5_right_inset_width * 1.9; ?>px !important;
		}
		#s5_right_wrap {
		width:<?php echo $s5_right_width * 1.9; ?>px !important;
		}
		#s5_right_bottom_wrap {
		width:<?php echo $s5_right_column_width * 1.9; ?>px !important;
		}
		#s5_left_top_wrap {
		width:<?php echo $s5_left_column_width * 1.9; ?>px !important;
		}
		#s5_left_inset_wrap {
		width:<?php echo $s5_left_inset_width * 1.9; ?>px !important;
		}
		#s5_left_wrap {
		width:<?php echo $s5_left_width * 1.9; ?>px !important;
		}
		#s5_left_bottom_wrap {
		width:<?php echo $s5_left_column_width * 1.9; ?>px !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_column_width * 1.9; ?>px !important;
		margin-left:-<?php echo ($s5_right_column_width + $s5_left_column_width) * 1.9; ?>px !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_column_width * 1.9; ?>px !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_center_column_margin_left * 1.9; ?>px !important;
		margin-right:<?php echo $s5_center_column_margin_right * 1.9; ?>px !important;
		}
	
	}
	
	<?php } ?>
	
<?php } ?>

<?php 
//Find the largest width
if ($s5_right_width >= $s5_right_inset_width) {
$s5_right_largest = $s5_right_width;
}
else {
$s5_right_largest = $s5_right_inset_width;
}
if ($s5_left_width >= $s5_left_inset_width) {
$s5_left_largest = $s5_left_width;
}
else {
$s5_left_largest = $s5_left_inset_width;
}
if (($s5_pos_right_inset == "unpublished" && $s5_pos_right_inset == "unpublished") && ($s5_pos_right_top == "published" || $s5_pos_right_bottom == "published")) {
$s5_right_largest = $s5_right_column_width;
}
if (($s5_pos_left_inset == "unpublished" && $s5_pos_left_inset == "unpublished") && ($s5_pos_left_top == "published" || $s5_pos_left_bottom == "published")) {
$s5_left_largest = $s5_left_column_width;
}
?>

<?php if ($s5_columns_fixed_fluid == "px" && $s5_responsive == "enabled") { ?>

	@media screen and (max-width: 970px){
	
		#s5_right_top_wrap {
		width:<?php echo $s5_right_largest; ?>px !important;
		}
		#s5_right_inset_wrap {
		width:<?php echo $s5_right_largest; ?>px !important;
		}
		#s5_right_wrap {
		width:<?php echo $s5_right_largest; ?>px !important;
		}
		#s5_right_bottom_wrap {
		width:<?php echo $s5_right_largest; ?>px !important;
		}
		#s5_left_top_wrap {
		width:<?php echo $s5_left_largest; ?>px !important;
		}
		#s5_left_inset_wrap {
		width:<?php echo $s5_left_largest; ?>px !important;
		}
		#s5_left_wrap {
		width:<?php echo $s5_left_largest; ?>px !important;
		}
		#s5_left_bottom_wrap {
		width:<?php echo $s5_left_largest; ?>px !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_largest; ?>px !important;
		margin-left:-<?php echo ($s5_right_largest + $s5_left_largest); ?>px !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_largest; ?>px !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_left_largest; ?>px !important;
		margin-right:<?php echo $s5_right_largest; ?>px !important;
		}
	
	}
	

<?php } ?>


<?php if ($s5_columns_fixed_fluid == "px" && $s5_responsive_columns_small_tablet == "reduce"  && $s5_responsive == "enabled"){ ?>

	@media screen and (max-width: 750px){
	
		#s5_right_top_wrap {
		width:<?php echo $s5_right_largest * 0.8; ?>px !important;
		}
		#s5_right_inset_wrap {
		width:<?php echo $s5_right_largest * 0.8; ?>px !important;
		}
		#s5_right_wrap {
		width:<?php echo $s5_right_largest * 0.8; ?>px !important;
		}
		#s5_right_bottom_wrap {
		width:<?php echo $s5_right_largest * 0.8; ?>px !important;
		}
		#s5_left_top_wrap {
		width:<?php echo $s5_left_largest * 0.8; ?>px !important;
		}
		#s5_left_inset_wrap {
		width:<?php echo $s5_left_largest * 0.8; ?>px !important;
		}
		#s5_left_wrap {
		width:<?php echo $s5_left_largest * 0.8; ?>px !important;
		}
		#s5_left_bottom_wrap {
		width:<?php echo $s5_left_largest * 0.8; ?>px !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_largest * 0.8; ?>px !important;
		margin-left:-<?php echo ($s5_right_largest + $s5_left_largest) * 0.8; ?>px !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_largest * 0.8; ?>px !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_left_largest * 0.8; ?>px !important;
		margin-right:<?php echo $s5_right_largest * 0.8; ?>px !important;
		}
	
	}

<?php } ?>


<?php if ($s5_columns_fixed_fluid == "px" && $s5_responsive_columns_small_tablet == "single"  && $s5_responsive == "enabled"){ ?>

		@media screen and (max-width: 750px){

		#s5_columns_wrap_inner {
		width:100% !important;
		}

		#s5_center_column_wrap {
		width:100% !important;
		left:100% !important;
		}

		#s5_left_column_wrap {
		left:0% !important;
		}
		
		#s5_left_top_wrap, #s5_left_column_wrap, #s5_left_inset_wrap, #s5_left_wrap, #s5_left_bottom_wrap, #s5_right_top_wrap, #s5_right_column_wrap, #s5_right_inset_wrap, #s5_right_wrap, #s5_right_bottom_wrap {
		width:100% !important;
		}
		
		#s5_center_column_wrap_inner {
		margin:0px !important;
		}
		
		#s5_left_column_wrap {
		margin-right:0px !important;
		}
		
		#s5_right_column_wrap {
		margin-left:0px !important;
		}
		
		.items-row .item {
		width:100% !important;
		padding-left:0px !important;
		padding-right:0px !important;
		margin-right:0px !important;
		margin-left:0px !important;
		}
	
	}

<?php } ?>


<?php if ($s5_columns_fixed_fluid == "%" && $s5_responsive == "enabled" && $s5_responsive_fluid_comibine_columns != "default") { ?>

	@media screen and (max-width: <?php echo $s5_responsive_fluid_comibine_columns; ?>px){
	
		#s5_right_wrap, #s5_left_wrap, #s5_right_inset_wrap, #s5_left_inset_wrap, #s5_right_top_wrap, #s5_left_top_wrap, #s5_right_bottom_wrap, #s5_left_bottom_wrap {
		width:100% !important;
		}
		#s5_right_column_wrap {
		width:<?php echo $s5_right_largest/2; ?>% !important;
		margin-left:-<?php echo ($s5_right_largest + $s5_left_largest)/2; ?>% !important;
		}
		#s5_left_column_wrap {
		width:<?php echo $s5_left_largest/2; ?>% !important;
		}
		#s5_center_column_wrap_inner {
		margin-left:<?php echo $s5_left_largest; ?>% !important;
		margin-right:<?php echo $s5_right_largest; ?>% !important;
		}
	
	}
	

<?php } ?>


<?php if ($s5_columns_fixed_fluid == "%" && $s5_responsive_fluid_single_column != "default"  && $s5_responsive == "enabled"){ ?>

		@media screen and (max-width: <?php echo $s5_responsive_fluid_single_column; ?>px){

		#s5_columns_wrap_inner {
		width:100% !important;
		}

		#s5_center_column_wrap {
		width:100% !important;
		left:100% !important;
		}

		#s5_left_column_wrap {
		left:0% !important;
		}
		
		#s5_left_top_wrap, #s5_left_column_wrap, #s5_left_inset_wrap, #s5_left_wrap, #s5_left_bottom_wrap, #s5_right_top_wrap, #s5_right_column_wrap, #s5_right_inset_wrap, #s5_right_wrap, #s5_right_bottom_wrap {
		width:100% !important;
		}
		
		#s5_center_column_wrap_inner {
		margin:0px !important;
		}
		
		#s5_left_column_wrap {
		margin-right:0px !important;
		}
		
		#s5_right_column_wrap {
		margin-left:0px !important;
		}
		
		.items-row .item {
		width:100% !important;
		padding-left:0px !important;
		padding-right:0px !important;
		margin-right:0px !important;
		margin-left:0px !important;
		}
	
	}

<?php } ?>


<?php if ($s5_responsive_center_single_column != "default" && $s5_responsive == "enabled"){ ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_center_single_column; ?>px){
		
		#s5_middle_top .s5_float_left, #s5_middle_bottom .s5_float_left {
		width:100% !important;
		}
		
		.items-row .item {
		width:100% !important;
		padding-left:0px !important;
		padding-right:0px !important;
		margin-right:0px !important;
		margin-left:0px !important;
		}
	
	}

<?php } ?>



<?php if ($s5_responsive_row_widths_top_row_1 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_top_row_1_exploded = explode(":", $s5_responsive_row_widths_top_row_1); ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row1 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 #s5_pos_top_row1_1.s5_float_left, #s5_top_row1 #s5_pos_top_row1_2.s5_float_left, #s5_top_row1 #s5_pos_top_row1_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_top_row1 #s5_pos_top_row1_4.s5_float_left, #s5_top_row1 #s5_pos_top_row1_5.s5_float_left, #s5_top_row1 #s5_pos_top_row1_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 #s5_pos_top_row1_1.s5_float_left, #s5_top_row1 #s5_pos_top_row1_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_top_row1 #s5_pos_top_row1_3.s5_float_left, #s5_top_row1 #s5_pos_top_row1_4.s5_float_left, #s5_top_row1 #s5_pos_top_row1_5.s5_float_left, #s5_top_row1 #s5_pos_top_row1_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 #s5_pos_top_row1_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row1 #s5_pos_top_row1_2.s5_float_left, #s5_top_row1 #s5_pos_top_row1_3.s5_float_left, #s5_top_row1 #s5_pos_top_row1_4.s5_float_left, #s5_top_row1 #s5_pos_top_row1_5.s5_float_left, #s5_top_row1 #s5_pos_top_row1_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_1_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
			
			#s5_top_row1 #s5_pos_top_row1_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row1 #s5_pos_top_row1_2.s5_float_left, #s5_top_row1 #s5_pos_top_row1_3.s5_float_left, #s5_top_row1 #s5_pos_top_row1_4.s5_float_left, #s5_top_row1 #s5_pos_top_row1_5.s5_float_left, #s5_top_row1 #s5_pos_top_row1_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_top_row_2 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_top_row_2_exploded = explode(":", $s5_responsive_row_widths_top_row_2); ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 .s5_float_left {
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 #s5_pos_top_row2_1.s5_float_left, #s5_top_row2 #s5_pos_top_row2_2.s5_float_left, #s5_top_row2 #s5_pos_top_row2_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_top_row2 #s5_pos_top_row2_4.s5_float_left, #s5_top_row2 #s5_pos_top_row2_5.s5_float_left, #s5_top_row2 #s5_pos_top_row2_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 #s5_pos_top_row2_1.s5_float_left, #s5_top_row2 #s5_pos_top_row2_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_top_row2 #s5_pos_top_row2_3.s5_float_left, #s5_top_row2 #s5_pos_top_row2_4.s5_float_left, #s5_top_row2 #s5_pos_top_row2_5.s5_float_left, #s5_top_row2 #s5_pos_top_row2_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 #s5_pos_top_row2_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row2 #s5_pos_top_row2_2.s5_float_left, #s5_top_row2 #s5_pos_top_row2_3.s5_float_left, #s5_top_row2 #s5_pos_top_row2_4.s5_float_left, #s5_top_row2 #s5_pos_top_row2_5.s5_float_left, #s5_top_row2 #s5_pos_top_row2_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_2_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row2 #s5_pos_top_row2_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row2 #s5_pos_top_row2_2.s5_float_left, #s5_top_row2 #s5_pos_top_row2_3.s5_float_left, #s5_top_row2 #s5_pos_top_row2_4.s5_float_left, #s5_top_row2 #s5_pos_top_row2_5.s5_float_left, #s5_top_row2 #s5_pos_top_row2_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_top_row_3 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_top_row_3_exploded = explode(":", $s5_responsive_row_widths_top_row_3); ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 #s5_pos_top_row3_1.s5_float_left, #s5_top_row3 #s5_pos_top_row3_2.s5_float_left, #s5_top_row3 #s5_pos_top_row3_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_top_row3 #s5_pos_top_row3_4.s5_float_left, #s5_top_row3 #s5_pos_top_row3_5.s5_float_left, #s5_top_row3 #s5_pos_top_row3_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 #s5_pos_top_row3_1.s5_float_left, #s5_top_row3 #s5_pos_top_row3_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_top_row3 #s5_pos_top_row3_3.s5_float_left, #s5_top_row3 #s5_pos_top_row3_4.s5_float_left, #s5_top_row3 #s5_pos_top_row3_5.s5_float_left, #s5_top_row3 #s5_pos_top_row3_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 #s5_pos_top_row3_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row3 #s5_pos_top_row3_2.s5_float_left, #s5_top_row3 #s5_pos_top_row3_3.s5_float_left, #s5_top_row3 #s5_pos_top_row3_4.s5_float_left, #s5_top_row3 #s5_pos_top_row3_5.s5_float_left, #s5_top_row3 #s5_pos_top_row3_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_top_row_3_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_top_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_top_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_top_row3 #s5_pos_top_row3_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_top_row3 #s5_pos_top_row3_2.s5_float_left, #s5_top_row3 #s5_pos_top_row3_3.s5_float_left, #s5_top_row3 #s5_pos_top_row3_4.s5_float_left, #s5_top_row3 #s5_pos_top_row3_5.s5_float_left, #s5_top_row3 #s5_pos_top_row3_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_above_columns != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_above_columns_exploded = explode(":", $s5_responsive_row_widths_above_columns); ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner #s5_above_columns_1.s5_float_left, #s5_above_columns_inner #s5_above_columns_2.s5_float_left, #s5_above_columns_inner #s5_above_columns_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_above_columns_inner #s5_above_columns_4.s5_float_left, #s5_above_columns_inner #s5_above_columns_5.s5_float_left, #s5_above_columns_inner #s5_above_columns_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner #s5_above_columns_1.s5_float_left, #s5_above_columns_inner #s5_above_columns_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_above_columns_inner #s5_above_columns_3.s5_float_left, #s5_above_columns_inner #s5_above_columns_4.s5_float_left, #s5_above_columns_inner #s5_above_columns_5.s5_float_left, #s5_above_columns_inner #s5_above_columns_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner #s5_above_columns_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_above_columns_inner #s5_above_columns_2.s5_float_left, #s5_above_columns_inner #s5_above_columns_3.s5_float_left, #s5_above_columns_inner #s5_above_columns_4.s5_float_left, #s5_above_columns_inner #s5_above_columns_5.s5_float_left, #s5_above_columns_inner #s5_above_columns_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_columns_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_columns_inner #s5_above_columns_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_above_columns_inner #s5_above_columns_2.s5_float_left, #s5_above_columns_inner #s5_above_columns_3.s5_float_left, #s5_above_columns_inner #s5_above_columns_4.s5_float_left, #s5_above_columns_inner #s5_above_columns_5.s5_float_left, #s5_above_columns_inner #s5_above_columns_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_middle_top != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_middle_top_exploded = explode(":", $s5_responsive_row_widths_middle_top); ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top #s5_pos_middle_top_1.s5_float_left, #s5_middle_top #s5_pos_middle_top_2.s5_float_left, #s5_middle_top #s5_pos_middle_top_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_middle_top #s5_pos_middle_top_4.s5_float_left, #s5_middle_top #s5_pos_middle_top_5.s5_float_left, #s5_middle_top #s5_pos_middle_top_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top #s5_pos_middle_top_1.s5_float_left, #s5_middle_top #s5_pos_middle_top_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_middle_top #s5_pos_middle_top_3.s5_float_left, #s5_middle_top #s5_pos_middle_top_4.s5_float_left, #s5_middle_top #s5_pos_middle_top_5.s5_float_left, #s5_middle_top #s5_pos_middle_top_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top #s5_pos_middle_top_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_middle_top #s5_pos_middle_top_2.s5_float_left, #s5_middle_top #s5_pos_middle_top_3.s5_float_left, #s5_middle_top #s5_pos_middle_top_4.s5_float_left, #s5_middle_top #s5_pos_middle_top_5.s5_float_left, #s5_middle_top #s5_pos_middle_top_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_top_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_top_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_top {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_top #s5_pos_middle_top_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_middle_top #s5_pos_middle_top_2.s5_float_left, #s5_middle_top #s5_pos_middle_top_3.s5_float_left, #s5_middle_top #s5_pos_middle_top_4.s5_float_left, #s5_middle_top #s5_pos_middle_top_5.s5_float_left, #s5_middle_top #s5_pos_middle_top_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_above_body != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_above_body_exploded = explode(":", $s5_responsive_row_widths_above_body); ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner #s5_above_body_1.s5_float_left, #s5_above_body_inner #s5_above_body_2.s5_float_left, #s5_above_body_inner #s5_above_body_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_above_body_inner #s5_above_body_4.s5_float_left, #s5_above_body_inner #s5_above_body_5.s5_float_left, #s5_above_body_inner #s5_above_body_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner #s5_above_body_1.s5_float_left, #s5_above_body_inner #s5_above_body_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_above_body_inner #s5_above_body_3.s5_float_left, #s5_above_body_inner #s5_above_body_4.s5_float_left, #s5_above_body_inner #s5_above_body_5.s5_float_left, #s5_above_body_inner #s5_above_body_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner #s5_above_body_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_above_body_inner #s5_above_body_2.s5_float_left, #s5_above_body_inner #s5_above_body_3.s5_float_left, #s5_above_body_inner #s5_above_body_4.s5_float_left, #s5_above_body_inner #s5_above_body_5.s5_float_left, #s5_above_body_inner #s5_above_body_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_above_body_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_above_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_above_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_above_body_inner #s5_above_body_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_above_body_inner #s5_above_body_2.s5_float_left, #s5_above_body_inner #s5_above_body_3.s5_float_left, #s5_above_body_inner #s5_above_body_4.s5_float_left, #s5_above_body_inner #s5_above_body_5.s5_float_left, #s5_above_body_inner #s5_above_body_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_middle_bottom != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_middle_bottom_exploded = explode(":", $s5_responsive_row_widths_middle_bottom); ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom #s5_pos_middle_bottom_1.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_2.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_middle_bottom #s5_pos_middle_bottom_4.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_5.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom #s5_pos_middle_bottom_1.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_middle_bottom #s5_pos_middle_bottom_3.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_4.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_5.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom #s5_pos_middle_bottom_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_middle_bottom #s5_pos_middle_bottom_2.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_3.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_4.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_5.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_middle_bottom_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_middle_bottom_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_middle_bottom {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_middle_bottom #s5_pos_middle_bottom_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_middle_bottom #s5_pos_middle_bottom_2.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_3.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_4.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_5.s5_float_left, #s5_middle_bottom #s5_pos_middle_bottom_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_below_body != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_below_body_exploded = explode(":", $s5_responsive_row_widths_below_body); ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner #s5_below_body_1.s5_float_left, #s5_below_body_inner #s5_below_body_2.s5_float_left, #s5_below_body_inner #s5_below_body_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_below_body_inner #s5_below_body_4.s5_float_left, #s5_below_body_inner #s5_below_body_5.s5_float_left, #s5_below_body_inner #s5_below_body_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner #s5_below_body_1.s5_float_left, #s5_below_body_inner #s5_below_body_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_below_body_inner #s5_below_body_3.s5_float_left, #s5_below_body_inner #s5_below_body_4.s5_float_left, #s5_below_body_inner #s5_below_body_5.s5_float_left, #s5_below_body_inner #s5_below_body_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner #s5_below_body_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_below_body_inner #s5_below_body_2.s5_float_left, #s5_below_body_inner #s5_below_body_3.s5_float_left, #s5_below_body_inner #s5_below_body_4.s5_float_left, #s5_below_body_inner #s5_below_body_5.s5_float_left, #s5_below_body_inner #s5_below_body_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_body_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_body_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_body {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_body_inner #s5_below_body_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_below_body_inner #s5_below_body_2.s5_float_left, #s5_below_body_inner #s5_below_body_3.s5_float_left, #s5_below_body_inner #s5_below_body_4.s5_float_left, #s5_below_body_inner #s5_below_body_5.s5_float_left, #s5_below_body_inner #s5_below_body_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_below_columns != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_below_columns_exploded = explode(":", $s5_responsive_row_widths_below_columns); ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner #s5_below_columns_1.s5_float_left, #s5_below_columns_inner #s5_below_columns_2.s5_float_left, #s5_below_columns_inner #s5_below_columns_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_below_columns_inner #s5_below_columns_4.s5_float_left, #s5_below_columns_inner #s5_below_columns_5.s5_float_left, #s5_below_columns_inner #s5_below_columns_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner #s5_below_columns_1.s5_float_left, #s5_below_columns_inner #s5_below_columns_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_below_columns_inner #s5_below_columns_3.s5_float_left, #s5_below_columns_inner #s5_below_columns_4.s5_float_left, #s5_below_columns_inner #s5_below_columns_5.s5_float_left, #s5_below_columns_inner #s5_below_columns_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner #s5_below_columns_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_below_columns_inner #s5_below_columns_2.s5_float_left, #s5_below_columns_inner #s5_below_columns_3.s5_float_left, #s5_below_columns_inner #s5_below_columns_4.s5_float_left, #s5_below_columns_inner #s5_below_columns_5.s5_float_left, #s5_below_columns_inner #s5_below_columns_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_below_columns_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_below_columns_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_below_columns_inner {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_below_columns_inner #s5_below_columns_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_below_columns_inner #s5_below_columns_2.s5_float_left, #s5_below_columns_inner #s5_below_columns_3.s5_float_left, #s5_below_columns_inner #s5_below_columns_4.s5_float_left, #s5_below_columns_inner #s5_below_columns_5.s5_float_left, #s5_below_columns_inner #s5_below_columns_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_bottom_row_1 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_bottom_row_1_exploded = explode(":", $s5_responsive_row_widths_bottom_row_1); ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 #s5_pos_bottom_row1_1.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_2.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_bottom_row1 #s5_pos_bottom_row1_4.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_5.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 #s5_pos_bottom_row1_1.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_bottom_row1 #s5_pos_bottom_row1_3.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_4.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_5.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 #s5_pos_bottom_row1_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row1 #s5_pos_bottom_row1_2.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_3.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_4.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_5.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_1_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_1_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row1 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row1 #s5_pos_bottom_row1_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row1 #s5_pos_bottom_row1_2.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_3.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_4.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_5.s5_float_left, #s5_bottom_row1 #s5_pos_bottom_row1_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_bottom_row_2 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_bottom_row_2_exploded = explode(":", $s5_responsive_row_widths_bottom_row_2); ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 #s5_pos_bottom_row2_1.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_2.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_bottom_row2 #s5_pos_bottom_row2_4.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_5.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 #s5_pos_bottom_row2_1.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_bottom_row2 #s5_pos_bottom_row2_3.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_4.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_5.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
			
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 #s5_pos_bottom_row2_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row2 #s5_pos_bottom_row2_2.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_3.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_4.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_5.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_2_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_2_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row2 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row2 #s5_pos_bottom_row2_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row2 #s5_pos_bottom_row2_2.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_3.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_4.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_5.s5_float_left, #s5_bottom_row2 #s5_pos_bottom_row2_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>


<?php if ($s5_responsive_row_widths_bottom_row_3 != "default" && $s5_responsive == "enabled"){

	$s5_responsive_row_widths_bottom_row_3_exploded = explode(":", $s5_responsive_row_widths_bottom_row_3); ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "1") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 .s5_float_left {
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "2") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 .s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "3") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 .s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "4") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 #s5_pos_bottom_row3_1.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_2.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_3.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}
			
			#s5_bottom_row3 #s5_pos_bottom_row3_4.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_5.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "5") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 #s5_pos_bottom_row3_1.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_2.s5_float_left {
			float:left !important;
			width:50% !important;
			}
			
			#s5_bottom_row3 #s5_pos_bottom_row3_3.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_4.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_5.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_6.s5_float_left {
			float:left !important;
			width:100% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "6") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 #s5_pos_bottom_row3_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row3 #s5_pos_bottom_row3_2.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_3.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_4.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_5.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_6.s5_float_left {
			float:left !important;
			width:33.3% !important;
			}

		}
		
	<?php } ?>
	
	<?php if ($s5_responsive_row_widths_bottom_row_3_exploded[0] == "7") { ?>

		@media screen and (min-width:580px) and (max-width: <?php echo $s5_responsive_row_widths_bottom_row_3_exploded[1]; ?>px){
		
			<?php if ($s5_resize_columns_row_distribution == "no") { ?>
			.s5_resize_bottom_row3 {
			min-height: 0px !important;
			}
			<?php } ?>
		
			#s5_bottom_row3 #s5_pos_bottom_row3_1.s5_float_left {
			float:left !important;
			width:100% !important;
			}
			
			#s5_bottom_row3 #s5_pos_bottom_row3_2.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_3.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_4.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_5.s5_float_left, #s5_bottom_row3 #s5_pos_bottom_row3_6.s5_float_left {
			float:left !important;
			width:50% !important;
			}

		}
		
	<?php } ?>

<?php } ?>




</style>