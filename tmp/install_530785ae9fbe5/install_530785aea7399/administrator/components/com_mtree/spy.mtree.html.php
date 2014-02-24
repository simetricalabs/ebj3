<?php
/**
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

class HTML_mtspy {

	public static function viewClones( $option, $clones, $cloners_listings ) {
	?>
	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="15%"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="85%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CLONES' ) ?></th>
	</tr>
	</thead>
	<?php
	if( count($clones) > 0 ) {
		$ip = '';
		foreach( $clones AS $clone ) {
			if( empty($ip) OR $ip <> $clone->log_ip ) {
				$ip = $clone->log_ip;
				$clone_count[$clone->log_ip] = 1;
			} else {
				$clone_count[$clone->log_ip]++;
			}
			$clone_user[$clone->log_ip][] = array( 
				'username' => $clone->username, 
				'user_id' => $clone->user_id, 
				'name' => $clone->name, 
				'email' => $clone->email, 
				'num_of_links' => $clone->num_of_links,
				'blocked' => $clone->user_blocked
				); 
		}
		foreach( $clone_count AS $ip => $count ) {
			echo '<tr align="left">';
			//echo '<td>' . $ip. ' ('.$count.')</td>';
			echo '<td>' . $ip . '</td>';
			echo '<td>';
			foreach( $clone_user[$ip] AS $cuser ) {
				if( $cuser['num_of_links'] > 0 ) {
					echo '<b>' . mtfHTML::user( $cuser['user_id'], $cuser['username'], '', $cuser['blocked'] ) . '</b>';
					echo ' (';
					echo $cuser['num_of_links'].' listings';
					echo ')';
				} else {
					echo mtfHTML::user( $cuser['user_id'], $cuser['username'], '', $cuser['blocked'] );
				}
				echo '&nbsp; ';
			}
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="2">' . JText::_( 'COM_MTREE_NO_CLONE_DETECTED' ) . '</td></tr>';
	}
	?>
	</table>
	<?php if( count($clones) > 0 ) { ?>
	<br />
	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="45%"><?php echo JText::_( 'COM_MTREE_LISTINGS_OWNED_BY_SUSPECT_CLONERS' ) ?></th>
		<th width="22%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_OWNER' ) ?></th>
		<th width="20%" nowrap="nowrap" style="min-width:150px" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_RATINGS_AND_VOTES' ) ?></th>
		<th width="5%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_HITS' ) ?></th>
		<th width="8%" nowrap="nowrap" align="center" style="min-width:80px" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
	</tr>
	</thead>
	<?php
		$c=0;
		foreach( $cloners_listings AS $link ) {
			echo '<tr align="left">';
			echo '<td width="5">' . ++$c . '</td>';			
			echo '<td><a href="index.php?option='.$option.'&task=spy&task2=viewlisting&id='.$link->link_id.'">' . $link->link_name . '</a></td>';
			echo '<td>' . mtfHTML::user( $link->user_id, $link->username, $link->name ) . '</td>';
			echo '<td class="hidden-phone">' . mtfHTML::rating( $link->link_rating ) . '&nbsp; ' . $link->link_votes . ' votes</td>';
			echo '<td class="hidden-phone">' . ( ($link->link_hits) ? $link->link_hits : '-' ) . '</td>';
			echo '<td align="right" class="hidden-phone">' . date( 'j M y', strtotime($link->link_created) ) . '</td>';
			echo '</tr>';
		}

	?>
	</table>
	<?php
		}
	}

	public static function viewListing( $option, $Itemid, $listing_activities, $reviews, $listing, $clones ) {
		global $mtconf;
		JHtml::_('behavior.tooltip');
	?>
	<script language="javascript" type="text/javascript">
	function perform_action(ref) {
		switch(ref.options[ref.selectedIndex].value) {
			case '1':
				if( confirm('<?php echo JText::_( "COM_MTREE_CONFIRM_DELETE_LISTING_REVIEWS_AND_RATINGS" ) ?>') ) {
					location.href = "index.php?option=com_mtree&task=spy&task2=removelistingreviewsandratings&id=<?php echo $listing->link_id ?>";
				}
				break;
		}
	}
	</script>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		
	<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_LISTING' ) ?>: <?php echo $listing->link_name ?></legend>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_OWNER' ) ?>
					</div>
					<div class="controls">
						<?php echo mtfHTML::user( $listing->user_id, $listing->username, $listing->name ); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_RATING' ) ?>
					</div>
					<div class="controls">
						<?php echo mtfHTML::rating( $listing->link_rating ); echo '&nbsp; ' . $listing->link_rating . ' (<b>'.$listing->link_votes.' votes</b>)'; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?>
					</div>
					<div class="controls">
						<?php echo count($reviews); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_ACTION' ) ?>
					</div>
					<div class="controls">
						<select id="action" onchange="perform_action(this)">
							<option value=''></option>
							<option value='1'><?php echo JText::_( 'COM_MTREE_REMOVE_ALL_REVIEWS_AND_RESET_RATING' ) ?></option>
						</select>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_LISTING_ID' ) ?>
					</div>
					<div class="controls">
						<?php echo $listing->link_id; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_CREATED' ) ?>
					</div>
					<div class="controls">
						<?php echo $listing->link_created; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_MODIFIED' ) ?>
					</div>
					<div class="controls">
						<?php echo $listing->link_modified; ?>
					</div>
				</div>
				
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<a href="<?php echo $mtconf->getjconf('live_site') . '/index.php?option=com_mtree&task=viewlink&link_id=' . $listing->link_id . '&Itemid='.$Itemid; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a> | <a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $listing->link_id ?>"><?php echo JText::_( 'COM_MTREE_EDIT' ) ?></a>
				
			</div>
		</div>
	</fieldset>

	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="65%"><?php echo JText::_( 'COM_MTREE_ACTIVITIES' ) ?></th>
		<th width="33%"><?php echo JText::_( 'COM_MTREE_USER' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="12%" nowrap="nowrap" style="min-width:100px" align="center" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_DATE' ) ?></th>
	</tr>
	</thead>
	<?php
	$clone_votes=0;
	$removed_votes=0;
	if( count($listing_activities) == 0 ) {
		echo '<tr><td colspan="5"><i>No activities.</i></td></tr>';
	} else {
		$c=0;
		foreach( $listing_activities AS $ua ) {
			echo '<tr align="left">';
			echo '<td width="5">' . ++$c . '</td>';
			echo '<td>' . mtfHTML::userActivity( $ua->log_type, $ua->value, $ua->link_id, $ua->link_name );
			if( $ua->log_type == 'votereview' ) echo ' - <i>'.$ua->rev_title . '</i>';
			if( $ua->user_id == $listing->user_id ) {
				switch( $ua->log_type ) {
					case 'vote':
						echo ' <span class="owner_vote">' . JText::_( 'COM_MTREE_OWNER_VOTE' ) . '</span>';
						break;
					case 'votereview':
						echo ' <span class="owner_vote">' . JText::_( 'COM_MTREE_OWNER_VOTE' ) . '</span>';
						break;
				}
			} elseif( in_array( $ua->user_id, $clones ) ) {
				echo ' <span class="' . (($ua->user_blocked)?'clone_vote_removed':'clone_vote') . '">' . JText::_( 'COM_MTREE_CLONE_VOTE' ) . '</span>';
				$clone_votes++;
				
				if ($ua->user_blocked) {
					$removed_votes++;
				}

			}

			echo '</td>';
			echo '<td>' . mtfHTML::user( $ua->user_id, $ua->username, $ua->name, $ua->user_blocked ) . '</td>';
			echo '<td class="hidden-phone">' . mtfHTML::ipAddress( $ua->log_ip ) . '</td>';
			echo '<td align="right" class="hidden-phone">' . date( 'j M y, H:i', strtotime($ua->log_date) ) . '</td>';
			echo '</tr>';
		}
	}
	if( ($clone_votes - $removed_votes) > 0 ) {
	?>
	<tr><td colspan="5" align="left">
	<?php if ($removed_votes > 0 ) { 
		echo '<b>' . $removed_votes . ' votes</b> have been removed.<br />';
	}
	echo JText::sprintf( 'COM_MTREE_ANALYSIS_FOR_ACTUAL_RATING', round(((($clone_votes-$removed_votes)/$listing->link_votes)*100),2), ($clone_votes-$removed_votes), $listing->link_votes, round((($listing->link_votes*$listing->link_rating)-(5*($clone_votes-$removed_votes)))/($listing->link_votes-($clone_votes-$removed_votes)),2)  );
	?></b>.</td></tr>
	<?php } ?>
	</table>
	<?php
	if( count($reviews) > 0 ) {
	?>
	<br />
	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="5">#</th>
		<th width="35%"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?></th>
		<th width="35%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_USER' ) ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_DATE' ) ?></th>
	</tr>
	</thead>
	<?php

	$c=0;
	foreach( $reviews AS $review ) {
		echo '<tr align="left">';
		echo '<td width="5">' . ++$c . '</td>';
		echo '<td>';
		echo mtfHTML::review($review->rev_id,$review->rev_title, $review->rev_text, $review->rev_approved);
		if( $review->user_id == $listing->user_id ) {
			echo ' <span class="owner_rev">'.JText::_( 'COM_MTREE_OWNER_REVIEW' ).'</span>';
		}
		if( in_array( $review->user_id, $clones ) ) {
			echo ' <span class="clone_rev">'.JText::_( 'COM_MTREE_CLONE_REVIEW' ).'</span>';
		}
		'</td>';
		echo '<td>' . mtfHTML::helpfuls( $review->vote_helpful, $review->vote_total ) . '</td>';
		echo '<td>' . mtfHTML::user( $review->user_id, $review->username, $review->name, $review->user_blocked ) . '</td>';
		echo '<td>' . mtfHTML::ipAddress( $review->log_ip ) . '</a></td>';
		echo '<td>' . date( 'j M, H:i', strtotime($review->rev_date) ) . '</td>';
		echo '</tr>';
	}
	?></table>
	<?php } ?>

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="spy" />
	<input type="hidden" name="task2" value="users" />
	</form>
	<?php
	}

	public static function viewLinks( $option, $lists, $search, $pageNav, $links ) {
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_LISTING' ) ?>: <?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></legend>
		<div class="row-fluid">
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_LISTING_NAME' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="link_name" id="link_name" size="24" value="<?php if (isset($search['link_name'])) echo $search['link_name'] ?>" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_LISTING_ID' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="link_id" name="link_id" size="24" value="<?php if (isset($search['link_id']) && $search['link_id'] > 0) echo $search['link_id'] ?>" />
					</div>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span12">
				<button type="submit" class="btn btn-primary btn-small"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></button>
				<button class="btn btn-small" onclick="document.id('link_name').value='';document.id('link_id').value='';this.form.submit();"><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-right">
					<?php echo $lists['orderby']; ?>
					<?php echo $pageNav->getLimitBox(); ?>
				</div>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="5">#</th>
		<th width="70%"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
		<th width="22%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_OWNER' ) ?></th>
		<th width="20%" nowrap="nowrap" style="min-width:150px" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_RATINGS_AND_VOTES' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
		<th width="5%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_HITS' ) ?></th>
		<th width="15%" nowrap="nowrap" class="hidden-phone hidden-tablet"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
	</tr>
	</thead>
	<?php
		$c=0;
		foreach( $links AS $link ) {
			echo '<tr>';
			echo '<td width="5">' . $pageNav->getRowOffset( $c++ ) . '</td>';			
			echo '<td>' . mtfHTML::listing( $link->link_id, $link->link_name ) . '</td>';
			echo '<td>' . mtfHTML::user( $link->user_id, $link->username, $link->name ) . '</td>';
			echo '<td class="hidden-phone">' . mtfHTML::rating( $link->link_rating ) . '&nbsp; ' . $link->link_votes . ' votes</td>';
			echo '<td class="hidden-phone">' . ( ($link->reviews) ? $link->reviews : '-' ) . '</td>';
			echo '<td class="hidden-phone">' . ( ($link->link_hits) ? $link->link_hits : '-' ) . '</td>';
			echo '<td class="hidden-phone hidden-tablet nowrap">' . date( 'j M y', strtotime($link->link_created) ) . '</td>';
			echo '</tr>';
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
	<input type="hidden" name="task" value="spy" />
	<input type="hidden" name="task2" value="listings" />
	</form>
	<?php
	}

	public static function viewUser( $option, $Itemid, $user_activities, $reviews, $links, $user, $clones=array(), $removed_clones=array(), $lists=array() ) {
		global $mtconf;
		$task2	= strval(JFactory::getApplication()->input->getCmd( 'task2', ''));
		JHtml::_('behavior.tooltip');
	?>
	<script language="javascript" type="text/javascript">
	<?php if(array_key_exists('clone_owner',$lists)) { ?>
	jQuery.noConflict();
	function detectOther(ref) {
		if(ref.options[ref.selectedIndex].value == '-1') {
			jQuery('#clone_owner_username').css('display','inline');
			jQuery('#clone_owner_username')[0].focus();
		} else {
			jQuery('#clone_owner_username').css('display','none');
		}
	}
	function removeClone(){
		var owner = '';
		if(jQuery('#clone_owner').val() == '-1') {
			owner = jQuery('#clone_owner_username').val();
		} else {
			owner = jQuery('#clone_owner').val();
		}
		if(owner!='') {
			location.href = "index.php?option=com_mtree&task=spy&task2=removecloneandalllogs&id=<?php echo $user->id ?>&owner=" + owner + "&<?php echo JSession::getFormToken(); ?>=1";
		}
	}
	<?php } ?>
	function perform_action(ref) {
		switch(ref.options[ref.selectedIndex].value) {
			case '1':
				window.open("<?php echo $mtconf->getjconf('live_site') . '/index.php?option=com_mtree&task=viewowner&user_id=' . $user->id . '&Itemid='.$Itemid; ?>");
				break;
			case '2':
				location.href = "index.php?option=com_users&task=user.edit&id=<?php echo $user->id ?>";
				break;
			case '3':
				if( confirm('<?php echo JText::_( 'COM_MTREE_CONFIRM_REMOVE_USER_AND_ALL_ITS_DATA' ) ?>') ) {
					location.href = "index.php?option=com_mtree&task=spy&task2=removeuserandalllogs&id=<?php echo $user->id ?>&<?php echo JSession::getFormToken(); ?>=1";
				}
				break;
			case '4':
				jQuery('#clone').slideDown('fast');
				break;
		}
	}

	function submitbutton(pressbutton) {
		var form = document.adminForm;
		form.task.value = 'spy';
		form.task2.value = 'removelogs';
		form.submit();
	}
	</script>
	<style type="text/css">
	fieldset input, fieldset select {
		margin: 0;
		float: none;
	}
	</style>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<fieldset>
		<legend><?php echo ($task2 == 'viewclone') ? JText::_( 'COM_MTREE_CLONE' ) : JText::_( 'COM_MTREE_USER' ) ?>: <?php echo $user->name ?></legend>

		<div class="row-fluid">
			<div class="span6">

				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_USERNAME' ) ?>
					</div>
					<div class="controls">
						<b><?php echo $user->username; ?></b>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_EMAIL' ) ?>
					</div>
					<div class="controls">
						<a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a>
					</div>
				</div>

				<?php if($task2 == 'viewuser') { ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_ACTION' ) ?>: 
					</div>
					<div class="controls">
						<select id="action" onchange="perform_action(this)">
							<option value=''></option>
							<option value='1'><?php echo JText::_( 'COM_MTREE_VIEW_USERS_PAGE_IN_FRONT_END' ) ?></option>
							<option value='2'><?php echo JText::_( 'COM_MTREE_EDIT_USER_IN_USER_MANAGER' ) ?></option>
							<?php if( count($links) <= 0 ) { ?>							
							<option value='3'><?php echo JText::_( 'COM_MTREE_REMOVE_USER_INCLUDING_HIS_HER_ACTIVITIES' ) ?></option>
							<option value='4'><?php echo JText::_( 'COM_MTREE_MARK_THIS_USER_AS_CLONE' ) ?></option>
							<?php } ?>
						</select>							
					</div>
				</div>

				<?php
				if(count($lists)>0) {
					echo '<div id="clone" style="display:none">';
					echo '<table cellpadding="2">';
					echo '<tr><td>';
					echo 'Set the clone owner to:';
					echo '&nbsp;';
					echo $lists['clone_owner'];
					echo '&nbsp;<input type="text" id="clone_owner_username" style="display:none" size="20" />';
					echo '</td></tr>';
					echo '<tr><td align="left">';
					echo '<input type="button" onclick="removeClone()" value="' . JText::sprintf( 'COM_MTREE_REMOVE_USER_INCLUDING_HIS_HER_ACTIVITIES',$user->username ) . '" />';
					echo '&nbsp; or &nbsp;<a href="#" onclick="jQuery(\'#clone\').slideUp(\'fast\');jQuery(\'#action\').val(\'\');return false;">' . JText::_( 'COM_MTREE_CANCEL' ) . '</a>';
					echo '</table>';
					echo '</div>';
				}

				} ?>
			</div>

			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_USER_ID' ) ?>
					</div>
					<div class="controls">
						<b><?php echo $user->id; ?></b>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_REGISTER' ) ?>
					</div>
					<div class="controls">
						<b><?php echo $user->registerDate; ?></b>							
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_LAST_VISIT' ) ?>
					</div>
					<div class="controls">
						<b><?php echo $user->lastvisitDate; ?></b>
					</div>
				</div>
			</div>
		</div>

		<?php
		if(count($removed_clones)>0) {
		?>
		<div class="row-fluid">
			<div class="span12">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_REMOVED_CLONES' ) . '(' . count($removed_clones) . ')'; ?>
					</div>
					<div class="controls">
						<?php
						$removed_clones_output = array();
						foreach($removed_clones AS $removed_clone) {
							$removed_clones_output[] = mtfHTML::cloneuser( $removed_clone->user_id, $removed_clone->username );
						}
						echo implode(', ',$removed_clones_output);
						?>
					</div>
				</div>				
			</div>
		</div>
		<?php } ?>
	</fieldset>

	<?php
	if( count($clones) > 0 ) {
	?>
	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="15%"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="85%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CLONES' ) ?></th>
	</tr>
	</thead>
	<?php
	$ip = '';
	foreach( $clones AS $clone ) {
		if( empty($ip) OR $ip <> $clone->log_ip ) {
			$ip = $clone->log_ip;
			$clone_count[$clone->log_ip] = 1;
		} else {
			$clone_count[$clone->log_ip]++;
		}
		$clone_user[$clone->log_ip][] = array( 'username' => $clone->username, 'user_id' => $clone->user_id, 'name' => $clone->name, 'blocked' => $clone->user_blocked ); 
	}
	foreach( $clone_count AS $ip => $count ) {
		echo '<tr align="left">';
		echo '<td>' . $ip . '</td>';
		echo '<td>';
		foreach( $clone_user[$ip] AS $cuser ) {
			echo mtfHTML::user( $cuser['user_id'], $cuser['username'], '', $cuser['blocked'] );
			echo '&nbsp; ';
		}
		echo '</td>';
		echo '</tr>';
	}
	?>
	</table><br />
	<?php
	}
	?>
			
	<?php
	if( count($user_activities) == 0 ) {
		echo '<div align=\'left\'><i>' . JText::_( 'COM_MTREE_THIS_USER_HAS_NOT_CAST_ANY_VOTES_YET' ) . '</i></div>';
	} else {
	?>

	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(<?php echo count($user_activities); ?>);" /></th>
		<th width="22%"><?php echo JText::_( 'COM_MTREE_ACTIVITIES' ) ?></th>
		<th width="55%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING_REVIEW' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="10%" nowrap="nowrap" class="visible-desktop"><?php echo JText::_( 'COM_MTREE_DATE' ) ?></th>
	</tr>
	</thead>
	<?php

	$c=0;
	$i=0;
		foreach( $user_activities AS $ua ) {
			echo '<tr align="left">';
			echo '<td width="5">' . ++$c . '</td>';
			echo '<td><input type="checkbox" id="cb' . $i . '" name="cid[]" value="' . $ua->log_id . '" onclick="Joomla.isChecked(this.checked);" /></td>';
			echo '<td>' . mtfHTML::userActivity( $ua->log_type, $ua->value, $ua->link_id, $ua->link_name ) . '</td>';
			echo '<td>' . mtfHTML::listing( $ua->link_id, $ua->link_name, $ua->rev_title, $ua->rev_approved ) . '</td>';
			echo '<td class="hidden-phone">' . mtfHTML::ipAddress( $ua->log_ip ) . '</td>';
			echo '<td align="right" class="visible-desktop">' . date( 'j M y, H:i', strtotime($ua->log_date) ) . '</td>';
			echo '</tr>';
			$i++;
		}

	?>
	</table>

	<?php
	}

	if( count($reviews) == 0 ) {
		//echo "<div align='left'><i>This user has not written any reviews yet.</i></div>";
	} else {
	?>
	<br />
	<table class="table table-striped" width="100%">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="22%"><?php echo JText::_( 'COM_MTREE_WRITTEN_REVIEWS' ) ?></th>
		<th width="15%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?></th>
		<th width="40%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
		<th width="10%" nowrap="nowrap" class="visible-desktop"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="13%" nowrap="nowrap" class="visible-desktop"><?php echo JText::_( 'COM_MTREE_DATE' ) ?></th>
	</tr>
	</thead>
	<?php

	$c=0;
	foreach( $reviews AS $review ) {
		echo '<tr align="left">';
		echo '<td width="5">' . ++$c . '</td>';
		echo '<td>' . mtfHTML::review($review->rev_id,$review->rev_title,$review->rev_text) . '</td>';
		echo '<td>' . mtfHTML::helpfuls( $review->vote_helpful, $review->vote_total ) . '</td>';
		echo '<td>' . '<a href="index.php?option='.$option.'&task=spy&task2=viewlisting&id='.$review->link_id.'">' . $review->link_name . '</a></td>';
		if( !empty($review->log_ip) ) {
			echo '<td class="visible-desktop">' . mtfHTML::ipAddress( $review->log_ip ) . '</a></td>';
		} else { echo '<td class="visible-desktop">-</td>'; }
		echo '<td class="visible-desktop">' . date( 'j M y, H:i', strtotime($review->rev_date) ) . '</td>';
		echo '</tr>';
	}

	?>
	</table>
	<?php } 

	if( count($links) > 0 ) {
	?>
	<br />
	<table class="table table-striped" width="100%">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="40%"><?php echo JText::_( 'COM_MTREE_OWNED_LISTINGS' ) ?></th>
		<th width="20%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_RATINGS_AND_VOTES' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_HITS' ) ?></th>
		<th width="10%" nowrap="nowrap" class="visible-desktop"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_MODIFIED' ) ?></th>
	</tr>
	</thead>
	<?php

	$c=0;
	foreach( $links AS $link ) {
		echo '<tr align="left">';
		echo '<td width="5">' . ++$c . '</td>';
		echo '<td>' . mtfHTML::listing( $link->link_id, $link->link_name ) . '</td>';
		echo '<td class="nowrap">' . mtfHTML::rating( $link->link_rating ) . '&nbsp; ' . $link->link_votes . ' votes</td>';
		echo '<td class="hidden-phone">' . $link->reviews . '</td>';
		echo '<td class="hidden-phone">' . $link->link_hits . '</td>';
		echo '<td class="visible-desktop">' . date( 'j M', strtotime($link->link_created) ) . '</td>';
		echo '<td class="hidden-phone">' . date( 'j M', strtotime($link->link_modified) ) . '</td>';
		echo '</tr>';
	}

	?>
	</table>
	<?php } ?>

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="spy" />
	<input type="hidden" name="task2" value="viewuser" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}

	public static function viewUsers( $option, $lists, $search, $pageNav, $Itemid, $users ) {
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_USERS' ) ?>: <?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></legend>
		<div class="row-fluid">
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_USERNAME' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="username" id="username" size="24" value="<?php if (isset($search['username'])) echo $search['username'] ?>" class="input-medium" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_NAME' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="name" id="name" size="24" value="<?php if (isset($search['name'])) echo $search['name'] ?>" class="input-medium" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_EMAIL' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="email" id="email" size="24" value="<?php if (isset($search['email'])) echo $search['email'] ?>" class="input-medium" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_USER_ID' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="id" id="id" size="24" value="<?php if (isset($search['id'])) echo $search['id'] ?>" class="input-medium" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_PASSWORD_HASH' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="password" id="password" size="24" value="<?php if (isset($search['password'])) echo $search['password'] ?>" class="input-medium" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?>
					</div>
					<div class="controls">
						<input type="text" name="ip" id="ip" size="24" value="<?php if (isset($search['ip'])) echo $search['ip'] ?>" class="input-medium" />
					</div>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span12">
				<button type="submit" class="btn btn-primary btn-small"><i class="icon-search"></i> <?php echo JText::_( 'COM_MTREE_SEARCH' ); ?></button>
				<button class="btn btn-small" onclick="document.id('ip').value='';document.id('id').value='';document.id('name').value='';document.id('password').value='';document.id('email').value='';document.id('username').value='';this.form.submit();"><?php echo JText::_( 'COM_MTREE_RESET' ) ?></button>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span12">
				<div class="pull-right">
				<?php echo $lists['orderby']; ?>
				<?php echo $pageNav->getLimitBox('ccc'); ?>
				</div>
			</div>
		</div>
		
	</fieldset>

	<table class="table table-striped">
	<thead>
	<tr align="left">
		<th width="1%">#</th>
		<th width="10%"><?php echo JText::_( 'COM_MTREE_USER' ) ?></th>
		<th width="12%" nowrap="nowrap" align="right"><?php echo JText::_( 'COM_MTREE_LAST_ACTIVITY' ) ?></th>
		<th width="43%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING_REVIEW' ) ?></th>
		<th width="10%" nowrap="nowrap" class="hidden-phone"><?php echo JText::_( 'COM_MTREE_IP_ADDRESS' ) ?></th>
		<th width="13%" nowrap="nowrap" style="min-width:80px" class="hidden-phone"><div class="vrhl"><abbr title="<?php echo JText::_( 'COM_MTREE_VOTES' ) ?>">V</abbr></div> <div class="vrhl"><abbr title="<?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?>">R</abbr></div> <div class="vrhl"><abbr title="<?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?>">H</abbr></div> <div class="vrhl"><abbr title="<?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?>">L</abbr></div> </th>
		<th width="12%" nowrap="nowrap" align="center" class="visible-desktop"><?php echo JText::_( 'COM_MTREE_DATE' ) ?></th>
	</tr>
	</thead>
	<?php
		$c=0;
		foreach( $users AS $user ) {
			echo '<tr align="left">';
			echo '<td width="5">' . $pageNav->getRowOffset( $c++ ) . '</td>';			
			echo '<td>' . mtfHTML::user( $user->id, $user->username, $user->name ) . '</td>';
			echo '<td align="right">' . mtfHTML::userActivity( $user->log_type, $user->value, $user->link_id, $user->link_name ) . '</td>';
			echo '<td>' . mtfHTML::listing( $user->link_id, $user->link_name, $user->rev_title, $user->rev_approved ) . '</td>';
			echo '<td class="hidden-phone">' . mtfHTML::ipAddress( $user->log_ip ) . '</td>';			
			echo '<td align="center" class="hidden-phone" class="hidden-phone">';
			echo '<div class="vrhl">' . ( ($user->votes) ? $user->votes : '-' ) . '</div> ';
			echo '<div class="vrhl">' . ( ($user->reviews) ? $user->reviews : '-' ) . '</div> ';
			echo '<div class="vrhl">' . ( ($user->votereviews) ? $user->votereviews : '-' ) . '</div> ';
			echo '<div class="vrhl">' . ( ($user->listings) ? $user->listings : '-' ) . '</div> ';
			echo '</td>';
			echo '<td align="right" class="hidden-phone hidden-tablet">' . ((!empty($user->log_date)) ? date( 'j M y, H:i', strtotime($user->log_date) ) : '') . '</td>';
			echo '</tr>';
		}
		?>
		<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $pageNav->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="spy" />
	<input type="hidden" name="task2" value="users" />
	</form>
	<?php
	}
	
	public static function printStartMenu( $option, $task ) {
		global $mtconf;
	?>
	<style type="text/css">
	div.vrhl {
	width:17px;
	float:left;
	text-align:center;
	margin-right:3px;
	}
	span.owner_rev, span.owner_vote {background-color:#FFD2D2;border-bottom:2px solid #FF7D7D;padding:2px 4px 0 4px;font-weight:bold}
	span.clone_rev, span.clone_vote {background-color:#FFD2D2;border-bottom:0px solid #FF7D7D;padding:0 4px}
	span.clone_vote_removed {padding:0 4px;text-decoration:line-through}
	span.blocked_user {text-decoration:line-through;color:black}
	span.user {text-decoration:underline;color:#C64934}
	span.pending{background-color:#FFFB8A;border-bottom:2px solid #CEC704;padding:2px 4px 0 4px;font-weight:bold;margin-left:3px;}
	</style>
	<div class="row-fluid">
		<div class="span2">

			<ul class="nav nav-list">
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_SPY_DIRECTORY' ) ?></li>
				<li>
					<a href="index.php?option=com_mtree&task=spy&task2=users">
						<?php echo JText::_( 'COM_MTREE_VIEW_USERS' ) ?>
					</a>
				</li>
				<li>
					<a href="index.php?option=com_mtree&task=spy&task2=listings">
						<?php echo JText::_( 'COM_MTREE_VIEW_LISTINGS' ) ?>
					</a>
				</li>
				<li>
					<a href="index.php?option=com_mtree&task=spy&task2=clones">
						<?php echo JText::_( 'COM_MTREE_VIEW_CLONES' ) ?>
					</a>
				</li>
				
				<li class="divider"></li>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_SEARCH_LISTINGS' ) ?></li>
				<li>
					<form action="index.php" method="post" name="adminForm_searchlistings" class="form-search">
						<div class="input-append">
							<input type="text" name="link_name" class="search-query span10" />
							<button type="submit" class="btn">
								<i class="icon-search"></i>
							</button>
						</div>
						<input type="hidden" name="option" value="<?php echo $option;?>" />
						<input type="hidden" name="task" value="spy" />
						<input type="hidden" name="task2" value="listings" />
					</form>
				</li>
				
				<li class="divider"></li>
				<li class="nav-header"><?php echo JText::_( 'COM_MTREE_SEARCH_USERS' ) ?></li>
				<li>
					<form action="index.php" method="post" name="adminForm_searchusers" class="form-search">
						<div class="input-append">
							<input type="text" name="username" class="search-query span10"/>
							<button type="submit" class="btn">
								<i class="icon-search"></i>
							</button>
						</div>
						<input type="hidden" name="option" value="<?php echo $option;?>" />
						<input type="hidden" name="task" value="spy" />
						<input type="hidden" name="task2" value="users" />
					</form>
				</li>
				
				<li class="divider"></li>
				<li>
					<a href="index.php?option=com_mtree">
						<i class="icon-arrow-left"></i>
						<?php echo JText::_( 'COM_MTREE_BACK_TO_DIRECTORY' ) ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="span10">

	<?php
	}

	public static function printEndMenu( $task ) {
	?>
	</div>
	</div>
	<?php
	}

}

class mtfHTML {

	public static function ipAddress( $ip ) {
		if( !empty($ip) ) {
			return '<a href="index.php?option=com_mtree&task=spy&task2=users&ip=' . $ip . '">' . $ip . '</a>';
		} else {
			return '-';
		}
	}

	public static function cloneuser( $user_id, $username='' ) {
		return mtfHTML::user($user_id,$username,'',0,'viewclone');
	}
	
	public static function user( $user_id, $username='', $name='', $block=0, $task2='viewuser' ) {
		$html = '';

		if( $user_id > 0 ) { 

			if( empty($name) && !empty($username) ) {
				$html = '<a href="index.php?option=com_mtree&task=spy&task2=' . $task2 . '&id='.$user_id.'">' . $username . '</a>';
			} elseif( !empty($name) && empty($username) ) {
				$html = '<a href="index.php?option=com_mtree&task=spy&task2=' . $task2 . '&id='.$user_id.'">' . $name . '</a>';
			} else {
				$html = '<a href="index.php?option=com_mtree&task=spy&task2=' . $task2 . '&id='.$user_id.'">' . $username . '</a>';
			}

			if( $block ) {
				$html = '<span class="blocked_user">' . $html . '</span>';
			} elseif( empty($name) && !empty($username) ) {
				$html = '<span class="user">' . $html . '</span>';
			}

		} elseif ( $user_id == 0 && ( !empty($username) && !empty($name) ) ) {
			$html = $name . ' ('.$username.')'; 
		} else { 
			$html = '<i>'.JText::_( 'COM_MTREE_UNREGISTERED' ).'</i>';
		}
		return $html;
	}

	public static function listing( $link_id, $link_name, $review_title='', $rev_approved=1 ) {
		$html = '<a href="index.php?option=com_mtree&task=spy&task2=viewlisting&id=' . $link_id .'">';
		if( !empty($review_title) ) {
			$html .= mtfHTML::forceMaxChars( $link_name, 50 );
			$html .= '</a>';
			$html .= ' (' . mtfHTML::forceMaxChars( $review_title, 50 ) . ')';
			if(!$rev_approved) {
				$html .= '<span class="pending">'.JText::_( 'COM_MTREE_PENDING' ).'</span>';
			}
		} else {
			$html .= mtfHTML::forceMaxChars( $link_name, 100 );
			$html .= '</a>';
		}
		return $html; 
	}

	public static function forceMaxChars( $text, $maxchar ) {
		if( strlen($text) > $maxchar ) {
			return substr( $text, 0, ($maxchar -3) ) . '...';
		} else {
			return $text;
		}
	}
	
	public static function helpfuls( $vote_helpful, $vote_total ) {
		if ( $vote_total == 0 ) {
			return '-';
		} else {
			return JText::sprintf( 'COM_MTREE_OUT_OF', $vote_helpful,  $vote_total );
		}
	}

	public static function userActivity( $log_type, $value, $link_id, $link_name ) {
		global $mtconf;

		$ret = '';
		switch( $log_type ) {
			case 'vote':
				if( $value > 0 ) {
					$ret .= mtfHTML::rating( $value );
				} else {
					$ret .= mtfHTML::rating( 0 );
				}
				break;

			case 'votereview':
				if( $value == 1 ) {
					$ret = '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'comment_add.png" width="16" height="16" hspace="0" />';

				} elseif  ( $value == -1 ) {
					$ret = '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'comment_delete.png" width="16" height="16" hspace="0" />';

				}
				break;

			case 'review':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'comment.png" width="16" height="16" hspace="0" />';
				break;

			case 'replyreview':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'user_comment.png" width="16" height="16" hspace="0" />';
				break;

			case 'addfav':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'heart_add.png" width="16" height="16" hspace="0" />';
				break;

			case 'removefav':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'heart_delete.png" width="16" height="16" hspace="0" />';
				break;

			case 'submitnewlisting':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'page_white_add.png" width="16" height="16" hspace="0" />';
				break;

			case 'modifylisting':
				return '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_images').'page_white_edit.png" width="16" height="16" hspace="0" />';
				break;

			default:
				return '<a href="index.php?option=com_mtree&task=spy&task2=viewlisting&id=' . $link_id .'">' . $log_type . '</a>';
				break;

		}
		return $ret;
	}

	public static function rating( $rating ) {
		global $mtconf;

		$star = floor($rating);
		$html = '';

		// Print starts
		for( $i=0; $i<$star; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" hspace="1" />';
		
		}

		if( ($rating-$star) >= 0.5 && $star > 0 ) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" hspace="1" />';
			$star += 1;
		}

		// Print blank star
		for( $i=$star; $i<5; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" hspace="1" />';
		}

		# Return the listing link
		return $html;

	}

	public static function review($rev_id, $rev_title, $rev_text='', $rev_approved=1) {
		$html = '<a href="index.php?option=com_mtree&task=editreview&rid=' . $rev_id .'"';
		if(!empty($rev_text)) {
			$html .= ' class="hasTip" title="'.$rev_title.'::'.$rev_text.'"';
		}
		$html .= '>';
		$html .= $rev_title;
		$html .= '</a>';
		if(!$rev_approved) {
			$html .= '<span class="pending">'.JText::_( 'COM_MTREE_PENDING' ).'</span>';
		}
		return $html; 
	}


}

?>