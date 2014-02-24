<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date		2014-01-23
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if (!empty($this->redirectTo))
{

	// render a refresh_parent layout
	/**
	 * This layout only insert javascript to close a modal windows
	 */
	$displayData = new stdClass();
	$displayData->refreshAfter = 0;
	$displayData->refreshTo = '"' . $this->redirectTo . '"';
	ShlMvcLayout_Helper::render('com_sh404sef.general.refresh_parent', $displayData);
	return;
}
if(strpos( $this->task, 'del') !== false) {
	$buttonLabel = 'JTOOLBAR_DELETE';
	$buttonClass = 'btn-danger';
	$icon = '<i class="icon-trash icon-white"> </i>';
	$textClass = 'error';
}
else
{
	$buttonLabel = 'COM_SH404SEF_OK';
	$buttonClass = 'btn-primary';
	$icon = '';
	$textClass = 'info';
}

?>
<div class="sh404sef-popup" id="sh404sef-popup">

<?php
echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <div class="row-fluid">

  <div>
  <?php

    if (!empty( $this->mainText)) {
      echo ShlHtmlBs_Helper::alert($this->mainText, $textClass, $dismiss = false);
    }
  ?>
  </div>

  <div class="shmodal-toolbar-buttons ">
  	<button class="btn shl-left-separator" type="button"  onclick="window.parent.shlBootstrap.closeModal();">
		<?php echo JText::_('JCANCEL'); ?>
	</button>
	<button class="btn btn-large shl-left-separator <?php echo $buttonClass; ?>" type="button" onclick="Joomla.submitform('<?php echo $this->task; ?>', this.form);">
		<?php echo $icon; ?>
		<?php echo JText::_($buttonLabel); ?>
	</button>

  <input type="hidden" name="c" value="<?php echo $this->actionController; ?>" />
  <input type="hidden" name="option" value="com_sh404sef" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="tmpl" value="component" />
    <?php
    // optional elements to pass to the action controller if action is confirmed
    if (!empty( $this->cid)) {
      foreach( $this->cid as $cid) {
        echo '  <input type="hidden" name="cid[]" value="' . intval($cid) . '" />' . "\n";
      }
    }

    // option hidden text as provided by the calling controller
    if (!empty( $this->hiddenText)) {
      echo $this->hiddenText;
    }
    ?>

    <?php echo JHTML::_( 'form.token' ); ?>
  </div>
    </div>
</form>
</div>
