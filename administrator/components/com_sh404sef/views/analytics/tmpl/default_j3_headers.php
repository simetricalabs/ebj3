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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

$alertType = empty($this->analytics->status) ? 'alert-warning' : 'alert-success';
if($this->options['showFilters'] == 'yes') : ?>
	<h4 class="page-header">&nbsp;</h4>
<?php endif; ?>

<div class="alert <?php echo $alertType; ?>">
<div class="row-fluid">
    <?php
		$allFilters = $this->options['showFilters'] == 'yes';
		echo '<a href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1' . ($allFilters ? '' : ',showFilters:\'no\'') . '});" > '
			. ShlHtmlBs_Helper::button(JText::_('COM_SH404SEF_CHECK_ANALYTICS'), 'primary') . '</a>';
	?>
</div>
<div class="row-fluid">
	<?php
		echo '  ' . (empty($this->analytics->status) ? JText::_('COM_SH404SEF_ERROR_CHECKING_ANALYTICS') : $this->escape($this->analytics->statusMessage));
	?>
</div>

<div class="row-fluid">
    <?php
		if (!empty($this->analytics->status)) :
			echo $this->loadTemplate($this->joomlaVersionPrefix . '_filters');
		endif;
	?>
</div>
</div>
