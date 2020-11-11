<?php
function _cme_act_pp_active()
{
    if (defined('PRESSPERMIT_VERSION') || (defined('PPC_VERSION') && function_exists('pp_init_cap_caster'))) {
        define('PRESSPERMIT_ACTIVE', true);
    } else {
        if (defined('SCOPER_VERSION') || (defined('PP_VERSION') && function_exists('pp_init_users_interceptor'))) {
            define('OLD_PRESSPERMIT_ACTIVE', true);
        }
    }
}

function _cme_cap_helper()
{
    global $cme_cap_helper;

    require_once(dirname(__FILE__) . '/cap-helper.php');
    $cme_cap_helper = new CME_Cap_Helper();

    add_action('registered_post_type', '_cme_post_type_late_reg', 5, 2);
    add_action('registered_taxonomy', '_cme_taxonomy_late_reg', 5, 2);
}

function _cme_post_type_late_reg($post_type, $type_obj)
{
    global $cme_cap_helper;

    if (!empty($type_obj->public) || !empty($type_obj->show_ui)) {
        $cme_cap_helper->refresh();
    }
}

function _cme_taxonomy_late_reg($taxonomy, $tx_obj)
{
    global $cme_cap_helper;

    if (!empty($tx_obj->public)) {
        $cme_cap_helper->refresh();
    }
}

function _cme_init()
{
    require_once(dirname(__FILE__) . '/filters.php');

    load_plugin_textdomain('capsman-enhanced', false, dirname(plugin_basename(__FILE__)) . '/lang');
}

function cme_is_plugin_active($check_plugin_file)
{
    if (!$check_plugin_file)
        return false;

    $plugins = get_option('active_plugins');

    foreach ($plugins as $plugin_file) {
        if (false !== strpos($plugin_file, $check_plugin_file))
            return $plugin_file;
    }
}

// if a role is marked as hidden, also default it for use by Press Permit as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
function _cme_pp_default_pattern_role($role)
{
    if (!$pp_role_usage = get_option('pp_role_usage'))
        $pp_role_usage = array();

    if (empty($pp_role_usage[$role])) {
        $pp_role_usage[$role] = 'pattern';
        update_option('pp_role_usage', $pp_role_usage);
    }
}

// deprecated
function capsman_get_pp_option($option_basename)
{
    return pp_capabilities_get_permissions_option($option_basename);
}

function pp_capabilities_autobackup()
{
    global $wpdb;

    $roles = get_option($wpdb->prefix . 'user_roles');
    update_option('cme_backup_auto_' . current_time('Y-m-d_g-i-s_a'), $roles, false);

    $max_auto_backups = (defined('CME_AUTOBACKUPS')) ? CME_AUTOBACKUPS : 20;

    $keep_ids = $wpdb->get_col("SELECT option_id FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' ORDER BY option_id DESC LIMIT $max_auto_backups");

    if (count($keep_ids) == $max_auto_backups) {
        $id_csv = implode("','", $keep_ids);

        $wpdb->query(
            "DELETE FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' AND option_id NOT IN ('$id_csv')"
        );
    }
}

function pp_capabilities_get_permissions_option($option_basename)
{
    return (function_exists('presspermit')) ? presspermit()->getOption($option_basename) : pp_get_option($option_basename);
}

function pp_capabilities_update_permissions_option($option_basename, $option_val)
{
    function_exists('presspermit') ? presspermit()->updateOption($option_basename, $option_val) : pp_update_option($option_basename, $option_val);
}


add_filter('admin_menu', 'pp_capabilities_admin_menu_permission', 99999999);
function pp_capabilities_admin_menu_permission()
{

    global $menu, $submenu;

    $admin_global_menu = (array)$GLOBALS['menu'];;
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

    $disabled_menu = '';
    $disabled_child_menu = '';
    $user_roles = wp_get_current_user()->roles;
    $admin_menu_option = !empty(get_option('capsman_admin_menus')) ? (array)get_option('capsman_admin_menus') : [];
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
                    pp_cabapbility_admin_menu_access($menu_slug);
                }

                //remove menu and prevent page access if set
                if (isset($admin_global_submenu) && !empty($admin_global_submenu[$menu_slug])) {
                    foreach ($admin_global_submenu[$menu_slug] as $subindex => $subitem) {
                        $sub_menu_value = $menu_slug . $subindex;

                        if (in_array($sub_menu_value, $disabled_child_menu_array)) {
                            remove_submenu_page($menu_slug, $subitem[2]);
                            pp_cabapbility_admin_menu_access($subitem[2]);
                        }
                    }
                }
            }
        }

    }

}


function pp_cabapbility_admin_menu_access($slug)
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
        add_action('load-' . $slug, 'pp_cabapbility_admin_menu_access_denied');
        return true;
    }

    if ($slug === $url) {
        add_action('load-' . basename($uri['path']), 'pp_cabapbility_admin_menu_access_denied');
        return true;
    }


}

function pp_cabapbility_admin_menu_access_denied()
{
    $forbidden = esc_attr__('You did not have permission to access this page.', 'capsman-enhanced');
    wp_die(esc_html($forbidden));
}

function pp_capability_nav_menu_access_denied()
{
    $forbidden = esc_attr__('You did not have permission to access this page.', 'capsman-enhanced');
    wp_die(esc_html($forbidden));
}

/**
 * Checks the menu items for their visibility options and
 * removes menu items that are not visible.
 *
 * @return array
 */
function pp_capabilities_nav_menu_permission($items, $menu, $args)
{


    //return if it's admin page
    if (is_admin()) {
        return $items;
    }

    $disabled_nav_menu = '';

    $user_roles = (array)wp_get_current_user()->roles;
    $nav_menu_item_option = !empty(get_option('capsman_nav_item_menus')) ? (array)get_option('capsman_nav_item_menus') : [];

    //add loggedin and guest option to role
    if (is_user_logged_in()) {
        $user_roles[] = 'ppc_users';
    } else {
        $user_roles[] = 'ppc_guest';
    }

    //extract disabled menu for roles user belong
    foreach ($user_roles as $role) {
        if (array_key_exists($role, $nav_menu_item_option)) {
            $disabled_nav_menu .= implode(", ", (array)$nav_menu_item_option[$role]) . ', ';
        }
    }


    if ($disabled_nav_menu) {

        //extract only IDS
        $disabled_item_ids = preg_replace('!(0|[1-9][0-9]*)_([a-zA-Z0-9_.-]*),!s', '$1,', $disabled_nav_menu);

        $disabled_nav_menu_array = array_filter(explode(", ", $disabled_item_ids));

        foreach ($items as $key => $item) {

            $item_parent = get_post_meta($item->ID, '_menu_item_menu_item_parent', true);

            if (in_array($item->ID, $disabled_nav_menu_array) || in_array($item_parent, $disabled_nav_menu_array)) {
                unset($items[$key]);
            }
        }


    }

    return $items;
}

add_filter('wp_get_nav_menu_items', 'pp_capabilities_nav_menu_permission', 10, 3);


/**
 * Checks the menu items for their privacy and remove
 * if user do not have permission to item
 *
 */
function pp_capabilities_nav_menu_access($query)
{
    $user_id = get_current_user_id();


    $disabled_nav_menu = '';

    $user_roles = (array)wp_get_current_user()->roles;
    $nav_menu_item_option = !empty(get_option('capsman_nav_item_menus')) ? (array)get_option('capsman_nav_item_menus') : [];

    //add loggedin and guest option to role
    if (is_user_logged_in()) {
        $user_roles[] = 'ppc_users';
    } else {
        $user_roles[] = 'ppc_guest';
    }

    //extract disabled menu for roles user belong
    foreach ($user_roles as $role) {
        if (array_key_exists($role, $nav_menu_item_option)) {
            $disabled_nav_menu .= implode(", ", (array)$nav_menu_item_option[$role]) . ', ';
        }
    }


    if ($disabled_nav_menu) {

        //we only need object id and object name e.g, 1_category
        $disabled_object = preg_replace('!(0|[1-9][0-9]*)_([a-zA-Z0-9_.-]*),!s', '$2,', $disabled_nav_menu);
        $disabled_nav_menu_array = array_filter(explode(", ", $disabled_object));

        //category tags and taxonomy page check
        if (is_category() || is_tag() || is_tax()) {
            $taxonomy_id = get_queried_object()->term_id;
            $taxonnomy_type = get_queried_object()->taxonomy;
            foreach ($disabled_nav_menu_array as $item_option) {
                $option_object = $taxonomy_id . '_' . $taxonnomy_type;
                if (in_array($option_object, $disabled_nav_menu_array)) {
                    pp_capability_nav_menu_access_denied();
                }
            }
        }

        //post, page, cpt check
        if (is_singular()) {
            $post_type = get_post_type();
            $post_id = get_the_ID();
            foreach ($disabled_nav_menu_array as $item_option) {
                $option_object = $post_id . '_' . $post_type;
                if (in_array($option_object, $disabled_nav_menu_array)) {
                    pp_capability_nav_menu_access_denied();
                }
            }
        }


    }

}

add_filter('parse_query', 'pp_capabilities_nav_menu_access');