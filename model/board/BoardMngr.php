<?php

require_once "BoardDao.php";
require_once "Board.php";
require_once "model/column/ColumnMngr.php";
require_once "model/user/UserMngr.php";

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

    // Prépare la liste pour l'affichage
    private function get_boards_for_view($board_array): array {
        $boards = [];
        foreach ($board_array as $board) {
            $user = $this->get_owner($board);
            $boards[] = array("id"=>$board->get_id(), "title"=>$board->get_title(), "fullName"=>$user->get_fullName());
        }
        return $boards;
    }

    public function get_own_boards(): array {
        return $this->get_boards_for_view($this->dao->get_owner_boards($this->user));
    }

    public function get_others_boards(): array {
        return $this->get_boards_for_view($this->dao->get_others_boards($this->user));
    }

    public function get_owner($board): User {
        $userMngr = new UserMngr();
        return $userMngr->get_by_id($board->get_owner_id());
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