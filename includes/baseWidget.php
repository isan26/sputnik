<?php

/**
 * Description of baseWidget
 *
 * @author Isan
 */
class baseWidget extends WP_Widget {

    public function renderPartial($view, $args = []) {
        $args[] = $this;
        extract($args, EXTR_SKIP);
        include $this->getViewDir($view);
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
