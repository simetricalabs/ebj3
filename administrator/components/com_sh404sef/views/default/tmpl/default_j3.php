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

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

?>
<div class="row-fluid">

<div class="shl-secondary-toolbar">
<?php

// kill the toolbar button displayed by Joomla
// doesnt't work at low width (J! 3.0.3)
$script = '
 				jQuery(document).ready(
 					function() {
 						var hide = function() {
 							jQuery(".btn-subhead:visible").css({display:"none"})
 						}
						jQuery(window).resize(hide);
 						hide();
 					}
 				);
 				';
JFactory::getDocument()->addScriptDeclaration($script);

// instead we directly display the toolbar
echo JToolBar::getInstance('toolbar')->render();

?>
</div>

<div id="shl-sidebar-container" class="span2 shl-no-margin">
	<?php echo $this->sidebar; ?>
</div>

<div id="left" class="span6">

  	  <?php
// prepare Analytics output
$sefConfig = Sh404sefFactory::getConfig();
$analyticsAvailable = $sefConfig->analyticsReportsEnabled && !empty($sefConfig->analyticsUser) && !empty($sefConfig->analyticsPassword);
		?>
      <ul class="nav nav-tabs" id="left-pane">
         <li <?php echo $analyticsAvailable ? '' : 'class="active"'; ?>><a data-toggle="tab" href="#management"><?php echo JText::_(
	'COM_SH404SEF_START'); ?></a></li>
         <li <?php echo !$analyticsAvailable ? '' : 'class="active"'; ?>><a data-toggle="tab" href="#analytics"><?php echo JText::_(
	'COM_SH404SEF_ANALYTICS'); ?></a></li>
      </ul>
	  <?php
// start pane
echo JHtml::_('bootstrap.startPane', 'left-pane', array('active' => $analyticsAvailable ? 'analytics' : 'management'));

// management icons
echo JHtml::_('bootstrap.addPanel', 'left-pane', 'management');
	  ?>
    	<div class="hero-unit">
			<?php echo JText::_('COM_SH404SEF_QCONTROL'); ?>
		</div>
	<?php
echo JHtml::_('bootstrap.endPanel');

if ($sefConfig->analyticsReportsEnabled)
{
	// analytics panel
	echo JHtml::_('bootstrap.addPanel', 'left-pane', 'analytics');
	echo ShlMvcLayout_Helper::render(
		'com_sh404sef.analytics.' . $this->joomlaVersionPrefix . '_controlpanel_' . Sh404sefConfigurationEdition::$id);
	echo JHtml::_('bootstrap.endPanel');
}

echo JHtml::_('bootstrap.endPane');

// prepare data for control panel tabs
$infoTabTitle = $this->updates->shouldUpdate ? ShlHtmlBs_Helper::label(JText::_('COM_SH404SEF_VERSION_INFO'), 'important')
	: JText::_('COM_SH404SEF_VERSION_INFO');
// configuration and global stats
$output = '';
foreach ($this->sefConfig->fileAccessStatus as $file => $access)
{
	if ($access == JText::_('COM_SH404SEF_UNWRITEABLE'))
	{
		$output .= '<tr><td>' . $file . '</td><td colspan="2">' . JText::_('COM_SH404SEF_UNWRITEABLE') . '</td></tr>';
	}
}
if (!empty($output))
{
	$output = '<th class="cpanel" colspan="3" >' . JText::_('COM_SH404SEF_NOACCESS') . '</th>' . $output;
}

// ad red on tab title if something special
if (!empty($output) || $this->sefConfig->debugToLogFile)
{
	$statsTabTitle = '<b><font color="red">(!) ' . JText::_('COM_SH404SEF_ACCESS_URLS_STATS') . '</font></b>';
}
else
{
	$statsTabTitle = JText::_('COM_SH404SEF_ACCESS_URLS_STATS');
}
	?>

</div>

<div id="right" class="span4">

    <ul class="nav nav-tabs" id="content-pane">
         <li class="active"><a data-toggle="tab" href="#qcontrol"><?php echo JText::_('COM_SH404SEF_QUICK_START'); ?></a></li>
         <li><a data-toggle="tab" href="#security"><?php echo JText::_('COM_SH404SEF_SEC_STATS_TITLE'); ?></a></li>
         <li><a data-toggle="tab" href="#infos"><?php echo $infoTabTitle; ?></a></li>
         <li><a data-toggle="tab" href="#stats"><?php echo $statsTabTitle; ?></a></li>
      </ul>

    <?php
echo JHtml::_('bootstrap.startPane', 'content-pane', array('active' => 'qcontrol'));
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'qcontrol');

	?>
      <div id="qcontrolcontent" class="qcontrol">
        <div class="sh-ajax-loading">&nbsp;</div>
      </div>

    <?php

echo JHtml::_('bootstrap.endPanel');
// security stats
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'security');

	?>

    <div id="secstatscontent" class="secstats">

    </div>

    <?php

echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'infos');

	?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td class="span3 shl-right"><?php echo JText::_('COM_SH404SEF_INSTALLED_VERS'); ?></td>
          <td><?php if (!empty($this->sefConfig))
{
	echo ShlHtmlBs_Helper::label($this->sefConfig->version, 'info');
}
else
{
	echo 'Please review and save configuration first';
}
			  ?></td>
        </tr>
      </thead>
      <tr>
        <td class="shl-right"><?php echo JText::_('COM_SH404SEF_COPYRIGHT'); ?></td>
        <td><a href="http://anything-digital.com/sh404sef/seo-analytics-and-security-for-joomla.html">&copy; 2006-<?php echo date('Y'); ?>
        Yannick Gaultier - Anything Digital</a></td>
      </tr>
      <tr>
        <td class="shl-right"><?php echo JText::_('COM_SH404SEF_LICENSE'); ?></td>
        <td><a
          href="http://anything-digital.com/tos.html"
          target="_blank"><?php echo JText::_('COM_SH404SEF_SEE_LICENSE_AND_TERMS'); ?></a></td>
      </tr>
    </table>

    <div id="updatescontent" class="updates">

    </div>

      <?php

echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'stats');

if (!empty($output) || $this->sefConfig->debugToLogFile) :
	  ?>
    <table class="adminform">
      <tr>
      <?php
	if (!empty($output))
	{
		echo $output;
	}
	if ($this->sefConfig->debugToLogFile)
	{
		echo '<tr><th class="cpanel" colspan="3" >DEBUG to log file : ACTIVATED <small>at ' . date('Y-m-d H:i:s', $this->sefConfig->debugStartedAt)
			. '</small></th></tr>';
	}
	  ?>
      </tr>
    </table>
<?php endif; ?>

    <table class="table table-striped">
      <tr>
        <th class="cpanel" colspan="8"> <?php echo JText::_('COM_SH404SEF_URLS_STATS'); ?></th>
      </tr>
      <tr>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_TOTAL') . ':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->sefCount + $this->Count404 + $this->customCount; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_SEF') . ':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->sefCount; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_404') . ':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->Count404; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_CUSTOM') . ':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->customCount; ?>
        </td>
      </tr>
    </table>
    <?php

echo JHtml::_('bootstrap.endPane');

	?>
</div>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
