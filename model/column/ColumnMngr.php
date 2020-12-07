<?php

require_once "ColumnDao.php";

class ColumnMngr {
    private $board;
    private $dao;

    public function __construct($board) {
        $this->board = $board;
        $this->dao = new BoardDao();
    }

    public function new($title): Board {
        return new Column($title, null, null, null);
    }

    public function get_columns(): ColumnMngr {
        $this->dao->get_columns($this->board);
    }

    public function add($board): Board {
        return $this->dao->insert($board);
    }

    public function update($board) {
        $this->dao->update($board);
    }

    public function delete_all() {

    }

    public function delete($board) {
        $columnMgr = $this->get_columns($board);
        $columnMgr->delete_all();

        $this->dao->delete($board);
    }
}