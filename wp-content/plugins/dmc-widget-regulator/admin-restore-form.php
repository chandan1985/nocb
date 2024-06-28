<?php
	/*
	 * Admin page for performing widget restores
	 */
?>
<div class="wrap">
	<h2>Widget Backup and Restore</h2>
	<p class="error">This only restores the sidebar configuration for what widgets were saved in which sidebar. It will not restore previous widget settings, i.e. it <strong>WON'T</strong> recover the settings that were defined for each widget, only the placement of widgets within the sidebars.</p>
	<p>
		
	<h3>Current</h3>
	<div style="height:150px; overflow: scroll; border:1px solid black;">
		<?php 
		foreach ($cur as $key=>$val) {
			echo $key.'<ul>'; 
			if (is_array($val)) {
			foreach ($val as $v) {
				echo '<li>['.$v.']</li>';
			}	
			echo '</ul>';
			}		
		}

		?>
	</div>

	<h3>Last 10 Backups</h3>	
	<?php if(count($rows)>0) : ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=widget-backup-and-restore">
	<table class="form-table">
		<tr><td></td><td>ID</td><td>Date</td><td>User</td><td>Value</td></tr>
	<?php
	foreach ($rows as $row) :
		$tmp_usr = get_userdata( $row->user_id );
		$tmp_wv = maybe_unserialize( $row->widget_value );
	?>
		<tr>
		<td><input type="radio" name="restore" value="<?php echo $row->widget_id; ?>"></td>
		<td><?php echo $row->widget_id; ?></td>
		<td><?php echo $row->create_date; ?></td>
		<td><?php echo $tmp_usr->user_login; ?></td>
		<td><div style="height:175px; overflow: scroll; border:1px solid black;">
		<?php 
		foreach ($tmp_wv as $key=>$val) {
			echo $key.'<ul>'; 
			if (is_array($val)) {
			foreach ($val as $v) {
				echo '<li>['.$v.']</li>';
			}	
			echo '</ul>';
			}		
		}
		
		?>
	</div></textarea>
		</tr>
		
	<?php endforeach; ?>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Restore Sidebar Widgets') ?>" />
		<div class="error">WARNING: THIS WILL OVERRIDE CURRENT SETTINGS</div>
	</p>
	</form>
	<?php else : ?>
		<strong>No backups found!</strong>
	<?php endif; ?>
	</p>
</div>
