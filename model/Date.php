<?php

/* Méthodes relatives au get et set de createdAt & modifiedAt.
Ce trait existe essentiellement pour éviter la répétition des setters
*/

trait Date {

    private DateTime $modifiedAt;
    private Datetime $createdAt;

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function set_createdAt(?DateTime $createdAt): void {
        if ($createdAt == null) {
            $this->createdAt = new DateTime("now");
        } else {
            $this->createdAt = $createdAt;
        }
    }

    public function set_modifiedAt(?DateTime $modifiedAt): void {
        if ($modifiedAt == null) {
            $this->modifiedAt = new DateTime("now");
        } else {
            $this->modifiedAt = $modifiedAt;
        }
    }

    public function set_modifiedDate() {
        $this->set_modifiedAt(new DateTime("now"));
    }

    public function set_modifiedDate_and_get_sql() {
        $this->set_modifiedDate();
        return DBTools::sql_date($this->get_modifiedAt());
    }
}
