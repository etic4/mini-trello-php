<?php

//require_once "ColumnModel.php";
//require_once "ColumnValidator.php";
require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/Card.php";

class Column extends Model {
    use DateGetSetTrait;

    private ?string $id;
    private string $title;
    private int $position;
    private DateTime $createdAt;
    private ?DateTime $modifiedAt;
    private string $board;
    private ?array $cards;

    public function __construct(string $title, int $position, string $board, string $id=null, DateTime $createdAt=null, ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->board = $board;
        $this->set_createdAt($createdAt);
        $this->set_modifiedAt($modifiedAt);
    }

    public static function create_new(string $title, Board $board) {
        return new Column(
            $title, 
            self::get_column_count($board), 
            $board->get_id(), 
            null, 
            new DateTime(), 
            null
        );
    }


    //    GETTERS    //

    public function get_id(): string {
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function get_position(): int {
        return $this->position;
    }

    public function get_board(): string {
        return $this->board;
    }

    public function get_board_inst(): Board {
        return Board::get_by_id($this->board);
    }

    public function get_board_id(): string {
        return $this->get_board_inst()->get_id();
    }

    public function get_cards(): array {
        return $this->cards;
    }

    protected static function get_instance($data) :Column {
        return new Column(
            $data["Title"],
            $data["Position"],
            $data["Board"], 
            $data["ID"],
            DBTools::php_date($data["CreatedAt"]), 
            DBTools::php_date($data["ModifiedAt"])
        );
    }


    //    SETTERS    //

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function set_position(int $position): void {
        $this->position = $position;
    }

    public function set_cards(): void {
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

    public static function get_by_id(string $id): ?Column {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE ID=:id";
        $param = array("id"=>$id);
        $query = self::execute($sql, $param);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            $column = self::get_instance($data);
            $column->set_cards();
            return $column;
        }
    }

    public static function get_all(Board $board): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id ORDER BY Position";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $columns = array();
        foreach ($data as $rec) {
            $column = self::get_instance($rec);
            array_push($columns, $column);
        }
        return $columns;
    }

    public static function get_columns_from_board(Board $board): array {
        $sql = 
            "SELECT * 
             FROM `column` 
             WHERE Board=:id ORDER BY Position";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $columns = array();
        foreach ($data as $rec) {
            $column = self::get_instance($rec);
            $column->cards = Card::get_cards_from_column($column);
            array_push($columns, $column);
        }
        return $columns;
    }

    //position de la dernière Column du Board
    public static function get_last_position(Board $board): int {
        $sql = 
            "SELECT MAX(Position) 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return -1;
        } 
        else {
            return $data["MAX(Position)"];
        }
    }

    //nombre de Column du Board
    public static function get_column_count(Board $board): int {
        $sql =
            "SELECT count(Position) 
             FROM `column` 
             WHERE Board=:id";
        $params= array("id"=>$board->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        return $data["count(Position)"];
    }

    //liste des colonnes précédant la référence
    public function get_previous_columns(): array {
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
            $column = self::get_instance($rec);
            array_push($columns, $column);
        }
        return $columns;
    }

    public function insert(): Column {
        $sql = 
            "INSERT INTO `column`(Title, Position, Board, CreatedAt, ModifiedAt) 
             VALUES(:title, :position, :board, :createdAt, :modifiedAt)";
        $params = array(
            "title" => $this->get_title(), 
            "position" => $this->get_position(), 
            "board" => $this->get_board(),
            "createdAt" => $this->get_createdAt(),
            "modifiedAt" => $this->get_modifiedAt()
        );

        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update(): void {
        $sql = 
            "UPDATE `column` 
             SET Title=:title, Position=:position, Board=:board, ModifiedAt=:modifiedAt 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(), 
            "position" => $this->get_position(),
            "board" => $this->get_board(), 
            "modifiedAt" => $this->set_modifiedDate_and_get_sql()
        );
        $this->execute($sql, $params);
    }

    public function delete(): void {
        Card::delete_all($this);
        $sql = 
            "DELETE 
             FROM `column` 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    public static function delete_all($board): void {
        foreach ($board->get_columns() as $column) {
            $column->delete();
        }
    }


    
    //    MOVE CARD    //   

    public function move_up(Card $card): void {
        $pos = $card->get_position();

        if ($pos > 0) {
            $target = Card::get_cards_from_column($this)[$pos-1];
            $card->set_position($pos-1);
            $target->set_position($pos);

            $card->update();
            $target->update();
        }
    }

    public function move_down(Card $card): void {
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

    public function move_left(Card $card): void {
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

    public function move_right(Card $card): void {
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

    public function decrement_previous_columns_position(): void {
        $columns = $this->get_previous_columns();
        if(count($columns) != 0) {
            foreach($columns as $column) {
                $column->set_position(($column->position) - 1);
                $column->update();
            }
        }
    }
    
}