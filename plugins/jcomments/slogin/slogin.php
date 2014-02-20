<?php
/**
 * JComments - Joomla Comment System
 *
 * Integrates JComments with SocialLogin extension (http://joomline.ru/rasshirenija/komponenty/slogin.html)
 *
 * @version 1.0.8
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2012-2013 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

defined('_JEXEC') or die;

class plgJCommentsSLogin extends JPlugin
{
	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		JPlugin::loadLanguage('plg_jcomments_slogin', JPATH_ADMINISTRATOR);
	}

	function onJCommentsFormBeforeDisplay()
	{
		$content = '';
		$componentPath = JPATH_SITE . '/components/com_slogin/slogin.php';
		
		jimport('joomla.filesystem.file');

		if (JFile::exists($componentPath)) {
			$user = JFactory::getUser();

			if (!$user->id) {
				JPluginHelper::importPlugin('slogin_auth');

				$plugins = array();
				$return = '&return=' . base64_encode(JRequest::getURI());

				if (version_compare(JVERSION, '3.0', 'ge')) {
					JHTML::_('behavior.framework');
					$dispatcher = JEventDispatcher::getInstance();
				} else {
					JHTML::_('behavior.mootools');
					$dispatcher = JDispatcher::getInstance();
				}

				$dispatcher->trigger('onCreateSloginLink', array(&$plugins, $return));

				if (!count($plugins)) {
					// if user have an old version of SLogin component
					$dispatcher->trigger('onCreateLink', array(&$plugins, $return));
				}

				if (count($plugins)) {
					ob_start();

					$app = JFactory::getApplication();
					$jtxf = $app->input->get('jtxf', null);

					if ($jtxf == null) {
						$document = JFactory::getDocument();
						$document->addScript(JURI::root() . 'media/plg_jcomments_slogin/js/slogin.js');
						$document->addStyleSheet(JURI::root() . 'media/plg_jcomments_slogin/css/slogin.css');
					} else {
						echo '<script type="text/javascript" src="' . JURI::root() . 'media/plg_jcomments_slogin/js/slogin.js"></script>';
						echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'media/plg_jcomments_slogin/css/slogin.css" />';
					}

					require_once(dirname(__FILE__) . '/tmpl/default.php');
					$content = ob_get_clean();
				}
			}
		}

		return $content;
	}
}
