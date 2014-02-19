<?php defined('_JEXEC') or die('Restricted access'); ?>
<ul class="menu<?php echo $class_sfx; ?>">
<?php
foreach( $list AS $key => $item )
{
	$chr = chr($key);
	echo '<li' . (($item->current)? ' id="current" class="active"':'') . '>';
	echo '<a href="' . $item->link . '"><span>' . $item->text . '</span>';
	if ($display_total_links) {
		echo " <small>(".$item->total.")</small>";
	}
	echo '</a>';
	echo '</li>';
}
?>
</ul>