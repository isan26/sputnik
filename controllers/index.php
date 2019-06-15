<?php
/**
 * Controladora por defecto para administración del plugin
 *
 * @author Isan
 */
class index extends baseController {

    public function actionIndex() {
        $this->render('index');
    }

}
