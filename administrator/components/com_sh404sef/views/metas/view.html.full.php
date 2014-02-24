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

class Sh404sefViewMetas extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'metas';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
			Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));

		// get model and update context with current
		$model = ShlMvcModel_Base::getInstance('urls', 'Sh404sefModel');
		$context = $model->setContext($this->_context . '.' . $this->getLayout());

		// display type: simple for very large sites/slow slq servers
		$sefConfig = Sh404sefFactory::getConfig();

		// store it
		$model = $this->setModel($model, true);

		// read data from model
		$list = $model
			->getList(
				(object) array('layout' => $this->getLayout(), 'getMetaData' => true, 'simpleUrlList' => true, 'slowServer' => $sefConfig->slowServer));

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = count($this->items);
		$this->contentcs = Sh404sefHelperGeneral::getDataMD5($this->items, array('metatitle', 'metadesc'));
		$this->pagination = $model
			->getPagination((object) array('layout' => $this->getLayout(), 'simpleUrlList' => true, 'slowServer' => $sefConfig->slowServer));
		$options = $model->getDisplayOptions();
		$this->options = $options;
		$this->helpMessage = JText::_('COM_SH404SEF_META_HELP');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();

			// add our own css and js
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/shajax.js');
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/j3.js');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// variable for modal, not used in 3..x+
			$params = array();

			$this->_addFilters();

			// render submenu sidebar
			$this->sidebar = Sh404sefHelperHtml::renderSubmenu();
		}
		else
		{
			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_urls.css');
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/metas.js');

			// add behaviors and styles as needed
			$modalSelector = 'a.modalediturl';
			$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\''
				. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}}';
			$params = array('overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
			Sh404sefHelperHtml::modal($modalSelector, $params);

			$this->optionsSelect = $this->_makeOptionsSelect($options);

		}

		// build the toolbar
		$toolbarMethod = '_makeToolbar' . ucfirst($this->getLayout() . ucfirst($this->joomlaVersionPrefix));
		if (is_callable(array($this, $toolbarMethod)))
		{
			$this->$toolbarMethod($params);
		}

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Create toolbar for default layout view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarDefaultJ2($params = null)
	{
		// add title
		$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_META_TAGS'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
		JFactory::getApplication()->JComponentTitle = $title;

		// get toolbar object
		$bar = JToolBar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');

		// add import button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 380);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=urls'; // importing metas is same as importing urls, as export format is same
		$bar
			->appendButton('Shpopuptoolbarbutton', 'import', $url, JText::_('COM_SH404SEF_IMPORT_BUTTON'), $msg = '', $task = 'import',
				$list = false, $hidemenu = true, $params);

		// add export button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 380);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=metas';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'export', $url, JText::_('COM_SH404SEF_EXPORT_BUTTON'), $msg = '', $task = 'export',
				$list = false, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// edit home page button
		$params['class'] = 'modalediturl';
		$params['size'] = array('x' => 800, 'y' => 600);
		$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) parent.window.location=\''
			. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}';
		$params['onClose'] = $js;
		$bar
			->appendButton('Shpopupbutton', 'home', JText::_('COM_SH404SEF_HOME_PAGE_ICON'),
				"index.php?option=com_sh404sef&c=editurl&task=edit&home=1&tmpl=component", $params);

		// separator
		JToolBarHelper::divider();

		// add save button as an ajax call
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
		$params['class'] = 'savemeta';
		$params['id'] = 'savemeta';
		$params['closewindow'] = 0;
		$bar->appendButton('Shajaxbutton', 'save', 'Save', "index.php?option=com_sh404sef&c=metas&task=save&shajax=1&tmpl=component", $params);

		// separator
		JToolBarHelper::divider();
	}

	/**
	 * Create toolbar for default layout view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarDefaultJ3($params = null)
	{
		// add title
		JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_META_TAGS'), 'sh404sef-toolbar-title');

		// add "New url" button
		$bar = JToolBar::getInstance('toolbar');

		// prepare configuration button
		$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');

		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['import'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-upload';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=urls';
		$bar
			->appendButton('J3popuptoolbarbutton', 'import', JText::_('COM_SH404SEF_IMPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_IMPORTING_TITLE'), $params);

		// add import button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['export'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-download';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=metas';
		$bar
			->appendButton('J3popuptoolbarbutton', 'export', JText::_('COM_SH404SEF_EXPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_EXPORTING_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// edit home page button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-home';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&home=1&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'home', JText::_('COM_SH404SEF_HOME_PAGE_ICON'), $url, $params['size']['x'], $params['size']['y'],
				$top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_HOME_PAGE_EDIT_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// add save button
		$params = array();
		$params['buttonClass'] = 'btn btn-small btn-primary';
		$params['iconClass'] = 'icon-apply icon-white';
		$params['closewindow'] = 0;
		$url = 'index.php?option=com_sh404sef&c=metas&task=save&shajax=1&tmpl=component';
		$bar->appendButton('J3Shajaxtoolbarbutton', 'save', JText::_('JAPPLY'), $url, $params);

		// separator
		JToolBarHelper::spacer(20);

		// save progress div
		$html = '<div id="sh-progress-cpprogress">&nbsp;</div>';
		$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');
	}

	private function _makeOptionsSelect($options)
	{
		$selects = new StdClass();

		// component list
		$current = $options->filter_component;
		$name = 'filter_component';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_COMPONENTS');
		$selects->components = Sh404sefHelperHtml::buildComponentsSelectList($current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// language list
		$current = $options->filter_language;
		$name = 'filter_language';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_LANGUAGES');
		$selects->languages = Sh404sefHelperHtml::buildLanguagesSelectList($current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

		// select custom
		$current = $options->filter_url_type;
		$name = 'filter_url_type';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_URL_TYPES');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'title' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'title' => JText::_('COM_SH404SEF_ONLY_AUTO')));
		$selects->filter_url_type = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// select title
		$current = $options->filter_title;
		$name = 'filter_title';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_TITLE');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_TITLE, 'title' => JText::_('COM_SH404SEF_ONLY_TITLE')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_NO_TITLE, 'title' => JText::_('COM_SH404SEF_NO_TITLE')));
		$selects->filter_title = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// select description
		$current = $options->filter_desc;
		$name = 'filter_desc';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_DESC');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_DESC, 'title' => JText::_('COM_SH404SEF_ONLY_DESC')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_NO_DESC, 'title' => JText::_('COM_SH404SEF_NO_DESC')));
		$selects->filter_desc = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

		// return set of select lists
		return $selects;
	}

	private function _addFilters()
	{
		// component selector
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_COMPONENTS'), 'filter_component',
			JHtml::_('select.options', Sh404sefHelperGeneral::getComponentsList(), 'element', 'name', $this->options->filter_component, true));

		// language list
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_LANGUAGES'), 'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', $all = false, $translate = true), 'value', 'text',
				$this->options->filter_language, false));

		// select custom
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'text' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'text' => JText::_('COM_SH404SEF_ONLY_AUTO')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_URL_TYPES'), 'filter_url_type',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_url_type, true));

		// filter on title presence
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_TITLE, 'text' => JText::_('COM_SH404SEF_ONLY_TITLE')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_NO_TITLE, 'text' => JText::_('COM_SH404SEF_NO_TITLE')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_TITLE'), 'filter_title',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_title, true));

		// filter on description presence
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_DESC, 'text' => JText::_('COM_SH404SEF_ONLY_DESC')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_NO_DESC, 'text' => JText::_('COM_SH404SEF_NO_DESC')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_DESC'), 'filter_desc',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_desc, true));
	}
}
