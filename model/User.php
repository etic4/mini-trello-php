
<?php

require_once "autoload.php";


class User extends Persist {
    private ?string $id;
    private string $email;
    private string $fullName;
    private string $role;
    private ?string $passwdHash;
    private ?DateTime $registeredAt;
    private ?string $clearPasswd; //Utilisé uniquement au moment du signup pour faciliter validation


    public static function get_tableName(): string {
        return "`user`";
    }

    public static function get_FKName(): string {
        return "`Owner`";
    }

    public static function get_random_password() {
        return "Password1,";
    }


    public function __construct(string $email, string $fullName, ?string $role=null, ?string $clearPasswd=null,
                                ?string $id=null, ?string $passwdHash=null, ?DateTime $registeredAt=null) {
        if (is_null($id)) {
            $passwdHash = Tools::my_hash($clearPasswd);
        }

        $this->id = $id;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->set_role($role);
        $this->passwdHash = $passwdHash;
        $this->clearPasswd = $clearPasswd;
        $this->registeredAt = $registeredAt;
    }


    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_email(): string {
        return $this->email;
    }

    public function set_email(string $email) {
        $this->email = $email;
    }

    public function get_fullName(): string {
        return $this->fullName;
    }

    public function set_fullName(string $fullName) {
        $this->fullName = $fullName;
    }

    public function get_role(): string {
        return $this->role;
    }

    public function set_role($role) {
        $this->role = is_null($role) ? Role::USER : $role;
    }

    public function get_passwdHash(): string {
        return $this->passwdHash;
    }

    public function get_registeredAt(): DateTime {
        return $this->registeredAt;
    }

    public function set_registeredAt(DateTime $registeredAt) {
        $this->registeredAt = $registeredAt;
    }


    // --- booleans ---

    public function is_admin(): bool {
        return $this->role == Role::ADMIN;
    }

    public function is_owner(Board $board): bool {
        return $this->get_id() == $board->get_owner_id();
    }

    public function is_collaborator(Board $board): bool {
        foreach ($board->get_collaborators() as $collaborator) {
            if ($collaborator == $this) {
                return true;
            }
        }
        return false;
    }

    public function is_participant(Card $card):bool {
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

    public function has_collaborating_boards(): bool {
        return count($this->get_collaborating_boards()) > 0;
    }

    // vérifie si l'utilisateur peut delete le comment $comment
    public function can_delete_comment(Card $card, Comment $comment): bool{
        return $this->is_owner($card->get_board()) || $this->is_author($comment);
    }


    // --- validation ---

    // TODO: extraire ça de là (classe propre ?)
    public static function validate_login(string $email, string $password): array {
        if (!empty($email)) {
            $user = User::get_by_email($email);
            if ($user && $user->check_password($password)) {
                return array();
            }
        }
        return array("Invalid username or password");
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

    public static function validate_admin_edit(User $user, string $new_email, string $newFullName) {
        $errors = array();

        //email
        if ($new_email != $user->get_email()) {
            if (!Validation::valid_email($new_email)) {
                $errors[] = "Invalid email";
            }

            if(!Validation::is_unique_email($user->email)){
                $errors[] = "Invalid email";
            }
        }

        //fullName
        if ($newFullName != $user->get_fullName() && !Validation::str_longer_than($user->fullName, 2)) {
            $errors[] = "Name must be at least 3 characters long";
        }

        return $errors;
    }


    // --- sql ---

    public function get_object_map(): array {
        return array(

            "Mail" => $this->get_email(),
            "FullName" => $this->get_fullName(),
            "Role" => $this->get_role(),
            "ID" => $this->get_id(),
            "Password" => $this->get_passwdHash(),
            "RegisteredAt" => $this->get_registeredAt()
        );
    }

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

    public static function get_by_id($id) {
        return self::sql_select("ID", $id);
    }

    public static function get_by_email(string $email): ?User {
        return self::sql_select("Mail", $email);
    }

    public function insert() {
        self::sql_insert();
    }

    public function update() {
        self::sql_update();
    }

    public function cascade_delete(): array {
        return array_merge(Card::get_cards_for_author($this), $this->get_own_boards());
    }

    public function delete() {
        foreach ($this->get_collaborating_boards() as $board) {
            $board->remove_collaborator($this);
        }

        foreach ($this->get_participating_cards() as $card) {
            $card->remove_participant($this);
        }

        self::sql_delete();
    }

    public function get_own_boards(): array {
        return Board::get_users_boards($this);
    }

    public function get_collaborating_boards(): array {
        return  Board::get_collaborating_boards($this);
    }

    public function get_participating_cards(): array {
        return Card::get_participating_cards($this);
    }

    public function get_others_boards(): array {
        return Board::get_others_boards($this);
    }

    public function __toString(): string {
        return $this->get_fullName() . " (" . $this->get_email() . ")";
    }
}
