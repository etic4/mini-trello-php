<?php

require_once "framework/Model.php";
require_once "Board.php";
require_once "Validation.php";

class User extends Model {
    private ?string $id;
    private string $email;
    private string $fullName;
    private ?string $passwdHash;
    private DateTime $registeredAt;
    private ?string $clearPasswd; //Utilisé uniquement au moment du signup pour faciliter validate


    public function __construct(string $email, string $fullName, ?string $clearPasswd=null,
                                ?string $id=null, ?string $passwdHash=null, ?DateTime $registeredAt=null) {
        if (is_null($id)) {
            $passwdHash = Tools::my_hash($clearPasswd);
        }

        $this->id = $id;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->passwdHash = $passwdHash;
        $this->set_registeredAt($registeredAt);
        $this->clearPasswd = $clearPasswd;
    }


    //    GETTERS    //

    public function get_id(): string {
        return $this->id;
    }

    public function get_email(): string {
        return $this->email;
    }

    public function get_fullName(): string {
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

    public function set_registeredAt(?Datetime $registeredAt) {
        if (is_null($registeredAt)){
            $this->registeredAt = new Datetime("now");
        } else {
            $this->registeredAt = $registeredAt;
        }
    }


    //    VALIDATION    //

    public static function validate_login($email, $password): array {
        $errors = [];
        $user = User::get_by_email($email);
        if ($user) {
            if (!$user->check_password($password)) {
                $errors[] = "You have entered an invalid username or password";
            }
        } else {
            $errors[] = "You have entered an invalid username or password";
        }
        return $errors;
    }

    public function check_password($clearPasswd): bool {
        return $this->passwdHash === Tools::my_hash($clearPasswd);
    }

    public function validate(): array {
        $errors = array();
        //email
        if (!Validation::valid_email($this->email)) {
            $errors[] = "Email non valide";
        }

        //fullName
        if (!Validation::str_longer_than($this->fullName, 2)) {
            $errors[] = "Le nom doit comporter au moins 3 caractères";
        }

        //password
        if (!Validation::str_longer_than($this->clearPasswd, 7)) {
            $errors[] = "Le mot de passe doit comporter au moins 8 caractères";
        }

        if (!Validation::contains_capitals($this->clearPasswd)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre capitale";
        }

        if (!Validation::contains_digits($this->clearPasswd)) {
            $errors[] = "Le mot de passe doit contenir au moins une chiffre capitale";
        }

        if (!Validation::contains_non_alpha($this->clearPasswd)) {
            $errors[] = "Le mot de passe doit contenir au moins une caractère spécial";
        }

        return $errors;
    }


    //    QUERIES    //

    public static function get_by_id($id): ?User {
        $sql = 
            "SELECT * 
             FROM user 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User(
                $data["Mail"], 
                $data["FullName"], 
                $data["Password"], 
                $data["ID"],  
                $registeredAt
            );
        }
    }

    public static function get_by_email($email): ?User {
        $sql = 
            "SELECT * 
             FROM user 
             WHERE Mail=:email";
        $query = self::execute($sql, array("email"=>$email));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User(
                $data["Mail"], 
                $data["FullName"], 
                $data["Password"], 
                $data["ID"],  
                $registeredAt
            );
        }
    }

    public function insert(): User {
        $sql = 
            "INSERT INTO user(Mail, FullName, Password) 
             VALUES(:email, :fullName, :passwdHash)";
        $params = array(
            "email" => $this->get_email(), 
            "fullName" => $this->get_fullName(),
            "passwdHash" => $this->get_passwdHash()
        );
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $sql = 
            "UPDATE user 
             SET Mail=:email, FullName=:fullName, Password=:passwdHash 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "email" => $this->get_email(), 
            "fullName" => $this->get_fullName(),
            "passwdHash" => $this->get_passwdHash());
        $this->execute($sql, $params);
    }

    public function delete() {
        $sql = 
            "DELETE FROM user 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }


    //    TOOLBOX    //

    // Prépare la liste des boards pour l'affichage
    private function get_boards_for_view($board_array): array {
        $boards = [];
        foreach ($board_array as $board) {
            $user = $board->get_owner();
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
