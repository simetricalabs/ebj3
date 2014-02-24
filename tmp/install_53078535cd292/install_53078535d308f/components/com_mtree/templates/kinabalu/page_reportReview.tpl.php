 
<div id="listing">
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false,"delete"=>false) ) ?></h2>

<div class="review row-fluid">
	<div class="review-head span2">
		
		<div class="review-info"><?php 
			echo JText::_( 'COM_MTREE_REVIEWED_BY' ) ?> <b><?php echo ( ($this->review->user_id) ? $this->review->username : $this->review->guest_name); ?></b>, <?php echo date("F j, Y",strtotime($this->review->rev_date)) ?>
		</div>
		
		<?php 
		echo '<div id="rhc'.$this->review->rev_id.'" class="found-helpful"'.( ($this->review->vote_total==0)?' style="display:none"':'' ).'>';
		echo '<span id="rh'.$this->review->rev_id.'">';
		if( $this->review->vote_total > 0 ) { 
			printf( JText::_( 'COM_MTREE_PEOPLE_FIND_THIS_REVIEW_HELPFUL' ), $this->review->vote_helpful, $this->review->vote_total );
		}
		echo '</span>';
		echo '</div>';
		?>
	</div>

	<div class="review-text span10">
		<div class="review-title"><?php 
		if($this->review->rating > 0) { 
		?>
			<div class="review-rating"><?php $this->plugin( 'review_rating', $this->review->rating ); ?></div><?php 
		}
		$this->plugin('ahref', array("path"=>"index.php?option=".$this->option."&task=viewlink&link_id=".$this->link_id."&Itemid=".$this->Itemid,"fragment"=>"rev-".$this->review->rev_id), $this->review->rev_title,'id="rev-'.$this->review->rev_id.'"'); 

		?>
		</div>
		
		<span itemprop="description">
			<?php echo trim($this->review->rev_text); ?>
		</span>

		<?php
		if( !empty($this->review->ownersreply_text) && $this->review->ownersreply_approved ) {
			echo '<div class="owners-reply">';
			echo '<span>'.JText::_( 'COM_MTREE_OWNERS_REPLY' ).'</span>';
			echo '<p>' . nl2br(trim($this->review->ownersreply_text)) . '</p>';
			echo '</div>';
		}
		?>
	</div>
</div>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			form.task.value='viewlink';
			form.submit();
			return;
		}
	<?php if( $this->user_id <= 0 ) { ?>
		// do field validation
		if (form.your_name.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else {
	<?php } ?>
			form.task.value=pressbutton;
			try {
				form.onsubmit();
				}
			catch(e){}
			form.submit();
	<?php if( $this->user_id <= 0 ) { ?>
		}
	<?php } ?>
	}
</script>
<br clear="all" />
<div class="title"><?php echo JText::_( 'COM_MTREE_REPORT_REVIEW' ); ?></div>
<form action="<?php echo JRoute::_("index.php") ?>" method="post" name="mtForm" id="mtForm" class="form-horizontal">

	<?php if( $this->user_id <= 0 ) { ?>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_YOUR_NAME' ) ?></label>
		<div class="controls">
			<input type="text" name="your_name" class="span8" size="40" value="<?php echo $this->user_fields_data['your_name']; ?>" />
		</div>
	</div>
	<?php } ?>
	
	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_MESSAGE' ) ?></label>
		<div class="controls">
		<textarea name="message" rows="8" cols="69" class="span8"><?php echo $this->user_fields_data['message']; ?></textarea>
		</div>
	</div>

	<?php if( $this->config->get('use_captcha_reportreview') ) { JHtml::_('behavior.framework'); ?>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?></label>
		<div class="controls">
			<?php echo $this->captcha_html; ?>
		</div>
	</div>
	<?php } ?>

	<div class="control-group">
		<div class="controls">
			<button type="button" onclick="javascript:submitbutton('send_reportreview')" class="btn btn-primary"><span class="icon-ok"></span> <?php echo JText::_( 'COM_MTREE_SEND' ) ?></button>
			<button type="button" onclick="javascript:submitbutton('cancel')" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="send_reportreview" />
	<input type="hidden" name="rev_id" value="<?php echo $this->review->rev_id ?>" />
	<input type="hidden" name="link_id" value="<?php echo $this->review->link_id ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

</div>