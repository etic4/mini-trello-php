<?php
/**/
require_once "framework/Model.php";

abstract class BaseModel extends Model {
    protected abstract function prepare_insert();
    protected abstract function prepare_update();
    protected abstract static function get_instance($data);
    protected abstract static function get_tableName();

    /**
     * 'get_by_id' est définie pour toutes les tables. Elle utilise la méthode statique 'get_tableName' implémentée
     * par chaque objet pour spécifier la table
     */
    public static function get_by_id($id) {
        $sql = "SELECT * FROM ". static::get_tableName() . " WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        return self::fetch_one_and_get_instance($query);
    }

    /**
     * fetch un enregistrement en DB et retourne un instance e l'objet
     */
    protected static function fetch_one_and_get_instance($query) {
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return static::get_instance($data);
        }
    }

    /**
     * fetch plusieurs enregistrements en DB et retourne un array d'instances
     */
    protected static function get_many($sql, $params): array {
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            array_push($objects, static::get_instance($rec));
        }
        return $objects;
    }

    /**
     * insertion en db. Invoque 'prépare_insert' sur l'instance pour recevoir
     * le sql et les paramètres
     */
    public function insert() {
        $prepared = $this->prepare_insert();
        $this->execute($prepared["sql"], $prepared["params"]);

        return $this->get_by_id($this->lastInsertId());
    }
    /**
     * update en db. Invoque 'prépare_update' sur l'instance pour recevoir
     * le sql et les paramètres
     */
    public function update() {
        $prepared = $this->prepare_update();
        $this->execute($prepared["sql"], $prepared["params"]);
    }

    /**
     * suppression. utilise 'get_tableName' pour déterminer le nom de la table
     */
    public function delete() {
        $sql = "DELETE FROM " . $this->get_tableName() . " WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    protected static function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    protected static function php_date($sqlDate): ?DateTime {
        try {
            return new DateTime($sqlDate);
        } catch (Exception $e) {
            print("Erreur lors de la conversion de la date: " . $sqlDate);
        }
    }

}
