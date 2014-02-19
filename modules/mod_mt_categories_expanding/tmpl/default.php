<?php defined('_JEXEC') or die('Restricted access');

if( !function_exists('print_cat_recursive') )
{
	function print_cat_recursive( &$all_categories, $category_id, $show_totalcats, $show_totallisting, $hide_active_cat_count, $class_sfx, $task, $itemid ) {
		global $cat_id;
		static $level=0;

		$categories = $all_categories[$category_id];

		if( !empty($categories) )
		{
			echo '<ul class="nav menu' . $class_sfx . '">';

			foreach( $categories AS $cat ) {

				echo '<li';
				if( $cat_id == $cat->cat_id || array_key_exists($cat->cat_id, $all_categories) ) {
					echo ' class="parent active"';
				}
				echo '>';

				echo '<a href="'.JRoute::_("index.php?option=com_mtree&task=$task&cat_id=$cat->cat_id" . $itemid).'">';
				echo htmlspecialchars($cat->cat_name);
				if ( $hide_active_cat_count && array_key_exists($cat->cat_id, $all_categories) ) {
					//hide
				} else {
					if ( $show_totalcats xor $show_totallisting ) {
						echo " <small>(".(($show_totalcats)? $cat->cat_cats:$cat->cat_links ).")</small>";
					} elseif( $show_totalcats && $show_totallisting ) {
						echo " <small>(".$cat->cat_cats."/".$cat->cat_links.")</small>";
					}
				}
				echo "</a>";

				if(array_key_exists($cat->cat_id, $all_categories))  {
					$level++;
					print_cat_recursive($all_categories, $cat->cat_id, $show_totalcats, $show_totallisting, $hide_active_cat_count, $class_sfx, $task, $itemid);
					$level--;
				}

				echo "</li>";

			}

			echo "</ul>";
		}
		return true;
	}	
}

print_cat_recursive($categories, 0, $show_totalcats, $show_totallisting, $hide_active_cat_count, $class_sfx, $task, $itemid);

?>