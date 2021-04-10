<?php

require_once "autoload.php";

class ControllerCollaborator extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $board = $this->get_object_or_redirect("board_id", "Board");
        $this->authorize_for_board_or_redirect($board, false);

        $collaborator = $this->get_object_or_redirect("collab_id", "User");

        $collaboration = new Collaboration($board, $collaborator);
        CollaborationDao::insert($collaboration);

        $this->redirect("board", "collaborators", $board->get_id());
    }

    public function remove() {
        $board = $this->get_object_or_redirect("board_id", "Board");
        $user = $this->authorize_for_board_or_redirect($board, false);

        $collaborator = $this->get_object_or_redirect("collab_id", "User");

        //TODO: permissions !

        $part_count = ParticipationDao::get_participations_count_in_board($collaborator, $board);

        if (Post::isset("confirm") || !$part_count) {
            if ($part_count) {
                ParticipationDao::remove_all($collaborator, $board);
            }
            CollaborationDao::remove($board, $collaborator);

            $this->redirect("board", "collaborators", $board->get_id());
        }

        (new View("remove_collab_confirm"))->show(array(
            "user" => $user,
            "collab_id" => Post::get("collab_id"),
            "board_id" => Post::get("board_id"),
            "part_count" => $part_count,
            "cancel_url" => "board/collaborators/".$board->get_id()));
    }
}