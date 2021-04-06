<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function right() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        $column->move_right();
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function left() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        $column->move_left();
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function delete() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        $cards = $column->get_cards();
        if (count($cards) == 0) {
            ColumnDao::delete($column);
            ColumnDao::decrement_following_columns_position($column);
            $this->redirect("board", "view", $column->get_board_id());
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
            ColumnDao::delete($column);
            ColumnDao::decrement_following_columns_position($column);
        }
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function add() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board);

        if (!Post::empty("title")) {
            $title = Post::get("title");
            $column = Column::new($title, $board);

            $error = new DisplayableError($column, "add");
            $error->set_messages(ColumnDao::validate($column));
            Session::set_error($error);

            if($error->is_empty()) {
                $column = ColumnDao::insert($column);
            }
        }
        $this->redirect("board", "view", $board->get_id());
    }


    // edit titre Column
    public function edit() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        if (!Post::empty("title")) {
            $title = Post::get("title");
            $error = new DisplayableError();

            if ($column->get_title() !== $title) {
                $column->set_title($title);

                $error = new DisplayableError($column, "edit");
                $error->set_messages(ColumnDao::validate($column));
                Session::set_error($error);
            }

            if ($error->is_empty()) {
                ColumnDao::update($column);
            }
        }
        $this->redirect("board", "view", $column->get_board_id());
    }
}