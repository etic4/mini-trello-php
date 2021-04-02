<?php


class Participation {
    private string $cardId;
    private string $participantId;

    public function __construct(string $cardId, string $participantId) {
        $this->cardId = $cardId;
        $this->participantId = $participantId;
    }

    public function getCardId(): string {
        return $this->cardId;
    }

    public function getParticipantId(): string {
        return $this->participantId;
    }

    public function get_card() {
        return CardDao::get_by_id($this->cardId);
    }

    public function get_participant() {
        return UserDao::get_by_id($this->participantId);
    }
}