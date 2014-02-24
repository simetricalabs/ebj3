 
<div id="listing" class="gallery">
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false,"delete"=>false) ) ?></h2>
<?php
foreach($this->images AS $image) {

	echo '<div class="thumbnail" style="width:' . ($this->config->get('resize_small_listing_size') + 10) . 'px;height:' . ($this->config->get('resize_small_listing_size') + 10) . 'px">';
	echo '<a href="'. JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $image->img_id . '&Itemid=' . $this->Itemid ) . '">';
	echo $this->plugin( 'mt_image', $image->filename, '3' );
	echo '</a>';
	echo '</div>';
	
}
?><br clear="all" />
<center><a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewlink&link_id=' . $this->link_id . '&Itemid=' . $this->Itemid ) ?>"><?php echo JText::_( 'COM_MTREE_BACK_TO_LISTING' ) ?></a></center>

</div>