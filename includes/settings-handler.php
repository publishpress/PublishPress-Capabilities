<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process updates to plugin settings
 * 
 */

add_action('init', function() {
    if (wp_verify_nonce($_REQUEST['_wpnonce'], 'pp-capabilities-settings') && current_user_can('manage_capabilities')) {
        if (!empty($_POST['all_options'])) {
	        foreach(explode(',', $_POST['all_options']) as $option_name) {
	            foreach (['cme_', 'capsman', 'pp_capabilities'] as $prefix) {
	                if (0 === strpos($option_name, $prefix)) {
			            $value = isset($_POST[$option_name]) ? $_POST[$option_name] : '';
			
			            if (!is_array($value)) {
			                $value = trim($value);
			            }
	
	                    update_option($option_name, stripslashes_deep($value));
	                }
	            }
	        }
	    }
	
	    do_action('pp-capabilities-update-settings');
    }
});
