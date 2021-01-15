<?php

require_once "framework/Model.php";
require_once "DateTrait.php";

/*
 * Le résultat d'un get_by_id est mis en cache dans Class::$data_cache et retourné lors d'appels subséquents.
 * */

/* Cf. "late static binding" pour l'utilisation de 'static' plutôt que 'self'. Avec 'self', déterminé à la
        "compilation", $data_cache serait l'attribut de CachedGet. Avec 'static' c'est la classe dont $this est une instance.
        https://stackoverflow.com/questions/1912902/what-exactly-are-late-static-bindings-in-php
     */

abstract class CachedGet extends Model {
    protected static array $data_cache = [];

    protected static abstract function get_instance($data);

    /* si $id n'est pas en cache, requêtre en DB, construction de l'instance, on la mets en cache et la retourne*/
    public static function get_by_id(string $id) {
        if (!self::is_in_cache($id)) {
            $table_name = strtolower(static::class);
            $sql =
                "SELECT * 
             FROM `$table_name` 
             WHERE ID=:id";

            $params = array("id" => $id);
            $query = self::execute($sql, $params);
            $data = $query->fetch();

            if ($query->rowCount() == 0) {
                static::$data_cache[$id] = null;
            } else {
                self::add_instance_to_cache(static::get_instance($data));
            }
        }
        return static::get_cached($id);
    }

    protected static function add_instance_to_cache($instance) {
        static::$data_cache[$instance->get_id()] = $instance;
    }

    /* Ajoute un élément quelconque au cache */
    protected static function add_to_cache(string $key, $value) {
        static::$data_cache[$key] = $value;
    }

    protected static function is_in_cache($key): bool {
        return array_key_exists($key, static::$data_cache);
    }

    /* Cf. https://www.php.net/manual/fr/migration70.new-features.php#migration70.new-features.null-coalesce-op
    */
    protected static function get_cached($key) {
        return static::$data_cache[$key] ?? null;
    }
}