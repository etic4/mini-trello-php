<?php

require_once "model/Validator.php";

//
class UserValidator extends Validator {
    private $user;

    public function __construct($board) {
        parent::__construct();
        $this->user = $board;
    }

    public function validate(): array {
        //email
        if (!$this->valid_email($this->user->email)) {
            $this->add_error("Email non valide");
        }

        //fullName
        if (!$this->str_longer_than($this->user->fullName, 2)) {
            $this->add_error("Le nom doit comporter au moins 3 caractères");
        }

        //password
        if (!$this->str_longer_than($this->user->clearPasswd, 7)) {
            $this->add_error("Le mot de passe doit comporter au moins 8 caractères");
        }

        if (!$this->contains_capitals($this->user->clearPasswd)) {
            $this->add_error("Le mot de passe doit contenir au moins une lettre capitale");
        }

        if (!$this->contains_digits($this->user->clearPasswd)) {
            $this->add_error("LLe mot de passe doit contenir au moins une chiffre capitale");
        }

        if (!$this->contains_non_alpha($this->user->clearPasswd)) {
            $this->add_error("Le mot de passe doit contenir au moins une caractère spécial");
        }

        return $this->get_errors();
    }
}