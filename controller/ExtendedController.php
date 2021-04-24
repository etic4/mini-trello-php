<?php

require_once "autoload.php";

/* Ajouts à la classe Controller du framework */

abstract class ExtendedController extends Controller {

    // Le paramètre 'authorized' représente le résultat de l'appel à Permission::{add|view|edit|delete}
    // lors duquel le fait que le user soit loggé est vérifié
    // ainsi que son droit à appeler {add|view|edit|delete} sur le contrôler

    protected function authorized_or_redirect(bool $authorized, string $redirect_url="") {
        if (!$authorized) {
            $this->redirect(...self::redirect_params($redirect_url));
        }
        return Session::get_user(); // retourne $user ou false, mais dans ce cas toujours $user puisque checké par Permissions::
    }

    // retourne le User s'il a le role admin, sinon redirige

    protected function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }

    // Raccourcis avec des arguments par défaut pour 'get_or_redirect'
    // Retourne un objet de type (Controller)Class (ex: sur ControllerBoard retourne une objet de type 'Board'
    // Dont l'id est obtenue:
    // à partir de la clé "id"" si la méthode est POST ($_POST["id"]
    // ou de la clé "param1" si la méthode est GET ($_GET["param1"]

    protected function get_or_redirect_default(string $redirect_url="") {
        $post_key = "id";
        $get_key = "param1";
        $class_name = str_replace("Controller", "", static::class);

        return $this->get_or_redirect($class_name, $post_key, $get_key, $redirect_url);
    }

    // Retourne un objet de type $class à partir de la clé "$post_object_id" de $_POST
    // et éventuellement redirige vers $redirect_url

    protected function get_or_redirect_post(string $class_name, string $post_key, string $redirect_url="") {
        return $this->get_or_redirect($class_name, $post_key, "", $redirect_url);
    }

    // Retourne un objet de type '$class' dont l'id est contenu
    // sous $_POST[$post] si la requête est de type POST
    // ou sous $_GET[$get] si la requête en de type GET
    //
    // Redirige le cas échéant vers $redirect_url si est non vide, sinon vers la page page par défaut
    //
    private function get_or_redirect(string $class_name, string $post_key, string $get_key, string $redirect_url="") {
        $GoT = $_POST;
        $param_name = $post_key;

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $GoT = $_GET;
            $param_name = $get_key;
        }

        $obj = null;

        if (isset($GoT[$param_name])) {
            $dao = $class_name."Dao";
            $obj = $dao::get_by_id($GoT[$param_name]);
        }

        if (is_null($obj)) {
            $this->redirect(...self::redirect_params($redirect_url));
        }

        return $obj;
    }

    private function redirect_params(string $url) {
        return !empty($redirect_url) ? explode("/", $redirect_url) : [];
    }
}