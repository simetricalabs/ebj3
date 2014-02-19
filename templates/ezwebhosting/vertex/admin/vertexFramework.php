<?php defined('_JEXEC') or die('Restricted access');
error_reporting(0);
define('PHPEXT', '.php');
define('JSONEXT', '.json');
define('VERTEX_PATH', dirname(__file__));
define('BASE_PATH', dirname(dirname(dirname(__file__))));
define('SYS_PATH', dirname(dirname(dirname(dirname(dirname(__file__))))));

function cLog($msg) {
  print "<script type='text/javascript'>console.log('$msg');</script>";
}
function getLanguage() {
  $user = &JFactory::getUser();
  $language = $user->getParam('admin_language', 'en-GB');
  return $language;
}

class FileFilter {
  private $bext = false;
  private $ff = false;
  private $noout = array('.', '..', '.htaccess');
  public $array = array();
  private $list = array();
  public function __construct($dir = array(), $bext = false, $ff = false) {
    if(!$dir) return false;
    if(is_array($dir)) $this->list = $dir;
    else $this->list[] = $dir;
    $this->bext = $bext;
    $this->ff = $ff;
    foreach($this->list as $d) if(is_dir($d)) foreach(scandir($d) as $f) array_push($this->array, $d.$f);
    if(version_compare(PHP_VERSION, '5.3', '>')) $this->array = array_filter($this->array, $this);
    else $this->array = array_filter($this->array, array($this, '__invoke'));
  }
  public function isDir($i) {
    if($this->ff) return (is_dir($this->dir.DIRECTORY_SEPARATOR.$i) ? true : false);
    return true;
  }
  public function byExt($i) {
    if($this->bext && $this->bext != pathinfo($i, PATHINFO_EXTENSION)) return false;
    return true;
  }
  function __invoke($i) {
    if(in_array($i, $this->noout) || $this->isDir($i) == false) return false;
    return $this->byExt($i);
  }
}

class vertexDefaults {
  var $specific_form;
  var $vertex_form;
  //Main
  var $templateName = '';
  var $templatePath = '';
  var $details = array();
  var $idPrefix = 'pane_';
  var $adminLayout = '';
  var $templateXml = '';
  var $vertexXml = '';
  var $vertexVersion = '0';
  var $cmsVersion = '';
  var $img_path = '';
  var $vLang = array();
  var $vMenu = array();
  var $count = 0;
  var $form = '<div id="{ID}_form_in" class="vertex-admin-form">{DATA}</div>';
  var $submit = '<input class="v-submit" type="submit" value="Submit" />';
  var $wrapper = '<div id="{ID}" class="{CLASS}">{DATA}</div>';
  var $menu = '<ul class="{CLASS}">{MENU}</ul>';
  var $fieldsetWrap = '<div class="{CLASS}" id="{ID}"><h2 class="{CLASS2}">{TITLE}</h2>{DATA}</div>';
  var $itemTemplate = '<div class="vItem"><div class="vItemName">{LABEL}</div><div class="vItemValue">{ITEM}</div></div>';
  var $currentValue = array();
  var $googleFonts;
  var $defaults = array();
  var $language = 'en-GB';
  var $headScript = '';
  var $headCss = '';
  var $preloaded = array();
  var $JSONWRITEERROR = false;
  var $noAdd = array();
  function loadDefaults() {
    $this->defaults['panel_wrap'] = 'vertex-admin-wrap';
    $this->defaults['panel'] = 'vertex-admin-panel';
    $this->defaults['panel_title'] = 'vertex-admin-panel-title';
    $this->defaults['top_menu'] = 'vertex-admin-menu';
    $this->defaults['main_menu'] = 'fader-tabs';
    $this->language = getLanguage();
  }
  function setVersion() {
    //if($this->vertexVersion != '') $_GLOBALS['vertexVersion'] = $this->vertexVersion;
    //print_r('<script type="text/javascript">alert("'.$_GLOBALS['vertexVersion'].'")<\/script>');
  }
  function setCmsVersion($v = false) {
    if($v && $this->cmsVersion == '') {
      $this->cmsVersion = $v;
    }
  }
}
class vertexCore extends vertexDefaults {
  function buildSpacer($spacer) {
    $build = '';
    $name = $spacer['name'];
    $type = explode(':', $spacer['type']);
    $label = $spacer['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $build .= '<div class="v-display ' . $type[0] . ' ' . (isset($type[1]) ? $type[1] : 'notice') . '"><img src="' . $this->img_path . '/' . (isset($type[1]) ? $type[1] : 'notice') . '.png" alt="' . ucfirst((isset($type[1]) ? $type[1] : 'notice')) . '" /><div>' . $label . '</div></div>';
    return $build;
  }
  function buildSelect($select, $stype) {
    $build = '';
    $name = $select['name'];
    $type = explode(':', $select['type']);
    $desc = $select['description'];
    $options = $select['option'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $select['default'];
    $value = explode(',', $value);
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $select['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $tmpSelect = '<select id="' . $name . '" name="' . $name . '"' . ((isset($type[1]) ? $type[1] : '') == 'multible' ? ' multiple="multiple"' : '') . '>';
    if (!preg_match("/fonts/i", $name)) {
      foreach ($select->option as $option) {
        $val = array();
        if ($stype == false) {
          $val = explode(':', $option);
        } else {
          $val[0] = (string )$option['value'];
          $val[1] = (string )$option;
          if (preg_match("/TPL/i", $val[1])) {
            $val[1] = isset($this->vLang["$val[1]"]) ? $this->vLang["$val[1]"] : $val[1];
          }
          $val2 = $val[1];
        }
        $tmpSelect .= '<option value="' . $val[0] . '"' . (in_array($val[0], $value) ? ' selected="selected"' : '') . '>' . $val2 . '</option>';
      }
    } else {
      foreach ($this->googleFonts as $key => $font) {
        if (preg_match("/TPL/i", $font)) {
          $font = isset($this->vLang["$font"]) ? $this->vLang["$font"] : $font;
        }
        $tmpSelect .= '<option value="' . $key . '"' . (in_array($key, $value) ? ' selected="selected"' : '') . '>' . $font . '</option>';
      }
    }
    $tmpSelect .= '</select>';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $tmpSelect, $build);
    return $build;
  }
  function buildExtraItem($item, $stype) {
    //var_dump($item);
    $build = '';
    $name = $item['name'];
    $desc = $item['description'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $item['default'];
    $sl = (string )isset($item['sl']) ? $item['sl'] : 'json';
    //$vars = isset($item['vars']) ? (string)$item['vars'] : false;
    //print_r('VARS1');
    //print_r($vars);
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $item['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '" class="' . $sl . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $el = $this->specific_form->getInput($name, null, $value);
    if (!$el) $el = $this->vertex_form->getInput($name, null, $value);
    if (!$el) $el = '';
    $tmpSelect = $el;
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $tmpSelect, $build);
    return $build;
  }
  function buildMenuSelect($select, $stype) {
    $build = '';
    $name = $select['name'];
    $type = explode(':', $select['type']);
    $desc = $select['description'];
    $options = $select['option'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $select['default'];
    $value = explode(',', $value);
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $select['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $tmpSelect = '';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $tmpSelect, $build);
    return $build;
  }
  function buildMenuItems($select, $stype) {
    $build = '';
    $name = $select['name'];
    $type = explode(':', $select['type']);
    $desc = $select['description'];
    $options = $select['option'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $select['default'];
    $value = explode(',', $value);
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $select['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $tmpSelect = '';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $tmpSelect, $build);
    return $build;
  }
  function buildRadio($radio) {
    $build = '';
    $name = $radio['name'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $radio['default'];
    $type = explode(':', $radio['type']);
    $desc = $radio['description'];
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $radio['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $vars = $radio['vars'];
    $vars = explode('|', $vars);
    $vals = explode(':', $vars[0]);
    $labels = explode(':', $vars[1]);
    if (preg_match("/TPL/i", $labels[0])) {
      $labels[0] = isset($this->vLang["$labels[0]"]) ? $this->vLang["$labels[0]"] : $labels[0];
    }
    if (preg_match("/TPL/i", $labels[1])) {
      $labels[1] = isset($this->vLang["$labels[1]"]) ? $this->vLang["$labels[1]"] : $labels[1];
    }
    $tmpRadio = '<input id="' . $name . '" type="' . $type[0] . '" name="' . $name . '" value="' . $vals[0] . '" ' . ($value == $vals[0] ? ' checked="checked"' : '') . ' /> ' . $labels[0] . '
        <input type="' . $type[0] . '" name="' . $name . '" value="' . $vals[1] . '"' . ($value == $vals[1] ? ' checked="checked"' : '') . '/> ' . $labels[1] . '';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $tmpRadio, $build);
    return $build;
  }
  function buildText($text) {
    $build = '';
    $name = $text['name'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $text['default'];
    $type = explode(':', $text['type']);
    $desc = $text['description'];
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $text['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $input = '<input id="' . $name . '" name="' . $name . '" type="' . $type[0] . '" size="' . (isset($type[1]) ? $type[1] : '30') . '" maxlength="' . (isset($type[2]) ? $type[2] : '200') . '" value="' . $value . '" />';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $input, $build);
    return $build;
  }
  function buildTextarea($textarea) {
    $build = '';
    $name = $textarea['name'];
    $value = isset($this->currentValue["$name"]) ? $this->currentValue["$name"] : $textarea['default'];
    $cols = isset($textarea['cols']) ? $textarea['cols'] : '10';
    $rows = isset($textarea['rows']) ? $textarea['rows'] : '10';
    $desc = $textarea['description'];
    if (preg_match("/TPL/i", $desc)) {
      $desc = isset($this->vLang["$desc"]) ? $this->vLang["$desc"] : $desc;
    }
    $label = $textarea['label'];
    if (preg_match("/TPL/i", $label)) {
      $label = isset($this->vLang["$label"]) ? $this->vLang["$label"] : $label;
    }
    $label = str_replace('_', ' ', $label);
    $label = '<label for="' . $name . '">' . $label . '</label><span class="vFloatDesc">' . $desc . '</span>';
    $input = '<textarea id="' . $name . '" name="' . $name . '" cols="' . $cols . '" rows="' . $rows . '">' . $value . '</textarea>';
    $tmp = $this->itemTemplate;
    $build = str_replace('{LABEL}', $label, $tmp);
    $build = str_replace('{ITEM}', $input, $build);
    return $build;
  }
  function vertexBuildAbout($title) {
    $html = str_replace('{TITLE}', $title, $this->fieldsetWrap);
    $html = str_replace('{CLASS}', $this->defaults['panel'], $html);
    $html = str_replace('{CLASS2}', $this->defaults['panel_title'], $html);
    $html = str_replace('{ID}', 'pane_about', $html);
    require ('vertexAbout' . PHPEXT);
    $html = str_replace('{DATA}', $about_file, $html);
    return $html;
  }
}
class vertexAdmin extends vertexCore {
  function __construct($vertexXML = false, $templateName = false, $templateXML = false, $templatePath = false, $img_path = false, $cmsv = false) {
    if ($vertexXML) {
      $this->vertexXml = $vertexXML;
    }
    if ($templateName) {
      $this->templateName = $templateName;
    }
    if ($templatePath) {
      $this->templatePath = $templatePath;
    }
    require ('googleFonts' . PHPEXT);
    $this->googleFonts = $googleFonts;
    if ($templateXML) {
      $this->templateXml = $templateXML;
    }
    if($img_path) {
      $this->img_path = $img_path;
    }
    if($cmsv) $this->setCmsVersion($cmsv);
    $this->loadDefaults();
  }
  function get_first($arr) {
    foreach ($arr as $k => $v) {
      return $v;
    }
  }
  function handleSaveFile($file = false, $style = false) {
    $file = $file . JSONEXT;
    if (!is_writable($file)) $this->JSONWRITEERROR = $file;
    if (file_exists($file)) {
      $check = file_get_contents($file);
      $file_data = $this->json2Array($check);
      if (is_array($file_data) && isset($file_data['vertexFramework'][$style])) {
        foreach ($file_data['vertexFramework'][$style] as $key => $val) {
          $this->currentValue[$key] = preg_replace('/\\\/', '', $val);
        }
      } else {
        $data = $this->get_first($file_data['vertexFramework']);
        foreach ($data as $key => $val) {
          $this->currentValue[$key] = preg_replace('/\\\/', '', $val);
        }
      }
    }
  }
  function object2Array($object) {
    return @json_decode(@json_encode($object), 1);
  }
  function array2Json($array) {
    return @json_encode($array);
  }
  function json2Array($json) {
    return json_decode($json, 1);
  }
  function parseLang($file) {
    $file = file($file);
    foreach ($file as $item) {
      if (@preg_match('/(.+)="(.+)"/', $item, $matches)) {
        $this->vLang[$matches[1]] = $matches[2];
      }
    }
  }
  function admin_lang($lang = false) {
    $def_lang = 'en-GB';
    if(!$lang) $lang = $def_lang;
    if($lang == $def_lang || !$lang) {
      $dir = array(SYS_PATH . '/language/'.$def_lang.'/', BASE_PATH . '/xml/language/'.$def_lang.'/');
      $files = new FileFilter($dir, 'ini');
      foreach($files->array as $file) $this->parseLang($file);
    } elseif($lang != false && $lang != '') {
      $dir = array(SYS_PATH . '/language/'.$def_lang.'/', BASE_PATH . '/xml/language/'.$def_lang.'/', SYS_PATH . '/language/'.$lang.'/', BASE_PATH . '/xml/language/'.$lang.'/');
      $files = new FileFilter($dir, 'ini');
      foreach($files->array as $file) $this->parseLang($file);
    }
  }
  function loadTD() {
    $path = dirname(dirname(dirname(__file__)));
    $loadType = (function_exists('simplexml_load_file') ? 'load_file' : (function_exists('simplexml_load_string') ? 'load_string' : false));
    if (is_file($path . '/templateDetails.xml')) {
      $xmlFile = false;
      if ($loadType == 'load_file') $xmlFile = simplexml_load_file($path . '/templateDetails.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
      elseif ($loadType == 'load_string') {
        $xmlFile = file_get_contents($path . '/templateDetails.xml');
        $xmlFile = simplexml_load_string($xmlFile, 'SimpleXMLElement', LIBXML_NOCDATA);
      }
      if ($xmlFile) {
        foreach ($xmlFile->xpath('config/fields/fieldset') as $fieldset) {
          foreach ($fieldset->children() as $field) {
            $this->noAdd[] = (string )$field['name'];
          }
        }
        unset($xmlFile);
      }
    }
  }
  function getJoomlaParams() {
    if (file_exists($this->templateXml)) {
      $this->specific_form = &JForm::getInstance('specific', $this->templateXml, array(), true, 'config/fields');
      //print_r($this->specific_form);
      $this->templateXml = simplexml_load_file($this->templateXml, 'SimpleXMLElement', LIBXML_NOCDATA);
      $this->vertexVersion = $this->templateXml->version;
      $this->setVersion();
      $i = 0;
      $i2 = 0;
      $admin_options = array();
      $fieldsets = $this->templateXml->config->fields;
      $htmlData = '';
      foreach ($fieldsets->fieldset as $key => $fieldset) {
        $version = isset($fieldset['version']) ? explode(',', $fieldset['version']) : false;
        $op = (string )isset($item['version'][0]) ? vertexFunctions::operand((string )$item['version'][0]) : '<=';
        $version = (string )isset($version[1]) ? $version[1] : $this->vertexVersion;
        if (!$version || version_compare($this->vertexVersion, $version, $op)) {
          $tmp = '';
          $html = '';
          $arraykey = 'vertex_page' . $i;
          $tab_title = $fieldset['label'];
          if (preg_match("/TPL/i", $tab_title)) {
            $tab_title = isset($this->vLang["$tab_title"]) ? $this->vLang["$tab_title"] : $tab_title;
          }
          $k = $this->idPrefix . strtolower(str_replace(' ', '_', $tab_title));
          $this->vMenu[$k] = $tab_title;
          $html = str_replace('{TITLE}', $tab_title, $this->fieldsetWrap);
          $html = str_replace('{CLASS}', $this->defaults['panel'], $html);
          $html = str_replace('{CLASS2}', $this->defaults['panel_title'], $html);
          $html = str_replace('{ID}', $k, $html);
          $admin_options[$arraykey] = array();
          $admin_options[$arraykey]['title'] = $tab_title;
          $admin_options[$arraykey]['id'] = $arraykey;
          foreach ($fieldset as $key => $item) {
            $admin_options[$arraykey]['items'][] = $this->vertexBuildAdmin($item, true);
            $tmp .= $this->vertexBuildAdmin($item, true);
          }
          $html = str_replace('{DATA}', $tmp, $html);
          $htmlData .= $html;
          $i++;
        }
      }
      $this->count = $i;
    } else {
      exit('Template XML file not found');
    }
    return $htmlData;
  }
  function vertexLoadAdmin($style = 'default') {
    if (file_exists($this->vertexXml)) {
      $this->vertex_form = &JForm::getInstance('vertex', $this->vertexXml, array(), true, 'admin');
      //print_r($this->vertex_form);
      $this->vertexXml = simplexml_load_file($this->vertexXml, 'SimpleXMLElement', LIBXML_NOCDATA);
      $this->details = $this->vertexXml->details;
      $this->admin_lang($this->language);
      $dir = dirname(dirname(dirname(__file__)));
      $this->handleSaveFile($dir . '/vertex', $style);
      $i = 0;
      $i2 = 0;
      $admin_options = array();
      $fieldsets = $this->vertexXml->admin;
      $htmlTmp = '';
      $htmlData = '';
      $htmlTmp .= $this->getJoomlaParams();
      if ($this->count > 0) {
        $i = $this->count;
      }
      foreach ($fieldsets->fieldset as $key => $fieldset) {
        $version = isset($fieldset['version']) ? explode(',', $fieldset['version']) : false;
        $op = (string )isset($version[0]) ? vertexFunctions::operand((string )$version[0]) : '<=';
        $version = (string )isset($version[1]) ? $version[1] : $this->vertexVersion;
        $html = '';
        if (!$version || version_compare($this->vertexVersion, $version, $op)) {
          $tmp = '';
          $arraykey = 'vertex_page' . $i;
          $tab_title = $fieldset['label'];
          if (preg_match("/TPL/i", $tab_title)) {
            $tab_title = isset($this->vLang["$tab_title"]) ? $this->vLang["$tab_title"] : $tab_title;
          }
          $k = $this->idPrefix . strtolower(str_replace(' ', '_', str_replace(':', '', $tab_title)));
          $this->vMenu[$k] = $tab_title;
          $html = str_replace('{TITLE}', $tab_title, $this->fieldsetWrap);
          $html = str_replace('{CLASS}', $this->defaults['panel'], $html);
          $html = str_replace('{CLASS2}', $this->defaults['panel_title'], $html);
          $html = str_replace('{ID}', $k, $html);
          $admin_options[$arraykey] = array();
          $admin_options[$arraykey]['title'] = $tab_title;
          $admin_options[$arraykey]['id'] = $arraykey;
          foreach ($fieldset as $key => $item) {
            $admin_options[$arraykey]['items'][] = $this->vertexBuildAdmin($item, true);
            $tmp .= $this->vertexBuildAdmin($item, true);
          }
          $html = str_replace('{DATA}', $tmp, $html);
          $i++;
        }
        $htmlTmp .= $html;
      }
      $htmlData = str_replace('{CLASS}', $this->defaults['panel_wrap'], $this->wrapper);
      $htmlData = str_replace('{ID}', 'vertex_fader', $htmlData);
      $this->vMenu['pane_about'] = 'About';
      $htmlTmp .= $this->vertexBuildAbout('About');
      $menus = $this->vertexBuildMenus();
      $error = '';
      if ($this->JSONWRITEERROR) $error = $this->buildSpacer(array(
          'name' => 'jsonError',
          'type' => 'spacer:error',
          'label' => "$this->JSONWRITEERROR is not writable. Please ensure that it is writable before saving. You may need to increase the chmod permission settings to 775 of this file, you can do this through any standard ftp agent."));
      $htmlData = str_replace('{DATA}', $error . $menus . $htmlTmp, $htmlData);
      $htmlForm = str_replace('{ID}', 'vertex_admin', $this->form);
      $htmlForm = str_replace('{DATA}', $htmlData, $htmlForm);
    } else {
      exit('Vertex XML file not found');
    }
    if ($version->RELEASE >= '3.0') {
      $htmlForm .= '</div>';
    }
    $this->vertexDisplayAdmin($htmlForm);
    return $this->adminLayout;
  }
  function vertexBuildMenus() {
    $menu_built = '';
    $menu1 = '';
    $menu2 = '';
    $i = 0;
    foreach ($this->vMenu as $k => $item) {
      $k = str_replace(':', '', $k);
      if ($i < 6) {
        $menu1 .= "<li class='panel-tab'><a href='#$k' class='a-tab'>$item</a></li>";
      } else
        if ($i > 5) {
          $menu2 .= "<li class='panel-tab'><a href='#$k' class='a-tab'>$item</a></li>";
        }
      $i++;
    }
    $menu3 = str_replace('{CLASS}', $this->defaults['top_menu'], $this->menu);
    $menu3 = str_replace('{MENU}', $menu1, $menu3);
    $menu4 = str_replace('{CLASS}', $this->defaults['main_menu'], $this->menu);
    $menu4 = str_replace('{MENU}', $menu2, $menu4);
    $menu_built = $menu3 . $menu4;
    return $menu_built;
  }
  function vertexBuildAdmin($item, $stype) {
    $item_built = '';
    $version = (isset($item['version']) ? explode(',', $item['version']) : false);
    $op = isset($version[0]) ? vertexFunctions::operand($version[0]) : '<=';
    $version = (string )isset($version[1]) ? $version[1] : $this->vertexVersion;
    if (!$version || version_compare((string )$this->vertexVersion, (string )$version, (string )$op)) {
      $match = array_search((string )$item['name'], $this->noAdd);
      if ($match) unset($this->noAdd[$match]);
      $parsed = false;
      if (!$parsed && preg_match("/spacer/i", $item['type'])) {
        $item_built .= $this->buildSpacer($item);
        $parsed = true;
      }
      if (!$parsed && preg_match("/text/i", $item['type']) && !preg_match("/textarea/i", $item['type'])) {
        $item_built .= $this->buildText($item);
        $parsed = true;
      }
      if (!$parsed && preg_match("/textarea/i", $item['type'])) {
        $item_built .= $this->buildTextarea($item);
        $parsed = true;
      }
      if (!$parsed && preg_match("/radio/i", $item['type'])) {
        $item_built .= $this->buildRadio($item);
        $parsed = true;
      }
      if (!$parsed && preg_match("/\bselect/i", $item['type'])) {
        $item_built .= $this->buildSelect($item, $stype);
        $parsed = true;
      }
      //if(preg_match("/menuitems/i", $item['type'])) {
      //$item_built .= $this->buildMenuItems($item, $stype);
      //$parsed = true;
      //}
      //if(preg_match("/menu_list/i", $item['type'])) {
      //$item_built .= $this->buildMenuSelect($item, $stype);
      //$parsed = true;
      //}
      if (!$parsed) $item_built .= $this->buildExtraItem($item, $stype);
    }
    return $item_built;
  }
  function vertexDisplayAdmin($htmlWrap) {
    print ($htmlWrap);
  }
}
class vertexFunctions {
  function json2Array($json) {
    return json_decode($json, 1);
  }
  function cacheFile($file, $data) {
    $error = new vertexError();
    $check = new fileAccess($file);
    if ($check) {
      $file = fopen($file, 'w');
      if ($file) {
        fwrite($file, $data);
      } else {
        $error->show_error('The admin could not be cached due to a write error. Please make this path writable for enhanced speed (' . $file . ')');
      }
      fclose($file);
    }
  }
  function checkCache($file) {
    if (file_exists($file)) {
      if (filemtime($file) > strtotime('-1 month', time())) return true;
      return false;
    }
    return false;
  }
  function operand($v) {
    $r = 'lt';
    switch ($v) {
      case 'lt':
        $r = '<';
        break;
      case 'le':
        $r = '<=';
        break;
      case 'gt':
        $r = '>';
        break;
      case 'ge':
        $r = '>=';
        break;
      case 'eq':
        $r = '=';
        break;
      case 'ne':
        $r = '!=';
        break;
    }
    return $r;
  }
} ?>