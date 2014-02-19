<?php 
/**
 * @package     Vertex Framework
 * @version		1.0
 * @author		Shape 5 http://www.shape5.com
 * @copyright 	Copyright (C) 2007 - 2010 Shape 5, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

$vertex_admin_path = $_GET['path'];
$template_path = $_GET['template_path'];
$script = '';

echo "var vertex_ajax_url = '$vertex_admin_path';";
echo "var img_path = '$vertex_admin_path/df-images';";
echo "var json_path = '$template_path/vertex.json';";
echo "var vertex_version = '2.0.5';";

$script .= require('js/jquery.vertexAdmin.core.min.js');
$script .= require('js/jquery.vertexAdmin.min.js');
echo $script;

?>