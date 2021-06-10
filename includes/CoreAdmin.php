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
    }

    function setUpgradeMenuLink() {
        $url = 'https://publishpress.com/links/capabilities-menu';
        ?>

        <script type="text/javascript">

            jQuery(document).ready(function($) {

                var current_link, links = $('#toplevel_page_capsman ul li a');

                $.each(links, function () {
                    current_link = $(this).attr('href');
                    current_link = current_link.split('=')[1];
                    if(current_link === 'capabilities-pro')
                    {
                        $(this).attr('href', '<?php echo $url;?>').attr('target', '_blank').css('font-weight', 'bold').css('color', '#FEB123');
                    }
                 });
            });
        </script>
		<?php
    }
}