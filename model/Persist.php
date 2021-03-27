<?php

require_once "autoload.php";

abstract class Persist extends Model {
    public static abstract function get_tableName();
    protected static abstract function get_FKName();
    protected abstract function get_object_map();
    protected static abstract function get_instance($data);
    protected abstract function get_id();
    protected abstract function set_id(string $id);
    protected abstract function get_childs();

    public static function get_by($col, $val) {
        $sql = "SELECT * FROM " .  static::get_tableName()  ." WHERE " . $col . "=:" . $col;
        $params = array($col=>$val);
        return self::get_one($sql, $params);
    }

    public static function get_by_id($id) {
        return self::get_by("ID", $id);
    }

    public static function get_all($object=null): array {
        $sql = "SELECT * FROM " . static::get_tableName();
        $params = null;

        if ($object != null) {
            $fkName = $object->get_fKName();
            $sql = $sql . " WHERE $fkName=:$fkName";
            $params = array($fkName=>$object->get_id());
        }
        return self::get_many($sql, $params);
    }

    public static function get_count($object=null): int {
        $sql = "SELECT COUNT(*) AS total FROM " . static::get_tableName();
        $params = null;
        if ($object != null) {
            $fkName = $object->get_fKName();
            $sql = $sql . " WHERE $fkName=:$fkName";
            $params = array($fkName=>$object->get_id());
        }
        $data = self::execute($sql, $params)->fetch();

        return $data["total"];
    }

    public function insert() {
        $map = $this->get_object_map();
        $table = $this->get_tableName();
        $colsNames =  join(", ", array_keys($map));
        $colsPH = join(", ", array_map(function($key) {return ":" . $key;}, array_keys($map)));

        $sql = "INSERT INTO $table($colsNames) VALUES ($colsPH)";

        self::execute($sql, $map);
        $this->set_id(self::lastInsertId());
    }

    public function update() {
        $map = $this->get_object_map();
        $table = $this->get_tableName();

        //utilisé pour filtrer $map et ne pas retenir ces éléments
        $dontKeep = function($key) {return !in_array($key, array("ID", "CreatedAt", "RegisteredAt"));};
        $keys = array_filter(array_keys($map), $dontKeep);

        $setCols = join(", ", array_map(function($key){return "$key=:" . $key;}, array_keys($keys)));
        $sql = "UPDATE $table SET $setCols WHERE ID=:ID";

        self::execute($sql, $map);
    }

    public function delete() {
        foreach ($this->get_childs() as $child) {
            $child->delete();
        }
        $sql = "DELETE FROM " . $this->get_tableName() . " WHERE ID=:ID";
        $params = array("ID"=>$this->get_id());
        $this->execute($sql, $params);
    }

    protected static function get_one($sql, $params) {
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return static::get_instance($data);
        }
    }

    protected static function get_many($sql, $params): array {
        $query = self::execute($sql, $params);
        $datas = $query->fetchAll();

        $objects = array();
        $prev = null;
        foreach ($datas as $data) {
            $rec = static::get_instance($data);
            if (self::class instanceof MoveIFace) {
                if ($prev !== null) {
                    $prev->set_next($rec);
                }
                $rec->set_prev($prev);
                $rec->set_next(null);
                $prev = $rec;
            }
            array_push($objects, $rec);
        }
        return $objects;
    }
}