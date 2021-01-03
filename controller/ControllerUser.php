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

            $errors = User::validate_login($email, $password);
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

    public function signup() {
        $email = '';
        $password = '';
        $fullName='';
        $confirm='';
        $errors = [];
        $user=null;
        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fullName']) && isset($_POST['confirm'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $fullName=$_POST['fullName'];
            $confirm=$_POST['confirm'];
            
            $user=new User($email,$fullName,$password,null,null,null);
            $errors = $user->validate();
            if($confirm!==$password){
               array_push($errors,"Votre mot de passe et votre confirmation de mot de passe sont différentes");
            }
            if (empty($errors)) {
                $user->insert();
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(array(
            "email" => $email, 
            "password" => $password,
            "fullName" => $fullName,
            "confirm" => $confirm, 
            "errors" => $errors)
        );
    }
}