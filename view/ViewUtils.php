<?php

require_once "model/User.php";

class ViewUtils {
    public static function created_intvl($object): string {
        return DateUtils::intvl($object->get_createdAt(), new DateTime());
    }

    public static function modified_intvl($object): string {
        if ($object->get_modifiedAt() != null && $object->get_createdAt() != $object->get_modifiedAt()) {
            return "Modified " . DateUtils::intvl($object->get_modifiedAt(), new DateTime());
        }
        return "Never modified";
    }

    public static function most_recent_interval($object) {
        $the_date = $object->get_modifiedAt() != null ? max($object->get_createdAt(), $object->get_modifiedAt()) : $object->get_createdAt();
        return DateUtils::intvl($the_date, new DateTime());
    }

    public static function due_date_string(DateTime $date): string {
        return $date != null ? $date->format('d/m/Y') : "";
    }

    public static function date_picker_due_date(?DateTime $date): string {
        return $date != null ? $date->format('Y-m-d') : "";
    }

    public static function date_picker_min_due_date(Card $card): string {
        $due_date = $card->get_createdAt()->add(new DateInterval("P1D"));
        return $due_date->format('Y-m-d');
    }

    // nbr de colonnes
    public static function columns_string(array $columns): string {
        $cnt = count($columns);
        return "($cnt column" . ($cnt > 1 ? "s" : "") . ")";
    }

    public static function selected_state(string $posted, string $role): string {
        return $posted == $role ? "selected" : "";
    }

    public static function due_date_styling(Card $card) {
        $card_background = "";
        $button_background = "is-white";
        $text_color = "has-text-info-dark";

        if ($card->is_due()) {
            $card_background = "has-background-danger";
            $button_background = "is-danger";
            $text_color = "has-text-white";
        }
        return ["card_background" => $card_background, "button_background" => $button_background, "text_color" => $text_color];
    }

    public static function class_name(object $obj, $lower=true): string {
        $class_name = get_class($obj);
        if ($lower) {
            $class_name = strtolower($class_name);
        }
        return $class_name;
    }
}