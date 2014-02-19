<?php

$dir = dirname(dirname(__FILE__));
function json2Array($json) {return json_decode($json, 1);}
function getCurrentAlias(){
  $active = JFactory::getApplication()->getMenu()->getActive();
  if($active) {
    return ($active->alias);
  }
}

global $s5_vertex_version, $s5_template_name;
$s5_vertex_version = '1.0';

function getTemplateName($template) {
  $db = JFactory::getDBO();
  $query = "SELECT * FROM #__template_styles WHERE template = '$template';";
  $db->setQuery($query);
  $result = $db->loadAssocList();
  $titles = array();
  $i = 0;
  $home_id = 0;
  foreach($result as $k => $style) {
    $ids[] = $style['id'];
    $titles[$style['id']] = $style['title'];
    if($style['home'] == 1) {
      $home_id = $style['id'];
    }
    $i++;
  }
  $ids = implode(",", $ids);
  $u_main = JURI::root();
  $u = JFactory::getURI();
  $u = $u->toString();
  $u = explode($u_main, $u);
  $com = $u[1];
  $alias = getCurrentAlias();
  $query2 = "SELECT * FROM #__menu WHERE template_style_id IN (". $ids .") AND alias = '$alias'";
  $db->setQuery($query2);
  $result2 = $db->loadAssocList();
  $i2 = 0;
	$id = 0;
  //print_r($result2);
  foreach($result2 as $k => $item) {
    if($item['link'] == $com || preg_match("/" . $item['alias'] . "/i", $alias)) {
      $id = $item['template_style_id'];
      $i2++;
    }
  }
  //print_r($id);
  if($i2 == 0) {
    $id = $home_id;
  }
  //print_r($titles[$id]);
  return $titles[$id];
}

function getVersion($dir) {
  global $s5_vertex_version;
  if(file_exists($dir . '/xml/Specific.xml')) {
    $templateXml = simplexml_load_file($dir . '/xml/Specific.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
    $s5_vertex_version = $templateXml->version;
  } else {
    exit('Template XML file not found');
  }
}

function getJoomlaParams($dir = false) {
  $admin_options = array();
  if(file_exists($dir . '/xml/Specific.xml')) {
    $templateXml = simplexml_load_file($dir . '/xml/Specific.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
    $fieldsets = $templateXml->config->fields;
    foreach($fieldsets->fieldset as $key => $fieldset){
      foreach($fieldset as $key => $item){
        $admin_options[(string)$item['name'][0]] = (string)$item['default'][0];
      }
    }
  } else {
    exit('Template XML file not found');
  }
  return $admin_options;
}

function getVertexParams($dir = false){
  if(file_exists($dir . '/xml/Vertex.xml')) {
    $vertexXml = simplexml_load_file($dir . '/xml/Vertex.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
    $admin_options = array();
    $fieldsets = $vertexXml->admin;
    foreach($fieldsets->fieldset as $key => $fieldset){
      foreach($fieldset as $key => $item){
        $admin_options[(string)$item['name'][0]] = (string)$item['default'][0];
      }
    }
  } else {
    exit('Vertex XML file not found');
  }
  return $admin_options;
}

function handleJSONFile($file = false, $style = false, $dir = false) {
  getVersion($dir);
  $config = array();
  if(file_exists($dir . '/' . $file)) {
    $check = file_get_contents($dir . '/' . $file);
    $file_data = json2Array($check);
    if(isset($file_data['vertexFramework'][$style]) && is_array($file_data['vertexFramework'][$style])) {
      $defaults = getJoomlaParams($dir);
      $defaults = array_merge($defaults, getVertexParams($dir));
      foreach($defaults as $key => $val){
        $key = str_replace('xml_', '', $key);
        $config[$key] = '';
      }
      foreach($file_data['vertexFramework'][$style] as $key => $val){
        $key = str_replace('xml_', '', $key);
        $config[$key] = preg_replace('/\\\/', '', $val);
      }
    } else {
      $defaults = getJoomlaParams($dir);
      $defaults = array_merge($defaults, getVertexParams($dir));
      foreach($defaults as $key => $val){
        $key = str_replace('xml_', '', $key);
        $config[$key] = preg_replace('/\\\/', '', $val);
      }
    }
  } else {
    $defaults = getJoomlaParams($dir);
    $defaults = array_merge($defaults, getVertexParams($dir));
    foreach($defaults as $key => $val){
      $key = str_replace('xml_', '', $key);
      $config[$key] = preg_replace('/\\\/', '', $val);
    }
  }
  return $config;
}

if (file_exists($dir . '/templateDetails.xml')) {
  $template_xml = simplexml_load_file($dir . '/templateDetails.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
  $template_name = $template_xml->name;
  $template_date = $template_xml->creationDate;
} else {
  $template_name = 'blank';
}

$style_name = getTemplateName($template_name);
$file = 'vertex.json';
$params = handleJSONFile($file, $style_name, $dir);

foreach($params as $k => $v){$$k = $v;}

$s5_lr_tab1_text = str_replace(" ","&nbsp;",$s5_lr_tab1_text);
$s5_lr_tab2_text = str_replace(" ","&nbsp;",$s5_lr_tab2_text);	
$s5_urlforSEO = $s5_seourl;

if ($s5_fstyle != "") {
$s5_fstyle = ":".$s5_fstyle;
}

if ($s5_fstyle_responsive_mobile_bar != "") {
$s5_fstyle_responsive_mobile_bar = ":".$s5_fstyle_responsive_mobile_bar;
}

$s5_fonts_with_style = $s5_fonts.$s5_fstyle;
$s5_fonts_responsive_mobile_bar_with_style = $s5_fonts_responsive_mobile_bar.$s5_fstyle_responsive_mobile_bar;

$s5_fonts_with_style = str_replace(" ","+",$s5_fonts_with_style);
$s5_fonts_responsive_mobile_bar_with_style = str_replace(" ","+",$s5_fonts_responsive_mobile_bar_with_style);	

if ($s5_columns_fixed_fluid == "") {
$s5_columns_fixed_fluid = "px";
}

?>