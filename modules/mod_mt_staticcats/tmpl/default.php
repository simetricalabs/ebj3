<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if( !empty($categories) ): ?>
<ul class="nav menu<?php echo $class_sfx; ?>">
<?php foreach( $categories AS $cat):

	echo '<li';
	if( $cat_id == $cat->cat_id ) {
		echo ' class="parent active"';
	}
	echo '>';
	echo '<a href="'. $cat->link .'">'.$cat->cat_name;
	if ( $show_totalcats xor $show_totallisting ) {
		echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
	} elseif( $show_totalcats && $show_totallisting ) {
		echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
	}
	echo '</a>';
	echo '</li>';

endforeach;
?>
</ul>
<?php endif; ?>