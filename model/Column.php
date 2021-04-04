<?php

require_once "autoload.php";

class Column {
    use DateTrait, TitleTrait;

    private ?string $id;
    private string $position;
    private Board $board;
    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;

    private array $cards;


    public static function new(string $title, Board $board): Column {
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
        $this->createdAt = self::now_if_null($createdAt);
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

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): ?DateTime {
        return $this->modifiedAt;
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
        if (!isset($this->cards)) {
            $this->cards = CardDao::get_cards($this);
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


    public function is_unique_title_in_board(): bool {
        $title_filter = fn($col) =>  $col->get_title() == $this->get_title() && $col->get_id() != $this->get_id();
        $titles = array_filter($this->get_board_columns(), $title_filter);

        return count($titles) == 0;
    }

    public function validate($update=false): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }

        if (!$update || ColumnDao::title_has_changed($this)) {
            if (!$this->is_unique_title_in_board()){
                $errors[] = "Title already exists in this board";
            }
        }
        return $errors;
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


    public function __toString(): string {
        return $this->get_title();
    }
}