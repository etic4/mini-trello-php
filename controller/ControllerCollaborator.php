<?php

require_once "autoload.php";

class ControllerCollaborator extends EController {
    use Authorize;

    public function index() {
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $board = $this->get_object_or_redirect($_POST, "board-id", "Board");
        $this->authorize_or_redirect($user, $board, false);

        $collaborator = $this->get_object_or_redirect($_POST, "id", "User");
        $board->add_collaborator($collaborator);

        $this->redirect("board", "collaborators", $board->get_id());
    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $board = $this->get_object_or_redirect($_POST, "board-id", "Board");
        $this->authorize_or_redirect($user, $board, false);

        $collaborator = $this->get_object_or_redirect($_POST, "id", "User");
        $board->remove_collaborator($collaborator);

        $this->redirect("board", "collaborators", $board->get_id());
    }
}