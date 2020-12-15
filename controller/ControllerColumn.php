<?php

require_once "framework/Controller.php";
require_once "model/Column.php";

class ControllerColumn extends Controller {
    public function index() {
        // TODO: Implement index() method.
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $colId = $_POST["id"];
            $col = Column::get_by_id($colId);
            $board = $col->get_board_inst();
            $board->move_right($col);

            $this->redirect("board", "board", $board->get_id());
        }
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $colId = $_POST["id"];
            $col = Column::get_by_id($colId);
            $board = $col->get_board_inst();
            $board->move_left($col);

            $this->redirect("board", "board", $board->get_id());
        }
    }


}