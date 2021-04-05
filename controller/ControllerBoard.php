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

    public function board() {
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

    // ajout nouveau Board
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
                $this->redirect("board", "board", $board->get_id());
            }
        }
        $this->redirect();
    }


    //edit titre Board
    public function edit() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board);

        if (Post::empty("title")) {
           $this->redirect();
        }

        // à ce stade on a un tout ce qu'il faut pour exécuter l'action

        $title = Post::get("title");

        // TODO: régler la nécessité d'istancier 2x
        $error = new DisplayableError();

        if ($board->get_title() != $title) {
            $board->set_title($title);

            $error = new DisplayableError($board, "edit");
            $error->set_messages(BoardDao::validate($board, $update=true));
            Session::set_error($error);
        }

        if($error->is_empty()) {
            BoardDao::update($board);
        }
        $this->redirect("board", "board", $board->get_id());
    }


    // si pas de colonne -> delete -> redirect index
    // sinon -> delete_confirm
    public function delete() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board, false);

        $columns = $board->get_columns();
        if (count($columns) == 0) {
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

        (new View("delete_confirm"))->show(array("user" => $user, "instance" => $board));

    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        $board = $this->get_object_or_redirect("id", "Board");
        $this->authorize_for_board_or_redirect($board, false);

        if(Post::isset("delete")) {
            BoardDao::delete($board);
            $this->redirect();
        }
        $this->redirect("board", "board", $board->get_id());

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


