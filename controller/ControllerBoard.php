<?php

require_once "autoload.php";


class ControllerBoard extends ExtendedController {
    public function index() {
        if(Get::isset("param1")) {
            $this->redirect();
        }

        $user = $this->get_user_or_false();

        (new View("boardlist"))->show(array(
            "user" => $user,
            "errors" => Session::get_error()
            )
        );
    }

    public function add() {
        $user = $this->authorized_or_redirect(Permissions::add("Board"));

        if (!Post::empty("board_title")) {
            $title = Post::get("board_title");

            $error = new DisplayableError();

            $error->set_messages((new BoardValidation())->validate_add($title));
            Session::set_error($error);

            if($error->is_empty()) {
                $board = new Board($title, $user);
                $board = BoardDao::insert($board);
                $this->redirect("board", "view", $board->get_id());
            }
        }
        $this->redirect();
    }

    public function view() {
        $board = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::view($board));

        (new View("board"))->show(array(
                "user" => $user,
                "board" => $board,
                "breadcrumb" => new BreadCrumb(array($board)),
                "errors" => Session::get_error()
            )
        );
    }

    public function edit() {
        $board = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::edit($board));

        $board_title = Post::get("board_title", $board->get_title());

        if (Post::get("confirm") == "true") {
            if (empty($board_title) || $board_title == $board->get_title()) {
                $this->redirect("board", "view", $board->get_id());
            }

            $error = new DisplayableError();
            $error->set_messages((new BoardValidation())->validate_edit($board_title, $board));
            Session::set_error($error);

            if($error->is_empty()) {
                $board->set_title($board_title);
                $board->set_modifiedAt(new DateTime());
                BoardDao::update($board);
                $this->redirect("board", "view", $board->get_id());
            }
            $this->redirect("board", "edit", $board->get_id());
        }

        (new View("board_edit"))->show(array(
                "user" => $user,
                "id" => $board->get_id(),
                "board_title" => $board_title,
                "breadcrumb" => new BreadCrumb(array($board), "Edit title"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $board = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::delete($board));

        if (Post::get("confirm") == "true" || $this->can_delete($board)) {
            BoardDao::delete($board);
            $this->redirect();
        }

        $this->redirect("board", "delete_confirm", $board->get_id());
    }

    public function delete_confirm() {
        $board = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::delete($board));

        (new View("delete_confirm"))->show(array(
            "user" => $user,
            "cancel_url" => "board/view/".$board->get_id(),
            "instance" => $board));
    }


    /*   --- Collaborators ---   */

    public function collaborators() {
        $board = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::is_owner($board));

        (new View("collaborators"))->show(
            array(
                "user" => $user,
                "breadcrumb" => new BreadCrumb(array($board), "Collaborators"),
                "board" => $board)
        );
    }

    /* --- Services --- */

    public function board_title_is_unique_service() {
        if (Post::empty("board_title")) {
            echo "false";
            die;
        }

        $board_title = Post::get("board_title");

        if (!Post::empty("board_id")) {
            $board = $this->get_or_redirect_post("Board", "board_id");
            $this->authorized_or_redirect(Permissions::edit($board));

            $errors = (new BoardValidation())->validate_edit($board_title, $board);
        } else {
            $this->authorized_or_redirect(Permissions::add("Board"));

            $errors = (new BoardValidation())->validate_add($board_title);
        }

        echo count($errors) == 0 ? "true" : "false";
    }

    public function needs_delete_confirm_service() {
        $board = $this->get_or_redirect_default();
        $this->authorized_or_redirect(Permissions::delete($board));

        echo  $this->can_delete($board) ? "false" : "true";
    }

    public function can_delete(Board $board): bool {
        return count($board->get_columns()) == 0;
    }
}


