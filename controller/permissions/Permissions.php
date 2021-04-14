<?php

require_once "autoload.php";

//TODO: ajouter fonction get_user_or_redirect dans utils (ControllerUtils ?)
// l'utiliser ici et la supprimer de partout ailleurs
class Permissions {
    public static function add($object): bool {
        $user = Session::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->add($user, $object);
        }
        return false;
    }

    public static function view($object): bool {
        $user = Session::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->view($user, $object);
        }
        return false;
    }

    public static function edit($object): bool {
        $user = Session::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->edit($user, $object);
        }
        return false;
    }

    public static function delete($object): bool {
        $user = Session::get_user();
        if ($user ) {
            $perm_class = self::perm_name($object);
            return (new $perm_class())->delete($user, $object);
        }
        return false;
    }

    public static function is_owner(Board $board): bool {
        $user = Session::get_user();
        if ($user ) {
            return $user->is_admin() || $user->is_owner($board);
        }
        return false;
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