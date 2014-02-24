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
defined( '_JEXEC' ) or die;

class Sh404sefHelperLanguage {

  /**
   * Find a language family
   *
   * @param object $language a Joomla! language object
   * @return string a 2 or 3 characters language family code
   */
  public static function getFamily( $language = null) {


    if (!is_object($language)) {

      // get application db instance
      $language = JFactory::getLanguage();

    }

    $code = $language->get( 'lang');
    $bits = explode( '-', $code);
    return empty($bits[0]) ? 'en' : $bits[0];
  }

}