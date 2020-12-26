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
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $board = Board::get_by_id($board_id);
            $column = Column::create_new($title, $board);
            $errors = $column->validate();

            if(empty($errors)) {
                $column = $column->insert();
                $this->redirect("board", "board", $board->get_id());
            }
        }
        $this->redirect("board", "board", $board->get_id());
    }
/*
    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $board = Board::get_by_id($board_id);
            $column = Column::create_new($title, $board);
            $column->insert();
        }
        $this->redirect("board", "board", $board_id);
    }
*/
    //PRG ???
    public function delete() {
        $user = $this->get_user_or_redirect(); 
        if(isset($_POST['id'])) {
            $column_id = $_POST['id'];
            $instance = Column::get_by_id($column_id);
            $board_id = $instance->get_board_id(); 

            if(!isset($_POST["delete"])) {

                if (count($instance->get_cards()) == 0) { 
                    $instance->delete();
                    $instance->decrement_previous_columns_position();
                    $this->redirect("board", "board", $board_id);        
                }

                else {
                    $this->redirect("column", "delete_confirm", $instance->get_id());
                } 
            }

            else {
                $instance->delete();
                $instance->decrement_previous_columns_position();
                $this->redirect("board", "board", $board_id);        
            }
        }
    }

    public function delete_confirm() {
        if (isset($_GET["param1"])) {
            $column_id = $_GET["param1"];
            $instance = Column::get_by_id($column_id);
            if(!is_null($instance)) {
                (new View("delete_confirm"))->show(array("instance" => $instance));
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