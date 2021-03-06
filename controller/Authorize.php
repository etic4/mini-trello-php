<?php

require_once "autoload.php";

trait Authorize {
    private function authorize_or_redirect(User $user, Board $board, bool $authorize_collaborators=true): bool {
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

    private function get_admin_or_redirect() {
        $user = $this->get_user_or_redirect();

        if (!$user->is_admin()) {
            $this->redirect();
        }
        return $user;
    }
}