<?php

require_once "ColumnModel.php";
require_once "ColumnValidator.php";
//require_once "model/Card.php";

class Column extends ColumnModel {
    private $id;
    private $title;
    private $position;
    private $createdAt;
    private $modifiedAt;
    private $board;

    protected static function get_tableName(): string {
        return "column";
    }

    public static function delete_all($board) {
        foreach ($board->get_all() as $column) {
            $column->delete();
        }
    }

    public function __construct($title, $position, $board, $id=null, $createdAt=null, $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->board = $board;
    }

    public function get_board_inst() {
        return Board::get_by_id($this->board);
    }

/*    public function get_cards(): array {
        return Card::get_all($this);
    }*/

    public function move_left() {
        /**
         * TODO implémenter column->move_left
         */
    }

    public function move_right() {
        /**
         * TODO implémenter column->move_right
         */
    }

    public function get_id() {
        return $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_position() {
        return $this->position;
    }

    public function set_position($position) {
        $this->position = $position;
    }

    public function get_createdAt() {
        return $this->createdAt;
    }

    public function get_modifiedAt() {
        return $this->modifiedAt;
    }

    public function set_modifiedDate() {
        $this->modifiedAt = new DateTime("now");
    }

    public function get_board() {
        return $this->board;
    }

    public function validate(): array {
        $columnValidator = new ColumnValidator($this);
        return $columnValidator->validate();
    }
}