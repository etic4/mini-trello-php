<?php

require_once "autoload.php";

class CardPermissions implements IPermissions {
    private BoardPermissions $board_perm;

    public function __construct(Card $card) {
        $this->board_perm = new BoardPermissions($card->get_board());
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