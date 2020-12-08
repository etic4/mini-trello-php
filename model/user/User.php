<?php

require_once "UserModel.php";
require_once "UserValidator.php";
require_once "model/board/Board.php";

class User extends UserModel {
    private $id;
    private $email;
    private $fullName;
    private $passwdHash;
    private $clearPasswd; //Utilisé uniquement au moment du signup
    private $registeredAt;

    protected static function get_tableName(): string {
        return "user";
    }

    public static function validate_login($email, $password): array {
        $errors = [];
        $user = User::get_by_email($email);
        if ($user) {
            if (!$user->check_password($password)) {
                $errors[] = "Le mot de passe ne correspond pas";
            }
        } else {
            $errors[] = "Aucun utilisateur '. $email . ' Enregistrez-vous SVP";
        }
        return $errors;
    }

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

    public function get_own_boards(): array {
        return $this->get_boards_for_view(Board::get_users_boards($this));
    }

    public function get_others_boards(): array {
        return$this->get_boards_for_view(Board::get_others_boards($this));
    }

    // Prépare la liste des boards pour l'affichage
    private function get_boards_for_view($board_array): array {
        $boards = [];
        foreach ($board_array as $board) {
            $user = $board->get_owner_inst();
            $boards[] = array("id"=>$board->get_id(), "title"=>$board->get_title(), "fullName"=>$user->get_fullName());
        }
        return $boards;
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

    public function get_passwdHash(): string {
        return $this->passwdHash;
    }

    public function get_registeredAt(): DateTime {
        return $this->registeredAt;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function check_password($clearPasswd): bool {
        return $this->passwdHash === Tools::my_hash($clearPasswd);
    }

    public function validate(): array {
        $validator = new UserValidator($this);
        return $validator->validate();
    }

}