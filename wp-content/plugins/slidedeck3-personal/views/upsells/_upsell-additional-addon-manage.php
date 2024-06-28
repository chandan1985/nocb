<?php 
return;
$sd3addon_path =SLIDEDECK2_UPDATE_SITE."/update-sd3addon.php";
$args = array('body' => array('action' => "getaddons") );
$json_addondata = wp_remote_post($sd3addon_path,$args); 
 
if(!is_wp_error($json_addondata))
{
	$external_addons = json_decode($json_addondata["body"],true);
}

if(isset($external_addons) && !empty($external_addons))
foreach( $external_addons as $slug => $addon_meta ): ?>
    
    <?php if( !in_array( $addon_meta['slug'], $addon_slugs ) ) : ?>
 	    <?php if( !class_exists($addon_meta['class']) ) : ?>
        <div class="lens add-lens">
            <div class="inner">
                <img class = "sdaddon-img" style ="width:258px; height:135px;" src="<?php echo $addon_meta['thumbnail']; ?>" />
                <h4><?php echo $addon_meta['name']; ?></h4>
                <p><?php echo $addon_meta['description']; ?></p>
                <div class="upgrade-button-cta">
                    <!--<a href="http://www.slidedeck.com/premium-lenses-ee0f2/?lens=<?php echo $slug; ?>&utm_source=premium_lenses_page&utm_medium=link&utm_content=<?php echo $addon_meta['utm_content']; ?>&utm_campaign=sd3_upgrade<?php echo self::get_cohort_query_string( '&' ) . slidedeck2_km_link( 'Browse Premium Lens', array( 'name' => $addon_meta['name'], 'location' => 'Lens Management' ) ); ?>" target="_blank" class="upgrade-button green">-->

<a href="http://www.slidedeck.com/slidedeck_addons/" target="_blank" class="upgrade-button green">
                        <span class="button-noise">
                            <span>Add <?php echo $addon_meta['name']; ?></span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="actions"></div>
        </div>
        
    <?php endif; ?>
   	<?php endif; ?>
    
<?php endforeach; ?>
