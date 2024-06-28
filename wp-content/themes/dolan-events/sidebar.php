<?php wp_reset_query(); ?>
<div id="right-content">
	<?php 
	$cat = get_the_category();
	if(is_page('past-winners') ||(is_single() && ($cat[0]->cat_name == 'Winners'))) { 
		?>
		<div class="sidebox">
			<ul>
				<?php
				global $post;
				$winnerposts = new WP_Query(array('category_name' => 'winners', 'posts_per_page' => -1));

				while ($winnerposts->have_posts()) : $winnerposts->the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
			</ul>
		</div>
		<?php
	} else {
		global $DR_Kids, $true_parent; if ( count($DR_Kids) > 0 ) : ?>
		<div class="sidebox">
			<h2><?php echo apply_filters('the_title', $true_parent->post_title); ?></h2>
			<ul>
				<?php wp_list_pages('title_li=&child_of=' . $true_parent->ID); ?>
			</ul>
		</div>
	<?php endif; 	
}
?>


<?php 
$options = get_option('theme_options');
?>
<?php if(empty($options['event_signup'])) : ?>
	<script type='text/javascript'> 
		function validate<?php echo $options['bsFormID']; ?>()
		{
			var isValid = true;
			var isNewsletterSelected = true;
			var errorString = '';
			var x = document.OptinForm<?php echo $options['bsFormID']; ?>;
			if (x.UEmail.value== "")
			{ 
				errorString = errorString + "Email Address. \n ";
				isValid = false;
			}
			else {
				if (x.UEmail.value.indexOf("@") == -1 || x.UEmail.value.indexOf(".") == -1)
				{
					errorString = errorString + 'Invalid email address.';
					isValid = false;
				}
			}

			if (isValid == true && isNewsletterSelected == true)
			{ 
				return true;  
			} 
			else 
			{ 
				if (isNewsletterSelected == false)
				{
					errorString = errorString +"----------------------\nPlease select at least one subscription";  
				}
				alert("Following fields are required:\n\n"+ errorString);  return false; }  
			}
		</script>
		<form class="signUp" id='Optin<?php echo $options['bsFormID']; ?>' name='OptinForm<?php echo $options['bsFormID']; ?>' method='post' action='http://click.oo155.com/OptIn.aspx?OptinFormID=<?php echo $options['bsFormID']; ?>&26241823=<?php echo $options['bsClientID']; ?>' onsubmit='return validate<?php echo $options['bsFormID']; ?>();'>
			<input type='hidden' name='OptinFormID' value='<?php echo $options['bsFormID']; ?>' />
			<input type='hidden' name='26241823' value='<?php echo $options['bsClientID']; ?>' />
			<?php
			if($options['bsText'] ==""){
				?>
				<label>Sign Up for E-mail Alerts</label>
				<?php
			}
			else{
				echo '<label>'.$options['bsText'].'</label>';
			}
			?>
			<input type='text' name='UEmail' class="textField" />
			<input type='submit' class="awesome" name='submit' value='Submit' style="float:right;" />
		</form>
	<?php endif; ?>
	<?php if(empty($options['request_sponsorship_info'])) : ?>
		<?php global $DR_Sponsorship; ?>
		<?php if ( empty($DR_Sponsorship['success']) ) : ?>
			<form class="signUp" method="post" action="">
				<?php
				if($options['sales_rep_text'] ==""){
					?>
					<label for="_tdr_email">Request Sponsorship Info</label>
					<?php
				}
				else{
					echo '<label for="_tdr_email">'.$options['sales_rep_text'].'</label>';
				}
				?>
				<?php if ( !empty($DR_Sponsorship['error']) ) :?>
					<div class="error">
						<?php echo $DR_Sponsorship['error']; ?>
					</div>
				<?php endif; ?>
				<input class="textField" type="text" name="_tdr_email" id="_tdr_email" tabindex="1" value="<?php echo isset($_POST['_tdr_email']) ? htmlentities($_POST['_tdr_email']) : ''; ?>" />
				<input type="hidden" name="_tdr_form" value="1" />
				<input name="goBtn" class="awesome" value="Go" type="submit" style="float:right;" />
			</form>
			<?php else : ?>
				<div class="success"><?php echo ($DR_Sponsorship['success']); ?></div>
			<?php endif; ?>
		<?php endif; ?>
		<div id="sponsors">
			<?php get_sidebar ('custom'); ?>
			<?php if ( function_exists ( dynamic_sidebar('custom') ) ) : ?>
				<?php dynamic_sidebar ('custom'); ?>
			<?php endif; ?>

		</div>
	</div>