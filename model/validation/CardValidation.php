<?php

require_once 'autoload.php';

class CardValidation extends Validation {
    const CardDao = "CardDao";

    public function validate_add(string $title, Board $board): array {
        $this->base_validate_title($title);
        $this->check_title_unicity($title, $board);

        return $this->get_errors();
    }

    public function validate_edit(string $title, ?DateTime $due_date, Card $card): array {
        $this->base_validate_title($title);

        $this->check_title_unicity($title, $card->get_board(), $card);
        $this->check_due_date_after_creation_date($due_date, $card);

        return $this->get_errors();
    }

    public function validate_title_unicity(string $title, Board $board, Card $card=null): array {
        $this->check_title_unicity($title, $board, $card);

        return $this->get_errors();
    }

    public function validate_due_date(?DateTime $due_date, Card $card): array {
        $this->check_due_date_after_creation_date($due_date, $card);

        return $this->get_errors();
    }

    private function check_title_unicity(string $title, Board $board, Card $card=null) {
        if ($card == null || $card->get_title() != $title) {
            if (!CardDao::is_title_unique($title, $board)) {
                $this->errors[] = "A card with the same title already exists in this board";
            }
        }
    }

    private function check_due_date_after_creation_date(?DateTime $due_date, Card $card) {
        if (self::date_before($due_date, $card->get_createdAt())) {
            $this->errors[] = "The due date can't be before card's creation date";
        }
    }
}