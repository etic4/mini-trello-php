<?php

require_once "autoload.php";

class BoardPermissions {

    public function add(User $user, $board): bool {
        return true;
    }

    public function view(User $user, $board): bool {
        return $user->is_admin()
            || $user->is_owner($board)
            || $user->is_collaborator($board);
    }

    public function edit(User $user, $board): bool {
        return $this->view($user, $board);
    }

    public function delete(User $user, $board): bool {
        return $user->is_admin()
            || $user->is_owner($board);
    }
}