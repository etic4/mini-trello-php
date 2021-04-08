<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function delete_confirm() {
        $column = $this->get_object_or_redirect("param1", "Column");
        $user = $this->authorize_for_board_or_redirect($column->get_board());

        $cards = $column->get_cards();
        if (count($cards)) {
            (new View("delete_confirm"))->show(array(
                "user" => $user,
                "cancel_url" => "board/view/".$column->get_board_id(),
                "instance" => $column
            ));
        }
    }

    public function delete() {
        $column = $this->get_object_or_redirect("id", "Column");
        $this->authorize_for_board_or_redirect($column->get_board());

        if (Post::get("confirm") == "true" || count($column->get_cards()) == 0) {
            ColumnDao::delete($column);
            ColumnDao::decrement_following_columns_position($column);
            $this->redirect("board", "view", $column->get_board_id());
        } else {
            $this->redirect("column", "delete_confirm", $column->get_id());
        }
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
        if (Request::is_get()) {
            $column = $this->get_object_or_redirect("param1", "Column");
            $user = $this->authorize_for_board_or_redirect($column->get_board());

            (new View("column_edit"))->show(array(
                    "user" => $user,
                    "column" => $column,
                    "breadcrumb" => new BreadCrumb(array($column->get_board()), "Edit column title"),
                    "errors" => Session::get_error()
                )
            );
        }
        else {
            $column = $this->get_object_or_redirect("id", "Column");
            $this->authorize_for_board_or_redirect($column->get_board());

            if (Post::empty("title") || Post::get("title") == $column->get_title()) {
                $this->redirect("board", "view", $column->get_board_id());
            }

            $column->set_title(Post::get("title"));

            $error = new DisplayableError();
            $error->set_messages(ColumnDao::validate($column));
            Session::set_error($error);

            if ($error->is_empty()) {
                ColumnDao::update($column);
                $this->redirect("board", "view", $column->get_board_id());
            }
            $this->redirect("board", "view", $column->get_board_id());
        }
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

}