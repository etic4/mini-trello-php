<?php

require_once "model/Validator.php";

class BoardValidator extends Validator {
    private $board;

    public function __construct($board) {
        parent::__construct();
        $this->board = $board;
    }

    public function validate(): array {

        //title
        if (!$this->str_longer_than($this->board->title, 2)) {
            $this->add_error("Le titre doit comporter au moins 3 caractères");
        }

        return $this->get_errors();
    }
}