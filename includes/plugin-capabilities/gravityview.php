<?php
/**
 * GravityView Capabilities class.
 *
 * Generated by Capabilities Extractor
 */
class Publishpress_Capabilities_Gravityview
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Publishpress_Capabilities_Gravityview();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //GravityView Capabilities
        add_filter('cme_plugin_capabilities', [$this, 'cme_gravityview_capabilities']);
    }

    /**
     * GravityView Capabilities
     *
     * @param array $plugin_caps
     * 
     * @return array
     */
    public function cme_gravityview_capabilities($plugin_caps)
    {

        if (defined('GV_PLUGIN_VERSION')) {
            $plugin_caps['GravityView'] = apply_filters(
                'cme_gravityview_capabilities',
                [
                    'edit_gravityviews',
                    'gk_foundation_trustedlogin-support',
                    'gravityview_edit_entries',
                    'gravityview_full_access',
                    'gravityview_getting_started'
                ]
            );
        }

        return $plugin_caps;
    }
}
Publishpress_Capabilities_Gravityview::instance();
?>
