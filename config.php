<?php

return [
    'params'=>[ //Parametros del plugin
        'debug'=>true
    ],
    'backend' => [
        'default'=>'controller.action', //Controladora action
        'icon'=>'', //Icono a mostrar, poder poner custon icom
        'name'=>'',//Testo a mostrar en la barra de navegacion
        'sublevels'=>[
            'controller.action'
        ]
    ],
    'oninstall' //Scripts de instalacion y desactivacion
];
