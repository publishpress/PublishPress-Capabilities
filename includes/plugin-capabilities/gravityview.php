<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin GravityView.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['GravityView'] = [
        'edit_gravityviews',
        'gravityview_edit_entries',
        'gravityview_full_access',
        'gravityview_getting_started'
    ];

    return $pluginCaps;
});