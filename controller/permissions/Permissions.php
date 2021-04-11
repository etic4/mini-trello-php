<?php

require_once "autoload.php";

class Permissions {

    public static function add($object): bool {
        $class = self::get_class_name($object);
        return (new $class($object))->add(self::get_user());
    }

    public static function view($object): bool {
        $class = self::get_class_name($object);
        return (new $class($object))->view(self::get_user());
    }

    public static function edit($object): bool {
        $class = self::get_class_name($object);
        return (new $class($object))->edit(self::get_user());
    }

    public static function delete($object): bool {
        $class = self::get_class_name($object);
        return (new $class($object))->delete(self::get_user());
    }

    public static function get_user() {
        return $_SESSION["user"];
    }

    private static function get_class_name($object): string {
        if (is_string($object)) {
            $class_name = $object;
        } else {
            $class_name = get_class($object);
        }
        return $class_name . "Permissions";
    }
}