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

?>
<div class="container-fluid">
<?php

// ogData
$data = new stdClass();
$data->name = 'og_enable';
$data->label = JText::_('COM_SH404SEF_OG_DATA_ENABLED_BY_URL');
$data->input = $this->ogData['og_enable'];
$data->tip = JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED_BY_URL');
echo $this->layoutRenderer['custom']->render($data);

$data = new stdClass();
$data->label = '<legend>' . JText::_('COM_SH404SEF_OG_REQUIRED_TITLE') . '</legend>';
echo $this->layoutRenderer['shlegend']->render($data);

// og_type
$data = new stdClass();
$data->name = 'og_type';
$data->label = JText::_('COM_SH404SEF_OG_TYPE_SELECT');
$data->input = $this->ogData['og_type'];
$data->tip = JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT');
echo $this->layoutRenderer['custom']->render($data);

// og_image
$data = new stdClass();
$data->name = 'og_image';
$data->label = JText::_('COM_SH404SEF_OG_IMAGE_PATH');
$data->input = $data->input = '<input type="text" name="og_image" id="og_image" size="90" value="' . $this->ogData['og_image'] . '" />';
$data->tip = JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH');
echo $this->layoutRenderer['custom']->render($data);

$data = new stdClass();
$data->label = '<legend>' . JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE') . '</legend>';
echo $this->layoutRenderer['shlegend']->render($data);

// og_enable_description
$data = new stdClass();
$data->name = 'og_enable_description';
$data->label = JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION');
$data->input = $this->ogData['og_enable_description'];
$data->tip = JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION');
echo $this->layoutRenderer['custom']->render($data);

// og_enable_site_name
$data = new stdClass();
$data->name = 'og_enable_site_name';
$data->label = JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME');
$data->input = $this->ogData['og_enable_site_name'];
$data->tip = JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME');
echo $this->layoutRenderer['custom']->render($data);

// og_site_name
$data = new stdClass();
$data->name = 'og_site_name';
$data->label = JText::_('COM_SH404SEF_OG_SITE_NAME');
$data->input = $data->input = '<input type="text" name="og_site_name" id="og_site_name" size="90" value="' . $this->ogData['og_site_name'] . '" />';
$data->tip = JText::_('COM_SH404SEF_TT_OG_SITE_NAME');
echo $this->layoutRenderer['custom']->render($data);

// og_enable_fb_admin_ids
$data = new stdClass();
$data->name = 'og_enable_fb_admin_ids';
$data->label = JText::_('COM_SH404SEF_OG_ENABLE_FB_ADMIN_IDS');
$data->input = $this->ogData['og_enable_fb_admin_ids'];
$data->tip = JText::_('COM_SH404SEF_TT_OG_ENABLE_FB_ADMIN_IDS');
echo $this->layoutRenderer['custom']->render($data);

// og_site_name
$data = new stdClass();
$data->name = 'fb_admin_ids';
$data->label = JText::_('COM_SH404SEF_FB_ADMIN_IDS');
$data->input = $data->input = '<input type="text" name="fb_admin_ids" id="fb_admin_ids" size="90" value="' . $this->ogData['fb_admin_ids'] . '" />';
$data->tip = JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS');
echo $this->layoutRenderer['custom']->render($data);

?>
</div>
