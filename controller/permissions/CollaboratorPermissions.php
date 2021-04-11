<?php

require_once "autoload.php";

class CollaboratorPermissions implements IPermissions {
    private Collaboration $collaboration;

    public function __construct(Collaboration $collaboration) {
        $this->collaboration = $collaboration;
    }

    function add(User $user): bool {
        return $user->is_admin() || $user->is_owner($this->collaboration->get_board());
    }

    function view(User $user): bool {
        throw new BadMethodCallException("Not implemented");
    }

    function edit(User $user): bool {
        throw new BadMethodCallException("Not implemented");
    }

    function delete(User $user): bool {
        return $this->add($user);
    }
}