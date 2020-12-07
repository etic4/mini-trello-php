<?php

require_once "model/board/BoardMngr.php";

class ControllerBoard extends Controller {

    public function index() {
        $user = $this->get_user_or_false();
        $owners = [];
        $others = [];

        if ($user) {
            $boardMngr = new BoardMngr($user);

            $owners = $boardMngr->get_own_boards();
            $others = $boardMngr->get_others_boards();
        }

        (new View("boardlist"))->show(array("user"=>$user, "owners" => $owners,
            "others" => $others));
    }

    public function board($id) {


    }
}