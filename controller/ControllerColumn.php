<?php

require_once "framework/Controller.php";
require_once "model/Column.php";
require_once "model/User.php";

class ControllerColumn extends Controller {

    public function index() {
        
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_right();
            $this->redirect("board", "board", $column->get_board_id());
        }
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_left();
            $this->redirect("board", "board", $column->get_board_id());
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $user = $this->get_user_or_redirect(); 
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

    /*
    public function add() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $action = $_POST["action"];
            $column = Column::create_new($title, $board_id);
            $errors = $column->validate($action);

            if(empty($errors)) {
                $column = $column->insert();
                $this->redirect("board", "board", $board_id);
            }

        }
        return $errors;
    }

    // edit titre Column
    public function edit() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $column_id = $_POST["id"];
            $action = $_POST["action"];
            $column = Column::get_by_id($column_id);
            $column->set_title($title);
            $errors = $column->validate($action);

            if(empty($errors)) {
                $column = $column->insert();
                $this->redirect("board", "board", $column->get_board_id());
            }

        }
        return $errors;
    }
    */

}