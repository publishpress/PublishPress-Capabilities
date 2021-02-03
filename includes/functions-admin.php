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
        'pp-capabilities',
        'cme_fakefunc',
        'dashicons-admin-network',
		$menu_order
    );

	add_submenu_page('pp-capabilities',  __('Roles', 'capsman-enhanced'), __('Roles', 'capsman-enhanced'), $cap_name, 'pp-capabilities-roles', 'cme_fakefunc');
  add_submenu_page('pp-capabilities',  __('Admin Menus', 'capsman-enhanced'), __('Admin Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-admin-menus', 'cme_fakefunc');
  add_submenu_page('pp-capabilities',  __('Nav Menus', 'capsman-enhanced'), __('Nav Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-nav-menus', 'cme_fakefunc');
  add_submenu_page('pp-capabilities',  __('Backup', 'capsman-enhanced'), __('Backup', 'capsman-enhanced'), $cap_name, 'pp-capabilities-backup', 'cme_fakefunc');
  add_submenu_page('pp-capabilities',  __('Settings', 'capsman-enhanced'), __('Settings', 'capsman-enhanced'), $cap_name, 'pp-capabilities-settings', 'cme_fakefunc');

	if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {
	    add_submenu_page(
	        'pp-capabilities',
	        __('Upgrade to Pro', 'capsman-enhanced'),
	        __('Upgrade to Pro', 'capsman-enhanced'),
	        'manage_capabilities',
	        'capsman-enhanced',
	        'cme_fakefunc'
	    );
	}
}

function cme_fakefunc() {
}
