<?php

require_once "autoload.php";

class CardPermissions {

    function add(User $user, $card): bool {
        return $this->view($user, $card);
    }

    function view(User $user, $card): bool {
        return (new BoardPermissions())->view($user, $card->get_board());
    }

    function edit(User $user, $card): bool {
        return $this->add($user, $card);
    }

    function delete(User $user, $card): bool {
        return $this->add($user, $card);
    }
}