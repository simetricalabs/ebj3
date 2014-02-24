 
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
	?><li class="active">
		<a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersreview&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?> (<?php echo $this->pageNav->total ?>)</a>
	</li><?php } ?>
	<?php if($this->mtconf['show_favourite']) { 
	?><li class="">
		<a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersfav&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_FAVOURITES' ) ?> (<?php echo $this->total_favourites ?>)</a>
	</li><?php } ?>
</ul>

<div class="reviews">
<?php if (is_array($this->reviews) && !empty($this->reviews)) { 

	foreach ($this->reviews AS $review): 
	?>
	<div class="review">
		<div class="review-listing"><?php $this->plugin('ahref', array("path"=>"index.php?option=".$this->option."&task=viewlink&link_id=".$review->link_id."&Itemid=".$this->Itemid), $review->link_name); ?></div>
		<div class="review-head">
		<div class="review-title"><?php 

		if($review->rating > 0) { ?><div class="review-rating"><?php $this->plugin( 'review_rating', $review->rating ); ?></div><?php }

		$this->plugin('ahref', array("path"=>"index.php?option=".$this->option."&task=viewlink&link_id=".$review->link_id."&Itemid=".$this->Itemid,"fragment"=>"rev-".$review->rev_id), $review->rev_title,'id="rev-'.$review->rev_id.'"'); 
		
		?></div>
		
		<div class="review-info"><?php 
		echo JText::_( 'COM_MTREE_REVIEWED_BY' ) ?><span class="review-owner"><?php echo ( ($review->user_id) ? $review->username : $review->guest_name); ?></span>, <?php echo date("F j, Y",strtotime($review->rev_date)) ?>
		</div><?php 
		
		echo '<div id="rhc'.$review->rev_id.'" class="found-helpful"'.( ($review->vote_total==0)?' style="display:none"':'' ).'>';
		echo '<span id="rh'.$review->rev_id.'">';
		if( $review->vote_total > 0 ) { 
			echo JText::sprintf( 'COM_MTREE_PEOPLE_FIND_THIS_REVIEW_HELPFUL', $review->vote_helpful, $review->vote_total );
		}
		echo '</span>';
		echo '</div>';
		
		echo '</div>';
		?>
		<div class="review-text">
		<?php 
		if ($review->link_image) {
			echo '<div class="thumbnail">';
			echo '<a href="index.php?option=com_mtree&task=viewlink&link_id=' . $review->link_id . '&Itemid=' . $this->Itemid . '">';
			$this->plugin( 'mt_image', $review->link_image, '3', $review->link_name );
			echo '</a>';
			echo '</div>';
		}
		
		echo $review->rev_text;

		if( !empty($review->ownersreply_text) && $review->ownersreply_approved ) {
			echo '<div class="owners-reply">';
			echo '<span>'.JText::_( 'COM_MTREE_OWNERS_REPLY' ).'</span>';
			echo '<p>' . $review->ownersreply_text . '</p>';
			echo '</div>';
		}
		?>
		</div>
	</div>
	<?php
	endforeach; 

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
		echo JText::_( 'COM_MTREE_YOU_DO_NOT_HAVE_ANY_REVIEWS' );
	} else {
		echo JText::_( 'COM_MTREE_THIS_USER_DO_NOT_HAVE_ANY_REVIEWS' );
	}
	?></center><?php
	
}
?></div>