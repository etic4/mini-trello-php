<?php

require_once "autoload.php";

class ControllerColumn extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $board = $this->get_or_redirect_post("Board", "board_id");
        $this->authorized_or_redirect(Permissions::view($board));

        if (!Post::empty("column_title")) {
            $column_title = Post::get("column_title");

            $error = new DisplayableError();
            $error->set_messages((new ColumnValidation())->validate_add($column_title, $board));
            Session::set_error($error);

            if($error->is_empty()) {
                $column = Column::new($column_title, $board);
                ColumnDao::insert($column);
            }
        }
        $this->redirect("board", "view", $board->get_id());
    }


    // edit titre Column
    public function edit() {
        $column = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::edit($column));

        $column_title = Post::get("column_title", $column->get_title());

        if (Post::get("confirm") == "true") {
            $error = new DisplayableError();
            $error->set_messages((new ColumnValidation())->validate_edit($column_title, $column));
            Session::set_error($error);

            if ($error->is_empty()) {
                $column->set_title($column_title);
                $column->set_modifiedAt(new DateTime());
                ColumnDao::update($column);
                $this->redirect("board", "view", $column->get_id()());
            }
            $this->redirect("column", "edit", $column->get_id());
        }

        (new View("column_edit"))->show(array(
                "user" => $user,
                "column_id" => $column->get_id(),
                "column_title" => $column_title,
                "board_id" => $column->get_board_id(),
                "breadcrumb" => new BreadCrumb(array($column->get_board()), "Edit column title"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $column = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::delete($column));

        if (Post::get("confirm") == "true" || count($column->get_cards()) == 0) {
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

    /* --- Services ---*/

    public function column_title_is_unique_service() {
        if (!Post::all_non_empty("column_title", "board_id")) {
            echo "false";
            die;
        }

        $column_title = Post::get("column_title");

        if (!Post::empty("column_id")) {
            $column = $this->get_or_redirect_post("Column", "column_id");
            $this->authorized_or_redirect(Permissions::edit($column));

            $errors = (new ColumnValidation())->validate_edit($column_title, $column);
        } else {
            $board = $this->get_or_redirect_post("Board", "board_id");
            $this->authorized_or_redirect(Permissions::view($board));

            $errors = (new ColumnValidation())->validate_add($column_title, $board);
        }

        echo count($errors) == 0 ? "true" : "false";
    }

    public function needs_delete_confirm_service() {
        $column = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::delete($column));

        echo  $this->can_delete($column) ? "false" : "true";
    }

    public function update_columns_positions_service() {
        $board = $this->get_or_redirect("Board", "board_id", "");
        $this->authorized_or_redirect(Permissions::view($board));

        if (!Post::empty("columns_list")) {
            ColumnDao::update_columns_position(Post::get("columns_list"));
        }
    }

    private function can_delete(Column $column): bool {
        return count($column->get_cards()) == 0;
    }
}