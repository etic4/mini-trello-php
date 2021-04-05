<?php

require_once 'autoload.php';

class CardValidation extends TitleValidation {
    public function __construct(string $dao) {
        parent::__construct($dao);
    }

    public function validate(object $card, $update=false): CardValidation {
        parent::validate($update, $update);

        if (self::date_before($card->get_dueDate(), new DateTime())) {
            $this->errors[] = "The date can't be in the past";
        }

        return $this;
    }
}