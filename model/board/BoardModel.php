<?php

require_once "Board.php";
require_once "model/user/User.php";
require_once "model/BaseModel.php";

abstract class BoardModel extends BaseModel {
    public static function get_users_boards($user): array {
        $sql = "SELECT * from board WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        return self::get_many($sql, $params);
    }

    public static function get_others_boards($user): array {
        $sql = "SELECT * from board WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        return self::get_many($sql, $params);
    }

    public function prepare_insert(): array {
        $sql = "INSERT INTO board(Title, Owner) VALUES(:title, :owner)";
        $params = array("title"=>$this->get_title(), "owner"=>$this->get_owner_inst());

        return array("sql"=>$sql, "params"=>$params);
    }

    public function prepare_update(): array {
        $this->set_modifiedDate();
        $modifiedAt = $this->sql_date($this->get_modifiedAt());

        $sql = "UPDATE board SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(), "owner"=>$this->get_owner_inst(),
            "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    protected static function get_instance($data): Board {
        $createdAt = self::php_date($data["CreatedAt"]);
        $modifiedAt = self::php_date($data["ModifiedAt"]);
        return new Board($data["Title"], $data["Owner"], $data["ID"], $createdAt, $modifiedAt);
    }

}