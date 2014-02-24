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

?>
<div id="sh404sef-popup" class="sh404sef-popup">

<div class="shmodal-toolbar row-fluid" id="shmodal-toolbar">
<div class="alert alert-success shmodal-toolbar-wrapper">
<div class="shmodal-toolbar-text">
<?php
$title = ShlHtmlBs_Helper::label($this->escape( Sh404sefHelperHtml::abridge($this->url->oldurl, 'editurl')), 'info', $dismiss = false, 'label-large');

echo JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_FOR') . ' ' . $title;
?>
</div>
<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
	<button class="btn btn-primary" type="button" onclick="Joomla.submitform('save', document.adminForm);">
		<i class="icon-apply icon-white"> </i>
				<?php echo JText::_('JSAVE'); ?>
	</button>
	<button class="btn" type="button"  onclick="<?php echo JRequest::getBool('refresh', 0)
	? 'window.parent.location.href=window.parent.location.href;' : '';
																  ?>  window.parent.shlBootstrap.closeModal();">
				<?php echo JText::_('JCANCEL'); ?>
	</button>
</div>
</div>
</div>

<div class="shmodal-content" id="shmodal-content">

<?php
echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <div id="editurl-container" class="row-fluid">

  <?php
  	 $data = new stdClass();
  	 $data->label = JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_LABEL');
  	 $data->input = '<input class="text_area" type="text" name="newurl" id="newurl" size="120" value="' . $this->escape($this->url->get('newurl')) . '" />';
  	 $data->tip = JText::_( 'COM_SH404SEF_TT_ENTER_REDIRECT');
  	 $data->name = "newurl";
     echo ShlMvcLayout_Helper::render('com_sh404sef.form.fields.custom', $data);
   ?>

	<div>
    <input type="hidden" name="id" value="<?php echo $this->url->get('id'); ?>" />
    <input type="hidden" name="c" value="editnotfound" />
    <input type="hidden" name="view" value="editnotfound" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="shajax" value="1" />
    <input type="hidden" name="tmpl" value="component" />
    <?php echo JHTML::_( 'form.token' ); ?>
    </div>
  </div>
</form>
</div>
</div>