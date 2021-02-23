<?php

/**/
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "CtrlTools.php";
require_once "ValidationError.php";
require_once "Authorize.php";
require_once "view/ViewTools.php";

class ControllerUser extends Controller {
    use Authorize;

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

        list($user, $error) = $this->add_user();

        if($error->is_empty()) {
            $user->insert();
            $this->log_user($user);
        }

        (new View("signup"))->show(array(
            "email" => $email, 
            "password" => $password,
            "fullName" => $fullName,
            "confirm" => $password_confirm,
            "errors" => $error)
        );
    }


    public function manage() {
        $admin = $this->get_admin_or_redirect();

        (new View("manage_users"))->show(array(
                "admin" => $admin,
                "users" => User::get_all(),
                "errors" => ValidationError::get_error_and_reset())
        );
    }

    public function add() {
        $this->get_admin_or_redirect();

        list($user, $error) = $this->add_user();

        if($error->is_empty()) {
            $user->insert();
        }

        $this->redirect("user","manage");

    }

    public function edit() {
        $this->get_admin_or_redirect();

    }

    public function delete() {
        $this->get_admin_or_redirect();
        $user = CtrlTools::get_object_or_redirect($_POST, "id", "User");
        $user->delete();
        $this->redirect("user","manage");
    }

    private function add_user() {
        $user = null;
        $error = new ValidationError();

        if (isset($_POST['email']) && isset($_POST['fullName']) && isset($_POST['role'])) {
            $email = $_POST['email'];
            $fullName = $_POST['fullName'];
            $role = $_POST['role'];
            $password = User::get_random_password();
            $password_confirm = $password;

            $user = new User($email, $fullName, $role, $password, null, null, null);
            $error->set_messages_and_add_to_session($user->validate($password_confirm));
        }

        return array($user, $error);
    }

}