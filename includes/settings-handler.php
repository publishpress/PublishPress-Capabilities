<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process updates to plugin settings
 * 
 */

add_action('init', function() {
    if (check_admin_referer('pp-capabilities-settings') && current_user_can('manage_capabilities_settings')) {
        if (!empty($_POST['all_options'])) {
            foreach (array_map('sanitize_key', explode(',', sanitize_text_field($_POST['all_options']))) as $option_name) {
                foreach (['cme_', 'capsman', 'pp_capabilities', 'presspermit'] as $prefix) {
                    if (0 === strpos($option_name, $prefix)) {
                        $value = isset($_POST[$option_name]) ? $_POST[$option_name] : '';// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                        $value = is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
            
                        if (!is_array($value)) {
                            $value = trim($value);
                        }
                        
                        update_option($option_name, $value);
                    }
                }
            }
        }
    }
});
