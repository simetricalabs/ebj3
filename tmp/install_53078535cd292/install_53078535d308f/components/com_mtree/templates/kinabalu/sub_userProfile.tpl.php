<?php

// Specify the profile fields to be displayed here
// Full list of field names: 
// profile_address1, profile_address2, profile_city, profile_country, profile_postal_code, profile_phone, profile_website, profile_aboutme

$profile_fields = array('profile_phone','profile_website','profile_aboutme');

?><div class="user">
	<div class="title"><?php echo MText::_( 'LISTING_DETAILS_USER', $this->tlcat_id ); ?></div>

	<div class="row-fluid">

		<?php if( !empty($this->profilepicture_url) ) { ?>
		<div class="span2">
			<div class="profile-picture">
			<!-- <div class=""> -->
				<img width="100" height="100" src="<?php echo $this->profilepicture_url; ?>" alt="'<?php echo $this->link->username; ?>" />
			</div>			
		</div>

		<div class="span10">
		<?php
		} 
		else
		{
		?>
		<div class="span12">
		<?php
		}
		?>
			<h3 class="user-name"><a href="<?php echo JRoute::_( 'index.php?option=com_mtree&task=viewowner&user_id='.$this->link->user_id); ?>"><?php echo $this->link->owner; ?></a></h3>
			<dl>
			<?php 
			foreach ($profile_fields as $profile_field) :
				if( !isset($this->user_profile_fields[$profile_field]) )
				{
					continue;
				}
				$profile = $this->user_profile_fields[$profile_field];
				if ($profile->value) :
					echo '<div class="row-fluid">';
					echo '<div class="span3 '.$profile_field.'">'.$profile->label.'</div>';
					$profile->text = htmlspecialchars($profile->value, ENT_COMPAT, 'UTF-8');

					switch ($profile->id) :
						case "profile_website":
							$v_http = substr ($profile->profile_value, 0, 4);
							echo '<div class="span9 '.$profile_field.'">';
							if ($v_http == "http") :
								echo '<a href="'.$profile->text.'">'.$profile->text.'</a>';
							else :
								echo '<a href="http://'.$profile->text.'">'.$profile->text.'</a>';
							endif;
							echo '</div>';
							break;

						default:
							echo '<div class="span9 '.$profile_field.'">'.$profile->text.'</div>';
							break;
					endswitch;
					echo '</div>';
				endif;
			endforeach;
			?>
			</dl>			
		</div>
		
	</div>
</div>