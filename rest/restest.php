<?php

/**
 * Prueba de  restful
 *
 * @author Isan
 */
class restest extends baseRestController {

    public function actionHello() {
        global $sputnik;
        $sputnik->addLog('Ejecutando Action HELLO');
        return [
            'message' => "Metal ist krieg!!!"
        ];
    }

    public function config() {
        return array();
    }

}
