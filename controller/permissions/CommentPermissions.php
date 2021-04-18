<?php

require_once "autoload.php";

class CommentPermissions {

    function add(User $user, $comment): bool {
        return $this->view($user, $comment);
    }

    function view(User $user, $comment): bool {
        return (new BoardPermissions())->view($user, $comment->get_board());
    }

    function edit(User $user, $comment): bool {
        return $user->is_admin() || $user->is_author($comment);
    }

    function delete(User $user, $comment): bool {
        return(new BoardPermissions())->delete($user, $comment->get_board()) || $user->is_author($comment);
    }
}