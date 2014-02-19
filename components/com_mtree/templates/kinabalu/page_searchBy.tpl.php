<div id="search-by" class="mt-template-<?php echo $this->template; ?> cat-id-<?php echo $this->cat_id ;?> tlcat-id-<?php echo $this->tlcat_id ;?>"> 
	
<h2 class="contentheading"><span class="customfieldcaption"><?php echo JText::_('COM_MTREE_SEARCH_BY'); ?></span></h2>

<?php
if( empty($this->taggable_fields) ) {

	?>
	<p /><center><?php echo JText::_( 'COM_MTREE_YOUR_SEARCH_DOES_NOT_RETURN_ANY_RESULT' ) ?></center><p />
	<?php
	
} else {
	if($this->pageNav->total > 0) {

		$i = 0;
		foreach ($this->taggable_fields AS $taggable_field) {
			$i++;
			echo '<li id="'.$taggable_field->elementId.'">';
			echo '<a href="'.$taggable_field->link.'">';
			echo $taggable_field->value;
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
		</div>
		<?php
		}
	}
}
?></div>