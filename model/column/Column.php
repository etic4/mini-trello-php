<?php

require_once "ColumnValidator.php";

class Column {
    private $id;
    private $title;
    private $position;
    private $createdAt;
    private $modifiedAt;
    private $board;

    public function __construct($title, $position, $board, $id=null, $createdAt=null, $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->board = $board;
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

    public function set_title($title) {
        $this->title = $title;
    }

    public function get_position() {
        return $this->position;
    }

    public function set_position($position) {
        $this->position = $position;
    }

    public function get_created_at() {
        return $this->createdAt;
    }


    public function get_modified_at() {
        return $this->modifiedAt;
    }

    public function set_modified_at($modifiedAt) {
        $this->modifiedAt = $modifiedAt;
    }

    public function get_board() {
        return $this->board;
    }

    public function validate(): array {
        $columnValidator = new ColumnValidator($this);
        return $columnValidator->validate();
    }



}