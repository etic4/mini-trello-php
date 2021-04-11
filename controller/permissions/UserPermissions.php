<?php

require_once "autoload.php";

class UserPermissions implements IPermissions {
    function add(User $user): bool {
        return $user->is_admin();
    }

    function view(User $user): bool {
        return $user->is_admin();
    }

    function edit(User $user): bool {
        return $user->is_admin();
    }

    function delete(User $user): bool {
        return $user->is_admin();
    }
}