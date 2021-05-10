<?php

require_once "autoload.php";

class ControllerParticipant extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $card = $this->get_or_redirect_post("Card", "card_id");
        $this->authorized_or_redirect(Permissions::view($card));

        $participant = $this->get_or_redirect_post("User", "participant_id");

        $particip = ParticipationDao::get_participation($card, $participant);

        if (is_null($particip)) {
            $participation = new Participation($card, $participant);
            ParticipationDao::insert($participation);
        }

        $this->redirect("card", "edit", $card->get_id()."#participants");
    }

    public function remove() {
        $card = $this->get_or_redirect_post("Card", "card_id");
        $this->authorized_or_redirect(Permissions::view($card));

        $participant = $this->get_or_redirect_post("User", "participant_id");
        ParticipationDao::remove($card, $participant);

        $this->redirect("card", "edit", $card->get_id() . "#participants");
    }
}