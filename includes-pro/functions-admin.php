<?php

add_action('pp-capabilities-admin-submenus-placeholder', 'pp_capabilities_add_restrict_menus');

add_filter('admin_menu', 'pp_capabilities_admin_menu_permission', PHP_INT_MAX - 1);

function pp_capabilities_add_restrict_menus() {
    $cap_name = (is_multisite() && is_super_admin()) ? 'read' : 'manage_capabilities';
    add_submenu_page('capsman',  __('Restrict Menus', 'capsman-enhanced'), __('Restrict Menus', 'capsman-enhanced'), $cap_name, 'capsman' . '-pp-admin-menus', 'cme_fakefunc');
}

function pp_capabilities_admin_menu_permission()
{
    global $menu, $submenu;

    $admin_global_menu 	  = (array)$GLOBALS['menu'];;
    $admin_global_submenu = (array)$GLOBALS['submenu'];

    if (is_object($admin_global_submenu)) {
        $admin_global_submenu = get_object_vars($admin_global_submenu);
    }

    if (!isset($admin_global_menu) || empty($admin_global_menu)) {
        $admin_global_menu = $menu;
    }

    if (!isset($admin_global_submenu) || empty($admin_global_submenu)) {
        $admin_global_submenu = $submenu;
    }

    //define menu and sub menu to be used on permission page
    define('PPC_ADMIN_GLOBAL_MENU', $admin_global_menu);
    define('PPC_ADMIN_GLOBAL_SUBMENU', $admin_global_submenu);

    //return if not admin page
    if (!is_admin()) {
        return;
    }

    //return if it's ajax request
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    $disabled_menu 		 	 = '';
    $disabled_child_menu 	 = '';
    $user_roles			 	 = wp_get_current_user()->roles;
    $admin_menu_option 	 	 = !empty(get_option('capsman_admin_menus')) ? (array)get_option('capsman_admin_menus') : [];
    $admin_child_menu_option = !empty(get_option('capsman_admin_child_menus')) ? (array)get_option('capsman_admin_child_menus') : [];

    //extract disabled menu for roles user belong
    foreach ($user_roles as $role) {
        if (array_key_exists($role, $admin_menu_option)) {
            $disabled_menu .= implode(", ", (array)$admin_menu_option[$role]) . ', ';
        }

        if (array_key_exists($role, $admin_child_menu_option)) {
            $disabled_child_menu .= implode(", ", (array)$admin_child_menu_option[$role]) . ', ';
        }
    }

    if ($disabled_menu || $disabled_child_menu) {
        $disabled_menu_array = [];
        $disabled_child_menu_array = [];

        if ($disabled_menu) {
            $disabled_menu_array = array_filter(explode(", ", $disabled_menu));
        }
        if ($disabled_child_menu) {
            $disabled_child_menu_array = array_filter(explode(", ", $disabled_child_menu));
        }

        foreach ($admin_global_menu as $key => $item) {

            if (isset($item[2])) {
                $menu_slug = $item[2];

                //remove menu and prevent page access if set
                if (in_array($menu_slug, $disabled_menu_array)) {
                    remove_menu_page($menu_slug);
                    pp_capabilities_admin_menu_access($menu_slug);
                }

                //remove menu and prevent page access if set
                if (isset($admin_global_submenu) && !empty($admin_global_submenu[$menu_slug])) {
                    foreach ($admin_global_submenu[$menu_slug] as $subindex => $subitem) {
                        $sub_menu_value = $menu_slug . $subindex;

                        if (in_array($sub_menu_value, $disabled_child_menu_array)) {
                            remove_submenu_page($menu_slug, $subitem[2]);
                            pp_capabilities_admin_menu_access($subitem[2]);
                        }
                    }
                }
            }
        }
    }
}

function pp_capabilities_admin_menu_access($slug)
{

    $url = basename(esc_url_raw($_SERVER['REQUEST_URI']));
    $url = htmlspecialchars($url);

    if (!isset($url)) {
        return false;
    }

    $uri = wp_parse_url($url);

    if (!isset($uri['path'])) {
        return false;
    }

    if (!isset($uri['query']) && strpos($uri['path'], $slug) !== false) {
        add_action('load-' . $slug, 'pp_capabilities_admin_menu_access_denied');
        return true;
    }

    if ($slug === $url) {
        add_action('load-' . basename($uri['path']), 'pp_capabilities_admin_menu_access_denied');
        return true;
    }


}

function pp_capabilities_admin_menu_access_denied()
{
    $forbidden = esc_attr__('You did not have permission to access this page.', 'capsman-enhanced');
    wp_die(esc_html($forbidden));
}

function ppc_process_admin_menu_title($title)
{
    //strip count content
    $title = preg_replace('#<span class="(.*?)count-(.*?)">(.*?)</span>#', '', $title);

    //strip screen reader content
    $title = preg_replace('#<span class="(.*?)screen-reader-text(.*?)">(.*?)</span>#', '', $title);

    //strip other html tags
    $title = strip_tags($title);

    return $title;
}
