<?php

//require_once "autoload.php";

class SimpleQuery {
    private string $tableName;
    private string $select_string;
    private string $from_string;
    private string $where_string;
    private string $order_string;

    private string $join_string;
    private string $insert_string;
    private string $update_string;
    private string $delete_string;
    private string $sql = "";
    private ?array $params;


    private static function new_from(SimpleQuery $query) {
        return new SimpleQuery($query->tableName, $query->select_string, $query->from_string, $query->where_string,
            $query->order_string, $query->join_string, $query->insert_string, $query->update_string, $query->delete_string, $query->params);
    }

    public function __construct(string $tableName, string $select="", string $from="", string $where="", string $order="",
        $join="", $insert="", $update="", $delete="", array $params=[]) {
        $this->tableName = $tableName;
        $this->select_string = $select;
        $this->from_string = $from;
        $this->where_string = $where;
        $this->order_string = $order;
        $this->join_string = $join;
        $this->insert_string = $insert;
        $this->update_string = $update;
        $this->delete_string = $delete;
        $this->params = $params;
    }


    public function select(array $columns_array=null): SimpleQuery {
        $columns = empty($columns_array) ? "*" : join(", ", array_values($columns_array));

        $this->select_string = "SELECT ".$columns;
        $this->from_string = "FROM " . $this->tableName;

        return SimpleQuery::new_from($this);
    }

    public function insert(array $object_map) {
        $colsNames =  join(", ", array_keys($object_map));
        $colsPH = join(", ", array_map(
            function($key) {
                $no_dot = $this->repl_dot($key);
                return ":$no_dot";},
            array_keys($object_map)));

        $this->insert_string = "INSERT INTO $this->tableName($colsNames) VALUES ($colsPH)";
        $this->merge_params($object_map);

        return SimpleQuery::new_from($this);
    }

    public function update(array $object_map): SimpleQuery {
        //ne pas retenir ces éléments (en cas d'update d'une ligne, quand $object_map est obtenu auprès d'instance)
        $dontKeep = function($key) {return !in_array($key, array("ID", "CreatedAt", "RegisteredAt"));};

        $keys = array_filter(array_keys($object_map), $dontKeep);

        $setCols = join(", ", array_map(
            function($key){
                $no_dot = $this->repl_dot($key);
                return "$key=:$no_dot";},
            $keys));

        $this->update_string = "UPDATE $this->tableName SET $setCols";
        $this->merge_params($object_map);

        return SimpleQuery::new_from($this);
    }

    public function delete() {
        $this->delete_string = "DELETE FROM " . $this->tableName;

        return SimpleQuery::new_from($this);
    }

    public function from(array $tablesNames) {
        if (!empty($tablesNames)) {
            $this->from_string = "FROM " . join(", ", $tablesNames);
        }
        return SimpleQuery::new_from($this);
    }

    public function join(array $joins_list) {
        $to_join = array_map(function($col1, $col2){return "$col1=$col2";}, array_keys($joins_list), $joins_list);
        $this->join_string = join(", ", $to_join);
        return SimpleQuery::new_from($this);
    }

    public function where(array $where_array): SimpleQuery {
        $columns = array_keys($where_array);

        // remplacer les "." par des "_" en cas de join
        $interpolate = array_map(
            function($col){
                $no_dot = $this->repl_dot($col);
                return "$col=:$no_dot";
            },
            $columns);

        $where_args = join(" AND ", $interpolate);

        $this->where_string = "WHERE " . $where_args;
        $this->merge_params($this->dot_to_underscore($where_array));

        return SimpleQuery::new_from($this);
    }

    public function order_by(array $order_array): SimpleQuery {
        $args = [];
        foreach ($order_array as $col => $order) {
            $args[] = "$col $order";
        }
        $this->order_string = "ORDER BY " . join(", ", $args);

        return SimpleQuery::new_from($this);
    }

    public function count(): SimpleQuery {
        $this->select_string = "SELECT COUNT(*)";
        return SimpleQuery::new_from($this);
    }

    private function process_join() {
        if (!empty($this->where_string) && !empty($this->join_string)) {
            $this->join_string = "AND " . $this->join_string;
        } else if (!empty($this->join_string)) {
            $this->join_string = "WHERE " . $this->join_string;
        }
    }

    public function get_sql(): string {
        if (empty($this->sql)) {
            $this->process_join();
            $elements = [$this->insert_string, $this->update_string, $this->delete_string, $this->select_string];
            $elements = array_merge($elements, [$this->from_string, $this->where_string, $this->join_string, $this->order_string]);
            $this->sql = join(" ", array_filter($elements));
        }
        return $this->sql;
    }

    public function get_params(): array {
        return $this->params;
    }

    public function get_preparable(): array {
        return array($this->get_sql(), $this->params);
    }

    // ajoute ces paramètres aux précédents
    private function merge_params(array $params) {
        $params = $this->dot_to_underscore($params);
        $this->params = array_merge($this->params, $params);
    }

    // remplacer les "." par un "_".
    private function repl_dot($value) {
        return str_replace(".", "_", $value);
    }

    // remplacer les "." par des "_" dans les paramètres pour l'interpolation (nécessaire en cas de join)
    private function dot_to_underscore($params) {
        $result = [];

        foreach ($params as $key => $val) {
            $new_k = $this->repl_dot($key);
            $result[$new_k] = $val;
        }
        return $result;
    }
}
