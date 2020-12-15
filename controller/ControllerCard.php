<?php

require_once "framework/Controller.php";
require_once "model/Card.php";

class ControllerCard extends Controller {

    public function index() {
        // TODO: Implement index() method.
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_left($card);
            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_right($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }

    }

    public function up() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_up($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }

    public function down() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_down($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }
}