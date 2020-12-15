<?php

//require_once "ColumnModel.php";
//require_once "ColumnValidator.php";
require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/Card.php";

class Column extends Model {
    private $id;
    private $title;
    private $position;
    private $createdAt;
    private $modifiedAt;
    private $board;
    private $cards;

    public function __construct($title, $position, $board, $id=null, $createdAt=null, $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->board = $board;
    }

    public static function create_new($title, $author, $board) {
        $position = Column::get_last_position($board) + 1;
        $createdAt = new DateTime();
        return new Column(
            $title, 
            $position, 
            $board, 
            null, 
            $createdAt, 
            null
        );
    }


    //    GETTERS    //

    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_position() {
        return $this->position;
    }

    public function get_createdAt() {
        return $this->createdAt;
    }

    public function get_modifiedAt() {
        return $this->modifiedAt;
    }

    public function get_board() {
        return $this->board;
    }

    public function get_board_inst() {
        return Board::get_by_id($this->board);
    }

    public function get_cards() {
        return $this->cards;
    }


    //    SETTERS    //

    public function set_id($id) {
        $this->id = $id;
    }

    public function set_position($position) {
        $this->position = $position;
    }

    public function set_modifiedDate() {
        $this->modifiedAt = new DateTime("now");
    }


    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] = "Le titre doit comporter au moins 3 caractères";
        }
        return $errors;
    }


    //    QUERIES    //

    public static function get_by_id($id) {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            $createdAt = DBTools::php_date($data["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($data["ModifiedAt"], $data["CreatedAt"]);
            return new Column(
                $data["Title"], 
                $data["Position"], 
                $data["Board"], 
                $data["ID"], 
                $createdAt, 
                $modifiedAt
            );
        }
    }

    public static function get_all($board): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $columns = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $column = new Column(
                $rec["Title"], 
                $rec["Position"], 
                $rec["Board"], 
                $rec["ID"], 
                $createdAt, 
                $modifiedAt
            );
            array_push($columns, $column);
        }
        return $columns;
    }

    public static function get_columns_from_board($board_id): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board_id);
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $columns = array();
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $column = new Column(
                $rec["Title"], 
                $rec["Position"], 
                $rec["Board"], 
                $rec["ID"], 
                $createdAt, 
                $modifiedAt
            );
            $column->cards = Card::get_cards_from_column($column->id);
            array_push($columns, $column);
        }
        return $columns;
    }

    //position de la dernière Column du Board
    public static function get_last_position($board_id) {
        $sql = 
            "SELECT MAX(Position) 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return -1;
        } 
        else {
            return $data["MAX(Position)"];
        }
    }

    public function insert() {
        $sql = 
            "INSERT INTO `column`(Title, Position, Board) 
             VALUES(:title, :position, :board)";
        $params = array(
            "title" => $this->get_title(), 
            "position" => $this->get_position(), 
            "board" => $this->get_board()
        );
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $this->set_modifiedDate();
        $modifiedAt = DBTools::sql_date($this->get_modifiedAt());

        $sql = 
            "UPDATE `column` 
             SET Title=:title, Position=:position, Board=:board, ModifiedAt=:modifiedAt 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(), 
            "position" => $this->get_position(),
            "board" => $this->get_board(), 
            "modifiedAt" => $modifiedAt
        );
        $this->execute($sql, $params);
    }

    public function delete() {
        $sql = 
            "DELETE 
             FROM `column` 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    public static function delete_all($board) {
        foreach ($board->get_all() as $column) {
            $column->delete();
        }
    }


    
    //    MOVE CARD    //   

    public function move_up(Card $card) {
        $pos = $card->get_position();

        if ($pos > 0) {
            $target = $this->cards[$pos-1];
            $card->set_position($pos-1);
            $target->set_position($pos);

            $card->update();
            $target->update();
        }
    }

    public function move_down(Card $card) {
        $pos = $card->get_position();

        if ($pos < sizeof($this->cards)-1) {
            $target = $this->cards[$pos+1];
            $card->set_position($pos+1);
            $target->set_position($pos);

            $card->update();
            $target->update();
        }
    }

    public function move_left(Card $card) {
        $pos = $this->position;

        if ($pos > 0) {
            $target = $this->get_board_inst()->get_columns()[$pos-1];
            $cards = $target->get_cards();
            $cardPos = $card->get_position();

            $card->set_column($target->get_id());
            $card->set_position(sizeof($cards));
            $card->update();

            for ($i = ++$cardPos; $cardPos < sizeof($cards);  ++$i) {
                $cards[$i]->set_position($cards[$i]->get_position() - 1);
            }
        }
    }

    public function move_right(Card $card) {
        $pos = $this->position;
        $colList = $this->get_board_inst()->get_columns();

        if ($pos < sizeof($colList)-1) {
            $target = $colList[$pos+1];
            $card->set_column($target->get_id());
            $card->set_position(sizeof($target->get_cards()));
            $card->update();
        }
    }

    

    

    

    

    


}