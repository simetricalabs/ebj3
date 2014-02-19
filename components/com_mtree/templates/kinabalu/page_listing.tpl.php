<?php 

// Check listing details' access
if( 
	$this->config->getTemParam('limitDetailsViewToRegistered',0) == 0
	||
	(
		$this->config->getTemParam('limitDetailsViewToRegistered',0) == 1
		&&
		$this->my->id > 0
	)
) {

	if( $this->config->getTemParam('listingDetailsStyle',1) == 1 )
	{
		include $this->loadTemplate( 'sub_listingDetails.tpl.php' );
	} 
	else 
	{
		include $this->loadTemplate( 'sub_listingDetailsStyle'.$this->config->getTemParam('listingDetailsStyle',1).'.tpl.php' );
	}

	if ($this->mtconf['use_map']) include $this->loadTemplate( 'sub_map.tpl.php' );

	if ($this->mt_show_review) include $this->loadTemplate( 'sub_reviews.tpl.php' ); 

	if (isset($this->links)) include $this->loadTemplate( 'sub_listings.tpl.php' );	
	
	#
	# Load listing#-footer-modules Modules
	#

	$document	= &JFactory::getDocument();
	$renderer	= $document->loadRenderer('module');

	$contents	= '';

	$modules = JModuleHelper::getModules('listing-footer');
	if( !empty($modules) )
	{
		$contents	.= '<div class="columns1-modules-inner">';
		foreach ($modules as $mod)  {
			$params = new JRegistry( $mod->params );
			$contents .= '<div class="module'.$params->get('moduleclass_sfx').'">';
			$contents .= '<h3>' . $mod->title . '</h3>';
			$contents .= '<div class="triangle"></div>';
			$contents .= $renderer->render($mod);
			$contents .= '</div>';
		}
		$contents	.= '</div>';
	}

	$modules = JModuleHelper::getModules('listing2-footer');
	if( !empty($modules) )
	{
		$contents	.= '<div class="columns2-modules-inner">';
		foreach ($modules as $mod)  {
			$params = new JRegistry( $mod->params );
			$contents .= '<div class="module'.$params->get('moduleclass_sfx').'">';
			$contents .= '<h3>' . $mod->title . '</h3>';
			$contents .= '<div class="triangle"></div>';
			$contents .= $renderer->render($mod);
			$contents .= '</div>';
		}
		$contents	.= '</div>';
	}

	$modules = JModuleHelper::getModules('listing3-footer');
	if( !empty($modules) )
	{
		$contents	.= '<div class="columns3-modules-inner">';
		foreach ($modules as $mod)  {
			$params = new JRegistry( $mod->params );
			$contents .= '<div class="module'.$params->get('moduleclass_sfx').'">';
			$contents .= '<h3>' . $mod->title . '</h3>';
			$contents .= '<div class="triangle"></div>';
			$contents .= $renderer->render($mod);
			$contents .= '</div>';
		}
		$contents	.= '</div>';
	}

	if( !empty($contents) )
	{
		echo '<div class="listing-footer-modules">' . $contents . '</div>';
	}
	
} else {
	echo JText::_( 'COM_MTREE_PLEASE_LOGIN_TO_VIEW_MORE_INFORMATION_ABOUT_THIS_LISTING' );
}
?>