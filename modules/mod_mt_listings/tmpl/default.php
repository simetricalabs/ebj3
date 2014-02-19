<?php /* $Id: default.php 2010 2013-08-02 09:32:44Z cy $ */ defined('_JEXEC') or die('Restricted access'); ?>
<style type="text/css">
.mod_mt_listings.tiles {
	overflow:hidden;
	margin:0;
}
.mod_mt_listings.tiles li {
	margin-bottom: 2px;
	padding: 2px 0 2px 10px;
	list-style: none;
	float: left;
}
#<?php echo $uniqid; ?> li {
	<?php
	if( !empty($tile_width) && $tile_width > 0 ) {
		echo 'width: '.$tile_width.";\n";
	}
	if( !empty($tile_height) && $tile_height > 0 ) {
		echo 'height: '.$tile_height.";\n";
	}
	if( $tiles_flow == 'vertical' ) {
		echo "clear: both;\n";
	}
?>}
.mod_mt_listings.tiles li.showmore {
	clear: both;
}
#<?php echo $uniqid; ?> li a img {
	width: <?php echo $image_size; ?>;
	height: <?php echo $image_size; ?>;
}
#<?php echo $uniqid; ?>.mod_mt_listings.tiles .name {
	text-align:<?php echo $name_alignment; ?>;
}
#<?php echo $uniqid; ?>.mod_mt_listings.tiles .name {
	display:block;;
}
#<?php echo $uniqid; ?>.mod_mt_listings.tiles li small {
	display:block;;
}
#<?php echo $uniqid; ?>.mod_mt_listings.tiles li a.top-listing-thumb {
	vertical-align:top;
	float:left;
	border:1px solid #ddd;
	margin-right:1em;
	background-color:#e1e6fa;
	padding:2px;
	margin-bottom:.5em;
}
#<?php echo $uniqid; ?>.mod_mt_listings.tiles li small {
	display:block;
	line-height:1.6em;
	font-size:.9em;
}

</style>
<ul id="<?php echo $uniqid; ?>" class="mod_mt_listings tiles">
<?php
global $mtconf;

$i = 0;
if ( is_array($listings) ) {
	foreach( $listings AS $l ) {
		echo '<li'.(($i==0)?' class="first"':'').'>';

		// Image
		if( $show_images )
		{
			echo '<a class="top-listing-thumb" href="' . $l->link . '">';
			if( isset($l->image_path) && !empty($l->image_path) )
			{
				echo '<img align="bottom" border="0" src="'. $l->image_path . '" alt="' . $l->link_name . '" />';
			}
			else
			{
				?><img src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_images'); ?>noimage_thb.png" width="<?php echo $mtconf->get('resize_small_listing_size'); ?>" height="<?php echo $mtconf->get('resize_small_listing_size'); ?>" alt="<?php echo $l->link_name; ?>" /><?php
			}
			echo  '</a>';
		}

		require JModuleHelper::getLayoutPath('mod_mt_listings', '_fields');

		echo '</li>';
		$i++;
	}
}

if ( $show_more ) {
	echo '<li class="showmore">';
	echo '<a href="';
	echo $show_more_link;
	echo '" class="'.$listingclass.'">';
	echo $caption_showmore . '</a></li>';
}

?></ul>