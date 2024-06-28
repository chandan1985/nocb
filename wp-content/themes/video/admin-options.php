<?php
/*
*	New Admin page for Video theme
*	Adapted from original by Dave Long
*
*/
global $cache_settings, $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'store_option') {
	unset($_POST['action']);
	$msg = $this->store_options($_POST);
} elseif (isset($_POST['action']) && $_POST['action'] == 'delete_options') {
	$this->delete_options();
} elseif (!$this->is_installed()) {
	$msg = $this->do_firstinit();
}

if ( !empty($msg) ) : ?>
	<div class='updated'><p><b><?php echo( $msg ); ?></b></p></div>
<?php endif; ?>
<div class="wrap">
	<?php
	if (!$this->infos['active']) { 
		echo '<p>(Please note that this theme is currently <strong>not activated</strong> on your site as the default theme.)</p>';
	}
	$cache_settings = '';
	$check = $this->read_options();
	?>
	<h2>Configure options</h2>
	<p>Here you can easily configure some of the features of the Video theme just by a few click:</p>
	<form action="" method="post">
		<input type="hidden" name="action" value="store_option">
		<p>
			The maximum height of the episode list on the stage is
			<input name="listheight" id="listheight" style="width:40px; text-align:right;"  maxlength="3" value="<?php echo( $this->option['listheight'] ); ?>" type="text">
			pixels, and the maximum number of posts shown is
			<input name="entrynumber" id="entrynumber" style="width:24px; text-align:right;"  maxlength="2" value="<?php echo( $this->option['entrynumber'] ); ?>" type="text">
		</p>
		<p>
			There should be
			<input name="maxentry" id="maxentry" style="width:24px; text-align:right;"  maxlength="2" value="<?php echo( $this->option['maxentry'] ); ?>" type="text">
			posts in the episode list at once ("Show previous/next <?php echo( $this->option['maxentry'] ); ?> posts" will appear in the episode list).
		</p>
		<p>
			Categories will show up in the top of the episodes list:  
			<label for="categorylist0"><input id="categorylist0" name="categorylist" value="0" <?php if ( $this->option['categorylist'] == 0 ) echo( 'checked="checked"' ); ?> type="radio"> in a list, or  </label>
			<label for="categorylist1"><input id="categorylist1" name="categorylist" value="1" <?php if ( $this->option['categorylist'] == 1 ) echo( 'checked="checked"' ); ?> type="radio"> in a dropdown box.</label>
		</p>
		<p>
			Do you want to enable the Digg button in every post in the "Now watching" segment?
			<label for="diggbutton1"><input id="diggbutton1" name="diggbutton" value="1" <?php if ( $this->option['diggbutton'] == 1 ) echo( 'checked="checked"' ); ?> type="radio">   Yes  </label>
			<label for="diggbutton2"><input id="diggbutton2" name="diggbutton" value="2" <?php if ( $this->option['diggbutton'] == 2 ) echo( 'checked="checked"' ); ?> type="radio"> No</label>
		</p>
		<h2>TDC Options</h2>
		<table>
			<tr>
				<td>Header link URL:</td>
				<td><input name="headerlink" id="headerlink" style="width:400px;" value="<?php echo isset($this->option['headerlink']) ? $this->option['headerlink'] : ''; ?>" type="text"></td>
			</tr>
			<tr>
				<td>Header logo URL:</td>
				<td><input name="headerlogourl" id="headerlogourl" style="width:400px;" value="<?php echo isset($this->option['headerlogourl']) ? $this->option['headerlogourl'] : ''; ?>" type="text"></td>
			</tr>
			<tr>
				<td>Header background URL:</td>
				<td><input name="backgroundlogo" id="backgroundlogo" style="width:400px;" value="<?php echo isset($this->option['backgroundlogo']) ? $this->option['backgroundlogo'] : ''; ?>" type="text"></td>
			</tr>
			<tr>
				<td>Footer text:</td>
				<td><input name="footertext" id="footertext" style="width:400px;" value="<?php echo isset($this->option['footertext']) ? $this->option['footertext'] : ''; ?>" type="text"></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" value="Save settings" /></p>
	</form>
</div>