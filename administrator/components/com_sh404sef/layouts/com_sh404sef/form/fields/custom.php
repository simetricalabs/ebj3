<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date		2014-01-23
 */

defined('JPATH_BASE') or die;

$tip = empty($displayData->tip) ? '' : ' title="' . htmlspecialchars($displayData->label . '::' . $displayData->tip, ENT_COMPAT, 'UTF-8') . '"';
?>

<div class="control-group">
	<?php if (empty($displayData->hidden)): ?>
		<div<?php echo $tip; ?> class="control-label<?php echo empty($displayData->tip) ? '' : ' hasTip'?>">
			<label for="<?php echo $displayData->name; ?>">
			<?php echo $displayData->label; ?>
			</label>
		</div>
	<?php endif; ?>
	<div class="controls">
		<?php echo $displayData->input; ?>
	</div>
</div>