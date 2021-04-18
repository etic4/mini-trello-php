<?php

require_once "autoload.php";

class Column {
    private string $title;
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
        $this->createdAt = DateUtils::now_if_null($createdAt);
        $this->modifiedAt = $modifiedAt;
    }


    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
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

    public function set_modifiedAt(DateTime $dateTime) {
        $this->modifiedAt = $dateTime;
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