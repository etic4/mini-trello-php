<?php

/**/
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "CtrlTools.php";
require_once "ValidationError.php";

class ControllerUser extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect();
        } else {
            $this->login();
        }
    }

    public function login() {
        if ($this->user_logged()) {
            $this->redirect();
        }

        $email = '';
        $password = '';
        $error = new ValidationError();

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $error->set_messages_and_add_to_session(User::validate_login($email, $password));
        
            if ($error->is_empty()) {
                $this->log_user(User::get_by_email($email));
            }
        }
        
        (new View("login"))->show(array(
            "email" => $email, 
            "password" => $password, 
            "errors" => $error)
        );
    }

    public function logout() {
        session_destroy();
        $this->redirect();
    }

    public function signup() {
        if ($this->user_logged()) {
            $this->redirect();
        }
        $email = '';
        $password = '';
        $fullName = '';
        $password_confirm = '';
        $user = null;
        $error = new ValidationError();

        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fullName']) && isset($_POST['confirm'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $fullName = $_POST['fullName'];
            $password_confirm = $_POST['confirm'];
            
            $user=new User($email, $fullName, $password, null, null, null);
            $error->set_messages_and_add_to_session($user->validate($password_confirm));

            if($error->is_empty()) {
                $user->insert();
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(array(
            "email" => $email, 
            "password" => $password,
            "fullName" => $fullName,
            "confirm" => $password_confirm,
            "errors" => $error)
        );
    }

}