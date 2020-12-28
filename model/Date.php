<?php

/* Méthodes relatives au get et set de createdAt & modifiedAt.
Ce trait existe essentiellement pour éviter la répétition des setters
*/

trait Date {

    private DateTime $modifiedAt;
    private Datetime $createdAt;

    public static function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function set_createdAt_from_sql(string $createdAt) {
        $this->createdAt = new DateTime($createdAt);
    }

    public function set_modifiedAt_from_sql(?string $modifiedAt){
        if ($modifiedAt == null) {
            $this->modifiedAt = $this->createdAt;
        } else {
            $this->createdAt = new DateTime($modifiedAt);
        }
    }

}
