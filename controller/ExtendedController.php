<?php

require_once "autoload.php";

/* Ajouts à la classe Controller du framework */
// TODO: fonction get_redirect_params

abstract class ExtendedController extends Controller {

    // Le paramètre authorized représente le résultat de l'appel à Permission::{add|view|edit|delete}
    // lors duquel le fait que le user soit loggé est vérifié
    // ainsi que son droit à effectuer l'action

    protected function authorized_or_redirect(bool $authorized, string $redirect_url="") {
        if (!$authorized) {
            $this->redirect(...self::redirect_params($redirect_url));
        }
        return $this->get_user_or_false(); // retourne toujours $user
    }

    // retourne le User s'il a le role admin, sinon redirige

    protected function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }

    protected function get_or_redirect(string $class, string $post="id", string $get="param1", string $redirect_url="") {
        $GoT = $_POST;
        $param_name = $post;

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $GoT = $_GET;
            $param_name = $get;
        }

        $obj = null;

        if (isset($GoT[$param_name])) {
            $dao = $class."Dao";
            $obj = $dao::get_by_id($GoT[$param_name]);
        }

        if (is_null($obj)) {
            $this->redirect(...self::redirect_params($redirect_url));
        }

        return $obj;
    }

    protected function get_object_or_redirect(string $redirect_url="") {
        $post = "id";
        $get = "param1";
        $class = str_replace("Controller", "", static::class);

        return $this->get_or_redirect($class, $post, $get, $redirect_url);
    }

    private function redirect_params(string $url) {
        return !empty($redirect_url) ? explode("/", $redirect_url) : [];
    }
}