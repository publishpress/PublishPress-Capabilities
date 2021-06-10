<?php

class PP_Capabilities_Admin_UI {
	function __construct() {
		add_action('init', [$this, 'featureRestrictionsGutenberg']);
	
	    if (is_admin()) {
	        add_action('admin_init', [$this, 'featureRestrictionsClassic']);
	    }
	}

	private function applyFeatureRestrictions($editor = 'gutenberg') {
        global $pagenow;

        // Return if not a post editor request
        if (!in_array($pagenow, ['post.php', 'post-new.php'], TRUE)) {
            return;
        }
    
        static $def_post_types; // avoid redundant filter application

        if (!isset($def_post_types)) {
            //$def_post_types = apply_filters('pp_capabilities_feature_post_types', get_post_types(['public' => true]));
            $def_post_types = apply_filters('pp_capabilities_feature_post_types', ['post', 'page']);
        }

        $post_type = pp_capabilities_get_post_type();

        // Return if not a supported post type
        if (!in_array($post_type, $def_post_types, TRUE)) {
            return;
        }

        switch ($editor) {
            case 'gutenberg':
                if (_pp_capabilities_is_block_editor_active()) {
                    require_once ( dirname(CME_FILE) . '/includes/features/restrict-editor-features.php' );
                    PP_Capabilities_Post_Features::applyRestrictions($post_type);
                }
                
                break;

            case 'classic':
                if (!_pp_capabilities_is_block_editor_active()) {
                    require_once ( dirname(CME_FILE) . '/includes/features/restrict-editor-features.php' );
                    PP_Capabilities_Post_Features::adminInitClassic($post_type);
                }
        }
    }

    function featureRestrictionsGutenberg() {
        $this->applyFeatureRestrictions();
    }

    function featureRestrictionsClassic() {
        $this->applyFeatureRestrictions('classic');
    }
}

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

    add_submenu_page('capsman',  __('Admin Menus', 'capsman-enhanced'), __('Admin Menus', 'capsman-enhanced'), $cap_name, 'capsman' . '-pp-admin-menus', 'cme_fakefunc');
	add_submenu_page('pp-capabilities',  __('Editor Features', 'capsman-enhanced'), __('Editor Features', 'capsman-enhanced'), $cap_name, 'pp-capabilities-post-features', 'cme_fakefunc');

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
                return $use_block && apply_filters('use_block_editor_for_post_type', $use_block, $post_type, PHP_INT_MAX);
            }
        }
    }

    $pluginsState = array(
        'classic-editor' => class_exists( 'Classic_Editor' ), // is_plugin_active('classic-editor/classic-editor.php'),
        'gutenberg'      => function_exists( 'the_gutenberg_project' ), //is_plugin_active('gutenberg/gutenberg.php'),
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
