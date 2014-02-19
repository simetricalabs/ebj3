<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			form.task.value='viewlink';
			form.submit();
			return;
		}

		// do field validation
		if (form.your_name.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else if (form.your_email.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else if (form.friend_name.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else if (form.friend_email.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else {
			form.task.value=pressbutton;
			try {
				form.onsubmit();
				}
			catch(e){}
			form.submit();
		}
	}
</script>
 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_RECOMMEND_LISTING_TO_FRIEND' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">

	<form action="<?php echo JRoute::_("index.php") ?>" method="post" name="mtForm" id="mtForm" class="form-horizontal">

			<label><strong><?php echo JText::_( 'COM_MTREE_FROM' ) ?></strong></label>

			<div class="control-group">
				<label class="control-label"><?php echo JText::_( 'COM_MTREE_YOUR_NAME' ) ?></label>
				<div class="controls">
					<input type="text" name="your_name" value="<?php echo ($this->my->id) ? $this->my->name : ''; ?>" />
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label"><?php echo JText::_( 'COM_MTREE_YOUR_EMAIL' ) ?></label>
				<div class="controls">
					<input type="text" name="your_email" value="<?php echo ($this->my->id) ? $this->my->email : ''; ?>" />
				</div>
			</div>

			<label><strong><?php echo JText::_( 'COM_MTREE_TO' ) ?></strong></label>

			<div class="control-group">
				<label class="control-label"><?php echo JText::_( 'COM_MTREE_FRIENDS_NAME' ) ?></label>
				<div class="controls">
					<input type="text" name="friend_name" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo JText::_( 'COM_MTREE_FRIENDS_EMAIL' ) ?></label>
				<div class="controls">
					<input type="text" name="friend_email" />
				</div>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->option ?>" />
			<input type="hidden" name="task" value="send_recommend" />
			<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<div class="controls controls-row">
				<button type="button" onclick="javascript:submitbutton('send_recommend')" class="btn btn-primary"><span class="icon-ok"></span> <?php echo JText::_( 'COM_MTREE_SEND' ) ?></button>
				<button type="button" onclick="javascript:submitbutton('cancel')" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
			</div>

	</form>

</div>