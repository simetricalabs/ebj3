	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate form-horizontal">
		<?php if( $this->task == 'viewlink' ) { ?>
		<div class="title"><?php echo MText::_( 'CONTACT_OWNER', $this->tlcat_id ); ?></div>
		<?php } ?>
		<fieldset>
			<legend><?php echo JText::_('COM_MTREE_LISTING_CONTACT_FORM_LABEL'); ?></legend>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('contact_name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('contact_name'); ?></div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('contact_email'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('contact_email'); ?></div>
			</div>
			
			<?php //Dynamically load any additional fields from plugins. ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			        <?php if ($fieldset->name != 'contact'):?>
			               <?php $fields = $this->form->getFieldset($fieldset->name);?>
			               <?php foreach($fields as $field): ?>
			                    <?php if ($field->hidden): ?>
			                         <?php echo $field->input;?>
			                    <?php else:?>
			<div class="control-group">
				<div class="control-label">
				<?php echo $field->label; ?>
				</div>
				<div class="controls"><?php echo $field->input;?></div>
			</div>
			                    <?php endif;?>
			               <?php endforeach;?>
		        <?php endif ?>
		     	<?php endforeach;?>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('contact_message'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('contact_message'); ?></div>
			</div>

			<?php if( $this->config->get('use_captcha_contact') ) { JHtml::_('behavior.framework'); ?>
			<div class="control-group">
				<div class="control-label">
					<label class="required" for="recaptcha_response_field" id="recaptcha_response_field-lbl">
					<?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?><span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="controls"><?php echo $this->captcha_html; ?></div>
			</div>
			<?php } ?>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary validate" type="submit"><span class="icon-mail"></span> <?php echo JText::_('COM_MTREE_SEND'); ?></button>
					<?php if( $this->task != 'viewlink' ) { ?>
					<button type="button" onclick="history.back();" class="btn"><span class="icon-cancel"></span> <?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
					<?php } ?>
				</div>
			</div>
	
		</fieldset>

		<input type="hidden" name="option" value="<?php echo $this->option ?>" />
		<input type="hidden" name="task" value="send_contact" />
		<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

	</form>