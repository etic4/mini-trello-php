<?php


/* 
    Méthodes relatives au get et set de createdAt & modifiedAt.
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

    public function set_createdAt_from_sql(?string $createdAt) {
        if (is_null($createdAt)) {
            $this->createdAt = new DateTime();
        } else {
            $this->createdAt = new DateTime($createdAt);
        }
    }

    public function set_modifiedAt_from_sql(?string $modifiedAt, ?string $createdAt) {
        if (is_null($createdAt)) {
            $this->modifiedAt = new DateTime();
        } else {
            if ($modifiedAt == null) {
                $this->modifiedAt = new DateTime($createdAt);
            } else {
                $this->modifiedAt = new DateTime($modifiedAt);
            }
        }
    }

    
}
