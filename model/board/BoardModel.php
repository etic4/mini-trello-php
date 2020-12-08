<?php

require_once "Board.php";
require_once "model/user/User.php";
require_once "model/DBTools.php";

abstract class BoardModel extends Model {
    
    public static function get_by_id($id) {
        $sql = "SELECT * FROM board WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $createdAt = DBTools::php_date($data["CreatedAt"]);
            $modifiedAt = DBTools::php_date($data["ModifiedAt"]);
            return new Board($data["Title"], $data["Owner"], $data["ID"], $createdAt, $modifiedAt);
        }
    }

    public static function get_users_boards($user): array {
        $sql = "SELECT * from board WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date($rec["ModifiedAt"]);
            $instance = new Board($rec["Title"], $rec["Owner"], $rec["ID"], $createdAt, $modifiedAt);
            array_push($objects, $instance);
        }
        return $objects;
    }
    
    public static function get_others_boards($user): array {
        $sql = "SELECT * from board WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date($rec["ModifiedAt"]);
            $instance = new Board($rec["Title"], $rec["Owner"], $rec["ID"], $createdAt, $modifiedAt);
            array_push($objects, $instance);
        }
        return $objects;
    }
    
    public function insert() {
        $sql = "INSERT INTO board(Title, Owner) VALUES(:title, :owner)";
        $params = array("title"=>$this->get_title(), "owner"=>$this->get_owner_inst());
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $this->set_modifiedDate();
        $modifiedAt = DBTools::sql_date($this->get_modifiedAt());

        $sql = "UPDATE board SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(), "owner"=>$this->get_owner_inst(),
            "modifiedAt"=>$modifiedAt);
        
        $this->execute($sql, $params);
    }
    
    public function delete() {
        $sql = "DELETE FROM board WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }
}