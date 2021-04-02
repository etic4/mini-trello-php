<?php

require_once "autoload.php";


class Card {
    use DateTrait, TitleTrait;

    private ?string $id;
    private string $body;
    private string $position;
    private User $author;
    private Column $column;

    // !! les dates de création et de modification sont dans le trait !!
    private ?DateTime $dueDate;

    private ?array $comments = null;
    private ?array $participants = null;


    public static function get_new(string $title, User $author, string $column_id, ?DateTime $dueDate=null ): Card {
        $column = ColumnDao::get_by_id($column_id);
        return new Card(
            $title,
            "",
            count($column->get_cards()),
            $author,
            $column,
            $dueDate
        );
    }


    public function __construct(string $title, string $body, int $position, User $author, Column $column, ?Datetime $dueDate=null,
                                ?string $id = null, ?DateTime $createdAt=null, ?DateTime $modifiedAt=null) {

        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->position = $position;
        $this->author = $author;
        $this->column = $column;
        $this->dueDate = $dueDate;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id(string $id) {
        $this->id = $id;
    }

    public function get_body(): string {
        return $this->body;
    }

    public function set_body(string $body) {
        $this->body = $body;
    }


    public function get_position(): string {
        return $this->position;
    }

    public function set_position(string $position) {
        $this->position = $position;
    }

    public function get_author(): User {
        return $this->author;
    }

    public function get_author_name(): string {
        return $this->author->get_fullName();
    }

    public function get_author_id(): string {
        return $this->author->get_id();
    }

    public function get_column(): Column {
        return $this->column;
    }

    public function set_column(Column $column) {
        $this->column = $column;
    }

    public function get_dueDate(): ?DateTime {
        return $this->dueDate;
    }

    public function set_dueDate(DateTime  $dueDate) {
        $this->dueDate = $dueDate;
    }

    public function get_comments(): array {
        if (is_null($this->comments)) {
            $this->comments = CommentDao::get_comments_for_card($this);
        }
        return $this->comments;
    }

    // --- demeter ---

    public function get_column_title(): string {
        return $this->column->get_title();
    }

    public function get_column_cards(): array {
        return $this->column->get_cards();
    }

    public function get_column_id(): string {
        return $this->column->get_id();
    }

    public function get_column_position(): string {
        return $this->column->get_position();
    }

    public function get_all_columns(): array {
        return $this->column->get_board_columns();
    }

    public function get_board() {
        return $this->column->get_board();
    }

    public function get_board_id(): string {
        return $this->column->get_board_id();
    }

    public function get_board_title(): string {
        return $this->column->get_board_title();
    }
 
    public function get_board_owner(): User{
        return $this->column->get_board_owner();
    }



    // --- booleans ---

    public function is_due(): bool {
        if ($this->get_dueDate() != null) {
            return $this->get_dueDate()->diff(new Datetime())->s > 0;
        }
        return false;
    }

    public function has_participants(): bool {
        return count($this->get_participants()) > 0;
    }

    public function has_collabs_no_participating(): bool {
        return count($this->get_collabs_no_participating()) > 0;
    }

    public function has_dueDate(): bool {
        return !is_null($this->get_dueDate());
    }

    public function get_collabs_no_participating(): array {
        $collab = $this->get_board()->get_collaborators();
        $collab[] = $this->get_board()->get_owner();

        return array_diff($collab, $this->get_participants());
    }



    public function get_participants(): array {
        if (is_null($this->participants)) {
            $sql = "SELECT Participant FROM participate WHERE Card=:id";
            $param = array("id" => $this->get_id());

            $query = self::execute($sql, $param);
            $participants = $query->fetchAll();

            $this->participants = [];

            foreach ($participants as $particip) {
                $this->participants[] = User::get_by_id($particip[0]);
            }
        }
        return $this->participants;
    }

    public function add_participant(User $user) {
        $sql = "INSERT INTO participate (Participant, Card) VALUES (:userId, :cardId)";
        $params = array("cardId" => $this->get_id(), "userId" => $user->get_id());
        self::execute($sql, $params);
    }

    public function remove_participant(User $user) {
        $sql = "DELETE FROM participate where Participant=:userId and Card=:cardId";
        $param = array("userId" => $user->get_id(), "cardId" => $this->get_id());
        self::execute($sql, $param);
    }

    //    VALIDATION    //

    // fonction de validation en cas d'ajout de nouvelle carte
    public function validate($update=null): array {
        $errors = [];
        if (!Validation::str_longer_than($this->get_title(), 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }

        $title_count = CardDao::title_count($this);
        if ($title_count != 0) {
            $stored = CardDao::get_by_id($this->get_id());
            if (is_null($update) || ($update == true && $stored->get_title() != $this->get_title())) {
                $errors[] = "Title already exists in this board";
            }
        }

        if (!Validation::is_date_after($this->get_dueDate(), new DateTime())) {
            $errors[] = "the date must be at least 10 seconds later than now";
        }

        return $errors;
    }



    //    TOOLS    //

    public function has_comments(): bool {
        return count($this->get_comments()) > 0;
    }

    public function get_comments_count(): int {
        return count($this->get_comments());
    }
 
    public function is_first(): bool {
        return $this->get_position() == 0;
    }

    public function is_last(): bool {
        return $this->get_position() == count($this->get_column_cards()) - 1;
    }



    //renvoie un tableau de cartes triées par leur position dans la colonne dont la colonne est $column;
    public static function get_cards_for_column(Column $column): array {
        $sql = 
            "SELECT * 
             FROM card 
             WHERE `Column`=:column 
             ORDER BY Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            $card = self::from_query($rec);
            self::add_instance_under_id($card);
            $cards[] = $card;
        }
        return $cards;
    }


    //insère la carte dans la db, la carte reçoit un nouvel id.
    public function insert() {
        self::sql_insert();
    }

    //met à jour la db avec les valeurs des attributs actuels de l'objet Card
    public function update() {
        self::sql_update();
    }


    protected function cascade_delete() {
        return $this->get_comments();
    }

    //supprime la carte de la db, ainsi que tous les commentaires liés a cette carte
    public function delete() {
        self::sql_delete();
    }


    //    MOVE CARD    //

    public function move_up(): void {
        $pos = $this->get_position();

        if ($pos > 0) {
            $target = $this->get_column_cards()[$pos-1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            CardDao::update($this);
            $target->update();
        }
    }

    public function move_down(): void {
        $pos = $this->get_position();
        $cards = $this->get_column_cards();

        if ($pos < sizeof($cards)-1) {
            $target = $cards[(int)$pos + 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            CardDao::update($this);
            $target->update();;
        }
    }

    public function move_left(): void {
        $pos = $this->get_column_position();

        if ($pos > 0) {
            $target = $this->get_all_columns()[$pos-1];

            /*Faut décrémenter les suivantes avant de changer de colonne*/
            CardDao::decrement_following_cards_position($this);

            $this->set_column($target);
            $this->set_position(count($target->get_cards()));
            CardDao::update($this);
        }
    }

    public function move_right(): void {
        $pos = $this->get_column_position();
        $colList = $this->get_all_columns();

        if ($pos < sizeof($colList)-1) {
            $target = $colList[$pos+1];

            /*Faut décrémenter les suivantes avant de changer de colonne*/
            CardDao::decrement_following_cards_position($this);

            $this->set_column($target);
            $this->set_position(count($target->get_cards()));
            CardDao::update($this);

        }
    }


    public function __toString(): string {
        return $this->get_title();
    }
}