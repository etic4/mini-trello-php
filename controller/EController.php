<?php

/* Ajouts à la classe Controller du framework */
abstract class EController extends Controller {

    protected function get_object_or_redirect(array $GET_or_POST, string $param_name, string $className) {
        $obj = null;

        if (isset($GET_or_POST[$param_name])) {
            $obj = $className::get_by_id($GET_or_POST[$param_name]);
        }

        if (is_null($obj)) {
            // Soit ça soit mettre méthode à static dans framework
            $this->redirect();
        }

        return $obj;
    }

    protected function authorize_or_redirect(User $user, Board $board, bool $authorize_collaborators=true): bool {
        if ($user->is_admin() || $user->is_owner($board)) {
            return true;
        }

        // Permet de n'authoriser que admin et le owner.
        // Notamment pour les board que seuls les owner admin peuvent deleter
        if ($authorize_collaborators && $user->is_collaborator($board)) {
            return true;
        }

        $this->redirect();
    }

    protected function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }

}