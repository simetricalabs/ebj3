<?php defined('_JEXEC') or die('Restricted access'); ?>
<link rel="StyleSheet" href="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>dtree.css" type="text/css" />
<script type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>dtree.js"></script>
<div style="overflow:hidden;width:<?php echo $width; ?>px;">
<script type="text/javascript">
	<!--
	fpath = '<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_images') . 'dtree/folder.gif'; ?>';
	ppath = '<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_images') . 'dtree/page.gif'; ?>';

	d = new dTree('d');

	d.config.closeSameLevel = <?php echo ($closesamelevel) ? 'true':'false'; ?>; 
	d.config.inOrder = true;
	d.config.domain = '<?php echo $uri->getHost(); ?>';
	d.config.path = '<?php echo JUri::base(true); ?>';
	for(key in d.icon){d.icon[key] = d.config.path + '/' + d.icon[key];}
	<?php
	
	if ( $root_catid == 0 ) { ?>
	d.add(0,-1, '<?php echo JText::_( 'MOD_MT_DTREE_ROOT' ) ?>', '<?php echo JRoute::_("index.php?option=com_mtree" . $itemid); ?>');
	<?php } else { 
	$db = JFactory::getDBO();
	$db->setQuery( "SELECT cat_name, cat_id FROM #__mt_cats WHERE cat_id ='".$root_catid."'" );
	$root = $db->loadObject();
	?>
	d.add(<?php echo $root_catid ?>,-1, '<?php echo $root->cat_name ?>', '<?php echo JRoute::_("index.php?option=com_mtree&task=listcats&cat_id=$root_catid" . $itemid); ?>');

	<?php
	}
	foreach( $categories AS $cat ) {

		if ( $show_empty_cat == 0 && $cat->cat_links == 0 && $cat->cat_cats == 0 ) {
			// Do Nothing
		} else {
			echo "\n ";
			echo "d.add(";
			echo $cat->cat_id.",";
			echo $cat->cat_parent.",";
			
			// Print Category Name
			echo "'".addslashes(htmlspecialchars($cat->cat_name, ENT_QUOTES ));
			if ( $show_totalcats xor $show_totallisting ) {
				echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
			} elseif( $show_totalcats && $show_totallisting ) {
				echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
			}
			echo "',";

			echo "'".JRoute::_("index.php?option=com_mtree&task=listcats&cat_id=$cat->cat_id" . $itemid)."',";
			echo "'', '',";
			echo "fpath";
			echo ");";
		}

	}

	if ( !is_null($listings) ) {
		foreach( $categories AS $cat ) {

			foreach( $listings AS $cl ) {
				if( $cl->cat_id == $cat->cat_id ) {
					echo "\n ";
					echo "d.add(";
					echo (10000 + $cl->link_id).",";
					echo $cat->cat_id.",";
					
					// Print Listing Name
					echo "'".addslashes(htmlspecialchars($cl->link_name, ENT_QUOTES ))."',";
					echo "'".JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$cl->link_id" . $itemid)."',";
					echo "'', '',";
					echo "ppath";
					echo ");";
					unset( $listings[$cl->link_id] );
				}
			}
		}
		
		foreach( $listings AS $cl ) {
			echo "\n ";
			echo "d.add(";
			echo (10000 + $cl->link_id).",";
			echo $cl->cat_id.",";
			
			// Print Listing Name
			echo "'".addslashes(htmlspecialchars($cl->link_name, ENT_QUOTES ))."',";
			echo "'".JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$cl->link_id" . $itemid)."',";
			echo "'', '',";
			echo "ppath";
			echo ");";
			unset( $listings[$cl->link_id] );
		}
	}

	?>
	document.write(d);
	<?php if ( $show_listings == '1' && $link_id > 0 ) { ?>
	d.openTo(<?php echo (10000 + $link_id) ?>,true);
	<?php } else if( $cat_id > 0 ) { ?>
	d.openTo(<?php echo $cat_id ?>,true);
	<?php } ?>
	//-->
</script>
</div>