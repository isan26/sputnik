<?php

/**
 * Helper para crear una tabla de administacion clasica en wordpress
 *
 * @author Isan
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class HelperGrid extends WP_List_Table {

    public $data;
    public $columns;
    public $itemsPerPage = 10;
    public $totalItems;
    public $primaryKey = 'id';
    public $hiddenColumns = ['id'];
    public $sortableColumns = true;
    private $modelClass;

    public function render($model, $query = '') {
        $start = 0;

        if (isset($_GET['paged'])) {
            $paged = $_GET['paged'];
            $start = ($paged - 1) * $this->itemsPerPage;
        }

        $orderBy = '';

        if (isset($_GET['orderby']) && $_GET['orderby'] && preg_match("/^[\w.-]*$/", $_GET['orderby'])) {
            $orderBy = 'ORDER BY ' . '`' . $_GET['orderby'] . '`';
            if (isset($_GET['order']) && ($_GET['order'] == 'asc' || $_GET['order'] == 'desc')) {
                $orderBy .= ' ' . $_GET['order'];
            }
        }

        $this->data = $model->findAll($query, $orderBy, $start, $this->itemsPerPage);
        $this->columns = $model->attributes();
        $this->totalItems = $model->count;

        $this->modelClass = get_class($model);

        $this->display();
    }

    public function display() {
        $this->prepare_items();
        $url = $this->generateUrl().'&action=bulk';
        echo "<form method='post' action={$url}>";
        echo parent::display();
        echo "</form>";
    }

    public function get_bulk_actions() {
        return [
            'delete' => 'Eliminar',
        ];
    }

    function column_actions($item) {
        $argument = "&{$this->primaryKey}={$item[$this->primaryKey]}";

        $url = $this->generateUrl();
        return "<a href='{$url}&action=view$argument' >Ver</a> <a href='{$url}&action=update$argument' >Editar</a> <a class='btn-delete' href='{$url}&action=delete$argument'>Eliminar</a>";
    }

    public function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="bulk[]" value="%s" />', $item[$this->primaryKey]
        );
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $this->_column_headers = array($this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns());

        $this->set_pagination_args(array(
            'total_items' => $this->totalItems,
            'per_page' => $this->itemsPerPage
        ));

        $this->items = $this->table_data();
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox" />',
        ];
        $columns = array_merge($columns, $this->columns);
        $columns['actions'] = '';
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return $this->hiddenColumns;
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        $sortableColumns = [];

        if ($this->sortableColumns === true) {
            foreach ($this->columns as $column => $name) {
                $sortableColumns[$column] = [$column, false];
            }
        } else {
            if (is_array($this->sortableColumns)) {
                foreach ($this->sortableColumns as $key => $column) {
                    $sortableColumns[$column] = [$column, false];
                }
            }
        }
        return $sortableColumns;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data() {
        return $this->data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name) {

        if (isset($this->columns[$column_name])) {
            return $item[$column_name];
        } else {
            return print_r($item, true);
        }
    }

    private function generateUrl() {
        $urlString = 'admin.php?page=sputnik';
        if (isset($_GET['section'])) {
            $urlString .= '&section=' . filter_input(INPUT_GET, 'section', FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return admin_url($urlString);
    }

}
