<?php

require_once "autoload.php";

class SimpleQuery {
    private string $tableName;
    private string $select_string;
    private string $from_string;
    private string $where_string;
    private string $order_string;
    private ?array $params;


    private static function new_from(SimpleQuery $query) {
        return new SimpleQuery($query->tableName, $query->select_string, $query->from_string, $query->where_string,
            $query->order_string, $query->params);
    }


    public function __construct(string $tableName, string $select="", string $from="", string $where="", string $order="", array $params=[]) {
        $this->tableName = $tableName;
        $this->select_string = $select;
        $this->from_string = $from;
        $this->where_string = $where;
        $this->order_string = $order;
        $this->params = $params;
    }


    public function select(array $columns_array=null): SimpleQuery {
        $columns = empty($columns_array) ? "*" : join(", ", array_values($columns_array));

        $this->select_string = "SELECT ".$columns;
        $this->from_string = "FROM ". $this->tableName;

        return SimpleQuery::new_from($this);
    }


    public function from(array $tablesNames=null) {
        $this->from_string = $this->tableName;

        if (!empty($tablesNames)) {
            $names = [];
            foreach($tablesNames as $name => $short) {
                $names[] = "$name AS $short";
            }
            $this->from_string = "FROM " . join(", ", $names);
        }
        return SimpleQuery::new_from($this);
    }


    public function where(array $where_array): SimpleQuery {
        $columns = array_keys($where_array);
        $interpolate = array_map(function($col){return "$col=:$col";}, $columns);
        $where_args = join(" AND ", $interpolate);

        $this->where_string = "WHERE " . $where_args;
        $this->params = array_merge($where_array);

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

    public function fetch_one() {
        return $this->construct_query();
    }

    public function fetch_many() {
        return $this->construct_query();
    }

    private function construct_query(): array {
        $sql = join(" ", array($this->select_string, $this->from_string, $this->where_string, $this->order_string));

        return array($sql, $this->params);
    }

}

$sp = new SimpleQuery("test");
list($sql, $params) = $sp->select()->where(["ID" => "22"])->order_by(["Machin"=>"ASC", "Truc" => "DESC"])->fetch_many();
echo($sql);
echo("\n");
var_dump($params);
echo ("\n----------------\n");
list($sql, $params) = $sp->select()->where(["ID" => "22"])->fetch_many();
echo ($sql);
echo ("\n");
var_dump($params);
echo ("\n----------------\n");
list($sql, $params) = $sp->select(["ca.ID","ca.Title"])->from(["card"=>"ca", "board"=>"bo"])->where(["ca.ID" => "bo.BoardId", "bo.Title" => "sdgd"])->fetch_many();
echo ($sql);
echo ("\n");
var_dump($params);