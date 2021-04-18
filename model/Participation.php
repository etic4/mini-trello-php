<?php


class Participation {
    private Card $card;
    private User $participant;

    public function __construct(Card $card, User $participant) {
        $this->card = $card;
        $this->participant = $participant;
    }

    public function get_CardId(): string {
        return $this->card->get_id();
    }

    public function get_ParticipantId(): string {
        return $this->participant->get_id();
    }

    public function get_card(): Card {
        return $this->card;
    }

    public function get_participant(): User {
        return $this->participant;
    }
}