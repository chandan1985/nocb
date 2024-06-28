<?php

/**
 * Functions for use in this view only
 */

function redirect_input_td($redirect, $index){
	$result = '
		<td><input type="text" name="dmc_dynamic_redirect['.$index.'][page]" class="dynamic_redirect_page" id="redirect'.$index.'[page]" value="'.(isset($redirect['page']) ? $redirect['page'] : '').'" /></td>		
		<td><input type="text" name="dmc_dynamic_redirect['.$index.'][loggedin]" id="redirect'.$index.'[loggedin]" value="'.(isset($redirect['loggedin']) ? $redirect['loggedin'] : '').'" /></td>
		<td><input type="text" name="dmc_dynamic_redirect['.$index.'][loggedout]" id="redirect'.$index.'[loggedout]" value="'.(isset($redirect['loggedout']) ? $redirect['loggedout'] : '').'" /></td>
		<td><input type="text" name="dmc_dynamic_redirect['.$index.'][incoming]" id="redirect'.$index.'[incoming]" value="'.(isset($redirect['incoming']) ? $redirect['incoming'] : '').'" /></td>
		<td><input type="text" name="dmc_dynamic_redirect['.$index.'][default_url]" id="redirect'.$index.'[default_url]" value="'.(isset($redirect['default_url']) ? $redirect['default_url'] : '').'" /></td>		
		';
	return $result;
}

?>

<div class="wrap">

	<h2>Dynamic Redirect Settings</h2>
	
	<script type="text/javascript" charset="utf-8">
		function dmc_dynamic_redirect_add(){
			// create our new html to insert
			var position = jQuery('.form-table .dmc_dynamic_redirect').length;
			var html = jQuery('.redirect_templates .dmc_dynamic_redirect').parent().html().replace(/%i%/g, position);
			
			// remove old Add buttons
			jQuery('.form-table .dmc_dynamic_redirect_add button').remove();
			
			// insert new html
			jQuery('.form-table .dmc_dynamic_redirect:last').after(html);

			// put our cursor in a convenient place
			jQuery('.form-table .dmc_dynamic_redirect:last .dynamic_redirect_page')[0].focus();
		}
	</script>
	
	<div class="redirect_templates" style="display:none">
		<table>
			<tr class="dmc_dynamic_redirect">
				<td> </td>
				<?php
				$redirect = array();
				echo redirect_input_td($redirect, '_new%i%');
				?>
				<td class="dmc_dynamic_redirect_add"><button type="button" onclick="javascript: dmc_dynamic_redirect_add();">add</button></td>
			</tr>
		</table>
	</div>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<?php wp_nonce_field('update-options') ?>
		
		<p>Type the redirect page, incoming url, redirect url if logged in, and default url into the textfields below.</p>
		<p>To redirect <strong>www.sitename.com/page</strong> to <strong>www.sitename.com/loggedin</strong> if logged in,
		<strong>www.sitename.com/loggedout</strong> if not logged in when coming from <strong>www.example.com</strong>, and the site home page otherwise:</p>
		<p>The Page to Redirect is: <strong>page</strong><br>
		<span style="font-size:10px">If left blank, options will not be saved.</span><br>
		</p>
		<p>The logged in redirect is : <strong>loggedin</strong><br>
		<span style="font-size:10px">If left blank, all logged in requests will redirect to default.</span><br>
		</p>
		<p>The logged out redirect is : <strong>loggedout</strong><br>
		<span style="font-size:10px">If left blank, all logged out requests will redirect to default.</span><br>
		</p>
		<p>The HTTP referer is: <strong>example.com</strong><br>
		<span style="font-size:10px">If left blank, http referrer will not be checked; any logged out requests will direct to default.</span><br>
		</p>
		<p>The default is: left blank<br>
		<span style="font-size:10px">If left blank, any default requests will redirect to the home page.</span><br>
		</p>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="dmc_dynamic_redirect">Redirect<br>Pages</label></th>
				<td>
					<table>
						<tr><th class="dmc_dynamic_redirect_delete">Delete</th><th>Redirect Page <font color="red">*required</font></th><th>Logged In URL</th><th>Logged Out URL</th><th>HTTP Referer</th><th>Default Redirect URL</th><th></th></tr>
						<?php 						
						$settings = get_option('dmc_dynamic_redirect');
						for($i=0; $i < count($settings); $i++): ?>
						<tr>
							<td class="dmc_dynamic_redirect_delete"><input type="checkbox" id="redirect<?php echo $i; ?>_[delete]" name="dmc_dynamic_redirect[<?php echo $i; ?>][delete]" /></td>
							<?php
							echo redirect_input_td($settings[$i], $i);
							?>
						</tr>
						<?php endfor; ?>
						<tr class="dmc_dynamic_redirect">
							<td> </td>
							<?php
							$redirect = array();
							echo redirect_input_td($redirect, '_new0');
							?>
							<td class="dmc_dynamic_redirect_add"><button type="button" onclick="javascript: dmc_dynamic_redirect_add();">add</button></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<p class="submit">
			<input type="submit" name="Submit" value="Save Changes" />
		</p>
	</form>
	<p style="color:red; font-weight:bold; font-size:14px;">*** External redirects require a full URL</p>
</div>