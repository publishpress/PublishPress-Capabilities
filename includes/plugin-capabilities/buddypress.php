<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin BuddyPress.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['BuddyPress'] = [
        'bp_members_invitations_view_screens',
        'bp_moderate'
    ];

    return $pluginCaps;
});