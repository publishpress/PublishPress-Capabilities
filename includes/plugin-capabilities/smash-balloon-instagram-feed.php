/**
 * Smash Balloon Instagram Feed Capabilities class.
 * Generated by Capabilities Extractor
 */
namespace PublishPress\Plugin_Capabilities;

class Smash_Balloon_Instagram_Feed
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Smash_Balloon_Instagram_Feed();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //Smash Balloon Instagram Feed Capabilities
        add_filter('cme_plugin_capabilities', [$this, 'cme_smash_balloon_instagram_feed_capabilities']);
    }

    /**
     * Smash Balloon Instagram Feed Capabilities
     *
     * @param array $plugin_caps
     * 
     * @return array
     */
    public function cme_smash_balloon_instagram_feed_capabilities($plugin_caps)
    {

        if (defined('SBI_STORE_URL')) {
            $plugin_caps['Smash Balloon Instagram Feed'] = apply_filters(
                'cme_smash_balloon_instagram_feed_capabilities',
                [
                    'manage_custom_instagram_feed_options',
                    'manage_instagram_feed_options'
                ]
            );
        }

        return $plugin_caps;
    }
}

\PublishPress\Plugin_Capabilities\Smash_Balloon_Instagram_Feed::instance();
