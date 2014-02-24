<?php if(!empty($this->searchword)): ?>
<h2 class="contentheading"><?php echo JText::sprintf( 'COM_MTREE_SEARCH_RESULTS_FOR_KEYWORD', $this->searchword ) ?></h2>
<?php endif; ?>

<?php include $this->loadTemplate( 'sub_subCats.tpl.php' ) ?>

<?php include $this->loadTemplate( 'sub_listings.tpl.php' ) ?>