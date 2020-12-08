<?php

require_once "Board.php";
require_once "model/user/User.php";
require_once "model/BaseModel.php";

abstract class BoardModel extends BaseModel {
    /**
     * Retourne la liste des boards appartenant à 'user
    */
    public static function get_users_boards($user): array {
        $sql = "SELECT * from board WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        return self::get_many($sql, $params);
    }

    /**
     * Retourne la liste des boards appartenant aux autres que 'user
     */
    public static function get_others_boards($user): array {
        $sql = "SELECT * from board WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        return self::get_many($sql, $params);
    }

    /**
     * C'est ici qu'est préparé le sql et les paramètre pour un insert dans la table
     * Cette méthode est appelée par la méthode insert(), qui est une méthode d'instance définie sur la classe abstraite BaseModel
     */
    public function prepare_insert(): array {
        $sql = "INSERT INTO board(Title, Owner) VALUES(:title, :owner)";
        $params = array("title"=>$this->get_title(), "owner"=>$this->get_owner_inst());

        return array("sql"=>$sql, "params"=>$params);
    }

    /**
     * C'est ici qu'est préparé le sql et les paramètre pour un update dans la table
     * Cette méthode est appelée par la méthode update(), qui est une méthode d'instance définie sur la classe abstraite BaseModel
     */
    public function prepare_update(): array {
        $this->set_modifiedDate();
        $modifiedAt = $this->sql_date($this->get_modifiedAt());

        $sql = "UPDATE board SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(), "owner"=>$this->get_owner_inst(),
            "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    /**
     * get_instance retourne une instance à partir d'une ligne de résultat d'un GET en db
     */
    protected static function get_instance($data): Board {
        $createdAt = self::php_date($data["CreatedAt"]);
        $modifiedAt = self::php_date($data["ModifiedAt"]);
        return new Board($data["Title"], $data["Owner"], $data["ID"], $createdAt, $modifiedAt);
    }

}