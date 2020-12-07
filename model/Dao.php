<?php

require_once "framework/Model.php";

abstract class Dao extends Model {
    protected  abstract function prepare_insert($object);
    protected  abstract function prepare_update($object);
    protected abstract function get_instance($data);
    protected function get_tableName() {
        return $this->tableName;
    }

    public function get_by_id($id) {
        $sql = "SELECT * FROM ". $this->get_tableName() . " WHERE ID=:id";
        $query = $this->execute($sql, array("id"=>$id));

        return $this->fetch_one_and_get_instance($query);
    }

    public function insert($object) {
        $prepared = $this->prepare_insert($object);
        $this->execute($prepared["sql"], $prepared["params"]);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update($object) {
        $prepared = $this->prepare_update($object);
        $this->execute($prepared["sql"], $prepared["params"]);
    }

    public function delete($object) {
        $sql = "DELETE FROM " . $this->get_tableName() . " WHERE ID = :id";
        $params = array("id"=>$object->id);
        $this->execute($sql, $params);
    }

    protected function get_many($sql, $params): array {
        $query = $this->execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            array_push($objects, $this->get_instance($rec));
        }
        return $objects;
    }

    protected function fetch_one_and_get_instance($query) {
        $data = $query->fetch();
        return $this->get_instance($data);
    }

    protected function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    protected function php_date($sqlDate): ?DateTime {
        try {
            return new DateTime($sqlDate);
        } catch (Exception $e) {
            print("Erreur lors de la conversion de la date: " . $sqlDate);
        }
    }
}
