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
        //$position = Column::get_last_position($board);
        $position = self::get_column_count($board);
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

    public function get_board_id() {
        return $this->get_board_inst()->get_id();
    }

    public function get_cards() {
        return $this->cards;
    }

    protected static function get_instance($data) :Column {
        return new Column(
            $data["Title"],
            $data["Position"],
            $data["Board"], 
            $data["ID"],
            $data["CreatedAt"], 
            $data["ModifiedAt"]
        );
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

    public function set_cards() {
        $this->cards = Card::get_cards_from_column($this);
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
            $column = new Column(
                $data["Title"], 
                $data["Position"], 
                $data["Board"], 
                $data["ID"], 
                $createdAt, 
                $modifiedAt
            );
            $column->set_cards();
            return $column;
        }
    }

    public static function get_all($board): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id ORDER BY Position";
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

    public static function get_columns_from_board($board): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id ORDER BY Position";
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
            $column->cards = Card::get_cards_from_column($column);
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

    //position de la dernière Column du Board
    public static function get_column_count($board_id) {
        $sql =
            "SELECT count(Position) 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        return $data["count(Position)"];
    }

    //liste des colonnes précédant la référence
    public function get_previous_columns() {
        $sql =
            "SELECT *
            FROM `column`
            WHERE Board = :id
            AND Position > :pos";
        $params= array("id"=>$this->board, "pos"=>$this->position);
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $columns = array();
        foreach ($data as $rec) {
            array_push($columns, self::get_instance($rec));
        }
        return $columns;
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
        Card::delete_all($this);
        $sql = 
            "DELETE 
             FROM `column` 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    public static function delete_all($board) {
        foreach ($board->get_columns() as $column) {
            $column->delete();
        }
    }


    
    //    MOVE CARD    //   

    public function move_up(Card $card) {
        $pos = $card->get_position();

        if ($pos > 0) {
            $target = Card::get_cards_from_column($this)[$pos-1];
            $card->set_position($pos-1);
            $target->set_position($pos);

            $card->update();
            $target->update();
        }
    }

    public function move_down(Card $card) {
        $pos = $card->get_position();
        $cards = Card::get_cards_from_column($this);

        if ($pos < sizeof($cards)-1) {
            $target = $cards[$pos+1];
            $card->set_position($pos+1);
            $target->set_position($pos);

            $card->update();
            $target->update();;
        }
    }

    public function move_left(Card $card) {
        $pos = $this->position;

        if ($pos > 0) {
            $target = $this->get_board_inst()->get_columns()[$pos-1];

            $card->set_column($target->get_id());
            $card->set_position(sizeof($target->get_cards()));
            $card->update();

            foreach (Card::get_cards_from_column($this) as $idx=>$card) {
                $card->set_position($idx);
                $card->update();
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

            foreach (Card::get_cards_from_column($this) as $idx=>$card) {
                $card->set_position($idx);
                $card->update();
            }
        }
    }

    public function decrement_previous_columns_position() {
        $columns = $this->get_previous_columns();
        if(count($columns) != 0) {
            foreach($columns as $column) {
                $column->set_position(($column->position) - 1);
                $column->update();
            }
        }
    }
    
}