<?php

/*wrapper autour de $_GET et $_POST pour en faciliter l'usage*/
abstract class GetPost {


    protected static ?array $GP = null;

    // Set $GP avec $_POST ou $_GET par les enfants
    abstract protected static function set_super_global();


    public static function get(string $key) {
        static::set_super_global();

        if (isset(static::$GP[$key])) {
            return static::$GP[$key];
        }
        return "";
    }

    public static function get_or_null(string $key) {
        static::set_super_global();

        if (isset(static::$GP[$key])) {
            return static::$GP[$key];
        }
        return null;
    }

    public static function get_or_default(string $key, $default) {
        static::set_super_global();

        return static::isset($key) ? static::get($key) : $default;
    }

    public static function isset(string $key): bool {
        static::set_super_global();

        return isset(static::$GP[$key]);
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