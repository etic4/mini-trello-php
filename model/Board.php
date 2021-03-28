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

    protected function cascade_delete() {
        return $this->get_columns();
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


    //    QUERIES    //

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
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Title = :title";
        $params = array("title" => $title);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            $board = self::get_instance($data);
            return $board;
        }
    }

    public static function get_users_boards(User $user): array {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Owner=:id";
        $params = array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            array_push($boards, self::get_instance($rec));
        }

        return $boards;
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
        $sql = 
            "INSERT INTO board(Title, Owner) 
             VALUES(:title, :owner)";
        $params = array(
            "title" => $this->get_title(),
            "owner" => $this->get_owner_id(),
            );
        $this->execute($sql, $params);
        $id = $this->lastInsertId();
        $this->set_id($id);
        $this->set_dates_from_db();
    }


    public function update(): void {
        $sql = 
            "UPDATE board 
             SET Title=:title, Owner=:owner, ModifiedAt=NOW() 
             WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(), 
            "owner" => $this->get_owner_id(),
        );
        
        $this->execute($sql, $params);
        $this->set_dates_from_db();
    }


    public function delete(): void {
        foreach ($this->get_collaborators() as $collaborator) {
            $this->remove_collaborator($collaborator);
        }

        foreach ($this->get_columns() as $col) {
            $col->delete();
        }

        $sql = "DELETE FROM board 
                WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    public function __toString(): string {
        return $this->get_title();
    }

}