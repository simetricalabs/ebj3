 

<div id="listing">

<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("delete"=>false, "edit"=>false) ) ?></h2>

<b><?php echo JText::_( 'COM_MTREE_CONFIRM_DELETE' ) ?></b>
<p />

<form action="<?php echo JRoute::_("index.php") ?>" method="post">

<div class="center">
	<button type="submit" name="Submit" class="btn btn-danger"><?php echo JText::_( 'COM_MTREE_DELETE' ) ?></button>
	<button type="button" onclick="history.back();" class="btn"><?php echo JText::_( 'COM_MTREE_CANCEL' ) ?></button>
</div>

<input type="hidden" name="option" value="com_mtree" />
<input type="hidden" name="task" value="confirmdelete" />
<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>

</div>