<?php

require('vertexFramework.php');

$Vertex = new vertexAdmin(false, $_POST['style'], false, false, $_POST['image_path']);
$Vertex->vertexLoadScript($Vertex->templatePath, 'admin');
$Vertex->vertexLoadCss($Vertex->templatePath, 'admin');

$Vertex->vertexLoadAdmin($_POST['style_name']);

?>