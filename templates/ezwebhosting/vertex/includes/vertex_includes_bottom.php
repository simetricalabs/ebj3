<!-- Page scroll, tooltips, multibox, and ie6 warning -->	
	<!-- Start compression if enabled -->	
	<?php if ($s5_compress_js == "yes" || $s5_compress_css == "yes") { ob_start(); } ?>
	<?php 
	if ($s5_scrolltotop  == "yes") { ?>
		<div id="s5_scroll_wrap"<?php if ($template_date != "July 2011" && $template_date != "December 2011") {?> class="s5_wrap"<?php } ?>>
			<?php require(dirname(__FILE__)."/../../vertex/page_scroll.php"); } ?>
		</div>
	<?php
	if ($s5_tooltips  == "yes" || $s5_multibox  == "yes") { require(dirname(__FILE__)."/../../vertex/tooltips_and_multibox.php"); }
	if ($s5_lazyload  != "disabled") { require(dirname(__FILE__)."/../../vertex/lazy_load.php"); }
	if ($s5_ie6plugin == "yes") { require(dirname(__FILE__)."/../../vertex/ie6_warning.php"); }
	if ($s5_resize_columns  != "disabled") { require(dirname(__FILE__)."/../../vertex/resize_columns.php"); }
	?>
	
<!-- Additional scripts to load just before closing body tag -->
	<?php 
	
	if ($s5_additional_scripts1 != "") {
		echo $s5_additional_scripts1;
	}
	
	if ($s5_additional_scripts2 != "") {
		echo $s5_additional_scripts2;
	}
	
	?>

<!-- Info Slide script - JS and CSS called in header -->
	<?php if ($s5_info_slide == "yes") { ?>
	<script type='text/javascript'>
	new Slidex();
	</script>
	<?php } ?>
	
<!-- File compression. Needs to be called last on this file -->	
	<?php if ($s5_compress_js == "yes" || $s5_compress_css == "yes") {
	require(dirname(__FILE__)."/../../vertex/compression/bottom_css_and_js_compression.php");
	}
	?>
	
<!-- Responsive Bottom Mobile Bar -->
	<?php if ($s5_responsive == "enabled" || $s5_responsive_cookie  != "" || $s5_scrolltotop  == "yes") {
		require(dirname(__FILE__)."/../../vertex/responsive/responsive_mobile_bottom_bar.php");
	} ?>