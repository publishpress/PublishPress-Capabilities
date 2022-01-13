<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process updates to plugin settings
 * 
 */

add_action('init', function() {
    if (check_admin_referer('pp-capabilities-settings') && current_user_can('manage_capabilities')) {
        if (!empty($_POST['all_options'])) {
	        foreach(array_map('sanitize_key', explode(',', sanitize_text_field($_POST['all_options']))) as $option_name) {
	            foreach (['cme_', 'capsman', 'pp_capabilities'] as $prefix) {
	                if (0 === strpos($option_name, $prefix)) {
						
						// Free plugin doesn't currently have any settings, so disable this code for now to avoid sanitization concerns.

						// Leave upstream conditionals in place to ensure access is properly regulated in any future implementation.
	                }
	            }
	        }
	    }
	
	    do_action('pp-capabilities-update-settings');
    }
});
