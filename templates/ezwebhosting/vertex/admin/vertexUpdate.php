<?php 
/**
 * @package     Vertex Framework
 * @author		Shape 5 http://www.shape5.com
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

$dir = dirname(dirname(dirname(__FILE__)));
$xml = simplexml_load_file($dir . '/xml/Vertex.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
$version = $xml->details->frameworkVersion;

$updateUrl = 'http://www.shape5.com/vertex/current_version/vertexVersion.php?version=' . $version;
$file = fopen($updateUrl, "r");
if ($file) {
    while (!feof($file)) {
        $data = fgets($file, 4096);
        print $data;
    }
    fclose($file);
}

?>