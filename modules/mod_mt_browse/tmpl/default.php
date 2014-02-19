<?php defined('_JEXEC') or die('Restricted access'); ?>
<ul class="nav menu<?php echo $class_sfx; ?>">
	<li><a href="<?php echo $root->link; ?>"><?php echo $root->name; ?></a>
		<ul>
		<?php
		// Print subcategories
		if( !is_null( $pathway ) ): foreach( $pathway AS $pcat ):
		?><li class="parent active">
			<a href="<?php echo $pcat->link; ?>" class="<?php echo $currentcat_class; ?>"><?php echo $pcat->cat_name; ?></a>
			<ul>
		<?php 
		endforeach;	endif;

		// Print Current category
		if ( !is_null($cat) ):
		?><li class="parent active">
			<a href="<?php echo $cat->link; ?>" class="<?php echo $subcat_class; ?>"><?php echo $cat->cat_name; ?></a>
			<?php if( !empty($cats) ): ?>
			<ul>
			<?php endif; ?>
		<?php
		endif;
	
		// Print subcategories
		foreach( $cats AS $cat):
			echo '<li>';
			?>
			<a href="<?php echo $cat->link; ?>"><?php echo $cat->cat_name; 
			if ( $show_totalcats xor $show_totallisting ):
				echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
			elseif( $show_totalcats && $show_totallisting ):
				echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
			endif;
			?>
			</a>
			</li><?php
		endforeach; 
	
		if ( !is_null($cat) ):
			?></li>
			<?php if( !empty($cats) ): ?>
			</ul>
			<?php endif;
		endif;
	
		$pathway_count = count($pathway);
		for( $i=0; $i<$pathway_count; $i++ ) {
			echo '</li></ul>';
		}
		?>
		</ul>
	</li>
</ul>