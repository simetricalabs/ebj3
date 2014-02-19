 

<div id="listing">

<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("delete"=>false, "edit"=>false) ) ?></h2>

<b><?php echo JText::_( 'COM_MTREE_CONFIRM_DELETE' ) ?></b>
<p />

<center>
<form action="<?php echo JRoute::_("index.php") ?>" method="post">
<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'COM_MTREE_DELETE' ) ?>" /> <input type="button" value="<?php echo JText::_( 'COM_MTREE_CANCEL' ) ?>" onclick="history.back();" class="button" />
<input type="hidden" name="option" value="com_mtree" />
<input type="hidden" name="task" value="confirmdelete" />
<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</center>

</div>