<?php

require_once "autoload.php";

abstract class Persist extends CachedGet {
    public static abstract function get_tableName();
    protected static abstract function get_FKName();
    protected abstract function get_object_map();
    protected static abstract function get_instance($data);
    protected abstract function get_id();
    protected abstract function set_id(string $id);
    protected abstract function cascade_delete();


    public static function sql_select($col, $val) {
        $sql = "SELECT * FROM " .  static::get_tableName()  ." WHERE " . $col . "=:" . $col;
        $params = array($col=>$val);

        return self::cache_get_one(array(static::class, "sql_get_one"), $sql, $params);
    }

    public static function sql_select_all($col=null, $val=null): array {
        $sql = "SELECT * FROM " . static::get_tableName();

        $params = null;
        if ($col != null && $val != null) {
            $sql = $sql . " WHERE $col=:$col";
            $params = array($col=>$val);
        }
        return self::cache_get_many(self::sql_get_many($sql, $params));
    }

    public static function sql_get_count($col=null, $val=null): int {
        $sql = "SELECT COUNT(*) AS total FROM " . static::get_tableName();

        $params = null;
        if ($col != null && $val != null) {
            $sql = $sql . " WHERE $col=:$col";
            $params = array($col=>$val);
        }
        $data = self::execute($sql, $params)->fetch();
        return $data["total"];
    }

    public function sql_insert() {
        $map = $this->get_object_map();
        $table = $this->get_tableName();
        $colsNames =  join(", ", array_keys($map));
        $colsPH = join(", ", array_map(function($key) {return ":" . $key;}, array_keys($map)));

        $sql = "INSERT INTO $table($colsNames) VALUES ($colsPH)";

        self::execute($sql, $map);
        $this->set_id(self::lastInsertId());
    }

    public function sql_update() {
        $map = $this->get_object_map();
        $table = $this->get_tableName();

        //utilisé pour filtrer $map et ne pas retenir ces éléments
        $dontKeep = function($key) {return !in_array($key, array("ID", "CreatedAt", "RegisteredAt"));};
        $keys = array_filter(array_keys($map), $dontKeep);

        $setCols = join(", ", array_map(function($key){return "$key=:" . $key;}, array_keys($keys)));
        $sql = "UPDATE $table SET $setCols WHERE ID=:ID";

        self::execute($sql, $map);
    }

    public function sql_delete() {
        foreach ($this->cascade_delete() as $child) {
            $child->delete();
        }
        $sql = "DELETE FROM " . $this->get_tableName() . " WHERE ID=:ID";
        $params = array("ID"=>$this->get_id());
        $this->execute($sql, $params);
    }

    protected static function sql_get_one($sql, $params) {
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return static::get_instance($data);
        }
    }

    protected static function sql_get_many($sql, $params): array {
        $query = self::execute($sql, $params);
        $datas = $query->fetchAll();

        $objects = array();
        foreach ($datas as $data) {
            $rec = static::get_instance($data);
            array_push($objects, $rec);
        }
        return $objects;
    }

}