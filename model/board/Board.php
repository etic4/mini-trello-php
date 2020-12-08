<?php

require_once "BoardModel.php";
require_once "BoardValidator.php";
require_once "model/user/User.php";
require_once "model/column/Column.php";

class Board extends BoardModel {
    private $id;
    private $title;
    private $owner;
    private $createdAt;
    private $modifiedAt;

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
    }

    public function get_owner_inst(): ?User {
        return User::get_by_id($this->owner);
    }

    public function get_columns(): array {
        return Column::get_all($this);
    }

    public function get_id() {
        return $this->id;
    }

    public function set_id($id): void {
        $this->id = $id;
    }

    public function get_title() {
        return $this->title;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function get_owner() {
        return $this->owner;
    }

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function set_modifiedDate() {
        $this->modifiedAt = new DateTime("now");
    }

    public function validate(): array {
        $validator = new BoardValidator($this);
        return $validator->validate();
    }


}