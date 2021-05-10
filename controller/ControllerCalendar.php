<?php

require_once "autoload.php";

class ControllerCalendar extends ExtendedController {

    public function index() {
        $user = $this->get_user_or_redirect();

        (new View("calendar"))->show(array(
                "user" => $user
            )
        );
    }
}