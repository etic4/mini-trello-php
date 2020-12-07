<?php

require_once "Column.php";
require_once "model/board/Board.php";
require_once "model/Dao.php";

class ColumnDao extends Dao {
    protected $tableName;

    public function get_all($board): array {
        $sql = "SELECT * from `column` WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        return $this->get_many($sql, $params);
    }

    public function prepare_insert($object): array {
        $sql = "INSERT INTO `column`(Title, Position, Board) VALUES(:title, :position, :board)";
        $params = array("title"=>$object->get_title(), "position"=>$object->get_owner(), "board"=>$object->get_board());

        return array("sql"=>$sql, "params"=>$params);
    }

    public function prepare_update($object): array {
        $object->set_modifiedDate();
        $modifiedAt = $this->sql_date($object->modifiedAt);

        $sql = "UPDATE `column` SET Title=:title, Position=:position, Board=:board, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$object->get_id(), "title"=>$object->get_title(), "position"=>$object->get_position(),
            "board"=>$object->get_board(), "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    protected function get_instance($data): Column {
        $createdAt = $this->php_date($data["CreatedAt"]);
        $modifiedAt = $this->php_date($data["ModifiedAt"]);
        return new Column($data["Title"], $data["Position"], $data["Board"], $data["ID"], $createdAt, $modifiedAt);
    }

}