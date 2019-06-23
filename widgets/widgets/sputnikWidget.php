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
        return __DIR__;
    }

}
