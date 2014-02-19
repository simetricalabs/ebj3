<?php
//Order up :)
$sjs = "window.onload = function() {va_loader('script', vertex_ajax_url+'/js/jquery.min.js', 'template.css');
va_loader('script', vertex_ajax_url+'/js/jquery.ui.core.min.js', 'jquery.min.js');
va_loader('script', vertex_ajax_url+'/js/jquery.vertexAdmin.core.min.js', 'jquery.ui.core.min.js');
va_loader('script', vertex_ajax_url+'/js/jquery.vertexAdmin.min.js', 'jquery.vertexAdmin.core.min.js');
setTimeout(function(){va_loader('script', vertex_ajax_url+'/spec/JLegacy.js', 'jquery.vertexAdmin.min.js');},1000);}";
?>