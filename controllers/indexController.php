<?php
/**
 * Controladora por defecto para administración del plugin
 *
 * @author Isan
 */
class indexController extends baseController {

    public function actionIndex() {
        $this->render('index');
    }
    
    public function actionView(){
        echo "Hello World";
    }

}
