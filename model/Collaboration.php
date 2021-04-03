<?php


class Collaboration {
    private Board $board;
    private User $user;

    public function __construct(Board $board, User $user) {
        $this->board = $board;
        $this->collaborator = $user;
    }

    protected function get_boardId(): string {
        return $this->board->get_id();
    }

    protected function get_collaboratorId(): string {
        return $this->collaborator->get_id();
    }

    public function get_board(): Board {
        return $this->board;
    }

    public function get_collaborator(): User {
        return $this->user;
    }
}