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

jimport('joomla.application.application');

/**
 * Implement Google analytics handling
 *
 * @author shumisha
 *
 */
class Sh404sefAdapterAnalyticsga extends Sh404sefClassBaseanalytics
{

	protected $_snippet = "
<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '{tracking_code}']);
  {customVar1}
  {customVar2}
  {customVar3}
  {customVar4}
  {customVar5}
  _gaq.push(['_trackPageview'{customUrl}]);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";

	protected $_uga_snippet = "
<script type='text/javascript'>
	
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
  	ga('create', '{tracking_code}', {customDomain});
  	{customUrl}
  	ga('send', 'pageview'{customUrl});
</script>";

	protected $_gtm_snippet = "
<!-- Google Tag Manager -->
			
<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id={tracking_code}\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{tracking_code}');</script>

<!-- End Google Tag Manager -->
";

	protected $_endPoint = 'https://www.googleapis.com/analytics/v2.4/';
	protected $_authPoint = 'https://www.google.com/accounts/ClientLogin';
	protected $_appKeys = array('AIzaSyCW0BXHyqBdvk_pjjrQzUVn9TGGuSCOo8s', 'AIzaSyAA43aNDAI-G_hLc8ZuDboKcF63lFgBIOE',
		'AIzaSyBPE9Vv4RMn7j6lxY2ddp-Tz7M9ldALmgk');

	// specific data
	protected $_SID = '';
	protected $_LSID = '';

	/**
	 * Get tracking snippet
	 *
	 */
	public function getSnippet()
	{
		// get config
		$config = Sh404sefFactory::getConfig();
		//$config->analyticsType = 'uga';
		switch ($config->analyticsEdition)
		{
			case 'ga':
				$snippet = $this->_getSnippetGa();
				break;
			case 'uga':
				$snippet = $this->_getSnippetUga();
				break;
			case 'ga_and_uga':
				$snippet = $this->_getSnippetGa() . "\n" . $this->_getSnippetUga();
				break;
			case 'gtm':
				$snippet = $this->_getSnippetGtm();
				break;
			default:
				$snippet = '';
				break;
		}

		return $snippet . "\n";
	}
	/**
	 * Get Classic Ananlytics tracking snippet
	 *
	 */
	protected function _getSnippetGa()
	{
		// get config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}

		// finalize snippet : add user tracking code
		$snippet = str_replace('{tracking_code}', trim($sefConfig->analyticsId), $this->_snippet);

		// prepare empty array to collect custom vars from plugins
		$customVars = array();

		// fire event so that plugin(s) attach custom vars
		$dispatcher = ShlSystem_factory::dispatcher();
		$dispatcher->trigger('onShInsertAnalyticsSnippet', array(&$customVars, $sefConfig));

		// put custom vars into snippet
		for ($i = 1; $i < 6; $i++)
		{
			$marker = '{customVar' . $i . '}';
			if (!empty($customVars[$i]) && !empty($customVars[$i]->name))
			{
				$replace = "_gaq.push(['_setCustomVar', " . $i . ", '" . htmlentities($customVars[$i]->name, ENT_QUOTES, 'UTF-8') . "', '"
					. htmlentities($customVars[$i]->value, ENT_QUOTES, 'UTF-8') . "', 3]);";
			}
			else
			{
				$replace = '';
			}
			$snippet = str_replace($marker, $replace, $snippet);
		}

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$marker = '{customUrl}';
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			//$customUrl = ", '/__404__?page=' + document.location.pathname + document.location.search + '&amp;from=' + document.referrer";
			$customUrl = ", '/__404__'";
		}
		else
		{
			$customUrl = '';
		}
		$snippet = str_replace($marker, $customUrl, $snippet);

		return $snippet;
	}

	/**
	 * Get Universal Analytics tracking snippet
	 *
	 */
	protected function _getSnippetUga()
	{
		// get config
		$sefConfig = &Sh404sefFactory::getConfig();
		$pageInfo = &Sh404sefFactory::getPageInfo();

		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}

		// finalize snippet : add user tracking code
		$snippet = str_replace('{tracking_code}', trim($sefConfig->analyticsUgaId), $this->_uga_snippet);

		// custom domain
		$snippet = str_replace('{customDomain}', "'auto'", $snippet);

		// prepare empty array to collect custom vars from plugins
		$customVars = array();

		// fire event so that plugin(s) attach custom vars
		$dispatcher = ShlSystem_factory::dispatcher();
		$dispatcher->trigger('onShInsertAnalyticsSnippet', array(&$customVars, $sefConfig));

		// put custom vars into snippet
		for ($i = 1; $i < 6; $i++)
		{
			$marker = '{customVar' . $i . '}';
			if (!empty($customVars[$i]) && !empty($customVars[$i]->name))
			{
				$replace = "_gaq.push(['_setCustomVar', " . $i . ", '" . htmlentities($customVars[$i]->name, ENT_QUOTES, 'UTF-8') . "', '"
					. htmlentities($customVars[$i]->value, ENT_QUOTES, 'UTF-8') . "', 3]);";
			}
			else
			{
				$replace = '';
			}
			$snippet = str_replace($marker, $replace, $snippet);
		}

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$marker = '{customUrl}';
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			$customUrl = "ga('set', 'page', '/__404__');";
		}
		else
		{
			$customUrl = '';
		}
		$snippet = str_replace($marker, $customUrl, $snippet);

		return $snippet;
	}

	/**
	 * Get Google Tags manager snippet
	 *
	 */
	protected function _getSnippetGtm()
	{
		// get config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}

		// finalize snippet : add user tracking code
		$snippet = str_replace('{tracking_code}', trim($sefConfig->analyticsGtmId), $this->_gtm_snippet);

		return $snippet;
	}
	/**
	 * Set client object to perform request
	 * for connection to analytics service
	 *
	 */
	protected function _prepareConnectRequest()
	{

		$hClient = Sh404sefHelperAnalytics::getHttpClient();

		// set params
		$hClient->setUri($this->_authPoint);
		$hClient->setConfig(array('maxredirects' => 0, 'timeout' => 10));

		// request details
		$hClient->setMethod(Zendshl_Http_Client::POST);
		$hClient->setEncType('application/x-www-form-urlencoded');

		// request data
		$postData = array('accountType' => 'GOOGLE', 'Email' => $this->_config->analyticsUser, 'Passwd' => $this->_config->analyticsPassword,
			'service' => 'analytics', 'source' => JFactory::getApplication()->getCfg('sitename') . '-sh404sef-' . $this->_config->version);

		$hClient->setParameterPost($postData);
	}

	/**
	 *
	 * Handle response from connect request
	 *
	 */
	protected function _handleConnectResponse($response)
	{

		// check if authentified
		Sh404sefHelperAnalytics::verifyAuthResponse($response);

		// we are authorized, collect Auth token from body
		$this->_extractAuthToken($response->getBody());

		return true;
	}

	protected function _fetchAccountsList()
	{

		$hClient = Sh404sefHelperAnalytics::getHttpClient();
		$hClient->resetParameters($clearAll = true);

		// build the request
		$sefConfig = Sh404sefFactory::getConfig();
		$accountIdBits = explode('-', trim($sefConfig->analyticsId));
		if (empty($accountIdBits) || count($accountIdBits) < 3)
		{
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', 'Invalid account Id'));
		}
		$accoundId = $accountIdBits[1];

		// set target API url
		$hClient
			->setUri(
				$this->_endPoint . 'management/accounts/' . $accoundId . '/webproperties/' . trim($sefConfig->analyticsId) . '/profiles?key='
					. $this->_getAppKey());

		// make sure we use GET
		$hClient->setMethod(Zendshl_Http_Client::GET);

		// set headers required by Google Analytics
		$headers = array('GData-Version' => 2, 'Authorization' => 'GoogleLogin auth=' . $this->_Auth);

		$hClient->setHeaders($headers);

		//perform request
		// establish connection with available methods
		$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
		$rawResponse = null;

		// perform connect request
		foreach ($adapters as $adapter)
		{
			try
			{
				$hClient->setAdapter($adapter);
				$response = $hClient->request();
				break;
			}
			catch (Exception $e)
			{
				// we failed, let's try another method
			}
		}

		// return if error
		if (empty($response))
		{
			$msg = 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}
		if (empty($response) || !is_object($response) || $response->isError())
		{
			$msg = method_exists($response, 'getStatus') ? $response->getStatus() : 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}

		// analyze response
		// check if authentified
		Sh404sefHelperAnalytics::verifyAuthResponse($response);
		$xml = simplexml_load_string($response->getBody());

		if (!empty($xml->entry))
		{
			foreach ($xml->entry as $entry)
			{
				$account = new StdClass();
				$bits = explode('/', (string) $entry->id);
				$account->id = array_pop($bits);
				$account->title = str_replace('Google Analytics Profile ', '', (string) $entry->title);
				$account->title = str_replace('Google Analytics View (Profile) ', '', $account->title);
				$this->_accounts[] = clone ($account);
			}
		}
	}

	/**
	 * prepare html filters to allow user to select the way she likes
	 * to view reports
	 */
	protected function _prepareFilters()
	{
		// array to hold various filters
		$filters = array();

		// find if we must display all filters. On dashboard, only a reduced set
		$allFilters = $this->_options['showFilters'] == 'yes';

		// select account to retrieve data for (or rather, profile
		$customSubmit = ' onchange="shSetupAnalytics({' . ($allFilters ? '' : 'showFilters:\'no\'') . '});"';

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$select = '<div class="btn-group">';
			$select .= Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$select .= '</div>';
			$filters[] = $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				$select = '<div class="btn-group">';
				$select .= '<label for="startDate">' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', 'class="textinput"');
				$select .= '</div>';
				$filters[] = $select;

				$select = '<div class="btn-group">';
				$select .= '<label for="endDate">' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', 'class="textinput"');
				$select .= '</div>';
				$filters[] = $select;

				// select groupBy (day, week, month)
				$select = '<div class="btn-group">';
				$select .= '<label for="groupBy">' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . '</label>';
				$select .= Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$select .= '</div>';
				$filters[] = $select;

				// add a click to update link
				$filters[] = '<a class="btn btn-link" href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});" > [' . JText::_('COM_SH404SEF_CHECK_ANALYTICS') . ']</a>';

			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		else
		{
			$select = Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$filters[] = JText::_('COM_SH404SEF_ANALYTICS_ACCOUNT') . ':&nbsp;' . $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				// select start date
				$select = JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', 'class="textinput"');
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . ':&nbsp;' . $select;

				// select end date
				$select = JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', 'class="textinput"');
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . ':&nbsp;' . $select;

				// select groupBy (day, week, month)
				$select = Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . ':&nbsp;' . $select;

				// add a click to update link
				$filters[] = '&nbsp;<a href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});" > [' . JText::_('COM_SH404SEF_CHECK_ANALYTICS') . ']</a>';
			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		// use layout to render
		return $filters;
	}

	protected function _extractAuthToken($body)
	{
		$SID = explode('LSID=', $body);
		$this->_SID = trim($SID[0]);
		$this->_SID = ltrim($this->_SID, 'SID=');

		$LSID = explode('Auth=', $SID[1]);
		$this->_LSID = trim($LSID[0]);

		$this->_Auth = trim($LSID[1]);
	}
}
