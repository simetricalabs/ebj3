<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
echo JHtml::stylesheet('mod_mt_filter/mod_mt_filter.css',array(),true, false);
?>
<div class="search<?php echo $moduleclass_sfx; ?>">
<form action="<?php echo JRoute::_("index.php") ?>" method="get" name="modMtFilterForm<?php echo $cat_id; ?>" id="modMtFilterForm<?php echo $cat_id; ?>">
	
	<ul id="modMtFilter<?php echo $cat_id; ?>" class="modMtFilter"<?php echo (!$hasSearchParams)?' style="display:none"':''; ?>>
	<?php
	$filter_fields->resetPointer();
	while( $filter_fields->hasNext() )
	{
		$filter_field = $filter_fields->getField();
		if($filter_field->hasFilterField())
		{
			echo '<li id="modFilterField_'.$filter_field->getId().'" class="'.$filter_field->getFieldTypeClassName().'">';
			echo '<label>' . $filter_field->caption . '</label>';
			echo '<span class="filterinput">';
			echo $filter_field->getFilterHTML();
			echo '</span>';
			echo '</li>';
		}
		$filter_fields->next();
	}
	
	if ( $filter_button ) { ?>
		<li class="button-send"><button type="submit" class="btn" onclick="javascript:var cookie = document.cookie.split(';');for(var i=0;i < cookie.length;i++) {var c = cookie[i];while (c.charAt(0)==' '){c = c.substring(1,c.length);}var name = c.split('=')[0];if( name.substr(0,35) == 'com_mtree_mfields_searchFieldValue_'){document.cookie = name + '=;';}}"><?php echo JText::_( 'MOD_MT_FILTER_FILTER' ) ?></button></li>
	<?php }
	
	if ( $reset_button ) { ?>
		<li class="button-reset"><button type="button" class="btn" onclick="javascript:var form=jQuery('form[name=modMtFilterForm<?php echo $cat_id; ?>] input,form[name=modMtFilterForm<?php echo $cat_id; ?>] select');form.each(function(index,el) {if(el.type=='checkbox'||el.type=='radio'){el.checked=false;} if(el.type=='text'){el.value='';}if(el.type=='select-one'||el.type=='select-multiple'){el.selectedIndex='';}});jQuery('form[name=modMtFilterForm<?php echo $cat_id; ?>]').trigger('submit');var cookie = document.cookie.split(';');for(var i=0;i < cookie.length;i++) {var c = cookie[i];while (c.charAt(0)==' '){c = c.substring(1,c.length);}var name = c.split('=')[0];if( name.substr(0,35) == 'com_mtree_mfields_searchFieldValue_'){document.cookie = name + '=;';}}"><?php echo JText::_( 'MOD_MT_FILTER_RESET' ) ?></button></li>
	<?php } ?>

	</ul>
	
	<input type="hidden" name="option" value="com_mtree" />
	<input type="hidden" name="task" value="listall" />
	<input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $intItemid ?>" />
</form>
</div>