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
class Sh404sefConfiguration
{
	/**
	 * List of search engines user agent strings
	 * @var array
	 */
	private $_searchEnginesAgents = array('B-l-i-t-z-B-O-T', 'Baiduspider', 'BlitzBot', 'btbot', 'DiamondBot', 'Exabot', 'FAST Enterprise Crawler',
		'FAST-WebCrawler/', 'g2Crawler', 'genieBot', 'Gigabot', 'Girafabot', 'Googlebot', 'ia_archiver', 'ichiro', 'Mediapartners-Google',
		'Mnogosearch', 'msnbot', 'MSRBOT', 'Nusearch Spider', 'SearchSight', 'Seekbot', 'sogou spider', 'Speedy Spider', 'Ask Jeeves/Teoma',
		'VoilaBot', 'Yahoo!', 'Slurp', 'YahooSeeker', 'YandexBot');

	/**
	 * List of tracking vars that should be removed from url when calculating canonical url or similar
	 * Note: 'hitcount' is introduced internally by Joomla! 3 vote plugin!!
	 * @var array
	 */
	private $_trackingVars = array('utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_id', 'utm_campaign', 'gclid', 'fb_xd_bust',
		'fb_xd_fragment', 'hitcount');

	/**
	 * sizes of popup windows used in the program
	 * @var array
	 */
	private $_windowSizes = array('editurl' => array('x' => 0.75, 'y' => 0.7), 'confirm' => array('x' => 0.5, 'y' => 0.3),
		'import' => array('x' => 0.75, 'y' => 0.50), 'export' => array('x' => 0.75, 'y' => 0.5), 'duplicates' => array('x' => 0.9, 'y' => 0.8),
		'selectredirect' => array('x' => 0.9, 'y' => 0.8), 'enterredirect' => array('x' => 0.75, 'y' => 0.4),
		'configuration' => array('x' => 0.9, 'y' => 0.80), 'need_full_popup' => array('x' => 0.9, 'y' => 0.80));
	/**
	 * Length for modal title trimming
	 * @var array
	 */
	private $_modalTitleSizes = array('configuration' => array('l' => 60, 'i' => 40), 'editurl' => array('l' => 60, 'i' => 40),
		'confirm' => array('l' => 30, 'i' => 20));

	/**
	 * Specifications for user input of meta data
	 * @var array
	 */

	private $_metaDataSpecs = array(
		'metatitle' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60, 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metadesc' => array('maxCharacterSize' => 255, 'warningNumber' => 140, 'errorNumber' => 160, 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metatitle-one-line' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60, 'style' => 'shl-char-counter-one-line',
			'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metatitle-joomla-be' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60,
			'style' => 'shl-char-counter-title-joomla-be', 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metadesc-joomla-be' => array('maxCharacterSize' => 255, 'warningNumber' => 140, 'errorNumber' => 160,
			'style' => 'shl-char-counter-desc-joomla-be', 'title' => 'PLG_SHLIB_CHAR_COUNTER'));

	public function __get($name)
	{

		switch ($name)
		{
			case 'searchEnginesAgents':
			case 'trackingVars':
				$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig(false);
				$prop = '_' . $name;
				$value = empty($remoteConfig->config[$name]) ? $this->$prop : $remoteConfig->config[$name];
				return $value;
				break;
			case 'windowSizes':
			case 'modalTitleSizes':
			case 'metaDataSpecs':
				$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig(false);
				$remotes = empty($remoteConfig->config[$name]) ? array() : $remoteConfig->config[$name];
				$prop = '_' . $name;
				$value = array_merge($this->$prop, $remotes);
				return $value;
				break;
			default:
				$prop = '_' . $name;
				return $this->$prop;
				break;
		}

	}
}
