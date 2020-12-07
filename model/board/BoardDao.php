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
        $createdAt = $this->sql_date($object->createdAt);
        $modifiedAt = $this->sql_date($object->modifiedAt);

        $sql = "INSERT INTO board(Title, Owner, CreatedAt, ModifiedAt) VALUES(:title, :owner, :createdAt, :modifiedAt)";
        $params = array("title"=>$object->title, "owner"=>$object->owner, "createdAt"=>$createdAt, "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    public function prepare_update($object): array {
        $object->set_modifiedDate();
        $modifiedAt = $this->sql_date($object->modifiedAt);
        $registeredAt = $this->sql_date($object->registeredAt);

        $sql = "UPDATE board SET Title=:title, Owner=:owner, CreatedAt=:createdAt, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$object->id, "title"=>$object->title, "owner"=>$object->owner->get_id(),
            "createdAt"=>$registeredAt, "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    protected function get_instance($data): Board {
        $createdAt = $this->php_date($data["CreatedAt"]);
        $modifiedAt = $this->php_date($data["ModifiedAt"]);
        return new Board($data["Title"], $data["Owner"], $data["ID"], $createdAt, $modifiedAt);
    }

}