<?php

/**
 * @author Dominik Ryńko <http://rynko.pl/>
 * @author Simon Jarvis
 * @version 1.0.0
 * @Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.ph
 * @Link: http://rynko.pl/captcha-class-php-czy-jestes-czlowiekiem
 */
class captcha {

    /**
     * Property with config data
     * @var array
     */
    public $config;

    /**
     * Property with default folder name with fonts
     * @var string
     */
    public $defaultFont = 'font';

    /**
     * @param $config
     */
    public function __construct($config) {
        if (empty($config['font'])) {
            throw new \Exception('Font name cannot be empty');
        } else {
            $config['font'] = explode('.', $config['font']);
            $ext = end($config['font']);

            if ($ext !== 'ttf') {
                throw new \Exception('Font\'s extension mu be .ttf');
            }

            $this->config['fontName'] = $config['font'][0] . '.' . $config['font'][1];
        }

        if (isset($config['dir']) && is_dir($config['dir'])) {
            $this->config['dir'] = $config['dir'];
        } else {
            $this->config['dir'] = $this->config['defaultDir'];
        }

        $this->config['font'] = $this->config['dir'] . '/' . $this->config['fontName'];
    }

    /**
     * @param  int $characters
     * @return string $code
     */
    public function generateCode($characters) {
        // List all possible characters, similar looking characters and vowels have been removed
        $possible = '23456789bcdfghjkmnpqrstvwxyz';
        $code = '';

        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $characters
     */
    public function CaptchaSecurityImages($width = 120, $height = 40, $characters = 6) {
        $code = $this->generateCode($characters);
        $_SESSION['security_code'] = $code;

        // Font size will be 75% of the image height
        $fontSize = $height * 0.45;
        $image = imagecreate($width, $height);
        // Set the colours

        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 46, 118, 126);
        $noiseColor = imagecolorallocate($image, 118, 173, 201);

        // Generate random dots in background
        for ($i = 0; $i < ($width * $height) / 10; $i++) {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noiseColor);
        }

        // Generate random lines in background
        for ($i = 0; $i < ($width * $height) / 310; $i++) {
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noiseColor);
        }

        // Create textbox and add text
        $textbox = imagettfbbox($fontSize, 0, $this->config['font'], $code);
        $x = ($width - $textbox[4]) / 2;
        $y = ($height - $textbox[5]) / 2;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $this->config['font'], $code);

        // Output captcha image to browser
        ob_start();
        imagejpeg($image);
        imagedestroy($image);
        $data = ob_get_contents();
        ob_end_clean();
        
        return base64_encode($data);
        
    }

}
