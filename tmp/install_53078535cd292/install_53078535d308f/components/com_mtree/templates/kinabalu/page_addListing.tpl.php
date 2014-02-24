<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_js']; ?>category.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_js']; ?>addlisting.js"></script>
<?php if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) { ?>
<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_js']; ?>jquery-ui-1.8.24.custom.min.js"></script>
<?php } ?>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var JURI_ROOT='<?php echo JURI::root(); ?>';
	var active_cat=<?php echo intval($this->cat_id); ?>;
	var attCount=0;
	var attNextId=1;
	var maxAtt=<?php echo intval($this->mtconf['images_per_listing']); ?>;
	var maxSecCat=<?php echo intval($this->mtconf['max_num_of_secondary_categories']); ?>;
	var validations=[];
	var presubmitFunctions=[];
	var cachedFields;
	var form = document.mtForm;
	var validation_failed=false;
	<?php
	$this->fields->resetPointer();
	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		if($field->hasJSValidation() && $field->hasInputField()) {
			echo "\n";
			echo 'validations[\''.$field->getInputFieldID().'\']='.$field->getJSValidation().';';
		}
		if($field->hasJSPresubmit() && $field->hasInputField()) {
			echo "\n";
			echo 'presubmitFunctions[\''.$field->getInputFieldID().'\']='.$field->getJSPresubmitFunction().';';
		}
		
		$this->fields->next();
	}
	?>
	jQuery(document).ready(function(){
		<?php
		$this->fields->resetPointer();
		while( $this->fields->hasNext() ) {
			$field = $this->fields->getField();
			if($field->hasJSOnInit()) {
				echo "\n";
				echo $field->getJSOnInit().';';
			}
			$this->fields->next();
		}
		?>	
	});
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		var validation_fields = jQuery('#mtForm .controls input,#mtForm .controls textarea, #mtForm .controls select');
		var scroll = new Fx.SmoothScroll({links:'mtForm',wheelStops:false})
		validation_failed=false;
		
		runPresubmitFunctions(form,validation_fields);
		
		<?php
		$this->fields->resetPointer();
		while( $this->fields->hasNext() ) {
			$field = $this->fields->getField();
			if($field->hasJSOnSave()) {
				echo "\n";
				echo $field->getJSOnSave().';';
			}
			$this->fields->next();
		}
		?>
		
		if(validation_fields.length>0)
		{
			for(var index=0;index<validation_fields.length;index++)
			{
				var id=validation_fields[index].id;
				// Validate required fields
				if(
					(
						validation_fields[index].required !== false 
						&& 
						typeof(validation_fields[index].required) != 'undefined' 
						&& 
						!mtValidateIsEmpty(validation_fields[index])
					)
					||
					!mtValidate(validation_fields[index])
				){
					validation_failed=true;
					addValidationErrorHighlight(id.slice(2).split('_').shift().toInt());
					scroll.toElement(id);
					jQuery('#validate-advice-'+id).fadeToggle('fast').fadeToggle('slow');
					validation_fields[index].focus();
				}else{
					removeValidationErrorHighlight(validation_fields[index].id.slice(2).split('_').shift().toInt());
				}
			}
		}

		if(validation_failed){return false;}
		<?php
		if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) {
		?>
		var hash = jQuery("#sortableimages").sortable('serialize');
		if(hash != ''){document.mtForm.img_sort_hash.value=hash;}
		<?php } ?>
		
		if(attCount>0 && checkImgExt(attCount,jQuery("input:file[name|='image[]']"))==false) {
			alert('<?php echo addslashes(JText::_( 'COM_MTREE_PLEASE_SELECT_A_JPG_PNG_OR_GIF_FILE_FOR_THE_IMAGES' )) ?>');
			return;
		<?php if($this->mtconf['image_required']) { ?>
		} else if ( typeof(jQuery('input[type=file][name="image[]"]').filter('[value!=""]').val()) == 'undefined' && jQuery("input[name='keep_img[]']:checked").length == 0 ) {
			alert('<?php echo addslashes(JText::_( 'COM_MTREE_PLEASE_UPLOAD_AN_IMAGE_FOR_YOUR_LISTING' )) ?>');
			scroll.toElement('sortableimages');
			return;
		<?php } ?>
		} else {
			Joomla.submitform(form.task.value, document.getElementById('mtForm'));
		}
		return;
	}
</script>

 
<h2 class="contentheading"><?php echo ($this->link->link_id) ? JText::_( 'COM_MTREE_PAGE_HEADER_EDIT_LISTING' ) : 	JText::_( 'COM_MTREE_PAGE_HEADER_ADD_LISTING' ); ?></h2>

<form action="<?php echo JRoute::_("index.php") ?>" method="post" enctype="multipart/form-data" name="mtForm" id="mtForm" class="form-horizontal form-validate">
	<?php if( isset($this->warn_duplicate) && $this->warn_duplicate == 1 ) { ?>
		<p class="text-info"><?php echo JText::_( 'COM_MTREE_THERE_IS_ALREADY_A_PENDING_APPROVAL_FOR_MODIFICATION' ); ?></p>
	<?php } ?>
	
	<div class="control-group">
		<div class="controls">
			<button type="button" onclick="javascript:submitbutton('savelisting')" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SUBMIT_LISTING' ) ?></button>
			<button type="button" onclick="history.back();" class="btn"><?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
		</div>
	</div>

	<div class="control-group">
		<div class="controls required-note">
		<?php echo JText::_('COM_MTREE_LISTING_SUBMIT_ASTERISK_REQUIRED'); ?>
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="browsecat"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></label>
		</div>
		<div class="controls">
			<?php if($this->mtconf['allow_changing_cats_in_addlisting']) { ?>
			<span>
			<ul class="linkcats" id="linkcats">
			<li id="lc<?php echo $this->cat_id; ?>"><?php echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' ); ?></li>
			<?php
			if ( !empty($this->other_cats) ) {
				foreach( $this->other_cats AS $other_cat ) {
					if ( is_numeric( $other_cat ) ) {
						echo '<li id="lc' . $other_cat . '">';
						if($this->mtconf['allow_user_assign_more_than_one_category']) {
							echo '<a href="javascript:remSecCat('.$other_cat.')">'.JText::_( 'COM_MTREE_REMOVE' ).'</a>';
						}
						echo $this->pathWay->printPathWayFromCat_withCurrentCat( $other_cat, '' ) . '</li>';
					}
				}
			}
			?>
			</ul>
			<a href="#" onclick="javascript:togglemc();return false;" id="lcmanage"><?php echo JText::_( 'COM_MTREE_MANAGE' ); ?></a>
			<div id="mc_con">
			<div id="mc_selectcat">
				<span id="mc_active_pathway"><?php echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' ); ?></span>
				<?php echo $this->catlist; ?>
			</div>
			<button type="button" class="btn" id="mcbut1" onclick="updateMainCat()"><?php echo JText::_( 'COM_MTREE_UPDATE_CATEGORY' ) ?></button>
			<?php if($this->mtconf['allow_user_assign_more_than_one_category']) { ?>
			<button type="button" class="btn" id="mcbut2" onclick="addSecCat()"><?php echo JText::_( 'COM_MTREE_ALSO_APPEAR_IN_THIS_CATEGORIES' ) ?></button>
			<?php } ?>
			</div>
			<?php } else {

				echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' );

			} ?>
			</span>
		</div>
	</div>

	<div id="mtfields">
	<?php
	$this->fields->resetPointer();
	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		if($field->hasInputField()) {
	?>
	<div class="control-group <?php echo $field->getFieldTypeClassName(); ?>" id="field_<?php echo $field->getId(); ?>">
		<?php if($field->getCaption() != false) { ?>
		<div class="control-label" id="caption_<?php echo $field->getId(); ?>">
			<label for="<?php echo $field->getInputFieldId(); ?>" data-caption="<?php echo $field->getCaption(); ?>"<?php echo ($field->isRequired())?' class="required"':''; ?>>
			<?php if($field->isRequired()) { ?>
				<strong><?php echo $field->getCaption(); ?></strong>
				<span class="star">&#160;*</span>
			<?php } else { ?>
				<?php echo $field->getCaption(); ?>
			<?php } ?>
			</label>
		</div>
		<?php } ?>
		<div class="controls" id="input_<?php echo $field->getId(); ?>">
			<?php
			echo $field->getModPrefixText();
			echo $field->getInputHTML();
			echo $field->getModSuffixText();
			?>
		</div>
	</div>
	<?php	}
		$this->fields->next();
	}
	?>
	</div>

<?php if( $this->mtconf['use_map'] == 1 ) { ?>
	<h3 class="title"><?php echo JText::_( 'COM_MTREE_MAP' ); ?></h3>
	<div id="mapcon">
	<?php
	$width = '100%';
	$height = '200px';
	?>
	<script src="http://maps.googleapis.com/maps/api/js?v=3.6&amp;sensor=false" type="text/javascript"></script>
	<script type="text/javascript">
		var map = null;
	    	var geocoder = null;
		var marker = null;
		var infowindow = null;
		var defaultCountry = '<?php echo addslashes($this->mtconf['map_default_country']); ?>';
		var defaultState = '<?php echo addslashes($this->mtconf['map_default_state']); ?>';
		var defaultCity = '<?php echo addslashes($this->mtconf['map_default_city']); ?>';
		var defaultLat = '<?php echo addslashes($this->mtconf['map_default_lat']); ?>';
		var defaultLng = '<?php echo addslashes($this->mtconf['map_default_lng']); ?>';
		var defaultZoom = <?php echo addslashes($this->mtconf['map_default_zoom']); ?>;
		var linkValLat = '<?php echo $this->link->lat; ?>';
		var linkValLng = '<?php echo $this->link->lng; ?>';
		var linkValZoom = <?php echo ($this->link->zoom)?$this->link->zoom:$this->mtconf['map_default_zoom']; ?>;
	</script>
	<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_js']; ?>map.js"></script>
	<div style="padding:4px 0; width:95%">
		<button type="button" onclick="locateInMap()" name="locateButton" id="locateButton" class="btn btn-small"><?php echo JText::_( 'COM_MTREE_LOCATE_IN_MAP' ); ?></button>
		<span style="padding:0px; margin:3px" id="map-msg"></span>
	</div>
	<div id="map" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>"></div>
	<input type="hidden" name="lat" id="lat" value="<?php echo $this->link->lat; ?>" />
	<input type="hidden" name="lng" id="lng" value="<?php echo $this->link->lng; ?>" />
	<input type="hidden" name="zoom" id="zoom" value="<?php echo $this->link->zoom; ?>" />
	<input type="hidden" name="show_map" id="show_map" value="<?php echo $this->link->show_map; ?>" />
	</div>
	<a id="togglemap" href="#" onclick="javascript:toggleMap();return false;"><? echo JText::_('COM_MTREE_REMOVE_MAP'); ?></a>
<?php 
}

if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) { ?>
	<div id="imagescon">
		<h3 class="title"><?php echo JText::_( 'COM_MTREE_IMAGES' ) ?></h3>
		<span><small><?php echo JText::_( 'COM_MTREE_DRAG_TO_SORT_IMAGES_DESELECT_CHECKBOX_TO_REMOVE' ); ?></small></span>
		<ol id="sortableimages"><?php
		foreach( $this->images AS $image ) {
			echo '<li id="img_' . $image->img_id . '">';
			echo '<input type="checkbox" name="keep_img[]" value="' . $image->img_id . '" checked />';
			echo '<a href="' . $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_medium_image'] . $image->filename . '" target="_blank">';
			echo '<img border="0" style="position:relative;border:1px solid black;" align="middle" src="' . $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_small_image'] . $image->filename . '" alt="' . $image->filename . '" />';
			echo '</a>';
			echo '</li>';
		}
		?>
		</ol>
		<ol id="uploadimages">
		</ol>
		<div class="actionimages">
			<a href="javascript:addAtt();" id="add_att"><?php if(count($this->images) < $this->mtconf['images_per_listing']) { ?><?php echo JText::_( 'COM_MTREE_ADD_AN_IMAGE' ) ?><?php } ?></a>
			<?php if($this->mtconf['image_required']) { ?>
			<br /><small><?php echo JText::_( 'COM_MTREE_IMAGE_REQUIRED' )?></small>
			<?php } ?>
			<?php if( $this->image_size_limit > 0 ) { ?>
			<br /><small><?php echo sprintf( JText::_( 'COM_MTREE_LIMIT_OF_X_PER_IMAGE' ), $this->image_size_limit )?></small>
			<?php } ?>
			<?php if( $this->mtconf['image_min_width'] > 0 && $this->mtconf['image_min_height'] > 0 ) { ?>
			<br /><small><?php echo sprintf( JText::sprintf( 'COM_MTREE_MINIMUM_IMAGE_DIMENSION_W_BY_H', $this->mtconf['image_min_width'], $this->mtconf['image_min_height']), $this->image_size_limit )?></small>
			<?php } ?>
		
		</div>
	</div>
	
	<input type="hidden" name="img_sort_hash" value="" />
<?php } ?>
	<br />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="savelisting" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />			
	<?php if ( $this->link->link_id == 0 ) { ?>
	<input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" />
	<?php } else { ?>
	<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" />
	<?php } ?>
	<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($this->other_cats) ) ? implode(', ', $this->other_cats) : '' ) ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	
	<div class="control-group">
		<div class="controls">
			<button type="button" onclick="javascript:submitbutton('savelisting')" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SUBMIT_LISTING' ) ?></button>
			<button type="button" onclick="history.back();" class="btn"><?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
		</div>
	</div>
</form>