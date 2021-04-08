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

    public function view() {
        $board = $this->get_object_or_redirect("param1", "Board");
        $user = $this->authorize_for_board_or_redirect($board);

        (new View("board"))->show(array(
                "user" => $user,
                "board" => $board,
                "breadcrumb" => new BreadCrumb(array($board)),
                "errors" => Session::get_error()
            )
        );
    }

    public function add() {
        $user = $this->get_user_or_redirect();

        if (!Post::empty("title")) {
            $title = Post::get("title");
            $board = new Board($title, $user, null, new DateTime(), null);

            $error = new DisplayableError($board, "add");
            $error->set_messages(BoardDao::validate($board));
            Session::set_error($error);

            if($error->is_empty()) {
                $board = BoardDao::insert($board);
                $this->redirect("board", "view", $board->get_id());
            }
        }
        $this->redirect();
    }

    public function edit() {
        if (Request::is_get()) {
            $board = $this->get_object_or_redirect("param1", "Board");
            $user = $this->authorize_for_board_or_redirect($board);

            (new View("board_edit"))->show(array(
                    "user" => $user,
                    "board" => $board,
                    "breadcrumb" => new BreadCrumb(array($board), "Edit title"),
                    "errors" => Session::get_error()
                )
            );
        }
        else {
            $board = $this->get_object_or_redirect("id", "Board");
            $this->authorize_for_board_or_redirect($board);

            if (Post::empty("title") || Post::get("title") == $board->get_title()) {
                $this->redirect("board", "view", $board->get_id());
            }

            $board->set_title(Post::get("title"));

            $error = new DisplayableError($board, "edit");
            $error->set_messages(BoardDao::validate($board, $update=true));
            Session::set_error($error);

            if($error->is_empty()) {
                BoardDao::update($board);
                $this->redirect("board", "view", $board->get_id());
            }
            $this->redirect("board", "edit", $board->get_id());
        }
    }

    public function delete() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board, false);

        $columns = $board->get_columns();
        if (Post::isset("confirm") || count($columns) == 0) {
            BoardDao::delete($board);
            $this->redirect();
        } else {
            $this->redirect("board", "delete_confirm", $board->get_id());
        }
    }

    //mise en place de view_delete_confirm
    public function delete_confirm() {
        $board = $this->get_object_or_redirect("param1", "Board");
        $user = $this->authorize_for_board_or_redirect($board, false);

        (new View("delete_confirm"))->show(array(
            "user" => $user,
            "cancel_url" => "board/view/".$board->get_id(),
            "instance" => $board));

    }

    // Colaborateurs
    public function collaborators() {
        $board = $this->get_object_or_redirect("param1", "Board");
        $user = $this->authorize_for_board_or_redirect($board, false);

        (new View("collaborators"))->show(
            array(
                "user" => $user,
                "breadcrumb" => new BreadCrumb(array($board), "Collaborators"),
                "board" => $board)
        );
    }
}


