<?php

require_once "autoload.php";

class Column {
    use DateTrait, TitleTrait;

    private ?string $id;
    private string $position;
    private Board $board;
    private ?array $cards = null;


    public static function get_tableName(): string {
        return "`column`";
    }

    public static function get_FKName(): string {
        return "`Column`";
    }


    public static function create_new(string $title, Board $board): Column {
        return new Column(
            $title,
            count($board->get_columns()),
            $board
        );
    }

    public function __construct(string $title, int $position, Board $board, string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->board = $board;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function get_position(): string {
        return $this->position;
    }

    public function set_position(int $position): void {
        $this->position = $position;
    }

    public function get_board(): Board {
        return $this->board;
    }

    public function get_board_id(): string {
        return $this->board->get_id();
    }

    public function get_board_title(): string {
        return $this->board->get_title();
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


    // --- booleans ---

    public function is_first(): bool {
        return $this->get_position() == 0;
    }

    public function is_last(): bool {
        return $this->get_position() == count($this->get_board_columns()) - 1;
    }


    // --- Validation ---

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

    public function has_unique_title_in_board(): bool {
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

    public static function get_by_id(string $id) {
        return self::sql_select("ID", $id);
    }

    public static function get_all(Board $board): array {
        return self::sql_select_all("Board", $board->get_id());
    }

    public static function get_columns_for_board(Board $board): array {
        $columns =  self::sql_select_all("Board", $board->get_id());
        usort($columns, function(Column $c1, Column $c2) {return $c1->get_position() - $c2->get_position();});

        return $columns;
    }

    public function insert() {
        self::sql_insert();
    }

    public function update(): void {
        self::sql_update();
    }

    protected function cascade_delete() {
        return $this->get_cards();
    }

    public function delete(): void {
        self::sql_delete();
    }


    // --- move ---

    public function move_left(): void {
        $pos = $this->get_position();

        if ($pos > 0) {
            $target = $this->get_board_columns()[$pos - 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            ColumnDao::update($this);
            ColumnDao::update($target);
        }
    }

    public function move_right(): void {
        $pos = $this->get_position();
        $columns = $this->get_board_columns();

        if ($pos < sizeof($columns) - 1) {
            $target = $columns[(int)$pos + 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            ColumnDao::update($this);
            ColumnDao::update($target);
        }
    }

    public static function decrement_following_columns_position(Column $column): void {
        $sql = "UPDATE `column` 
                SET Position = Position - 1
                WHERE Board=:board 
                AND Position >:pos";
        $params = array(
            "board" => $column->get_board_id(),
            "pos" => $column->get_position()
        );
        self::execute($sql,$params);
    }

    public function __toString(): string {
        return $this->get_title();
    }
}