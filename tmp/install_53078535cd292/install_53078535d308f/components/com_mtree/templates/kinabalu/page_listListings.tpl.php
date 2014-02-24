<div id="top-listings" class="mt-template-<?php echo $this->template; ?> cat-id-<?php echo $this->cat->cat_id ;?> tlcat-id-<?php echo $this->tlcat_id ;?>">

<h2 class="contentheading"><?php echo $this->header ?>&nbsp;<?php echo $this->plugin('showrssfeed',$this->task); ?></h2>
<?php if( $this->task == 'listall'): ?>
<form action="<?php echo JRoute::_("index.php") ?>" method="get" name="mtFormAllListings" id="mtFormAllListings">
<span class="sort-by">
	<label for="sort"><?php echo JText::_( 'COM_MTREE_SORT_BY' );?></label>
	<?php echo $this->lists['sort']; ?>
</span>
<?php endif; ?>

<div id="listings">

<div class="pages-links">
	<span class="xlistings"><?php echo $this->pageNav->getResultsCounter(); ?></span>
	<?php // echo $this->pageNav->getPagesLinks(); ?>
	<?php if( in_array($this->task,array('listcats','listall')) && $this->mtconf['display_all_listings_link']): ?>
	<span class="category-scope">
		<?php
		if( $this->task == 'listcats' ) {
			echo '<strong>'.JTEXT::_( 'COM_MTREE_THIS_CATEGORY' ).'</strong>';
		} else {
			echo '<a href="';
			echo JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$this->cat_id);
			echo '">';
			echo JTEXT::_( 'COM_MTREE_THIS_CATEGORY' );
			echo '</a>';
		}
		echo ' · ';
		if( $this->task == 'listall' ) {
			echo '<strong>'.MTEXT::_( 'ALL_LISTINGS', $this->tlcat_id ).'</strong>';
		} else {
			echo '<a href="';
			echo JRoute::_('index.php?option=com_mtree&task=listall&cat_id='.$this->cat_id);
			echo '">';
			echo MText::_( 'ALL_LISTINGS', $this->tlcat_id );
			echo '</a>';
		}
		?>
	</span>
	<?php endif; ?>
</div>

<?php if( 
	$this->mtconf['display_filters']
	&&
	($this->hasSearchParams || $this->task == 'listall')
): ?>
<div class="filterbox">
<a href="#" onclick="javascript:jQuery('#comMtFilter<?php echo $this->cat_id; ?>').slideToggle('300'); return false;"><?php echo MText::_( 'FILTER_LISTINGS', $this->tlcat_id ); ?></a>
<ul id="comMtFilter<?php echo $this->cat_id; ?>" class="comMtFilter"<?php echo (!$this->hasSearchParams)?' style="display:none"':''; ?>><?php
$this->filter_fields->resetPointer();
while( $this->filter_fields->hasNext() )
{
	$filter_field = $this->filter_fields->getField();
	if($filter_field->hasFilterField())
	{
		echo '<li id="comFilterField_'.$filter_field->getId().'" class="'.$filter_field->getFieldTypeClassName().'">';
		echo '<label>' . $filter_field->caption . ':' . '</label>';
		echo '<span class="filterinput">';
		echo $filter_field->getFilterHTML();
		echo '</span>';
		echo '</li>';
	}
	$this->filter_fields->next();
}
?>
<li class="button-send"><button type="submit" class="btn" onclick="javascript:var cookie = document.cookie.split(';');for(var i=0;i < cookie.length;i++) {var c = cookie[i];while (c.charAt(0)==' '){c = c.substring(1,c.length);}var name = c.split('=')[0];if( name.substr(0,35) == 'com_mtree_mfields_searchFieldValue_'){document.cookie = name + '=;';}}"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button></li>
<li class="button-reset"><button class="btn" onclick="javascript:var form=jQuery('form[name=mtFormAllListings] input,form[name=mtFormAllListings] select');form.each(function(index,el){if(el.type=='checkbox'||el.type=='radio'){el.checked=false;}if(el.type=='text'){el.value='';}if(el.type=='select-one'||el.type=='select-multiple'){el.selectedIndex='';}});var cookie = document.cookie.split(';');for(var i=0;i < cookie.length;i++) {var c = cookie[i];while (c.charAt(0)==' '){c = c.substring(1,c.length);}var name = c.split('=')[0];if( name.substr(0,35) == 'com_mtree_mfields_searchFieldValue_'){document.cookie = name + '=;';}}jQuery('form[name=mtFormAllListings]').submit();"><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button></li>
</ul>
</div>
<?php else: 

$this->filter_fields->resetPointer();
while( $this->filter_fields->hasNext() )
{
	$filter_field = $this->filter_fields->getField();
	if($filter_field->hasFilterField() && $filter_field->hasSearchValue())
	{
		echo $filter_field->getHiddenHTML();
	}
	$this->filter_fields->next();
}

endif; ?>

<?php
	if( !empty($this->links) )
	{
		$i = 0;
		foreach ($this->links AS $link) {
			$i++;
			$link_fields = $this->links_fields[$link->link_id];
			include $this->loadTemplate('sub_listingSummary.tpl.php');
		}	
	}
	
	if( $this->pageNav->total > 0 ) { ?>
<div class="pagination">
	<p class="counter pull-right">
		<?php echo $this->pageNav->getPagesCounter(); ?>
	</p>
	<?php echo $this->pageNav->getPagesLinks(); ?>
</div>
	<?php }
?></div>

<?php if( $this->task == 'listall'): ?>
<input type="hidden" name="option" value="<?php echo $this->option ?>" />
<input type="hidden" name="task" value="listall" />
<input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
</form>
<?php endif; ?>
</div>