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

    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board = Board::get_by_id($_POST["id"]);
            $column = Column::create_new($title, $board);
            $column->insert();
        }
        $this->redirect("board", "board", $_POST["id"]);
    }

    //PRG ???
    public function delete() {
        $user = $this->get_user_or_redirect(); 
        if(isset($_POST['id'])) {
            $col = Column::get_by_id($_POST['id']);
            $board = $col->get_board();

            if(!isset($_POST["delete"])) {
                if (Column::get_columns_count($board) == 0) {
                    $col->delete();
                    $col->decrement_following_columns_position();
                    $this->redirect("board", "board", $board->get_id());
                }

                else {
                    $this->redirect("column", "delete_confirm", $col->get_id());
                } 
            }

            else {
                $col->delete();
                Column::decrement_following_columns_position($col);
                $this->redirect("board", "board", $board->get_id());
            }
        }
    }

    public function delete_confirm() {
        if (isset($_GET["param1"])) {
            $col = Column::get_by_id($_GET["param1"]);
            if(!is_null($col)) {
                (new View("delete_confirm"))->show(array("instance" => $col));
            }
            else {
                $this->redirect("board", "board");
            }
        }
         else {
            $this->redirect("board", "board");
         }
    }

}