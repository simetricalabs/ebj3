<?php
/**
 * @package     Vertex Framework
 * @version		1.0
 * @author		Shape 5 http://www.shape5.com
 * @copyright 	Copyright (C) 2007 - 2010 Shape 5, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$dir = dirname(dirname(dirname(dirname(__FILE__))));
if(isset($_POST['style_name'])) {
  ob_end_clean();
  flush();
  include($dir.'/vertex/admin/saveOptions.php');
  exit;
}
jimport('joomla.form.form');
jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield
require(dirname(dirname(__FILE__)).'/vertexFramework.php');
if (file_exists($dir . '/templateDetails.xml')) {
  $template_xml = simplexml_load_file($dir . '/templateDetails.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
  $template_name = $template_xml->name;
} else {
  $template_name = 'blank';
}
define('VERTEX_TEMPLATE_NAME', $template_name);
function vertex_fix($a = false, $b = false) {
  if(!$a || !$b) return;
  //print_r($b);  
}
function getCurrentAlias(){
  //$path = &JFactory::getURI()->getPath();
  //print_r($path);
  //$active = $menu->getActive();
  //return $active->alias;
}
class JFormFieldVertex extends JFormField {
  protected $type = 'Vertex'; //the form field type
  protected function getInput() {
    if(!defined('VERTEX_LOADED')) {
      $vertex_admin_path = JURI::root() . 'templates/' . VERTEX_TEMPLATE_NAME . '/vertex/admin';
      $template_path = JURI::root(true) . '/templates/' . VERTEX_TEMPLATE_NAME;
      $document = JFactory::getDocument();
      $cmsversion = new JVersion();
      $document->addStyleSheet(JURI::root(true) . '/templates/' . VERTEX_TEMPLATE_NAME . '/vertex/admin/vertex.css');
      $document->addScript($vertex_admin_path . '/js/vertexAdmin.Loader.js');
      $session = JFactory::getSession();
      $main_path = dirname(dirname(dirname(dirname(__FILE__))));
      $lang_dir = dirname(dirname($main_path)) . '/language/';
      $Vertex = new vertexAdmin($main_path.'/xml/Vertex.xml', 'sienna', $main_path.'/xml/Specific.xml', $lang_dir, $vertex_admin_path . '/df-images', $cmsversion->RELEASE);
      $Vertex->loadTD();
      vertex_fix('joomla3', $document);
      if($cmsversion->RELEASE <= 2.5) require_once(dirname(dirname(__FILE__)).'/spec/JLegacy.php'); else require_once(dirname(dirname(__FILE__)).'/spec/JCurrent.php');
      $script = "\n";
      $script .= "var vertex_ajax_url = '$vertex_admin_path';";
      $script .= "var img_path = '$vertex_admin_path/df-images';";
      $script .= "var json_path = '$template_path/vertex.json';";
      $script .= "var vertex_version = '2.1';";
      $script .= "var vertex_cmsversion = '$cmsversion->RELEASE';";
      $script .= "var vertexNoAdd = ".json_encode($Vertex->noAdd).";\n";
      $script .= $sjs;
      $document->addScriptDeclaration($script);
      $table = JForm::getFieldsets('adminform');
      $data = null;
      foreach((Array)$this->form as $key => $val) {
        if($val instanceof JRegistry){
          $data = &$val;
          break;
        }
      }
      $title = $data->toArray();
      $title = $title['title'];
      $html = $Vertex->vertexLoadAdmin($title);
      define('VERTEX_LOADED', true);
      // Output
      return $html;
    }
  }
}