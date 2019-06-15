<?php

/**
 * Description of LoginForm
 *
 * @author Isan
 */
class LoginForm extends baseModel {

    public function attributes() {
        return [
            'email' => 'Email',
            'pass' => 'Contraseña',
        ];
    }

    public function rules(): array {
        return [
                ['required', ['email', 'pass']],
                ['email', ['email']],
        ];
    }

    public static function cifrar($pass) {
        //Pendiente utilizar algoritmo de hash compatible con sistema
        return $pass;
    }

    public static function validPass($pass, $hash) {
        if ($hash === LoginForm::cifrar($pass)) {
            return true;
        } else {
            sleep(3); //Prevenir BRUTEFORCE
            return false;
        }
    }

    public function validUser() {
        $usuario = (new Usuario())->findByAttribute('email', $this->email);

        if ($usuario && LoginForm::validPass($this->pass, $usuario->pass)) {
            return $usuario;
        } else {
            $this->errors[] = 'Acceso incorrecto';
            return false;
        }
    }

}
