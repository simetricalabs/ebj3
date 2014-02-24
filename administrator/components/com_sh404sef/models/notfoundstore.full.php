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

/**
 * Stores 404 infos into database
 * 
 *
 */
class Sh404sefModelNotfoundstore
{
	private static $_instance = null;

	/**
	 * Singleton method
	 *
	 * @param string $extension extension name, with com_ - ie com_content
	 * @return object instance of Sh404sefModelCategories
	 */
	public static function getInstance()
	{

		if (is_null(self::$_instance))
		{
			self::$_instance = new Sh404sefModelNotfoundstore();
		}

		return self::$_instance;
	}

	public function store($reqPath, $config)
	{
		// optionnally log the 404 details
		if ($config->shLog404Errors && !empty($reqPath))
		{
			try
			{
				$record = ShlDbHelper::selectObject('#__sh404sef_urls', '*', array('oldurl' => $reqPath));

				if (!empty($record))
				{
					// we have, so update counter
					ShlDbHelper::runQuotedQuery('update ?? set cpt=(cpt+1) where ?? = ?', array('#__sh404sef_urls', 'oldurl'), array($reqPath));
				}
				else
				{
					// record the 404
					ShlDbHelper::insert('#__sh404sef_urls',
						array('cpt' => 1, 'rank' => 0, 'oldurl' => $reqPath, 'newurl' => '', 'dateadd' => ShlSystem_Date::getUTCNow('Y-m-d')));
				}
				// add more details about 404 into security log file
				if ($config->shSecEnableSecurity && $config->shSecLogAttacks)
				{
					$logData = array();
					$logData['DATE'] = ShlSystem_Date::getSiteNow('Y-m-d');
					$logData['TIME'] = ShlSystem_Date::getSiteNow('H:i:s');
					$logData['CAUSE'] = 'Page not found (404)';
					$logData['C-IP'] = empty($_SERVER['REMOTE_ADDR']) ? '-' : $_SERVER['REMOTE_ADDR'];
					if ($_SERVER['REMOTE_ADDR'] != 'localhost' && $_SERVER['REMOTE_ADDR'] != '::1')
					{
						$name = getHostByAddr($_SERVER['REMOTE_ADDR']);
					}
					else
					{
						$name = '-';
					}
					$logData['NAME'] = $name;
					$logData['USER_AGENT'] = empty($_SERVER['HTTP_USER_AGENT']) ? '-' : $_SERVER['HTTP_USER_AGENT'];
					$logData['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
					$logData['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
					$logData['COMMENT'] = '';

					shLogToSecFile($logData);
				}
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, ' Database error: ' . $e->getMessage());
				return false;
			}
		}
		
		return true;
	}
}
