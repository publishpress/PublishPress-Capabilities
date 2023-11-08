<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Smash Balloon Instagram Feed.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Smash Balloon Instagram Feed'] = [
        'manage_custom_instagram_feed_options',
        'manage_instagram_feed_options'
    ];

    return $pluginCaps;
});