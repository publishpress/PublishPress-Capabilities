<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Wordfence Security.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Wordfence Security'] = [
        'wf2fa_activate_2fa_others',
        'wf2fa_activate_2fa_self',
        'wf2fa_manage_settings'
    ];

    return $pluginCaps;
});