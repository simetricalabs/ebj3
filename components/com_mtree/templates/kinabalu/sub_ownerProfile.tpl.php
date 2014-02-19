<div class="row-fluid owner-profile">
	<div class="span3 profile-picture">
<?php
// Specify the profile fields to be displayed here
// Full list of field names: 
// profile_address1, profile_address2, profile_city, profile_country, profile_postal_code, profile_phone, profile_website, profile_aboutme

$profile_fields = array('profile_phone','profile_website','profile_aboutme');

// Show Owner Profile Picture
$profilepicture_enabled = JPluginHelper::isEnabled( 'user', 'profilepicture' );

if( $profilepicture_enabled )
{
	jimport('mosets.profilepicture.profilepicture');

	$profilepicture = new ProfilePicture($this->owner->id);
	
	if( $profilepicture->exists() )
	{
		echo '<img src="'.$profilepicture->getURL(PROFILEPICTURE_SIZE_200).'" alt="'.$this->owner->username.'" />';
	}
	else
	{
		echo '<img src="'.$profilepicture->getFillerURL(PROFILEPICTURE_SIZE_200).'" alt="'.$this->owner->username.'" />';
	}
}
	?>
	</div>
	<div class="span9">
		<?php
		// Show Owner Profile
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
	</div>
</div>