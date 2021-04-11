<?php

require_once "autoload.php";

class BoardPermissions implements IPermissions {
    private ?Board $board;

    public function __construct(Board $board=null) {
        $this->board = $board;
    }

    public function add(User $user): bool {
        return true;
    }

    public function view(User $user): bool {
        return $user->is_admin()
            || $user->is_owner($this->board)
            || $user->is_collaborator($this->board);
    }

    public function edit(User $user): bool {
        return $this->view($user);
    }

    public function delete(User $user): bool {
        return $user->is_admin()
            || $user->is_owner($this->board);
    }
}