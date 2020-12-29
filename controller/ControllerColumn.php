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
            $col->move_right();
            $this->redirect("board", "board", $col->get_board()->get_id());
        }
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $col = Column::get_by_id($_POST["id"]);
            $col->move_left();

            $this->redirect("board", "board", $col->get_board()->get_id());
        }
    }

    

    //PRG ???
    public function delete() {
        $user = $this->get_user_or_redirect(); 
        if(isset($_POST['id'])) {
            $col = Column::get_by_id($_POST['id']);

            if (Card::get_cards_count($col) == 0) {
                $col->delete();
                Column::decrement_following_columns_position($col);
                $this->redirect("board", "board", $col->get_board_id());
            } else {
                $this->redirect("column", "delete_confirm", $col->get_id());
            }
        }
    }

    public function delete_confirm() {
        if (isset($_GET["param1"])) {
            $col = Column::get_by_id($_GET["param1"]);
            if(!is_null($col)) {
                (new View("delete_confirm"))->show(array("instance" => $col));
                die;
            }
        }
        $this->redirect("board", "index");
    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        if(isset($_POST["id"])) {
            $col = Column::get_by_id($_POST["id"]);
            if(isset($_POST["delete"])) {
                $col->delete();
            }
            $this->redirect("board", "board",   $col->get_board_id());
        }
        $this->redirect("board", "index");
    }
}