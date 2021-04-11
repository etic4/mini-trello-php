<?php

require_once "autoload.php";

class ControllerCollaborator extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $board = $this->get_or_redirect($post="board_id", $class="Board");
        $this->authorized_or_redirect($user->is_admin() || $user->is_owner($board));

        $collaborator = $this->get_or_redirect($post="collab_id", $class="User");

        $collaboration = new Collaboration($board, $collaborator);
        CollaborationDao::insert($collaboration);

        $this->redirect("board", "collaborators", $board->get_id());
    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $board = $this->get_or_redirect($post="board_id", $class="Board");
        $this->authorized_or_redirect($user->is_admin() || $user->is_owner($board));

        $collaborator = $this->get_or_redirect($post="collab_id", $class="User");

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