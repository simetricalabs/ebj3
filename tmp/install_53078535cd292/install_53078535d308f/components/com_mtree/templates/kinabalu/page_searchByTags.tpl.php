<div id="search-by-tags" class="mt-template-<?php echo $this->template; ?> cf-id-<?php echo $this->cf_id;?> cat-id-<?php echo $this->cat_id ;?> tlcat-id-<?php echo $this->tlcat_id ;?>"> 
	
<h2 class="contentheading"><span class="customfieldcaption"><?php echo $this->customfieldcaption; ?></span></h2>

<?php

if( empty($this->tags) ) {

	?>
	<p /><center><?php echo JText::_( 'COM_MTREE_YOUR_SEARCH_DOES_NOT_RETURN_ANY_RESULT' ) ?></center><p />
	<?php
	
} else {
	if($this->pageNav->total > 0) {

		$i = 0;
		foreach ($this->tags AS $tag) {
			$i++;
			echo '<li id="'.$tag->elementId.'">';
			echo '<a href="'.$tag->link.'">';
			echo $tag->value;
			echo ' ('.$tag->items.')';
			echo '</a>';
			echo '</li>';
		}
		?>
		</ul>
		<?php
		if( $this->pageNav->total > $this->pageNav->limit ) { ?>
		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->pageNav->getPagesCounter(); ?>
			</p>
			<?php echo $this->pageNav->getPagesLinks(); ?>
		</div>		<?php
		}
	}
}
?></div>