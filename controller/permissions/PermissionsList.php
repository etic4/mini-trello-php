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
            "add" => fn(User $user, Column $column) => $this->board()["view"]($user, $column->get_board()),
            "view" => $this->column()["add"],
            "edit" =>$this->column()["add"],
            "delete" => $this->column()["add"]
        ];
    }

    protected function card(): array {
        return [
            "add" => fn(User $user, Column $card) => $this->board()["view"]($user, $card->get_board()),
            "view" => $this->card()["add"],
            "edit" =>$this->card()["add"],
            "delete" => $this->card()["add"]
        ];
    }

    protected function comment(): array {
        $edit = function(User $user, Comment $comment) {
            return $user->is_admin() || $user->is_author($comment);
        };

        $delete = function(User $user, Comment $comment) {
            return $user->is_admin() || $user->is_owner($comment->get_board()) || $user->is_author($comment);
        };

        return [
            "add" => fn(User $user, Comment $comment) => $this->board()["view"]($user, $comment->get_card()),
            "view" => $this->comment()["add"],
            "edit" => $edit,
            "delete" => $delete
        ];
    }
}