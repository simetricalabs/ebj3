 
<h2 class="contentheading"><?php 
	if( $this->my->id == $this->owner->id ) {
		echo JText::_( 'COM_MTREE_MY_PAGE' ) ?> (<?php echo $this->owner->username ?>)<?php
	} else {
		echo $this->owner->username;
	}
?></h2>
<?php include $this->loadTemplate('sub_ownerProfile.tpl.php'); ?>

<ul class="nav nav-tabs" style="clear:left">
	<li>
		<a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewuserslisting&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?> (<?php echo $this->total_links ?>)</a>
  	</li>
	<?php if($this->mtconf['show_review']) { 
	?><li>
		<a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersreview&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?> (<?php echo $this->total_reviews ?>)</a>
	</li><?php } ?>
	<?php if($this->mtconf['show_favourite']) { 
	?><li class="active">
		<a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersfav&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_FAVOURITES' ) ?> (<?php echo $this->pageNav->total ?>)</a>
	</li><?php } ?>
</ul>

<div id="listings"><?php
if (is_array($this->links) && !empty($this->links)) {

	$i = 0;
	foreach ($this->links AS $link) {
		$i++;
		$link_fields = $this->links_fields[$link->link_id];
		include $this->loadTemplate('sub_listingSummary.tpl.php');
	}
	
	if( $this->pageNav->total > $this->pageNav->limit ) {
	?>
	<div class="pagination">
		<p class="counter pull-right">
			<?php echo $this->pageNav->getPagesCounter(); ?>
		</p>
		<?php echo $this->pageNav->getPagesLinks(); ?>
	</div>
	<?php
	}

} else {

	?><center><?php 

	if( $this->my->id == $this->owner->id ) {
		echo JText::_( 'COM_MTREE_YOU_DO_NOT_HAVE_ANY_FAVOURITES' );
	} else {
		echo JText::_( 'COM_MTREE_THIS_USER_DO_NOT_HAVE_ANY_FAVOURITES' );
	}
	
	?></center><?php
	
} ?></div>