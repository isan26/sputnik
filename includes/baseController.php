<?php

/**
 * Description of baseController
 *
 * @author Isan
 */
class baseController {
    
    public $defaultAction  = 'index';

    /**
     * Renderiza una vista
     * @param string $view Nombre del fichero
     * @param array $args Argumentos a pasar a la vista
     */
    public function render($view, $args = []) {
        if (isset($_SESSION['messages'])) {
            foreach ($_SESSION['messages'] as $message => $type) {
                $this->messageRender($message, $type);
            }
            $_SESSION['messages'] = [];
        }
        $this->renderPartial($view, $args);
    }

    public function renderPartial($view, $args = []) {
        $args[] = $this;
        extract($args, EXTR_SKIP);
        include $this->getViewDir($view);
    }

    public function redirect($url, $partial = true) {
        $fullUrl = $url;
        if ($partial) {
            $fullUrl = $_SERVER['script_name'] . $url;
        }
        wp_redirect($fullUrl);
        exit();
    }

    public function getViewDir($view) {
        global $sputnik;
        $fullPath = $sputnik->basePath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "$view.php";
        if (file_exists($fullPath)) {
            return $fullPath;
        } else {
            wp_die("La vista $view no existe");
        }
    }

    public function messageSuccess($message) {
        $this->messageSet($message, "updated notice");
    }

    public function messageError($message) {
        $this->messageSet($message, "error notice");
    }

    public function messageWarning($message) {
        $this->messageSet($message, "update-nag notice");
    }

    private function messageSet($message, $type) {
        $messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : [];
        $messages[$message] = $type;
        $_SESSION['messages'] = $messages;
    }

    private function messageRender($message, $type = 'updated notice') {
        echo "<div class='{$type}'>";
        echo "<h3>{$message}</h3>";
        echo "</div>";
    }

    public function __call($name, $arguments) {
        $actionName = "action". ucfirst($name);
        if (method_exists($this,$actionName)) {
            return $this->$actionName();
        } else {
            wp_die("La página solicitada no existe.");
        }
    }

}
