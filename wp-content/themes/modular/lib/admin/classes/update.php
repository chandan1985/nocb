<?php
/**
 *
 */
class mysiteUpdate {
	
	private $_old_framework_version;
	
	/**
	 *
	 */
	function __construct( $old_framework_version ) {
		
		if ( !is_admin() ) return;
		
		# Define the old framework version from the database
		$this->_old_framework_version = $old_framework_version;
		
		# Update functions
		$this->update_1_6();
	}
	
	/**
	 * Adds new default SEO options and page/post layout options to current theme options
	 * Only runs if < v1.6
	 *
	 * @since 1.6
	 */
	function update_1_6() {
		
		# If the $this->_old_framework_version is less than 1.6 run the upgrade
		if( $this->_old_framework_version < '1.6' ) {
			
			# Define and decode new options
			$new_options = 'nZRRcoMwDERv028Mado4h2EUUIingKkl2jCZ3L3gBtd4Jonpr5cnr6TFINOtvJAUQr589pr3HVSY1zDonn8P9qOY3USjqhPnpEo8gPmTHauJ17Kbm0yo85NuMGfFNTo9ucnuIE1CoEQqjOpY6fY-Jt5C7AOHb21Kus_4SAFzcZBCXpRMxg9m72z62fF1dCg8zo7EtpQftWmAH3S24KY1RHKvC5-MlTZDLOuPH0xxUl_R1_qLYKhisczDCKc7Y8nUI72lr3O6STaR94ltsI_yOeKvAls41FhMCYiNjtgF0eGhw0LXfdOSq5GFNbwblBS-MJp2QjoKO0cYPupaaS-1fhJ6wmmllAOF_8nTHjJ_2uXQQqMK2wvZTK8tt1ieS3erVVvi-V9znWO-tka6MNJNZuhfMJ5tnB68POI9fEIaZLAriYWmWa-F7Lv4FLr-AA';
			
			$new_options = mysite_decode( $new_options, $serialize = true );
			
			# Get current theme options
			$current_options = get_option( MYSITE_SETTINGS );
			$current_options = ( is_array( $current_options ) ) ? $current_options : array();
			
			# Merege old options with new
			$options = array_merge( $current_options, $new_options );
			
			if( update_option( MYSITE_SETTINGS, $options ) ) {
				# update was succesful : do nothing
			} else {
				# Revert to old options if theres an issue
				update_option( MYSITE_SETTINGS, $current_options );
			}
		}
	}
	
	
}

?>