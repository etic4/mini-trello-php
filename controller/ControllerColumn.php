<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $board = $this->get_or_redirect_post("Board", "id");
        $this->authorized_or_redirect(Permissions::view($board));

        if (!Post::empty("title")) {
            $title = Post::get("title");

            $error = new DisplayableError();
            $error->set_messages(ColumnDao::validate($title));
            Session::set_error($error);

            if($error->is_empty()) {
                $column = Column::new($title, $board);
                ColumnDao::insert($column);
            }
        }
        $this->redirect("board", "view", $board->get_id());
    }


    // edit titre Column
    public function edit() {
        $column = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::edit($column));

        $title = Post::get("title", $column->get_title());

        if (Post::isset("confirm")) {
            $error = new DisplayableError();
            $error->set_messages(ColumnValidation::get_inst()->validate_add($title, $column));
            Session::set_error($error);

            if ($error->is_empty()) {
                $column->set_title($title);
                $column->set_modifiedAt(new DateTime());
                ColumnDao::update($column);
                $this->redirect("board", "view", $column->get_board_id());
            }
            $this->redirect("column", "edit", $column->get_id());
        }

        (new View("column_edit"))->show(array(
                "user" => $user,
                "id" => $column->get_id(),
                "title" => $title,
                "breadcrumb" => new BreadCrumb(array($column->get_board()), "Edit column title"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $column = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::delete($column));

        if (Post::isset("confirm") || count($column->get_cards()) == 0) {
            ColumnDao::delete($column);
            ColumnDao::decrement_following_columns_position($column);
            $this->redirect("board", "view", $column->get_board_id());
        }
        $this->redirect("column", "delete_confirm", $column->get_id());
    }

    public function delete_confirm() {
        $column = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::delete($column));

        (new View("delete_confirm"))->show(array(
            "user" => $user,
            "cancel_url" => "board/view/".$column->get_board_id(),
            "instance" => $column
        ));
    }


    /* --- Moves --- */

    public function right() {
        $column = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::view($column));

        $column->move_right();
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function left() {
        $column = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::view($column));

        $column->move_left();
        $this->redirect("board", "view", $column->get_board_id());

    }

}