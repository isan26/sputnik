<?php

abstract class baseModel {

    /**
     * Variable para almacenar el contenido del item, key es nombre del campo, value valor a insertar
     * @var array
     */
    public $data = [];

    /**
     * Errores
     * @var array
     */
    public $errors = [];

    public function __construct() {
        $this->data = array_fill_keys(array_keys($this->attributes()), null);
    }

    /**
     * Util para llenar el modelo con lo que viene de POST o GET $model->load($_POST)
     * donde post contiene los nombre de los campos
     * @param array $data
     * @return boolean
     */
    public function load($data) {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $this->data[$name] = $value;
            }
            return true;
        }
        return false;
    }

    public function validate() {
        $this->errors = [];
        $valid = true;

        foreach ($this->rules() as $rule) {
            switch ($rule[0]) {
                case 'required': {
                        $valid = $this->isEmpty($rule[1]);
                    }
                    break;
                case 'string': continue; //Pendiente
                    break;
                case 'int': {
                        $valid = $this->isValid($rule[1], "/^\s*[+-]?\d+\s*$/");
                    }
                    break;
                case 'number': {
                        $valid = $this->isValid($rule[1], "/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/");
                    }
                    break;
                case 'email': {
                        $valid = $this->isValid($rule[1], "/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/");
                    }
                    break;
                case 'captcha' : {
                        $valid = $this->validCaptcha($rule[1]);
                    }
                    break;
                default: {
                        $this->errors[] = "El validador {$rule[0]} no existe";
                        return false;
                    }
                    break;
            }
        }
        return $valid;
    }

    private function isValid($params, $expression) {
        $valid = true;
        foreach ($params as $param) {
            if ($this->data[$param] <> '' && !preg_match($expression, $this->data[$param])) {
                $this->errors[] = "Valor no válido para " . $this->getAttributeLabel($param);
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Comprueba si un campo esta vacío
     * @param type $params
     * @return boolean
     */
    private function isEmpty($params) {
        foreach ($params as $param) {
            if ($this->data[$param] === '') {
                $this->errors[] = "El Campo " . $this->getAttributeLabel($param) . " no puede estar vacío.";
                return false;
            }
        }
        return true;
    }

    /**
     * Comprueba el valor de los campos contra un valor
     * @param type $params
     * @param string $value Valor contra el cual comprobar
     * @return boolean
     */
    private function isEqual($params, $value) {
        $valid = true;
        foreach ($params as $param) {
            if ($this->data[$param] !== $value) {
                $this->errors[] = "El valor de " . $this->getAttributeLabel($param) . " no es correcto.";
                return false;
            }
        }
    }

    private function validCaptcha($params) {
        $valid = true;
        foreach ($params as $param) {
            if (!isset($_SESSION['security_code']) || $this->data[$param] != $_SESSION['security_code']) {
                $this->errors[] = "El valor de " . $this->getAttributeLabel($param) . " no es correcto.";
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Attributos de la tabla, array asociativo
     * donde key(es nombre del campo)=>value(Valor a presentar) ej 'name'=>'Nombre del Cliente'
     */
    public abstract function attributes();

    public function getAttributeLabel($attribute) {
        $attributes = $this->attributes();
        if (isset($attributes[$attribute])) {
            return $attributes[$attribute];
        } else {
            global $sputnik;
            $sputnik->addLog("El campo {$attribute} no existe");
        }
    }

    public function formName() {
        $reflector = new ReflectionClass($this);

        return $reflector->getShortName();
    }

    /**
     * Validaciones:
     * required:
     * string:
     * int:
     * float:
     * email:
     * date:
     *
     * ejemplo [
     *       ['required',['campo1','campo2']]
     *       ['string',['campo1','campo2']]
     *       ['int',['edad']]
     * ]
     */
    public abstract function rules(): array;

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            wp_die("El campo $name no existe");
        }
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;
        } else {
            wp_die("El campo \"$name\" no existe");
        }
    }

}
