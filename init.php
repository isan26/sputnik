<?php
require (__DIR__ . '/includes/sputnik.php');
require(__DIR__ . '/vendor/autoload.php');

add_action('admin_enqueue_scripts', function () {
    wp_register_script('sputnik.js', plugin_dir_url(__FILE__) . 'js/sputnik.js', ['jquery']);
    wp_enqueue_script('sputnik.js');
});

ob_start();
session_start();


//Incluye las clases basicas
require_once __DIR__ . '/includes/sputnik.php';
require_once __DIR__ . '/includes/baseModel.php';
require_once __DIR__ . '/includes/baseController.php';
require_once __DIR__ . '/includes/baseRestController.php';
require_once __DIR__ . '/includes/baseWidget.php';
require_once __DIR__ . '/includes/baseActiveModel.php';

add_action('init', function() {
    global $sputnik;
    $sputnik = new sputnik();
    $sputnik->debug = true;

    if ($sputnik->debug) {
        global $wpdb;
        $wpdb->show_errors();
    }
});


add_action('rest_api_init', function() {
    $restControllers = sputnik::getFiles('rest');
    global $sputnik;
    foreach ($restControllers as $restController) {
        $classname = basename($restController, '.php');
        $$classname = new $classname();
        $$classname->init('sputnik');
    }
});

add_action('widgets_init', function() {
    sputnik::includeDir('widgets/widgets');
    $widgets = sputnik::getFiles('widgets/widgets');
    foreach ($widgets as $widget) {
        if ($widget == 'index.html') {
            continue;
        }
        register_widget(basename($widget, '.php'));
    }
});

register_activation_hook(__FILE__, ['sputnik', 'install']);
register_deactivation_hook(__FILE__, ['sputnik', 'uninstall']);
