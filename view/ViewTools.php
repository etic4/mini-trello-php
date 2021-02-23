<?php

require_once "model/User.php";

class ViewTools {
    public static function get_columns_string(array $columns): string {
        $cnt = count($columns);
        return "($cnt column" . ($cnt > 1 ? "s" : "") . ")";
    }



    public static function due_date_string(DateTime $date): string {
        return $date != null ? $date->format('d/m/Y') : "";
    }

    public static function date_picker_due_date(?DateTime $date): string {
        return $date != null ? $date->format('Y-m-d') : "";
    }

    public static function date_picker_min_due_date(Card $card) {
        $due_date = $card->get_createdAt()->add(new DateInterval("P1D"));

        return $due_date->format('Y-m-d');
    }

    public static function selected(User $user, string $role): string {
        return $user->get_role() == $role ? "selected" : "";
    }

}