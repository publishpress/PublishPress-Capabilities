<?php

/*
 * PublishPress Capabilities [Free]
 *
 * Functions available to wp-admin requests, which are not contained within a class
 *
 */


require_once (dirname(CME_FILE) . '/includes/features/frontend-features/frontend-features-action.php');
\PublishPress\Capabilities\PP_Capabilities_Frontend_Features_Action::instance();

function cme_fakefunc() {
}

function pp_capabilities_get_post_id()
{
    global $post;

    if (defined('REST_REQUEST') && REST_REQUEST) {
        if ($_post_id = apply_filters('presspermit_rest_post_id', 0)) {
            return $_post_id;
        }
    }

    if (!empty($post) && is_object($post)) {
        if ('auto-draft' == $post->post_status) {
            return 0;
        } else {
            return $post->ID;
        }

    } elseif (isset($_REQUEST['post'])) {
        return (int)$_REQUEST['post'];

    } elseif (isset($_REQUEST['post_ID'])) {
        return (int)$_REQUEST['post_ID'];

    } elseif (isset($_REQUEST['post_id'])) {
        return (int)$_REQUEST['post_id'];

    } elseif (defined('WOOCOMMERCE_VERSION') && !empty($_REQUEST['product_id'])) {
        return (int)$_REQUEST['product_id'];
    }
}

/**
 * Based on Edit Flow's \Block_Editor_Compatible::should_apply_compat method.
 *
 * @return bool
 */
function _pp_capabilities_is_block_editor_active($post_type = '', $args = [])
{
    global $current_user, $wp_version;

    $defaults = ['suppress_filter' => false, 'force_refresh' => false];
    $args = array_merge($defaults, $args);
    $suppress_filter = $args['suppress_filter'];

    // Check if Revisionary lower than v1.3 is installed. It disables Gutenberg.
    if (defined('REVISIONARY_VERSION') && version_compare(REVISIONARY_VERSION, '1.3-beta', '<')) {
        return false;
    }

    static $buffer;
    if (!isset($buffer)) {
        $buffer = [];
    }

    if (!$post_type = pp_capabilities_get_post_type()) {
        return true;
    }

    if ($post_type_obj = get_post_type_object($post_type)) {
        if (!$post_type_obj->show_in_rest) {
            return false;
        }
    }

    if (isset($buffer[$post_type]) && empty($args['force_refresh']) && !$suppress_filter) {
        return $buffer[$post_type];
    }

    if (class_exists('Classic_Editor')) {
        if (isset($_REQUEST['classic-editor__forget']) && (isset($_REQUEST['classic']) || isset($_REQUEST['classic-editor']))) {
            return false;
        } elseif (isset($_REQUEST['classic-editor__forget']) && !isset($_REQUEST['classic']) && !isset($_REQUEST['classic-editor'])) {
            return true;
        } elseif (get_option('classic-editor-allow-users') === 'allow') {
            if ($post_id = pp_capabilities_get_post_id()) {
                $which = get_post_meta( $post_id, 'classic-editor-remember', true );

                if ('block-editor' == $which) {
                    return true;
                } elseif ('classic-editor' == $which) {
                    return false;
                }
            } else {
                $use_block = ('block' == get_user_meta($current_user->ID, 'wp_classic-editor-settings'));

                if (version_compare($wp_version, '5.9-beta', '>=')) {
                    remove_action('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2);
                    remove_filter('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2);
                }

                $use_block = $use_block && apply_filters('use_block_editor_for_post_type', $use_block, $post_type, PHP_INT_MAX);

                if (defined('PP_CAPABILITIES_RESTORE_NAV_TYPE_BLOCK_EDITOR_DISABLE') && version_compare($wp_version, '5.9-beta', '>=')) {
                    add_filter('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2 );
                }

                return $use_block;
            }
        }
    }

    $pluginsState = array(
        'classic-editor' => class_exists( 'Classic_Editor' ),
        'gutenberg'      => function_exists( 'the_gutenberg_project' ),
        'gutenberg-ramp' => class_exists('Gutenberg_Ramp'),
    );
    
    $conditions = [];

    if ($suppress_filter) remove_filter('use_block_editor_for_post_type', $suppress_filter, 10, 2);

    /**
     * 5.0:
     *
     * Classic editor either disabled or enabled (either via an option or with GET argument).
     * It's a hairy conditional :(
     */

    if (version_compare($wp_version, '5.9-beta', '>=')) {
        remove_action('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2);
        remove_filter('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2);
    }

    // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.Security.NonceVerification.NoNonceVerification
    $conditions[] = (version_compare($wp_version, '5.0', '>=') || $pluginsState['gutenberg'])
                    && ! $pluginsState['classic-editor']
                    && ! $pluginsState['gutenberg-ramp']
                    && apply_filters('use_block_editor_for_post_type', true, $post_type, PHP_INT_MAX);

    $conditions[] = version_compare($wp_version, '5.0', '>=')
                    && $pluginsState['classic-editor']
                    && (get_option('classic-editor-replace') === 'block'
                        && ! isset($_GET['classic-editor__forget']));

    $conditions[] = version_compare($wp_version, '5.0', '>=')
                    && $pluginsState['classic-editor']
                    && (get_option('classic-editor-replace') === 'classic'
                        && isset($_GET['classic-editor__forget']));

    $conditions[] = $pluginsState['gutenberg-ramp'] 
                    && apply_filters('use_block_editor_for_post', true, get_post(pp_capabilities_get_post_id()), PHP_INT_MAX);

    if (defined('PP_CAPABILITIES_RESTORE_NAV_TYPE_BLOCK_EDITOR_DISABLE') && version_compare($wp_version, '5.9-beta', '>=')) {
        add_filter('use_block_editor_for_post_type', '_disable_block_editor_for_navigation_post_type', 10, 2 );
    }

    // Returns true if at least one condition is true.
    $result = count(
                array_filter($conditions,
                    function ($c) {
                        return (bool)$c;
                    }
                )
            ) > 0;
    
    if (!$suppress_filter) {
        $buffer[$post_type] = $result;
    }

    // Returns true if at least one condition is true.
    return $result;
}

/**
 * Remove all non-alphanumeric and space characters from a string.
 *
 * @param string $string .
 *
 * @return string
 *
 * @since 2.1.1
 */
function ppc_remove_non_alphanumeric_space_characters($string)
{
    return preg_replace("/(\W)+/", "", $string);
}
	
/**
 * Get all capabilities backup section.
 *
 * @return array $backup_sections
 */
function pp_capabilities_backup_sections()
{
   $cms_id = 'capsman';
   $backup_sections = [];

   //Editor Features
   $backup_sections[$cms_id . '_editor_features_backup']['label']    = esc_html__('Editor Features', 'capability-manager-enhanced');
   $classic_editor = pp_capabilities_is_classic_editor_available();
   $def_post_types = array_unique(apply_filters('pp_capabilities_feature_post_types', ['post', 'page']));
   foreach ($def_post_types as $post_type) {
       if ($classic_editor) {
           $backup_sections[$cms_id . '_editor_features_backup']['options'][] = "capsman_feature_restrict_classic_{$post_type}";
       }
       $backup_sections[$cms_id . '_editor_features_backup']['options'][] = "capsman_feature_restrict_{$post_type}";
   }

   //Admin Features
   $backup_sections[$cms_id . '_admin_features_backup']['label']     = esc_html__('Admin Features', 'capability-manager-enhanced');
   $backup_sections[$cms_id . '_admin_features_backup']['options'][] = "capsman_disabled_admin_features";

   //Frontend Features
   $backup_sections[$cms_id . '_frontend_features_backup']['label']     = esc_html__('Frontend Features', 'capability-manager-enhanced');
   $backup_sections[$cms_id . '_frontend_features_backup']['options'][] = "capsman_disabled_frontend_features";

   //Profile Features
   $backup_sections[$cms_id . '_profile_features_backup']['label']     = esc_html__('Profile Features', 'capability-manager-enhanced');
   $backup_sections[$cms_id . '_profile_features_backup']['options'][] = "capsman_disabled_profile_features";
   $backup_sections[$cms_id . '_profile_features_backup']['options'][] = "capsman_profile_features_elements";

   //Redirects
   $backup_sections[$cms_id . '_redirects_backup']['label']     = esc_html__('Redirects', 'capability-manager-enhanced');
   $backup_sections[$cms_id . '_redirects_backup']['options'][] = "capsman_role_redirects";

   //Nav Menu
   $backup_sections['capsman_nav_menu_backup']['label']     = esc_html__('Nav Menu', 'capability-manager-enhanced');
   $backup_sections['capsman_nav_menu_backup']['options'][] = "capsman_nav_item_menus";

   //settings
   $backup_sections['capsman_settings_backup']['label']     = esc_html__('Settings');
   $backup_sections['capsman_settings_backup']['options']   = pp_capabilities_settings_options();

   return apply_filters('pp_capabilities_backup_sections', $backup_sections);
}

/**
 * Register and add inline styles.
 *
 * @param string $custom_css
 * @param string $handle
 *
 * @return string
 *
 * @since 2.3.5
 */
function ppc_add_inline_style($custom_css, $handle = 'ppc-dummy-css-handle')
{
   global $ppc_dummy_css_handle;

   if (!is_array($ppc_dummy_css_handle)) {
       $ppc_dummy_css_handle = [];
   }

   if (in_array($handle, $ppc_dummy_css_handle)) {
       // duplicate usage of this function with same handle won't work
       $handle .= '-' . time(); 
   }

    $ppc_dummy_css_handle[] = $handle;

    wp_register_style(esc_attr($handle), false);
    wp_enqueue_style(esc_attr($handle));
    wp_add_inline_style(esc_attr($handle), $custom_css);
}

/**
 * Register and add inline script.
 *
 * @param string $custom_script
 * @param string $handle
 *
 * @return string
 *
 * @since 2.4.0
 */
function ppc_add_inline_script($custom_script, $handle = 'ppc-dummy-script-handle')
{
    global $ppc_dummy_script_handle;

    if (!is_array($ppc_dummy_script_handle)) {
       $ppc_dummy_script_handle = [];
    }

    if (in_array($handle, $ppc_dummy_script_handle)) {
        // duplicate usage of this function with same handle won't work
        $handle .= '-' . time(); 
    }

    $ppc_dummy_script_handle[] = $handle;

    wp_register_script(esc_attr($handle), false, ['jquery']);
    wp_enqueue_script(esc_attr($handle), false, ['jquery']);
    wp_add_inline_script(esc_attr($handle), $custom_script);
}

function pp_capabilities_settings_options() {
   $settings_options = [
       'cme_editor_features_private_post_type',
       'cme_capabilities_show_private_taxonomies',
       'cme_capabilities_add_user_multi_roles',
       'cme_capabilities_edit_user_multi_roles',
       'cme_editor_features_classic_editor_tab',
       'cme_test_user_admin_bar',
       'cme_test_user_admin_bar_search',
       'cme_test_user_footer_notice',
       'cme_test_user_excluded_roles',
       'cme_profile_features_auto_redirect',
       'cme_role_same_page_redirect_cookie',
       'cme_admin_notice_options',
   ];

   return apply_filters('pp_capabilities_settings_options', $settings_options);
}

function cme_publishpress_capabilities_capabilities($capabilities) {

    $capabilities = (array)$capabilities;

    $capabilities = array_merge(
        $capabilities,
        [
            'manage_capabilities_dashboard',
            'manage_capabilities_roles',
            'manage_capabilities',
            'manage_capabilities_editor_features',
            'manage_capabilities_admin_features',
            'manage_capabilities_admin_menus',
            'manage_capabilities_frontend_features',
            'manage_capabilities_profile_features',
            'manage_capabilities_redirects',
            'manage_capabilities_nav_menus',
            'manage_capabilities_user_testing',
            'manage_capabilities_backup',
            'manage_capabilities_settings'
        ]
    );

    $capabilities = array_unique($capabilities);
    
    return $capabilities;
}

/**
 * Dashboard items
 *
 * @param mixed $current
 * @param bool $role_edit whether current action is role edit
 * @param bool $role_copy whether current action is role copy
 *
 * @return array
 */
function pp_capabilities_dashboard_options() {

    $features = [];

    $features['roles'] = [
        'label'        => esc_html__('Roles', 'capability-manager-enhanced'),
        'description'  => esc_html__('Roles allows you to create, edit, and delete all the user roles on your site.', 'capability-manager-enhanced'),
    ];

    $features['capabilities'] = [
        'label'        => esc_html__('Capabilities', 'capability-manager-enhanced'),
        'description'  => esc_html__('Capabilities allows you to change the permissions for any user role.', 'capability-manager-enhanced'),
    ];

    $features['editor-features'] = [
        'label'        => esc_html__('Editor Features', 'capability-manager-enhanced'),
        'description'  => esc_html__('Editor Features allows you to remove elements from the post editing screen.', 'capability-manager-enhanced'),
    ];

    $features['admin-features'] = [
        'label'        => esc_html__('Admin Features', 'capability-manager-enhanced'),
        'description'  => esc_html__('Admin Features allows you to remove elements from the admin area and toolbar.', 'capability-manager-enhanced'),
    ];

    $features['frontend-features'] = [
        'label'        => esc_html__('Frontend Features', 'capability-manager-enhanced'),
        'description'  => esc_html__('Frontend Features allows you to add or remove elements from the frontend of your site.', 'capability-manager-enhanced'),
    ];

    $features['profile-features'] = [
        'label'        => esc_html__('Profile Features', 'capability-manager-enhanced'),
        'description'  => esc_html__('Profile Features allows you to remove elements from the Profile screen.', 'capability-manager-enhanced'),
    ];

    $features['redirects'] = [
        'label'        => esc_html__('Redirects', 'capability-manager-enhanced'),
        'description'  => esc_html__('Redirects allows you to redirect users in a role after Registration, Login or Logout.', 'capability-manager-enhanced'),
    ];

    $features['nav-menus'] = [
        'label'        => esc_html__('Nav Menus', 'capability-manager-enhanced'),
        'description'  => esc_html__('Nav Menus allows you to block access to frontend menu links.', 'capability-manager-enhanced'),
    ];

    $features['user-testing'] = [
        'label'        => esc_html__('User Testing', 'capability-manager-enhanced'),
        'description'  => esc_html__('Test your site by instantly logging in as another user. Available accounts include any which the current user can edit.', 'capability-manager-enhanced'),
    ];

    $features['admin-notices'] = [
        'label'        => esc_html__('Admin Notices', 'capability-manager-enhanced'),
        'description'  => esc_html__('Remove Admin Notices from WordPress admin screen and organize them in to a single area.', 'capability-manager-enhanced'),
    ];

    $features = apply_filters('pp_capabilities_dashboard_features', $features);

    return $features;
}



/**
 * Return list of capabilities sub menus
 *
 * @param boolean $cme_fakefunc
 * @return array $sub_menu_pages
 */
function pp_capabilities_sub_menu_lists($cme_fakefunc = false) {
    global $capsman;

    $super_user = (is_multisite() && is_super_admin());

    $sub_menu_pages = [];
    $sub_menu_pages['dashboard'] = [
        'title'             => __('Dashboard'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_dashboard',
        'page'              => 'pp-capabilities-dashboard',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'dashboardPage'],
        'dashboard_control' => false,
    ];
    $sub_menu_pages['roles'] = [
        'title'             => __('Roles', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_roles',
        'page'              => 'pp-capabilities-roles',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageRoles'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['capabilities'] = [
        'title'             => __('Capabilities', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities',
        'page'              => 'pp-capabilities',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'generalManager'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['editor-features'] = [
        'title'             => __('Editor Features', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_editor_features',
        'page'              => 'pp-capabilities-editor-features',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageEditorFeatures'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['admin-features'] = [
        'title'             => __('Admin Features', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_admin_features',
        'page'              => 'pp-capabilities-admin-features',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageAdminFeatures'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['frontend-features'] = [
        'title'             => __('Frontend Features', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_frontend_features',
        'page'              => 'pp-capabilities-frontend-features',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageFrontendFeatures'],
        'dashboard_control' => true,
    ];
    if ($cme_fakefunc) {
        $sub_menu_pages['admin-menus'] = [
            'title'             => __('Admin Menus', 'capability-manager-enhanced'),
            'capabilities'      => $super_user ? 'read' : 'manage_capabilities_admin_menus',
            'page'              => 'pp-capabilities-admin-menus',
            'callback'          => 'cme_fakefunc',
            'dashboard_control' => true,
        ];
    }
    $sub_menu_pages['profile-features'] = [
        'title'             => __('Profile Features', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_profile_features',
        'page'              => 'pp-capabilities-profile-features',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageProfileFeatures'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['redirects'] = [
        'title'             => __('Redirects', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_redirects',
        'page'              => 'pp-capabilities-redirects',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageRedirects'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['nav-menus'] = [
        'title'             => __('Nav Menus', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_nav_menus',
        'page'              => 'pp-capabilities-nav-menus',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'ManageNavMenus'],
        'dashboard_control' => true,
    ];
    $sub_menu_pages['backup'] = [
        'title'             => __('Backup', 'capability-manager-enhanced'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_backup',
        'page'              => 'pp-capabilities-backup',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'backupTool'],
        'dashboard_control' => false,
    ];
    $sub_menu_pages['settings'] = [
        'title'             => __('Settings'),
        'capabilities'      => $super_user ? 'read' : 'manage_capabilities_settings',
        'page'              => 'pp-capabilities-settings',
        'callback'          => $cme_fakefunc ? 'cme_fakefunc' : [$capsman, 'settingsPage'],
        'dashboard_control' => false,
    ];

    $sub_menu_pages = apply_filters('pp_capabilities_sub_menu_lists', $sub_menu_pages, $cme_fakefunc);

    return $sub_menu_pages;
}

function pp_capabilities_user_can_caps() {
    $ppc_user_caps = [];

    $menu_caps = apply_filters('cme_publishpress_capabilities_capabilities', []);
    foreach ($menu_caps as $menu_cap) {
        if (current_user_can($menu_cap)) {
            $ppc_user_caps[] = $menu_cap;
        }
    }

    return $ppc_user_caps;
}

/**
 * Convert title to slug
 *
 * @param string $title
 * @param string $separator
 * @param string $slug_case
 * 
 * @return string
 */
function pp_capabilities_convert_to_slug($title, $separator = '-', $slug_case = 'strtolower') {
    
    if ($slug_case == 'strtolower') {
        $title = strtolower($title);
    } elseif ($slug_case == 'ucwords') {
        $title = ucwords($title);
    }

    $title = preg_replace('/[^a-zA-Z0-9]+/', $separator, $title);
    $title = trim($title, $separator);

    return $title;
}