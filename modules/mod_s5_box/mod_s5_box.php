<?php
/**
* @title		Shape 5 Box Module
* @version		1.0
* @package		Joomla
* @website		http://www.shape5.com
* @copyright	Copyright (C) 2009 Shape 5 LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$url = JURI::root().'modules/mod_s5_box/';

?>

<script type="text/javascript" src="<?php echo $url ?>js/s5_box_hide_div.js"></script>

<?php

$s5_boxeffect = $params->get( 's5_boxeffect', '' );

$s5boxwidth1	= $params->get( 's5boxwidth1', '' );
$s5boxwidth2	= $params->get( 's5boxwidth2', '' );
$s5boxwidth3	= $params->get( 's5boxwidth3', '' );
$s5boxwidth4	= $params->get( 's5boxwidth4', '' );
$s5boxwidth5	= $params->get( 's5boxwidth5', '' );
$s5boxwidth6	= $params->get( 's5boxwidth6', '' );
$s5boxwidth7	= $params->get( 's5boxwidth7', '' );
$s5boxwidth8	= $params->get( 's5boxwidth8', '' );
$s5boxwidth9	= $params->get( 's5boxwidth9', '' );
$s5boxwidth10	= $params->get( 's5boxwidth10', '' );

require(JModuleHelper::getLayoutPath('mod_s5_box'));


