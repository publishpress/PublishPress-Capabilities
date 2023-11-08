<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Give - Donation Plugin.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Give Donation Plugin'] = [
        'assign_give_form_terms',
        'assign_give_payment_terms',
        'delete_give_form_terms',
        'delete_give_forms',
        'delete_give_payment_terms',
        'delete_give_payments',
        'delete_others_give_forms',
        'delete_others_give_payments',
        'delete_private_give_forms',
        'delete_private_give_payments',
        'delete_published_give_forms',
        'delete_published_give_payments',
        'edit_give_form_terms',
        'edit_give_forms',
        'edit_give_payment_terms',
        'edit_give_payments',
        'edit_others_give_forms',
        'edit_others_give_payments',
        'edit_private_give_forms',
        'edit_private_give_payments',
        'edit_published_give_forms',
        'edit_published_give_payments',
        'export_give_reports',
        'import_give_forms',
        'import_give_payments',
        'manage_give_form_terms',
        'manage_give_payment_terms',
        'manage_give_settings',
        'publish_give_forms',
        'publish_give_payments',
        'read_private_give_forms',
        'read_private_give_payments',
        'view_give_form_stats',
        'view_give_payment_stats',
        'view_give_payments',
        'view_give_reports',
        'view_give_sensitive_data'
    ];

    return $pluginCaps;
});