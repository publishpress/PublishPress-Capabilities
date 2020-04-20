<?php

// perf enhancement: display submenu links without loading framework and plugin code
function cme_submenus() {
    // First we check if user is administrator and can 'manage_capabilities'.
    if (current_user_can('administrator') && ! current_user_can('manage_capabilities')) {
        if ($admin = get_role('administrator')) {
        	$admin->add_cap('manage_capabilities');
        }
    }

	$cap_name = (is_multisite() && is_super_admin()) ? 'read' : 'manage_capabilities';

    $permissions_title = __('Capabilities', 'capsman-enhanced');

    $menu_order = 72;

    if (defined('PUBLISHPRESS_PERMISSIONS_MENU_GROUPING')) {
        foreach (get_option('active_plugins') as $plugin_file) {
            if ( false !== strpos($plugin_file, 'publishpress.php') ) {
                $menu_order = 27;
            }
        }
    }

    add_menu_page(
        $permissions_title,
        $permissions_title,
        $cap_name,
        'capsman',
        'cme_fakefunc',
        'dashicons-admin-network',
		$menu_order
    );

    add_submenu_page('capsman',  __('Backup', 'capsman-enhanced'), __('Backup', 'capsman-enhanced'), $cap_name, 'capsman' . '-tool', 'cme_fakefunc');

	if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {
	    add_submenu_page(
	        'capsman', 
	        __('Upgrade to Pro', 'capsman-enhanced'), 
	        __('Upgrade to Pro', 'capsman-enhanced'), 
	        'manage_capabilities', 
	        'capabilities-pro',
	        'cme_fakefunc'
	    );
	}
}

function cme_fakefunc() {
}
