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

        add_filter('pp_capabilities_post_feature_elements', [$this, 'ppc_free_post_feature_metaboxes_elements']);
        add_filter('pp_capabilities_post_feature_elements_classic', [$this, 'ppc_free_post_feature_metaboxes_elements']);
        add_action('pp_capabilities_feature_gutenberg_metaboxes_section', [$this, 'ppc_post_feature_metaboxes_section_promo'], 11, 3);
        add_action('pp_capabilities_feature_classic_metaboxes_section', [$this, 'ppc_post_feature_metaboxes_section_promo'], 11, 3);
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
    * Filter post features element and add metaboxes items
    *
    * @param array $elements Post screen elements.
    *
    * @since 2.1.1
    */
    public function ppc_free_post_feature_metaboxes_elements($elements)
    {

        $elements[__('Metaboxes', 'capsman-enhanced')] = [];

        return $elements;
    }

    /**
    * Add promo to post features metaboxes section title
    *
    * @param array $post_types Post type.
    * @param array $elements All elements.
    * @param array $post_disabled All disabled post type element.
    *
    * @since 2.1.1
    */
    function ppc_post_feature_metaboxes_section_promo($post_types, $elements, $post_disabled)
    {
    ?>
    <div style="color:red;text-align:center;">
        <?php _e('This is a PRO feature. Upgrade to PublishPress Capabilities Pro to have ability to hide metaboxes.', 'capsman-enhanced') ?>
    </div>
    <?php
}

}
