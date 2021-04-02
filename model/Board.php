<?php

require_once "autoload.php";

class Board {
    use DateTrait, TitleTrait;

    private ?string $id;
    private User $owner;
    private ?array $columns = null;

    private ?array $collaborators = null;

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

    public function set_id(string $id): void {
        $this->id = $id;
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
            $this->columns = ColumnDao::get_columns_for_board($this);
        }
        return $this->columns;
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
        return array_diff(UserDao::get_all(), [$this->get_owner()]);
    }

    public function get_not_collaborating(): array {
        return array_diff($this->get_non_owner(), $this->get_collaborators());
    }

    public function has_user_not_collaborating():bool {
        return count($this->get_not_collaborating()) > 0;
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


    public static function get_by_title(string $title): ?Board {
        return BoardDao::get_by_title($title);
    }

    public function insert() {
        BoardDao::insert($this);
    }

    public function update(): void {
        BoardDao::update($this);
    }

    public function delete(): void {
        foreach ($this->get_collaborators() as $collaborator) {
            $this->remove_collaborator($collaborator);
        }

        BoardDao::cascade_delete($this);
    }

    public function __toString(): string {
        return $this->get_title();
    }

}