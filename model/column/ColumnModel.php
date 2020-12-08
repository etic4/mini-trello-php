<?php

require_once "Column.php";
require_once "model/board/Board.php";
require_once "model/BaseModel.php";

abstract class ColumnModel extends BaseModel {
    /**
     * Retourne toutes les colonnes de '$board'
     */
    public static function get_all($board): array {
        $sql = "SELECT * from `column` WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        return self::get_many($sql, $params);
    }

    /**
     * C'est ici qu'est préparé le sql et les paramètre pour un insert dans la table
     * Cette méthode est appelée par la méthode insert(), qui est une méthode d'instance définie sur la classe abstraite BaseModel
     */
    public function prepare_insert(): array {
        $sql = "INSERT INTO `column`(Title, Position, Board) VALUES(:title, :position, :board)";
        $params = array("title"=>$this->get_title(), "position"=>$this->get_position(), "board"=>$this->get_board());

        return array("sql"=>$sql, "params"=>$params);
    }

    /**
     * C'est ici qu'est préparé le sql et les paramètre pour un update dans la table
     * Cette méthode est appelée par la méthode update(), qui est une méthode d'instance définie sur la classe abstraite BaseModel
     */
    public function prepare_update(): array {
        $this->set_modifiedDate();
        $modifiedAt = $this->sql_date($this->get_modifiedAt());

        $sql = "UPDATE `column` SET Title=:title, Position=:position, Board=:board, ModifiedAt=:modifiedAt WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(), "position"=>$this->get_position(),
            "board"=>$this->get_board(), "modifiedAt"=>$modifiedAt);

        return array("sql"=>$sql, "params"=>$params);
    }

    /**
     * get_instance retourne une instance à partir d'une ligne de résultat d'un GET en db
     */
    protected static function get_instance($data): Column {
        $createdAt = self::php_date($data["CreatedAt"]);
        $modifiedAt = self::php_date($data["ModifiedAt"]);
        return new Column($data["Title"], $data["Position"], $data["Board"], $data["ID"], $createdAt, $modifiedAt);
    }

}