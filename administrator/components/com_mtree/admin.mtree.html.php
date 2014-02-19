<?php
/**
 * @version	$Id: admin.mtree.html.php 2115 2013-10-17 01:40:19Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

class HTML_mtree {

	/***
	* Left Navigation
	*/
	function print_style() {
		global $mtconf;
	?>
	<style type="text/css">
		a.mt_menu {
			font-weight: bold;
			text-decoration: none;
		}
		a.mt_menu:hover {
			font-weight: bold;
			text-decoration: underline;
		}
		a.mt_menu_selected {
			font-weight: bold;
			color: #515151;
			text-decoration: none;
			font-size: 12px;
		}
		a.mt_menu_selected:hover {
			text-decoration: underline;
			font-weight: bold;
			color: #515151;
			font-size: 12px;
		}
		ul.linkcats{
			margin:0px;
			padding:0;
		}
		ul.linkcats > li:first-child
		{
		font-weight:bold;
		}
		ul.linkcats li {
			margin:0;
			padding:0;
			list-style:none;
			padding:0 0 3px 0;
		}
		ul.linkcats img {margin-right:4px;}
		ul.linkcats a {text-decoration:underline;margin-right:3px;}
		.icon-48-mosetstree {background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>mosetstree-icon.png) no-repeat left;}
	</style>
	<?php
	}

	function print_startmenu( $task, $cat_parent ) {
		global $mtconf;
		
		$app		= JFactory::getApplication();
		$database	=& JFactory::getDBO();
		$template	= $app->getTemplate(true)->template;
		
		# Count the number of pending links/cats/reviews/reports/claims
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_cats WHERE cat_approved='0'" );
		$pending_cats = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_links WHERE link_approved <= 0" );
		$pending_links = $database->loadResult();
	
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews WHERE rev_approved='0'" );
		$pending_reviews = $database->loadResult();
	
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reports WHERE rev_id = 0 && link_id > 0" );
		$pending_reports = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews WHERE ownersreply_text != '' AND ownersreply_approved = '0'" );
		$pending_reviewsreply = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reports WHERE rev_id > 0 && link_id > 0" );
		$pending_reviewsreports = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_claims" );
		$pending_claims = $database->loadResult();

		HTML_mtree::print_style();

	?>
	<div class="row-fluid">
		<div class="span2">
			<ul class="nav nav-list">

				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_TITLE' ) ?></li>
				<?php if (!$mtconf->get('admin_use_explorer')) { ?>
				<li<?php echo ($task=="listcats" || $task=="editcat" || $task=="") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listcats"><?php echo JText::_( 'COM_MTREE_NAVIGATE_TREE' ) ?></a>
				</li>
				<?php } ?>
				<li<?php echo ($task=="newlink") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&amp;task=newlink&amp;cat_parent=<?php echo $cat_parent ?>"><?php echo JText::_( 'COM_MTREE_ADD_LISTING' ) ?></a>
				</li>
				<li<?php echo ($task=="newcat") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&amp;task=newcat&amp;cat_parent=<?php echo $cat_parent ?>"><?php echo JText::_( 'COM_MTREE_ADD_CAT' ) ?></a>
				</li>
				
				<li class="divider"></li>
				
				<?php 
				# Pending Approvals
				if ( 
					($pending_links > 0)
					OR
					($pending_cats > 0)
					OR
					($pending_reviews > 0)
					OR
					($pending_reports > 0)
					OR
					($pending_reviewsreply > 0)
					OR
					($pending_reviewsreports > 0)
					OR
					($pending_claims > 0)
				 ) { 
				?>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_PENDING_APPROVAL' ) ?></li>
				
				<?php if ( $pending_cats > 0 ) { ?>
				<li<?php echo ($task=="listpending_cats") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_cats"><span class="badge"><?php echo $pending_cats; ?></span> <?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_links > 0 ) { ?>
				<li<?php echo ($task=="listpending_links") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_links"><span class="badge"><?php echo $pending_links; ?></span> <?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_reviews > 0 ) { ?>
				<li<?php echo ($task=="listpending_reviews") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_reviews"><span class="badge"><?php echo $pending_reviews; ?></span> <?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_reports > 0 ) { ?>
				<li<?php echo ($task=="listpending_reports") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_reports"><span class="badge"><?php echo $pending_reports; ?></span> <?php echo JText::_( 'COM_MTREE_REPORTS' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_reviewsreply > 0 ) { ?>
				<li<?php echo ($task=="listpending_reviewsreply") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_reviewsreply"><span class="badge"><?php echo $pending_reviewsreply; ?></span> <?php echo JText::_( 'COM_MTREE_OWNERS_REPLIES' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_reviewsreports > 0 ) { ?>
				<li<?php echo ($task=="listpending_reviewsreports") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_reviewsreports"><span class="badge"><?php echo $pending_reviewsreports; ?></span> <?php echo JText::_( 'COM_MTREE_REVIEWS_REPORTS' ) ?></a>
				</li>
				<?php 
				}

				if ( $pending_claims > 0 ) { ?>
				<li<?php echo ($task=="listpending_claims") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=listpending_claims"><span class="badge"><?php echo $pending_claims; ?></span> <?php echo JText::_( 'COM_MTREE_CLAIMS' ) ?></a>
				</li>
				<li class="divider"></li>
				<?php 
				}

				}
				# End of Pending Approvals
				
				# dtree
				
				 # This Directory
				if ( $task == 'listcats' || $task == 'editcat' || $task == 'editcat_browse_cat' || $task == 'editcat_add_relcat' || $task == 'editcat_remove_relcat' ) {
					if($cat_parent > 0) {
						# Lookup all information about this directory
						$thiscat = new mtCats( $database );
						$thiscat->load( $cat_parent );

				?>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_THIS_CATEGORY' ) ?></li>
				<li>
				<?php
					$tcat = new mtDisplay();
					$tcat->add(JText::_( 'COM_MTREE_NAME' ), '<a href="index.php?option=com_mtree&task=editcat&cat_id=' . $thiscat->cat_id . '&cat_parent=' . $thiscat->cat_parent . '">' . $thiscat->cat_name . '</a>');
					$tcat->add( JText::_( 'COM_MTREE_CAT_ID' ), $thiscat->cat_id );
					$tcat->add( JText::_( 'COM_MTREE_LISTINGS' ), $thiscat->cat_links);
					$tcat->add( JText::_( 'COM_MTREE_CATEGORIES' ), $thiscat->cat_cats);
					$tcat->add( JText::_( 'COM_MTREE_RELATED_CATEGORIES2' ), $thiscat->getNumOfRelCats() );
					$tcat->add( JText::_( 'COM_MTREE_PUBLISHED' ), JHtml::_('jgrid.published', $thiscat->cat_published, '', '', false) );
					$tcat->add( JText::_( 'COM_MTREE_FEATURED' ), JHtml::_('mtree.featured', $thiscat->cat_featured, '', '', false) );
					$tcat->display();
				?>
				</li>
				<li class="divider"></li>
				<?php
					}
				# This Listing
				} elseif( $task == 'editlink' || $task == 'editlink_change_cat' || $task == 'reviews_list' || $task == 'newreview' || $task == 'editreview' || $task == 'editlink_browse_cat' || $task == 'editlink_add_cat' || $task == 'editlink_remove_cat' ) {
					global $link_id;

					if ( $link_id[0] > 0 ) {
						$thislink = new mtLinks( $database );
						$thislink->load( $link_id[0] );

						$database->setQuery( 'SELECT COUNT(*) FROM #__mt_reviews WHERE link_id = ' . $database->quote($link_id[0]) . ' AND rev_approved = 1' );
						$reviews = $database->loadResult();
						?>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_THIS_LISTING' ) ?></li>
				<li>
				<?php
					$tlisting = new mtDisplay();
					$tlisting->add(JText::_( 'COM_MTREE_NAME' ), '<a href="index.php?option=com_mtree&task=editlink&link_id=' . $thislink->link_id . '">' . $thislink->link_name . '</a>');
					$tlisting->add( JText::_( 'COM_MTREE_LISTING_ID' ), $thislink->link_id );
					$tlisting->add( JText::_( 'COM_MTREE_CATEGORY' ), '<a href="index.php?option=com_mtree&task=listcats&cat_id=' . $thislink->cat_id . '">' . $thislink->getCatName() . '</a>');
					$tlisting->add( JText::_( 'COM_MTREE_REVIEWS' ), '<a href="index.php?option=com_mtree&task=reviews_list&link_id=' . $thislink->link_id . '">' . $reviews . '</a>');
					$tlisting->add( JText::_( 'COM_MTREE_HITS' ), $thislink->link_hits );
					$tlisting->add( JText::_( 'COM_MTREE_MODIFIED2' ), tellDateTime($thislink->link_modified) );
					$tlisting->display();
				?>
				</li>
				<li class="divider"></li>
				<?php
					}
				}
				
				 # dTree
				if ($mtconf->get('admin_use_explorer')) {
				?>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_EXPLORER' ) ?></li>
				<li>
				<?php

				$cats = HTML_mtree::getChildren( 0, $mtconf->get('explorer_tree_level') );
				?>
				<link rel="StyleSheet" href="components/com_mtree/dtree.css" type="text/css" />
				<script type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>dtree.js"></script>
				<script type="text/javascript">
					<!--
					
					fpath = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif';
					d = new dTree('d');

					d.config.closeSameLevel = true; 

					d.icon.root = '..<?php echo $mtconf->get('relative_path_to_images'); ?>house.png',
					d.icon.folder = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif',
					d.icon.folderOpen = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif',
					d.icon.node = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/page.gif',
					d.icon.empty = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/empty.gif',
					d.icon.line = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/line.png',
					d.icon.join = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/join.png',
					d.icon.joinBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/joinbottom.png',
					d.icon.plus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/plus.png',
					d.icon.plusBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/plusbottom.png',
					d.icon.minus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/minus.gif',
					d.icon.minusBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/minusbottom.gif',
					d.icon.nlPlus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/nolines_plus.gif',
					d.icon.nlMinus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/nolines_minus.gif'

					d.add(0,-1,'<?php echo JText::_( 'COM_MTREE_ROOT' ) ?>', 'index.php?option=com_mtree');
					<?php
					foreach( $cats AS $cat ) {
							echo "\nd.add(";
							echo $cat->cat_id.",";
							echo $cat->cat_parent.",";
							
							// Print Category Name
							echo "'".addslashes(htmlspecialchars($cat->cat_name, ENT_QUOTES ));
							echo "',";

							echo "pp(".$cat->cat_id."),";
							echo "'','',";
							echo "fpath";
							echo ");";
					}
					?>
					document.write(d);
					
					function pp(cid) {
						return 'index.php?option=com_mtree&task=listcats&cat_id='+cid;
					}
					//-->
				</script>

				</li>
				<?php
					}

				# End of  dTree

				// Search
				$search_text 	= JFactory::getApplication()->input->get( 'search_text', '');
				$search_where	= JFactory::getApplication()->input->getInt( 'search_where', 1); // 1: Listing, 2: Category
				
				?>
				<li class="divider"></li>
				<li>
					<form action="index.php" method="post" class="form-inline" id="mtSearchForm">
				
					<div class="input-append">
						<input type="text" name="search_text" size="10" maxlength="250" value="<?php echo $search_text; ?>" placeholder="<?php echo ($search_where==1)?JText::_( 'COM_MTREE_SEARCH_LISTINGS' ):JText::_( 'COM_MTREE_SEARCH_CATEGORIES' ); ?>" class="span10"/>
						<div class="btn-group">
							<button class="btn"><i class="icon-search"></i></button>
							<button class="btn dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li>
									<a href="javascript:var mtsearch=document.forms.mtSearchForm;mtsearch.search_where.value=1;mtsearch.submit();">
										<?php echo JText::_( 'COM_MTREE_SEARCH_LISTINGS' )?>
										<?php if ($search_where==1){ ?>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-checkmark"></i>
										<?php } ?>
									</a>
								</li>
								<li>
									<a href="javascript:var mtsearch=document.forms.mtSearchForm;mtsearch.search_where.value=2;mtsearch.submit();">
										<?php echo JText::_( 'COM_MTREE_SEARCH_CATEGORIES' )?>
										<?php if ($search_where==2){ ?>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-checkmark"></i>
										<?php } ?>
									</a>
								</li>
								<li class="divider"></li>
								<li><a href="index.php?option=com_mtree&task=advsearch"><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCH_SHORT' ) ?></a></li>
							</ul>
						</div>
					</div>
					
					<input type="hidden" name="option" value="com_mtree" />
					<input type="hidden" name="task" value="search" />
					<input type="hidden" name="search_where" value="<?php echo $search_where; ?>" />
					<input type="hidden" name="limitstart" value="0" />
					</form>
				</li>

				<li class="divider"></li>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_MORE' ) ?></li>
				<li>
					<a class="mt_menu" href="index.php?option=com_mtree&task=spy">
					<span class="icon-eye"></span>
					<?php echo JText::_( 'COM_MTREE_SPY_DIRECTORY' );?>
					</a>
				</li>
				<li<?php echo ($task=="config") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=config">
						<span class="icon-cog"></span>
						<?php echo JText::_( 'COM_MTREE_CONFIGURATION' ) ?>
					</a>
				</li>
				<li<?php echo ($task=="templates") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=templates">
						<span class="icon-color-palette"></span>
						<?php echo JText::_( 'COM_MTREE_TEMPLATES' ) ?>
					</a>
				</li>
				<li<?php echo ($task=="customfields") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=customfields">
						<span class="icon-cube"></span>
						<?php echo JText::_( 'COM_MTREE_CUSTOM_FIELDS' ) ?>
					</a>
				</li>
				<li<?php echo ($task=="tools") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=tools">
						<span class="icon-tools"></span>
						<?php echo JText::_( 'COM_MTREE_TOOLS' ) ?>
					</a>
				</li>
				<li<?php echo ($task=="about") ? ' class="active"': ''; ?>>
					<a class="mt_menu" href="index.php?option=com_mtree&task=about">
						<span class="icon-bookmark"></span>
						<?php echo JText::_( 'COM_MTREE_ABOUT_MOSETS_TREE' ) ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="span10">
		<?php 
	}

	function print_endmenu() {	
	?>
		</div>
	</div>
	<?php
	}

	function getChildren( $cat_id, $cat_level ) {
		global $mtconf;

		$database	=& JFactory::getDBO();
		$cat_ids	= array();

		if ( $cat_level > 0  ) {

			$sql = "SELECT cat_id, cat_name, cat_parent, cat_cats, cat_links FROM #__mt_cats AS cat WHERE cat_published=1 && cat_approved=1 && cat_parent= " . $database->quote($cat_id) . ' ';
			
			if ( !$mtconf->get('display_empty_cat') ) { 
				$sql .= "&& ( cat_cats > 0 || cat_links > 0 ) ";	
			}

			if( $mtconf->get('first_cat_order1') != '' )
			{
				$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
				if( $mtconf->get('second_cat_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
				}
			}

			$database->setQuery( $sql );
			$cat_ids = $database->loadObjectList();

			if ( count($cat_ids) ) {
				foreach( $cat_ids AS $cid ) {
					$children_ids = HTML_mtree::getChildren( $cid->cat_id, ($cat_level-1) );
					$cat_ids = array_merge( $cat_ids, $children_ids );
				}
			}
		}

		return $cat_ids;

	}

	/***
	* Link
	*/
	function editLink( &$row, $fields, $images, $cat_id, $other_cats, &$lists, $number_of_prev, $number_of_next, &$pathWay, $returntask, &$form, $option, $activetab=0 ) {
		global $mtconf;

		JFilterOutput::objectHTMLSafe( $row );
		$editor = &JFactory::getEditor();
		?>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>addlisting.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>jquery-ui-1.8.24.custom.min.js"></script>
		<?php if( $mtconf->get( 'use_map' ) ) { 
		?><script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>map.js"></script><?php
		}
		?>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var JURI_ROOT='<?php echo JURI::root(); ?>';

		var active_cat=<?php echo $cat_id; ?>;
		var attCount=0;
		var validations=[];
		var cachedFields;
		<?php
		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			if($field->hasJSValidation() && $field->hasInputField()) {
				echo "\n";
				echo 'validations[\''.$field->getInputFieldID().'\']='.$field->getJSValidation().';';
			}
			$fields->next();
		}
		?>
		jQuery(document).ready(function(){
			<?php
			$fields->resetPointer();
			while( $fields->hasNext() ) {
				$field = $fields->getField();
				if($field->hasJSOnInit()) {
					echo "\n";
					echo $field->getJSOnInit().';';
				}
				$fields->next();
			}
			?>	
		});
		function addAtt() {
			attCount++;
			var newLi = document.createElement("LI");
			newLi.id="att"+attCount;
			var newFile=document.createElement("INPUT");
			newFile.className="";
			newFile.name="image[]";
			newFile.type="file";
			newFile.size="28";
			newFile.multiple=true;
			newLi.appendChild(newFile);
			var newLink=document.createElement("A");
			newLink.href="javascript:remAtt("+attCount+")";
			removeText=document.createTextNode("<?php echo JText::_('Remove') ?>");
			newLink.appendChild(removeText);
			newLi.appendChild(newLink);
			gebid('upload_att').appendChild(newLi);
		}
		function remAtt(id) {gebid('upload_att').removeChild(gebid('att'+id));attCount--;}
		Joomla.submitbutton = function(task)
		{
			var form = document.adminForm;
			var scroll = new Fx.SmoothScroll({links:'adminForm',wheelStops:false})
			if (task == 'cancellink') {
				Joomla.submitform(task, document.getElementById('adminForm'));
				return;
			}
			
			<?php
			$fields->resetPointer();
			while( $fields->hasNext() ) {
				$field = $fields->getField();
				if($field->hasJSOnSave()) {
					echo "\n";
					echo $field->getJSOnSave().';';
				}
				$fields->next();
			}
			?>
			
			var validation_failed = false;
			var validation_fields = jQuery('#adminForm .controls input, #adminForm .controls textarea, #adminForm .controls select');
			if(validation_fields.length>0)
			{
				for(var index=0;index<validation_fields.length;index++)
				{
					var id=validation_fields[index].id;
					// Validate required fields
					if(
						(
							validation_fields[index].required !== false 
							&& 
							typeof(validation_fields[index].required) != 'undefined' 
							&& 
							!mtValidateIsEmpty(validation_fields[index])
						)
						||
						!mtValidate(validation_fields[index])
					){
						validation_failed=true;
						addValidationErrorHighlight(id.slice(2).split('_').shift().toInt());
						scroll.toElement(id);
						jQuery('#validate-advice-'+id).fadeToggle('fast').fadeToggle('slow');
						validation_fields[index].focus();
					}else{
						removeValidationErrorHighlight(validation_fields[index].id.slice(2).split('_').shift().toInt());
					}
				}
			}

			if(validation_failed) {return false;}

			if(jQuery('#mapcon').css('display') == 'none') {
				document.adminForm.lat.value=0;
				document.adminForm.lng.value=0;
				document.adminForm.zoom.value=0;
			}

			var hash = jQuery("#sortableimages").sortable('serialize');
			if(hash != ''){document.adminForm.img_sort_hash.value=hash;}
			if(attCount>0 && checkImgExt(attCount,jQuery("input:file[name|='image[]']"))==false) {
				alert('<?php echo addslashes(JText::_( 'COM_MTREE_PLEASE_SELECT_A_JPG_PNG_OR_GIF_FILE_FOR_THE_IMAGES' )) ?>');
				return;
			} else {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			return;
		}
		</script>
		
	<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<center>
	<?php
		if ( $row->link_approved <= 0 ) {

			?>
			<table cellpadding="0" cellspacing="0" border="0" class="toolbar">
			<tr height="60" valign="middle" align="center">
			<?php

			if ( $number_of_prev > 0 ) {
			?>
			<td class="button">
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:Joomla.submitbutton('prev_link');">
					<span class="icon-32-back" title="<?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?>"></span>
					<b> (<?php echo $number_of_prev ?>) <?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?></b>
				</a>
				</div>
			</td>
			<?php
			} else {
			?>
			<td class="button">
				<div class="toolbar-list">
				<span class="icon-32-back" title="<?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?>"></span>
				<b><font color="#C0C0C0"> (0) <?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?></font></b>
				</div>
			</td>
			<?php
			}
			?>
			<td>
				<fieldset style="padding: 5px; border: 1px solid #c0c0c0">
					<input style="float:none;display:inline" type="radio" name="act" id="act_ignore" value="ignore" checked="checked" /><label style="float:none;display:inline" for="act_ignore"><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
					<input style="float:none;display:inline" type="radio" name="act" id="act_approve" value="approve" /><label style="float:none;display:inline" for="act_approve"><?php echo JText::_( 'COM_MTREE_APPROVE' ) ?></label>
					<input style="float:none;display:inline" type="radio" name="act" id="act_discard" value="discard" /><label style="float:none;display:inline" for="act_discard"><?php echo JText::_( 'COM_MTREE_REJECT' ) ?></label>
				</fieldset>
			</td>
			<?php 

			if ( $number_of_next > 0 ) {
			?>
			<td class="button">
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:Joomla.submitbutton('next_link');">
					<span class="icon-32-forward" title="<?php echo JText::_( 'COM_MTREE_NEXT' ) ?>"></span>
					<b><?php echo JText::_( 'COM_MTREE_NEXT' ) ?> (<?php echo $number_of_next ?>) </b>
				</a>
				</div>
			</td>
			<?php
			} else {
			?>
			<td>
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:Joomla.submitbutton('next_link');">
					<span class="icon-32-save" title="<?php echo JText::_( 'COM_MTREE_SAVE' ) ?>"></span>
					<strong><?php echo JText::_( 'COM_MTREE_SAVE' ) ?></strong>
				</a>
				</div>
			</td>
			<?php
			}
			?>
			</tr>
			</table>
			<?php
		}
	?>
	</center>

	<h4>
	<img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif" style="position:relative;top:-2px;left:1px"/>
	<?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, 'index.php?option=com_mtree&task=listcats' ); ?>
	<?php if( !empty($row->link_name) ) {echo $row->link_name;} ?>
	</h4>

	<div class="row-fluid">
	<div class="span9 form-horizontal">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'listing-details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'listing-details', JText::_('COM_MTREE_TEM_LISTING_DETAILS', true)); ?>
		
		<fieldset class="adminform">
				<div class="control-group">
					<div class="control-label">
						<label for="browsecat"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></label>
					</div>
					<div class="controls">
						<ul class="linkcats" id="linkcats">
						<li id="lc<?php echo $cat_id; ?>"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, '' ); ?></li>
						<?php
						if ( !empty($other_cats) ) {
							foreach( $other_cats AS $other_cat ) {
								if ( is_numeric( $other_cat ) ) {
									echo '<li id="lc' . $other_cat . '"><a href="javascript:remSecCat('.$other_cat.')">'.JText::_( 'COM_MTREE_REMOVE' ).'</a>'. $pathWay->printPathWayFromCat_withCurrentCat( $other_cat, '' ) . '</li>';
								}
							}
						}
						?>
						</ul>
						<a href="#" onclick="javascript:togglemc();return false;" id="lcmanage"><?php echo JText::_( 'COM_MTREE_MANAGE' ); ?></a>
						<div id="mc_con" style="display:none">
						<span id="mc_active_pathway" style="float:left;padding: 1px 0pt 1px 3px; background-color: white; width: 98%;position:relative;top:4px;height:13px;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, '' ); ?></span>
						<?php echo $lists["cat_id"]; ?>
						<button type="button" class="btn" id="mcbut1" onclick="updateMainCat()" style="float:left;clear:left"><?php echo JText::_( 'COM_MTREE_UPDATE_CATEGORY' ) ?></button>
						<button type="button" class="btn" id="mcbut2" onclick="addSecCat()" style="float:left"><?php echo JText::_( 'COM_MTREE_ALSO_APPEAR_IN_THIS_CATEGORIES' ) ?></button>
						</div>
					</div>
				</div>
				<div id="mtfields">
			<?php
			$field_link_desc = $fields->getFieldById(2);
			$fields->resetPointer();
			while( $fields->hasNext() ) {
				$field = $fields->getField();
				if($field->hasInputField() && !in_array($field->name,array('metakey','metadesc'))) {
				?>
				<div class="control-group <?php echo $field->getFieldTypeClassName(); ?>" id="field_<?php echo $field->getId(); ?>">
					<?php
					if($field->hasCaption()) {
					?>
					<div class="control-label" id="caption_<?php echo $field->getId(); ?>">
						<label for="<?php echo $field->getInputFieldId(); ?>">
							<?php echo $field->getCaption(); ?>
							<?php if($field->isRequired()) { ?>
							<span class="star">&#160;*</span>
							<?php } ?>
						</label>
					</div>
					<?php } ?>
					<div class="controls" id="input_<?php echo $field->getId(); ?>">
						<?php
						echo $field->getModPrefixText();
						echo $field->getInputHTML();
						echo $field->getModSuffixText();
						?>
					</div>
				</div>
				<?php
				}
				$fields->next();
			}
			?>
				</div>
		</fieldset>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'listing-images', JText::_('COM_MTREE_IMAGES', true)); ?>
		<style>
		#listing-images ol {
			margin:0;
			left:0;
		}
		#sortableimages li {
			position:relative;
			left:-13px;
			margin: 0 0 13px 0;
			padding: 0px; 
			float: left; 
			list-style-position: outside;
			line-height: 100%;
		}
		</style>
		<fieldset class="adminform">
				<p style="color:#666"><?php echo JText::_( 'COM_MTREE_DRAG_TO_SORT_IMAGES_DESELECT_CHECKBOX_TO_REMOVE' ); ?></p>

				<ul style="list-style-type: none; 
			margin: 10px 0 0 0;
			padding: 0;
			width: auto;
			overflow: visible;" id="sortableimages"><?php
			foreach( $images AS $image ) {
				echo '<li id="img_' . $image->img_id . '">';
				echo '<input style="position:relative;
				left: 20px;
				top: 10px;
				vertical-align: top;
				z-index: 1;
				margin: 0;
				padding: 0;" type="checkbox" name="keep_img[]" value="' . $image->img_id . '" checked />';
				echo '<a href="' . $mtconf->getjconf('live_site');
				switch( $mtconf->get('small_image_click_target_size','o') )
				{
					case 'm':
						echo $mtconf->get('relative_path_to_listing_medium_image');
						break;
					default:
					case 'o':
						echo $mtconf->get('relative_path_to_listing_original_image');
						break;
					case 's':
						echo $mtconf->get('relative_path_to_listing_small_image');
						break;
				}
				echo $image->filename . '" target="_blank">';
				echo '<img border="0" style="position:relative;border:1px solid black;" align="middle" src="' . $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_listing_small_image') . $image->filename . '" alt="' . $image->filename . '" />';
				echo '</a>';
				echo '</li>';
			}
			?>
			</ul>
			<ol id="upload_att" style="overflow:hidden;
			clear: both;
			list-style-type: none;
			margin: 0;
			padding: 0;">
			</ol>
			<div style="margin: 10px 0 10px 2px;">
				<button type="button" class="btn btn-small" onclick="javascript:addAtt();" id="add_att"><?php echo JText::_( 'COM_MTREE_ADD_AN_IMAGE' ) ?></button>
			</div>
		</fieldset>

		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php 
		if( $mtconf->get( 'use_map' ) ) {
			echo JHtml::_('bootstrap.addTab', 'myTab', 'listing-map', JText::_('COM_MTREE_MAP', true));
		?>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MTREE_MAP' ); ?></legend>
			<div id="mapcon">
			<?php
			$width = '100%';
			$height = '200px';
			?>
			<script src="http://maps.googleapis.com/maps/api/js?v=3.6&amp;sensor=false" type="text/javascript"></script>
			<script type="text/javascript">
				var map = null;
			    	var geocoder = null;
				var marker = null;
				var infowindow = null;
				var defaultCountry = '<?php echo addslashes($mtconf->get( 'map_default_country' )); ?>';
				var defaultState = '<?php echo addslashes($mtconf->get( 'map_default_state' )); ?>';
				var defaultCity = '<?php echo addslashes($mtconf->get( 'map_default_city' )); ?>';
				var defaultLat = '<?php echo addslashes($mtconf->get('map_default_lat')); ?>';
				var defaultLng = '<?php echo addslashes($mtconf->get('map_default_lng')); ?>';
				var defaultZoom = '<?php echo addslashes($mtconf->get('map_default_zoom')); ?>';
				var linkValLat = '<?php echo $row->lat; ?>';
				var linkValLng = '<?php echo $row->lng; ?>';
				var linkValZoom = '<?php echo $row->zoom; ?>';
			</script>
			<div id="mapContainer">
			<div style="padding:4px 0; width:95%"><input style="float:none" type="button" onclick="locateInMap()" value="<?php echo JText::_( 'COM_MTREE_LOCATE_IN_MAP' ); ?>" name="locateButton" id="locateButton" /><span style="padding:0px; margin:3px" id="map-msg"></span></div>
			<div id="map" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>"></div>
			</div>
			<input type="hidden" id="lat" name="lat" value="<?php echo $row->lat; ?>" />
			<input type="hidden" id="lng" name="lng" value="<?php echo $row->lng; ?>" />
			<input type="hidden" id="zoom" name="zoom" value="<?php echo $row->zoom; ?>" />
			<input type="hidden" id="show_map" name="show_map" value="<?php echo $row->show_map; ?>" />
			</div>
			<a id="togglemap" href="#" onclick="javascript:toggleMap();"><? echo JText::_('COM_MTREE_REMOVE_MAP'); ?></a>
		</fieldset>

		<?php 
			echo JHtml::_('bootstrap.endTab');
		}
		?>
		
		<?php 
		$fieldsets = array('publishing', 'parameters', 'notes');
		$sidebar_fields = array( 
			'publishing' => array('link_approved', 'link_published', 'link_featured')
			);
		
		foreach( $fieldsets AS $fieldset )
		{
			echo JHtml::_('bootstrap.addTab', 'myTab', 'listing-'.$fieldset, JText::_('COM_MTREE_LISTING_'.strtoupper($fieldset), true));
			?>
			<fieldset class="adminform">
			<?php
			foreach($form->getFieldset($fieldset) AS $field):

				if( 
					array_key_exists($fieldset, $sidebar_fields) 
					&& 
					in_array( str_replace(array($fieldset.'[',']'), '', $field->name), $sidebar_fields[$fieldset]) 
				) { continue; }
			?>
			<div class="control-group">
				<?php if ($field->hidden): ?>
					<?php echo $field->input;?>
				<?php else: ?>
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls">
					<?php echo $field->input;?>
				</div>
				</tr>
				<?php endif;?>
			</div>
			<?php endforeach; ?>
			</fieldset>
			<?php
			echo JHtml::_('bootstrap.endTab');
		}
		?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>
	<div class="span3">
		<fieldset class="adminform form-vertical">
		<?php
		foreach( $sidebar_fields AS $fieldset => $fields )
		{
			foreach($form->getFieldset($fieldset) AS $field)
			{
				if( in_array(str_replace(array($fieldset.'[',']'), '', $field->name), $fields) )
				{
					?>
					<div class="control-group">
						<?php if ($field->hidden): ?>
							<?php echo $field->input;?>
						<?php else: ?>
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls">
							<?php echo $field->input;?>
						</div>
						</tr>
						<?php endif;?>
					</div>
					<?php					
				}
			}
		}
		?>
		</div>
	</div>
	</div>
	
	<input type="hidden" name="img_sort_hash" value="" />
	<input type="hidden" name="link_id" value="<?php echo $row->link_id; ?>" />
	<input type="hidden" name="original_link_id" value="<?php echo $row->original_link_id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="editlink" />
	<input type="hidden" name="returntask" value="<?php echo ($row->link_approved <= 0)?"listpending_links" : $returntask ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
	<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($other_cats) ) ? implode(', ', $other_cats) : '' ) ?>" />
	<input type="hidden" name="is_admin" value="1" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	
		</div>
	<?php
	}
	
	function move_links( $link_id, $cat_parent, $catList, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var JURI_ROOT='<?php echo JURI::root(); ?>';
	var active_cat=<?php echo $cat_parent; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

<div class="row-fluid">
	<div class="span12">
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>
			</div>
			<div class="controls">
				<?php echo count( $link_id );?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>
			</div>
			<div class="controls">
				<strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?>
			</div>
			<div class="controls">
				<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 30%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
				<?php echo $catList;?>
			</div>
		</div>
		
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="links_move" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHtml::_( 'form.token' ); ?>
<?php
		foreach ($link_id as $id) {
			echo "\n<input type=\"hidden\" name=\"lid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}

	function copy_links( $link_id, $cat_parent, $lists, $options, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var JURI_ROOT='<?php echo JURI::root(); ?>';
	var active_cat=<?php echo $cat_parent; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<div class="row-fluid">
		<div class="span12">
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>
				</div>
				<div class="controls">
					<?php echo count( $link_id );?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>
				</div>
				<div class="controls">
					<?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_COPY_TO' ) ?>
				</div>
				<div class="controls">
					<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 30%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
					<?php echo $lists['cat_id'];?>
				</div>
			</div>
			
			<fieldset>
				<legend><?php echo JText::_( 'COM_MTREE_OPTIONS' ) ?></legend>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_COPY_REVIEWS' ) ?>
					</div>
					<div class="controls">
						<?php bootstrapRadioBoolean('copy_reviews', $options['copy_reviews']); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_COPY_SECONDARY_CATEGORIES' ) ?>
					</div>
					<div class="controls">
						<?php bootstrapRadioBoolean('copy_secondary_cats', $options['copy_secondary_cats']); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_RESET_HITS' ) ?>
					</div>
					<div class="controls">
						<?php bootstrapRadioBoolean('reset_hits', $options['reset_hits']); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_RESET_RATINGS_AND_VOTES' ) ?>
					</div>
					<div class="controls">
						<?php bootstrapRadioBoolean('reset_rating', $options['reset_rating']); ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
	<input type="hidden" name="task" value="links_copy" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<?php
		foreach ($link_id as $id) {
			echo "\n<input type=\"hidden\" name=\"lid[]\" value=\"$id\" />";
		}
	?>
</form>

<?php
	}

	/**
	* Category
	*/
	function listcats( &$rows, &$links, &$softlink_cat_ids, &$parent, $catPageNav, &$pageNav, &$pathWay, $option ) {
		global $mtconf;
		
		$app		= JFactory::getApplication();
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		JHtml::_('behavior.tooltip');
		
		$max_char = 80;

		?>
		<script language="javascript" type="text/javascript">
			function link_listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<script language="Javascript">
		<?php
		if ( $mtconf->get('admin_use_explorer') ) { ?>
		// Open Explorer
		d.openTo(<?php echo ( (isset($parent->cat_id)) ? $parent->cat_id : '0'); ?>, true);
		<?php } ?>
		</script>

		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' ); ?></div>
				</th>
				<th align="right">
					<?php JHtml::_('behavior.modal'); ?>
					<a rel="{handler: 'iframe', size: {x: 500, y: 400}, onClose: function() {}}" href="index.php?option=com_mtree&amp;task=fastadd&amp;cat_parent=<?php echo $parent->cat_id; ?>&amp;hide=1&amp;tmpl=component" class="modal"><?php echo JText::_("COM_MTREE_FAST_ADD"); ?></a>
				</th>
			</tr>
		</table>
		<?php
		
		$app		= JFactory::getApplication('site');
		$database 	=& JFactory::getDBO();
		$listOrder	= $app->getUserStateFromRequest("listcats{$parent->cat_id}listordering", 'list.ordering', 'cat.lft');
		$listDirn	= $app->getUserStateFromRequest("listcats{$parent->cat_id}listdirection", 'list.direction', 'asc');
		$saveOrder 	= ($listOrder == 'cat.lft' && $listDirn == 'asc');
		$ordering 	= ($listOrder == 'cat.lft');
		$colspanAdd	= 0;
		
		?>
		<table class="table table-stripped">
			<thead>
			<tr>
				<th width=1% style="min-width:45px;padding-left:8px;text-align:left" align="left">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="75%" align="left" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="5%" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="5%" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
				<th width="10%" class="center hidden-phone"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="10%" class="center"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
				<?php if( $mtconf->get('first_cat_order1') == 'lft' ): ?>
				<th width="6%">
					<?php echo JText::_( 'JGRID_HEADING_ORDERING' ); ?>
				</th>
				<?php 
					$colspanAdd++;
				endif; 
				?>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input style=\"float:left\" type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="Joomla.isChecked(this.checked);" />
					<a href="index.php?option=com_mtree&amp;task=listcats&amp;cat_id=<?php echo $row->cat_id; ?>"><?php 
				if ($row->cat_image) {
					echo "<img style=\"float:right\" border=\"0\" src=\".." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder2.gif\" width=\"18\" height=\"18\" onmouseover=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder.gif'\" onmouseout=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder2.gif'; return nd(); \" />";
				} else {
					echo "<img style=\"float:right\" border=\"0\" src=\".." . $mtconf->get('relative_path_to_images') . "dtree/folder.gif\" width=\"18\" height=\"18\" name=\"img".$i."\" onmouseover=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/folderopen.gif'\" onmouseout=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/folder.gif'\" />"; 
				}
				?></a>
				</td>
				<td align="left" class="nowrap"><a href="index.php?option=com_mtree&amp;task=editcat&amp;cat_id=<?php echo $row->cat_id; ?>"><?php echo htmlspecialchars($row->cat_name); ?></a></td>
				<td align="center" class="hidden-phone"><?php echo $row->cat_cats; ?></td>
				<td align="center" class="hidden-phone"><?php echo $row->cat_links; ?></td>
				<td class="center hidden-phone">
					<?php echo JHtml::_('mtree.featured', $row->cat_featured, $i,'cat_'); ?>
				</td>
				 <td class="center">
					<?php echo JHtml::_('jgrid.published', $row->cat_published, $i, 'cat_', true); ?>
				</td>
				<?php if( $mtconf->get('first_cat_order1') == 'lft' ): ?>
				<td class="order" align=center>
					<?php if ($saveOrder) : ?>
						<span><?php 
							echo $catPageNav->orderUpIcon(
								$i, 
								isset($rows[$i -1]), 
								(($mtconf->get('first_cat_order2') == 'asc')?'cat_orderup':'cat_orderdown'), 
								'JLIB_HTML_MOVE_UP', 
								$ordering
								); 
						?></span>
						<span><?php 
							echo $catPageNav->orderDownIcon(
								$i, 
								$catPageNav->total, 
								isset($rows[$i +1]), 
								(($mtconf->get('first_cat_order2') == 'asc')?'cat_orderdown':'cat_orderup'), 
								'JLIB_HTML_MOVE_DOWN', 
								$ordering
								); 
						?></span>
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php $k = 1 - $k; } ?>
		</table>
		<table class="table table-stripped">
		
			<tr style="background-color:#F7F7F7;">
				<th width=1% style="min-width:45px;padding-left:8px;text-align:left" align="left">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th width="75%" colspan="<?php echo 2 + $colspanAdd; ?>" style="text-align:left;border-bottom:1px solid #CCCCCC;" class="title" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="5%" style="border-bottom:1px solid #CCCCCC;"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th width="10%" style="text-align:center;border-bottom:1px solid #CCCCCC;" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="10%" style="text-align:center;border-bottom:1px solid #CCCCCC;"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<?php if ( $row->main == 1 ) { ?>
				<td>
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
					<?php
					echo "<img style=\"float:right\" src=\".." . $mtconf->get('relative_path_to_images') . "page_white.png\" width=\"16\" height=\"16\" />" ?>
				</td>
				<td colspan="<?php echo 2 + $colspanAdd; ?>" align="left">
					<?php
					if ($row->internal_notes) {
						$intnotes = preg_replace('/\s+/', ' ', nl2br($row->internal_notes));
						echo JHtml::_('tooltip', $intnotes, '', 'new.png' );
					}
					?>
					<a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a>
				</td>
				<?php } else { ?>
				<td></td>
				<td colspan="<?php echo 2 + $colspanAdd; ?>" align="left">
					<a href="index.php?option=com_mtree&task=listcats&cat_id=<?php echo $softlink_cat_ids[$row->link_id]->cat_id ?>"> <?php echo $pathWay->printPathWayFromLink( $row->link_id ); ?></a> <?php echo JText::_( 'COM_MTREE_ARROW' ) ?> <a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id ?>"><?php echo htmlspecialchars($row->link_name); ?></a>
				</td>
				<?php } ?>
				<td align="center"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  <td align="center" class="hidden-phone">
				<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
			</td>
			  <td align="center">
				<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="<?php echo 6 + $colspanAdd; ?>">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cat_parent" value="<?php echo $parent->cat_id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<input type="hidden" name="cat_names" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
<?php
	}
	
	/**
	* Fast Add Category
	*/
	function fastadd( $cat_parent, $option ) {
		?>
		<h1><?php echo JText::_( 'COM_MTREE_FAST_ADD_TITLE' ) ?></h1>
		
		<p><?php echo JText::_( 'COM_MTREE_FAST_ADD_INSTRUCTIONS' ) ?></p>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<p><textarea name="cat_names" cols=60 rows=12 class="span12"></textarea></p>

			<div class="btn-toolbar">
				<div class="btn-group">
					<button type="submit" class="btn btn-primary" onclick="Joomla.submitbutton('module.save');">
					<?php echo JText::_( 'COM_MTREE_FAST_ADD_SUBMIT_BUTTON' ) ?></button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();">
					<?php echo JText::_( 'JCANCEL' ) ?></button>
				</div>
			</div>
			
			<input type="hidden" name="task" value="fastadd_cat" />
			<input type="hidden" name="cat_parent" value="<?php echo $cat_parent;?>" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="tmpl" value="component" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	/**
	*
	* Writes the edit form for new and existing category
	*
	*/
	function editCat( &$row, $cat_parent, $related_cats, $browse_cat, $customfields, $fields_map_cfs, &$lists, &$pathWay, $configs, $cat_params, $configgroups, $total_assoc_links, $returntask, $option, $form, $show ) {
		global $mtconf;

		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'cat_desc' );
		$editor = &JFactory::getEditor();
		?>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var txtRemove = '<?php echo addslashes(JText::_( 'COM_MTREE_REMOVE' )) ?>';
		var JURI_ROOT='<?php echo JURI::root(); ?>';
		var active_cat=<?php echo $row->cat_id; ?>;
		jQuery(document).ready(function(){
			toggleMcBut(active_cat);			
			jQuery('#browsecat').click(function(){
				cc(jQuery(this).val());
			});
			jQuery('input[type=checkbox][name^=override]').click(function(){
				toggleOverridableConfig(this);
			});
			jQuery('div[id^=config_]').each(function(){
				console.log(this.id);
				var li=this;
				var config_name = /config_([0-9A-Za-z_]+)/i.exec(li.id);
				config_name = config_name[1];
				jQuery('select[name=config\\['+config_name+'\\]], input[name=config\\['+config_name+'\\]], input[name=config\\['+config_name+'\\]\\[\\]], textarea[name=config\\['+config_name+'\\]]').each(function(){
					if( jQuery(li).hasClass('default')) {
						jQuery(this).prop('disabled', true);
						jQuery(this).addClass('disabled');
						if(jQuery("label[for='"+this.id+"']").length){
							jQuery("label[for='"+this.id+"']").prop('disabled', true);
							jQuery("label[for='"+this.id+"']").addClass('disabled');
						}
					} else {
						jQuery(this).prop('disabled', false)
						jQuery(this).removeClass('disabled');
						if(jQuery("label[for='"+this.id+"']").length){
							jQuery("label[for='"+this.id+"']").prop('disabled',false);
							jQuery("label[for='"+this.id+"']").removeClass('disabled');
						}
					}
				});
			});
		});
		function toggleOverridableConfig(obj){
			var config_name = /override\[([0-9A-Za-z_]+)\]/i.exec(obj.name)
			config_name = config_name[1];
			var config = jQuery('#config_'+config_name);

			console.log(config);
			console.log(config.prop('id'));
			if(jQuery(config).hasClass('default')) {
				jQuery(config).addClass('override');
				jQuery(config).removeClass('default');
				jQuery('#'+config.prop('id')+' div.controls input').removeProp('disabled').removeClass('disabled');
				jQuery('#'+config.prop('id')+' div.controls select').removeProp('disabled').removeClass('disabled');
				jQuery('#'+config.prop('id')+' div.controls textarea').removeProp('disabled').removeClass('disabled');

				jQuery('#config_'+config_name+' div.controls label').removeProp('disabled').removeClass('disabled');
			} else {
				jQuery(config).addClass('default');
				jQuery(config).removeClass('override');
				jQuery('#'+config.prop('id')+' div.controls input').prop('disabled',true);
				jQuery('#'+config.prop('id')+' div.controls select').prop('disabled',true);
				jQuery('#'+config.prop('id')+' div.controls textarea').prop('disabled',true);

				jQuery('#'+config.prop('id')+' div.controls label').prop('disabled',true);
				jQuery('#'+config.prop('id')+' div.controls label').addClass('disabled');
			}
			
			jQuery( "#config_"+config_name+"_fieldset" ).button( "refresh" );
		}
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelcat' || pressbutton == 'editcat_add_relcat' || pressbutton == 'editcat_browse_cat') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.cat_name.value == ""){
				alert( "<?php echo JText::_( 'COM_MTREE_CATEGORY_MUST_HAVE_NAME' ) ?>" );
			} else {
				<?php echo $editor->save( 'cat_desc' );	?>
				submitform( pressbutton );
			}
		}
		</script>
		<style type="text/css">
		ul.linkcats a {text-decoration:underline;margin-right:3px;}
		fieldset.fields-assignment {font-size:1.091em}
		fieldset.fields-assignment .controls label {display: inline;}
		fieldset ul {
			list-style: none;
			margin: 0;
		}
		#config input[type="checkbox"] {
			margin:-3px 4px 0 0;
		}
		#config .control-label {
			min-width: 250px;
		}
		</style>
		<?php if( $row->cat_id ) { ?>
			<h4><?php echo $row->cat_name; ?></h4>
		<?php } ?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="span10 form-horizontal">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MTREE_CATEGORY_DETAILS', true)); ?>

		<div style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left">
			<div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' ); ?></div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<label id="cat_name-lbl" for="cat_name" class="required" title=""><?php echo JText::_( 'COM_MTREE_NAME' ) ?><span class="star">&nbsp;*</span></label>
			</div>
			<div class="controls">
				<input class="required" type="text" name="cat_name" id="cat_name" size="40" maxlength="250" required="required" aria-required="true" value="<?php echo $row->cat_name;?>" />
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<label id="lcmanage-lbl" for="lcmanage" class="" title=""><?php echo JText::_( 'COM_MTREE_RELATED_CATEGORIES' ) ?></label>
			</div>
			<div class="controls">
				<ul class="linkcats" id="linkcats">
					<li>
						<button id="lcmanage" class="btn btn-small" name="lcmanage" onclick="javascript:togglemc();return false;"><?php echo JText::_( 'COM_MTREE_ADD_RELATED_CATEGORIES' ); ?></button>
					</li>
				<?php
				if ( !empty($related_cats) ) {
					foreach( $related_cats AS $related_cat ) {
						if ( is_numeric( $related_cat ) ) {
							echo '<li id="lc' . $related_cat . '"><a href="javascript:remSecCat('.$related_cat.')">'.JText::_( 'COM_MTREE_REMOVE' ).'</a>'. $pathWay->printPathWayFromCat_withCurrentCat( $related_cat, '' ) . '</li>';
						}
					}
				}
				?>
				</ul>
				<div id="mc_con" style="display:none" class="row-fluid">
					<div class="row-fluid">
						<div class="span5" id="mc_active_pathway" style=""><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $row->cat_id, '' ); ?></div>
					</div>
					<div class="row-fluid">
						<?php echo $lists["new_related_cat"]; ?>
					</div>
					<div class="row-fluid">
						<button type="button" class="btn btn-small" id="mcbut1" onclick="addSecCat()"><?php echo JText::_( 'COM_MTREE_ADD' ) ?></button>
					</div>
				</div>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<label id="cat_name-lbl" for="cat_desc" class="" title=""><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></label>
			</div>
			<div class="controls">
				<?php 
				echo $editor->display( 'cat_desc',  $row->cat_desc , '100%', '250', '75', '20', array('readmore', 'pagebreak') ) ;
				?>
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label">
				<label id="cat_image-lbl" for="cat_image" class="" title=""><?php echo JText::_( 'COM_MTREE_IMAGE' ) ?></label>
			</div>
			<div class="controls">
				<input style="float:none" type="file" name="cat_image" id="cat_image" />
				<?php if ($row->cat_image != "") { ?>
				<p />
				<img style="border: 5px solid #c0c0c0;" src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_cat_small_image') . $row->cat_image ?>" />
				<input style="float:none" type="checkbox" name="remove_image" value="1" /> <?php echo JText::_( 'COM_MTREE_REMOVE_THIS_IMAGE' ) ?>
				<?php } ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php if( !empty($configgroups) ): ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'config', JText::_('COM_MTREE_CATEGORY_CONFIGURATION', true)); ?>
		
		<div id="category-configuration-sliders" class="tabbable tabs-left">
			<?php
			echo '<ul class="nav nav-tabs">';
			foreach ($configgroups as $configgroup)
			{
				$active = "";
				if ($configgroup == 'main')
				{
					$active = "active";
				}
		
				echo '<li class="' . $active . '">';
					echo '<a href="#category-configuration-' . $configgroup . '" data-toggle="tab">';
					echo JText::_('COM_MTREE_'.$configgroup);
					echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
			
			echo '<div class="tab-content">';
			
			foreach( $configgroups AS $configgroup ) {
				$i = 0;
				$active = "";
				if ($configgroup == 'main')
				{
					$active = " active";
				}
				echo '<div class="tab-pane' . $active . '" id="category-configuration-' . $configgroup . '">';
				
				foreach( $configs AS $config )
				{ 
					if( $config->configcode == 'sort_direction' )
					{
						continue;
					}
					
					if( $config->groupname == $configgroup )
					{
						echo "\n\n";
						echo '<div ';
						echo ' id="config_'.$config->varname.'"';
						$override_value = $cat_params->get($config->varname);

						echo ' class="control-group';
						if( $override_value != '' ) {
							echo ' override';
						} else {
							echo ' default';
						}
						echo '">';

						echo '<div class="control-label">';
						if( $config->configcode == 'note' ) {
							echo JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($config->varname) );
						} elseif( !in_array($config->configcode, array('sort_direction','predefined_reply')) ) {
							echo '<label';
							if($i<=1) {
								// echo ' style="width:295px"';
							}
							echo '>';
						
							echo MTConfigHtml::overrideCheckbox(
								array(
									array(
										'varname'	=>	$config->varname,
										'value'		=>	$config->value,
										'override'	=>	$cat_params->get($config->varname)
									)
								),
								array('namespace'=>'config', 'class'=>'override')
							);
						
							$langcode = 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname);
							if( JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) ) == $langcode ) {
								echo $config->varname;
							} else {
								echo JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) );
							}
						
							if( substr($config->varname,0,4) == 'rss_' ) {
								if( $config->varname == 'rss_custom_fields') {
									echo ' (cust_#)';
								} else {
									echo ' ('.substr($config->varname,4).')';
								}
							}
							echo '</label>';
						}
						echo '</div>';
						echo '<div class="controls">';
						switch( $config->configcode ) {
							case 'text':
							case 'user_access':
							case 'user_access2':
							case 'sef_link_slug_type':
							default:
								echo MTConfigHtml::_(
									$config->configcode, 
									array(
										array(
											'varname'	=>	$config->varname,
											'value'		=>	$config->value,
											'override'	=>	$cat_params->get($config->varname)
										)
									),
									array('namespace'=>'config')
								);
						
								break;
							case 'sort_direction':
								continue;
								break;
							case 'cat_order':
							case 'listing_order':
							case 'review_order':
								$tmp_varname = substr($config->varname,0,-1);
								echo MTConfigHtml::_(
									$config->configcode, 
									array(
										array(
											'varname'	=>	$config->varname,
											'value'		=>	$config->value,
											'override'	=>	$cat_params->get($config->varname)
										),
										array(
											'varname'	=>	$tmp_varname.'2',
											'value'		=>	$configs[$tmp_varname.'2']->value,
											'override'	=>	$cat_params->get($tmp_varname.'2')
										)
									),
									array('namespace'=>'config')
								);
								if( substr($config->varname,-1) == '1' ) {
									unset($configs[$tmp_varname.'2']);
								} else {
									unset($configs[$tmp_varname.'1']);
								}
								break;
							case 'predefined_reply':
								continue;
								break;
							case 'predefined_reply_title':
								$tmp_varname = substr($config->varname,17,1);
								echo MTConfigHtml::_(
									$config->configcode, 
									array(
										array(
											'varname'	=>	$tmp_varname.'_title',
											'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_title']->value,
											'override'	=>	$cat_params->get('predefined_reply_'.$tmp_varname.'_title')
										),
										array(
											'varname'	=>	$tmp_varname.'_message',
											'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_message']->value,
											'override'	=>	$cat_params->get('predefined_reply_'.$tmp_varname.'_message')
										)
									),
									array('namespace'=>'config')
								);
								if( substr($config->varname,19) == 'title' ) {
									unset($configs['predefined_reply_'.$tmp_varname.'_message']);
								} else {
									unset($configs['predefined_reply_'.$tmp_varname.'_title']);
								}						
								break;
							case 'note':
								// Output nothing.
								break;
						} // End switch

						echo '</div>';
						echo '</div>';
						unset($configs[$config->varname]);
						$i++;
					}
				}
				echo '</div>';
			}
			echo '</div>';
			
			?>
		</div>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if( !empty($customfields) ): ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'fields-assignment', JText::_('COM_MTREE_EDIT_CATEGORY_FIELDS_ASSIGNMENT', true)); ?>

		<fieldset class="fields-assignment form-vertical">

		<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_FIELDS_ASSIGNMENT_INSTRUCTIONS' ); ?>
		<p />
		<button type="button" id="jform_toggle" class="btn btn-small" onclick="jQuery('.chk-cf').each(function() { this.checked = !this.checked; });jQuery('#cf-1').attr('checked',true);">
			<?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
		</button>
		<p />
		<div class="row-fluid">
		<?php
		$columns = 4;
		$n_column_grid = 12;
		
		$total_cusomfields = count($customfields);
		$num_of_customfields_per_column = floor($total_cusomfields / 4);
		$spans = floor($n_column_grid / $columns);
		
		$i = 0;
		foreach( $customfields AS $cf ) 
		{
			switch($cf->cf_id)
			{
				case 1:
					$checked = ' checked="checked" disabled';
					break;
				default:
					$checked = (in_array($cf->cf_id,$fields_map_cfs) ? ' checked="checked"' : '');
					break;
			}
			if( $i%$columns == 0 )
			{
				echo "\n\n";
				echo '<div class="row-fluid">';
			}
			echo '<div class="control-group span'.$spans.'">';
			echo '<div class="controls">';
			echo '<input type="checkbox" id="cf-'.$cf->cf_id.'" name="fields_map_cfs[]"' .
					' value="'.$cf->cf_id.'" class="chk-cf"'
					.$checked.'/>';
			echo '<label for="cf-'.$cf->cf_id.'">&nbsp;'.$cf->caption.'</label>';
			echo '</div>';
			echo '</div>';

			if( ($i + 1) ==  $total_cusomfields || $i%$columns == 3 ) {
				echo '</div>';
			}
			$i++;
			
		}
		?>
		</div>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>
		
		<?php
		if( $row->cat_parent == 0 )
		{
		?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'category-association', JText::_('COM_MTREE_EDIT_CATEGORY_ASSOCIATION', true)); ?>

		<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_ASSOCIATION_DESC' ) ?>

		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_ASSOCIATED_CATEGORY' ) ?>
			</div>
			<div class="controls">
				<?php echo $lists['cat_association']; ?>
			</div>
		</div>
		
		<p />
		
		<?php if( $total_assoc_links > 0 ) { ?>
		<strong><?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_CHANGE_ASSOCIATED_CATEGORY_EXPLAIN' ) ?></strong>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_TOTAL_ASSOCIATED_LISTINGS' ) ?>
			</div>
			<div class="controls">
				<?php echo $total_assoc_links; ?>
			</div>
		</div>
		<?php } ?>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php
		}
		?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'operations', JText::_('COM_MTREE_OPERATIONS', true)); ?>
		<?php JHtml::_('behavior.modal'); ?>

		<h4><?php echo JText::_( 'COM_MTREE_FULL_RECOUNT' ) ?></h4>
		<?php echo JText::_( 'COM_MTREE_FULL_RECOUNT_EXPLAIN' ) ?>
		<a rel="{handler: 'iframe', size: {x: 300, y: 150}, onClose: function() {}}" href="index.php?option=com_mtree&task=fullrecount&hide=1&cat_id=<?php echo $row->cat_id ?>&tmpl=component" class="modal btn btn-small"><?php echo JText::_('COM_MTREE_PERFORM_FULL_RECOUNT'); ?></a>

		<h4><?php echo JText::_( 'COM_MTREE_FAST_RECOUNT' ) ?></h4>
		<?php echo JText::_( 'COM_MTREE_FAST_RECOUNT_EXPLAIN' ) ?>
		<a rel="{handler: 'iframe', size: {x: 300, y: 150}, onClose: function() {}}" href="index.php?option=com_mtree&task=fastrecount&hide=1&cat_id=<?php echo $row->cat_id ?>&tmpl=component" class="modal btn btn-small"><?php echo JText::_('COM_MTREE_PERFORM_FULL_RECOUNT'); ?></a>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		
		</div>
		<div class="span2">

		<fieldset class="form-vertical">
			<?php if ( $row->cat_approved == 0 || $row->cat_id == 0 ) { ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_approved'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_approved'); ?>
				</div>
			</div>
			<?php } else { ?>
			<input type="hidden" name="cat_approved" value="1" />
			<?php } ?>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_published'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_published'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_featured'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_featured'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_allow_submission'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_allow_submission'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_show_listings'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_show_listings'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('alias'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('alias'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('title'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('title'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('cat_template'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('cat_template'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('metadesc'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('metadesc'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('metakey'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('metakey'); ?>
				</div>
			</div>
		</fieldset>
		</div>

		<input type="hidden" name="cat_id" value="<?php echo $row->cat_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="editcat" />
		<input type="hidden" name="show" value="<?php echo $show ?>" />
		<input type="hidden" name="returntask" value="<?php echo $returntask ?>" />
		<input type="hidden" name="cat_parent" value="<?php echo $cat_parent; ?>" />
		<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($related_cats) ) ? implode(', ', $related_cats) : '' ) ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
<?php
	}

	/***
	* Move Category
	*/
	function move_cats( $cat_id, $cat_parent, $catList, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var JURI_ROOT='<?php echo JURI::root(); ?>';
	var active_cat=<?php echo $cat_id; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){cc(jQuery(this).val());});
	});
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancelcats_move') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

<div class="row-fluid">
	<div class="span12">
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>
			</div>
			<div class="controls">
				<?php echo count( $cat_id );?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>
			</div>
			<div class="controls">
				<?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_MOVE_TO' ) ?>
			</div>
			<div class="controls">
				<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 40%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
				<?php echo $catList;?>

			</div>
		</div>
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="cats_move" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHtml::_( 'form.token' ); ?>
<?php
		foreach ($cat_id as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}
	
	/***
	* Copy Category
	*/
	function copy_cats( $cat_id, $cat_parent, $lists, $options, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var JURI_ROOT='<?php echo JURI::root(); ?>';
	var active_cat=<?php echo $cat_id; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancelcats_copy') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

<div class="row-fluid">
	<div class="span12">
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>
			</div>
			<div class="controls">
				<?php echo count( $cat_id );?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>
			</div>
			<div class="controls">
				<strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_MTREE_COPY_TO' ) ?>
			</div>
			<div class="controls">
				<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 40%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
				<?php echo $lists['cat_id'] ;?>
			</div>
		</div>
		
		<fieldset id="" class="">
			<legend><?php echo JText::_( 'COM_MTREE_OPTIONS' ) ?></legend>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_COPY_SUBCATS' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('copy_subcats', $options['copy_subcats']); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_COPY_RELCATS' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('copy_relcats', $options['copy_relcats']); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_COPY_LISTINGS' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('copy_listings', $options['copy_listings']); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_COPY_REVIEWS' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('copy_reviews', $options['copy_reviews']); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_RESET_HITS' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('reset_hits', $options['reset_hits']); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MTREE_RESET_RATINGS_AND_VOTES' ) ?>
				</div>
				<div class="controls">
					<?php bootstrapRadioBoolean('reset_rating', $options['reset_rating']); ?>
				</div>
			</div>
		</fieldset>
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="cats_copy" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHtml::_( 'form.token' ); ?>
<?php
		foreach ($cat_id as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}

	function removecats( $categories, $cat_parent, $option ) {
		global $mtconf;
	?>
		<fieldset>
			<div class="alert">
		  		<p><?php echo JText::_( 'COM_MTREE_CONFIRM_DELETE_CATS' ) ?></p>
			</div>
	  	</fieldset>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<table class="table table-striped">
			<thead>
			<tr>
				<th width="40px" nowrap="nowrap">&nbsp;</th>
				<th width="80%" nowrap="nowrap" style="text-align:left" align="left"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="10%" nowrap="nowrap" align="center"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $categories ); $i < $n; $i++) {
			$row = &$categories[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td width="18px"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif" width="18" height="18" /><input type="hidden" name="cid[]" value="<?php echo $row->cat_id ?>" /></td>
				<td align="left" width="80%"><?php echo $row->cat_name; ?></td>
				<td><?php echo $row->cat_cats; ?></td>
				<td><?php echo $row->cat_links; ?></td>
			</tr>
			<?php		$k = 1 - $k; } ?>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cat_parent" value="<?php echo $cat_parent;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
	<?php

	}
	
	/***
	* Approval
	*/
	function listpending_links( $links, $pathWay, $pageNav, $option ) {
		global $mtconf;
		JHtml::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
			function link_listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="table table-stripped">
			<thead>
			<tr>
				<th width="1%" style="min-width:45px;padding-left:8px;text-align:left "align="right">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th width="50%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="60%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="10%" style="text-align:left" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td>
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
					<?php
					echo "<img style=\"float:right\" src=\".." . $mtconf->get('relative_path_to_images') . "page_white.png\" width=\"16\" height=\"16\">"; ?>
				</td>
				<td><?php
					if ($row->internal_notes) {
						$intnotes = preg_replace('/\s+/', ' ', nl2br($row->internal_notes));
						echo JHtml::_('tooltip', $intnotes, '', 'messaging.png' );
						echo '&nbsp;';
					}
					echo (($row->link_approved < 0 ) ? '': '<b>' ); ?><a href="#edit" onclick="return link_listItemTask('lb<?php echo $i;?>','editlink_for_approval')"><?php echo $row->link_name; ?></a><?php echo (($row->link_approved < 0 ) ? '': '<b>' ); ?></td>
				<td><?php $pathWay->printPathWayFromLink( $row->link_id, '' ); ?></td>
				<td class="hidden-phone"><?php echo tellDateTime($row->link_created); ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_links" />
		<input type="hidden" name="returntask" value="listpending_links" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_cats( $cats, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="table table-stripped">
			<thead>
			<tr>
				<th width="1%" style="min-width:45px;padding-left:8px;text-align:left" align="left"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
				<th width="50%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="55%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_PARENT' ) ?></th>
				<th width="10%" style="text-align:left" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $cats ); $i < $n; $i++) {
			$row = &$cats[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td>
					<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="Joomla.isChecked(this.checked);" />
					<a href="#go" onclick="return listItemTask('cb<?php echo $i;?>','listcats')"><?php 
					
				if ($row->cat_image) {
					echo "<img style=\"float:right\" border=\"0\" src=\"..".$mtconf->get('relative_path_to_images')."dtree/imgfolder2.gif\" width=\"18\" height=\"18\" onmouseover=\"showInfo('" .$row->cat_name ."', '".$row->cat_image."', 'cat'); this.src='..".$mtconf->get('relative_path_to_images')."dtree/imgfolder.gif'\" onmouseout=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/imgfolder2.gif'; return nd(); \" />";
				} else {
					echo "<img style=\"float:right\" border=\"0\" src=\"..".$mtconf->get('relative_path_to_images')."dtree/folder.gif\" width=\"18\" height=\"18\" name=\"img".$i."\" onmouseover=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/folderopen.gif'\" onmouseout=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/folder.gif'\" />"; 
				}
				?></a>
				</td>
				<td><a href="index.php?option=com_mtree&amp;task=editcat&amp;cat_id=<?php echo $row->cat_id; ?>"><?php echo $row->cat_name; ?></a></td>
				<td><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $row->cat_parent, 0 ); ?></td>
				<td class="hidden-phone"><?php echo tellDateTime($row->cat_created); ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>

			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_cats" />
		<input type="hidden" name="returntask" value="listpending_cats" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviews( $reviews, $pathWay, $pageNav, $option ) {
		global $mtconf;
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/spy.mtree.html.php' );
		?>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var predefined_reply=new Array();
		<?php
		$num_of_predefined_reply=0;
		for ( $j=1; $j <= 5; $j++ )
		{ 
			if( $mtconf->get( 'predefined_reply_'.$j.'_title' ) <> '' && $mtconf->get( 'predefined_reply_'.$j.'_message' ) <> '') {
				echo 'predefined_reply['.$j.']="'.str_replace("'","\\'",str_replace('"','\\"',str_replace("\t","\\t",str_replace("\r\n","\\n",str_replace("\\","\\\\",$mtconf->get( 'predefined_reply_'.$j.'_message' ))))))."\";\n";
				$num_of_predefined_reply++;
			}
		}
		?>
		function selectreply(value,rev_id){
			jQuery('#emailmsg_'+rev_id).val( predefined_reply[value] );
		}
		function toggleemaileditor(rev_id){
			jQuery('#emaileditor_'+rev_id).slideToggle('fast');
		}
		</script>
		<style>
		.review {
			margin-bottom:35px;
		}
		.review-header {
			padding:10px 0 0 0;
		}
		.review-header img {
			position: relative;
			top:-3px;
		}
		</style>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php
		if ( count($reviews) <= 0 ) {
			?>
			<h1><?php echo JText::_( 'COM_MTREE_NO_REVIEW_FOUND' ) ?></h1>
			<?php
		} else {
			?>
			<div class="row-fluid">
				<div class="pagination">
					<?php echo $pageNav->getPagesLinks(); ?>
					<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
				</div>
			</div>
			<?php
			$k = 0;
			for ($i=0, $n=count( $reviews ); $i < $n; $i++) {
				$row = &$reviews[$i]; ?>
				<div class="row-fluid review"><div class="span12">
					<div class="row-fluid review-header" style="background-color:#ededed"><div class="span12">
						<?php
						echo mtfHTML::rating($row->value);
						?>
						<a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo $row->link_name ?></a> by <?php
						if($row->user_id > 0) {
							echo '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a>';
						} elseif(!empty($row->email)) {
							echo '<a href="mailto:' . $row->email . '">' . $row->guest_name . '</a>';
						} else {
							echo $row->guest_name;
						}
						?>, <?php echo $row->rev_date ?> - <a href="<?php echo $mtconf->getjconf('live_site'). "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a>
					</div></div>

					<div class="row-fluid">
						<div class="span8">
							<div class="row-fluid"><div class="span12">
								<div class="control-group">
									<div class="control-label">
										<?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?>
									</div>
									<div class="controls">
										<input class="span12" type="text" name="rev_title[<?php echo $row->rev_id; ?>]" value="<?php echo htmlspecialchars($row->rev_title); ?>" size="60" />
									</div>
								</div>
							</div></div>
							<div class="row-fluid"><div class="span12">
								<div class="control-group">
									<div class="control-label">
										<?php echo JText::_( 'COM_MTREE_REVIEW_TEXT' ) ?>
									</div>
									<div class="controls">
										<textarea class="span12" rows="8" name="rev_text[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->rev_text) ?></textarea>
									</div>
								</div>
							</div></div>
							
						</div>
						<div class="span4">
							<?php if ( $mtconf->get('use_internal_notes') ) { ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>
									</div>
									<div class="controls">
										<textarea class="span12" rows="11" name="admin_note[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				
					<div class="row-fluid">
						<div class="span12">
							<fieldset class="radio btn-group">
								<input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="1" id="app_<?php echo $row->rev_id ?>" />
								<label class="btn" for="app_<?php echo $row->rev_id ?>">
									<?php echo JText::_( 'COM_MTREE_APPROVE' ) ?>
								</label>

								<input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="" id="ign_<?php echo $row->rev_id ?>" checked="checked" />
								<label class="btn" for="ign_<?php echo $row->rev_id ?>">
									<?php echo JText::_( 'COM_MTREE_IGNORE' ) ?>
								</label>
								
								<input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="-1" id="rej_<?php echo $row->rev_id ?>" />
								<label class="btn" for="rej_<?php echo $row->rev_id ?>">
									<?php echo JText::_( 'COM_MTREE_REJECT' ) ?>
								</label>
								
								<?php if($row->value > 0) { ?>
								<input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="-2" id="rejrv_<?php echo $row->rev_id ?>" />
								<label class="btn" for="rejrv_<?php echo $row->rev_id ?>">
									<?php echo JText::_( 'COM_MTREE_REJECT_AND_REMOVE_VOTE' ) ?>
								</label>
								<?php } ?>
							</fieldset>
							<?php
							if( !empty($row->email) ) {
							?>						
							<span style="margin-top:2px;display:block;clear:left;">
									<label class="checkbox inline" for="sendemail_<?php echo $row->rev_id ?>">
										<input type="checkbox"<?php echo (($row->send_email)?' checked':''); ?> name="sendemail[<?php echo $row->rev_id ?>]" value="1" id="sendemail_<?php echo $row->rev_id ?>" onclick="toggleemaileditor(<?php echo $row->rev_id ?>)" />
										<?php echo JText::_( 'COM_MTREE_SEND_EMAIL_TO_REVIEWER_UPON_APPROVAL_OR_REJECTION' ) ?>
									</label>
							</span>
							<div id="emaileditor_<?php echo $row->rev_id ?>"<?php echo ((!$row->send_email)?' style="display:none"':''); ?>>
								<select onchange="selectreply(this.value,<?php echo $row->rev_id ?>)"<?php echo (($num_of_predefined_reply==0)?' disabled':''); ?>>
									<option><?php echo JText::_( 'COM_MTREE_SELECT_A_PRE_DEFINED_REPLY' ) ?></option>
									<?php
									for ( $k=1; $k <= 5; $k++ )
									{ 
										if( $mtconf->get( 'predefined_reply_'.$k.'_title' ) <> '') {
											echo '<option value="'.$k.'">'.$mtconf->get( 'predefined_reply_'.$k.'_title' ).'</option>';
										}
									}
									?>
								</select>&nbsp;<?php echo JText::_( 'COM_MTREE_OR_ENTER_THE_EMAIL_MESSAGE' ) ?>
								<p />
								<textarea class="span8" rows="6" name="emailmsg[<?php echo $row->rev_id ?>]" id="emailmsg_<?php echo $row->rev_id ?>"><?php echo $row->email_message ?></textarea>
							</div>
							<?php } ?>
						</div>
					</div>
				</div></div>
				<?php		$k = 1 - $k; } 
				
			} ?>

		<div class="row-fluid">
			<?php echo $pageNav->getListFooter(); ?>
		</div>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reviews" />
		<input type="hidden" name="returntask" value="listpending_reviews" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reports( $reports, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<?php
		if ( count($reports) <= 0 ) {
			?>
			<h1><?php echo JText::_( 'COM_MTREE_NO_REPORT_FOUND' ) ?></h1>
			<?php
		} else {
			?>
			<div class="row-fluid">
				<div class="pagination">
					<?php echo $pageNav->getPagesLinks(); ?>
					<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
				</div>
			</div>
			<?php
		$k = 0;
		for ($i=0, $n=count( $reports ); $i < $n; $i++) {
			$row = &$reports[$i]; ?>
			<div class="row-fluid"><div class="span12">
				
				<div class="row-fluid" style="background-color:#ededed;padding-top:10px"><div class="span12">
					<a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id; ?>">
					<?php echo $row->link_name ?></a> - <a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a>
				</div></div>
				
				<div class="row-fluid">
					<div class="span8">
						<u><?php echo $row->subject . "</u>, " . ( (empty($row->username))? $row->guest_name : '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a> ' ) ." ". $row->created ?>
						<p />
						<?php echo nl2br($row->comment) ?>

						<fieldset class="radio btn-group">
							<input type="radio" name="report[<?php echo $row->report_id ?>]" value="1" id="res_<?php echo $row->report_id ?>" />	
							<label class="btn" for="res_<?php echo $row->report_id ?>">
								<?php echo JText::_( 'COM_MTREE_RESOLVED' ) ?>
							</label>

							<input type="radio" name="report[<?php echo $row->report_id ?>]" value="" id="ign_<?php echo $row->report_id ?>" checked="checked" />
							<label class="btn" for="ign_<?php echo $row->report_id ?>">
								<?php echo JText::_( 'COM_MTREE_IGNORE' ) ?>
							</label>
						</fieldset>
					</div>
					<div class="span4">
						<?php if( $mtconf->get('use_internal_notes') ) { ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>
							</div>
							<div class="controls">
								<textarea class="span12" rows="4" name="admin_note[<?php echo $row->report_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div></div>
			
			<?php $k = 1 - $k; } } ?>

		<div class="row-fluid">
			<?php echo $pageNav->getListFooter(); ?>
		</div>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reports" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviewsreports( $reports, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php
		if ( count($reports) <= 0 ) {
			?>
			<h1><?php echo JText::_( 'COM_MTREE_NO_REPORT_FOUND' ) ?></h1>
			<?php
		} else {
		?>
		<div class="row-fluid">
			<div class="pagination">
				<?php echo $pageNav->getPagesLinks(); ?>
				<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
			</div>
		</div>
		<?php
		$k = 0;
		for ($i=0, $n=count( $reports ); $i < $n; $i++) {
			$row = &$reports[$i]; ?>
			<div class="row-fluid" style="margin-bottom:20px"><div class="span12">
				<div class="row-fluid" style="background-color:#ededed;padding-top:10px"><div class="span12">
					<a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo $row->link_name ?></a>
				</div></div>
				
				<div class="row-fluid">
					<div class="span8">
						<blockquote style="margin:3px 0 10px 2px;background-color:#F3F3F3;padding:6px;border: 1px solid #e1e1e1;border-left:6px solid #E1E1E1;">
						<?php echo '<strong>' . $row->rev_title . '</strong>';
						echo ' - <a href="index.php?option=com_mtree&task=editreview&rid=' . $row->rev_id . '">' . JText::_( 'COM_MTREE_EDIT_REVIEW' ) . '</a>';
						 echo '<br />' . JText::_( 'COM_MTREE_REVIEWED_BY' ) . ' <a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->review_user_id.'">' . $row->review_username . '</a>, ' . $row->rev_date ?>
						<p />
						<?php echo nl2br($row->rev_text); ?>
						</blockquote>
						<?php echo '</pre>'; echo ( (empty($row->username))? $row->guest_name : '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">'.$row->username."</a> " ) ." ". $row->created ?>
						<p />
						<?php echo nl2br($row->comment) ?>
						<p />

						<fieldset class="radio btn-group">
							<input type="radio" name="report[<?php echo $row->report_id ?>]" value="1" id="res_<?php echo $row->report_id ?>" />	
							<label class="btn" for="res_<?php echo $row->report_id ?>">
								<?php echo JText::_( 'COM_MTREE_RESOLVED' ) ?>
							</label>

								<input type="radio" name="report[<?php echo $row->report_id ?>]" value="" id="ign_<?php echo $row->report_id ?>" checked="checked" />
							<label class="btn" for="ign_<?php echo $row->report_id ?>">
								<?php echo JText::_( 'COM_MTREE_IGNORE' ) ?>
							</label>
						</fieldset>
					</div>
					<div class="span4">
						<?php if ( $mtconf->get('use_internal_notes') ) { ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>
								</div>
								<div class="controls">
									<textarea class="span12" rows="11" name="admin_note[<?php echo $row->report_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div></div>
			
			<?php		$k = 1 - $k; } } ?>
		<div class="row-fluid">
			<?php echo $pageNav->getListFooter(); ?>
		</div>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reviewsreports" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviewsreply( $reviewsreply, $pathWay, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<?php
		if ( count($reviewsreply) <= 0 ) {
			?>
			<h1><?php echo JText::_( 'COM_MTREE_NO_REPLY_FOUND' ) ?></h1>
			<?php
		} else {

		$k = 0;
		for ($i=0, $n=count( $reviewsreply ); $i < $n; $i++) {
			$row = &$reviewsreply[$i]; ?>
		<div class="row-fluid"><div class="span12">
			<div class="row-fluid" style="background-color:#ededed; padding-top:10px"><div class="span12">
				<a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo $row->link_name ?></a>
			</div></div>
			<div class="row-fluid">
				<div class="span8">
					<blockquote style="margin:3px 0 10px 2px;background-color:#F3F3F3;padding:6px;border: 1px solid #e1e1e1;border-left:6px solid #E1E1E1;">
					<?php echo '<strong>' . $row->rev_title . '</strong>';
					echo ' - <a href="index.php?option=com_mtree&task=editreview&rid=' . $row->rev_id . '">' . JText::_( 'COM_MTREE_EDIT_REVIEW' ) . '</a>';
					echo '<br />' . JText::_( 'COM_MTREE_REVIEWED_BY' ) . ' <a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a>, ' . $row->rev_date ?>
					<p />
					<?php echo nl2br($row->rev_text); ?>
					</blockquote>
					<?php 
						if( !empty($row->owner_username) ) {
							echo '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->owner_user_id.'">'.$row->owner_username."</a>  ";
						}
						echo $row->ownersreply_date;
					?>
					<p />
					<textarea class="span12" style="height:150px" name="or_text[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->ownersreply_text) ?></textarea>
					<p />

					<fieldset class="radio btn-group">
						<input type="radio" name="or[<?php echo $row->rev_id ?>]" value="1" id="app_<?php echo $row->rev_id ?>" />
						<label class="btn" for="app_<?php echo $row->rev_id ?>">
							<?php echo JText::_( 'COM_MTREE_APPROVE' ) ?>
						</label>
						
						<input type="radio" name="or[<?php echo $row->rev_id ?>]" value="" id="ign_<?php echo $row->rev_id ?>" checked="checked" />
						<label class="btn" for="ign_<?php echo $row->rev_id ?>">
							<?php echo JText::_( 'COM_MTREE_IGNORE' ) ?>
						</label>
						
						<input type="radio" name="or[<?php echo $row->rev_id ?>]" value="-1" id="rej_<?php echo $row->rev_id ?>" />
						<label class="btn" for="rej_<?php echo $row->rev_id ?>">
							<?php echo JText::_( 'COM_MTREE_REJECT' ) ?>
						</label>
					</fieldset>
					<p />
				</div>
				<div class="span4">
					<?php if ( $mtconf->get('use_internal_notes') ) { ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>
							</div>
							<div class="controls">
								<textarea class="span12" rows="11" name="admin_note[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->ownersreply_admin_note) ?></textarea>
							</div>
						</div>
					<?php } ?>

				</div>
			</div>
			
		</div></div>
			
		<?php		$k = 1 - $k; } } ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="save_reviewsreply" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_claims( $claims, $pathWay, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<?php
		if ( count($claims) <= 0 ) {
			?>
			<h1><?php echo JText::_( 'COM_MTREE_NO_CLAIM_FOUND' ) ?></h1>
			<?php
		} else {

		$k = 0;
		for ($i=0, $n=count( $claims ); $i < $n; $i++) {
			$row = &$claims[$i]; ?>
			<div class="row-fluid"><div class="span12">
				<div class="row-fluid" style="background-color:#ededed; padding-top:10px"><div class="span12">
					<a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id; ?>"><?php echo $row->link_name ?></a> by <a href="mailto:<?php echo $row->email ?>"><?php echo $row->name ?></a> (<?php echo $row->username ?>), <?php echo $row->created ?> - <a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a>
				</div></div>
				<div class="row-fluid">
					<div class="span8">
						<p>
						<?php echo nl2br(htmlspecialchars($row->comment)) ?>
						</p>
						<fieldset class="radio btn-group">
							<input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="<?php echo $row->user_id ?>" id="app_<?php echo $row->claim_id ?>" />
							<label class="btn" for="app_<?php echo $row->claim_id ?>">
								<?php echo JText::_( 'COM_MTREE_APPROVE' ) ?>
							</label>
							
							<input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="" id="ign_<?php echo $row->claim_id ?>" checked="checked" />			
							<label class="btn" for="ign_<?php echo $row->claim_id ?>">
								<?php echo JText::_( 'COM_MTREE_IGNORE' ) ?>
							</label>

							<input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="-1" id="rej_<?php echo $row->claim_id ?>" />
							<label class="btn" for="rej_<?php echo $row->claim_id ?>">
								<?php echo JText::_( 'COM_MTREE_REJECT' ) ?>
							</label>
						</fieldset>
						<p />
					</div>
					<div class="span4">
						<?php if ( $mtconf->get('use_internal_notes') ) { ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>
								</div>
								<div class="controls">
									<textarea class="span12" rows="6" name="admin_note[<?php echo $row->claim_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div></div>
		<?php		$k = 1 - $k; } } ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="save_claims" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	/***
	* Reviews
	*/
	function list_reviews( &$reviews, &$link, &$pathWay, &$pageNav, $option ) {
		global $mtconf;
	?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( $link->link_id, 'index.php?option=com_mtree&task=listcats' ); ?></div></th>
			</tr>
			<tr>
				<th colspan="5" style="text-align:left"><?php echo $link->link_name; ?></th>
			</tr>
	  	</table>

		<table class="table table-stripped">
			<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="60%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?></th>
				<th width="15%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_USER' ) ?></th>
				<th width="10%" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?></th>
				<th width="15%" class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $reviews ); $i < $n; $i++) {
			$row = &$reviews[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20">
					<input type="checkbox" id="cb<?php echo $i;?>" name="rid[]" value="<?php echo $row->rev_id; ?>" onclick="Joomla.isChecked(this.checked);" />
				</td>
				<td align="left"><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editreview')"><?php echo $row->rev_title; ?></a></td>
				<td align="left"><?php echo (($row->user_id) ? $row->username : $row->guest_name); ?></td>
				<td align="center" class="hidden-phone"><?php if( $row->vote_total > 0 ) { 
					echo $row->vote_helpful.' '.JText::_( 'COM_MTREE_OF' ).' '.$row->vote_total; 
				} else {
					echo '-';
				}
				?></td>
				<td align="center" class="hidden-tablet hidden-phone"><?php echo $row->rev_date; ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>

			<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="reviews_list" />
		<input type="hidden" name="link_id" value="<?php echo $link->link_id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function editreview( &$row, &$pathWay, $returntask, $lists, $option ) {
		global $mtconf;
		
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'rev_text' );
		JHtml::_( 'behavior.calendar' );
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelreview') {
				submitform( pressbutton );
				return;
			}
			if (form.rev_text.value == ""){
				alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_ENTER_REVIEW_TEXT' ) ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( $row->link_id, 'index.php?option=com_mtree&task=listcats' ); ?></div></th>
			</tr>
	  	</table>

		
		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="span12 form-horizontal">

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_USER' ); ?></div>
				<div class="controls form-inline">
					<input type="text" name="owner" size="20" maxlength="250" value="<?php echo (($row->not_registered) ? $row->guest_name : $row->owner );?>" />
					&nbsp;
					<input type="checkbox" name="not_registered" id="not_registered" value="1" <?php echo (($row->not_registered) ? 'checked ' : '' ); ?>/>
					<label for="not_registered"><?php echo JText::_( 'COM_MTREE_THIS_IS_NOT_A_REGISTERED_USER' ) ?></label>
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?></div>
				<div class="controls">
					<input class="input-xxlarge" type="text" name="rev_title" size="60" maxlength="250" value="<?php echo $row->rev_title;?>" />
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_REVIEW' ) ?></div>
				<div class="controls">
					<textarea class="input-xxlarge" name="rev_text" cols="70" rows="15"><?php echo $row->rev_text; ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo JText::_( 'COM_MTREE_APPROVED' ) ?></div>
				<?php echo $lists['rev_approved'] ?>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?></div>
				<div class="controls">
					<input class="input-mini" type="text" name="vote_helpful" size="3" maxlength="4" value="<?php echo $row->vote_helpful;?>" /> <?php echo JText::_( 'COM_MTREE_OF' ) ?> <input class="input-mini" type="text" name="vote_total" size="3" maxlength="4" value="<?php echo $row->vote_total;?>" />
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_OVERRIDE_CREATED_DATE' ) ?></div>
				<div class="controls">
					<?php echo JHtml::_('calendar', $row->rev_date, 'rev_date', 'rev_date', '%Y-%m-%d %H:%M:%S', array('class'=>'', 'size'=>'25',  'maxlength'=>'19')); ?>
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo JText::_( 'COM_MTREE_OWNERS_REPLY' ) ?></div>
				<div class="controls">
					<textarea class="input-xxlarge" name="ownersreply_text" cols="70" rows="8"><?php echo $row->ownersreply_text; ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo JText::_( 'COM_MTREE_APPROVED' ) ?></div>
				<?php echo $lists['ownersreply_approved'] ?>
			</div>

			<input type="hidden" name="rev_id" value="<?php echo $row->rev_id; ?>" />
			<input type="hidden" name="link_id" value="<?php echo $row->link_id; ?>" />
			<input type="hidden" name="returntask" value="<?php echo $returntask ?>" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_( 'form.token' ); ?>
		
		</div>
		
		</form>
<?php
	}

	/***
	* Search
	*/
	function searchresults_links( &$links, &$pageNav, &$pathWay, $search_where, $search_text, $option ) {
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();
	?>
		<script language="javascript" type="text/javascript">
			function listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="table table-striped">
			<thead>
				<th width="1%">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th class="title" width="35%" nowrap="nowrap" style="text-align:left"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="35%" align="left" nowrap="nowrap" style="text-align:left" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="1%" class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th width="1%" class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="1%"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td width="20">
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
				</td>
				<td><a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a></td>
				<td class="hidden-phone"><?php echo $pathWay->printPathWayFromLink( $row->link_id, '' ); ?></td>
				<td align="center" class="hidden-tablet hidden-phone"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  	<td align="center" class="hidden-tablet hidden-phone">
					<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
				</td>
			  	<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="search_text" value="<?php echo $search_text ?>" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
	<?php
	}

	function searchresults_categories( &$rows, &$pageNav, &$pathWay, $search_where, $search_text, $option ) {
?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th style="text-align:left" class="title" width="25%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th style="text-align:left" align="left" width="65%" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_PARENT' ) ?></th>
				<th class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
				<th class="hidden-phone"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20">
					<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="Joomla.isChecked(this.checked);" />
				</td>
				<td width="50%" align="left">
					<a href="#go" onclick="return listItemTask('cb<?php echo $i;?>','listcats')"><?php 
						echo $row->cat_name; ?></a>
				</td>
				<td align="left" class="hidden-phone"><?php echo $pathWay->printPathWayFromCat( $row->cat_id, 0 ); ?></td>
				<td align="center" class="hidden-tablet hidden-phone"><?php echo $row->cat_cats; ?></td>
				<td align="center" class="hidden-tablet hidden-phone"><?php echo $row->cat_links; ?></td>
				<td width="10%" align="center" class="hidden-phone">
					<?php echo JHtml::_('mtree.featured', $row->cat_featured, $i,'cat_'); ?>
				</td>
			  	<td width="10%" align="center">
					<?php  echo JHtml::_('jgrid.published', $row->cat_published, $i, 'cat_', true); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="search_text" value="<?php echo $search_text ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
	<?php
	}

	/***
	* Tree Template
	*/
	function list_templates( $rows, $option ) {
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="table table-striped">
			<thead>
			<tr>
				<th class="title" width="30%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></th>
				<th class="title" width="60%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></th>
				<th class="title" width="10%" nowrap="nowrap" align="center"><?php echo JText::_( 'COM_MTREE_DEFAULT' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td><input type="radio" id="cb<?php echo $i ?>" name="template" value="<?php echo $row->directory; ?>" onClick="Joomla.isChecked(this.checked);" /> <a href="" onClick="return listItemTask('cb<?php echo $i ?>','template_pages')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td align="center"><?php 
					echo ($row->default) ? '<i class="icon-star"></i>' : '&nbsp;' ;
				 ?></td>
			</tr>
			<?php		$k = 1 - $k; } ?>
			<tfoot>
			<tr><th colspan="3">&nbsp;</th></tr>
			</tfoot>
		</table>

		<p />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	function template_pages( $template, $template_files, $template_name, $form, $option ) {
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>

	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
			<th class="templates"><?php echo JText::_( 'COM_MTREE_TREE_TEMPLATES' ) ?>: <? echo $template_name ?></th>
		</tr>
	</table>

	<div class="row-fluid">
	<?php if(!is_null($form)) { JHtml::_('behavior.tooltip');	?>
	<div class="span9 form-horizontal">
	<?php
	echo '<fieldset class="adminform long" style="min-width:240px">';
	?>
	<legend><?php echo JText::_( 'COM_MTREE_PARAMETERS' ) ?></legend>
	<?php
	foreach ($form->getFieldsets() as $fieldset): 

		foreach($form->getFieldset($fieldset->name) AS $field):
		?>
	            <?php if ($field->hidden): ?>
	                 <?php echo $field->input;?>
	            <?php elseif ($field->type == 'Spacer'):?>
	                <?php echo $field->label; ?>
			<hr />
	            <?php else:?>
			<div class="control-group">
				<div class="control-label">
	                	<label><?php echo $field->label; ?></label>
				</div>
				<div class="controls">
				<?php echo $field->input;?>
				</div>
			</div>
	            <?php endif;?>
	       <?php endforeach;

	endforeach;
	echo '</fieldset>';
	?>	
	</div>
	<?php } ?>
	
	<div class="span3 form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MTREE_SELECT_TEMPLATE_FILE_TO_EDIT' ) ?></legend>
			<ul>
				<li><strong><?php echo JText::_( 'COM_MTREE_POPULAR_TEMPLATE_FILES' ); ?></strong></li>
				<?php
				$popular_template_files = array(
					'sub_listingDetails.tpl.php',
					'page_index.tpl.php',
					'sub_listingSummary.tpl.php',
					'page_advSearch.tpl.php',
					'page_subCatIndex.tpl.php',
					'sub_map.tpl.php',
					'page_addListing.tpl.php'
					);
				
				sort($popular_template_files);
				
				foreach( $popular_template_files AS $popular_template_file )
				{
					if( in_array($popular_template_file,$template_files) )
					{
						echo '<li>';
						echo '<a href="'
							. JRoute::_(
								"index.php?option=$option&task=edit_templatepage&template=$template&page=".str_replace('.tpl.php','',$popular_template_file)
							)
							. '">';
						echo $popular_template_file;
						echo '</a>';
						echo '</li>';
					}
				}
				
				echo '<li style="border-bottom:1px solid #ccc"></li>';
				echo '<li><strong>' . JText::_( 'COM_MTREE_OTHER_TEMPLATE_FILES' ) . '</strong></li>';

				foreach( $template_files AS $template_file )
				{
					if( in_array($template_file,$popular_template_files) )
					{
						continue;
					}
					
					echo '<li>';
					echo '<a href="'
						. JRoute::_(
							"index.php?option=$option&task=edit_templatepage&template=$template&page=".str_replace('.tpl.php','',$template_file)
						)
						. '">';
					echo $template_file;
					echo '</a>';
					echo '</li>';
				}
				?>
			</ul>
		</fieldset>
	</div>
	</div>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="template" value="<?php echo $template ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}
	
	function edit_templatepage( $page, $template, $content, $option ) {
		global $mtconf;
		?>
		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function(task)
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<fieldset>
			<legend>
				/components/com_mtree/templates/<?php echo $template; ?>/<?php echo $page; ?>.tpl.php
	      		<?php
	      		$template_path = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template . '/'.$page.'.tpl.php';
	      		echo is_writable( $template_path ) ? '<b><font color="orange"> - '.JText::_( 'COM_MTREE_WRITEABLE' ).'</font></b>' : '<b><font color="red"> - '.JText::_( 'COM_MTREE_UNWRITEABLE' ).'</font></b>';
	      		?>
			</legend>
		<table class="admintable" width="100%">
		<tr>
			<td>
			<textarea cols="90" rows="50" name="pagecontent" style="width:100%"><?php echo $content; ?></textarea>
			</td>
		</tr>
		</table>
		</fieldset>
		
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="page" value="<?php echo $page; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	function new_template( $option ) {
		global $mtconf;
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
	<tr><th><?php echo JText::_( 'COM_MTREE_UPLOAD_PACKAGE_FILE' ) ?></th></tr>
	<tr>
		<td align="left">
		<?php echo JText::_( 'COM_MTREE_PACKAGE_FILE' ) ?>:
		<input name="template" type="file" size="70"/>
		<input class="button" type="submit" value="<?php echo JText::_( 'COM_MTREE_UPLOAD_FILE_AND_INSTALL' ) ?>" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option ?>" />
	<input type="hidden" name="task" value="install_template" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	
	<p />
	
	<table class="content">
	<?php
		echo '<td class="item">';
		echo '<strong>/components/com_mtree/templates</strong>';
		echo '</td><td align="left">';
		if( is_writable( $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates' ) ) {
			echo '<b><font color="green">Writeable</font></b>';
		} else {
			echo '<b><font color="red">Unwriteable</font></b>';
		} 
	?></td></tr>
		
	</table>
	<?php
	}
	
	function copy_template( $template, $template_name, $option )
	{
		JHtml::_('behavior.tooltip');
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MTREE_NEW_TEMPLATE' ); ?></legend>
		<table cellspacing="1" class="admintable">
			<tbody><tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'COM_MTREE_ORIGINAL_TEMPLATE' ); ?>
				</td>
				<td>
					<strong>
						<em><?php echo $template_name; ?> (<?php echo $template; ?>)</em>
					</strong>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_name">
						<?php echo JText::_( 'COM_MTREE_TEMPLATE_NAME' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_name" name="new_template_name"/>
					<?php echo JHtml::_('tooltip',  JText::_( 'COM_MTREE_THE_NAME_OF_THE_NEW_TEMPLATE' ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_folder">
						<?php echo JText::_( 'COM_MTREE_FOLDER_NAME' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_folder" name="new_template_folder"/>
					<?php echo JHtml::_('tooltip',  JText::_( "COM_MTREE_THE_NAME_OF_THE_NEW_TEMPLATE_S_FOLDER_ENTER_ONLY_ALPHA_NUMERIC_VALUES_AND_UNDERSCORE" ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_creation_date">
						<?php echo JText::_( 'COM_MTREE_CREATION_DATE' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php echo strftime('%e %B %Y'); ?>" size="35" id="new_template_creation_date" name="new_template_creation_date"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author">
						<?php echo JText::_( 'COM_MTREE_AUTHOR' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php $my =& JFactory::getUser(); echo $my->name; ?>" size="35" id="new_template_author" name="new_template_author"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author_email">
						<?php echo JText::_( 'COM_MTREE_AUTHOR_EMAIL' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php $my =& JFactory::getUser(); echo $my->email; ?>" size="35" id="new_template_author_email" name="new_template_author_email"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author_url">
						<?php echo JText::_( 'COM_MTREE_AUTHOR_URL' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_author_url" name="new_template_author_url"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_copyright">
						<?php echo JText::_( 'COM_MTREE_COPYRIGHT' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_copyright" name="new_template_copyright"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_version">
						<?php echo JText::_( 'COM_MTREE_VERSION' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_version" name="new_template_version"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_description">
						<?php echo JText::_( 'COM_MTREE_TEMPLATE_DESCRIPTION' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_description" name="new_template_description"/>
				</td>
			</tr>			
		</tbody></table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="template" value="<?php echo $template; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}
	
	/***
	* Advanced Back-end Search
	*/
	function advsearch( $fields, $lists, $option ) {
		global $mtconf;
		?>
		<style type="text/css">
		.task-advsearch form ul {
			margin:0;
			padding:0;
			list-style-type:none
		}
		</style>

		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_SEARCH_LISTINGS' ) ?></legend>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button>
				<button type="reset" class="btn" /><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<?php echo JText::sprintf( 'COM_MTREE_RETURN_RESULTS_IF_X_OF_THE_FOLLOWING_CONDITIONS_ARE_MET',$lists['searchcondition'] ); ?>
			</div>
		</div>

		<?php
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			if($field->hasSearchField()) {
			?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->caption; ?></div>
				<div class="controls">
					<?php echo $field->getSearchHTML(); ?>
				</div>
			</div>
			<?php
			}
			$fields->next();
		}
		?>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_( 'COM_MTREE_OWNER' ); ?></div>
			<div class="controls">
				<input name="owner" type="text" size="20" />
			</div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo JText::_( 'COM_MTREE_LISTING_PUBLISHING' ); ?></div>
			<div class="controls">
				<?php echo $lists['publishing'] ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo JText::_( 'COM_MTREE_TEMPLATE' ); ?></div>
			<div class="controls">
				<?php echo $lists['templates'] ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo JText::_( 'COM_MTREE_LISTING_NOTES' ); ?></div>
			<div class="controls">
				<input name="internal_notes" type="text" size="20" />
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button>
				<button type="reset" class="btn" /><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
			</div>
		</div>

		</fieldset>
		
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="advsearch2" />
		<input type="hidden" name="search_where" value="1" />
		</form>
		<?php		
	}

	function advsearchresults_links( &$links, &$fields, &$pageNav, &$pathWay, $search_where, $option ) {
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();
	?>
		<script language="javascript" type="text/javascript">
			function listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="table table-striped">
			<thead>
				<th width="1%"><input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" /></th>
				<th style="text-align:left" class="title" width="35%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th style="text-align:left" width="35%" align="left" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="1%" class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th class="hidden-tablet hidden-phone"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="1%"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td><input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" /></td>
				<td><a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a></td>
				<td class="hidden-phone"><?php echo '<a href="index.php?option=com_mtree&task=listcats&cat_id='.$row->cat_id.'">'.$pathWay->getCatName( $row->cat_id ).'</a>'; ?></td>
				<td align="center" class="hidden-tablet hidden-phone"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  	<td width="10%" align="center" class="hidden-tablet hidden-phone">
					<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
				</td>
			  	<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="advsearch2" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="searchcondition" value="<?php echo JFactory::getApplication()->input->getInt( 'searchcondition', 1 ); ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		<?php
		$post = $_POST;
		$core_fields = array('link_name', 'link_desc', 'website', 'address', 'city', 'state', 'country', 'postcode', 'telephone', 'fax', 'email', 'publishing', 'link_template', 'price', 'price_2', 'link_rating', 'link_featured', 'rating_2', 'link_votes', 'votes_2', 'link_hits', 'hits_2', 'internal_notes', 'metakey', 'metadesc', 'link_created', 'link_created_2', 'link_created_3', 'link_created_4', 'link_created_5', 'link_created_6', 'link_created_7', 'link_created_8', 'link_created_9', 'link_created_10', 'link_modified', 'link_modified_2', 'link_modified_3', 'link_modified_4', 'link_modified_5', 'link_modified_6', 'link_modified_7', 'link_modified_8', 'link_modified_9', 'link_modified_10');
		foreach($core_fields AS $core_field) {
			echo '<input type="hidden" name="' . $core_field . '" value="';
			if(isset($post[$core_field])) {
				echo $post[$core_field];
			}
			echo '" />';
		}

		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			if( array_key_exists('cf'.$field->id, $post) && !empty($post['cf'.$field->id]) ) {
				
				if( is_array($post['cf'.$field->id]) )
				{
					$array = $post['cf'.$field->id];
					foreach( $array AS $items[0]['value'] )
					{
						?>
						<input type="hidden" name="cf<?php echo $field->id ?>[]" value="<?php echo $items[0]['value']; ?>" /><?php
					}
				} else {
				?>
					<input type="hidden" name="cf<?php echo $field->id ?>" value="<?php echo $post['cf'.$field->id] ?>" /><?php
					if( $field->numOfSearchFields > 1 )
					{
						for($i=2; $i<=$field->numOfSearchFields; $i++)
						{
							?>
								<input type="hidden" name="cf<?php echo $field->id ?>_<?php echo $i; ?>" value="<?php echo $post['cf'.$field->id.'_'.$i] ?>" /><?php
						}
					}
				}
			}
			$fields->next();
		}

		?>
		</form>
	<?php
	}

	/***
	* CSV Import/Export
	*/
	function csv( $fields, $lists, $option ) {
		JHtml::_('behavior.framework', false);
	?>
  <script type="text/javascript" language="javascript">
		function submitbutton( pressbutton ) {
			var form = document.adminForm;

			// do field validation
			var temp = false;
			if(pressbutton=='csv_export') {
				var elts      = document.adminForm.elements['fields[]'];
				var elts_cnt  = (typeof(elts.length) != 'undefined')
											? elts.length
											: 0;

				for (var i = 0; i < elts_cnt; i++) {
						if (elts[i].checked == true) temp = true;
				} 
			} else {
				temp = true;
			}
			if (temp == true) {
				Joomla.submitform( pressbutton );
			} else {
				alert('<?php echo JText::_( 'COM_MTREE_PLEASE_SELECT_AT_LEAST_ONE_FIELD' ) ?>');
			}
		}

		function setCheckboxes(the_form, do_check)
		{
				var elts      = document.forms[the_form].elements['fields[]'];
				var elts_cnt  = (typeof(elts.length) != 'undefined')
											? elts.length
											: 0;

				if (elts_cnt) {
						for (var i = 0; i < elts_cnt; i++) {
								elts[i].checked = do_check;
						}
				} else {
						elts.checked        = do_check;
				}

				return true;
		}
		</script>
		
		<style type="text/css">
		table td label {clear:none;}
		</style>
		
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

		<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_FIELDS' ); ?></legend>

		<?php
		$additional_fields = array(
			'l.link_id' => JText::_( 'COM_MTREE_LISTING_ID' ),
			'cat_id' => JText::_( 'COM_MTREE_CATEGORY_ID' ),
			'internal_notes' => JText::_( 'COM_MTREE_INTERNAL_NOTES' ),
			'lat' => JText::_( 'COM_MTREE_LATITUDE' ),
			'lng' => JText::_( 'COM_MTREE_LONGITUDE' ),
			'zoom' => JText::_( 'COM_MTREE_ZOOM' )			
			);
		$fields->resetPointer();
		$count=0;

		for($j=0;$j<$fields->getTotal() && $fields->hasNext();$j++) {
			$field = $fields->getField();
			?>
			<div class="span3">
				<label for="<?php echo $field->getInputFieldName(1) ?>">
					<input type="checkbox" name="fields[]" value="<?php echo $field->getInputFieldName(1) ?>" id="<?php echo $field->getInputFieldName(1) ?>" />
					<?php echo $field->getCaption(true) ?>
				</label>
			</div>
			<?php
			$count++;
			$fields->next();
		}
		
		foreach( $additional_fields AS $key => $caption )
		{
			?>
			<div class="span3">
				<label for="<?php echo $key ?>">
					<input type="checkbox" name="fields[]" value="<?php echo $key ?>" id="<?php echo $key ?>" />
					<?php echo $caption ?>
				</label>
			</div>
			<?php
		}
		?>
		<p />
		<div class="row-fluid">
			<div class="span12">
				<a href="#" onclick="jQuery('#adminForm').find(':checkbox').attr('checked', true);return false;"><?php echo JText::_( 'COM_MTREE_SELECT_ALL' ); ?></a> / <a href="#" onclick="jQuery('#adminForm').find(':checkbox').attr('checked', false); return false;"><?php echo JText::_( 'COM_MTREE_UNSELECT_ALL' ); ?></a>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span12">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_LISTING_PUBLISHING' ) ?>
					</div>
					<div class="controls">
						<?php echo $lists['publishing'] ?>
					</div>
				</div>
			</div>
		</div>

		</fieldset>
		
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}

	function csv_export( $header, $data, $option ) {
	?>
	<script language="Javascript">
	<!--
	/*
	Select and Copy form element script- By Dynamicdrive.com
	For full source, Terms of service, and 100s DTHML scripts
	Visit http://www.dynamicdrive.com
	*/

	//specify whether contents should be auto copied to clipboard (memory)
	//Applies only to IE 4+
	//0=no, 1=yes
	var copytoclip=1

	function HighlightAll(theField) {
	var tempval=eval("document."+theField)
	tempval.focus()
	tempval.select()
	if (document.all&&copytoclip==1){
	therange=tempval.createTextRange()
	therange.execCommand("Copy")
	window.status="Contents highlighted and copied to clipboard!"
	setTimeout("window.status=''",1800)
	}
	}
	//-->
	</script>

	<center>
	<form action="index.php" method="POST" name="adminForm" id="adminForm">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
		<tr class="row0">
			<td>
			<p />
			<a href="javascript:HighlightAll('adminForm.csv_excel')"><?php echo JText::_( 'COM_MTREE_SELECT_ALL' ) ?></a>
			<p />
			<textarea name="csv_excel" rows="30" cols="80" style="width:100%"><?php 
				echo $header; 
				echo $data;
			?></textarea>
			<p />
			<a href="javascript:HighlightAll('adminForm.csv_excel')"><?php echo JText::_( 'COM_MTREE_SELECT_ALL' ) ?></a>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="doreport" />
	</form>
	</center>
	<?php
	}

	/***
	* Configuration
	*/
	function config( $configs, $configgroups, $show, $option ) {
		JHtml::_('behavior.formvalidation');
	?>
<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<style type="text/css">
fieldset ul {
	list-style: none;
	margin: 0;
}
#adminForm input[type="checkbox"] {
	margin:-3px 4px 0 0;
}
#adminForm .control-label {
	min-width: 250px;
}
</style>
<form action="index.php" method="POST" name="adminForm" id="adminForm" id="application-form">

	<div class="span10 form-horizontal">

	<div id="category-configuration-sliders" class="tabbable">
		<?php
		echo '<ul class="nav nav-tabs">';
		foreach ($configgroups as $configgroup)
		{
			$active = "";
			if ($configgroup == 'main')
			{
				$active = "active";
			}
	
			echo '<li class="' . $active . '">';
				echo '<a href="#category-configuration-' . $configgroup . '" data-toggle="tab">';
				echo JText::_('COM_MTREE_'.$configgroup);
				echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
		
		echo '<div class="tab-content">';
		
		foreach( $configgroups AS $configgroup ) {
			$i = 0;
			$active = "";
			if ($configgroup == 'main')
			{
				$active = " active";
			}
			echo '<div class="tab-pane' . $active . '" id="category-configuration-' . $configgroup . '">';
			
			foreach( $configs AS $config )
			{ 
				if( $config->configcode == 'sort_direction' )
				{
					continue;
				}
				
				if( $config->groupname == $configgroup )
				{
					if( in_array($config->configcode, array('note')) ) {
						echo '<div style="margin:25px 0 15px 0;padding:0 0 5px 0;border-bottom: 1px solid #ddd;border-top: 0px solid #ddd; background-color: #FFFFFF">';
						echo JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($config->varname) );
						echo '</div>';
						continue;
					}
					
					echo "\n\n";
					echo '<div ';
					echo ' id="config_'.$config->varname.'"';
					echo ' class="control-group';
					echo '">';

					echo '<div class="control-label">';
					
					if( !in_array($config->configcode, array('sort_direction','predefined_reply')) ) {
						echo '<label>';

						$langcode = 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname);
						if( JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) ) == $langcode ) {
							echo $config->varname;
						} else {
							echo JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) );
						}
					
						if( substr($config->varname,0,4) == 'rss_' ) {
							if( $config->varname == 'rss_custom_fields') {
								echo ' (cust_#)';
							} else {
								echo ' ('.substr($config->varname,4).')';
							}
						}
						echo '</label>';
					}
					echo '</div>';
					echo '<div class="controls">';
					switch( $config->configcode ) {
						case 'text':
						case 'user_access':
						case 'user_access2':
						case 'sef_link_slug_type':
						default:
							$output = MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$config->varname,
										'value'		=>	$config->value,
									)
								)
							);
							
							if( $config->configcode == 'resize_method' ) {
								echo '<strong>'.$output.'</strong>';
							} else {
								echo $output;
							}
							break;
						case 'sort_direction':
							continue;
							break;
						case 'cat_order':
						case 'listing_order':
						case 'review_order':
							$tmp_varname = substr($config->varname,0,-1);
							echo MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$config->varname,
										'value'		=>	$config->value,
									),
									array(
										'varname'	=>	$tmp_varname.'2',
										'value'		=>	$configs[$tmp_varname.'2']->value,
									)
								)
							);
							if( substr($config->varname,-1) == '1' ) {
								unset($configs[$tmp_varname.'2']);
							} else {
								unset($configs[$tmp_varname.'1']);
							}
							break;
						case 'predefined_reply':
							continue;
							break;
						case 'predefined_reply_title':
							$tmp_varname = substr($config->varname,17,1);
							echo MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$tmp_varname.'_title',
										'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_title']->value,
									),
									array(
										'varname'	=>	$tmp_varname.'_message',
										'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_message']->value,
									)
								)
							);
							if( substr($config->varname,19) == 'title' ) {
								unset($configs['predefined_reply_'.$tmp_varname.'_message']);
							} else {
								unset($configs['predefined_reply_'.$tmp_varname.'_title']);
							}						
							break;
						case 'note':
							// Output nothing.
							break;
					} // End switch

					echo '</div>';
					echo '</div>';
					unset($configs[$config->varname]);
					$i++;
				}
			}
			echo '</div>';
		}
		echo '</div>';
		
		?>
	</div>

	</div>
  	<input type="hidden" name="option" value="<?php echo $option; ?>">
  	<input type="hidden" name="task" value="saveconfig">
  	<input type="hidden" name="show" value="<?php echo $show; ?>">
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}

	/***
	* Tools
	*/
	function tools( $lists, $option ) {
		global $mtconf;
		JHtml::_('behavior.modal');
		
	?>
	<table class="table table-stripped">
		<thead>
							<tr>
								<th width="140px">
									<?php echo JText::_( 'COM_MTREE_TOOL' ); ?>
								</th>
								<th colspan=2>
									<?php echo JText::_( 'COM_MTREE_DESCRIPTION' ); ?>
								</th>
							</tr>
						</thead>
		<tfoot>
							<tr>
								<th colspan="3">&nbsp;</th>
							</tr>
						</tfoot>
		<tbody>
							<tr>
								<td><a href="index.php?option=com_mtree&task=csv"><?php echo JText::_( 'COM_MTREE_EXPORT' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_EXPORT_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=geocode"><?php echo JText::_( 'COM_MTREE_LOCATE_LISTINGS_IN_MAP' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_LOCATE_LISTINGS_IN_MAP_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=globalupdate"><?php echo JText::_( 'COM_MTREE_RECOUNT_CATEGORIES_LISTINGS' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_RECOUNT_CATEGORIES_LISTINGS_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=rebuild_tree"><?php echo JText::_( 'COM_MTREE_REBUILD_TREE' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_REBUILD_TREE_DESC' ) ?></td>
							</tr>

							<tr>
								<td rowspan=2><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES' ) ?></td>
								<td colspan=2>
								<?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES_DESC' ) ?>
								</td>
							</tr>
							<tr>
								<td width=100px><?php echo JText::_('COM_MTREE_CUSTOM_FIELD'); ?></td>
								<td>
									<?php echo $lists['mweblinks'] ?>
									&nbsp;
									<a href="" class="modal" onclick="javascript:this.href='index.php?option=com_mtree&task=import_images&tmpl=component&limit=100&limitstart=0&cfid='+escape(document.getElementById('cfid').value)" rel="{handler: 'iframe', size: {x: 500, y: 210}, onClose: function() {}}">
								<?php echo JText::_('COM_MTREE_IMPORT_IMAGES'); ?>
									</a>
								</td>
							</tr>
							
							<tr>
								<td rowspan=2><?php echo JText::_( 'COM_MTREE_REBUILD_THUMBNAILS' ) ?></td>
								<td colspan=2>
								<?php echo JText::_( 'COM_MTREE_REBUILD_THUMBNAILS_DESC' ) ?>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_MTREE_CATEGORY'); ?></td>
								<td>
									<?php echo $lists['top_level_cats'] ?>
									&nbsp;
									<a href="" class="modal" onclick="javascript:this.href='index.php?option=com_mtree&task=rebuild_thumbnails&tmpl=component&limit=50&limitstart=0&cat_id='+escape(document.getElementById('rebuild_thumbnails_cat_id').value)" rel="{handler: 'iframe', size: {x: 500, y: 210}, onClose: function() {}}">
								<?php echo JText::_('COM_MTREE_REBUILD_THUMBNAILS'); ?>
									</a>
								</td>
							</tr>
						</tbody>
	</table>
	<?php
	}

	/***
	* Rebuild thumbnails
	*/
	function rebuild_thumbnails( $option, $cat_id, $next_link )
	{
		if( !empty($next_link) )
		{
		?>
		<h1><?php echo JText::_( 'COM_MTREE_REBUILDING_THUMBNAILS' ); ?></h1>
		
		<a href="<?php echo $next_link; ?>"><?php echo JText::_( 'COM_MTREE_NEXT' ); ?> &gt;</a>
		<?php
		JFactory::getApplication('site')->redirect( $next_link );
		
		}
		else
		{
		?>
		<h1 style="text-align:center"><?php echo JText::_( 'COM_MTREE_REBUILDING_THUMBNAILS_DONE' ); ?></h1>
		<p style="text-align:center"><?php echo JText::_( 'COM_MTREE_YOU_CAN_SAFELY_CLOSE_THIS_WINDOW_NOW' ); ?></p>
		<?php
		}
	}
	
	/***
	* Import images
	*/
	function import_images( $option, $cf_id, $next_link )
	{
		if( !empty($next_link) )
		{
		?>
		<h1><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES' ); ?></h1>
		
		<a href="<?php echo $next_link; ?>"><?php echo JText::_( 'COM_MTREE_NEXT' ); ?> &gt;</a>
		<?php
		JFactory::getApplication('site')->redirect( $next_link );
		
		}
		else
		{
		?>
		<h1 style="text-align:center"><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES_DONE' ); ?></h1>
		<p style="text-align:center"><?php echo JText::_( 'COM_MTREE_YOU_CAN_SAFELY_CLOSE_THIS_WINDOW_NOW' ); ?></p>
		<?php
		}
	}
	
	/***
	* About Mosets Tree
	*/
	function about() {
	global $mtconf;
	?>
	
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MTREE_GENERAL_INFORMATION', true)); ?>

	<div class="row-fluid">
		<div class="span3">
			<a href="http://www.mosets.com/tree/"><img width="260" height="62" src="..<?php echo $mtconf->get('relative_path_to_images'); ?>logo.png" alt="<?php echo $mtconf->get('name') ?>"></a>
		</div>
		<div class="span9">
			<div class="row-fluid">
				<table class="table table-striped">
					<tr>
						<td width="100">
							<strong><?php echo JText::_( 'COM_MTREE_VERSION' ); ?></strong>
						</td>
						<td>
							<?php echo $mtconf->get('version') ; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_( 'COM_MTREE_WEBSITE' ); ?></strong>
						</td>
						<td>
							<a href="http://www.mosets.com">www.mosets.com</a>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_( 'COM_MTREE_AUTHOR'); ?></strong>
						</td>
						<td>
							C.Y. Lee at Mosets Consulting
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_( 'COM_MTREE_EMAIL' ); ?></strong>
						</td>
						<td>
							<a href="mailto:mtree@mosets.com">mtree@mosets.com</a>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_( 'COM_MTREE_SUPPORT' ); ?></strong>
						</td>
						<td>
							<a href="http://forum.mosets.com/forumdisplay.php?f=25" target="_blank">Mosets Tree Priority Support</a>
						</td>
					</tr>
				</table>
			</div>			
		</div>
	</div>

	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'license', JText::_('COM_MTREE_LICENSE_AGREEMENT', true)); ?>

	<table class="table table-stripped">
		<tfoot>
			<tr>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>

					<h3><a href="index.php?option=com_mtree&amp;task=about">GNU GENERAL PUBLIC LICENSE</a></h3>
					<p>
					Version 2, June 1991
					</p>
					Copyright (C) 1989, 1991 Free Software Foundation, Inc.<br />
					51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA<br />
					<p />
					Everyone is permitted to copy and distribute verbatim copies of this license document, but changing it is not allowed.

					<h3>Preamble</h3>

					<p>
					  The licenses for most software are designed to take away your
					freedom to share and change it.  By contrast, the GNU General Public
					License is intended to guarantee your freedom to share and change free
					software--to make sure the software is free for all its users.  This
					General Public License applies to most of the Free Software
					Foundation's software and to any other program whose authors commit to
					using it.  (Some other Free Software Foundation software is covered by
					the GNU Lesser General Public License instead.)  You can apply it to
					your programs, too.
					</p>

					<p>
					  When we speak of free software, we are referring to freedom, not
					price.  Our General Public Licenses are designed to make sure that you
					have the freedom to distribute copies of free software (and charge for
					this service if you wish), that you receive source code or can get it
					if you want it, that you can change the software or use pieces of it
					in new free programs; and that you know you can do these things.
					</p>

					<p>
					  To protect your rights, we need to make restrictions that forbid
					anyone to deny you these rights or to ask you to surrender the rights.
					These restrictions translate to certain responsibilities for you if you
					distribute copies of the software, or if you modify it.
					</p>

					<p>
					  For example, if you distribute copies of such a program, whether
					gratis or for a fee, you must give the recipients all the rights that
					you have.  You must make sure that they, too, receive or can get the
					source code.  And you must show them these terms so they know their
					rights.
					</p>

					<p>
					  We protect your rights with two steps: (1) copyright the software, and
					(2) offer you this license which gives you legal permission to copy,
					distribute and/or modify the software.
					</p>

					<p>
					  Also, for each author's protection and ours, we want to make certain
					that everyone understands that there is no warranty for this free
					software.  If the software is modified by someone else and passed on, we
					want its recipients to know that what they have is not the original, so
					that any problems introduced by others will not reflect on the original
					authors' reputations.
					</p>

					<p>
					  Finally, any free program is threatened constantly by software
					patents.  We wish to avoid the danger that redistributors of a free
					program will individually obtain patent licenses, in effect making the
					program proprietary.  To prevent this, we have made it clear that any
					patent must be licensed for everyone's free use or not licensed at all.
					</p>

					<p>
					  The precise terms and conditions for copying, distribution and
					modification follow.
					</p>


					<h3>TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION</h3>


					<a name="section0"></a><p>
					<strong>0.</strong>
					 This License applies to any program or other work which contains
					a notice placed by the copyright holder saying it may be distributed
					under the terms of this General Public License.  The "Program", below,
					refers to any such program or work, and a "work based on the Program"
					means either the Program or any derivative work under copyright law:
					that is to say, a work containing the Program or a portion of it,
					either verbatim or with modifications and/or translated into another
					language.  (Hereinafter, translation is included without limitation in
					the term "modification".)  Each licensee is addressed as "you".
					</p>

					<p>
					Activities other than copying, distribution and modification are not
					covered by this License; they are outside its scope.  The act of
					running the Program is not restricted, and the output from the Program
					is covered only if its contents constitute a work based on the
					Program (independent of having been made by running the Program).
					Whether that is true depends on what the Program does.
					</p>

					<a name="section1"></a><p>
					<strong>1.</strong>
					 You may copy and distribute verbatim copies of the Program's
					source code as you receive it, in any medium, provided that you
					conspicuously and appropriately publish on each copy an appropriate
					copyright notice and disclaimer of warranty; keep intact all the
					notices that refer to this License and to the absence of any warranty;
					and give any other recipients of the Program a copy of this License
					along with the Program.
					</p>

					<p>
					You may charge a fee for the physical act of transferring a copy, and
					you may at your option offer warranty protection in exchange for a fee.
					</p>

					<a name="section2"></a><p>
					<strong>2.</strong>
					 You may modify your copy or copies of the Program or any portion
					of it, thus forming a work based on the Program, and copy and
					distribute such modifications or work under the terms of Section 1
					above, provided that you also meet all of these conditions:
					</p>

					<dl>
					  <dt></dt>
					    <dd>
					      <strong>a)</strong>
					      You must cause the modified files to carry prominent notices
					      stating that you changed the files and the date of any change.
					    </dd>
					  <dt></dt>
					    <dd>
					      <strong>b)</strong>
					      You must cause any work that you distribute or publish, that in
					      whole or in part contains or is derived from the Program or any
					      part thereof, to be licensed as a whole at no charge to all third
					      parties under the terms of this License.
					    </dd>
					  <dt></dt>
					    <dd>
					      <strong>c)</strong>
					      If the modified program normally reads commands interactively
					      when run, you must cause it, when started running for such
					      interactive use in the most ordinary way, to print or display an
					      announcement including an appropriate copyright notice and a
					      notice that there is no warranty (or else, saying that you provide
					      a warranty) and that users may redistribute the program under
					      these conditions, and telling the user how to view a copy of this
					      License.  (Exception: if the Program itself is interactive but
					      does not normally print such an announcement, your work based on
					      the Program is not required to print an announcement.)
					    </dd>
					</dl>

					<p>
					These requirements apply to the modified work as a whole.  If
					identifiable sections of that work are not derived from the Program,
					and can be reasonably considered independent and separate works in
					themselves, then this License, and its terms, do not apply to those
					sections when you distribute them as separate works.  But when you
					distribute the same sections as part of a whole which is a work based
					on the Program, the distribution of the whole must be on the terms of
					this License, whose permissions for other licensees extend to the
					entire whole, and thus to each and every part regardless of who wrote it.
					</p>

					<p>
					Thus, it is not the intent of this section to claim rights or contest
					your rights to work written entirely by you; rather, the intent is to
					exercise the right to control the distribution of derivative or
					collective works based on the Program.
					</p>

					<p>
					In addition, mere aggregation of another work not based on the Program
					with the Program (or with a work based on the Program) on a volume of
					a storage or distribution medium does not bring the other work under
					the scope of this License.
					</p>

					<a name="section3"></a><p>
					<strong>3.</strong>
					 You may copy and distribute the Program (or a work based on it,
					under Section 2) in object code or executable form under the terms of
					Sections 1 and 2 above provided that you also do one of the following:
					</p>

					<!-- we use this doubled UL to get the sub-sections indented, -->
					<!-- while making the bullets as unobvious as possible. -->

					<dl>
					  <dt></dt>
					    <dd>
					      <strong>a)</strong>
					      Accompany it with the complete corresponding machine-readable
					      source code, which must be distributed under the terms of Sections
					      1 and 2 above on a medium customarily used for software interchange; or,
					    </dd>
					  <dt></dt>
					    <dd>
					      <strong>b)</strong>
					      Accompany it with a written offer, valid for at least three
					      years, to give any third party, for a charge no more than your
					      cost of physically performing source distribution, a complete
					      machine-readable copy of the corresponding source code, to be
					      distributed under the terms of Sections 1 and 2 above on a medium
					      customarily used for software interchange; or,
					    </dd>
					  <dt></dt>
					    <dd>
					      <strong>c)</strong>
					      Accompany it with the information you received as to the offer
					      to distribute corresponding source code.  (This alternative is
					      allowed only for noncommercial distribution and only if you
					      received the program in object code or executable form with such
					      an offer, in accord with Subsection b above.)
					    </dd>
					</dl>

					<p>
					The source code for a work means the preferred form of the work for
					making modifications to it.  For an executable work, complete source
					code means all the source code for all modules it contains, plus any
					associated interface definition files, plus the scripts used to
					control compilation and installation of the executable.  However, as a
					special exception, the source code distributed need not include
					anything that is normally distributed (in either source or binary
					form) with the major components (compiler, kernel, and so on) of the
					operating system on which the executable runs, unless that component
					itself accompanies the executable.
					</p>

					<p>
					If distribution of executable or object code is made by offering
					access to copy from a designated place, then offering equivalent
					access to copy the source code from the same place counts as
					distribution of the source code, even though third parties are not
					compelled to copy the source along with the object code.
					</p>

					<a name="section4"></a><p>
					<strong>4.</strong>
					 You may not copy, modify, sublicense, or distribute the Program
					except as expressly provided under this License.  Any attempt
					otherwise to copy, modify, sublicense or distribute the Program is
					void, and will automatically terminate your rights under this License.
					However, parties who have received copies, or rights, from you under
					this License will not have their licenses terminated so long as such
					parties remain in full compliance.
					</p>

					<a name="section5"></a><p>
					<strong>5.</strong>
					 You are not required to accept this License, since you have not
					signed it.  However, nothing else grants you permission to modify or
					distribute the Program or its derivative works.  These actions are
					prohibited by law if you do not accept this License.  Therefore, by
					modifying or distributing the Program (or any work based on the
					Program), you indicate your acceptance of this License to do so, and
					all its terms and conditions for copying, distributing or modifying
					the Program or works based on it.
					</p>

					<a name="section6"></a><p>
					<strong>6.</strong>
					 Each time you redistribute the Program (or any work based on the
					Program), the recipient automatically receives a license from the
					original licensor to copy, distribute or modify the Program subject to
					these terms and conditions.  You may not impose any further
					restrictions on the recipients' exercise of the rights granted herein.
					You are not responsible for enforcing compliance by third parties to
					this License.
					</p>

					<a name="section7"></a><p>
					<strong>7.</strong>
					 If, as a consequence of a court judgment or allegation of patent
					infringement or for any other reason (not limited to patent issues),
					conditions are imposed on you (whether by court order, agreement or
					otherwise) that contradict the conditions of this License, they do not
					excuse you from the conditions of this License.  If you cannot
					distribute so as to satisfy simultaneously your obligations under this
					License and any other pertinent obligations, then as a consequence you
					may not distribute the Program at all.  For example, if a patent
					license would not permit royalty-free redistribution of the Program by
					all those who receive copies directly or indirectly through you, then
					the only way you could satisfy both it and this License would be to
					refrain entirely from distribution of the Program.
					</p>

					<p>
					If any portion of this section is held invalid or unenforceable under
					any particular circumstance, the balance of the section is intended to
					apply and the section as a whole is intended to apply in other
					circumstances.
					</p>

					<p>
					It is not the purpose of this section to induce you to infringe any
					patents or other property right claims or to contest validity of any
					such claims; this section has the sole purpose of protecting the
					integrity of the free software distribution system, which is
					implemented by public license practices.  Many people have made
					generous contributions to the wide range of software distributed
					through that system in reliance on consistent application of that
					system; it is up to the author/donor to decide if he or she is willing
					to distribute software through any other system and a licensee cannot
					impose that choice.
					</p>

					<p>
					This section is intended to make thoroughly clear what is believed to
					be a consequence of the rest of this License.
					</p>

					<a name="section8"></a><p>
					<strong>8.</strong>
					 If the distribution and/or use of the Program is restricted in
					certain countries either by patents or by copyrighted interfaces, the
					original copyright holder who places the Program under this License
					may add an explicit geographical distribution limitation excluding
					those countries, so that distribution is permitted only in or among
					countries not thus excluded.  In such case, this License incorporates
					the limitation as if written in the body of this License.
					</p>

					<a name="section9"></a><p>
					<strong>9.</strong>
					 The Free Software Foundation may publish revised and/or new versions
					of the General Public License from time to time.  Such new versions will
					be similar in spirit to the present version, but may differ in detail to
					address new problems or concerns.
					</p>

					<p>
					Each version is given a distinguishing version number.  If the Program
					specifies a version number of this License which applies to it and "any
					later version", you have the option of following the terms and conditions
					either of that version or of any later version published by the Free
					Software Foundation.  If the Program does not specify a version number of
					this License, you may choose any version ever published by the Free Software
					Foundation.
					</p>

					<a name="section10"></a><p>
					<strong>10.</strong>
					 If you wish to incorporate parts of the Program into other free
					programs whose distribution conditions are different, write to the author
					to ask for permission.  For software which is copyrighted by the Free
					Software Foundation, write to the Free Software Foundation; we sometimes
					make exceptions for this.  Our decision will be guided by the two goals
					of preserving the free status of all derivatives of our free software and
					of promoting the sharing and reuse of software generally.
					</p>

					<a name="section11"></a><p><strong>NO WARRANTY</strong></p>

					<p>
					<strong>11.</strong>
					 BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY
					FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.  EXCEPT WHEN
					OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
					PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED
					OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
					MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  THE ENTIRE RISK AS
					TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU.  SHOULD THE
					PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING,
					REPAIR OR CORRECTION.
					</p>

					<a name="section12"></a><p>
					<strong>12.</strong>
					 IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
					WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY AND/OR
					REDISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES,
					INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING
					OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED
					TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY
					YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER
					PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE
					POSSIBILITY OF SUCH DAMAGES.
					</p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>


	<?php
	}

}

class MTConfigHtml {
	
	public static function _($function, $items=array(), $config=null)
	{
		$args = func_get_args();
		array_shift($args);
		$i = 0;

		foreach( $items AS $item )
		{
			if( !isset($item['override']) ) {
				$item['override'] = null;
			}

			if( !isset($items[$i]['override']) ) {
				$items[$i]['override'] = null;
				$args[0][$i]['override'] = null;
			}
			
			if( !is_null($item['override']) ) {
				// echo '<p />[override '.$item['varname'].' : '.$item['override'].']';
			}
			
			if( !empty($config['namespace']) ) {
				$args[0][$i]['varname'] = $config['namespace'] . '[' . $args[0][$i]['varname'] . ']';
			}
			$i++;
		}
		
		if( empty($function) )
		{
			return call_user_func_array(array('MTConfigHtml','self::text'), $args);
		}
		else
		{
			return call_user_func_array(array('MTConfigHtml','self::'.$function), $args);
		}
	}
	
	public static function overrideCheckbox($items=array(), $config=null)
	{
		$args = func_get_args();
		$i = 0;

		foreach( $items AS $item )
		{
			if( !empty($config['namespace']) ) {
				$args[0][$i]['varname'] = $config['namespace'] . '[' . $args[0][$i]['varname'] . ']';
			}
			$i++;
		}
		
		$checked = ($item['override'] != ''?true:false);
		$class = (!empty($config['class'])?'class="'.$config['class'].'" ':'');
		return '<input type="checkbox" name="override['.$item['varname'].']" value="1" '.($checked?'checked ':'').$class.'onclick="" />';
	}
	
	public static function text($items, $config=null)
	{
		return '<input name="'.$items[0]['varname'].'" value="'.self::getValue($items[0]).'" size="30" />';
	}

	public static function type_of_listings_in_index($items, $config=null)
	{
		# Listings type in index
		$type_of_listings_in_index = array();
		$arr_tmp = array('listcurrent','listpopular', 'listmostrated', 'listtoprated', 'listmostreview', 'listnew', 'listupdated', 'listfavourite', 'listfeatured');

		foreach( $arr_tmp AS $tmp )
		{
			$type_of_listings_in_index[] = JHtml::_('select.option', $tmp, JText::_( 'COM_MTREE_TYPES_OF_LISTINGS_IN_INDEX_OPTION_'.strtoupper($tmp) ) );
		}

		return JHtml::_('select.genericlist', $type_of_listings_in_index, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function owner_default_page($items, $config=null)
	{
		$default_owner_listing_page = array();
		$default_owner_listing_page[] = JHtml::_('select.option', "viewuserslisting", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSLISTING' ) );
		$default_owner_listing_page[] = JHtml::_('select.option', "viewusersfav", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSFAV' ) );
		$default_owner_listing_page[] = JHtml::_('select.option', "viewusersreview", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSREVIEW' ) );
		return JHtml::_('select.genericlist', $default_owner_listing_page, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function feature_locations($items, $config=null)
	{
		$feature_locations = array();
		$feature_locations[] = JHtml::_('select.option', "1", JText::_( 'COM_MTREE_STANDALONE_PAGE' ) );
		$feature_locations[] = JHtml::_('select.option', "2", JText::_( 'COM_MTREE_LISTING_DETAILS_PAGE' ) );
		return JHtml::_('select.genericlist', $feature_locations, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function user_access($items, $config=null)
	{
		$access = array();
		$access[] = JHtml::_('select.option', "-1", JText::_( 'JNONE' ) );
		$access[] = JHtml::_('select.option', "0", JText::_( 'COM_MTREE_PUBLIC' ) );
		$access[] = JHtml::_('select.option', "1", JText::_( 'COM_MTREE_REGISTERED_ONLY' ) );
		return JHtml::_('select.genericlist', $access, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}
	
	public static function user_access2($items, $config=null)
	{
		$access = array();
		$access[] = JHtml::_('select.option', "-1", JText::_( 'JNONE' ) );
		$access[] = JHtml::_('select.option', "0", JText::_( 'COM_MTREE_PUBLIC' ) );
		$access[] = JHtml::_('select.option', "1", JText::_( 'COM_MTREE_REGISTERED_ONLY' ) );
		$access[] = JHtml::_('select.option', "2", JText::_( 'COM_MTREE_REGISTERED_ONLY_EXCEPT_LISTING_OWNER' ) );
		return JHtml::_('select.genericlist', $access, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function sef_link_slug_type($items, $config=null)
	{
		$sef_link_slug_type = array();
		$sef_link_slug_type[] = JHtml::_('select.option', "1", JText::_( 'COM_MTREE_ALIAS' ) );
		$sef_link_slug_type[] = JHtml::_('select.option', "2", JText::_( 'COM_MTREE_LINK_ID' ) );
		$sef_link_slug_type[] = JHtml::_('select.option', "3", JText::_( 'COM_MTREE_LINK_ID_AND_ALIAS_HYBRID' ) );
		return JHtml::_('select.genericlist', $sef_link_slug_type, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function resize_method($items, $config=null)
	{
		$imageLibs=array();
		$imageLibs=detect_ImageLibs();
		return $imageLibs['gd2'];
	}
	
	public static function yesno($items, $config=null)
	{
		$arr = array(
			JHtml::_('select.option', '0', JText::_('JNO')),
			JHtml::_('select.option', '1', JText::_('JYES'))
		);

		$html = '<fieldset class="radio btn-group" id="'.str_replace(array('[',']'),array('_',''),$items[0]['varname']).'_fieldset">';

		$yesno_values = array(1,0);
		$value = (int) self::getValue($items[0]);
		
		foreach( $yesno_values AS $yesno_value )
		{
			$html .= '<input type="radio" ';
			if( $value == $yesno_value )
			{
				$html .= 'checked="checked" ';
			}
			$html .= 'value="'.$yesno_value.'" name="'.$items[0]['varname'].'" id="'.str_replace(array('[',']'),array('_',''),$items[0]['varname']).$yesno_value.'">';
			$html .= '<label for="'.str_replace(array('[',']'),array('_',''),$items[0]['varname']).$yesno_value.'" ';
			$html .= 'class="';
			
			$active = '';
			if( $value == $yesno_value )
			{
				$active = ' active';
			}

			switch( $yesno_value )
			{
				case 1:
					$html .= '">';
					$html .= JText::_( 'JYES' );
					break;
				case 0:
				default:
					$html .= '">';
					$html .= JText::_( 'JNO' );
					break;
					
			}
			$html .= '</label>';
		}

		$html .= '</fieldset>';
		return $html;
	}

	public static function cat_order($items, $config=null) {
		# Sort Direction
		$sort[] = JHtml::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHtml::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );

		# Category Order
		$cat_order = array();
		$cat_order[] = JHtml::_('select.option', '', JText::_( '' ) );
		$cat_order[] = JHtml::_('select.option', "lft", JText::_( 'COM_MTREE_CONFIG_CUSTOM_ORDER' ) );
		$cat_order[] = JHtml::_('select.option', "cat_name", JText::_( 'COM_MTREE_NAME' ) );
		$cat_order[] = JHtml::_('select.option', "cat_featured", JText::_( 'COM_MTREE_FEATURED' ) );
		$cat_order[] = JHtml::_('select.option', "cat_created", JText::_( 'COM_MTREE_CREATED' ) );
		
		$html = JHtml::_('select.genericlist', $cat_order, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHtml::_('select.genericlist', $sort, $items[1]['varname'], 'size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	
	public static function predefined_reply_title($items, $config=null)
	{
		$html = '<input name="'.$items[0]['varname'].'" value="'.self::getValue($items[0]).'" size="60" />';
		$html .= '<br />';
		$html .= '<textarea style="margin-top:5px" name="'.$items[1]['varname'].'" cols="80" rows="8" />'.self::getValue($items[1]).'</textarea>';
		
		return $html;
	}
	
	public static function note($items)
	{
		return JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($items[0]['varname']) );
	}
	
	public static function listing_order($items, $config=null)
	{
		# Sort Direction
		$sort[] = JHtml::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHtml::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );
		
		# Listing Order
		$listing_order = array();
		$listing_order[] = JHtml::_('select.option', "link_name", JText::_( 'COM_MTREE_NAME' ) );
		$listing_order[] = JHtml::_('select.option', "link_hits", JText::_( 'COM_MTREE_HITS' ) );
		$listing_order[] = JHtml::_('select.option', "link_votes", JText::_( 'COM_MTREE_VOTES' ) );
		$listing_order[] = JHtml::_('select.option', "link_rating", JText::_( 'COM_MTREE_RATING' ) );
		$listing_order[] = JHtml::_('select.option', "link_visited", JText::_( 'COM_MTREE_VISIT' ) );
		$listing_order[] = JHtml::_('select.option', "link_featured", JText::_( 'COM_MTREE_FEATURED' ) );
		$listing_order[] = JHtml::_('select.option', "link_created", JText::_( 'COM_MTREE_CREATED' ) );
		$listing_order[] = JHtml::_('select.option', "link_modified", JText::_( 'COM_MTREE_MODIFIED' ) );
		$listing_order[] = JHtml::_('select.option', "address", JText::_( 'COM_MTREE_ADDRESS' ) );
		$listing_order[] = JHtml::_('select.option', "city", JText::_( 'COM_MTREE_CITY' ) );
		$listing_order[] = JHtml::_('select.option', "state", JText::_( 'COM_MTREE_STATE' ) );
		$listing_order[] = JHtml::_('select.option', "country", JText::_( 'COM_MTREE_COUNTRY' ) );
		$listing_order[] = JHtml::_('select.option', "postcode", JText::_( 'COM_MTREE_POSTCODE' ) );
		$listing_order[] = JHtml::_('select.option', "telephone", JText::_( 'COM_MTREE_TELEPHONE' ) );
		$listing_order[] = JHtml::_('select.option', "fax", JText::_( 'COM_MTREE_FAX' ) );
		$listing_order[] = JHtml::_('select.option', "email", JText::_( 'COM_MTREE_EMAIL' ) );
		$listing_order[] = JHtml::_('select.option', "website", JText::_( 'COM_MTREE_WEBSITE' ) );
		$listing_order[] = JHtml::_('select.option', "price", JText::_( 'COM_MTREE_PRICE' ) );

		if( in_array('l.ordering', array( $items[0]['value'], $items[0]['override'], $items[1]['value'], $items[1]['override'])) )
		{
			$listing_order[] = JHtml::_('select.option', "l.ordering", JText::_( 'COM_MTREE_ORDERING' ) );
		}

		$html = JHtml::_('select.genericlist', $listing_order, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHtml::_('select.genericlist', $sort, $items[1]['varname'], 'size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	public static function review_order($items, $config=null)
	{
		# Sort Direction
		$sort[] = JHtml::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHtml::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );
		
		# Review Order
		$review_order[] = JHtml::_('select.option', '', JText::_( '' ) );
		$review_order[] = JHtml::_('select.option', "rev_date", JText::_( 'COM_MTREE_REVIEW_DATE' ) );
		$review_order[] = JHtml::_('select.option', "vote_helpful", JText::_( 'COM_MTREE_TOTAL_HELPFUL_VOTES' ) );
		$review_order[] = JHtml::_('select.option', "vote_total", JText::_( 'COM_MTREE_TOTAL_VOTES' ) );

		$html = JHtml::_('select.genericlist', $review_order, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHtml::_('select.genericlist', $sort, $items[1]['varname'], 'size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	public static function sort($items, $config=null)
	{
		$sort_by_options = array('-link_featured', '-link_created', '-link_modified', '-link_hits', '-link_visited', '-link_rating', '-link_votes', 'link_name', '-price','price');

		foreach( $sort_by_options AS $sort_by_option ) {
			$sort_by[] = JHtml::_('select.option', $sort_by_option, JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) ) );
		}
		$html = JHtml::_('select.genericlist', $sort_by, $items[0]['varname'], 'size="1"', 'value', 'text', self::getValue($items[0]) );
		return $html;
	}
	
	public static function sort_options($items, $config=null)
	{
		$sort_by_options = array('-link_featured', '-link_created', '-link_modified', '-link_hits', '-link_visited', '-link_rating', '-link_votes', 'link_name', '-price','price');

		$sort_by_option_values = self::getValue($items[0]);
		if( !is_array($sort_by_option_values) ) {
			$sort_by_option_values = explode('|',$sort_by_option_values);
		}

		$html = '';
		$html .= '<fieldset>';
		$html .= '<ul>';
		foreach( $sort_by_options AS $sort_by_option ) {
			$html .= '<li>';
			$html .= '<label style="">';
			$html .= '<input type="checkbox" name="'.$items[0]['varname'].'[]" value="'.$sort_by_option.'"';
			$html .= ' style="clear:left"';
			if( isset($sort_by_option_values) && in_array($sort_by_option,$sort_by_option_values) ) {
				$html .= ' checked';
			}
			$html .= ' />';
			$html .= JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) );
			$html .= '</label>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</fieldset>';
		return $html;		
	}
	
	public static function getValue($item) {
		if( isset($item['override']) && $item['override'] != '' ) {return $item['override'];}
		else {return $item['value'];}
	}

}
?>