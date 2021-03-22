<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function right() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        $column->move_right();
        $this->redirect("board", "board", $column->get_board_id());

    }

    public function left() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        $column->move_left();
        $this->redirect("board", "board", $column->get_board_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

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
        $column = $this->get_object_or_redirect("param1", "Column");
        $user = $this->authorize_for_board_or_redirect($column->get_board());

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
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        if(Post::isset("delete")) {
            $column->delete();
            Column::decrement_following_columns_position($column);
        }
        $this->redirect("board", "board", $column->get_board_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board);

        if (!Post::empty("title")) {
            $title = Post::get("title");
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
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        if (!Post::empty("title")) {
            $title = Post::get("title");
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