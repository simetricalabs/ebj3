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

jimport('joomla.application.component.view');

class Sh404sefViewDefault extends ShlMvcView_Base
{

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
			Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));

		// prepare the view, based on layout
		$method = '_makeView' . ucfirst($this->getLayout());
		if (is_callable(array($this, $method)))
		{
			$this->$method();
		}

		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Attach css, js and create toolbar for default view
	 *
	 * @param midxed $params
	 */
	private function _makeViewDefault($params = null)
	{

		// prepare database stats, etc
		$this->_prepareControlPanelData();

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();

			// render submenu sidebar
			$this->sidebar = JHtmlSidebar::render();

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add title
			JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_CONTROL_PANEL'), 'sh404sef-toolbar-title');

			// prepare configuration button
			$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');
			$params = array();
			$params['class'] = 'modaltoolbar btn-success';
			$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['configuration'];
			$params['buttonClass'] = 'btn-success btn btn-small modal';
			$params['iconClass'] = 'icon-options';
			$url = 'index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1';
			$bar
				->appendButton('J3popuptoolbarbutton', 'configj3', JText::_('COM_SH404SEF_CONFIGURATION'), $url, $params['size']['x'],
					$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = '', $params);

			$html = '<div id="sh-progress-cpprogress"></div>';
			$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');

			// add analytics and other ajax calls loader
			$sefConfig = Sh404sefFactory::getConfig();
			$analyticsBootstrap = $sefConfig->analyticsReportsEnabled ? 'shSetupAnalytics({report:"dashboard",showFilters:"no"});' : '';
			$js = 'jQuery(document).ready(function(){ ' . $analyticsBootstrap . '  shSetupQuickControl(); shSetupSecStats(); shSetupUpdates();});';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}
		else
		{
			// add behaviors and styles as needed
			$modalSelector = 'a.modalediturl';
			$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\'index.php?option=com_sh404sef\';window.parent.shReloadModal=true}}';
			$params = array('overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
			Sh404sefHelperHtml::modal($modalSelector, $params);

			// import tabs
			jimport('joomla.html.pane');

			// add tooltips handler
			JHTML::_('behavior.tooltip');

			// add title
			$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_CONTROL_PANEL'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			JFactory::getApplication()->JComponentTitle = $title;

			// add a div to display our ajax-call-in-progress indicator
			$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
			$html = '<div id="sh-progress-cpprogress"></div>';
			$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');

			// add modal handler for configuration
			JHTML::_('behavior.modal');
			$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
				. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
			$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');

			// add analytics and other ajax calls loader
			$sefConfig = Sh404sefFactory::getConfig();
			$analyticsBootstrap = $sefConfig->analyticsReportsEnabled ? 'shSetupAnalytics({report:"dashboard",showFilters:"no"});' : '';
			$js = 'window.addEvent(\'domready\', function(){ ' . $analyticsBootstrap
				. '  shSetupQuickControl(); shSetupSecStats(); shSetupUpdates();});';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}

		// add our javascript
		JHTML::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/' . $this->joomlaVersionPrefix . '_cp.' . Sh404sefConfigurationEdition::$id . '.js');
		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_cp.css');
	}

	/**
	 * Attach css, js and create toolbar for Info view
	 *
	 * @param midxed $params
	 */
	private function _makeViewInfo($params = null)
	{
		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

		// decide on help file language
		$languageCode = Sh404sefHelperLanguage::getFamily();
		$basePath = JPATH_ROOT . '/administrator/components/com_sh404sef/language/%s.readme.php';
		// fall back to english if language readme does not exist
		jimport('joomla.filesystem.file');
		if (!JFile::exists(sprintf($basePath, $languageCode)))
		{
			$languageCode = 'en';
		}
		$this->readmeFilename = sprintf($basePath, $languageCode);

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// render submenu sidebar
			$this->sidebar = JHtmlSidebar::render();

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			JToolbarHelper::title(JText::_('COM_SH404SEF_TITLE_SUPPORT'), 'sh404sef-toolbar-title');
		}
		else
		{
			// add title
			$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_TITLE_SUPPORT'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			JFactory::getApplication()->JComponentTitle = $title;
		}
	}

	private function _prepareControlPanelData()
	{
		$sefConfig = Sh404sefFactory::getConfig();
		$this->sefConfig = $sefConfig;

		// update information
		$versionsInfo = Sh404sefHelperUpdates::getUpdatesInfos();
		$this->updates = $versionsInfo;

		// url databases stats
		$database = ShlDbHelper::getDb();
		try
		{
			$sql = 'SELECT count(*) FROM #__sh404sef_urls WHERE ';
			$database->setQuery($sql . "`dateadd` > '0000-00-00' and `newurl` = '' "); // 404
			$count404 = $database->loadResult();
			$database->setQuery($sql . "`dateadd` > '0000-00-00' and `newurl` != '' "); // custom
			$customCount = $database->loadResult();
			$database->setQuery($sql . "`dateadd` = '0000-00-00'"); // regular
			$sefCount = $database->loadResult();
			// calculate security stats
			$default = empty($sefConfig->shSecLastUpdated) ? '- -' : '0';
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			$sefCount = 0;
			$count404 = 0;
			$customCount = 0;
		}

		$this->sefCount = $sefCount;
		$this->Count404 = $count404;
		$this->customCount = $customCount;
	}
}
