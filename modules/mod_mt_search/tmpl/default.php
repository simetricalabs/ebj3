<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" class="form-inline">
	<div class="search<?php echo $moduleclass_sfx; ?>">

		<div class="control-group">
			<div class="controls">
				<input type="text" id="mod_mt_search_searchword<?php echo $parent_cat_id; ?>" name="searchword" maxlength="<?php echo $mtconf->get('limit_max_chars'); ?>" class="search-query small" size="<?php echo $width; ?>" value="<?php echo $searchword; ?>"  placeholder="<?php echo $placeholder_text; ?>" style="width:auto" />
			</div>
		</div>

		<?php if( $lists['categories'] ) { ?>
			<div class="control-group">
				<div class="controls">
					<?php echo $lists['categories']; ?>
				</div>
			</div>
		<?php
		} ?>
	
		<?php if ( $search_button ) { ?>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn"><?php echo JText::_( 'MOD_MT_SEARCH_SEARCH' ) ?></button>
				</div>
			</div>
		<?php } ?>

		<?php if ( $advsearch ) { ?>
			<div class="control-group">
				<div class="controls">
					<a href="<?php echo $advsearch_link; ?>"><?php echo JText::_( 'MOD_MT_SEARCH_ADVANCED_SEARCH' ) ?></a>
				</div>
			</div>
		<?php } ?>
		<input type="hidden" name="option" value="com_mtree" />
		<input type="hidden" name="task" value="search" />
		<?php if ( $searchCategory == 1 ) { ?>
		<input type="hidden" name="search_cat" value="1" />
		<?php } ?>
	</div>
</form>