<?php

require_once "CachedGet.php";
require_once "model/Card.php";


class Column extends CachedGet {
    use DateTrait;

    private ?string $id;
    private string $title;
    private string $position;
    private Board $board;

    private ?array $cards = null;


    public static function create_new(string $title, Board $board): Column {
        return new Column(
            $title,
            count($board->get_columns()),
            $board
        );
    }

    public function __construct(string $title, 
                                int $position, 
                                Board $board, 
                                string $id=null, 
                                ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->board = $board;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    //    GETTERS    //

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function get_position(): string {
        return $this->position;
    }

    public function get_board(): Board {
        return $this->board;
    }

    public function get_board_id(): string {
        return $this->board->get_id();
    }

    public function get_board_owner(): User {
        return $this->board->get_owner();
    }

    public function get_board_columns(): array {
        return $this->board->get_columns();
    }

    public function get_cards(): array {
        if (is_null($this->cards)) {
            $this->cards = Card::get_cards_for_column($this);
        }
        return $this->cards;
    }


    //    SETTERS    //

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function set_position(int $position): void {
        $this->position = $position;
    }

    public function is_first(): bool {
        return $this->get_position() == 0;
    }

    public function is_last(): bool {
        return $this->get_position() == count($this->get_board_columns()) - 1;
    }

    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }
        if(!Validation::is_unique_column_title($this)) {
            $errors[] = "A column with the same title already exists in this board";
        }
        return $errors;
    }

    public function is_unique_title_in_the_board(): bool {
        $title = $this->get_title();
        $columns = $this->get_board_columns();
        $count = 0;
        foreach($columns as $column) {
            if($column->get_title() === $title){
                ++$count;
            }
        }
        return $count == 0;
    }

    //    QUERIES    //

    protected static function get_instance($data, $board=null) :Column {
        list($createdAt, $modifiedAt) = self::get_dates_from_sql($data["CreatedAt"], $data["ModifiedAt"]);
        return new Column(
            $data["Title"],
            $data["Position"],
            Board::get_by_id($data["Board"]),
            $data["ID"],
            $createdAt,
            $modifiedAt
        );
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
            array_push($columns, self::get_instance($rec));
        }
        return $columns;
    }

    public static function get_columns_for_board(Board $board): array {
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
            self::add_instance_to_cache($column);
            array_push($columns, $column);
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
            "board" => $this->get_board_id()
        );

        $this->execute($sql, $params);
        $id = $this->lastInsertId();
        $this->set_id($id);
        $this->set_dates_from_db();
    }

    public function update(): void {
        $sql = 
            "UPDATE `column` 
             SET Title=:title, Position=:position, Board=:board, ModifiedAt=NOW() 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(), 
            "position" => $this->get_position(),
            "board" => $this->get_board_id()
        );

        $this->execute($sql, $params);
        $this->set_dates_from_db();
    }

    public function delete(): void {
        foreach ($this->get_cards() as $card) {
            $card->delete();
        }

        $sql = "DELETE 
                FROM `column` 
                WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }


    // MOVE COLUMN //

    public function move_left(): void {
        $pos = $this->get_position();

        if ($pos > 0) {
            $target = $this->get_board_columns()[$pos - 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            $this->update();
            $target->update();
        }
    }

    public function move_right(): void {
        $pos = $this->get_position();
        $columns = $this->get_board_columns();

        if ($pos < sizeof($columns) - 1) {
            $target = $columns[(int)$pos + 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            $this->update();
            $target->update();;
        }
    }

    public static function decrement_following_columns_position(Column $column): void {
        $sql = "UPDATE `column` 
                SET Position = Position - 1
                WHERE Board=:board 
                AND Position>:pos";
        $params = array(
            "board" => $column->get_board_id(),
            "pos" => $column->get_position()
        );
        self::execute($sql,$params);
    }

}