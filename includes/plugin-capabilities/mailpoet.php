<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin MailPoet.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['MailPoet'] = [
        'mailpoet_access_plugin_admin',
        'mailpoet_manage_automations',
        'mailpoet_manage_emails',
        'mailpoet_manage_features',
        'mailpoet_manage_forms',
        'mailpoet_manage_segments',
        'mailpoet_manage_settings',
        'mailpoet_manage_subscribers'
    ];

    return $pluginCaps;
});