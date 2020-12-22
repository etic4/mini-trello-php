<?php

/**/
require_once "framework/Controller.php";
require_once "model/User.php";

class ControllerUser extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("board");
        } else {
            $this->login();
        }
    }

    public function login() {
        $email = '';
        $password = '';
        $errors = [];

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            //$errors = User::validate_login($email, $password);
            if (empty($errors)) {
                $this->log_user(User::get_by_email($email));
            }
        }
        (new View("login"))->show(array(
            "email" => $email, 
            "password" => $password, 
            "errors" => $errors)
        );
    }

    public function logout() {
        session_destroy();
        $this->redirect();
    }
}