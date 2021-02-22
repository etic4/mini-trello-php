<?php

class ViewTools {
    public static function get_columns_string(array $columns): string {
        $cnt = count($columns);
        return "($cnt column" . ($cnt > 1 ? "s" : "") . ")";
    }

    public static function due_date_string(DateTime $date): string {
        return $date != null ? $date->format('d/m/Y') : "";
    }

    public static function date_now() {
        return(new Datetime())->format('d/m/Y');
    }
}