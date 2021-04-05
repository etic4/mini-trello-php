<?php


/* 
    Méthodes relatives au get et set de createdAt & modifiedAt.
    Ce trait existe essentiellement pour éviter la répétition des setters
*/

trait DateTrait {

    public static function now_if_null(?DateTime $date): DateTime {
        return DateUtils::now_if_null($date);
    }

    public function get_created_intvl() {
        return $this->intvl($this->get_createdAt(), new DateTime());
    }

    public function get_modified_intvl() {
        if ($this->get_modifiedAt() != null && $this->get_createdAt() != $this->get_modifiedAt()) {
            return "Modified " . $this->intvl($this->get_modifiedAt(), new DateTime());
        }
        return "Never modified";
    }

    private function intvl($firstDate, $secondDate): string {
        return DateUtils::intvl($firstDate, $secondDate);
    }
}
