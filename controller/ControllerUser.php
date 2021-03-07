<?php

require_once "autoload.php";

class ControllerUser extends EController {

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
            "email" => $this->get_or_empty($_POST, "email") ,
            "password" => $this->get_or_empty($_POST, "password"),
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

        list($user, $error) = $this->add_user_or_errors();

        if (is_null($user)) {
            (new View("signup"))->show(array(
                "email" => $this->get_or_empty($_POST, "email"),
                "password" => $this->get_or_empty($_POST, "password"),
                "fullName" => $this->get_or_empty($_POST, "fullName"),
                "confirm" => $this->get_or_empty($_POST, "confirm"),
                "errors" => $error)
            );
        } else {
            if($error->is_empty()) {
                $user->insert();
                $this->log_user($user);
            } else {
                (new View("signup"))->show(array(
                    "email" => $user->get_email(),
                    "password" => $user->get_password(),
                    "fullName" => $user->get_fullName(),
                    "confirm" => $this->get_or_empty($_POST, "confirm"),
                    "errors" => $error)
                );
            }
        }
    }


    public function manage() {
        $user = $this->get_admin_or_redirect();

        (new View("manage_users"))->show(array(
                "user" => $user,
                "users" => User::get_all(),
                "errors" => ValidationError::get_error_and_reset())
        );
    }

    public function add() {
        $this->get_admin_or_redirect();

        list($user, $error) = $this->add_user_or_errors();

        if($error->is_empty()) {
            $user->insert();
        }

        $this->redirect("user","manage");
    }

    public function edit() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect($_POST, "id", "User");

        $email = $user->get_email();
        $fullName = $user->get_fullname();
        $role = $user->get_role();

        if (isset($_POST['email']) && isset($_POST['name'])) {
            $email = $_POST['email'];
            $fullName = $_POST['name'];
        }

        if (isset($_POST['role'])) {
            $role = $_POST['role'];
        }

        $error = new ValidationError($user, "edit");
        $error->set_messages_and_add_to_session(User::validate_admin_edit($user, $email, $fullName));

        if ($error->is_empty()) {
            $user->set_fullName($fullName);
            $user->set_email($email);
            $user->set_role($role);
            $user->update();
        }

        $this->redirect("user","manage");
    }

    public function delete() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect($_POST, "id", "User");

        $this->redirect("user", "delete_confirm", $user->get_id());
    }

    public function delete_confirm() {
        $admin = $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect($_GET, "param1", "User");

        (new View("delete_confirm"))->show(array(
            "user"=>$admin,
            "instance"=>$user
        ));
    }

    public function remove() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect($_POST, "id", "User");

        $user->delete();
        $this->redirect("user","manage");
    }

    // Ajoute un user sur base de $_POST ou retourne une liste d'erreur
    // utilisé par user/signup et user/manage
    private function add_user_or_errors() {
        $user = null;
        $error = new ValidationError($user, "add");

        if (isset($_POST['email']) && isset($_POST['fullName'])) {
            $email = $_POST['email'];
            $fullName = $_POST['fullName'];
            $role = Role::USER;

            if (isset($_POST['role'])) {
                $role = $_POST['role'];
            }

            if (isset($_POST['password']) && isset($_POST['confirm'])) {
                $password = $_POST['password'];
                $password_confirm = $_POST['confirm'];
            } else {
                $password = User::get_random_password();
                $password_confirm = $password;
            }

            $user = new User($email, $fullName, $role, $password, null, null, null);
            $error = new ValidationError($user, "add");
            $error->set_messages_and_add_to_session($user->validate($password_confirm));
        }
        return array($user, $error);
    }

    private function get_or_empty($GET_or_POST, $name) {
        if (isset($GET_or_POST[$name])){
                return $GET_or_POST[$name];
        }
        return "";
    }
}