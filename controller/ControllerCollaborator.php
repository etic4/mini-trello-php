<?php
require_once "framework/Controller.php";
require_once "Authorize.php";

class ControllerCollaborator extends Controller {
    use Authorize;

    public function index() {
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_POST, "board-id", "Board");
        $this->authorize_or_redirect($user, $board->get_board(), false);

        $collaborator = CtrlTools::get_object_or_redirect($_POST, "id", "User");
        $board->add_collaborator($collaborator);

        $this->redirect("board", "index", $board->get_id());
    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_POST, "board-id", "Board");
        $this->authorize_or_redirect($user, $board->get_board(), false);

        $collaborator = CtrlTools::get_object_or_redirect($_POST, "id", "User");
        $board->remove_collaborator($collaborator);

        $this->redirect("board", "index", $board->get_id());
    }
}