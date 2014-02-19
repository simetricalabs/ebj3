<?php
//Order up :)
$sjs = "va_loader('script', vertex_ajax_url+'/js/jquery.ui.core.min.js', 'bootstrap.min.js');
setTimeout(function(){
va_loader('script', vertex_ajax_url+'/js/jquery.vertexAdmin.core.min.js', 'jquery.ui.core.min.js');
va_loader('script', vertex_ajax_url+'/js/jquery.vertexAdmin.min.js', 'jquery.vertexAdmin.core.min.js');
va_loader('script', vertex_ajax_url+'/spec/JCurrent.js', 'jquery.vertexAdmin.min.js');
},500);";
?>