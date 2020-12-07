<?php

require_once "../Dao.php";

class BoardDao extends Dao {
    protected $tableName;

    public function get_owner_boards($user): array {
        $sql = "SELECT * from board WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        return $this->get_many($sql, $params);
    }

    public function get_others_boards($user): array {
        $sql = "SELECT * from board WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        return $this->get_many($sql, $params);
    }

    public function prepare_insert($object): array {
        $sql = "INSERT INTO board(Title, Owner) VALUES(:title, :owner)";
        $params = array("title"=>$object->get_title(), "owner"=>$object->get_owner());

        return array("sql"=>$sql, "params"=>$params);
    }

    public function prepare_update($object): array {
        $object->set_modifiedDate();
        $modifiedAt = $this->sql_date($object->get_modifiedAt());

        $sql = "UPDATE board SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$object->get_id(), "title"=>$object->get_title(), "owner"=>$object->get_owner(),
            "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    protected function get_instance($data): Board {
        $createdAt = $this->php_date($data["CreatedAt"]);
        $modifiedAt = $this->php_date($data["ModifiedAt"]);
        return new Board($data["Title"], $data["Owner"], $data["ID"], $createdAt, $modifiedAt);
    }

}