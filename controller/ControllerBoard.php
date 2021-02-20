<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Board.php";
require_once "CtrlTools.php";
require_once "view/ViewTools.php";
require_once "ValidationError.php";
require_once "Authorize.php";


class ControllerBoard extends Controller {
    use Authorize;

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
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_GET, "param1", "Board");
        $this->board_authorize_or_redirect($user, $board, "view");

        (new View("board"))->show(array(
                "user" => $user,
                "board" => $board,
                "errors" => ValidationError::get_error_and_reset()
            )
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ajout nouveau Board
    public function add() {
        $user = $this->get_user_or_redirect();

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
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
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_GET, "param1", "Board");
        $this->board_authorize_or_redirect($user, $board, "edit" );

        if (empty($_POST["title"])) {
           $this->redirect();
        }

        // à ce stade on a un tout ce qu'il faut pour exécuter l'action

        $title = $_POST["title"];

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
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_GET, "param1", "Board");
        $this->board_authorize_or_redirect($user, $board, "delete");

        $columns = $board->get_columns();
        if (count($columns) == 0) {
            $board->delete();
        } else {
            $this->redirect("board", "delete_confirm", $board->get_id());
        }
    }

    //mise en place de view_delete_confirm
    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_GET, "param1", "Board");
        $this->board_authorize_or_redirect($user, $board, "delete");

        (new View("delete_confirm"))->show(array("user" => $user, "instance" => $board));

        $this->redirect("board", "board", $board->get_id());
    }

    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        if(isset($_POST["id"])) {
            $board = Board::get_by_id($_POST["id"]);
            if(isset($_POST["delete"])) {
                $board->delete();
                $this->redirect();
            }
            $this->redirect("board", "board", $board->get_id());
        }
        $this->redirect();
    }


    private function board_authorize_or_redirect(User $user, Board $board, string $action): bool {
        $this->authorize_or_redirect($user, $board, false);

        switch ($action) {
            case "view":
            case "edit":
                return $user->is_collaborator($board);
        }

        $this->redirect();
    }
}


