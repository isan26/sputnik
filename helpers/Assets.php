<?php

/**
 * Class to register CSS and JS files, also you can register inline JS
 *
 * @author Isan
 */
class Assets {

    public static function registerJS(Array $files = [], Array $deps = []) {
        foreach ($files as $file) {
            wp_enqueue_script(basename($file, '.js'), plugin_dir_url(__DIR__) . "js/$file", $deps, false, false);
        }
    }

    public static function registerCSS(Array $files = [], Array $deps = []) {
        foreach ($files as $file) {
            wp_enqueue_style(basename($file, '.css'), plugin_dir_url(__DIR__) . "css/$file", $deps, false);
        }
    }

    public static function registerInlineJS($js) {
        add_action('wp_print_footer_scripts', function() use ($js) {
            echo '<script>';
            echo $js;
            echo '</script>';
        });
    }
    
        public static function registerInlineCSS($css) {
        add_action('wp_print_footer_scripts', function() use ($css) {
            echo '<style>';
            echo $css;
            echo '</style>';
        });
    }

}
