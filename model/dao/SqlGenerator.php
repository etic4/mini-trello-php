<?php


class SqlGenerator {
    private string $tableName;
    private string $select_string;
    private string $from_string;
    private string $where_string;
    private string $order_string;
    private string $join_string;
    private string $insert_string;
    private string $update_string;
    private string $set_string;
    private string $delete_string;
    private string $sql = "";
    private ?array $params;


    private static function new(SqlGenerator $query): SqlGenerator {
        return new SqlGenerator($query->tableName, $query->select_string, $query->from_string, $query->where_string,
            $query->order_string, $query->join_string, $query->insert_string, $query->update_string, $query->set_string, $query->delete_string, $query->params);
    }

    public function __construct(string $tableName="", string $select="", string $from="", string $where="", string $order="",
        $join="", $insert="", $update="", $set="", $delete="", array $params=[]) {
        $this->tableName = $tableName;
        $this->select_string = $select;
        $this->from_string = $from;
        $this->where_string = $where;
        $this->order_string = $order;
        $this->join_string = $join;
        $this->insert_string = $insert;
        $this->update_string = $update;
        $this->set_string = $set;
        $this->delete_string = $delete;
        $this->params = $params;
    }


    public function select(array $columns=null, $distinct=false): SqlGenerator {
        $cols = empty($columns) ? "*" : join(", ", array_values($columns));
        $distinct = $distinct ? " DISTINCT " : " ";

        $this->select_string = "SELECT$distinct".$cols;
        $this->from_string = "FROM " . $this->tableName;

        return SqlGenerator::new($this);
    }

    public function insert(array $object_map) {
        $colsNames =  join(", ", array_keys($object_map));
        $cols_place_holders = join(", ", array_map(
            function($key) {
                $place_older = $this->get_place_holder($key);
                return ":$place_older";},
            array_keys($object_map)));

        $this->insert_string = "INSERT INTO $this->tableName($colsNames) VALUES ($cols_place_holders)";
        $this->merge_params($object_map);

        return SqlGenerator::new($this);
    }

    public function update(array $object_map): SqlGenerator {
        //ne pas retenir ces éléments (en cas d'update d'une ligne, quand $object_map est obtenu auprès d'instance)
        $dontKeep = function($key) {return !in_array($key, array("ID", "CreatedAt", "RegisteredAt"));};

        $keys = array_filter(array_keys($object_map), $dontKeep);

        $setCols = join(", ", array_map(
                function($key){
                    $place_older = $this->get_place_holder($key);
                    return "$key=:$place_older";},
                $keys)
        );

        $this->update_string = "UPDATE $this->tableName SET $setCols";
        $this->merge_params($object_map);

        return SqlGenerator::new($this);
    }

    public function set(array $object_map): SqlGenerator {
        //ne pas retenir ces éléments (en cas d'update d'une ligne, quand $object_map est obtenu auprès d'instance)
        $dontKeep = function($key) {return !in_array($key, array("ID", "CreatedAt", "RegisteredAt"));};

        $keys = array_filter(array_keys($object_map), $dontKeep);

        $setCols = join(", ", array_map(
                function($key){
                    $place_older = $this->get_place_holder($key);
                    return "$key=:$place_older";},
                $keys)
        );

        $this->update_string = "UPDATE $this->tableName SET $setCols";
        $this->merge_params($object_map);

        return SqlGenerator::new($this);
    }

    public function delete(): object {
        $this->delete_string = "DELETE FROM " . $this->tableName;

        return SqlGenerator::new($this);
    }

    public function join(array $tablesNames): SqlGenerator {
        if (!empty($tablesNames)) {
            $this->from_string = "FROM " . join(", ", $tablesNames);
        }
        return SqlGenerator::new($this);
    }

    public function on(array $joins_list): SqlGenerator {
        $to_join = array_map(function($col1, $col2){return "$col1=$col2";}, array_keys($joins_list), $joins_list);
        $this->join_string = join(", ", $to_join);
        return SqlGenerator::new($this);
    }


    public function where(array $where_array, array $operators=null): SqlGenerator {
        $columns = array_keys($where_array);

        if (is_null($operators)) {
            $operators = array_fill(0, count($columns), "=");
        }

        $interpolate = array_map(
            function($col, $op){
                $place_older = $this->get_place_holder($col);
                return "$col$op:$place_older";
            },
            $columns, $operators);

        $where_args = join(" AND ", $interpolate);

        $this->where_string = "WHERE " . $where_args;
        $this->merge_params($this->dot_to_underscore($where_array));

        return SqlGenerator::new($this);
    }

    public function order_by(array $order_array): SqlGenerator {
        $args = [];
        foreach ($order_array as $col => $order) {
            $args[] = "$col $order";
        }
        $this->order_string = "ORDER BY " . join(", ", $args);

        return SqlGenerator::new($this);
    }

    public function count(): SqlGenerator {
        $this->select_string = "SELECT COUNT(*) as total";
        return SqlGenerator::new($this);
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
            $elements = array_merge($elements, [$this->from_string, $this->set_string, $this->where_string, $this->join_string, $this->order_string]);
            $this->sql = join(" ", array_filter($elements));
        }
        return $this->sql;
    }

    public function get_params(): array {
        return $this->params;
    }

    public function get(): array {
        return array($this->get_sql(), $this->params);
    }

    // ajoute ces paramètres aux précédents
    private function merge_params(array $params) {
        $params = $this->dot_to_underscore($params);
        $this->params = array_merge($this->params, $params);
    }

    // remplacer les "." par un "_".
    private function get_place_holder($value) {
        $ph = str_replace("`", "", $value);
        $ph = str_replace(".", "_", $ph);
        return $ph;
    }

    // remplacer les "." par des "_" dans les paramètres pour l'interpolation (nécessaire en cas de join)
    private function dot_to_underscore($params) {
        $result = [];

        foreach ($params as $key => $val) {
            $new_k = $this->get_place_holder($key);
            $result[$new_k] = $val;
        }
        return $result;
    }
}
