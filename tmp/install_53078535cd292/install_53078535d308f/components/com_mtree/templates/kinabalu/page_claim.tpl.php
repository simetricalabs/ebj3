<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			submitform( 'viewlink' );
			return;
		} else {
			form.submit();
		}
	}
</script>
 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_CLAIM_LISTING' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">

	<form action="<?php echo JRoute::_("index.php") ?>" method="post" name="mtForm" id="mtForm" class="form-horizontal">

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_MTREE_MESSAGE' ) ?></label>
			<div class="controls">
				<textarea name="message" rows="12" cols="69" class="span8"></textarea>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="button" onclick="javascript:submitbutton('send_claim')" class="btn btn-primary"><span class="icon-ok"></span> <?php echo JText::_( 'COM_MTREE_CLAIM_LISTING' ) ?></button>
				<button type="button" onclick="history.back();" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
			</div>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option ?>" />
		<input type="hidden" name="task" value="send_claim" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
		<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

	</form>

</div>