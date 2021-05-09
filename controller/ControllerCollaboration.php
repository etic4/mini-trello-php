<?php

require_once "autoload.php";

class ControllerCollaboration extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $board = $this->get_or_redirect_post("Board", "board_id");
        $this->authorized_or_redirect(Permissions::is_owner($board));

        $collaborator = $this->get_or_redirect_post("User", "collab_id");

        $collaboration = new Collaboration($board, $collaborator);
        CollaborationDao::insert($collaboration);

        $this->redirect("board", "collaborators", $board->get_id());
    }

    public function remove() {
        $board = $this->get_or_redirect_post("Board", "board_id");
        $user = $this->authorized_or_redirect(Permissions::is_owner($board));

        $collaborator = $this->get_or_redirect_post("User", "collab_id");

        $part_count = ParticipationDao::get_participations_count_in_board($collaborator, $board);

        if (Post::get("confirm") == "true" || !$part_count) {
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