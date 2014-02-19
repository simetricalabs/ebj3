<div class="print-links">
	<a href="#" onclick="javascript:window.print(); return false" title="<?php echo JText::_( 'COM_MTREE_PRINT' ) ?>"><?php echo JText::_( 'COM_MTREE_PRINT' ) ?></a>&nbsp;|&nbsp;
	<a href="#" onclick="window.close(); return false" title="<?php echo JText::_( 'COM_MTREE_CLOSE_THIS_WINDOW' ) ?>"><?php echo JText::_( 'COM_MTREE_CLOSE_THIS_WINDOW' ) ?></a>
</div>

<div id="listing">
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false,"delete"=>false) ) ?></h2>

<?php 

if (!empty($this->images)) {
	echo '<div class="mainimage">';
	include $this->loadTemplate( 'sub_images.tpl.php' );
	echo '</div>';
}

if(!is_null($this->fields->getFieldById(2))) { 
	$link_desc = $this->fields->getFieldById(2);
	echo $link_desc->getOutput();
}
?>

<div class="fields">
<?php
$field_count = 0;
$this->fields->resetPointer();
while( $this->fields->hasNext() ) {
	$f = $this->fields->getField();
	$value = $f->getValue();
	if( ( (!$f->hasInputField() && !$f->isCore() && empty($value)) || !empty($value) ) && !in_array($f->getName(),array('link_name','link_desc','city','state','country','postcode')) ) {

		$this->fields->resetPointer();
		while( $this->fields->hasNext() ) {
			$field = $this->fields->getField();
			$value = $field->getValue();
			if( ( (!$field->hasInputField() && !$field->isCore() && empty($value)) || !empty($value) ) && !in_array($field->getName(),array('link_name','link_desc','city','state','country','postcode')) ) {
				echo '<div class="row0">';
				echo '<div class="fieldRow" style="width:100%">';
				if($field->id == 4) {
					echo '<div class="caption">' . $field->getCaption() . '</div>';
					echo '<div class="output">';
					echo $field->getOutput(); 
					if($field5 = $this->fields->getFieldById(5)) {
						echo ', ' . $field5->getValue();
					}
					if($field8 = $this->fields->getFieldById(8)) {
						echo ', ' . $field8->getValue();
					}
					if($field6 = $this->fields->getFieldById(6)) {
						echo ', ' . $field6->getValue();
					}
					if($field7 = $this->fields->getFieldById(7)) {
						echo ', ' . $field7->getValue();
					}
					echo '</div>';
				} else { 
					echo '<div class="caption">';
					if($field->hasCaption()) {
						echo $field->getCaption() . '';
					}
					echo '</div>';
					echo '<div class="output">';
					echo $field->getDisplayPrefixText(); 
					echo $field->getOutput(1);
					echo $field->getDisplaySuffixText(); 
					echo '</div>';
				}
				$field_count++;
				echo '</div>';
				echo '</div>';
			}
			$this->fields->next();
		}
		break;
	}
	$this->fields->next();
}
?></div>

</div>
<div class="print-links">
	<a href="#" onclick="javascript:window.print(); return false" title="<?php echo JText::_( 'COM_MTREE_PRINT' ) ?>"><?php echo JText::_( 'COM_MTREE_PRINT' ) ?></a>&nbsp;|&nbsp;
	<a href="#" onclick="window.close(); return false" title="<?php echo JText::_( 'COM_MTREE_CLOSE_THIS_WINDOW' ) ?>"><?php echo JText::_( 'COM_MTREE_CLOSE_THIS_WINDOW' ) ?></a>
</div>
