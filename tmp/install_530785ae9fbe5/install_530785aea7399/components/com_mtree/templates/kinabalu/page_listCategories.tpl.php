<div id="listCategories">

<!-- <div class="title"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ); ?></div> -->
<?php

$right = array();    
$closing_elements = array();    
$i = 0;

foreach( $this->categories AS $cat )
{
	$k = 0;

	if ( count($right)>0 )
	{
		if( $right[count($right)-1]>=$cat->rgt)
		{
			echo "\n<ul>";
		}
		while ($right[count($right)-1]<$cat->rgt)
		{
			if( $k > 0 )
			{
				echo "\n</ul>";
			}
			echo array_pop($closing_elements);
			array_pop($right);
			$k++;
		}
	}
	if( !empty($right) )
	{
		echo "\n<li>";
		echo '<a href="'.JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$cat->cat_id).'">';
		echo $cat->cat_name;
	} else {
		echo '<h2 class="contentheading">';
		echo $this->header;
	}

	
	if( !empty($right) )
	{
		echo '</a>';
		array_push($closing_elements,"</li>");
	} else {
		echo '</h2>';
	}

	$right[] = $cat->rgt;
	$i++;
}

$k=1;
foreach($right AS $rgt_pop)
{
	echo array_pop($closing_elements);
	if( count($right) > 1 && $k > 0 )
	{
		echo "\n</ul>";
	}
	array_pop($right);
	$k++;
}
?>
</div>