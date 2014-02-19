<?php 
/**
 * @package     Vertex Framework
 * @version		1.0
 * @author		Shape 5 http://www.shape5.com
 * @copyright 	Copyright (C) 2007 - 2010 Shape 5, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
define('PHPEXT', '.php');
define('JSONEXT', '.json');
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../'));
if ( !defined( 'DS')) define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_BASE .DS.'includes'.DS.'defines.php');
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php');
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();
$session =& JFactory::getSession();
function array2Json($array) {return json_encode($array);}
function json2Array($json) {return json_decode($json, 1);}
function buildArray($style, $data) {return array($style => $data);}
function runCron(){
    $file = '../../vertex' . JSONEXT;
    $jsonData = array();
    $jsonData['vertexFramework'] = array();
    $currentValues = array();
    $check = '';
    $cronned = 0;
    if(file_exists($file)) {
        $check = file_get_contents($file);
        $file_data = json2Array($check);
        foreach($file_data['vertexFramework'] as $key => $data){
            $db = JFactory::getDBO();
            $query = "SELECT * FROM #__template_styles WHERE title = '$key';";
            $db->setQuery($query);
            $result = $db->loadAssocList();
            foreach($result as $k => $style) {
                if(isset($style['title'])) {
                    $jsonData['vertexFramework'][$key] = $data;
                } else {
                    unset($file_data['vertexFramework'][$key]);
                    $cronned++;
                }
            }
        }
        $data = array2Json($jsonData);
        $file = fopen($file, 'w');
        fwrite($file, $data);
        fclose($file);
    }
    $msg = array('message' => false);
    if($cronned) {
        $msg = array('message' => "$cronned items have been cleaned up");
    }
    return array2Json($msg);
}
header('Content-type: application/json');
print runCron();
?>