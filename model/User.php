
<?php

require_once "CachedGet.php";
require_once "Board.php";
require_once "Validation.php";
require_once "Role.php";


class User extends CachedGet {
    private ?string $id;
    private string $email;
    private string $fullName;
    private string $role;
    private ?string $passwdHash;
    private ?DateTime $registeredAt;
    private ?string $clearPasswd; //Utilisé uniquement au moment du signup pour faciliter validate


    public function __construct(string $email, string $fullName, ?string $role=null, ?string $clearPasswd=null,
                                ?string $id=null, ?string $passwdHash=null, ?DateTime $registeredAt=null) {
        if (is_null($id)) {
            $passwdHash = Tools::my_hash($clearPasswd);
        }

        $this->id = $id;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->role = is_null($role) ? Role::USER : $role;
        $this->passwdHash = $passwdHash;
        $this->clearPasswd = $clearPasswd;
        $this->registeredAt = $registeredAt;
    }


    //    GETTERS    //

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_email(): string {
        return $this->email;
    }

    public function get_fullName(): string {
        return $this->fullName;
    }

    public function get_role(): string {
        return $this->role;
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

    public function set_role($role) {
        $this->role = Role::USER;
        if (Role::is_valid_role($role)) {
            $this->role = $role;
        }
    }

    public function set_registeredAt(DateTime $registeredAt) {
        $this->registeredAt = $registeredAt;
    }

    //    OTHERS

    public function is_admin(): bool {
        return $this->role == Role::ADMIN;
    }

    //    VALIDATION    //

    public static function validate_login($email, $password): array {
        $errors = [];
        $user = User::get_by_email($email);
        if ($user) {
            if (!$user->check_password($password)) {
                $errors[] = "Invalid username or password";
            }
        } else {
            $errors[] = "Invalid username or password";
        }
        return $errors;
    }

    public function check_password($clearPasswd): bool {
        return $this->passwdHash === Tools::my_hash($clearPasswd);
    }

    public function validate(string $password_confirm=null): array {
        $errors = array();
        //email
        if (!Validation::valid_email($this->email)) {
            $errors[] = "Invalid email";
        }
        if(!Validation::is_unique_email($this->email)){
            $errors[] = "Invalid email";
        }
        //fullName
        if (!Validation::str_longer_than($this->fullName, 2)) {
            $errors[] = "Name must be at least 3 characters long";
        }

        //password
        if (!Validation::str_longer_than($this->clearPasswd, 7)) {
            $errors[] = "Password must be at least 8 characters long";
        }

        if (isset($password_confirm)) {
            if (!Validation::is_same_password($this->clearPasswd, $password_confirm)) {
                $errors[] = "Passwords don't match";
            }
        }

        if (!Validation::contains_capitals($this->clearPasswd)) {
            $errors[] = "Password must contain at least 1 uppercase letter";
        }

        if (!Validation::contains_digits($this->clearPasswd)) {
            $errors[] = "Password must contain at least 1 number";
        }

        if (!Validation::contains_non_alpha($this->clearPasswd)) {
            $errors[] = "Password must contain at least one special character";
        }

        return $errors;
    }

    public function is_owner(Board $board): bool {
        return $this == $board->get_owner();
    }

    public function is_collaborator(Board $board): bool {
        foreach ($board->get_collaborators() as $collaborator) {
            if ($collaborator == $this) {
                return true;
            }
        }
        return false;
    }

    public function is_participant(Card $card) {
        foreach ($card->get_participants() as $participant) {
            if ($participant == $this) {
                return true;
            }
            return false;
        }
    }

    public function is_author(Comment $comment): bool {
        return $this->get_id() == $comment->get_author_id() && !isset($show_comment);
    }


    public function get_own_boards(): array {
        return Board::get_users_boards($this);
    }

    public function get_collaborating_boards(): array {
        return  Board::get_collaborating_boards($this);
    }

    public function get_others_boards(): array {
        return Board::get_others_boards($this);
    }

    public function has_collaborating_boards(): bool {
        return count($this->get_collaborating_boards()) > 0;
    }


    //    QUERIES    //

    /* Retourne une instance de User à partir d'une colonne de la DB */
    protected static function get_instance($data): User {
        return new User(
            $data["Mail"],
            $data["FullName"],
            $data["Role"],
            null,
            $data["ID"],
            $data["Password"],
            new DateTime($data["RegisteredAt"])
        );
    }

    // J'essaie de tirer avantage du cache tel qu'il est implémenté
    public static function get_all() {
        $sql = "SELECT ID FROM user";
        $query = self::execute($sql, null);
        $data = $query->fetchAll();

        $userList = [];
        if ($query->rowCount() > 0) {
            foreach ($data as $userId) {
                $userList[] = self::get_by_id($userId[0]);
            }
        }
        return $userList;
    }

    public static function get_by_email(string $email): ?User {
        $sql = 
            "SELECT * 
             FROM user 
             WHERE Mail=:email";
        $query = self::execute($sql, array("email"=>$email));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return self::get_instance($data);
    }

    public function insert() {
        $sql = 
            "INSERT INTO user(Mail, FullName, Role, Password) 
             VALUES(:email, :fullName, :role, :passwdHash)";
        $params = array(
            "email" => $this->get_email(), 
            "fullName" => $this->get_fullName(),
            "role" => $this->get_role(),
            "passwdHash" => $this->get_passwdHash()
        );
        $this->execute($sql, $params);
        $user = self::get_by_id($this->lastInsertId());
        $this->set_id($user->get_id());
        $this->set_registeredAt($user->get_registeredAt());
    }

    public function update() {
        $sql = 
            "UPDATE user 
             SET Mail=:email, FullName=:fullName, Role=:role, Password=:passwdHash 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "email" => $this->get_email(), 
            "fullName" => $this->get_fullName(),
            "role" => $this->get_role(),
            "passwdHash" => $this->get_passwdHash());
        $this->execute($sql, $params);
    }

    public function delete() {
        foreach ($this->get_own_boards() as $board) {
            $board->delete();
        }
        $sql = 
            "DELETE FROM user 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }


    // vérifie si l'utilisateur peut delete le comment $comment
    public function can_delete_comment(Card $card, Comment $comment): bool{
        return $this->is_owner($card->get_board()) || $this->is_author($comment);
    }

    public function __toString(): string {
        return $this->get_fullName() . " (" . $this->get_email() . ")";
    }
}
