/**
 * Download Monitor Capabilities class.
 * Generated by Capabilities Extractor
 */
namespace PublishPress\Plugin_Capabilities;

class Download_Monitor
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Download_Monitor();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //Download Monitor Capabilities
        add_filter('cme_plugin_capabilities', [$this, 'cme_download_monitor_capabilities']);
    }

    /**
     * Download Monitor Capabilities
     *
     * @param array $plugin_caps
     * 
     * @return array
     */
    public function cme_download_monitor_capabilities($plugin_caps)
    {

        if (defined('DLM_VERSION')) {
            $plugin_caps['Download Monitor'] = apply_filters(
                'cme_download_monitor_capabilities',
                [
                    'dlm_manage_logs',
                    'dlm_view_reports',
                    'manage_downloads'
                ]
            );
        }

        return $plugin_caps;
    }
}

\PublishPress\Plugin_Capabilities\Download_Monitor::instance();
