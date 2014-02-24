<?php /* $Id$ */ defined('_JEXEC') or die('Restricted access'); ?>
<select onchange="javascript:if(this.value){window.location=this.value;}" size="1" name="id" style="width:<?php echo $dropdown_width; ?>px">
	<option value="" selected><?php echo $dropdown_select_text; ?></option>
	<?php foreach( $listings AS $l ) { ?>
	<option value="<?php echo $l->link; ?>"><?php echo $l->link_name; ?></option>
	<?php } ?>
</select>