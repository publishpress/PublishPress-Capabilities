<?php
namespace PublishPress\Capabilities;

class CoreAdmin {
    function __construct() {
        add_action('admin_print_scripts', [$this, 'setUpgradeMenuLink'], 50);
    }

    function setUpgradeMenuLink() {
        $url = 'https://publishpress.com/links/capabilities-menu';
        ?>
        <style type="text/css">
        #toplevel_page_capsman ul li:last-of-type a {font-weight: bold !important; color: #FEB123 !important;}
        </style>

		<script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#toplevel_page_capsman ul li:last a').attr('href', '<?php echo $url;?>').attr('target', '_blank').css('font-weight', 'bold').css('color', '#FEB123');
            });
        </script>
		<?php
    }
}