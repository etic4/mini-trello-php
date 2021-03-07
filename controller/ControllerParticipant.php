<?php

require_once "autoload.php";


class ControllerParticipant extends EController {

    public function index() {
        // TODO: Implement index() method.
        $this->redirect();
    }

    public function add() {
        list($_, $card) = $this->authorize_or_redirect("card_id", "Card");

        $participant = $this->get_object_or_redirect("id", "User");
        $card->add_participant($participant);

        $this->redirect("card", "edit", $card->get_id());

    }

    public function remove() {
        list($_, $card) = $this->authorize_or_redirect("card_id", "Card");

        $participant = $this->get_object_or_redirect("id", "User");
        $card->remove_participant($participant);

        $this->redirect("card", "edit", $card->get_id());


    }
}