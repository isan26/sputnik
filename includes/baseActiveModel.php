<?php

/**
 * Description of baseActiveModel
 *
 * @author Isan
 */
abstract class baseActiveModel extends baseModel {

    /**
     * Campo llave
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Cantidad de items total que genera la busqueda
     * @var int
     */
    public $count;

    /**
     * Crea la tabla con nombre completo, prefix+tabla
     * @var string
     */
    private $tableFullName = '';

    public function __construct() {
        global $wpdb;
        $this->data = array_fill_keys(array_keys($this->attributes()), null);
        $this->data[$this->primaryKey] = null;
        $this->tableFullName = $wpdb->prefix . static::tableName();
    }

    /**
     * Si el campo es nuevo
     */
    public function isNewRecord() {
        return empty($this->data[$this->primaryKey]);
    }

    /**
     * Inserta un nuevo item en la BD
     */
    private function insert() {
        global $wpdb;
        if ($wpdb->insert($this->tableFullName, $this->data)) {
            $this->data[$this->primaryKey] = $wpdb->insert_id;
        } else {
            $wpdb->print_error();
            return FALSE;
        }

        return $this;
    }

    private function update() {
        global $wpdb;
        if ($wpdb->update($this->tableFullName, $this->data, [$this->primaryKey => $this->data[$this->primaryKey]])) {
            $this->data[$this->primaryKey] = $wpdb->insert_id;
        } else {
            $wpdb->print_error();
            return FALSE;
        }
        return $this;
    }

    public function findOne($id) {
        return $this->findByAttribute($this->primaryKey, $id);
    }

    public function findByAttribute($attribute, $param) {
        global $wpdb;
        $attribute = esc_sql($attribute);
        $param = esc_sql($param);
        $this->data = $wpdb->get_row("SELECT * FROM {$this->tableFullName} WHERE {$attribute} = '{$param}' LIMIT 1", ARRAY_A);
        if (empty($this->data)) {
            return false;
        }
        return $this;
    }

    /**
     * Buscar varios elemento en la tabla
     * @global type $wpdb
     * @param String $where
     * @param String $orderBy
     * @param int $start Definir limite en la query
     * @param int $end
     * @return array
     */
    public function findAll(string $where = "", $orderBy = "", int $start = 0, int $end = 1000) {
        global $wpdb;
        $query = "SELECT * FROM {$this->tableFullName} ";
        $queryCount = "SELECT COUNT(*) FROM {$this->tableFullName}";

        if ($where) {
            $query .= $where;
            $queryCount .= $where;
        }

        if ($orderBy) {
            $query .= $orderBy;
        }

        $this->count = $wpdb->get_var($queryCount);

        $query .= " LIMIT {$start},{$end}";

        return $wpdb->get_results($query, ARRAY_A);
    }

    public function deleteAll($where = '') {
        $query = "DELETE FROM {$this->tableFullName} WHERE {$where}";
        return $this->query($query);
    }

    public function query($query) {
        global $wpdb;
        return $wpdb->query($query);
    }

    public function delete() {
        global $wpdb;
        return $wpdb->delete($this->tableFullName, [$this->primaryKey => $this->data[$this->primaryKey]]);
    }

    /**
     * Guarda el objeto luego de validar
     * @return $this
     */
    public function save() {
        if (!$this->validate()) {
            return false;
        }
        if ($this->isNewRecord()) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    private function isValid($params, $expression) {
        global $sputnik;
        foreach ($params as $param) {
            $sputnik->addLog("Validando $param");
            $sputnik->addLog("datos de $param: " . $this->data[$param]);
            if (!preg_match($expression, $this->data[$param])) {
                $this->errors[] = "Valor no válido para " . $this->getAttributeName($param);
                return false;
            }
        }
        return true;
    }

    private function isEmpty($params) {
        foreach ($params as $param) {
            if (!$this->data[$param]) {
                $this->errors[] = "El Campo " . $this->getAttributeLabel($param) . " no puede estar vacío.";
                return false;
            }
        }
        return true;
    }

    /**
     * Nombre de la tabla(sin el prefijo de wordpress)
     */
    public static abstract function tableName();
}
