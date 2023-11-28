<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin MailOptin - Lite.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['MailOptin Lite'] = [
        'manage_mailoptin'
    ];

    return $pluginCaps;
});