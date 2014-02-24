<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date  2014-01-23
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.view');

class Sh404sefViewConfiguration extends ShlMvcView_Base
{

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();

		if ($this->getLayout() != 'close')
		{
			// insert custom stylesheet
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/configuration.css');
			
			switch (Sh404sefConfigurationEdition::$id)
			{
				case 'community':
				case 'lite':
					$this->byComponentItemsCount = 6;
					JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/configuration.community.css');
					break;
				default:
					$this->byComponentItemsCount = 7;
			
			}
			
			// get model
			$model = $this->getModel();
			// ask for the form
			$this->form = $model->getForm();

			// prepare layouts objects, to be used by sub-layouts
			$this->layoutRenderer = array();
			$this->layoutRenderer['default'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.default', sh404SEF_LAYOUTS);
			$this->layoutRenderer['shlegend'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.legend', sh404SEF_LAYOUTS);
			$this->layoutRenderer['Rules'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.rules', sh404SEF_LAYOUTS);

			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
				ShlHtmlBs_helper::addBootstrapModalFixCss(JFactory::getDocument());
				ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

				JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/j3_list.css');
			}
		}

		parent::display($this->joomlaVersionPrefix);
	}

}
