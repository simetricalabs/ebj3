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
	if($versionsInfo->shouldUpdate) {
		$updateText = JText::_('COM_SH404SEF_UPDATE_REQUIRED');
		$class = 'badge badge-warning';
	} else {
		return;
	}

?>

<div class="btn-group" title="<?php echo $updateText; ?>"><a href="index.php?option=com_sh404sef"><span class="<?php echo $class?>">sh404SEF</span></a></div>
