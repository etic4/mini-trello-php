<?php

require_once "autoload.php";

//TODO: ajouter fonction get_user_or_redirect dans utils (ControllerUtils ?)
// l'utiliser ici et la supprimer de partout ailleurs
class Permissions {
    public static function add($object): bool {
        $user = self::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->add(self::get_user(), $object);
        }
        return false;
    }

    public static function view($object): bool {
        $user = self::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->view(self::get_user(), $object);
        }
        return false;
    }

    public static function edit($object): bool {
        $user = self::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->edit(self::get_user(), $object);
        }
        return false;
    }

    public static function delete($object): bool {
        $user = self::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->delete(self::get_user(), $object);
        }
        return false;
    }

    public static function is_owner(Board $board): bool {
        $user = self::get_user();
        if ($user ) {
            return self::get_user()->is_admin() || self::get_user()->is_owner($board);
        }
        return false;
    }

    public static function get_user() {
        return $_SESSION["user"] ?? false;
    }

    private static function perm_name($object): string {
        if (is_string($object)) {
            $perm_class_name = $object;
        } else {
            $perm_class_name = get_class($object);
        }
        return $perm_class_name . "Permissions";
    }
}