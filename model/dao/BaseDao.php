<?php

require_once "autoload.php";

abstract class BaseDao extends CachedGet {
    protected const PkName = "ID";

    public static function get_by(array $params) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select()->where($params)->get();

        return self::get_one($sql, $params);
    }

    public static function get_by_id(string $id) {
        return self::get_by([static::PkName => $id]);
    }

    public static function get_all(array $params=null) {
        $sql = new SqlGenerator(static::tableName);

        $sql = $sql->select();
        if (!is_null($params)) {
            $sql = $sql->where($params);
        }
        list($sql, $params) = $sql->get();

        return self::get_many($sql, $params);
    }

    public static function insert($object) {
        $map = static::get_object_map($object);
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->insert($map)->get();
        self::execute($sql, $params);

        if (static::PkName != null) {
            $object->set_id(self::lastInsertId());
        }
        return $object;
    }

    public static function update($object) {
        $map = static::get_object_map($object);
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->update($map)->where([static::PkName => $object->get_id()])->get();

        self::execute($sql, $params);
    }

    protected static function delete_one($object) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->delete()->where([static::PkName => $object->get_id()])->get();
        self::execute($sql, $params);

    }

    public static function delete_all(array $params) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->delete()->where($params)->get();
        self::execute($sql, $params);
    }

    public static function is_unique($col_name, $value): bool {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select()->where([$col_name => $value])->count()->get();
        return self::count($sql, $params) == 0;
    }

    // --- validation ---

    public static function validate_title($object, $update=false): array {
        $valid = (new TitleValidation(static::class))->validate($object, $update);
        return $valid->get_errors();
    }

    // En cas de validation d'un update, récupérer la ligne en db sans la mettre en cache
    public static function title_has_changed($object): bool {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select()->where(["ID" => $object->get_id()])->get();
        $stored = self::get_one($sql, $params, $cache=false);

        return $stored->get_title() != $object->get_title();
    }


    // --- /validation ---


    // Ne cache pas le résultat si $cache == true ou PkName == null
    protected static function get_one($sql, $params, $cache=true) {
        $key = self::get_key_for($params);

        if (!$cache || !self::is_in_cache($key)) {
            $query = self::execute($sql, $params);
            $data = $query->fetch();

            $result = $query->rowCount() != 0 ? static::from_query($data) : null;

            // static::PkName == null dans les tables de jointure -> on cache pas
            if (!$cache || static::PkName == null) {
                return $result;
            }

            self::cache_result($result, $key);

            // au cas où requête sur user.Mail (ou autre chose ?)
            if (isset($result) && count($params) == 1 && !array_key_exists("ID", $params)) {
                $id_key = self::get_key_for([static::PkName => $result->get_id()]);
                self::cache_result($result, $id_key);
            }
        }
        return self::get_cached($key);
    }

    protected static function get_many($sql, $params, callable $constructor=null): array {
        $query = self::execute($sql, $params);
        $datas = $query->fetchAll();

        if (is_null($constructor)) {
            $constructor = fn($data) => static::from_query($data);
        }

        $instances = [];

        foreach ($datas as $data) {
            $inst = $constructor($data);

            // static::PkName == null dans tables de jointure -> on met pas en cache
            if (static::PkName != null) {
                // mise en cache sous 'static::PkName' ("ID" en principe)
                $key = self::get_key_for([static::PkName => $inst->get_id()]);
                self::cache_result($inst, $key);
            }

            $instances[] = $inst;
        }
        return $instances;
    }

    protected static function count($sql, $params): int {
        return (int) self::execute($sql, $params)->fetch()["total"];
    }

}