<?php

/**
 * Clase base para los controladores REST,
 * Se encarga de inicializar la configuración por defecto y de registrar las acciones automaticamente.
 */
class baseRestController {

    //Default namespace para las acciones
    private $namespace;

    //Carga configuracion y registra las acciones
    public function init($namespace) {
        $this->namespace = $namespace;
        foreach (get_class_methods($this) as $function) {
            if (substr($function, 0, 6) == 'action') {
                $this->register($function);
            }
        }
    }

    /**
     * Configuraciones para las acciones
     * @return array
     */
    public function config() {
        return array();
    }

    //Carga la configuracion de la action y la registra.
    private function register($action) {
        //Route es el nombre de la funcion sin el prefijo action
        $route = substr($action, 6);
        $configs = $this->config();

        if (isset($configs[$action])) {
            $config = $configs[$action];
        } else {
            $config = [
                'methods' => WP_REST_Server::READABLE,
            ];
        }
        $config['callback'] = [$this, $action];
        return register_rest_route($this->namespace."/".get_class($this), $route, $config);
    }

}
