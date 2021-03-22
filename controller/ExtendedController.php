<?php

/* Ajouts à la classe Controller du framework */
abstract class ExtendedController extends Controller {

    // Autorise ou redirige l'utilisateur pour le board concerné par la requête
    // retourne une instance de User
    protected function authorize_for_board_or_redirect(Board $board, bool $authorize_collaborators=true) {
        $user = $this->get_user_or_redirect();

        if ($user->is_admin() || $user->is_owner($board)) {
            return $user;
        }

        // Permet de n'authoriser que admin et le owner.
        // Notamment pour les board que seuls les owner admin peuvent deleter
        if ($authorize_collaborators && $user->is_collaborator($board)) {
            return $user;
        }

        $this->redirect();
    }

    // retourne le User s'il a le role admin, sinon redirige
    protected function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }

    //Retourne l'objet de type $className dont l'id est contenue dans $param_name
    protected function get_object_or_redirect(string $param_name, string $className) {
        $GoT = $_POST;

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $GoT = $_GET;
        }

        $obj = null;

        if (isset($GoT[$param_name])) {
            $obj = $className::get_by_id($GoT[$param_name]);
        }

        if (is_null($obj)) {
            $this->redirect();
        }
        return $obj;
    }
}