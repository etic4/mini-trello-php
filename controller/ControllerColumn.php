<?php

require_once "framework/Controller.php";
require_once "model/Column.php";
require_once "model/User.php";
require_once "ValidationError.php";
require_once "CtrlTools.php";

class ControllerColumn extends Controller {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function right() {
        $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_right();
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect();
    }

    public function left() {
        $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $column = Column::get_by_id($_POST["id"]);
            $column->move_left();
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $this->get_user_or_redirect();
        if(isset($_POST['id'])) {
            $column_id = $_POST['id'];
            $column = Column::get_by_id($column_id);
            $cards = $column->get_cards();

            if (count($cards) == 0) {
                $column->delete();
                Column::decrement_following_columns_position($column);
                $this->redirect("board", "board", $column->get_board_id());
            } else {
                $this->redirect("column", "delete_confirm", $column->get_id());
            }
        } else {
            $this->redirect();
        }
    }

    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $column_id = $_GET["param1"];
            $column = Column::get_by_id($column_id);

            if(!is_null($column) && $user) {
                $cards = $column->get_cards();
                if (count($cards)) {
                    (new View("delete_confirm"))->show(array(
                        "user"=>$user, 
                        "instance"=>$column
                        ));
                    die;
                }
            }
        }
        $this->redirect();
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
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $this->get_user_or_redirect();

        if (isset($_POST["id"])) {
            if (!empty($_POST["title"])) {
                $board_id = $_POST["id"];
                $board = Board::get_by_id($board_id);
                $title = $_POST["title"];
                $column = Column::create_new($title, $board);

                $error = new ValidationError($column, "add");
                echo $column->has_unique_title_in_board();
                $error->set_messages_and_add_to_session($column->validate());

                if($error->is_empty()) {
                    $column->insert();
                }
            }
            $this->redirect("board", "board", $_POST["id"]);
        }
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // edit titre Column
    public function edit() {
        $this->get_user_or_redirect();
        $error = new ValidationError();

        if (isset($_POST["id"]) && !empty($_POST["title"])) {
            $id = $_POST["id"];
            $title = $_POST["title"];
            $column = Column::get_by_id($id);

            if ($column->get_title() !== $title) {
                $column->set_title($title);
                $error = new ValidationError($column, "edit");
                $error->set_messages_and_add_to_session($column->validate());
            }

            if ($error->is_empty()) {
                $column->update();
            }
            $this->redirect("board", "board", $column->get_board_id());
        }
        $this->redirect();
    }
}