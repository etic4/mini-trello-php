<?php

require_once 'autoload.php';

class CardValidation extends Validation {
    const CardDao = "CardDao";

    public function validate_add(string $title, Board $board): array {
        $this->base_validate_title($title);
        $this->validate_title_unicity($title, $board);

        return $this->get_errors();
    }

    public function validate_edit(string $title, ?DateTime $due_date, Card $card): array {
        $this->base_validate_title($title);

        if ($card->get_title() != $title) {
            $this->validate_title_unicity($title, $card->get_board());
        }

        if (self::date_before($due_date, new DateTime())) {
            $this->errors[] = "The date can't be in the past";
        }
        return $this->get_errors();
    }

    private function validate_title_unicity(string $title, Board $board) {
        if (!CardDao::is_title_unique($title, $board)){
            $this->errors[] = "A card with the same title already exists in this board";
        }
    }
}