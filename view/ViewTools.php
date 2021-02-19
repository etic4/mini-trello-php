<?php

class ViewTools {
    public static function get_columns_string(array $columns): string {
        $cnt = count($columns);
        return "($cnt column" . ($cnt > 1 ? "s" : "") . ")";
    }
}