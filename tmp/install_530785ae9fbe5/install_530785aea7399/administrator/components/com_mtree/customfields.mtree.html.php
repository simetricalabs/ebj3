<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class HTML_mtcustomfields {
	
	public static function managefieldtypes( $option, $rows ) {
		global $mtconf;
	?>

	<table class="table table-striped">
	<thead>
	<tr>
		<th width="25%" class="title"><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?></th>
		<th width="35%" class="title"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></th>
		<th width="5%" align="center"><?php echo JText::_( 'COM_MTREE_VERSION' ) ?></th>
		<th width="20%" align="left"><?php echo JText::_( 'COM_MTREE_WEBSITE' ) ?></th>
	</tr>
	</thead>
	<?php
	if(count($rows) > 0) {
		$rc = 0;
		$i=0;
		foreach($rows AS $row) {
			?>
		<tr class="<?php echo "row$rc"; ?>">
			<td valign="top">
			<?php echo $row->ft_caption; ?></td>
			<td><?php 
			if($row->iscore) {
				echo '<b>' . JText::_( 'COM_MTREE_CORE_FIELDTYPE' ) . '</b>';	
			} else {
				echo $row->ft_desc;
			}
			?></td>
			<td><?php echo $row->ft_version; ?></td>
			<td><a href="<?php echo $row->ft_website; ?>" target="_blank"><?php echo $row->ft_website; ?></a></td>
		</tr>
			<?php 
			$rc = $rc == 0 ? 1 : 0;
			$i++;
		} 
	} else {
		echo '<tr><td colspan="5">No custom field type installed.</td></tr>';
	}
	?>
	<tfoot>
	<tr><th colspan="5"></th></tr>
	<tfoot>
	</table>
	<?php
	}
	
	public static function customfields( $custom_fields, $pageNav, $option ) {
		global $mtconf;
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr><td>
			<a href="index.php?option=com_mtree&amp;task=managefieldtypes"><?php echo JText::_( 'COM_MTREE_VIEW_INSTALLED_FIELD_TYPES' ) ?></a>
		</td></tr>
	</table>

	<table class="table table-striped">
		<thead>
		<th width="1%"><?php echo JText::_( 'COM_MTREE_ID' ) ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
		<th width="40%" align="left" nowrap><?php echo JText::_( 'COM_MTREE_CAPTION' ) ?></th>
		<th width="20%" align="left"><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?></th>
		<th width="50" align="center" nowrap class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCHABLE' ) ?></th>
		<th width="50" align="center" nowrap class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?></th>
		<th width="50" align="center" nowrap class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_REQUIRED' ) ?></th>

		<th width="50" align="center" nowrap class="hidden-phone"><?php echo JText::_( 'COM_MTREE_SUMMARY_VIEW' ) ?></th>
		<th width="50" align="center" nowrap class="hidden-phone"><?php echo JText::_( 'COM_MTREE_DETAILS_VIEW' ) ?></th>

		<th width="10%" align="center" nowrap><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
		<th width="4%" align="center" nowrap colspan="2" class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_ORDERING' ) ?></th>
		</thead>
	
		<?php
		$k = 0;
		for ($i=0, $n=count( $custom_fields ); $i < $n; $i++) {
			$row = &$custom_fields[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $row->cf_id;?></td>
			<td>
				<input type="checkbox" id="cb<?php echo $i;?>" name="cfid[]" value="<?php echo $row->cf_id; ?>" onClick="Joomla.isChecked(this.checked);" />
			</td>
			<td align="left">
				<a href="index.php?option=com_mtree&amp;task=editcf&amp;cfid=<?php echo $row->cf_id; ?>"><?php 
					if ( strlen($row->caption) > 55 ) {
						echo strip_tags(substr($row->caption, 0, 55))."...";
					} else {
						echo strip_tags($row->caption);
					}
				?></a>
			</td>
			<td><?php 
				if($row->iscore) {
					echo '<b>' . strtoupper(JText::_( 'COM_MTREE_CORE' )) . '</b>';
				} else { 
					if( is_null($row->ft_caption) ) {
						echo JText::_( 'COM_MTREE_FIELD_TYPE_' . strtoupper($row->field_type) );
					} else {
						echo $row->ft_caption;
					}
				} ?></td>
			<?php if ($row->hidden) { 
				?>
				<td align="center" colspan="5"><strong><?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?></strong></td>
				<?php
			} else { ?>
			<td align="center" class="hidden-tablet hidden-phone">
				<?php echo JHtml::_('jgrid.published', $row->advanced_search, $i, '', false); ?>
			</td>
			<td align="center" class="hidden-tablet hidden-phone">
				<?php echo JHtml::_('jgrid.published', $row->simple_search, $i, '', false); ?>
			</td>
			<td align="center" class="hidden-tablet hidden-phone">
				<?php echo JHtml::_('jgrid.published', $row->required_field, $i, '', false); ?>
			</td>
			<td align="center" class="hidden-phone">
				<?php echo JHtml::_('jgrid.published', $row->summary_view, $i, '', false); ?>
			</td>
			<td align="center" class="hidden-phone">
				<?php echo JHtml::_('jgrid.published', $row->details_view, $i, '', false); ?>
			</td>
			<?php
			
			}
			
				$task = $row->published ? 'cf_unpublish' : 'cf_publish';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png';
			?>
			<td align="center">
				<?php if ($row->field_type <> 'corename') { ?>
				<?php echo JHtml::_('jgrid.published', $row->published, $i, 'cf_', true); ?>
				<?php } else {echo JHtml::_('jgrid.published', $row->published, $i, '', false);} ?>
			</td>
			<td class="order hidden-tablet hidden-phone">
				<span><?php echo $pageNav->orderUpIcon( $i, true, 'cf_orderup' ); ?></span>
			</td>
			<td class="order hidden-tablet hidden-phone">
				<span><?php echo $pageNav->orderDownIcon( $i, $n, true, 'cf_orderdown'  ); ?></span>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="12">
					<div class="pull-right"><?php echo $pageNav->getLimitBox(); ?></div>
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="customfields" />
	<input type="hidden" name="boxchecked" value="0">
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}

	public static function editcf( $row, $custom_cf_types, $lists, $isDisabled, $cats, $fields_map_cats, $form, $option ) {
		JHtml::_('behavior.tooltip');
	?>
	<script language="javascript">
	<!--
	Joomla.submitbutton = function(task) {
		var form = document.getElementById('adminForm');
		if(task=='cancelcf') {
			Joomla.submitform(task, form);
			return;
		}
		if (form.caption.value == "") {
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FIELDS_CAPTION' ) ?>" );
		} else if (form.iscore.value == "0" && ( form.field_type.value == "checkbox" || form.field_type.value == "selectlist" || form.field_type.value == "selectmultiple" || form.field_type.value == "radiobutton" ) && form.field_elements.value == "" ) {
			alert( "Please fill in the Field Elements." );
		} else {
			Joomla.submitform(task, form);
		}
	}
	function updateInputs(ftype) {
		var f = document.adminForm;

		// No search
		if(ftype=='associatedlisting') {
			jQuery(f.simple_search_fieldset).addClass('disabled');
			jQuery(f.simple_search).addClass('disabled').prop('disabled', true);

			jQuery(f.advanced_search_fieldset).addClass('disabled');
			jQuery(f.advanced_search).addClass('disabled').prop('disabled', true);

			jQuery(f.filter_search_fieldset).addClass('disabled');
			jQuery(f.filter_search).addClass('disabled').prop('disabled', true);
		} else {
			jQuery(f.simple_search_fieldset).removeClass('disabled');
			jQuery(f.simple_search).removeClass('disabled').removeProp('disabled');

			jQuery(f.advanced_search_fieldset).removeClass('disabled');
			jQuery(f.advanced_search).removeClass('disabled').removeProp('disabled');

			jQuery(f.filter_search_fieldset).removeClass('disabled');
			jQuery(f.filter_search).removeClass('disabled').removeProp('disabled');
		}

		if (ftype=='selectlist'||ftype=='selectmultiple'||ftype=='checkbox'||ftype=='radiobutton'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->use_elements) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.field_elements.disabled=false;
			f.field_elements.style.backgroundColor='#FFFFFF'; 
		} else {
			f.field_elements.style.backgroundColor='#f5f5f5'; 
			f.field_elements.disabled=true;
		}

		if (ftype=='selectlist'||ftype=='selectmultiple'||ftype=='checkbox'||ftype=='radiobutton'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'||ftype=='corepostcode'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->taggable) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			jQuery(f.tag_search_fieldset).removeClass('disabled');
			jQuery(f.tag_search).removeClass('disabled');
			jQuery(f.tag_search).removeProp('disabled');
		} else {
			jQuery(f.tag_search_fieldset).addClass('disabled');
			jQuery(f.tag_search).addClass('disabled');
			jQuery(f.tag_search).prop('disabled', true);
		}
		
		if(ftype=='checkbox'||ftype =='radiobutton'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if(!$custom_cf_type->use_size) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.size.disabled=true;
		} else {
			f.size.disabled=false;
		}
		
		if (ftype=='coreprice'||ftype=='coreaddress'||ftype=='coreaddress'||ftype=='corepostcode'||ftype=='coretelephone'||ftype=='corefax'||ftype=='coreemail'||ftype=='corewebsite'||ftype=='corename'||ftype=='coredesc'||ftype=='coremetakey'||ftype==''||ftype=='coremetadesc'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->use_placeholder) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.placeholder_text.disabled=false;
			f.placeholder_text.style.backgroundColor='#FFFFFF'; 
		} else {
			f.placeholder_text.style.backgroundColor='#f5f5f5'; 
			f.placeholder_text.disabled=true;
		}
		
		<?php
		$tmp_fields = array();
		foreach( $custom_cf_types AS $custom_cf_type )
		{
			if($custom_cf_type->is_file)
			{	
				$tmp_fields[] = 'ftype==\'' . $custom_cf_type->field_type . '\'';
			}
		}
		if( !empty($tmp_fields) )
		{
			echo 'if(ftype==\'corecreated\'||ftype==\'coremodified\'||ftype==\'corefeatured\'||ftype==\'corerating\'||ftype==\'corevotes\'||ftype==\'corehits\'||'.implode('||',$tmp_fields).')';
		}
		?>{
			f.default_value.style.backgroundColor='#f5f5f5'; 
			f.default_value.disabled=true;
		} else {
			f.default_value.disabled=false;
			f.default_value.style.backgroundColor='#FFFFFF'; 
		}
	}
	-->
	</script>
	<style type="text/css">
	table.paramlist td {
		background-color:#F6F6F6;
		border-bottom:1px solid #E9E9E9;
		padding:5px 3px;
	}
	</style>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php
	$fieldsets = array();
	if( $form )
	{
		$fieldsets = $form->getFieldsets();
	}
	?>
	<div class="row-fluid form-horizontal">
	<div class="span12">

	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MTREE_CUSTOM_FIELD', true)); ?>

	<div class="row-fluid">
		<div class="span<?php echo (intval($row->cf_id)>0 && !empty($fieldsets))?'7':'12'; ?>">
			<fieldset class="form-horizontal">
			<legend><?php echo JText::_( 'COM_MTREE_BASIC_SETTINGS' ) ?></legend>
			<?php if( !$row->iscore && $row->cf_id == 0 ) { ?>
			<blockquote>
				<hr>
					<?php echo JText::_( 'COM_MTREE_SOME_FIELDTYPE_HAS_PARAMS_DESC' ); ?>
				<hr>
			</blockquote>
			<?php } ?>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?>:</label>
				</div>
				<div class="controls">
				<?php
				if( $row->iscore ) { 
					echo '<b>' . JText::_( 'COM_MTREE_CORE_FIELD' ) . '</b>';
					echo '<input type="hidden" name="field_type" value="' . $row->field_type. '" />';
				} else { 
					echo $lists['field_types']; 
				}
				echo '<input type="hidden" name="iscore" value="' . $row->iscore . '" />';
				?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_CAPTION' ) ?>:</label>
				</div>
				<div class="controls form-inline">
					<input type="text" size="40" name="caption" value="<?php echo htmlspecialchars($row->caption) ?>" />
					<input type="checkbox" name="hide_caption" id="hide_caption" value="1"<?php echo ($row->hide_caption) ? ' checked' : '' ?> /> <label for="hide_caption"><?php echo JText::_( 'COM_MTREE_HIDE_CAPTION' ) ?></label>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_FIELD_ELEMENTS' ) ?>:</label>
				</div>
				<div class="controls">
				<textarea name="field_elements" rows="8" cols="50" style="width:auto;"><?php echo $row->field_elements ?></textarea>
				<br /><?php echo JText::_( 'COM_MTREE_FIELD_ELEMENTS_NOTE' ) ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label style="max-width:100%"><?php echo JText::_( 'COM_MTREE_PREFIX_AND_SUFFIX_TEXT_TO_DISPLAY_DURING_FIELD_MODIFICATION' ) ?>:</label>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label>&nbsp;</label>
				</div>
				<div class="controls">
					<?php echo JText::_( 'COM_MTREE_PREFIX' ) ?>
					<input type="text" size="40" name="prefix_text_mod" value="<?php echo htmlspecialchars($row->prefix_text_mod) ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label>&nbsp;</label>
				</div>
				<div class="controls">
					<?php echo JText::_( 'COM_MTREE_SUFFIX' ) ?>
					<input type="text" size="40" name="suffix_text_mod" value="<?php echo htmlspecialchars($row->suffix_text_mod) ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label style="max-width:100%"><?php echo JText::_( 'COM_MTREE_PREFIX_AND_SUFFIX_TEXT_TO_DISPLAY_DURING_DISPLAY' ) ?>:</label>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label>&nbsp;</label>
				</div>
				<div class="controls">
					<?php echo JText::_( 'COM_MTREE_PREFIX' ) ?>
					<input type="text" size="40" name="prefix_text_display" value="<?php echo htmlspecialchars($row->prefix_text_display) ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label>&nbsp;</label>
				</div>
				<div class="controls">
					<?php echo JText::_( 'COM_MTREE_SUFFIX' ) ?>
					<input type="text" size="40" name="suffix_text_display" value="<?php echo htmlspecialchars($row->suffix_text_display) ?>" /></li>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_SIZE' ) ?>:</label>
				</div>
				<div class="controls">
					<input type="text" size="40" name="size" value="<?php echo $row->size ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_ALIAS' ) ?>:</label>
				</div>
				<div class="controls">
					<input type="text" size="40" name="alias" value="<?php echo $row->alias ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_PLACEHOLDER_TEXT' ) ?>:</label>
				</div>
				<div class="controls">
					<input type="text" size="40" name="placeholder_text" value="<?php echo htmlspecialchars($row->placeholder_text) ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_DEFAULT_CUSTOM_FIELD_VALUE' ) ?>:</label>
				</div>
				<div class="controls">
					<input type="text" size="40" name="default_value" value="<?php echo htmlspecialchars($row->default_value) ?>" />
				</div>
			</div>
	
	
			<?php if ($row->field_type <> 'corename') { ?>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('published', $row->published, in_array('published', $isDisabled)); ?>
				</div>
			</div>
			<?php } else { ?><input type="hidden" name="published" value="1"><?php
			} 
			?>
			<?php /* ?>
			<div id="config_user_allowmodify" class="control-group default">
				<div class="control-label">
					<label><input type="checkbox" name="override[user_allowmodify]" value="1" class="override" onclick="">Allow owner to modify listing</label>
				</div>
				<div class="controls">
					<fieldset class="radio btn-group" id="config_user_allowmodify_fieldset">
						<input type="radio" checked="checked" value="1" name="config[user_allowmodify]" id="config_user_allowmodify1" disabled="" class="disabled">
						<label for="config_user_allowmodify1" class="btn active btn-success disabled">Yes</label>
						<input type="radio" value="0" name="config[user_allowmodify]" id="config_user_allowmodify0" disabled="" class="disabled">
						<label for="config_user_allowmodify0" class="btn disabled">No</label>
					</fieldset>
				</div>
			</div>
			<?php */ ?>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_SHOWN_IN_DETAILS_VIEW' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('details_view', $row->details_view, in_array('details_view', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_SHOWN_IN_SUMMARY_VIEW' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('summary_view', $row->summary_view, in_array('summary_view', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label title="<?php echo JText::_( 'COM_MTREE_TAGGABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_TAGGABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_TAGGABLE' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('tag_search', $row->tag_search, in_array('tag_search', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label title="<?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('simple_search', $row->simple_search, in_array('simple_search', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCHABLE' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('advanced_search', $row->advanced_search, in_array('advanced_search', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label title="<?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('filter_search', $row->filter_search, in_array('filter_search', $isDisabled)); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_REQUIRED_FIELD' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('required_field', $row->required_field, in_array('required_field', $isDisabled)); ?>
				</div>
			</div>
			<?php if ($row->field_type <> 'corename') { ?>
			<div class="control-group">
				<div class="control-label">
					<label title="<?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?>::<?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?>:</label>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('hidden', $row->hidden, in_array('hidden', $isDisabled)); ?>
				</div>
			</div>
			<?php } else { ?><input type="hidden" name="hidden" value="0"><?php
			} 

			if($row->cf_id) { ?>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_( 'COM_MTREE_ORDERING' ) ?>:</label>
				</div>
				<div class="controls">
					<?php echo $lists['order'] ?>
				</div>
			</div>
			<?php } ?>
			</fieldset>
		</div>
		<?php 
		if( !empty($fieldsets) ):
		?>
		<div class="span5">
			<fieldset class="form-horizontal">
			<legend><?php echo JText::_( 'COM_MTREE_PARAMETERS' ) ?></legend>
			<?php $hidden_fields = ''; ?>
			<?php 

			foreach ($fieldsets as $fieldset): 
				foreach($form->getFieldset($fieldset->name) AS $field):
				?>
			<?php if ($field->hidden): ?>
				<?php $hidden_fields.= $field->input; ?>
			<?php else:?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label; ?>
				</div>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			</div>
		                    <?php endif;?>
		               <?php endforeach;?>
		     	<?php endforeach; ?>
			<?php echo $hidden_fields; ?>
			</fieldset>
		</div>
		<?php endif; ?>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'fields-assignment', JText::_('COM_MTREE_EDIT_FIELD_CATEGORIES_ASSIGNMENT', true)); ?>

		<?php echo JText::_( 'COM_MTREE_EDIT_FIELD_CATEGORIES_ASSIGNMENT_INSTRUCTIONS' ) ?>
		<p />
		<button type="button" id="jform_toggle" class="btn btn-small" onclick="$$('.chk-menulink').each(function(el) { el.checked = !el.checked; });">
			<?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
		</button>
		
		<ul class="menu-links form-inline">
			<li class="menu-link">
			<?php 
				echo '<input type="checkbox" id="category-0" name="fields_map_cats[]"' 
					.	' value="0" class="chk-menulink"'
					.	(in_array(0,$fields_map_cats) ? ' checked="checked"' : '').'/>';
				echo '<label for="category-0">'.JText::_( 'COM_MTREE_ROOT' ).'</label>';
			?>
			</li>
			<?php
			foreach( $cats AS $cat )
			{
				$checked = (in_array($cat->cat_id,$fields_map_cats) ? ' checked="checked"' : '');
				echo '<li class="menu-link" style="clear:both">';
				echo '<input type="checkbox" id="category-'.$cat->cat_id.'" name="fields_map_cats[]"' .
						' value="'.$cat->cat_id.'" class="chk-menulink"'
						.$checked.'/>';
				echo '<label for="category-'.$cat->cat_id.'"> — '.$cat->cat_name.'</label>';
				echo '</li>';
			}
		
			?>
		</ul>

	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>
	

	</div>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<input type="hidden" name="cf_id" value="<?php echo $row->cf_id; ?>">
	<input type="hidden" name="task" value="save_customfields" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<script language="javascript"><!--
	updateInputs(document.adminForm.field_type.value);
	--></script>
	<?php
	}
}
?>