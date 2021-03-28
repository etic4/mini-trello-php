<?php

require_once "autoload.php";

class Board extends Persist {
    use DateTrait, TitleTrait;

    private ?string $id;
    private User $owner;
    private ?array $columns = null;
    private ?array $collaborators = null;


    public static function get_tableName(): string {
        return "`board`";
    }

    public static function get_FKName(): string {
        return "`Board`";
    }


    public function __construct(string $title, User $owner, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    //    GETTERS    //

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_owner(): User {
        return $this->owner;
    }

    public function get_owner_id(): string {
        return $this->owner->get_id();
    }

    public function get_owner_fullName(): string {
        return $this->owner->get_fullName();
    }

    public function get_columns(): array {
        if (is_null($this->columns)) {
            $this->columns = Column::get_columns_for_board($this);
        }
        return $this->columns;
    }

    // nécessaire pour la gestion des authorisations
    public function get_board() {
        return $this;
    }

    // retourne la liste des collaborateurs de ce tableau
    public function get_collaborators(): array {
        if (is_null($this->collaborators)) {
            $sql = "SELECT Collaborator FROM collaborate WHERE Board=:id";
            $param = array("id" => $this->get_id());

            $query = self::execute($sql, $param);
            $collaborators = $query->fetchAll();

            $this->collaborators = [];

            foreach ($collaborators as $collab) {
                $this->collaborators[] = User::get_by_id($collab[0]);
            }
        }
        return $this->collaborators;
    }

    // Ajoute un collaborateur au tableau
    public function add_collaborator(User $user) {
        $sql = "INSERT INTO collaborate (Board, Collaborator) VALUES (:boardId, :collabId)";
        $params = array("boardId" => $this->get_id(), "collabId" => $user->get_id());
        self::execute($sql, $params);
    }

    // supprime un collaborateur du tableau
    public function remove_collaborator(User $user) {
        $sql = "DELETE FROM collaborate where Collaborator=:userId";
        $param = array("userId" => $user->get_id());
        self::execute($sql, $param);
    }

    public function get_non_owner(): array {
        $users = User::sql_select_all();
        return array_diff(User::sql_select_all(), [$this->get_owner()]);
    }

    public function get_not_collaborating(): array {
        return array_diff($this->get_non_owner(), $this->get_collaborators());
    }

    public function has_user_not_collaborating():bool {
        return count($this->get_not_collaborating()) > 0;
    }

    //    SETTERS    //

    public function set_id(string $id): void {
        $this->id = $id;
    }


    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] = "Title must be at least 3 characters long";
            
        }
        if (!Validation::is_unique_title($this->title)) {
            $errors[] = "A board with the same title already exists";
        }

        return $errors;
    }


    // --- sql ---

    protected function get_object_map(): array {
        return array (
            "Title" => $this->get_title(),
            "Owner" => $this->get_owner(),
            "ID" => $this->get_id(),
            "ModifiedAt" => self::sql_date($this->get_modifiedAt())
        );
    }

    protected static function get_instance($data): Board {
        return new Board(
            $data["Title"],
            User::get_by_id($data["Owner"]),
            $data["ID"],
            self::php_date($data["CreatedAt"]),
            self::php_date($data["ModifiedAt"])
        );
    }

    public static function get_by_title(string $title): ?Board {
        return self::sql_select("Title", $title);
    }

    public static function get_users_boards(User $user): array {
        return self::sql_select_all("Owner", $user->get_id());
    }

    public static function get_collaborating_boards(User $user): array {
        $sql =
            "SELECT b.ID, b.Title, b.Owner, b.CreatedAt, b.ModifiedAt 
             FROM collaborate 
             JOIN board b on b.ID = collaborate.Board
             WHERE Collaborator=:id";

        $params = array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            array_push($boards, self::get_instance($rec));
        }

        return $boards;
    }
    
    public static function get_others_boards(User $user): array {
        $sql = 
            "SELECT b.ID, b.Title, b.Owner, b.CreatedAt, b.ModifiedAt 
             from board b where b.ID not in (
                SELECT b1.ID FROM board b1 WHERE b1.Owner=:userId 
                UNION 
                select b2.ID FROM board b2 join collaborate c on c.Board = b2.ID where c.Collaborator=:userId)";
        $params = array("userId" => $user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = [];
        foreach ($data as $rec) {
            $boards[] = self::get_instance($rec);
        }

        return $boards;
    }


    
    public function insert() {
        self::sql_insert();
    }

    public function update(): void {
        self::sql_update();
    }

    protected function cascade_delete() {
        return $this->get_columns();
    }

    public function delete(): void {
        foreach ($this->get_collaborators() as $collaborator) {
            $this->remove_collaborator($collaborator);
        }

        self::sql_delete();
    }

    public function __toString(): string {
        return $this->get_title();
    }

}