<?php

require_once "autoload.php";

class ControllerUser extends ExtendedController {

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

        $email = Post::get('email');
        $password = Post::get('password');

        $error = new ValidationError();

        if (Post::any_non_empty("email", "password")) {
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

        list($user, $error) = $this->get_user_and_errors();

        $passwordConfirm = Post::get("confirm");;
        $error->set_messages_and_add_to_session($user->validate($passwordConfirm));

        if($error->is_empty()) {
            $user->insert();
            $this->log_user($user);
        } else {
            (new View("signup"))->show(array(
                    "email" => $user->get_email(),
                    "password" => Post::get("password"),
                    "fullName" => $user->get_fullName(),
                    "confirm" => Post::get("confirm"),
                    "errors" => $error)
            );
        }
    }


    public function manage() {
        $admin = $this->get_admin_or_redirect();

        (new View("manage_users"))->show(array(
                "user" => $admin,
                "users" => User::sql_select_all(),
                "errors" => ValidationError::get_error_and_reset())
        );
    }

    public function add() {
        $this->get_admin_or_redirect();

        list($user, $error) = $this->get_user_and_errors();

        if($error->is_empty()) {
            $user->insert();
        }

        $this->redirect("user","manage");
    }

    public function edit() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("id", "User");

        $email = Post::get_or_default("email",  $user->get_email());
        $fullName = Post::get_or_default("name", $user->get_fullname()) ;
        $role = Post::get_or_default("role", $user->get_role());

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
        $user = $this->get_object_or_redirect("id", "User");

        $this->redirect("user", "delete_confirm", $user->get_id());
    }

    public function delete_confirm() {
        $admin = $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("param1", "User");

        (new View("delete_confirm"))->show(array(
            "user"=>$admin,
            "instance"=>$user
        ));
    }

    public function remove() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("id", "User");

        $user->delete();
        $this->redirect("user","manage");
    }

    // Ajoute un user sur base de $_POST ou retourne une liste d'erreur
    // utilisé par user/signup et user/manage
    private function get_user_and_errors() {
        $email = Post::get("email");
        $fullName = Post::get("fullName");
        $password = Post::get_or_default("password", User::get_random_password());
        $role = Post::get_or_default("role", Role::USER);

        $user = new User($email, $fullName, $role, $password);

        $error = new ValidationError($user, "add");

        $passwordConfirm = Post::get("confirm");
        $error->set_messages_and_add_to_session($user->validate($passwordConfirm));

        return array($user, $error);
    }
}