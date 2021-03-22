<?php


/* 
    Méthodes relatives au get et set de createdAt & modifiedAt.
    Ce trait existe essentiellement pour éviter la répétition des setters
*/

trait DateTrait {

    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;

    public static function sql_date($datetime) {
        return $datetime != null ? $datetime->format('Y-m-d H:i:s') : null;
    }

    /* Crée des intances de Datetime à partir d'un string provenant de la DB
    Si $modifiedAt est null, il est set à la valeur de $createdAt
    retourne une array de 2 DateTime.
    TODO: revoir toute cette logique ultérieurement, c'est foireux.
*/
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

    /* Comme la date de création est set en db,
    cette méthode set les dates de l'instance après insertion ou update */
    public function set_dates_from_db() {
        list($createdAt, $modifiedAt) = $this->query_dates();
        $this->set_createdAt($createdAt);
        $this->set_modifiedAt($modifiedAt);
    }

    /* Récupère les dates de création et de modification en DB après insertion*/
    private function query_dates() {
        $tableName = strtolower(get_class($this));
        $sql = "SELECT CreatedAt, ModifiedAt FROM `$tableName` WHERE ID=:id";
        $params = array("id" => $this->get_id());
        $query = $this->execute($sql, $params);
        $data = $query->fetch();

        return self::get_dates_from_sql($data["CreatedAt"], $data["ModifiedAt"]);
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
        if ($intvl->y != 0) {
            if($intvl->y == 1) {
                $laps = "1 year ago";
            } else {
                $laps = $intvl->y . " years ago";
            }
        } elseif ($intvl->m != 0) {
            if($intvl->m == 1) {
                $laps = "1 month ago";
            } else {
                $laps = $intvl->m . " months ago";
            }
        } elseif ($intvl->d != 0) {
            if($intvl->d == 1) {
                $laps = "1 day ago";
            } else {
                $laps = $intvl->d . " days ago";
            }
        } elseif ($intvl->h != 0) {
            if($intvl->h == 1) {
                $laps = "1 hour ago";
            } else {
                $laps = $intvl->h . " hours ago";
            }
        } elseif ($intvl->i != 0) {
            if($intvl->i == 1) {
                $laps = "1 minute ago";
            } else {
                $laps = $intvl->i . " minutes ago";
            }
        } elseif ($intvl->s != 0) {
            if($intvl->s == 1) {
                $laps = "1 second ago";
            } else {
                $laps = $intvl->s . " seconds ago";
            }
        }
    return $laps;
    }
}
