<?php

require_once "autoload.php";


class ControllerParticipant extends ExtendedController {

    public function index() {
        // TODO: Implement index() method.
        $this->redirect();
    }

    public function add() {
        $card = $this->get_object_or_redirect("card_id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $participant = $this->get_object_or_redirect("id", "User");

        $participation = new Participation($card, $participant);

        ParticipationDao::insert($participation);

        $this->redirect("card", "edit", $card->get_id());

    }
}