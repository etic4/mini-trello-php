<?php
/**/
require_once "framework/Controller.php";
require_once "model/user/User.php";

class ControllerBoard extends Controller {

    public function index() {
        $user = $this->get_user_or_false();
        $owners = [];
        $others = [];

        if ($user) {
            $owners = $user->get_own_boards();
            $others = $user->get_others_boards();
        }

        (new View("boardlist"))->show(array("user"=>$user, "owners" => $owners,
            "others" => $others));
    }

    public function board($id) {


    }
}