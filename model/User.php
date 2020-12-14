<?php

//require_once "UserModel.php";
//require_once "UserValidator.php";
require_once "framework/Model.php";
require_once "model/Board.php";

class User extends Model {
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


    //    GETTERS    //

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


    //    SETTERS    //

    public function set_id($id) {
        $this->id = $id;
    }


    //    VALIDATION    //

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

    public function check_password($clearPasswd): bool {
        return $this->passwdHash === Tools::my_hash($clearPasswd);
    }

    public function validate(): array {
        $validator = new Validator($this);
        return $validator->validate();
    }


    //    QUERIES    //

    public static function get_by_id($id) {
        $sql = "SELECT * FROM user WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
        }
    }

    public static function get_by_email($email): ?User {
        $sql = "SELECT * FROM user WHERE Mail=:email";
        $query = self::execute($sql, array("email"=>$email));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
        }
    }

    public function insert() {
        $sql = "INSERT INTO user(Mail, FullName, Password) VALUES(:email, :fullName, :passwdHash)";
        $params = array("email"=>$this->get_email(), "fullName"=>$this->get_fullName(),"passwdHash"=>$this->get_passwdHash());
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $sql = "UPDATE user SET Mail=:email, FullName=:fullName, Password=:passwdHash WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "email"=>$this->get_email(), "fullName"=>$this->get_fullName(),
            "passwdHash"=>$this->get_passwdHash());
        $this->execute($sql, $params);
    }

    public function delete() {
        $sql = "DELETE FROM user WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }


    //    TOOLBOX    //

    // Prépare la liste des boards pour l'affichage
    private function get_boards_for_view($board_array): array {
        $boards = [];
        foreach ($board_array as $board) {
            $user = $board->get_owner_inst();
            $boards[] = array("id"=>$board->get_id(), "title"=>$board->get_title(), "fullName"=>$user->get_fullName());
        }
        return $boards;
    }

    public function get_own_boards(): array {
        return $this->get_boards_for_view(Board::get_users_boards($this));
    }

    public function get_others_boards(): array {
        return$this->get_boards_for_view(Board::get_others_boards($this));
    }

}
