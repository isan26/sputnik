<?php
class sputnik {

    CONST TOINCLUDE = [
        'helpers', //Clases con helpers para HTML
        'models', //Modelos(ORM)
        'backend/controllers', //Controladoras
        'rest', //Controladoras RESTFULL
    ];

    //Debug Mode?
    public $debug = true;
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
        if (get_option('sputnik_debug', NULL) === NULL) { //Si null es pq es la primera vez que se instala el plugin
            add_option('sputnik_debug', TRUE);
        }
        //CREAR LAS TABLAS EN LA BD
        global $wpdb;

        $table_usuario = <<<USUARIO
CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}usuario` (
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

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($table_usuario);
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
            file_put_contents(__DIR__ .DIRECTORY_SEPARATOR . "..". "/" . "debug.log", $log . "\n", FILE_APPEND);
        }
    }

    public function getLogs() {
        return $this->logs;
    }

    public static function getFiles($dir) {
        $files = scandir(__DIR__ . DIRECTORY_SEPARATOR . "..".DIRECTORY_SEPARATOR. $dir);
        unset($files[0]);
        unset($files[1]);
        return $files;
    }

    public static function includeDir($dir) {
        foreach (self::getFiles($dir) as $file) {
            if ($file == 'index.html') {
                continue;
            }
            require __DIR__ . DIRECTORY_SEPARATOR ."..". DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file;
        }
    }

    public function admin_menu() {
        add_menu_page(__('Sputnik', 'sputnik'), __('Sputnik', 'sputnik'), 'manage_options', 'sputnik', [$this, 'init'], 'dashicons-admin-generic', null);
    }

    private function sectionExists($controller) {
        return file_exists(__DIR__ . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR ."$controller.php");
    }

    public function init() {
        $section = $this->defaultSection."Controller";
        if (isset($_GET['section'])) {
            $section = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_SPECIAL_CHARS);
            $section.="Controller";
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