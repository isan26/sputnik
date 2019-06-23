<?php
/*
  Plugin Name: Sputnik
  Plugin URI: https://github.com/Isan26/sputnik
  Description: Sputnik mini-framework MVC for wordpress
  Author: Isan Rodriguez Trimiño
  Version: 0.1
  Author URI: mailto:isan1989@gmail.com
 */
spl_autoload_register(function($className) {
    $file = $className . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

require (__DIR__.'/init.php');

