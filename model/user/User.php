<?php

require_once "UserValidator.php";

class User {
    private $id;
    private $email;
    private $fullName;
    private $passwdHash;
    private $clearPasswd; //Utilisé uniquement au moment du signup
    private $registeredAt;

    public function __construct($email, $fullName, $passwdHash, $id=null, $registeredAt=null, $clearPasswd=null) {
        if (is_null($id)) {
            $passwdHash = Tools::my_hash($clearPasswd);
        }

        $this->id = $id;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->passwdHash = $passwdHash;
        $this->registeredAt = $registeredAt;
        $this->clearPasswd = $clearPasswd;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_fullName() {
        return $this->fullName;
    }

    public function get_registeredAt(): DateTime {
        return $this->registeredAt;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function check_password($clearPasswd): bool {
        print(Tools::my_hash($clearPasswd));
        return $this->passwdHash === Tools::my_hash($clearPasswd);
    }

    public function validate(): array {
        $validator = new UserValidator($this);
        return $validator->validate();
    }
}