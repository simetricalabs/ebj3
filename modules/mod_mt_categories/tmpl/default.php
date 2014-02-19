<?php defined('_JEXEC') or die('Restricted access'); ?>
<ul class="nav menu<?php echo $class_sfx; ?>">

	<?php if ( !is_null( $back_category ) ) { ?>
		<li><a href="<?php echo $back_category->link; ?>"><?php echo $back_symbol . '&nbsp;' . $back_category->cat_name; ?></a></li>
	<?php }

	if ( count($categories) > 0 ) {

		foreach( $categories AS $cat) {
			echo '<li' . (($cat->active) ? ' class="parent active"' : '') . '>';
			echo '<a href="'. $cat->link .'">'.$cat->cat_name;
			if ( $show_totalcats xor $show_totallisting ) {
				echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
			} elseif( $show_totalcats && $show_totallisting ) {
				echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
			}
			echo '</a>';
			echo '</li>';
		
		}

	}
?>
</ul>