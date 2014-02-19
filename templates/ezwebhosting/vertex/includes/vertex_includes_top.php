<!-- Call top bar for mobile devices if layout is responsive -->	
<?php if ($s5_responsive == "enabled") { ?>
	<?php require(dirname(__FILE__)."/../../vertex/responsive/responsive_mobile_top_bar.php"); ?>
<?php } ?>

<!-- Fixed Tabs -->	
<?php if($s5_lr_tab1_text != "" || $s5_lr_tab1_text != "") {
	require(dirname(__FILE__)."/../../vertex/fixed_tabs.php"); 
} ?>

<!-- Drop Down -->	
<?php if ($s5_pos_drop_down_1 == "published" || $s5_pos_drop_down_2 == "published" || $s5_pos_drop_down_3 == "published" || $s5_pos_drop_down_4 == "published" || $s5_pos_drop_down_5 == "published" || $s5_pos_drop_down_6 == "published") { ?>
	<?php require(dirname(__FILE__)."/../../vertex/drop_down.php"); ?>
<?php } ?>