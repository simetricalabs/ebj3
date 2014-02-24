<?php
$profilepicture_loaded = jimport('mosets.profilepicture.profilepicture');
?>
<div class="row-fluid">
<div class="reviews">
	<?php if( !isset($hide_title) || $hide_title == false ) { ?>
	<div class="title"><?php echo JText::_( 'COM_MTREE_REVIEWS' ); ?> (<?php echo $this->total_reviews; ?>)</div>

	<?php
	}

	if (is_array($this->reviews) && !empty($this->reviews)):
		foreach ($this->reviews AS $review): 
	?>
	<div itemprop="reviews" itemscope itemtype="http://schema.org/Review" class="review row-fluid">
		<div class="review-head span2">
			<div class="review-info">
			<?php 
			if( $profilepicture_loaded )
			{
				$profilepicture = new ProfilePicture($review->user_id);

				echo ( ($review->user_id) ? '<a href="' . JRoute::_('index.php?option=com_mtree&amp;task=viewusersreview&amp;user_id='.$review->user_id) . '">': '');
				if( $profilepicture->exists() )
				{
					echo '<img width=80 height=80 src="'.$profilepicture->getURL(PROFILEPICTURE_SIZE_200).'" alt="'.$review->username.'" style="display:block"/>';
				}
				else
				{
					echo '<img width=80 height=80 src="'.$profilepicture->getFillerURL(PROFILEPICTURE_SIZE_200).'" alt="'.$review->username.'" style="display:block"/>';
				}
				echo ( ($review->user_id) ? '</a>': '');
			}
			
			echo JText::_( 'COM_MTREE_REVIEWED_BY' ); ?>
				<span itemprop="author" class="review-owner">
					<?php echo ( ($review->user_id) ? '<a href="' . JRoute::_('index.php?option=com_mtree&amp;task=viewusersreview&amp;user_id='.$review->user_id) . '">' . $review->username . '</a>': $review->guest_name); ?>
				</span>
				<p class="review-date">
					<time itemprop="publishDate" datetime="<?php echo strftime('%Y-%m-%d', strtotime($review->rev_date)); ?>">
						<?php echo strftime('%B %e, %Y', strtotime($review->rev_date)); ?>
					</time>
				</p>
			</div><?php 
		
		echo '<div id="rhc'.$review->rev_id.'" class="found-helpful"'.( ($review->vote_total==0)?' style="display:none"':'' ).'>';
		echo '<span id="rh'.$review->rev_id.'">';
		if( $review->vote_total > 0 ) { 
			printf( JText::_( 'COM_MTREE_PEOPLE_FIND_THIS_REVIEW_HELPFUL' ), $review->vote_helpful, $review->vote_total );
		}
		echo '</span>';
		echo '</div>';
		?>
		</div>
		<div class="span10">
			<div class="review-text">
				<div itemprop="name" class="review-title"><?php 
				if($review->rating > 0) { 
					?>
					<div class="review-rating"><?php $this->plugin( 'review_rating', $review->rating ); ?></div>
					<?php 
				}
				echo $review->rev_title;
				?></div>
				 <span itemprop="description"><?php echo trim($review->rev_text); ?></span><?php

			if( !empty($review->ownersreply_text) && $review->ownersreply_approved ) {
				echo '<div class="owners-reply">';
				echo '<span>'.JText::_( 'COM_MTREE_OWNERS_REPLY' ).'</span>';
				echo '<p>' . $review->ownersreply_text . '</p>';
				echo '</div>';
			}
			?>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<?php
	
					if( $this->my->id > 0 && $this->mtconf['user_vote_review'] == 1 ) { 
						echo '<div class="ask-helpful">';
						if( array_key_exists($review->rev_id, $this->voted_reviews) ) {
							// User has voted before
						} else {
							echo '<div class="ask-helpful2" id="ask'.$review->rev_id.'">';
							echo JText::_( 'COM_MTREE_WAS_THIS_REVIEW_HELPFUL' );
							echo '</div>';
						?> <span id="rhaction<?php echo $review->rev_id ?>" class="rhaction"><a href="javascript:voteHelpful('<?php echo $review->rev_id ?>','1');"><?php echo JText::_( 'JYES' ); ?></a>&nbsp;&nbsp;<a href="javascript:voteHelpful('<?php echo $review->rev_id ?>','-1')"><?php echo JText::_( 'JNO' ); ?></a></span><?php 
						}
						echo '</div>';
					} 
					?>
				</div>
				<div class="span6">
					<div class="review-reply-report-permalink">
					<?php
					if( ( ($this->mtconf['user_report_review'] == 1 && $this->my->id > 0) || $this->mtconf['user_report_review'] == 0) || ( $this->my->id == $this->link->user_id && empty($review->ownersreply_text) )) {
						if( ($this->mtconf['user_report_review'] == 1 && $this->my->id > 0) || $this->mtconf['user_report_review'] == 0) { 
							?><div class="review-report"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=reportreview&amp;rev_id='.$review->rev_id) ?>"><?php echo JText::_( 'COM_MTREE_REPORT_REVIEW' ); ?></a></div><?php 
						} 

						if( $this->my->id == $this->link->user_id && empty($review->ownersreply_text) && $this->mtconf['owner_reply_review'] == 1 ) { 
							?><div class="review-reply"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=replyreview&amp;rev_id='.$review->rev_id) ?>"><?php echo JText::_( 'COM_MTREE_REPLY_REVIEW' ) ?></a></div><?php 
						}
					}
					?>
						<div class="review-permalink">
							<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewreview&rev_id='.$review->rev_id); ?>"><?php echo JText::_('COM_MTREE_PERMALINK'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach;
	
	if(isset($this->reviewsNav))
	{
		if( !isset($hide_submitreview) || $hide_submitreview == false ) { ?>
		<p>
			<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=writereview&link_id='.$this->link->link_id); ?>" class="btn">
				<span class="icon-edit"></span> 
				<?php echo JText::_( 'COM_MTREE_WRITE_REVIEW'); ?>
			</a>
		</p>
		<?php
		}

		if($this->total_reviews > $this->reviewsNav->limit ) { 
		?>
		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->reviewsNav->getPagesCounter(); ?>
			</p>
			<?php echo $this->reviewsNav->getPagesLinks(); ?>
		</div>
		<?php }
	}
	else
	{
		?><p><?php
		if( !isset($hide_submitreview) || $hide_submitreview == false ) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=writereview&link_id='.$this->link->link_id); ?>" class="btn">
				<span class="icon-edit"></span> 
				<?php echo JText::_( 'COM_MTREE_WRITE_REVIEW'); ?>
			</a>
		<?php
		}
		?>
		<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewreviews&link_id='.$this->link->link_id); ?>" class="btn"><?php echo JText::sprintf('COM_MTREE_SEE_ALL_N_REVIEWS',$this->total_reviews); ?> <span class="icon-small icon-chevron-right"></span></a>
		<p><?php
	}
	?>

	<?php else: 
	
	if( !isset($hide_submitreview) || $hide_submitreview == false ) { ?>
	<p>
		<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=writereview&link_id='.$this->link->link_id); ?>" class="btn">
			<span class="icon-edit"></span> 
			<?php echo JText::_( 'COM_MTREE_BE_THE_FIRST_TO_REVIEW'); ?>
		</a>
	</p>
	<?php 
	}
	
	endif; ?>

</div>
</div>