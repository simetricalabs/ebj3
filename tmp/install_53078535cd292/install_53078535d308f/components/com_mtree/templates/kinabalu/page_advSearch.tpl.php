<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCH' ) ?></h2>

<form action="<?php echo JRoute::_("index.php") ?>" method="get" name="mtForm" id="mtForm" class="form-horizontal">

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button>
			<button type="reset" class="btn"><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<?php printf(JText::_( 'COM_MTREE_RETURN_RESULTS_IF_X_OF_THE_FOLLOWING_CONDITIONS_ARE_MET' ),$this->lists['searchcondition']); ?>
		</div>
	</div>

	<?php if( isset($this->catlist) ) { ?>
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?>
		</div>
		<div class="controls">
			<?php echo $this->catlist; ?>
		</div>
	</div>
	<?php }

	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		if($field->hasSearchField()) {
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo $field->caption; ?>
		</div>
		<div class="controls">
			<?php echo $field->getSearchHTML(); ?>
		</div>
	</div>
	<?php
		}
		$this->fields->next();
	}
	?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button>
			<button type="reset" class="btn"><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
		</div>
	</div>

	<?php
	if( !$this->hasCategoryCF && !isset($this->catlist) ) {
	?><input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" /><?php	
	}
	?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
	<input type="hidden" name="option" value="com_mtree" />
	<input type="hidden" name="task" value="listall" />
	<input type="hidden" name="sort" value="<?php echo $this->config->get('advanced_search_sort_by'); ?>" />
</form>