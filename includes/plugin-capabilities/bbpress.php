/**
 * bbPress Capabilities class.
 * Generated by Capabilities Extractor
 */
namespace PublishPress\Plugin_Capabilities;

class BbPress
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new BbPress();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //bbPress Capabilities
        add_filter('cme_plugin_capabilities', [$this, 'cme_bbpress_capabilities']);
    }

    /**
     * bbPress Capabilities
     *
     * @param array $plugin_caps
     * 
     * @return array
     */
    public function cme_bbpress_capabilities($plugin_caps)
    {

        if (defined('REPLACE_WITH_PLUGIN_VERSION_CONSTANT')) {
            $plugin_caps['bbPress'] = apply_filters(
                'cme_bbpress_capabilities',
                [
                    'edit_forums',
                    'edit_replies',
                    'edit_topics',
                    'keep_gate',
                    'moderate',
                    'read_private_replies',
                    'read_private_topics',
                    'spectate',
                    'view_trash'
                ]
            );
        }

        return $plugin_caps;
    }
}

\PublishPress\Plugin_Capabilities\BbPress::instance();
