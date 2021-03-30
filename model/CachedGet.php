<?php

require_once "autoload.php";


/*
 * Le résultat d'un get_by_id est mis en cache dans Class::$data_cache et retourné lors d'appels subséquents.
 *
 * Cf. "late static binding" pour l'utilisation de 'static' plutôt que 'self'. Avec 'self', déterminé à la
 *   "compilation", self::class retourne "CachedGet" alors que static::class retourne le nom de la classe dont $this est une instance.
 *   https://stackoverflow.com/questions/1912902/what-exactly-are-late-static-bindings-in-php
 */

abstract class CachedGet extends Model {

    protected static array $data_cache = [];

    // retourne une clé (string) pour le query params
    protected static function get_key_for($queryParams): string {
        $keys = [];
        foreach ($queryParams as $key => $value) {
            $keys[] = $key . "_" . $value;
        }
        return join("_", $keys);
    }

    protected static function cache_result($result, $key) {
        self::ensure_cache_for_class();
        self::$data_cache[static::class][$key] = $result;
    }

    /* Check si $key existe en cache */
    protected static function is_in_cache($key): bool {
        return isset(self::$data_cache[static::class][$key]) ;
    }

    /* Cf. https://www.php.net/manual/fr/migration70.new-features.php#migration70.new-features.null-coalesce-op
    */
    protected static function get_cached($key) {
        return self::is_in_cache($key) ? static::$data_cache[static::class][$key] :  null;
    }


    /* Crée un array avec comme clé static::class si n'existe pas*/
    protected static function ensure_cache_for_class() {
        $class_name = static::class;
        if (!isset(self::$data_cache[$class_name])) {
            self::$data_cache[$class_name] = [];
        }
    }
}