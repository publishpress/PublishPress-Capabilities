<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Fluent Forms.
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Fluent Forms'] = [
        'fluentform_dashboard_access',
        'fluentform_entries_viewer',
        'fluentform_forms_manager',
        'fluentform_full_access',
        'fluentform_manage_entries',
        'fluentform_manage_payments',
        'fluentform_settings_manager',
        'fluentform_view_payments',
    ];

    return $pluginCaps;
});
<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin PublishPress Planner.
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $caps = [
        'edit_metadata',
        'edit_post_subscriptions',
        'pp_manage_roles',
        'pp_set_notification_channel',
        'pp_view_calendar',
        'pp_view_content_overview',
    ];

    /**
     * @deprecated 2.11.0
     */
    $caps = apply_filters_deprecated('cme_publishpress_capabilities', $caps, '2.11.0', 'cme_plugin_capabilities');

    $pluginCaps['PublishPress Planner'] = $caps;

    return $pluginCaps;
});
