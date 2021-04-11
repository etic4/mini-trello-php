<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $board = $this->get_or_redirect("Board");
        $this->authorized_or_redirect(Permissions::view($board));

        if (!Post::empty("title")) {
            $title = Post::get("title");
            $column = Column::new($title, $board);

            $error = new DisplayableError($column, "add");
            $error->set_messages(ColumnDao::validate($column));
            Session::set_error($error);

            if($error->is_empty()) {
                ColumnDao::insert($column);
            }
        }
        $this->redirect("board", "view", $board->get_id());
    }


    // edit titre Column
    public function edit() {
        $user = $this->get_user_or_redirect();
        $column = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::edit($column));

        if (Post::isset("confirm")) {
            if (Post::empty("title") || Post::get("title") == $column->get_title()) {
                $this->redirect("board", "view", $column->get_board_id());
            }

            $column->set_title(Post::get("title"));

            $error = new DisplayableError();
            $error->set_messages(ColumnDao::validate($column));
            Session::set_error($error);

            if ($error->is_empty()) {
                $column->set_modifiedAt(new DateTime());
                ColumnDao::update($column);
                $this->redirect("board", "view", $column->get_board_id());
            }
            $this->redirect("column", "edit", $column->get_id());
        }

        (new View("column_edit"))->show(array(
                "user" => $user,
                "column" => $column,
                "breadcrumb" => new BreadCrumb(array($column->get_board()), "Edit column title"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        $column = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::delete($column));

        if (Post::isset("confirm") || count($column->get_cards()) == 0) {
            ColumnDao::delete($column);
            ColumnDao::decrement_following_columns_position($column);
            $this->redirect("board", "view", $column->get_board_id());
        }
        $this->redirect("column", "delete_confirm", $column->get_id());
    }

    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        $column = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::delete($column));

        (new View("delete_confirm"))->show(array(
            "user" => $user,
            "cancel_url" => "board/view/".$column->get_board_id(),
            "instance" => $column
        ));
    }


    /* --- Moves --- */

    public function right() {
        $user = $this->get_user_or_redirect();
        $column = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($column->get_board()));

        $column->move_right();
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function left() {
        $user = $this->get_user_or_redirect();
        $column = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($column->get_board()));

        $column->move_left();
        $this->redirect("board", "view", $column->get_board_id());

    }

}