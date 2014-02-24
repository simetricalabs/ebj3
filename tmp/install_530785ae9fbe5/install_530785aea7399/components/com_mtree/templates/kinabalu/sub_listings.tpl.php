<div id="listings"><?php

if( $this->task == "search" && empty($this->links) ) {

	if( empty($this->categories) ) {
	?>
	<p /><center><?php echo JText::_( 'COM_MTREE_YOUR_SEARCH_DOES_NOT_RETURN_ANY_RESULT' ) ?></center><p />
	<?php
	}
	
} else {
	?>
	<div class="pages-links">
		<span class="xlistings"><?php 
		if(
			isset($this->cat_id)
			&&
			($this->cat_id == 0 || (isset($this->cat_usemainindex) && $this->cat_usemainindex == 1)) 
			&&
			$this->mtconf['type_of_listings_in_index'] != 'listcurrent'
			&&
			$this->task == 'listcats'
		)
		{
			echo MText::_($this->listListing->list[$this->listListing->task]['title_lang_key'], $this->tlcat_id);
		}
		elseif( isset($this->link) )
		{
			echo MText::plural('LISTINGS',$this->tlcat_id,count($this->links));
		}
		else
		{
			echo $this->pageNav->getResultsCounter(); 
		}
		?></span>
		<?php // echo $this->pageNav->getPagesLinks(); ?>
		<?php if( in_array($this->task,array('listcats','listall','')) && $this->mtconf['display_all_listings_link'] ): ?>
		<span class="category-scope">
			<?php
			if( 
				in_array($this->task,array('listcats','')) 
			)
			{
				echo '<strong>'.JText::_( 'COM_MTREE_THIS_CATEGORY' ).'</strong>';
			} else {
				echo '<a href="';
				echo JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$this->cat_id);
				echo '">';
				echo JText::_( 'COM_MTREE_THIS_CATEGORY' );
				echo '</a>';
			}
			echo ' · ';
			if( $this->task == 'listall' ) {
				echo '<strong>'.MText::_( 'ALL_LISTINGS', $this->tlcat_id ).'</strong>';
			} else {
				echo '<a href="';
				echo JRoute::_('index.php?option=com_mtree&task=listall&cat_id='.$this->cat_id);
				echo '">';
				echo MText::_( 'ALL_LISTINGS', $this->tlcat_id );
				echo '</a>';
			}
			?>
		</span>
		<?php endif; ?>
	</div>

	<?php
	$i = 0;

	foreach ($this->links AS $link) {
		$i++;
		$link_fields = $this->links_fields[$link->link_id];
		include $this->loadTemplate('sub_listingSummary.tpl.php');
	}

	if( $this->pageNav->total > 0 ) {
	 ?>
	<div class="pagination">
		<?php 
		if( 
			((isset($this->cat_id) && $this->cat_id == 0) || (isset($this->cat_usemainindex) && $this->cat_usemainindex == 1)) 
			&&
			$this->mtconf['type_of_listings_in_index'] != 'listcurrent'
			&&
			isset($this->listListing)
		)
		{
			?>
			<ul class="pagination-list">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_mtree&task='.$this->mtconf['type_of_listings_in_index'].'&cat_id='.$this->cat_id); ?>"><?php echo $this->listListing->getViewMoreText(); ?></a>
				</li>
			</ul><?php
		}
		else
		{
			?>
			<p class="counter pull-right">
				<?php echo $this->pageNav->getPagesCounter(); ?>
			</p>
			<?php echo $this->pageNav->getPagesLinks();
		}
		?>
	</div>
	<?php
	}
}
?></div>