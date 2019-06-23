<?php

/**
 * Dummmy test widget
 *
 * @author Isan
 */
class sputnikWidget extends baseWidget {
    
    public $name = 'Widget De Pruebas';
    public $description = '';

  
    public function actionIndex(){
        return $this->renderPartial('index');
    }

}
