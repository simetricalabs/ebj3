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
  <table class="adminlist">
  <tbody>
    <tr>
      <td width="10%" class="key">
        <label for="newurl">
          <?php echo JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_LABEL'); ?>&nbsp;
        </label>
      </td>
      <td width="60%">
        <input class="text_area" type="text" name="newurl" id="newurl" size="120" value="<?php echo $this->escape($this->url->get('newurl')); ?>" />
      </td>  
      <td width="30%" style="vertical-align: top;">
        <span ><?php echo JHTML::_('tooltip', JText::_( 'COM_SH404SEF_TT_ENTER_REDIRECT')); ?></span>
      </td>
    </tr>
  </tbody>  
  </table>
