<?php
$dir = dirname(dirname(dirname(__FILE__)));
$xml = simplexml_load_file($dir . '/xml/Vertex.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
$version = $xml->details->frameworkVersion;
$updateUrl = 'http://www.shape5.com/vertex/current_version/vertexVersion.php?version=' . $version;
$file = fopen($updateUrl, "r");
$update = false;
$msg = false;
if($file) {
  while(!feof($file)) {
    $data = fgets($file, 4096);
    $update = json_decode($data);
  }
  fclose($file);
}

if($update) {
  $msg = '<span class="about-details" style="display: block;">'.$update->name.'</span>';
  $msg .= '<span class="about-details" style="display: block;">The most current version of Vertex is: '.$update->version.' You are currently running '.$version.'.</span>';
  $msg .= '<span class="about-details" style="display: block;">'.$update->update_link.'</span>';
}
$about_file = '<div class="vItem" style="text-align:center;margin-bottom:20px;"><div id="showVertexMsgWrap"><div id="progressBar" style="display:none;"></div><div id="showVertexMsg">'.($msg ? $msg : '').'</div></div>';
?>