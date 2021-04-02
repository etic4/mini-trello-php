
<?php

require_once "autoload.php";


class User {
    private ?string $id;
    private string $email;
    private string $fullName;
    private string $role;
    private ?string $passwdHash;
    private ?DateTime $registeredAt;
    private ?string $clearPasswd; //Utilisé uniquement au moment du signup pour faciliter validation

    private array $own_boards;
    private array $admin_visible_boards;
    private array $collaborations;
    private array $participations;

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
        return in_array($board, $board->get_collaborators());
    }

    public function is_participant(Card $card):bool {
        return in_array($card, $card->get_participants());
    }

    public function is_author(Comment $comment): bool {
        return $this->get_id() == $comment->get_author_id();
    }

    public function has_collaborating_boards(): bool {
        return count($this->get_collaborating_boards()) > 0;
    }

    // vérifie si l'utilisateur peut delete le comment $comment
    public function can_delete_comment(Card $card, Comment $comment): bool{
        return $this->is_owner($card->get_board()) || ($this->is_author($comment)  && !isset($show_comment));
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



    public function delete() {
        UserDao::cascade_delete($this);
    }

    public function get_boards(): array {
        return [
            $this->get_own_boards(),
            $this->get_collaborating_boards(),
            $this->get_admin_visible_boards()
        ];
    }

    public function get_admin_visible_boards(): array {
        if (!isset($this->admin_visible_boards) && $this->is_admin()) {
            $this->admin_visible_boards = BoardDao::get_admin_visible_boards($this);
        } else {
            $this->admin_visible_boards = [];
        }
        return $this->admin_visible_boards;
    }

    public function get_own_boards(): array {
        if (!isset($this->own_boards)) {
            $this->own_boards = BoardDao::get_users_boards($this);
        }
        return $this->own_boards;
    }

    public function get_collaborating_boards(): array {
        if (!isset($this->collaborations)) {
            $this->collaborations = CollaborationDao::get_collaborating_boards($this);
        }
        return $this->collaborations;
    }

    public function get_participating_cards($board): array {
        if (!isset($this->participations)) {
            $this->participations = CardDao::get_participating_cards($this, $board);
        }
        return $this->participations;
    }

    public function __toString(): string {
        return $this->get_fullName() . " (" . $this->get_email() . ")";
    }
}
