<?php

//require_once "BoardModel.php";
require_once "Validation.php";
require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/User.php";
require_once "model/Column.php";

class Board extends Model {
    private $id;
    private $title;
    private $owner;
    private $createdAt;
    private $modifiedAt;
    private $columns;

    public static function delete_all($user) {
        foreach (Board::get_users_boards($user) as $board) {
            $board->delete();
        }
    }

    public function __construct($title, $owner, $id=null, $createdAt=null, $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->columns = $this->get_columns();
    }


    //    GETTERS    //

    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_owner() {
        return $this->owner;
    }

    public function get_owner_inst(): ?User {
        return User::get_by_id($this->owner);
    }

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function get_columns(): array {
        return Column::get_all_columns_from_board($this);
    }

    public function move_left(Column $col) {
        $pos = $col->get_position();

        if ($pos > 0) {
            $target = $this->columns[$pos-1];
            $col->set_position($pos-1);
            $target->set_position($pos);

            $col->update();
            $target->update();
        }
    }

    public function move_right(Column $col) {
        $pos = $col->get_position();

        if ($pos < sizeof($this->columns)-1) {
            $target = $this->columns[$pos+1];
            $col->set_position($pos+1);
            $target->set_position($pos);

            $col->update();
            $target->update();
        }
    }
    
    //    SETTERS    //

    public function set_id($id): void {
        $this->id = $id;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function set_modifiedDate() {
        $this->modifiedAt = new DateTime("now");
    }


    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors = "Le titre doit comporter au moins 3 caractères";
        }
        return $errors;
    }


    //    QUERIES    //

    public static function get_by_id($id) {
        $sql = "SELECT * FROM board WHERE ID=:id";
        $params = array("id"=>$id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            $createdAt = DBTools::php_date($data["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($data["ModifiedAt"], $data["CreatedAt"]);
            $owner = User::get_by_id($data["Owner"]);
            return new Board($data["Title"], $owner, $data["ID"], $createdAt, $modifiedAt);
        }
    }

    public static function get_users_boards($user): array {
        $sql = "SELECT * FROM board WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $board = new Board($rec["Title"], $rec["Owner"], $rec["ID"], $createdAt, $modifiedAt);
            array_push($boards, $board);
        }

        return $boards;
    }
    
    public static function get_others_boards($user): array {
        $sql = "SELECT * from board WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $board = new Board($rec["Title"], $rec["Owner"], $rec["ID"], $createdAt, $modifiedAt);
            array_push($boards, $board);
        }
        return $boards;
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