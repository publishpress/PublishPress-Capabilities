<?php

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

$activatedPlugins = get_option('active_plugins');

foreach ($activatedPlugins as $plugin) {
    $pluginName = explode('/', $plugin)[0];

    $filePath = __DIR__ . '/plugin-capabilities/' . $pluginName . '.php';
    if (is_readable($filePath)) {
        require_once $filePath;
    }
}
