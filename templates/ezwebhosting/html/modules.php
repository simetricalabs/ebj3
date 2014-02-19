<?php
/**
 * @version		$Id: modules.php 8255 2007-07-30 23:37:23Z jinx $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

function modChrome_notitle($module, &$params, &$attribs)
{

	$s5_module_title = $module->title;
	
	$responsive_class_placement_beginning = strpos($s5_module_title,"class=");
	$responsive_class = "";
	
	if ($responsive_class_placement_beginning >= 1) {
		$responsive_class = substr($s5_module_title, $responsive_class_placement_beginning + 6, 5000);
		$s5_module_title = substr($s5_module_title, 0, $responsive_class_placement_beginning);
	}
	
	if (!empty ($module->content)) : ?>
		<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?><?php if ($responsive_class != "") { echo ' '.$responsive_class; } ?>">
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}


function modChrome_fourdivs($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : 
	
	$s5_module_title = $module->title;
	
	$responsive_class_placement_beginning = strpos($s5_module_title,"class=");
	$responsive_class = "";
	
	if ($responsive_class_placement_beginning >= 1) {
		$responsive_class = substr($s5_module_title, $responsive_class_placement_beginning + 6, 5000);
		$s5_module_title = substr($s5_module_title, 0, $responsive_class_placement_beginning);
	}
	
	?>
		<div class="s5_fourdivs_1<?php echo $params->get('moduleclass_sfx'); ?><?php if ($responsive_class != "") { echo ' '.$responsive_class; } ?>">
		<div class="s5_fourdivs_2">
		<div class="s5_fourdivs_3">
		<div class="s5_fourdivs_4">
			<?php if ($module->showtitle) { echo '<h3 class="s5_fourdivs_title">'.$s5_module_title.'</h3>'; } ?>
			<?php echo $module->content; ?>
		</div>
		</div>
		</div>
		</div>
	<?php endif;
}


function modChrome_round_box($module, &$params, &$attribs)
{
$suffix = $params->get('moduleclass_sfx');
if ($suffix == "_menu") {
$suffix = "";
}
if ($suffix == "_text") {
$suffix = "";
}

	if (!empty ($module->content)) : 
	
			$s5_module_title = $module->title;
	
			$responsive_class_placement_beginning = strpos($s5_module_title,"class=");
			$responsive_class = "";
			
			if ($responsive_class_placement_beginning >= 1) {
				$responsive_class = substr($s5_module_title, $responsive_class_placement_beginning + 6, 5000);
				$s5_module_title = substr($s5_module_title, 0, $responsive_class_placement_beginning);
			}
			
		?>
	
		<?php if ($module->showtitle) : ?>
			
			<?php 
			
			$s5_h3 = strpos($s5_module_title," ");
			
			if ($s5_h3 != "") {
			$s5_h3_beginning = substr($s5_module_title, 0, $s5_h3);
			$s5_h3_end = substr($s5_module_title, $s5_h3, 500);
			}
			else {
			$s5_h3_beginning = $s5_module_title;
			$s5_h3_end = "";
			}
			
			?>
			
		<?php endif; ?>
		
		<div class="module_round_box_outer<?php if ($responsive_class != "") { echo ' '.$responsive_class; } ?>">
		
		<div class="module_round_box<?php echo $suffix ?>">


				<div class="s5_module_box_1">
					<div class="s5_module_box_2">
						<?php if ($module->showtitle) : ?>
						<div class="s5_mod_h3_outer">
							<h3 class="s5_mod_h3"><span class="s5_h3_first"><?php echo $s5_h3_beginning ?> </span><?php echo $s5_h3_end ?></h3>
						</div>
						<?php endif; ?>
						<?php echo $module->content; ?>
						<div style="clear:both; height:0px"></div>
					</div>
				</div>

			
		</div>
		
		</div>

	<?php endif;
}



function modChrome_title_only($module, &$params, &$attribs)
{
	echo $module->title;
}






?>