<?php

require_once "autoload.php";


class ControllerBoard extends EController {

    public function index() {
        if(isset($_GET["param1"])) {
            $this->redirect();
        }

        $user = $this->get_user_or_false();

        (new View("boardlist"))->show(array(
            "user"=>$user,
            "errors" => ValidationError::get_error_and_reset()
            )
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function board() {
        list($user, $board) = $this->authorize_or_redirect("param1", "Board");

        (new View("board"))->show(array(
                "user" => $user,
                "board" => $board,
                "breadcrumb" => new BreadCrumb(array($board)),
                "errors" => ValidationError::get_error_and_reset()
            )
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ajout nouveau Board
    public function add() {
        $user = $this->get_user_or_redirect();

        if (!Post::empty("title")) {
            $title = Post::get("title");
            $board = new Board($title, $user, null, new DateTime(), null);

            $error = new ValidationError($board, "add");
            $error->set_messages_and_add_to_session($board->validate());

            if($error->is_empty()) {
                $board->insert();
                $this->redirect("board", "board", $board->get_id());
            }
        }
        $this->redirect();
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //edit titre Board
    public function edit() {
        list($_, $board) = $this->authorize_or_redirect("id", "Board");

        if (Post::empty("title")) {
           $this->redirect();
        }

        // à ce stade on a un tout ce qu'il faut pour exécuter l'action

        $title = Post::get("title");

        // TODO: régler la nécessité d'istancier 2x
        $error = new ValidationError();

        if ($board->get_title() != $title) {
            $board->set_title($title);
            $error = new ValidationError($board, "edit");
            $error->set_messages_and_add_to_session($board->validate());
        }

        if($error->is_empty()) {
            $board->update();
        }
        $this->redirect("board", "board", $board->get_id());
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // si pas de colonne -> delete -> redirect index
    // sinon -> delete_confirm
    public function delete() {
        list($_, $board) = $this->authorize_or_redirect("id", "Board", false);

        $columns = $board->get_columns();
        if (count($columns) == 0) {
            $board->delete();
            $this->redirect();
        } else {
            $this->redirect("board", "delete_confirm", $board->get_id());
        }
    }

    //mise en place de view_delete_confirm
    public function delete_confirm() {
        list($user, $board) = $this->authorize_or_redirect("param1", "Board", false);

        (new View("delete_confirm"))->show(array("user" => $user, "instance" => $board));

    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        list($_, $board) = $this->authorize_or_redirect("id", "Board", false);

        if(Post::isset("delete")) {
            $board->delete();
            $this->redirect();
        }
        $this->redirect("board", "board", $board->get_id());

    }

    // Colaborateurs

    public function collaborators() {
        list($user, $board) = $this->authorize_or_redirect("param1", "Board", false);

        (new View("collaborators"))->show(
            array(
                "user" => $user,
                "breadcrumb" => new BreadCrumb(array($board), "Collaborators"),
                "board" => $board)
        );
    }
}


