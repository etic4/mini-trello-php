<?php

require_once "autoload.php";


class ControllerAdminuser extends Controller {
    use Authorize;

    public function index() {
        $this->get_admin_or_redirect();

        (new View("admin_user"))->show(
            array("users" => User::get_all())
        );
    }

    public function add() {
        $this->get_admin_or_redirect();

        $email = '';
        $password = '';
        $fullName = '';
        $role = '';
        $user = null;
        $error = new ValidationError();

        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fullName']) && isset($_POST['role'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $fullName = $_POST['fullName'];
            $role = $_POST['role'];

            $user = new User($email, $fullName, $role, $password, null, null, null);
            $error->set_messages_and_add_to_session($user->validate());

            if($error->is_empty()) {
                $user->insert();
                $this->redirect("adminuser");
            }
        }

        (new View("edit"))->show(array(
                "email" => $email,
                "password" => $password,
                "fullName" => $fullName,
                "role" => $role,
                "errors" => $error)
        );
    }

    public function delete() {
        $this->get_admin_or_redirect();
        $user = CtrlTools::get_object_or_redirect($_POST, "userId", "User");

        $this->redirect("adminuser", "delete_confirm", $user->get_id());
    }

    public function delete_confirm() {
        $admin = $this->get_admin_or_redirect();
        $user = CtrlTools::get_object_or_redirect($_POST, "userId", "User");

        (new View("delete_confirm"))->show(array(
            "user"=>$admin,
            "instance"=>$user
        ));

        $this->redirect("adminuser");
    }

    public function edit() {
        $this->get_admin_or_redirect();
        $user = CtrlTools::get_object_or_redirect($_POST, "userId", "User");

        $email = '';
        $password = '';
        $fullName = '';
        $role = '';
        $user = null;
        $error = new ValidationError();

        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fullName']) && isset($_POST['role'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $fullName = $_POST['fullName'];
            $role = $_POST['role'];

            $user=new User($email, $fullName, $role, $password, null, null, null);
            $error->set_messages_and_add_to_session($user->validate());

            if($error->is_empty()) {
                $user->insert();
                $this->redirect("adminuser");
            }
        }

        (new View("edit"))->show(array(
                "email" => $email,
                "password" => $password,
                "fullName" => $fullName,
                "role" => $role,
                "errors" => $error)
        );
    }
}