<?php
defined('_JEXEC') or die;
?>
<?php if (count($plugins)): ?>
<div id="jcomments-slogin-buttons" class="jcomments-slogin-buttons">
	<?php if ($this->params->get('pretext')): ?>
	<div class="pretext">
		<p><?php echo $this->params->get('pretext'); ?></p>
	</div>
	<?php endif; ?>
	<?php foreach($plugins as $plugin): ?>
	<a href="<?php echo JRoute::_($plugin['link']);?>"><span class="<?php echo $plugin['class'];?>">&nbsp;</span></a>
	<?php endforeach; ?>
</div>
<div class="slogin-clear"></div>
<?php endif; ?>