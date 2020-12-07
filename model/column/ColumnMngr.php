<?php

require_once "Column.php";
require_once "ColumnDao.php";

class ColumnMngr {
    private $board;
    private $dao;

    public function __construct($board) {
        $this->board = $board;
        $this->dao = new ColumnDao();
    }

    public function new($title): Column {
        return new Column($title, null, null, null);
    }

    public function get_board(): Board {
        return $this->board;
    }

    public function get_columns(): array {
        $this->dao->get_all($this->board);
    }

    public function get_cards($column): CardMngr {
        return new CardMngr($column);
    }

    public function add($board): Board {
        return $this->dao->insert($board);
    }

    public function update($board) {
        $this->dao->update($board);
    }

    public function delete_all() {
        foreach ($this->get_columns() as $column) {
            $this->delete($column);
        }
    }

    public function delete($column) {
        $cardMngr = $this->get_cards($column);
        $cardMngr->delete_all();

        $this->dao->delete($column);
    }
}