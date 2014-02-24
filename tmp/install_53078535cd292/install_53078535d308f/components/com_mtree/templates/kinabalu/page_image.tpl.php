 
<div id="listing">
	<h2><?php 
	$link_name = $this->fields->getFieldById(1);
	$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false,"delete"=>false) ) ?></h2>

<div class="next-previous-image"><?php
echo '<div class="previous-image">';
if($this->image->ordering > 1) {
	echo '<a href="'. JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $this->images[$this->image->ordering -2]->img_id . '&Itemid=' . $this->Itemid ) . '">';
	echo JText::_( 'COM_MTREE_PREVIOUS_IMAGE' );
	echo '</a>';
}
echo '</div>';

echo '<div class="next-image">';
if(count($this->images) > $this->image->ordering) {
	echo '<a href="'. JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $this->images[$this->image->ordering]->img_id . '&Itemid=' . $this->Itemid ) . '">';
	echo JText::_( 'COM_MTREE_NEXT_IMAGE' );
	echo '</a>';
}
echo '</div>';
echo '</div>';

echo '<div class="medium-image">';
echo $this->plugin( 'mt_image', $this->image->filename, '2' ); 
echo '</div>';

echo '<div class="next-previous-image">';
echo '<div class="previous-image">';
if($this->image->ordering > 1) {
	echo '<a href="'. JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $this->images[$this->image->ordering -2]->img_id . '&Itemid=' . $this->Itemid ) . '">';
	echo JText::_( 'COM_MTREE_PREVIOUS_IMAGE' );
	echo '</a>';
}
echo '</div>';

echo '<div class="next-image">';
if(count($this->images) > $this->image->ordering) {
	echo '<div class="next-image"><a href="'. JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $this->images[$this->image->ordering]->img_id . '&Itemid=' . $this->Itemid ) . '">';
	echo JText::_( 'COM_MTREE_NEXT_IMAGE' );
	echo '</a>';
	echo '</div>';
}
echo '</div>';
echo '</div>';
?>
<br clear="all" /><p />
<center><?php
	if(count($this->images) == 1) {
		echo '<a href="' . JRoute::_('index.php?option=com_mtree&task=viewlink&link_id=' . $this->link_id . '&Itemid=' . $this->Itemid ) . '">' . JText::_( 'COM_MTREE_BACK_TO_LISTING' ) . '</a>';
	} else {
		echo '<a href="' . JRoute::_('index.php?option=com_mtree&task=viewgallery&link_id=' . $this->link_id . '&Itemid=' . $this->Itemid ) . '">' . JText::_( 'COM_MTREE_BACK_TO_GALLERY' ) . '</a>';
	}
?></center>
</div>