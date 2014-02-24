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

 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_REPORT_LISTING' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">
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
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_REPORT_PROBLEM' ) ?></label>
			<div class="controls">
				<select name="report_type" class="span8">
				<?php echo $this->plugin( "options", $this->report_types, $this->user_fields_data['report_type'] ); ?>
				</select>
			</div>
		</div>


		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_MESSAGE' ) ?></label>
			<div class="controls">
			<textarea name="message" rows="8" cols="69" class="span8"><?php echo $this->user_fields_data['message']; ?></textarea>
			</div>
		</div>
		
		<?php if( $this->config->get('use_captcha_report') ) { JHtml::_('behavior.framework'); ?>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?></label>
			<div class="controls">
				<?php echo $this->captcha_html; ?>
			</div>
		</div>
		<?php } ?>
		
		<div class="control-group">
			<div class="controls">
				<button type="button" onclick="javascript:submitbutton('send_report')" class="btn btn-primary"><span class="icon-ok"></span> <?php echo JText::_( 'COM_MTREE_SEND' ) ?></button>
				<button type="button" onclick="javascript:submitbutton('cancel')" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
			</div>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option ?>" />
		<input type="hidden" name="task" value="send_report" />
		<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
		<?php echo JHtml::_('form.token'); ?>

	</form>
</div>