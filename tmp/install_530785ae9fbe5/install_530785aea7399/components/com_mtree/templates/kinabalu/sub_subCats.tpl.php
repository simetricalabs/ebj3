<?php if ($this->config->get('display_categories') && isset($this->categories) && is_array($this->categories) && !empty($this->categories)) { ?>
<div id="subcats">
<div class="title"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ); ?></div>
<?php 
	$numOfColumns = 2;
	$span = round(12 / $numOfColumns);
	$i = 0;
	
	#
	# Sub Categories
	#

	foreach ($this->categories as $cat) {
		if($this->task == 'listalpha' && $this->config->getTemParam('onlyShowRootLevelCatInListalpha',0) && $cat->cat_parent > 0) {
			continue;
		}
		
		if( $i % $numOfColumns == 0 ) {
			echo '<div class="row-fluid">';
		}

		echo '<div class="span'.$span.'">';
		if($cat->cat_featured) echo '<strong>';
		$this->plugin('ahref', "index.php?option=$this->option&task=listcats&cat_id=$cat->cat_id&Itemid=$this->Itemid", htmlspecialchars($cat->cat_name), '' );
		
		if( $this->config->getTemParam('displaySubcatsCatCount','0') ) {
			$count[] = $cat->cat_cats;
		}
		if( $this->config->getTemParam('displaySubcatsListingCount','1') ) {
			$count[] = $cat->cat_links;
		}
		if( !empty($count) ) {
			echo ' <small>('.implode('/',$count).')</small>';
			unset($count);
		}
		if($cat->cat_featured) echo '</strong>';
		echo '</div>';

		if( $i % $numOfColumns == ($numOfColumns -1) || $i == (count($this->categories)-1)) {
			echo '</div>';
		}
		
		$i++;
	}
?></div><?php 
}

	#
	# Related Categories
	#
	if ( isset($this->related_categories) && count($this->related_categories) > 0 ) {
		echo '<div id="relcats">';
		?><div class="title"><?php echo JText::_( 'COM_MTREE_RELATED_CATEGORIES' ); ?></div><?php
		echo '<ul>';
		foreach( $this->related_categories AS $related_category ) {
			echo '<li>';
			$this->plugin('ahref', "index.php?option=com_mtree&task=listcats&cat_id=".$related_category."&Itemid=$this->Itemid", $this->pathway->printPathWayFromCat_withCurrentCat( $related_category )); 
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
	?>