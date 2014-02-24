<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			submitform( 'listcats' );
			return;
		}

		// do field validation
		if (form.cat_name.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_CATEGORY_NAME' ) ?>" );
		} else {
			form.task.value=pressbutton;
			form.submit();
		}
	}
</script>

 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_ADD_CATEGORY' ) ?></h2>

<form action="<?php echo JRoute::_("index.php") ?>" method="post" name="mtForm" id="mtForm" class="form-horizontal">

	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_PARENT_CATEGORY' ) ?></label>
		<div class="controls">
			<strong><?php echo $this->pathway->printPathWayFromLink( 0, "index.php?option=com_mtree&task=listcats&Itemid=$this->Itemid" ) ?></strong>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></label>
		<div class="controls">
			<input class="span10" type="text" name="cat_name" maxlength="250" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></label>
		<div class="controls">
			<textarea name="cat_desc" rows="12" cols="60" class="span10"></textarea>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="button" value="" onclick="javascript:submitbutton('addcategory2')" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_ADD_CATEGORY' )?></button>
			<button type="button" onclick="javascript:submitbutton('cancel')" class="btn"><?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="addcategory2" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
	<input type="hidden" name="cat_parent" value="<?php echo $this->cat_parent ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>