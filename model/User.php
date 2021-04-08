
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


    public static function get_random_password(): string {
        return "Password1,";
    }

    public static function from_post(): User {
        return new User(Post::get("email"), Post::get("fullName"), Post::get("role", "user"), Post::get("password"));
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
        $this->registeredAt = DateUtils::now_if_null($registeredAt);
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

    public function set_password(string $clear_password) {
        $this->passwdHash = Tools::my_hash($clear_password);
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
        return in_array($this, $board->get_collaborators());
    }

    public function is_participant(Card $card):bool {
        return in_array($this, $card->get_participants());
    }

    public function is_author(Comment $comment): bool {
        return $this->get_id() == $comment->get_author_id();
    }

    public function has_collaborating_boards(): bool {
        return CollaborationDao::has_collaborating_boards($this);
    }

    public function can_delete_comment(Comment $comment): bool{
        return $this->is_admin() || $this->is_owner($comment->get_board()) || ($this->is_author($comment));
    }


    // --- pas de lazzy loading possible sur ces listes pcq l'instance de User est conservée en session
    // et que ces listes, dans un environnement multiutilistaeur, ne seraient pas synchronisées
    // avec les changements effectués par d'autres utilisateurs
    // Ne serait faisable que si par exemple l'id de User serait conservé en $_SESSION et qu'une instance serait
    // reconstruite à chaque requête

    public function get_own_boards(): array {
        return BoardDao::get_users_boards($this);
    }

    public function get_admin_visible_boards(): array {
        return BoardDao::get_admin_visible_boards($this);
    }

    public function get_collaborating_boards(): array {
        return CollaborationDao::get_collaborating_boards($this);
    }

    public function get_participating_cards($board): array {
        return CardDao::get_participating_cards($this, $board);
    }

    public function __toString(): string {
        return $this->get_fullName() . " (" . $this->get_email() . ")";
    }
}
