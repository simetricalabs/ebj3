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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Model to read and save sh404SEF configuration from
 * #__extensions table, based on user input in a JForm
 *
 * @TODO: rewrite dynamic parts as custom form fields instead
 * of adding text to the form definition
 *
 */
class Sh404sefModelConfiguration extends ShlMvcModel_Base
{

	protected $_context = 'sh404sef.configuration';

	/**
	 * Save configuration to disk
	 * from POST data or input array of data
	 *
	 * When config will be saved to db, most of the code in this
	 * model will be removed and basemodel should handle everything
	 *
	 * @param array $data an array holding data to save
	 * @param integer id the com_sh404sef component id in extension table
	 *
	 * @return integer id of created or updated record
	 */
	public function save($data, $id)
	{
		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin', 'com_sh404sef'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// instantiate a model from com_config
		$comConfigModel = Sh404sefHelperGeneral::getComConfigComponentModel('com_sh404sef',
			JPATH_ADMINISTRATOR . '/components/com_sh404sef/configuration');

		// collect input
		$app = JFactory::getApplication();
		$form = $comConfigModel->getForm();

		// don't save version, must be read from hardcoded value in config class
		if (isset($data["version"]))
		{
			unset($data["version"]);
		}

		// Save content of error page as an article
		// and remove it from data set, that will be saved in "params"
		// column of #__extensions table
		if (isset($data["txt404"]))
		{
			$this->_saveErrordocs($data["txt404"]);
			unset($data["txt404"]);
		}

		// Mobile parameters will be saved both in the component parameters as well as plugin parameters
		if (isset($data['mobile_template']) || isset($data['mobile_switch_enabled']))
		{
			// get plugins details
			$plugin = JPluginHelper::getPlugin('system', 'shmobile');
			$params = new JRegistry();
			$params->loadString($plugin->params);

			// set params
			if (isset($data['mobile_switch_enabled']))
			{
				$params->set('mobile_switch_enabled', $data['mobile_switch_enabled']);
			}
			if (isset($data['mobile_template']))
			{
				$params->set('mobile_template', $data['mobile_template']);
			}
			// save
			$textParams = (string) $params;
			try
			{
				ShlDbHelper::update('#__extensions', array('params' => $textParams),
					array('element' => 'shmobile', 'folder' => 'system', 'type' => 'plugin'));
			}
			catch (Exception $e)
			{
			}
		}

		// special processing for Analytics password, not displayed
		// if not changed from default, use existing password
		if (isset($data['analyticsPassword']) && $data['analyticsPassword'] == '********')
		{
			$data['analyticsPassword'] = Sh404sefFactory::getConfig()->analyticsPassword;
		}

		// special processing for fields stored as arrays, but edited as strings
		$fields = array('shSecOnlyNumVars', 'shSecAlphaNumVars', 'shSecNoProtocolVars', 'ipWhiteList', 'ipBlackList', 'uAgentWhiteList',
			'uAgentBlackList', 'analyticsExcludeIP');
		foreach ($fields as $field)
		{
			if (isset($data[$field]))
			{
				$data[$field] = $this->_setArrayParam($data[$field]);
			}
		}

		// Attempt to save the configuration.
		$config = array('params' => $data, 'id' => $id, 'option' => 'com_sh404sef');
		$status = $comConfigModel->save($config);

		// store any error
		if (!$status)
		{
			$this->setError(JText::_('COM_SH404SEF_ERR_CONFIGURATION_NOT_SAVED'));
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $comConfigModel->getError());
		}

		return $status;
	}

	public function getForm()
	{
		// import com_config model
		$comConfigModel = Sh404sefHelperGeneral::getComConfigComponentModel('com_sh404sef',
			JPATH_ADMINISTRATOR . '/components/com_sh404sef/configuration');
		$form = $comConfigModel->getForm();
		$component = $comConfigModel->getComponent();

		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$method = '_getByComponentField' . $this->joomlaVersionPrefix;
		// inject the by components part in the form
		$field = $this->$method();
		$form->setField($field);

		// inject the languages part in the form
		$method = '_getLanguagesField' . $this->joomlaVersionPrefix;
		$field = $this->$method();
		$form->setField($field);

		// inject the current content of the 404 error page as default value in the txt404 form field
		$currentErrorPageContent = $this->_getErrorPageContent();
		$form->setFieldAttribute('txt404', 'default', $currentErrorPageContent);

		// inject analytics group field in form
		$field = $this->_getAnalyticsGroupsField();
		$form->setField($field);

		// merge categories in jooomla tab
		$field = $this->_getCategoriesField();
		$form->setField($field);

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		// make sure Analytics password is not visible in the source code of the page
		$form->setValue('analyticsPassword', null, '********');

		// special processing for various parameters: turn string into an array
		// security
		$form->setValue('shSecOnlyNumVars', null, implode("\n", $form->getValue('shSecOnlyNumVars', null, array())));
		$form->setValue('shSecAlphaNumVars', null, implode("\n", $form->getValue('shSecAlphaNumVars', null, array())));
		$form->setValue('shSecNoProtocolVars', null, implode("\n", $form->getValue('shSecNoProtocolVars', null, array())));
		$form->setValue('ipWhiteList', null, implode("\n", $form->getValue('ipWhiteList', null, array())));
		$form->setValue('ipBlackList', null, implode("\n", $form->getValue('ipBlackList', null, array())));
		$form->setValue('uAgentWhiteList', null, implode("\n", $form->getValue('uAgentWhiteList', null, array())));
		$form->setValue('uAgentBlackList', null, implode("\n", $form->getValue('uAgentBlackList', null, array())));
		// analytics
		$form->setValue('analyticsExcludeIP', null, implode("\n", $form->getValue('analyticsExcludeIP', null, array())));

		// read mobile params from the mobile plugin, not from the component config, which only has a copy
		$plugin = JPluginHelper::getPlugin('system', 'shmobile');
		$params = new JRegistry();
		$params->loadString($plugin->params);
		$form->setValue('mobile_switch_enabled', null, $params->get('mobile_switch_enabled', 0));
		$form->setValue('mobile_template', null, $params->get('mobile_template', ''));

		// inject a link to shLib plugin params for cache settings
		$form
			->setFieldAttribute('UrlCacheHandlerLink', 'additionaltext',
				'<span class = "btn sh404sef-textinput"><a href="' . Sh404sefHelperGeneral::getShLibPluginLink() . '" target="_blank">'
					. JText::_('COM_SH404SEF_CONFIGURE_SHLIB_PLUGIN') . '</a></span>');
		return $form;
	}

	/**
	 * Set values in configuration record in database
	 * Optionally update current in memory configuration object
	 *
	 * @param array $values
	 * @param boolean $reset if true, config object in memory will be reset to new values
	 * @return boolean
	 */
	public function setValues($values = array(), $reset = false)
	{

		if (empty($values))
		{
			return true;
		}

		jimport('joomla.application.component.helper');
		$component = JComponentHelper::getComponent('com_sh404sef');
		$params = new JRegistry();
		$params->loadString($component->params);

		// set values
		foreach ($values as $key => $value)
		{
			$params->set($key, $value);
		}

		// convert to json and store into db
		$textParams = $params->toString();
		try
		{
			ShlDbHelper::update('#__extensions', array('params' => $textParams), array('element' => 'com_sh404sef', 'type' => 'component'));
			if ($reset)
			{
				$config = Sh404sefFactory::getConfig($reset = true);
			}
			$status = true;
		}
		catch (Exception $e)
		{
			$status = false;
		}

		return $status;

	}

	/**
	 * Push current error documents content
	 * values into the view for edition
	 * this is a altered version of the same
	 * method in the old config view.
	 */
	private function _getErrorPageContent()
	{
		// find about sh404sef custom content category id
		$sh404sefContentCatId = Sh404sefHelperCategories::getSh404sefContentCat()->id;

		try
		{
			// read current content of 404 page in default language
			$article = ShlDbHelper::selectAssoc('#__content', array('id', 'introtext'),
				array('title' => '__404__', 'catid' => $sh404sefContentCatId, 'language' => '*'));
			$txt404 = empty($article['introtext']) ? JText::_('COM_SH404SEF_DEF_404_MSG') : $article['introtext'];
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			$txt404 = JText::_('COM_SH404SEF_DEF_404_MSG');
		}

		// push params in to view
		return $txt404;
	}

	/*
	 * Creates the By component dynamic form field
	 */
	private function _getByComponentFieldJ3()
	{
		$installedComponents = Sh404sefHelperGeneral::getComponentsList();
		$xml = '';

		$xml .= '<fieldset name="by_component" label="COM_SH404SEF_CONF_TAB_BY_COMPONENT" description="" groupname="COM_SH404SEF_CONFIG">';
		foreach ($installedComponents as $name => $properties)
		{
			$xml .= '<field type="shlegend" shlrenderer="shlegend" class="text" label="' . ucfirst(str_replace('com_', '', $name)) . '"/>';
			$xml .= '<field menu="hide" name="' . $name
				. '___manageURL" type="list" default="0" label="" description="COM_SH404SEF_TT_ADV_MANAGE_URL">';
			$xml .= '<option value="0">COM_SH404SEF_USE_DEFAULT</option>
					<option value="1">COM_SH404SEF_NOCACHE</option>
					<option value="2">COM_SH404SEF_SKIP</option>
					<option value="3">COM_SH404SEF_USE_JOOMLA_ROUTER</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___translateURL" type="list" default="" label="" description="COM_SH404SEF_TT_ADV_TRANSLATE_URL">';
			$xml .= '<option value="0">COM_SH404SEF_TRANSLATE_URL</option>
					<option value="1">COM_SH404SEF_DO_NOT_TRANSLATE_URL</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___insertIsoCode" type="list" default="" label="" description="COM_SH404SEF_TT_ADV_INSERT_ISO">';
			$xml .= '<option value="0">COM_SH404SEF_INSERT_LANGUAGE_CODE</option>
					<option value="1">COM_SH404SEF_DO_NOT_INSERT_LANGUAGE_CODE</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___shDoNotOverrideOwnSef" type="list" default="" label="" description="COM_SH404SEF_TT_ADV_OVERRIDE_SEF">';
			$xml .= '<option value="0">COM_SH404SEF_OVERRIDE_SEF_EXT</option>
					<option value="1">COM_SH404SEF_USE_JOOMLA_PLUGIN</option>
					<option value="30">COM_SH404SEF_USE_JOOMSEF_PLUGIN</option>
					<option value="40">COM_SH404SEF_USE_ACESEF_PLUGIN</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___compEnablePageId" type="list" default="" label="" description="COM_SH404SEF_TT_COMP_ENABLE_PAGEID">';
			$xml .= '<option value="0">COM_SH404SEF_DISABLE_PAGEID</option>
					<option value="1">COM_SH404SEF_ENABLE_PAGEID</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" type="text" name="' . $name
				. '___defaultComponentString" default=""  label="" description="COM_SH404SEF_TT_ADV_COMP_DEFAULT_STRING" size="30" maxlength="30"/>';
		}

		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	private function _getByComponentFieldJ2()
	{
		$installedComponents = Sh404sefHelperGeneral::getComponentsList();
		$xml = '';

		$xml .= '<fieldset name="by_component" label="COM_SH404SEF_CONF_TAB_BY_COMPONENT" description="" groupname="COM_SH404SEF_CONFIG">';
		$xml .= '<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_MANAGE_URL"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_TRANSLATE_URL"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_INSERT_ISO"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_OVERRIDE_SEF"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_COMP_ENABLE_PAGEID"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_COMP_DEFAULT_STRING"/>';
		foreach ($installedComponents as $name => $properties)
		{
			$xml .= '<field menu="hide" name="' . $name . '___manageURL" type="list" default="0" label="' . str_replace('com_', '', $name)
				. '" description="">';
			$xml .= '<option value="0">COM_SH404SEF_USE_DEFAULT</option>
					<option value="1">COM_SH404SEF_NOCACHE</option>
					<option value="2">COM_SH404SEF_SKIP</option>
					<option value="3">COM_SH404SEF_USE_JOOMLA_ROUTER</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___translateURL" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_TRANSLATE_URL</option>
					<option value="1">COM_SH404SEF_DO_NOT_TRANSLATE_URL</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___insertIsoCode" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_INSERT_LANGUAGE_CODE</option>
					<option value="1">COM_SH404SEF_DO_NOT_INSERT_LANGUAGE_CODE</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___shDoNotOverrideOwnSef" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_OVERRIDE_SEF_EXT</option>
					<option value="1">COM_SH404SEF_USE_JOOMLA_PLUGIN</option>
					<option value="30">COM_SH404SEF_USE_JOOMSEF_PLUGIN</option>
					<option value="40">COM_SH404SEF_USE_ACESEF_PLUGIN</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___compEnablePageId" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_DISABLE_PAGEID</option>
					<option value="1">COM_SH404SEF_ENABLE_PAGEID</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" type="text" name="' . $name
				. '___defaultComponentString" default=""  label="" description="" size="30" maxlength="30"/>';
		}

		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	/*
	 * Creates the Languages dynamic form field
	 */
	private function _getLanguagesFieldJ3()
	{

		$activeLanguages = shGetActiveLanguages();

		$xml = '';
		$xml .= '<fieldset
		name="languages"
		label="COM_SH404SEF_CONF_TAB_LANGUAGES"
		description=""
		groupname="COM_SH404SEF_CONFIG"
		>
		<field menu="hide" name="enableMultiLingualSupport" type="radio" class="btn-group" default="0" label="COM_SH404SEF_ENABLE_MULTILINGUAL_SUPPORT" description="COM_SH404SEF_TT_ENABLE_MULTILINGUAL_SUPPORT">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>
        <field type="shlegend" shlrenderer="shlegend" class="text" label="COM_SH404SEF_TRANSLATION_TITLE"/>
		<field menu="hide" name="shTranslateURL" type="radio" class="btn-group" default="1" label="COM_SH404SEF_TRANSLATE_URL" description="COM_SH404SEF_TT_TRANSLATE_URL_GEN">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>
		<field menu="hide" name="shInsertLanguageCode" type="radio" class="btn-group" default="1" label="COM_SH404SEF_INSERT_LANGUAGE_CODE" description="COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_GEN">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>';
		foreach ($activeLanguages as $language)
		{
			$xml .= '<field type="shlegend" shlrenderer="shlegend" class="text" label="' . $language->code
				. '"/>
			<field menu="hide" name="languages_' . $language->code
				. '_pageText" type="text" default="Page-&#37;s" label="COM_SH404SEF_PAGETEXT" description="COM_SH404SEF_TT_PAGETEXT"/>
			<field menu="hide" name="languages_' . $language->code
				. '_translateURL" type="list" default="0" label="COM_SH404SEF_TRANSLATE_URL" description="COM_SH404SEF_TT_TRANSLATE_URL_PER_LANG" class="inputbox">
				<option value="0">COM_SH404SEF_DEFAULT</option>
				<option value="1">COM_SH404SEF_YES</option>
				<option value="2">COM_SH404SEF_NO</option>
			</field>
			<field menu="hide" name="languages_' . $language->code
				. '_insertCode" type="list" default="0" label="COM_SH404SEF_INSERT_LANGUAGE_CODE" description="COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_PER_LANG" class="inputbox">
				<option value="0">COM_SH404SEF_DEFAULT</option>
				<option value="1">COM_SH404SEF_YES</option>
				<option value="2">COM_SH404SEF_NO</option>
			</field>';
		}
		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	private function _getLanguagesFieldJ2()
	{
		$activeLanguages = shGetActiveLanguages();

		$xml = '';
		$xml .= '<fieldset
		name="languages"
		label="COM_SH404SEF_CONF_TAB_LANGUAGES"
		description=""
		groupname="COM_SH404SEF_CONFIG"
		>
		<field menu="hide" name="enableMultiLingualSupport" type="radio" default="0" label="COM_SH404SEF_ENABLE_MULTILINGUAL_SUPPORT" description="COM_SH404SEF_TT_ENABLE_MULTILINGUAL_SUPPORT">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>
        <field type="spacer" class="text" label="COM_SH404SEF_TRANSLATION_TITLE"/>
		<field menu="hide" name="shTranslateURL" type="radio" default="1" label="COM_SH404SEF_TRANSLATE_URL" description="COM_SH404SEF_TT_TRANSLATE_URL_GEN">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>
		<field menu="hide" name="shInsertLanguageCode" type="radio" default="1" label="COM_SH404SEF_INSERT_LANGUAGE_CODE" description="COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_GEN">
				<option value="0">COM_SH404SEF_NO</option>
				<option value="1">COM_SH404SEF_YES</option>
		</field>';
		foreach ($activeLanguages as $language)
		{
			$xml .= '<field type="spacer" class="text" label="' . $language->code . '"/>
			<field menu="hide" name="languages_' . $language->code
				. '_pageText" type="text" default="Page-&#37;s" label="COM_SH404SEF_PAGETEXT" description="COM_SH404SEF_TT_PAGETEXT"/>
			<field menu="hide" name="languages_' . $language->code
				. '_translateURL" type="list" default="0" label="COM_SH404SEF_TRANSLATE_URL" description="COM_SH404SEF_TT_TRANSLATE_URL_PER_LANG" class="inputbox">
				<option value="0">COM_SH404SEF_DEFAULT</option>
				<option value="1">COM_SH404SEF_YES</option>
				<option value="2">COM_SH404SEF_NO</option>
			</field>
			<field menu="hide" name="languages_' . $language->code
				. '_insertCode" type="list" default="0" label="COM_SH404SEF_INSERT_LANGUAGE_CODE" description="COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_PER_LANG" class="inputbox">
				<option value="0">COM_SH404SEF_DEFAULT</option>
				<option value="1">COM_SH404SEF_YES</option>
				<option value="2">COM_SH404SEF_NO</option>
			</field>';
		}
		$xml .= '</fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	/*
	 * Creates the Analytics groups dynamic field
	 */
	private function _getAnalyticsGroupsField()
	{
		$usergroups = JHtml::_('user.groups', $includeSuperAdmin = true);
		$xml = '';
		$xml .= '<fieldset name="analytics" label="COM_SH404SEF_CONFIG_ANALYTICS" description="COM_SH404SEF_CONF_ANALYTICS_HELP" groupname="COM_SH404SEF_CONFIG_ANALYTICS">';
		$xml .= '<field menu="hide" name="analyticsUserGroups" type="list" multiple="true" size="10" default="[3,7]" label="COM_SH404SEF_ANALYTICS_USER_GROUPS" description="COM_SH404SEF_TT_ANALYTICS_USER_GROUPS">';
		foreach ($usergroups as $usergroup)
		{
			$t = htmlspecialchars($usergroup->text, ENT_COMPAT, 'UTF-8');
			$xml .= '<option value="' . $usergroup->value . '">' . htmlspecialchars($t, ENT_COMPAT, 'UTF-8') . '</option>';
		}
		$xml .= '</field></fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	private function _getCategoriesField()
	{
		$catListOptions = JHtml::_('category.options', 'com_content');
		$options = '';
		foreach ($catListOptions as $cat)
		{
			// need to apply htmlspecialchars twice, as SimpleXMLElement does an
			// htmlentitydecode in the constructor, which then causes
			// an error downstream when this data is injected in the form
			$t = htmlspecialchars($cat->text, ENT_COMPAT, 'UTF-8');
			$options .= '<option value="' . $cat->value . '">' . htmlspecialchars($t, ENT_COMPAT, 'UTF-8') . '</option>';
		}
		$xml = '';
		$xml .= '<fieldset name="joomla" label="Joomla" description="" groupname="COM_SH404SEF_CONFIG_EXT">';
		$xml .= '<field menu="hide" name="shInsertContentArticleIdCatList" type="list" multiple="true" default="" label="COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST" description="COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST">';
		$xml .= '<option value="">COM_SH404SEF_INSERT_NUMERICAL_ID_ALL_CAT</option>';
		$xml .= $options;
		$xml .= '</field>';
		$xml .= '<field menu="hide" name="shInsertNumericalIdCatList" type="list" multiple="true" default="" label="COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST" description="COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST">';
		$xml .= '<option value="">COM_SH404SEF_INSERT_NUMERICAL_ID_ALL_CAT</option>';
		$xml .= $options;
		$xml .= '</field>';
		$xml .= '</fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	/**
	 * Prepare saving of  Error documents configuration options set
	 */
	private function _saveErrordocs($errorPagecontent)
	{
		// update 404 error page
		$quoteGPC = get_magic_quotes_gpc();
		$shIntroText = empty($_POST) ? '' : ($quoteGPC ? stripslashes($errorPagecontent) : $errorPagecontent);
		try
		{
			// is there already a 404 page article?
			$id = ShlDbHelper::selectResult('#__content', 'id',
				array('title' => '__404__', 'catid' => Sh404sefHelperCategories::getSh404sefContentCat()->id, 'language' => '*'));

			if (!empty($id))
			{
				// yes, update it
				ShlDbHelper::update('#__content', array('introtext' => $shIntroText, 'modified' => date("Y-m-d H:i:s")), array('id' => $id));
			}
			else
			{
				$catid = Sh404sefHelperCategories::getSh404sefContentCat()->id;
				if (empty($catid))
				{
					$this->setError(JText::_('COM_SH404SEF_CANNOT_SAVE_404_NO_UNCAT'));
					return;
				}
				$contentTable = JTable::getInstance('content');
				$content = array('title' => '__404__', 'alias' => '__404__', 'title_alias' => '__404__', 'introtext' => $shIntroText, 'state' => 1,
					'catid' => $catid,
					'attribs' => '{"menu_image":"-1","show_title":"0","show_section":"0","show_category":"0","show_vote":"0","show_author":"0","show_create_date":"0","show_modify_date":"0","show_pdf_icon":"0","show_print_icon":"0","show_email_icon":"0","pageclass_sfx":""',
					'language' => '*');

				$saved = $contentTable->save($content);
				if (!$saved)
				{
					$this->setError($contentTable->getError());
				}

			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMEssage());
		}
	}

	/**
	 * Turns a value entered by user as a string
	 * into an array, suitable for storage
	 *
	 * @param string $value input from user
	 */
	private function _setArrayParam($value)
	{
		$array = array();
		if (!empty($value))
		{
			$array = explode("\n", $value);
			foreach ($array as $k => $v)
			{
				$array[$k] = JString::trim($v);
			}
		}
		if (!empty($array))
		{
			$array = array_filter($array);
		}

		return $array;
	}
}
