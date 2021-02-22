<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";
require_once "CtrlTools.php";
require_once "ValidationError.php";
require_once "Authorize.php";
require_once  "view/ViewTools.php";


class ControllerParticipant extends Controller {
    use Authorize;

    public function index() {
        // TODO: Implement index() method.
        $this->redirect();
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "card-id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $participant = CtrlTools::get_object_or_redirect($_POST, "id", "User");
        $card->add_participant($participant);

        $this->redirect("card", "edit", $card->get_id());

    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "card-id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $participant = CtrlTools::get_object_or_redirect($_POST, "id", "User");
        $card->remove_participant($participant);

        $this->redirect("card", "edit", $card->get_id());


    }
}