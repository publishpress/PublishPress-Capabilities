<?php
namespace PublishPress\Capabilities;

class CoreAdmin {
    function __construct() {
        add_action('admin_print_scripts', [$this, 'setUpgradeMenuLink'], 50);

        if (is_admin()) {
            $autoloadPath = PUBLISHPRESS_CAPS_ABSPATH . '/vendor/autoload.php';
			if (file_exists($autoloadPath)) {
				require_once $autoloadPath;
			}

            require_once PUBLISHPRESS_CAPS_ABSPATH . '/vendor/publishpress/wordpress-version-notices/includes.php';
    
            add_filter(\PPVersionNotices\Module\TopNotice\Module::SETTINGS_FILTER, function ($settings) {
                $settings['capabilities'] = [
                    'message' => 'You\'re using PublishPress Capabilities Free. The Pro version has more features and support. %sUpgrade to Pro%s',
                    'link'    => 'https://publishpress.com/links/capabilities-banner',
                    'screens' => [
                        ['base' => 'toplevel_page_pp-capabilities'],
                        ['base' => 'capabilities_page_pp-capabilities-roles'],
                        ['base' => 'capabilities_page_pp-capabilities-backup'],
                        ['base' => 'capabilities_page_pp-capabilities-settings'],
                    ]
                ];
    
                return $settings;
            });
        }

        add_action('pp-capabilities-admin-submenus', [$this, 'actCapabilitiesSubmenus']);

        add_action('wp_ajax_ppc_submit_feature_gutenberg_by_ajax', [$this, 'ppc_submit_custom_feature_post_promo_callback']);
        add_action('wp_ajax_ppc_submit_feature_classic_by_ajax', [$this, 'ppc_submit_custom_feature_post_promo_callback']);
    }

    function setUpgradeMenuLink() {
        $url = 'https://publishpress.com/links/capabilities-menu';
        ?>
        <style type="text/css">
        #toplevel_page_pp-capabilities ul li:last-of-type a {font-weight: bold !important; color: #FEB123 !important;}
        </style>

		<script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#toplevel_page_pp-capabilities ul li:last a').attr('href', '<?php echo $url;?>').attr('target', '_blank').css('font-weight', 'bold').css('color', '#FEB123');
            });
        </script>
		<?php
    }

    function actCapabilitiesSubmenus() {
        $cap_name = (is_multisite() && is_super_admin()) ? 'read' : 'manage_capabilities';
        
        add_submenu_page('pp-capabilities',  __('Admin Menus', 'capsman-enhanced'), __('Admin Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-admin-menus', [$this, 'AdminMenusPromo']);
        add_submenu_page('pp-capabilities',  __('Nav Menus', 'capsman-enhanced'), __('Nav Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-nav-menus', [$this, 'NavMenusPromo']);
    }

    function AdminMenusPromo() {
        wp_enqueue_style('pp-capabilities-admin-core', plugin_dir_url(CME_FILE) . 'includes-core/admin-core.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');
        include (dirname(__FILE__) . '/admin-menus-promo.php');
    }

    function NavMenusPromo() {
        wp_enqueue_style('pp-capabilities-admin-core', plugin_dir_url(CME_FILE) . 'includes-core/admin-core.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');
        include (dirname(__FILE__) . '/nav-menus-promo.php');
    }

    /**
    * Submit new item for editor feature ajax callback.
    *
    * @since 2.1.1
    */
    function ppc_submit_custom_feature_post_promo_callback()
    {

        $response['status']  = 'error';
        $response['message'] = __('An error occured!', 'capsman-enhanced');
        $response['content'] = '';

        $def_post_types = apply_filters('pp_capabilities_feature_post_types', ['post', 'page']);
        $custom_label   = isset($_POST['custom_label']) ? $_POST['custom_label'] : '';
        $custom_element = isset($_POST['custom_element']) ? $_POST['custom_element'] : '';
        $security       = isset($_POST['security']) ? $_POST['security'] : '';
        $action         = isset($_POST['action']) ? $_POST['action'] : '';

        if (!wp_verify_nonce($security, 'ppc-custom-feature-nonce')) {
            $response['message'] = __('Invalid action. Reload this page and try again if occured in error.', 'capsman-enhanced');
        } elseif (empty(trim($custom_label)) || empty(trim($custom_element))) {
            $response['message'] = __('All fields are required.', 'capsman-enhanced');
        } else {
            $response['status']  = 'promo';
            $response['message'] = __('This is a pro feature. Upgrade to PRO version of the plugin to be able to add custom item.','capsman-enhanced');
        }

        wp_send_json($response);
    } 




}
