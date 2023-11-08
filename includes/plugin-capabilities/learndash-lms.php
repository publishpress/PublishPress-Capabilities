<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin LearnDash LMS.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['LearnDash LMS'] = [
        'delete_assignment',
        'delete_course',
        'delete_courses',
        'delete_essays',
        'delete_group',
        'delete_groups',
        'delete_others_assignments',
        'delete_others_courses',
        'delete_others_essays',
        'delete_others_groups',
        'delete_private_courses',
        'delete_published_assignments',
        'delete_published_courses',
        'delete_published_essays',
        'delete_published_groups',
        'edit_assignment',
        'edit_assignments',
        'edit_courses',
        'edit_essays',
        'edit_groups',
        'edit_others_assignments',
        'edit_others_courses',
        'edit_others_essays',
        'edit_others_groups',
        'edit_private_courses',
        'edit_published_assignments',
        'edit_published_courses',
        'edit_published_essays',
        'edit_published_groups',
        'enroll_users',
        'publish_assignments',
        'publish_courses',
        'publish_essays',
        'publish_groups',
        'read_assignment',
        'read_course',
        'read_essays',
        'read_group',
        'read_private_assignments',
        'read_private_courses',
        'read_private_essays',
        'read_private_groups',
        'wpProQuiz_add_quiz',
        'wpProQuiz_change_settings',
        'wpProQuiz_delete_quiz',
        'wpProQuiz_edit_quiz',
        'wpProQuiz_export',
        'wpProQuiz_import',
        'wpProQuiz_reset_statistics',
        'wpProQuiz_show',
        'wpProQuiz_show_statistics',
        'wpProQuiz_toplist_edit'
    ];

    return $pluginCaps;
});