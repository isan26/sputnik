<?php

/*
  Plugin Name: Bolsa ACOREC
  Plugin URI: http://www.datys.cu
  Description: Plugin gestión de la Bolsa de ACOREC.
  Author: Isan Rodriguez Trimiño
  Version: 0.1
  Author URI: mailto:isan.rodriguez@datys.cu
 */

add_action('admin_enqueue_scripts', function () {
    wp_register_script('sputnik.js', plugin_dir_url(__FILE__) . 'js/sputnik.js', ['jquery']);
    wp_enqueue_script('sputnik.js');
});

ob_start();
session_start();

class sputnik {

    CONST TOINCLUDE = [
        'helpers', //Clases con helpers para HTML
        'models', //Modelos(ORM)
        'controllers', //Controladoras
        'rest', //Controladoras RESTFULL
    ];

    //Debug Mode?
    public $debug = false;
    public $defaultSection = 'index';

    /**
     * Variable para debug;
     * @var array
     */
    private $logs = [];

    public function __construct() {
        //include all files
        foreach (self::TOINCLUDE as $dir) {
            self::includeDir($dir);
        }
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    static function install() {
        if (get_option('acorec_convocatoria', NULL) === NULL) { //Si null es pq es la primera vez que se instala el plugin
            add_option('acorec_convocatoria', true);
            add_option('acorec_inscripcion_deshabilitada', 'No hay convocatoria activa por el momento.');
            add_option('acorec_smtp_user', '');
            add_option('acorec_smtp_pass', '');
            add_option('acorec_smtp_server', '');
            add_option('acorec_smtp_puerto', '25'); //Puerto SMTP standard
            add_option('acorec_smtp_email', ''); //Email desde el que se envia
            add_option('acorec_smtp_sender', ''); //Nombre de quien lo envia
            add_option('acorec_mail_pag', ''); //URL interna para cambio de password
            add_option('acorec_smtp_encryption', ''); //Cifrado para SMTP
        }
        //CREAR LAS TABLAS EN LA BD
        global $wpdb;
        $table_buffer = <<<BUFFER
CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}acorec_buffer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  `body` TEXT NOT NULL,
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
BUFFER;

        $table_usuario = <<<USUARIO
CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}acorec_usuario` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `pass` VARCHAR(45) NOT NULL,
  `token` VARCHAR(32) DEFAULT NULL,
  `expire` date DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;
USUARIO;

        $table_nomenclador = <<<NOMENCLADOR
CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}acorec_nomenclator` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  `body` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
NOMENCLADOR;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($table_buffer);
        dbDelta($table_usuario);
        dbDelta($table_nomenclador);
    }

    static function unistall() {
        return true;
    }

    public function basePath() {
        return __DIR__;
    }

    /**
     * Logs para debug
     * @param string $log el texto a guardar
     * @param bool $toFile si guardar los logs en un fichero false por defecto
     */
    public function addLog($log, $toFile = false) {
        $this->logs[] = $log;
        if ($toFile === true) {
            //Agrega el log a un fichero
            file_put_contents(__DIR__ . "/" . "debug.log", $log . "\n", FILE_APPEND);
        }
    }

    public function getLogs() {
        return $this->logs;
    }

    public static function getFiles($dir) {
        $files = scandir(__DIR__ . DIRECTORY_SEPARATOR . $dir);
        unset($files[0]);
        unset($files[1]);
        return $files;
    }

    public static function includeDir($dir) {
        foreach (self::getFiles($dir) as $file) {
            if ($file == 'index.html') {
                continue;
            }
            require __DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file;
        }
    }

    public function admin_menu() {
        add_menu_page(__('Sputnik', 'Datys'), __('Bolsa ACOREC', 'Datys'), 'manage_options', 'sputnik', [$this, 'init'], 'dashicons-groups', null);
    }

    private function sectionExists($controller) {
        return file_exists(__DIR__ . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . $controller . ".php");
    }

    public function init() {
        $section = $this->defaultSection;
        if (isset($_GET['section'])) {
            $section = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$this->sectionExists($section)) {
                wp_die("La página solicitada no existe", "Error");
            }
        }
        $controller = new $section;
        $action = $controller->defaultAction;

        if (isset($_GET['action'])) {
            $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $controller->$action();

        if ($this->debug) {
            echo '<hr/>';
            echo '<h2>DEBUG</h2>';
            echo '<ul>';
            foreach ($this->getLogs() as $log) {
                echo "<li>$log</li>";
            }
            echo '</ul>';
        }
    }

}

//Incluye las clases basicas
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
    sputnik::includeDir('widgets');
    $widgets = sputnik::getFiles('widgets');
    foreach ($widgets as $widget) {
        if ($widget == 'index.html') {
            continue;
        }
        register_widget(basename($widget, '.php'));
    }
});

register_activation_hook(__FILE__, ['sputnik', 'install']);
register_deactivation_hook(__FILE__, ['sputnik', 'uninstall']);
