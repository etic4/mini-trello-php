<?php

require_once "autoload.php";


class ControllerParticipant extends ExtendedController {

    public function index() {
        // TODO: Implement index() method.
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $card = $this->get_or_redirect($post="card_id", $class="Card");
        $this->authorize_or_redirect(Permissions::view($card));

        $participant = $this->get_or_redirect($post="participant_id", $class="User");

        $participation = new Participation($card, $participant);
        ParticipationDao::insert($participation);

        $this->redirect("card", "edit", $card->get_id()."#participants");
    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $card = $this->get_or_redirect("card_id", $class="Card");
        $this->authorize_or_redirect(Permissions::view($card));

        $participant = $this->get_or_redirect($post="participant_id", $class="User");
        ParticipationDao::remove($card, $participant);

        $this->redirect("card", "edit", $card->get_id() . "#participants");
    }
}