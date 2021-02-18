<?php

class PP_Capabilities_Admin_UI {
    function __construct() {
        global $pagenow;

        if (is_admin() && (isset($_REQUEST['page']) && (in_array($_REQUEST['page'], ['pp-capabilities', 'pp-capabilities-backup', 'pp-capabilities-roles', 'pp-capabilities-admin-menus', 'pp-capabilities-nav-menus', 'pp-capabilities-settings']))
        || (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], ['pp-roles-add-role', 'pp-roles-delete-role']))
        || ( ! empty($_SERVER['SCRIPT_NAME']) && strpos( $_SERVER['SCRIPT_NAME'], 'p-admin/plugins.php' ) && ! empty($_REQUEST['action'] ) ) 
        || ( isset($_GET['action']) && 'reset-defaults' == $_GET['action'] )
        || in_array( $pagenow, array( 'users.php', 'user-edit.php', 'profile.php', 'user-new.php' ) )
        ) ) {
            global $capsman;
            
            // Run the plugin
            require_once ( dirname(CME_FILE) . '/framework/lib/formating.php' );
            require_once ( dirname(CME_FILE) . '/framework/lib/users.php' );
            
            require_once ( dirname(CME_FILE) . '/includes/manager.php' );
            $capsman = new CapabilityManager();
        } else {
            add_action( 'admin_menu', [$this, 'cmeSubmenus'], 20 );
        }
    }

	// perf enhancement: display submenu links without loading framework and plugin code
    function cmeSubmenus() {
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

}

function cme_fakefunc() {
}
