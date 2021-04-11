<?php

require_once "autoload.php";

class ColumnPermissions implements IPermissions {
    private BoardPermissions $board_perm;

    public function __construct(Column $column) {
        $this->board_perm = new BoardPermissions($column->get_board());
    }

    function add(User $user): bool {
        return $this->board_perm->view($user);
    }

    function view(User $user): bool {
        return $this->add($user);
    }

    function edit(User $user): bool {
        return $this->add($user);
    }

    function delete(User $user): bool {
        return $this->add($user);
    }
}