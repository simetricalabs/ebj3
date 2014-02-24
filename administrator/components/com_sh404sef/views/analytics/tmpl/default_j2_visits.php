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


switch ($this->sefConfig->analyticsDashboardDataType) {
  case 'ga:visits':
    $title = JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITS_DESC_RAW');
    break;
  case 'ga:visitors':
    $title = JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITORS_DESC_RAW');
    break;
  case 'ga:pageviews':
    $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_PAGEVIEWS_DESC_RAW');
    break;
  default:
    $title = '';
    break;
}

$title = Sh404sefHelperAnalytics::getDataTypeTitle() . (empty($title) ? '' : '::' . $title);

?>


  <div  class="hasAnalyticsTip width-100" title="<?php echo $title; ?>" >

  	<fieldset>
            <?php
              echo '<legend>' . Sh404sefHelperAnalytics::getDataTypeTitle() . '</legend>';

              echo '<div class="analytics-report-image"><img src="' . $this->analytics->analyticsData->images['visits']. '" /></div>';
            ?>
    </fieldset>

  </div>
