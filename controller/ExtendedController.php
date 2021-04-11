<?php

/* Ajouts à la classe Controller du framework */
abstract class ExtendedController extends Controller {
    private Permissions $perm;

    protected function __construct() {
        parent::__construct();
        $this->perm = new Permissions();
    }

    // Autorise ou redirige l'utilisateur pour le board concerné par la requête
    // retourne une instance de User
    protected function authorize_for_board_or_redirect(Board $board, bool $authorize_collaborators=true) {
        $user = $this->get_user_or_redirect();

        // Permet de n'authoriser que admin et le owner.
        // Notamment pour les board que seuls les owner admin peuvent deleter
        if ($user->is_admin() || $user->is_owner($board)) {
            return $user;
        }

        if ($authorize_collaborators && $user->is_collaborator($board)) {
            return $user;
        }

        $this->redirect();
    }

    protected function authorize_or_redirect(bool $authorized, string $redirect_url="") {
        if (!$authorized) {
            $params = !empty($redirect_url) ? explode("/", $redirect_url) : [];
            $this->redirect(...$params);
        }
    }

    // retourne le User s'il a le role admin, sinon redirige
    protected function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }

    protected function get_or_redirect(string $post="id", string $get="param1", string $class="", string $redirect_url="") {
        if (empty($class)) {
            $class = str_replace("Controller", "", static::class);
        }

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
            $params = !empty($redirect_url) ? explode("/", $redirect_url) : [];
            $this->redirect(...$params);
        }
        return $obj;
    }

    protected function get_object_or_redirect() {
        $post = "id";
        $get = "params1";
        $class = str_replace("Controller", "", static::class);

        return $this->get_or_redirect($post, $get, $class);
    }
}