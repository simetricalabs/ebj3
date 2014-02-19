<?php defined('_JEXEC') or die('Restricted access'); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/base.gif" align="left" vspace="0" hspace="0" />
			<a href="<?php echo $root->link; ?>"><?php echo $root->name; ?></a>
		</td>
	</tr><?php
	$level = 0;
	// Print subcategories
	if( !is_null( $pathway ) ): foreach( $pathway AS $pcat ):
	?><tr><td>
		<?php echo str_repeat($spacer, $level++); ?>
		<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/joinbottom.png" border="0" align="left" vspace="0" hspace="0" />
		<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/folder.gif" align="left" vspace="0" hspace="0" />
		<a href="<?php echo $pcat->link; ?>" class="<?php echo $currentcat_class; ?>"><?php echo $pcat->cat_name; ?></a>
	</td></tr>
	<?php 
	endforeach;	endif;
	
	// Print Current category
	if ( !is_null($cat) ):
	?><tr><td>
		<?php echo str_repeat($spacer, $level++); ?>
		<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/joinbottom.png" align="left" vspace="0" hspace="0" />
		<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif" align="left" vspace="0" hspace="0" />
		<a href="<?php echo $cat->link; ?>" class="<?php echo $subcat_class; ?>"><?php echo $cat->cat_name; ?></a>
	</td></tr>
	<?php
	endif;
	
	// Print subcategories
	foreach( $cats AS $cat):
	?><tr><td>
		<?php echo str_repeat($spacer, $level); ?>
		<?php if ( $cat->cat_id == $cats[count($cats)-1]->cat_id ): ?>
			<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/joinbottom.png" align="left" vspace="0" hspace="0" />
		<?php else: ?>
			<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/join.png" align="left" vspace="0" hspace="0" />
		<?php endif; ?>
		<img src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images'); ?>dtree/folder.gif" align="left" vspace="0" hspace="0" />
		<a href="<?php echo $cat->link; ?>" class="<?php echo $subcat_class; ?>"><?php echo $cat->cat_name; 
		if ( $show_totalcats xor $show_totallisting ):
			echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
		elseif( $show_totalcats && $show_totallisting ):
			echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
		endif;
		?>
		</a>
		</td></tr>
	<?php endforeach; ?>
</table>