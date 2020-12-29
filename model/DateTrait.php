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

    public function get_created_intvl() {
        return $this->intvl($this->get_createdAt(), new DateTime());
    }

    public function get_modified_intvl() {
        if ($this->get_createdAt() != $this->get_modifiedAt()) {
            return "Modified " . $this->intvl($this->get_modifiedAt(), new DateTime());
        }
        return "Never modified";
    }

    private function intvl($firstDate, $secondDate): string {
        $intvl = $secondDate->diff($firstDate);
        $laps = "1 second ago";
        if(!is_null($intvl)) {
            if ($intvl->y != 0) {
                if($intvl->y == 1) {
                    $laps = "1 year ago";
                } else {
                    $laps = $intvl->y . " years ago";
                }
            } elseif ($intvl->m != 0) {
                if($intvl->m == 1) {
                    $laps = "1 month";
                } else {
                    $laps = $intvl->m . " months ago";
                }
            } elseif ($intvl->d != 0) {
                if($intvl->d == 1) {
                    $laps = "1 day";
                } else {
                    $laps = $intvl->d . " days ago";
                }
            } elseif ($intvl->h != 0) {
                if($intvl->h == 1) {
                    $laps = "1 hour";
                } else {
                    $laps = $intvl->h . " hours ago";
                }
            } elseif ($intvl->i != 0) {
                if($intvl->i == 1) {
                    $laps = "1 minute";
                } else {
                    $laps = $intvl->i . " minutes ago";
                }
            } elseif ($intvl->s != 0) {
                if($intvl->s == 1) {
                    $laps = "1 second";
                } else {
                    $laps = $intvl->s . " seconds ago";
                }
            }
        }
        return $laps;
    }

    public function laps ($firstDate, $secondDate): string {
        if ($secondDate->diff($firstDate)->format('Y-m-d H:i:s') == "0-0-0 0:0:0") {
            return "Modified " . $this->intvl($firstDate, $secondDate);
        }
        else {
            return "Never modified";
        }
    }
}
