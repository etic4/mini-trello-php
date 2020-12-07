<?php

require_once "model/Validator.php";

class ColumnValidator extends Validator {
    private $column;

    public function __construct($column) {
        parent::__construct();
        $this->column = $column;
    }

    public function validate(): array {

        //title
        if (!$this->str_longer_than($this->board->title, 2)) {
            $this->add_error("Le titre doit comporter au moins 3 caractères");
        }

        return $this->get_errors();
    }
}