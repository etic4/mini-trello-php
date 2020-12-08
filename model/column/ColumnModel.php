<?php

require_once "Column.php";
require_once "model/board/Board.php";
require_once "model/DBTools.php";

abstract class ColumnModel extends Model {
    public static function get_by_id($id) {
        $sql = "SELECT * FROM `column` WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $createdAt = DBTools::php_date($data["CreatedAt"]);
            $modifiedAt = DBTools::php_date($data["ModifiedAt"]);
            return new Column($data["Title"], $data["Position"], $data["Board"], $data["ID"], $createdAt, $modifiedAt);
        }
    }

    public static function get_all($board): array {
        $sql = "SELECT * from `column` WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date($rec["ModifiedAt"]);
            $instance = new Column($rec["Title"], $rec["Position"], $rec["Board"], $rec["ID"], $createdAt, $modifiedAt);
            array_push($objects, $instance);
        }
        return $objects;
    }

    public function insert() {
        $sql = "INSERT INTO `column`(Title, Position, Board) VALUES(:title, :position, :board)";
        $params = array("title"=>$this->get_title(), "position"=>$this->get_position(), "board"=>$this->get_board());
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $this->set_modifiedDate();
        $modifiedAt = DBTools::sql_date($this->get_modifiedAt());

        $sql = "UPDATE `column` SET Title=:title, Position=:position, Board=:board, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(), "position"=>$this->get_position(),
            "board"=>$this->get_board(), "modifiedAt"=>$modifiedAt);
        $this->execute($sql, $params);
    }

    public function delete() {
        $sql = "DELETE FROM `column` WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }
}