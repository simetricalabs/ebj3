<?php 
JHtml::_('stylesheet',$this->jconf['live_site'].$this->mtconf['relative_path_to_js'].'jquery.fancybox-1.3.4.css');
if ( 
	is_array($this->images) 
	&& 
	!empty($this->images)
	): ?>
<div class="row-fluid">
	<div class="images"><?php 
		if(isset($showImageSectionTitle) && $showImageSectionTitle) { ?>
		<div class="title"><?php echo JText::_( 'COM_MTREE_IMAGES' ); ?> (<?php 
			if ($this->config->getTemParam('skipFirstImage','0') == 1) {
				echo ($this->total_images-1);
			} else {
				echo $this->total_images;
			}
		 ?>)</div><?php } ?>
		<ul class="thumbnails">
		<?php
			$i = 0;
			$totalImages = count($this->images);
			foreach ($this->images AS $image): 
				if( $i == 0 && $this->config->getTemParam('showBigImage','1') == 1 ) 
				{
					?>
					<li class="span12">
						<div class="">
						<img id="mainimage" src="<?php 
					echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_medium_image'] . $image->filename;
				 	?>" alt="<?php echo $image->filename; ?>" />
						</div>
					</li>
					<?php 
					$i++;
					if( $totalImages == 1 )	continue;
				}
			?>
			<li>
				<a class="listingimage" rel="group1" href="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_medium_image'] . $image->filename; ?>">
					<img src="<?php 
						echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_small_image'] . $image->filename;
		 			?>" alt="<?php echo $image->filename; ?>" />
				</a>
			</li>
			<?php 
				$i++;
			endforeach; 
			?>
		</ul>
	</div>
</div>
<script type="text/javascript">
jQuery(function () {
		jQuery("a.listingimage").fancybox({
		'opacity'	: true,
		'overlayShow'	: true,
		'overlayOpacity': 0.7,
		'overlayColor'	: '#fff',
		'transitionIn'	: 'none',
		'transitionOut'	: 'none',
		'changeSpeed'	: '0',
		'padding'	: '0',
		'type'		: 'image',
		'changeFade'	: 0,
		'cyclic'	: true
	});
});
</script>
<?php endif; ?>