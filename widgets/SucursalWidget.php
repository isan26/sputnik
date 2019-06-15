<?php

/**
 * Widget para las Sucursales, lleva login y pass
 *
 * @author Isan
 */
class SucursalWidget extends baseWidget {

    public function __construct() {
        $widgetOptions = [
            'classname' => 'SucursalWidget',
            'description' => 'Panel de búsqueda para las sucursales',
        ];

        parent::__construct('sucursalwidget', "Widget para las sucursales", $widgetOptions);
    }

    public function login() {
        $model = new LoginForm();

        if (isset($_POST['LoginForm'])) {
            $model->load($_POST['LoginForm']);
            if ($model->validate() && $model->validUser()) {
                session_start();
                $_SESSION['user'] = $model->data;
            }
        }

        if (isset($_SESSION['user'])) {
            $this->renderPartial('solicitud', ['model' => new Solicitud(),'habilidades'=> acorecNomenclator::getListOf('habilidad')]);
            return true;
        }
        $this->renderPartial('loginform', ['model' => $model]);
    }

    public function logout() {
        session_destroy();
        echo "<h2>Su sesion ha sido terminada</h2>";
        $model = new LoginForm();
        $this->renderPartial('loginform', ['model' => $model]);
    }

    /**
     * Cambiar contraseña para usuario ya logeado
     */
    public function passChange() {
        $model = new PassChange();

        if (isset($_POST['PassChange'])) {
            $model->load($_POST['PassChange']);
            if ($model->validate() && $model->passEquals() && $model->validPass() && $model->doChange()) {
                echo "<p>Su contraseña ha sido actualizada</p>";
            }
        }

        $this->renderPartial('passchange', ['model' => $model]);
    }

    /**
     * Recuperar contraseña perdida vía email
     */
    public function passResset() {
        $model = new PassResset();

        if (isset($_GET['token'])) {
            $token = esc_sql($_GET['token']);
            if ($model->validToken($token)) {
                $passbytoken = new PassRessetByToken();
                $passbytoken->token = $token;
                $this->renderPartial('passbytoken', ['model' => $passbytoken]);
                exit();
            } else {
                wp_die("Página no encontrada");
            }
        }

        if (isset($_POST['PassRessetByToken'])) {
            $passbytoken = new PassRessetByToken();
            $passbytoken->load($_POST['PassRessetByToken']);
            if ($passbytoken->validate() && $passbytoken->passEquals()) {
                $passbytoken->doChange();
                echo "<p>Su contraseña ha sido actualizada</p>";
                $this->login();
            } else {
                $passbytoken->new = '';
                $passbytoken->re = '';
                $this->renderPartial('passbytoken', ['model' => $passbytoken]);
            }
            exit();
        }

        if (isset($_POST['PassResset'])) {
            $model->load($_POST['PassResset']);
            if ($model->validate() && $model->sendEmail()) {
                echo "<p>Se le ha enviado a su email un link de recuperación</p>";
                exit();
            }
        }
        $this->renderPartial('passresset', ['model' => $model]);
    }

    public function widget($args, $instance) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'login';

        switch ($action) {
            case 'login': {
                    $this->login();
                }
                break;
            case 'logout': {
                    $this->logout();
                }
                break;
            case 'passchange': {
                    $this->passChange();
                }
                break;
            case 'passresset': {
                    $this->passResset();
                }
                break;

            case 'captcha': {
                    $this->captcha();
                }
                break;

            default: {
                    echo "Página no encontrada";
                }
                break;
        }
    }

}
