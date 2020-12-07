<?php

require_once "framework/Controller.php";
require_once "model/user/UserMngr.php";

class ControllerUser extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("board");
        } else {
            (new View("login"))->show();
        }
    }

    public function login() {
        $email = '';
        $password = '';
        $errors = [];
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userMngr = new UserMngr();
            $errors = $userMngr->validate_login($email, $password);
            if (empty($errors)) {
                $this->log_user($userMngr->get_by_email($email));
            }
        }
        (new View("login"))->show(array("email" => $email, "password" => $password, "errors" => $errors));
    }
}