<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			submitform( 'viewlink' );
			return;
		}
		if (form.rev_text.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_REVIEW' ) ?>" );
		} else if (form.rev_title.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_TITLE' ) ?>" );
		<?php
		if( 
			$this->config->get('require_rating_with_review')
			&& 
			$this->config->get('allow_rating_during_review') 
			&&
			(
				$this->config->get('user_rating') == '0'
				||
				($this->config->get('user_rating') == '1' && $this->my->id > 0)
				||
				($this->config->get('user_rating') == '2' && $this->my->id > 0 && $this->my->id != $this->link->user_id)
			)
		) {			
			echo '} else if (form.rating.value == ""){ alert("' . JText::_( 'COM_MTREE_PLEASE_FILL_IN_RATING' ) . '"); ';
		}		
		?>} else {
			form.submit();
		}
	}
</script>
 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_WRITE_REVIEW' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">

	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="mtForm" id="mtForm" class="form-horizontal">
		<?php if ( !($this->my->id > 0) ) { ?>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_YOUR_NAME' ) ?></label>
			<div class="controls">
				<input type="text" name="guest_name" class="span8" size="20" value="<?php echo $this->user_fields_data['guest_name']; ?>" />
			</div>
		</div>
		<?php } ?>

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?></label>
			<div class="controls">
				<input type="text" name="rev_title" class="span6" size="69" value="<?php echo $this->user_fields_data['rev_title']; ?>" />
		<?php
		if( 
			$this->config->get('allow_rating_during_review') 
			&&
			(
				$this->config->get('user_rating') == '0'
				||
				($this->config->get('user_rating') == '1' && $this->my->id > 0)
				||
				($this->config->get('user_rating') == '2' && $this->my->id > 0 && $this->my->id != $this->link->user_id)
			)
		) {
		?>
				<select name="rating" class="span4">
					<?php echo $this->plugin( "options", $this->rating_options, $this->user_fields_data['rating'] ); ?>
				</select>
		<?php } ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_REVIEW' ) ?></label>
			<div class="controls">
				<?php $this->plugin('textarea', 'rev_text', $this->user_fields_data['rev_text'], 12, 90, 'class="span10"'); ?>
			</div>
		</div>
		
		<?php if( $this->config->get('use_captcha_review') ) { ?>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?></label>
			<div class="controls">
				<?php echo $this->captcha_html; ?>
			</div>
		</div>
		<?php } ?>

		<input type="hidden" name="option" value="<?php echo $this->option ?>" />
		<input type="hidden" name="task" value="addreview" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
		<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="controls controls-row">
			<button type="button" onclick="javascript:submitbutton('addreview')" class="btn btn-primary"><span class="icon-ok"></span> <?php echo JText::_( 'COM_MTREE_ADD_REVIEW' ) ?></button>
			<button type="button" onclick="history.back();" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
		</div>
	</form>

</div>