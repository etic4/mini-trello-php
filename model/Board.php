<?php

require_once "autoload.php";

class Board {
    use DateTrait, TitleTrait;

    private ?string $id;
    private User $owner;
    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;

    private array $columns;
    private array $cards;
    private array $collaborators;

    public function __construct(string $title, User $owner, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->createdAt = self::now_if_null($createdAt);
        $this->modifiedAt = $modifiedAt;
    }


    // --- getters & setters ---

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
        if (!isset($this->columns)) {
            $this->columns = ColumnDao::get_columns($this);
        }
        return $this->columns;
    }

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): ?DateTime {
        return $this->modifiedAt;
    }


    public function get_cards(): array {
        if (!isset($this->cards)) {
            $this->cards = [];
            foreach ($this->get_columns() as $col){
                $this->cards = array_merge($this->cards, $col->get_cards());
            }
        }
        return $this->cards;
    }

    // retourne la liste des collaborateurs de ce tableau
    public function get_collaborators(): array {
        if (!isset($this->collaborators)) {
            $this->collaborators = CollaborationDao::get_collaborating_users($this);
        }
        return $this->collaborators;
    }

    public function get_non_owner(): array {
        return array_diff(UserDao::get_all_users(), [$this->get_owner()]);
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


    public function __toString(): string {
        return $this->get_title();
    }

}