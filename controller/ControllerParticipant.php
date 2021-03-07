<?php

require_once "autoload.php";


class ControllerParticipant extends EController {

    public function index() {
        // TODO: Implement index() method.
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $card = $this->get_object_or_redirect($_POST, "card-id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $participant = $this->get_object_or_redirect($_POST, "id", "User");
        $card->add_participant($participant);

        $this->redirect("card", "edit", $card->get_id());

    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $card = $this->get_object_or_redirect($_POST, "card-id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $participant = $this->get_object_or_redirect($_POST, "id", "User");
        $card->remove_participant($participant);

        $this->redirect("card", "edit", $card->get_id());


    }
}