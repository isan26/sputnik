<?php

/**
 * Description of Captcha
 *
 * @author Isan
 */
require_once __DIR__ . '/../libs/captcha.php';

class CaptchaWidget {

    public static function generateCode() {
        $config = ['font' => 'Roboto-Regular.ttf', 'dir' => __DIR__ . "/../font/"];
        $captcha = new captcha($config);
        return $captcha->CaptchaSecurityImages(120, 40, 4);
    }

    public static function widget($model,$attribute) {
        $img = self::generateCode();
        echo "<div class='captcha'>";
        echo "<div class='captcha-img'>";
        echo "<img src='data:image/jpeg;base64,{$img}' />";
        echo "</div>";
        echo "<div class='captcha-input'>";
        echo Html::activeTextInput($model,$attribute);
        echo "</div>";
        echo "</div>";
    }

}
