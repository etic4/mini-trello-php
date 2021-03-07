<?php

require_once "autoload.php";

class ControllerColumn extends EController {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function right() {
        list($_, $column) = $this->authorize_or_redirect("id", "Column");

        $column->move_right();
        $this->redirect("board", "board", $column->get_board_id());

    }

    public function left() {
        list($_, $column) = $this->authorize_or_redirect("id", "Column");

        $column->move_left();
        $this->redirect("board", "board", $column->get_board_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        list($_, $column) = $this->authorize_or_redirect("id", "Column");

        $cards = $column->get_cards();
        if (count($cards) == 0) {
            $column->delete();
            Column::decrement_following_columns_position($column);
            $this->redirect("board", "board", $column->get_board_id());
        } else {
            $this->redirect("column", "delete_confirm", $column->get_id());
        }

    }

    public function delete_confirm() {
        list($user, $column) = $this->authorize_or_redirect("param1", "Column");

        $cards = $column->get_cards();
        if (count($cards)) {
            (new View("delete_confirm"))->show(array(
                "user"=>$user,
                "instance"=>$column
            ));
        }
    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        list($_, $column) = $this->authorize_or_redirect("id", "Column");

        if(isset($_POST["delete"])) {
            $column->delete();
            Column::decrement_following_columns_position($column);
        }
        $this->redirect("board", "board", $column->get_board_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        list($_, $board) = $this->authorize_or_redirect("id", "Board");

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $column = Column::create_new($title, $board);

            $error = new ValidationError($column, "add");
            $error->set_messages_and_add_to_session($column->validate());

            if($error->is_empty()) {
                $column->insert();
            }
        }
        $this->redirect("board", "board", $board->get_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // edit titre Column
    public function edit() {
        list($_, $column) = $this->authorize_or_redirect("id", "Column");

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $error = new ValidationError();

            if ($column->get_title() !== $title) {
                $column->set_title($title);
                $error = new ValidationError($column, "edit");
                $error->set_messages_and_add_to_session($column->validate());
            }

            if ($error->is_empty()) {
                $column->update();
            }
        }
        $this->redirect("board", "board", $column->get_board_id());
    }
}