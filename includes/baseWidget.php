<?php
/**
 * Description of baseWidget
 *
 * @author Isan
 */
class baseWidget extends \WP_Widget {

    public $defaultAction = "index";
    public $name = false;
    public $description = '';

    public function __construct() {
        $widgetOptions = [
            'classname' => __CLASS__,
            'description' => $this->description,
        ];

        parent::__construct(strtolower(__CLASS__), $this->name ?? __CLASS__, $widgetOptions);
    }

    public function widget($args, $instance) {
        $action = isset($_GET['action']) ? $_GET['action'] : $this->defaultAction;
        $action = 'action' . ucfirst($action);

        if (method_exists($this, $action)) {
            echo $this->$action();
        } else {
            throw new Exception("Not Found", 404);
        }
    }

    public function renderPartial($view, $args = []) {
        $args[] = $this;
        extract($args, EXTR_SKIP);
        include $this->getViewDir($view);
    }

    public function getViewDir($view) {
        global $sputnik;
         $fullPath = $sputnik->basePath() . DIRECTORY_SEPARATOR .'..' .DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
        if (strpos($view, ".")) {
            $fullPath .= str_replace(".", DIRECTORY_SEPARATOR, $view) . ".php";
        } else {
            $fullPath .= str_replace('Widget', '', get_class($this)) . DIRECTORY_SEPARATOR . "$view.php";
        }
        if (file_exists($fullPath)) {
            return $fullPath;
        } else {
            wp_die("La vista $view no existe");
        }
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        echo Html::label('Inserte el titulo', 'title');
        echo Html::textInput($this->get_field_name('title'), $title, ['id' => $this->get_field_id('title')]);
    }

    public function update($new_instance, $old_instance): array {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}
