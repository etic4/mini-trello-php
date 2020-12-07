<?php

require_once "BoardDao.php";
require_once "Board.php";
require_once "model/column/ColumnMngr.php";

class BoardMngr {
    private $user;
    private $dao;

    public function __construct($user) {
        $this->user = $user;
        $this->dao = new BoardDao();
    }

    public function new($title, $owner): Board {
        return new Board($title, $owner, null, null, null);
    }

    public function get_own_boards(): array {
        return $this->dao->get_owner_boards($this->user);
    }

    public function get_others_boards(): array {
        return $this->dao->get_others_boards($this->user);
    }

    public function get_owner(): User {
        return $this->user;
    }

    public function get_columns($board): ColumnMngr {
        return new ColumnMngr($board);
    }

    public function add($board): Board {
        return $this->dao->insert($board);
    }

    public function update($board) {
        $this->dao->update($board);
    }

    public function delete($board) {
        $columnMgr = $this->get_columns($board);
        $columnMgr->delete_all();

        $this->dao->delete($board);
    }
}