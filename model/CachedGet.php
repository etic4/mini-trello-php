<?php

require_once "framework/Model.php";
require_once "DateTrait.php";

/*
 * Le résultat d'un get_by_id est mis en cache dans Class::$data_cache et retourné lors d'appels subséquents.
 *
 * Cf. "late static binding" pour l'utilisation de 'static' plutôt que 'self'. Avec 'self', déterminé à la
 *   "compilation", self::class retourne "CachedGet" alors que static::class retourne le nom de la classe dont $this est une instance.
 *   https://stackoverflow.com/questions/1912902/what-exactly-are-late-static-bindings-in-php
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
                self::add_null_for_id($id);
            } else {
                self::add_instance_to_cache(static::get_instance($data));
            }
        }
        return self::get_cached($id);
    }

    /* Crée un array avec comme clé static::class si n'existe pas*/
    private static function ensure_cache_for_class() {
        $class_name = static::class;
        if (!array_key_exists($class_name, self::$data_cache)) {
            self::$data_cache[$class_name] = [];
        }
    }

    protected static function add_instance_to_cache($instance) {
        self::ensure_cache_for_class();
        self::$data_cache[static::class][$instance->get_id()] = $instance;
    }

    protected static function add_null_for_id($id) {
        self::ensure_cache_for_class();
        self::$data_cache[static::class][$id] = null;
    }

    /* Ajoute un élément quelconque au cache */
    protected static function add_to_cache(string $key, $value) {
        self::ensure_cache_for_class();
        static::$data_cache[$key] = $value;
    }

    /* Check si $key existe en cache */
    protected static function is_in_cache($key): bool {
        $class_name = static::class;
        if (array_key_exists($class_name, self::$data_cache)) {
            return array_key_exists($key, self::$data_cache[$class_name]);
        }
        return false;
    }

    /* Cf. https://www.php.net/manual/fr/migration70.new-features.php#migration70.new-features.null-coalesce-op
    */
    protected static function get_cached($key) {
        $class_name = static::class;
        return static::$data_cache[$class_name][$key] ?? null;
    }
}