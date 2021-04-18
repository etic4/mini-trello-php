<?php


class ColumnValidation extends Validation {
    const ColumnDao = "ColumnDao";

    public function validate_add(string $title, Board $board): array {
        $this->base_validate_title($title);
        $this->validate_title_unicity($title, $board);

        return $this->get_errors();
    }

    public function validate_edit(string $title, Column $column): array {
        $this->base_validate_title($title);

        if ($column->get_title() != $title) {
            $this->validate_title_unicity($title, $column->get_board());
        }

        return $this->get_errors();
    }

    private function validate_title_unicity(string $title, Board $board) {
        if (!ColumnDao::is_title_unique($title, $board)){
            $this->errors[] = "A column with the same title already exists in this board";
        }
    }
}