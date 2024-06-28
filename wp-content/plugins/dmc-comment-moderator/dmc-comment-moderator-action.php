<?php
$wpadmin_email = get_option('admin_email');
$wpmoderator_email = get_option('commentmod_email', $wpadmin_email);

echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( '/style.css', __FILE__ ) . '" />';

if(isset($_POST['submit_action']) && $_POST['submit_action'] == "change") {
	if (!empty($_POST['mod_email'])) 
	{
		$wpmoderator_option_name = 'commentmod_email';
		$wpmoderator_option_value = $_POST['mod_email']; 
		if (is_email( $wpmoderator_option_value )) {
			if ( get_option($wpmoderator_option_name) != $wpmoderator_option_value)
			{
				update_option($wpmoderator_option_name, $wpmoderator_option_value);
			}
			else 
			{
				$deprecated = ' ';
			    $autoload = 'no';
			    add_option($wpmoderator_option_name, $wpmoderator_option_value, $deprecated, $autoload);
			}
			echo '<div id="message" class="updated fade"><p><strong>' . __('Setting saved') . '</strong></p></div>';
		}
		else
		{
			echo '<div id="message" class="error"><p><strong>' . __('Please enter a valid email address') . '</strong></p></div>';
		}
	} 
	
} ?>
<div class="wrap">
	<div class="menuHead">Change comment moderator email:</div>
	<form method='POST' id='modemailchange_form' action=''>
		<table>
			<tr>
				<td valign='top'>
					<input type="text" class="inputfield" name="mod_email" size="50" value="<?php if (isset($_POST['mod_email']) && $_POST['mod_email'] != '') echo $_POST['mod_email']; else echo $wpmoderator_email; ?>" />
				</td>
			</tr>
			<tr>
				<td valign='top'>
					<input type='hidden' name='submit_action' value='change' />
					<input type='submit' value='<?php echo 'Submit';?>' name='submit_values' />
				</td>
			</tr>
		</table>
	</form>
</div>