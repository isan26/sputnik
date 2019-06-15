<?php

/**
 * Description of Usuario
 *
 * @author Isan
 */
class Usuario extends baseActiveModel {

    public static function tableName() {
        return 'sputnik_usuario';
    }

    public function attributes() {
        return [
            'email' => 'Email',
            'nombre' => 'Nombre',
            'pass' => 'Pass',
            'token' => 'Token', //Para validar password
            'expire' => 'Expire', //Para expirar token
        ];
    }

    public function rules(): array {
        return [
                ['required', ['email']],
        ];
    }

}
