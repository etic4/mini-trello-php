<?php

require_once "framework/Controller.php";
require_once "model/Column.php";
require_once "model/User.php";
require_once "ErrorTrait.php";

class ControllerColumn extends Controller {
    use ErrorTrait;

    public function index() {
        
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function right() {
        $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_right();
            $this->redirect("board", "board", $column->get_board_id());
        }
    }

    public function left() {
        $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_left();
            $this->redirect("board", "board", $column->get_board_id());
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $this->get_user_or_redirect();
        if(isset($_POST['id'])) {
            $column = Column::get_by_id($_POST['id']);

            if (Card::get_cards_count($column) == 0) {
                $column->delete();
                Column::decrement_following_columns_position($column);
                $this->redirect("board", "board", $column->get_board_id());
            } else {
                $this->redirect("column", "delete_confirm", $column->get_id());
            }
        }
    }

    public function delete_confirm() {
        if (isset($_GET["param1"])) {
            $column = Column::get_by_id($_GET["param1"]);
            if(!is_null($column)) {
                (new View("delete_confirm"))->show(array("instance" => $column));
                die;
            }
        }
        $this->redirect("board", "index");
    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        if(isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            if(isset($_POST["delete"])) {
                $column->delete();
                Column::decrement_following_columns_position($column);
            }
            $this->redirect("board", "board",   $column->get_board_id());
        }
        $this->redirect("board", "index");
    }

    public function add() {
        $this->get_user_or_redirect();

        if (isset($_POST["id"]) && !empty($_POST["title"])) {
            $board = Board::get_by_id($_POST["id"]);
            $title = $_POST["title"];

            $column = Column::create_new($title, $board);
            $this->set_errors($column->validate("add"));

            if($this->no_errors()) {
                $column->insert();
            }
            $this->redirect("board", "board", $_POST["id"]);
        }
        $this->redirect("board");
    }

    // edit titre Column
    public function edit() {
        $this->get_user_or_redirect();

        if (isset($_POST["id"]) && !empty($_POST["title"])) {
            $id = $_POST["id"];
            $title = $_POST["title"];
            $column = Column::get_by_id($id);
            $column->set_title($title);
            $this->set_errors($column->validate("edit"));

            if($this->no_errors()) {
                $column->insert();
            }
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect("board");
    }
}