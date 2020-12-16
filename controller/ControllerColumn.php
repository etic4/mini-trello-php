<?php

require_once "framework/Controller.php";
require_once "model/Column.php";
require_once "model/User.php";

class ControllerColumn extends Controller {

    public function index() {
        // TODO
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $col = Column::get_by_id($_POST["id"]);
            $board = $col->get_board_inst();
            $board->move_right($col);

            $this->redirect("board", "board", $board->get_id());
        }
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $col = Column::get_by_id($_POST["id"]);
            $board = $col->get_board_inst();
            $board->move_left($col);

            $this->redirect("board", "board", $board->get_id());
        }
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board = $_POST["id"];
            $author = $user->get_id();
            $column = Column::create_new($title, $author, $board); 
            $column->insert();
        }
        $this->redirect("board", "board", $board);
    }

}