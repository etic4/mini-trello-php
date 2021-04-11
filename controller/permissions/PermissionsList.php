<?php

require_once "autoload.php";

class PermissionsList {

    protected function board(): array {
        $view = function(User $user, Board $board) {
            return $user->is_admin() || $user->is_owner($board) || $user->is_collaborator($board);
        };

        $delete = function (User $user, Board $board) {
            return $user->is_admin() || $user->is_owner($board);
        };

        return [
            "add" => fn() => true,
            "view" => $view,
            "edit" => $view,
            "delete" => $delete
        ];
    }

    protected function column(): array {
        return [
            "add" => $this->board()["view"],
            "view" => $this->board()["view"],
            "edit" => $this->board()["view"],
            "delete" => $this->board()["view"]
        ];
    }

    protected function card(): array {
        return $this->column();
    }

    protected function comment(): array {
        $edit = function(User $user, Comment $comment) {
            return $user->is_admin() || $user->is_author($comment);
        };

        $delete = function(User $user, Comment $comment) {
            return $user->is_admin() || $user->is_owner($comment->get_board()) || $user->is_author($comment);
        };

        return [
            "add" => $this->board()["view"],
            "view" => $this->board()["view"],
            "edit" => $edit,
            "delete" => $delete
        ];
    }
}