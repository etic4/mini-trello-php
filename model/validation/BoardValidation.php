<?php


class BoardValidation extends Validation {
    const BoardDao = "BoardDao";

    public function validate_add(string $title): array {
        $this->base_validate_title($title);
        $this->validate_title_unicity($title);

        return $this->get_errors();
    }

    public function validate_edit(string $title, Board $board): array {
        $this->base_validate_title($title);

        if ($board->get_title() != $title) {
            $this->validate_title_unicity($title);
        }

        return $this->get_errors();
    }

    private function validate_title_unicity(string $title) {
        if (!BoardDao::is_title_unique($title)){
            $this->errors[] = "A board with the same title already exists";
        }
    }
}