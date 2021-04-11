<?php

require_once "autoload.php";

class CommentPermissions implements IPermissions {
    private Comment $comment;
    private BoardPermissions $board_perm;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
        $this->board_perm = new BoardPermissions($this->comment->get_board());
    }

    function add(User $user): bool {
        return $this->view($user);
    }

    function view(User $user): bool {
        return $this->board_perm->view($user);
    }

    function edit(User $user): bool {
        return $this->board_perm->edit($user) || $user->is_author($this->comment);
    }

    function delete(User $user): bool {
        return $this->board_perm->delete($user) || $user->is_author($this->comment);
    }
}