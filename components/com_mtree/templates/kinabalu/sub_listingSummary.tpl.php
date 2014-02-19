<?php
$number_of_ls_columns = $this->config->getTemParam('numOfListingColumnsInSummaryView',1);

if( ($number_of_ls_columns >= 2 && ($i % $number_of_ls_columns) == 1) || $number_of_ls_columns == 1 ) {
	echo '<div class="lsrow row-fluid">';
}

?><div class="listing-summary<?php 
	if( $number_of_ls_columns >= 2 ) {
	echo ' ls'.(floor(100/$number_of_ls_columns));
	if($i % $number_of_ls_columns == 0) {
		echo ' column4';
	} else {
		echo ' column'.($i % $number_of_ls_columns);
	}
	
}
echo ' span'.round(12/$number_of_ls_columns);

echo ($link->link_featured && $this->config->getTemParam('useFeaturedHighlight','1')) ? ' featured':'';
?>" data-link-id="<?php echo $link->link_id; ?>">
		<div class="header">
		<h3><?php 
			$link_name = $link_fields->getFieldById(1);
			switch( $this->config->getTemParam('listingNameLink','1') )
			{
				default:
				case 1:
					$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), '', array('delete'=>false, 'edit'=>false) );
					break;
				case 4:
					if( !empty($link->website) ) {
						$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), '', array("edit"=>false,"delete"=>false), 1 );
					} else {
						$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), '', array("edit"=>false,"delete"=>false) );
					}
					break;
				case 2:
					$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), '', array("edit"=>false,"delete"=>false), 1 );
					break;
				case 3:
					$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), 'target="_blank"', array("edit"=>false,"delete"=>false), 1 );
					break;
				case 0:
					$this->plugin( 'ahreflisting', $link, $link_name->getOutput(2), '', array("edit"=>false,"delete"=>false, 'link'=>false) );
					break;
			}
		?></h3><?php
		
		if( $number_of_ls_columns == 1 )
		{
			// Rating
			$this->plugin( 'rating', $link->link_rating, $link->link_votes, $link->attribs);
			
			if( $this->config->get('show_review') )
			{
				echo '<span class="reviews">';
				echo '<a href="' . JRoute::_( 'index.php?option=com_mtree&task=viewlink&link_id=' . $link->link_id . '&Itemid='  . $this->Itemid ) . '">' . $this->reviews_count[$link->link_id]->total . ' ' . strtolower(JText::_( 'COM_MTREE_REVIEWS' )) . '</a>';
				echo '</span>';
			}
			echo '</div>';
		} else {
			echo '</div>';
			// Rating
			echo '<div class="rating-review">';
			$this->plugin( 'rating', $link->link_rating, $link->link_votes, $link->attribs);

			if( $this->config->get('show_review') )
			{
				echo '<span class="reviews">';
				echo '<a href="' . JRoute::_( 'index.php?option=com_mtree&task=viewlink&link_id=' . $link->link_id . '&Itemid='  . $this->Itemid ) . '">' . $this->reviews_count[$link->link_id]->total . ' ' . strtolower(JText::_( 'COM_MTREE_REVIEWS' )) . '</a>';
				echo '</span>';
			}
			echo '</div>';
		}
		
		if ( $this->my->id == $link->user_id && $this->config->get('user_allowmodify') == 1 && $this->my->id > 0 ) {
			?>
			<div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" role="button"> <span class="icon-cog"></span> <span class="caret"></span> </a>
				<ul class="dropdown-menu">
					<li class="edit-icon">
						<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=editlisting&link_id='.$link->link_id); ?>">
							<span class="icon-edit"></span>
							<?php echo JText::_( 'COM_MTREE_EDIT' ); ?>
						</a>
					</li>
					<li class="delete-icon">
						<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=deletelisting&link_id='.$link->link_id); ?>">
							<span class="icon-delete"></span>
							<?php echo JText::_( 'COM_MTREE_DELETE' ); ?>
						</a>
					</li>
				</ul>
			</div>
			<?php
		}

		if( $this->config->getTemParam('showImageInSummary',1) )
		{
			if ($link->link_image ) {
				$this->plugin( 'ahreflistingimage', $link, 'class="image' . (($this->config->getTemParam('imageDirectionListingSummary','left')=='right') ? '':'-left') . '" alt="'.htmlspecialchars($link->link_name).'"' );
			}
			else if ( $this->config->getTemParam('showFillerImage',1) ) 
			{
				?>
				<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewlink&link_id=' . $link->link_id . '&Itemid='  . $this->Itemid); ?>">
				<img src="<?php echo $this->config->getjconf('live_site') . $this->config->get('relative_path_to_images'); ?>noimage_thb.png" width="<?php echo $this->config->get('resize_small_listing_size'); ?>" height="<?php echo $this->config->get('resize_small_listing_size'); ?>" class="image<?php echo (($this->config->getTemParam('imageDirectionListingSummary','left')=='right') ? '':'-left'); ?>" alt="" />
				</a>
				<?php
			}
		}
		
		// Address
		if( $this->config->getTemParam('displayAddressInOneRow','1') ) {
			$address_parts = array();
			foreach( array( 4,5,6,7,8 ) AS $address_field_id )
			{
				$field = $link_fields->getFieldById( $address_field_id );
				if ( isset($field) && ($output = $field->getOutput(1)) )
				{
					$address_parts[] = $output;
				}
			}
			if( !empty($address_parts) ) { echo '<p class="address">' . implode(', ',$address_parts) . '</p>'; }
		}
		
		// Website
		$website = $link_fields->getFieldById(12);
		if(!is_null($website) && $website->hasValue()) { echo '<p class="website">' . $website->getOutput(2) . '</p>'; }

		// Listing's first image
		if(!is_null($link_fields->getFieldById(2)) || $link->link_image) {
			echo '<p>';
			if(!is_null($link_fields->getFieldById(2))) { 
				$link_desc = $link_fields->getFieldById(2);
				echo $link_desc->getOutput(2);
			}
			echo '</p>';
		}
		
		// Listing's category
		if($this->task <> 'listcats' && $this->task <> '' ) {
			echo '<div class="category"><span>' . JText::_( 'COM_MTREE_CATEGORY' ) . ':</span>';
			$this->plugin( 'mtpath', $link->cat_id, '' );
			echo '</div>';
		}
		
		// Other custom field		
		$link_fields->resetPointer();
		echo '<div class="fields">';
		$number_of_columns = $this->config->getTemParam('numOfColumnsInSummaryView','1');
		$field_count = 1;
		$need_div_closure = false;
		while( $link_fields->hasNext() ) {
			$field = $link_fields->getField();
			$value = $field->getOutput(2);
			$hasValue = $field->hasValue();
			if(
				( 
					(
						!$field->hasInputField() && !$field->isCore() && empty($value)) 
						|| 
						(!empty($value) || $value == '0')
					) 
					&&	
					!in_array($field->getId(),array(1,2,12))
					&&
					(
						($this->config->getTemParam('displayAddressInOneRow','1') && !in_array($field->getId(),array(4,5,6,7,8))
						||
						$this->config->getTemParam('displayAddressInOneRow','1') == 0						
					)
					&&
					$hasValue
				)
			) {
				// Start of a row
				if( $number_of_columns == 1 || $field_count % $number_of_columns == 1 ) {
					echo "\n\t\t<div class=\"row-fluid\">";
					$need_div_closure = true;
				}
				echo "\n\t\t\t" . '<div id="field_'.$field->getId().'" class="fieldRow span'.round(12/$number_of_columns).(($number_of_columns == 1 || $field_count % $number_of_columns == 0)?' lastFieldRow':'').'">';

				if($field->hasCaption()) {
					echo "\n\t\t\t\t".'<span class="caption">' . $field->getCaption() . '</span>';
					echo '<span class="output">';
					// Special case to always output Price field's 'display prefix' and 'display suffix' text
					if( $field->getId() == 13 && $field->getValue() > 0 )
					{
						echo $field->getDisplayPrefixText(); 
						echo $field->getOutput(2);
						echo $field->getDisplaySuffixText(); 
					}
					else
					{
						echo $field->getOutput(2);
					}
					echo '</span>';
				} else {
					echo "\n\t\t\t\t".'<span class="output">' . $field->getOutput(2) . '</span>';
				}
				echo "\n\t\t\t".'</div>';

				// End of a row
				if( $number_of_columns == 1 || $field_count % $number_of_columns == 0 ) {
					echo "\n\t\t</div>";
					$need_div_closure = false;
				}
				$field_count++;
			}
			$link_fields->next();
		}
		if( $need_div_closure ) {
			echo "\n\t\t</div>";
			$need_div_closure = false;
		}
		echo '</div>';
		
		if($this->config->getTemParam('showActionLinksInSummary','0')) {
			echo '<div class="actions">';
			$this->plugin( 'ahrefreview', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefrecommend', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefprint', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefcontact', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefvisit', $link, JText::_( 'COM_MTREE_VISIT' ), 1, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefreport', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefclaim', $link, array("rel"=>"nofollow") );
			$this->plugin( 'ahrefownerlisting', $link );
			$this->plugin( 'ahrefmap', $link, array("rel"=>"nofollow") );
			echo '</div>';
		}
?></div><?php

if( ($number_of_ls_columns > 1 && ($i % $number_of_ls_columns == 0 || $i == count($this->links))) || $number_of_ls_columns == 1 ) {
	echo '</div>';
}

?>