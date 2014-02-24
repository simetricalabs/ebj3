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

// no direct access
defined('_JEXEC') or die('Restricted access');

	$lang = JFactory::getLanguage();
	$app = JFactory::getApplication();
	$document = JFactory::getDocument();

	// is an update available?
	$versionsInfo = Sh404sefHelperUpdates::getUpdatesInfos();
	$updateText = $versionsInfo->shouldUpdate ? '<br /><font color="red">' . JText::_('COM_SH404SEF_UPDATE_REQUIRED') . '</font>'
		: '<br /><font color="green">' . JText::_('COM_SH404SEF_UPDATE_NOT_REQUIRED') . '</font>';
?>

<div id="cpanel" >

<div class="icon-wrapper">
  <div class="icon">
    <a href="index.php?option=com_sh404sef"><img src="components/com_sh404sef/assets/images/icon-48-analytics.png" title="sh404sef & Analytics" alt="sh404sef & Analytics" /><span>sh404sef &amp; Analytics<?php echo $updateText; ?></span>
    </a>
  </div>
</div>
</div>
