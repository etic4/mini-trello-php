<?php
/**/
require_once "framework/Model.php";

abstract class DBTools {
    public static function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    public static function php_date($sqlDate): ?DateTime {
        try {
            return new DateTime($sqlDate);
        } catch (Exception $e) {
            print("Erreur lors de la conversion de la date: " . $sqlDate);
        }
    }

}
