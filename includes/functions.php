<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Functions available for any URL, which are not contained within a class
 * 
 * For performance and code separation, do not include functions that are only needed for wp-admin requests
 * 
 */


/**
 * Sanitizes a string entry
 *
 * Keys are used as internal identifiers. Uppercase or lowercase alphanumeric characters,
 * spaces, periods, commas, plusses, asterisks, colons, pipes, parentheses, dashes and underscores are allowed.
 *
 * @param string $entry String entry
 * @return string Sanitized entry
 */
function pp_capabilities_sanitize_entry( $entry ) {
    $entry = preg_replace( '/[^a-zA-Z0-9 \.\,\+\*\:\|\(\)_\-\=]/', '', $entry );
    return $entry;
}

function pp_capabilities_is_editable_role($role_name, $args = []) {
    static $editable_roles;

    if (!function_exists('wp_roles')) {
        return false;
    }

    if (!isset($editable_roles) || !empty($args['force_refresh'])) {
        $all_roles = wp_roles()->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles, $args);
    }

    return apply_filters('pp_capabilities_editable_role', isset($editable_roles[$role_name]), $role_name);
}

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

    load_plugin_textdomain('capsman-enhanced', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function cme_is_plugin_active($check_plugin_file)
{
    if (!$check_plugin_file)
        return false;

    $plugins = (array)get_option('active_plugins');

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

    $max_auto_backups = (defined('CME_AUTOBACKUPS')) ? (int) CME_AUTOBACKUPS : 20;

    $current_options = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' ORDER BY option_id DESC");

    if (count($current_options) >= $max_auto_backups) {
        $i = 0;

        foreach($current_options as $option_name) {
            $i++;

            if ($i > $max_auto_backups) {
        		$wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM $wpdb->options WHERE option_name = %s",
                        $option_name
                    )
        		);

                wp_cache_delete($option_name, 'options');
            }
        }
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

/**
 * Get post type.
 *
 * @return null|string String of the post type.
 */
function pp_capabilities_get_post_type()
{
    global $post, $typenow, $current_screen;

    // We have a post so we can just get the post type from that.
    if ($post && $post->post_type) {
        return $post->post_type;
    }

    // Check the global $typenow - set in admin.php
    if ($typenow) {
        return $typenow;
    }

    // Check the global $current_screen object - set in screen.php
    if ($current_screen && $current_screen->post_type) {
        return $current_screen->post_type;
    }

    if (isset($_GET['post']) && !is_array($_GET['post'])) {
        $post_id = (int) $_GET['post'];

    } elseif (isset($_POST['post_ID'])) {
        $post_id = (int) $_POST['post_ID'];
    }

    if (!empty($post_id)) {
        return get_post_type($post_id);
    }

    // lastly check the post_type querystring
    if (isset($_REQUEST['post_type'])) {
        return sanitize_key($_REQUEST['post_type']);
    }

    return 'post';
}

/**
 * Check if Classic Editor plugin is available.
 *
 * @return bool
 */
function pp_capabilities_is_classic_editor_available()
{
    global $wp_version;

    return class_exists('Classic_Editor')
        || function_exists( 'the_gutenberg_project' )
        || class_exists('Gutenberg_Ramp')
        || version_compare($wp_version, '5.0', '<')
        || class_exists('WooCommerce')
        || (defined('PP_CAPABILITIES_CONFIGURE_CLASSIC_EDITOR') && PP_CAPABILITIES_CONFIGURE_CLASSIC_EDITOR)
        || !empty(get_option('cme_editor_features_classic_editor_tab'))
        || (function_exists('et_get_option') && 'on' === et_get_option('et_enable_classic_editor', 'off'));
}

/**
 * Get admin bar node and set as global for our usage.
 * Due to admin toolbar, this function need to run in frontend as well
 *
 * @return array||object $wp_admin_bar nodes.
 */
function ppc_features_get_admin_bar_nodes($wp_admin_bar){

    $adminBarNode = is_object($wp_admin_bar) ? $wp_admin_bar->get_nodes() : '';
    $ppcAdminBar = [];

    if (is_array($adminBarNode) || is_object($adminBarNode)) {
        foreach ($adminBarNode as $adminBarnode) {
            $id = $adminBarnode->id;
            $title = $adminBarnode->title;
            $parent = $adminBarnode->parent;
            $ppcAdminBar[$id] = array('id' => $id, 'title' => $title, 'parent' => $parent);
        }
    }

    $GLOBALS['ppcAdminBar'] = $ppcAdminBar;
}
add_action('admin_bar_menu', 'ppc_features_get_admin_bar_nodes', 999);

/**
 * Implement admin features restriction.
 * Due to admin toolbar, this function need to run in frontend as well
 *
 */
function ppc_admin_feature_restrictions() {
    require_once ( PUBLISHPRESS_CAPS_ABSPATH . '/includes/features/restrict-admin-features.php' );    
    PP_Capabilities_Admin_Features::adminFeaturedRestriction();
}
add_action('init', 'ppc_admin_feature_restrictions', 999);


/**
 * Implement test user feature
 *
 * @return void
 */
function ppc_test_user_init () {
    require_once (PUBLISHPRESS_CAPS_ABSPATH . '/includes/test-user.php');
    PP_Capabilities_Test_User::init();
}
add_action('init', 'ppc_test_user_init');


/**
 * Redirect user to configured role login redirect
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function ppc_roles_login_redirect($redirect_to, $request, $user) {

    if (isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $user_role) {
            //get role option
            $role_option = get_option("pp_capabilities_{$user_role}_role_option", []);

            if (is_array($role_option) && !empty($role_option) 
                && !empty($role_option['custom_redirect']) && (int)$role_option['custom_redirect'] > 0
                && !empty($role_option['login_redirect'])
            ) {
                //custom url redirect
                $redirect_to = esc_url_raw($role_option['login_redirect']);
                break;
            } else if (is_array($role_option) && !empty($role_option) 
                && !empty($role_option['referer_redirect']) && (int)$role_option['referer_redirect'] > 0
                && wp_get_referer()
            ) {
                //referer url redirect
                $redirect_to = esc_url_raw(wp_get_referer());
                break;
            }
        }
    }

    return $redirect_to;
}
add_filter('login_redirect', 'ppc_roles_login_redirect', 10, 3);

/**
 * Redirect user to configured role logout redirect
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function ppc_roles_logout_redirect($redirect_to, $request, $user) {

    if (isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $user_role) {
            //get role option
            $role_option = get_option("pp_capabilities_{$user_role}_role_option", []);
            if (is_array($role_option) && !empty($role_option) && !empty($role_option['logout_redirect'])) {
                $redirect_to = esc_url_raw($role_option['logout_redirect']);
                break;
            }
        }
    }

    return $redirect_to;
}
add_filter('logout_redirect', 'ppc_roles_logout_redirect', 10, 3);

/**
 * Block user role login
 *
 * @param $user (null|WP_User|WP_Error) WP_User if the user is authenticated. WP_Error or null otherwise.
 * 
 * @return WP_User object if credentials authenticate the user. WP_Error or null otherwise
*/
function ppc_roles_wp_authenticate_user($user) {

    if (is_wp_error($user)) {
        return $user;
    }

    if (isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $user_role) {
            //get role option
            $role_option = get_option("pp_capabilities_{$user_role}_role_option", []);
            if (is_array($role_option) && !empty($role_option) 
                && !empty($role_option['disable_role_user_login']) 
                && (int)$role_option['disable_role_user_login'] > 0
            ) {
                return new WP_Error('ppc_roles_user_banned', __('Login permission denied.', 'capsman-enhanced'));
            }
        }
    }

    return $user;
}
add_filter('wp_authenticate_user', 'ppc_roles_wp_authenticate_user', 1);

/**
 * Wocommerce role admin access restriction remove
 */
function ppc_roles_disable_woocommerce_admin_restrictions($restrict_access) {

    if ($restrict_access && is_user_logged_in()) {
        $user = get_userdata(get_current_user_id());

        if (isset($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $user_role) {
                //get role option
                $role_option = get_option("pp_capabilities_{$user_role}_role_option", []);
                if (is_array($role_option) && !empty($role_option) && !empty($role_option['disable_woocommerce_admin_restrictions'])) {
                    $restrict_access = false;
                    break;
                }
            }
        }
    }
    return $restrict_access;
}
add_filter('woocommerce_prevent_admin_access', 'ppc_roles_disable_woocommerce_admin_restrictions', 20);
add_filter('woocommerce_disable_admin_bar', 'ppc_roles_disable_woocommerce_admin_restrictions', 20);

/**
 * List of capabilities admin pages
 *
 */
function pp_capabilities_admin_pages(){

    $pp_capabilities_pages = [
        'pp-capabilities', 
        'pp-capabilities-roles', 
        'pp-capabilities-admin-menus', 
        'pp-capabilities-nav-menus', 
        'pp-capabilities-editor-features', 
        'pp-capabilities-backup', 
        'pp-capabilities-settings', 
        'pp-capabilities-admin-features', 
        'pp-capabilities-profile-features'
    ];

   return apply_filters('pp_capabilities_admin_pages', $pp_capabilities_pages);
}

/**
 * Check if user is in capabilities admin page
 *
 */
function is_pp_capabilities_admin_page(){
    
    $pp_capabilities_pages = pp_capabilities_admin_pages();

    $is_pp_capabilities_page = false;
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pp_capabilities_pages )) {
        $is_pp_capabilities_page = true;
    }

    return apply_filters('is_pp_capabilities_admin_page', $is_pp_capabilities_page);
}


function pp_capabilities_nav_menu_access_denied()
{
    $forbidden = esc_attr__('You do not have permission to access this page.', 'capabilities-pro');
    wp_die(esc_html($forbidden));
}

/**
 * Nav menu restriction
 */
if (!is_admin()) {

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

        /* 
         * PublishPress Permissions: Restrict Nav Menus for a Permission Group
         * (Integrate PublishPress Capabilities Pro functionality).
         *
         * Copy into functions.php, modifying $restriction_role and $permission_group_ids to match your usage.
         *
         * note: Restriction_role can be an extra role that you create just for these menu restrictions.
         *       Configure Capabilities > Nav Menus as desired for that role.
         */
        /*
        add_filter('pp_capabilities_nav_menu_apply_role_restrictions', 
            function($roles, $menu_object) {
                if (function_exists('presspermit')) {
                    $permission_group_ids = [12, 14, 15];   // group IDs to restrict
                    $restriction_role = 'subscriber';       // role that has restrictions defined by Capabilities > Nav Menus

                    if (array_intersect(
                        array_keys(presspermit()->getUser()->groups['pp_group']), 
                        $permission_group_ids
                    )) {
                        $roles []= $restriction_role;
                    }
                }

                return $roles;
            },
            10, 2
        );
        */

        // Support plugin integrations by allowing additional role-based limitations to be applied to user based on external criteria
        $user_roles = apply_filters('pp_capabilities_nav_menu_apply_role_restrictions', $user_roles, compact('menu'));

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
    add_filter('wp_get_nav_menu_items', 'pp_capabilities_nav_menu_permission', 99, 3);

    /**
     * Checks the menu items for their privacy and remove
     * if user do not have permission to item
     *
     */
    function pp_capabilities_nav_menu_access($query)
    {
        if (!function_exists('wp_get_current_user')) {
            return;
        }

        $nav_menu_item_option = !empty(get_option('capsman_nav_item_menus')) ? (array)get_option('capsman_nav_item_menus') : [];

        if (!$nav_menu_item_option || !function_exists('wp_get_current_user')) {
            return;
        }

        $user_roles = (array)wp_get_current_user()->roles;

        //add loggedin and guest option to role
        $user_roles[] = (is_user_logged_in()) ? 'ppc_users' : 'ppc_guest';

        // Support plugin integrations by allowing additional role-based limitations to be applied to user based on external criteria
        $user_roles = apply_filters('pp_capabilities_nav_menu_access_role_restrictions', $user_roles);

        $disabled_nav_menu = '';

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
                        pp_capabilities_nav_menu_access_denied();
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
                        pp_capabilities_nav_menu_access_denied();
                    }
                }
            }
        }
    }
    add_action('parse_query', 'pp_capabilities_nav_menu_access');
}