<?php

require_once "autoload.php";

class ColumnPermissions {

    function add(User $user, $column): bool {
        return $this->view($user, $column);
    }

    function view(User $user, $column): bool {
        return (new BoardPermissions())->view($user, $column->get_board());
    }

    function edit(User $user, $column): bool {
        return $this->add($user, $column);
    }

    function delete(User $user, $column): bool {
        return $this->add($user, $column);
    }
}