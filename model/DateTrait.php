<?php


/* 
    Méthodes relatives au get et set de createdAt & modifiedAt.
    Ce trait existe essentiellement pour éviter la répétition des setters
*/

trait DateTrait {

    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;

    public static function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    public static function get_dates_from_sql($createdAt, $modifiedAt): array {
        $createdAtInst = new DateTime($createdAt);
        $modifiedAtInst = $createdAtInst;
        if (!is_null($modifiedAt)) {
            $modifiedAtInst = new DateTime($modifiedAt);
        }
        return array($createdAtInst, $modifiedAtInst);
    }


    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function set_createdAt(DateTime $createdAt) {
        $this->createdAt = $createdAt;
    }

    public function set_modifiedAt(DateTime $modifiedAt) {
        $this->modifiedAt = $modifiedAt;
    }

    public function set_dates_from_instance($inst) {
        $this->set_createdAt($inst->get_createdAt());
        $this->set_modifiedAt($inst->get_modifiedAt());
    }
}
