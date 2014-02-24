<div id="listing" class="reviews">

<h2><?php echo JText::sprintf('COM_MTREE_USER_REVIEW'); ?></h2>
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false,"delete"=>false) ) ?></h2>

<?php
$hide_title = true;
$hide_submitreview = true;
include $this->loadTemplate( 'sub_reviews.tpl.php' );	
?>

</div>