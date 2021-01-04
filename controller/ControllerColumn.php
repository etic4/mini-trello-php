<?php

require_once "framework/Controller.php";
require_once "model/Column.php";
require_once "model/User.php";
require_once "ValidationError.php";

class ControllerColumn extends Controller {

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
            $column_id = $_POST['id'];
            $column = Column::get_by_id($column_id);
            $cards = Card::get_cards_count($column);

            if (count($cards) == 0) {
                $column->delete();
                Column::decrement_following_columns_position($column);
                $this->redirect("board", "board", $column->get_board_id());
            } 
            
            else {
                $this->redirect("column", "delete_confirm", $column->get_id());
            }
        }
        else {
            $this->redirect("board", "index");
        }
    }

    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $column_id = $_GET["param1"];
            $column = Column::get_by_id($column_id);
            if(!is_null($column) && $user) {
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
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect("board", "index");
    }

    public function add() {
        $this->get_user_or_redirect();

        if (isset($_POST["id"]) && !empty($_POST["title"])) {
            $board_id = $_POST["id"];
            $board = Board::get_by_id($board_id);
            $title = $_POST["title"];
            $column = Column::create_new($title, $board);

            $error = new ValidationError($column, "add");
            $error->set_messages($column->validate());
            $error->add_to_session();

            if($error->is_empty()) {
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

            $error = new ValidationError($column, "edit");
            $error->set_messages($column->validate());
            $error->add_to_session();

            if($error->is_empty()) {
                $column->update();
            }
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect("board");
    }
}