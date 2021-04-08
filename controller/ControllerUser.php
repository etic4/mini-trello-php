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

        if (Post::any_non_empty("email", "password")) {
            $error = new DisplayableError();
            $error->set_messages(UserDao::validate_login($email, $password));
            Session::set_error($error);

            if ($error->is_empty()) {
                $this->log_user(UserDao::get_by_email($email));
            }
        }

        (new View("login"))->show(array(
            "email" => $email,
            "password" => $password,
            "errors" => Session::get_error())
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

        if (!Post::empty("email")) {
            $user= User::from_post();

            $error = new DisplayableError();
            $error->set_messages(UserDao::validate_signup($user, Post::get("password"), Post::get("confirm")));
            Session::set_error($error);

            if($error->is_empty()) {
                $user = UserDao::insert($user);
                $this->log_user($user);
                die;
            }
        }
            (new View("signup"))->show(array(
                    "email" => Post::get("email"),
                    "password" => Post::get("password"),
                    "fullName" => Post::get("fullName"),
                    "confirm" => Post::get("confirm"),
                    "errors" =>Session::get_error())
            );
    }

    public function add() {
        $this->get_admin_or_redirect();

        $user= User::from_post();
        $user->set_password(User::get_random_password());

        $error = new DisplayableError($user, "add");

        $error->set_messages(UserDao::validate_add($user));
        Session::set_error($error);

        if($error->is_empty()) {
            UserDao::insert($user);
        }

        $this->redirect("user","manage");
    }

    public function edit() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("id", "User");

        $user->set_email(Post::get("email", $user->get_email()));
        $user->set_fullName(Post::get("name", $user->get_fullname()));
        $user->set_role(Post::get("role", $user->get_role()));


        $error = new DisplayableError($user, "edit");
        $error->set_messages(UserDao::validate_edit($user));
        Session::set_error($error);

        if ($error->is_empty()) {
            UserDao::update($user);
        }

        $this->redirect("user","manage");
    }

    public function delete() {
        $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("id", "User");

        if (Post::isset("confirm")) {
            UserDao::delete($user);
        }

        $this->redirect("user", "delete_confirm", $user->get_id());
    }

    public function delete_confirm() {
        $admin = $this->get_admin_or_redirect();
        $user = $this->get_object_or_redirect("param1", "User");

        (new View("delete_confirm"))->show(array(
            "user"=>$admin,
            "cancel_url" => "user/manage",
            "instance"=>$user
        ));
    }


    public function manage() {
        $admin = $this->get_admin_or_redirect();

        (new View("manage_users"))->show(array(
                "user" => $admin,
                "users" => UserDao::get_all_users(),
                "errors" => Session::get_error())
        );
    }
}