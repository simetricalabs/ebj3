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

// no direct access
defined('_JEXEC') or die('Restricted access');


class TableExtension extends JTable
{
  var $id;
  var $file;
  var $title;
  var $filters;
  var $params;

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function TableExtension(& $db) {
  }

  function store( $updateNulls = false ) {

  }

}
