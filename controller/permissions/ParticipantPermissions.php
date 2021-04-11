<?php

require_once "autoload.php";

class ParticipantPermissions implements IPermissions {
    private CardPermissions $cardPermissions;

    public function __construct(Participation $participation) {
        $this->cardPermissions = new CardPermissions($participation->get_card());
    }

    function add(User $user): bool {
        return  $this->cardPermissions->view($user);
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