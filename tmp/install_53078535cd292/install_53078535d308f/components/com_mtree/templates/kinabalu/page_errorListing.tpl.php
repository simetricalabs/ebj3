 
<div id="listing">
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("edit"=>false, "delete"=>false, "pending_approval"=>false) ) ?></h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane">
	<tr>
		<td>
		<br />
		<center><strong><?php echo $this->error_msg ?></strong></center>
		<p />
		<center><?php $this->plugin('ahref', "index.php?option=com_mtree&task=viewlink&link_id=$this->link_id&Itemid=$this->Itemid", JText::_( 'COM_MTREE_BACK_TO_LISTING' )); ?></center>
		</td>
	</tr>
</table>
</div>