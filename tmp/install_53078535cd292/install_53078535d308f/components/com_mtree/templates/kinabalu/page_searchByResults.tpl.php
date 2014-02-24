<div id="search-by-results" class="mt-template-<?php echo $this->template; ?> cf-id-<?php echo $this->cf_id;?> cat-id-<?php echo $this->cat_id ;?> tlcat-id-<?php echo $this->tlcat_id ;?>"> 
<h2 class="contentheading"><span class="customfieldcaption"><?php echo $this->customfieldcaption; ?>: </span><span class="customfieldvalue"><?php echo $this->searchword; ?></span></h2>

<?php include $this->loadTemplate( 'sub_listings.tpl.php' ) ?>
</div>