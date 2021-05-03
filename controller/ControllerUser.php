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
            $error->set_messages((new UserValidation())->validate_login($email, $password));
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

        $email = Post::get("email");
        $fullName = Post::get("fullName");
        $password = Post::get("password");
        $role = Post::get("role", "user");
        $password_confirm = Post::get("confirm");

        if (!Post::empty("email")) {
            $error = new DisplayableError();
            $error->set_messages((new UserValidation())->validate_signup($email, $fullName, $password, $password_confirm));
            Session::set_error($error);

            if($error->is_empty()) {
                $user = new User($email, $fullName, $role, $password);
                $user = UserDao::insert($user);
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(array(
                "email" => $email,
                "fullName" => $fullName,
                "password" => $password,
                "confirm" => $password_confirm,
                "errors" =>Session::get_error())
        );
    }

    public function manage() {
        $admin = $this->get_admin_or_redirect();

        (new View("users_manage"))->show(array(
                "user" => $admin,
                "users" => UserDao::get_all_users(),
                "errors" => Session::get_error())
        );
    }


    /*   --- Admin seul ---   */

    public function add() {
        $admin = $this->get_admin_or_redirect();

        $email = Post::get("email");
        $fullName = Post::get("fullName");

        if (Post::isset("confirm")) {
            $error = new DisplayableError();
            $error->set_messages((new UserValidation())->validate_add($fullName, $email));
            Session::set_error($error);

            if($error->is_empty()) {
                $user= new User($email, $fullName, Post::get("role"), User::get_random_password());
                UserDao::insert($user);
                $this->redirect("user", "manage");
            }
        }

        (new View("user_add"))->show(array(
                "user" => $admin,
                "email" => $email,
                "fullName" => $fullName,
                "role" => Post::get("role", "user"),
                "errors" => Session::get_error())
        );
    }

    public function edit() {
        $admin = $this->get_admin_or_redirect();
        $user = $this->get_or_redirect_default();

        $email = Post::get("email", $user->get_email());
        $fullName = Post::get("fullName", $user->get_fullName());
        $role = Post::get("role", $user->get_role());

        if (Post::isset("confirm")) {
            $error = new DisplayableError();
            $error->set_messages((new UserValidation())->validate_edit($fullName, $email, $role, $user, $admin));
            Session::set_error($error);

            if ($error->is_empty()) {
                $user->set_email($email);
                $user->set_fullName($fullName);
                $user->set_role($role);

                if (Post::get("new_password") == "on") {
                    $user->set_password(User::get_random_password());
                }

                UserDao::update($user);

                // Si admin s'édite, comme il est stocké dans $_SESSION["user"]
                // il faut mettre à jour $_SESSION["user"] sinon les changements ne sont pas reflétés
                // dans l'UI
                $_SESSION["user"] = UserDao::get_by_id($_SESSION["user"]->get_id());
                $this->redirect("user", "manage");
            }
        }

        (new View("user_edit"))->show(array(
                "user" => $admin,
                "id" => $user->get_id(),
                "email" => $email,
                "fullName" => $fullName,
                "role" => $role,
                "breadcrumb" => new BreadCrumb([], $user->get_fullName()),
                "errors" =>Session::get_error())
        );
    }

    public function delete() {
        $this->get_admin_or_redirect();
        $user = $this->get_or_redirect_default();

        if (Post::isset("confirm")) {
            UserDao::delete($user);
            $this->redirect("user", "manage");
        }
        $this->redirect("user", "delete_confirm", $user->get_id());
    }

    public function delete_confirm() {
        $admin = $this->get_admin_or_redirect();
        $user = $this->get_or_redirect_default();

        (new View("delete_confirm"))->show(array(
            "user"=>$admin,
            "cancel_url" => "user/manage",
            "instance"=>$user
        ));
    }

    // --- Services ---

    public function email_is_unique_service() {
        $res = "true";

        if(!Post::empty("email")){
            $user = UserDao::get_by_email(Post::get("email"));
            if($user){
                $res = "false";
            }
        }

        echo $res;
    }
}