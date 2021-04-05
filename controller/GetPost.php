<?php

/*wrapper autour de $_GET et $_POST pour en faciliter l'usage*/
abstract class GetPost {

    protected static ?array $GoP = null;

    // Set $GoP avec $_POST ou $_GET par les enfants
    abstract protected static function set_super_global();


    public static function get(string $key, string $default="") {
        static::set_super_global();

        if (isset(static::$GoP[$key])) {
            return static::$GoP[$key];
        }
        return $default;
    }

    public static function get_or_null(string $key) {
        static::set_super_global();

        if (isset(static::$GoP[$key])) {
            return static::$GoP[$key];
        }
        return null;
    }

    public static function isset(string $key): bool {
        static::set_super_global();

        return isset(static::$GoP[$key]);
    }

    // retourne true si toutes les clés sont set
    public static function all_sets(string ...$keys): bool {
        static::set_super_global();

        foreach ($keys as $key) {
            if (!static::isset($key)) {
                return false;
            }
        }
        return true;
    }

    // retourne true si au moins une des clés est non vide
    public static function any_non_empty(string ...$keys): bool {
        static::set_super_global();

        foreach ($keys as $key) {
            if (!empty(static::get($key))) {
                return true;
            }
        }
        return false;
    }

    // retourne true si la clé est empty
    public static function empty(string $key): bool {
        return empty(static::get($key));
    }

    // retourne true si aucune des clés n'est vide
    public static function no_empty(string ...$keys): bool {
        static::set_super_global();

        foreach ($keys as $key) {
            if (empty(static::get($key))) {
                return false;
            }
        }
        return true;
    }
}