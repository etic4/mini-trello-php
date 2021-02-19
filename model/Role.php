<?php


class Role {
    const ADMIN = "admin";
    const USER = "user";

    public static function is_valid_role($role): bool {
        return $role == self::ADMIN || $role == self::USER;
    }
}