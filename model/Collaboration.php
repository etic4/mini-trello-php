<?php


class Collaboration {
    private string $boardId;
    private string $collaboratorId;

    public function __construct(string $boardId, string $collaboratorId) {
        $this->boardId = $boardId;
        $this->collaboratorId = $collaboratorId;
    }

    protected function get_boardId(): string {
        return $this->boardId;
    }

    protected function get_collaboratorId(): string {
        return $this->collaboratorId;
    }

    public function get_board() {
        return BoardDao::get_by_id($this->boardId);
    }

    public function get_collaborator() {
        return UserDao::get_by_id($this->collaboratorId);
    }
}