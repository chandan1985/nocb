        	<div id="footer">
					<?php
					$options = get_option('theme_options');
					$logosArray = array();
					for($i=1; $i<=8; $i++){
						//if($options['footer_logo'.$i] == 1) {
							$logosArray[$i] = array($options['footer_logo'.$i], $options['footer_link'.$i]);			
						//}
					}
					foreach($logosArray as $item) {
						if($item[0] != ""){
					?>
						<a href="<?php echo $item[1]; ?>"><img src="<?php echo $item[0]; ?>" alt="" /></a>	
					<?php
						}
					}
					?>
            </div>
        </div>
   </div>
   <div id="main-content-btm">
   <div id='dolan-footer'>
   Copyright &copy <?php echo date("Y")." ".$options['pub_name'] ?> | 
   <a href='http://bridgetowermedia.com/privacy-policy/'>Privacy Policy </a> | 
   <a href='http://bridgetowermedia.com/subscriber-agreement/'>Subscriber Agreement</a>
   <a id='dmc-logo' href='http://bridgetowermedia.com' ></a>
   </div>
   </div>
</div>
<div id="container-btm">&nbsp;</div>
   </div>
<?php wp_footer(); ?>

</body>
</html>