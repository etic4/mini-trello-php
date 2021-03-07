<?php

/* Ajouts à la classe Controller du framework */
abstract class EController extends Controller {

    //Autorise ou redirige l'utilisateur pour le board concerné par la requête
    // retourne une instance de User et une instance de $className
    protected function authorize_or_redirect(string $param_name, string $className, bool $authorize_collaborators=true): array {
        $user = $this->get_user_or_redirect();
        $object = $this->get_object_or_redirect($param_name, $className);
        $board = $object->get_board();

        if ($user->is_admin() || $user->is_owner($board)) {
            return array($user, $object);
        }

        // Permet de n'authoriser que admin et le owner.
        // Notamment pour les board que seuls les owner admin peuvent deleter
        if ($authorize_collaborators && $user->is_collaborator($board)) {
            return array($user, $object);
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
            // Soit ça soit mettre méthode à static dans framework
            $this->redirect();
        }

        return $obj;
    }


}