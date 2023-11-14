<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin bbPress.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['bbPress'] = [
        'edit_forums',
        'edit_replies',
        'edit_topics',
        'keep_gate',
        'moderate',
        'read_private_replies',
        'read_private_topics',
        'spectate',
        'view_trash'
    ];

    return $pluginCaps;
});