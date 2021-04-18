<?php

require_once "autoload.php";


class Card {
    private string $title;
    private ?string $id;
    private string $body;
    private string $position;
    private User $author;
    private Column $column;
    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;

    // !! les dates de création et de modification sont dans le trait !!
    private ?DateTime $dueDate;

    private array $comments;
    private array $participants;
    private array $collaborators;


    public static function new(string $title, User $author, string $column_id, ?DateTime $dueDate=null ): Card {
        $column = ColumnDao::get_by_id($column_id);
        return new Card( $title,  "", count($column->get_cards()), $author, $column, $dueDate);
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
        $this->createdAt = DateUtils::now_if_null($createdAt);
        $this->modifiedAt = $modifiedAt;
    }


    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id(string $id) {
        $this->id = $id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
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

    public function get_author_fullName(): string {
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

    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): ?DateTime {
        return $this->modifiedAt;
    }

    public function set_modifiedAt(DateTime $dateTime) {
        $this->modifiedAt = $dateTime;
    }

    public function get_dueDate(): ?DateTime {
        return $this->dueDate;
    }

    public function set_dueDate(?DateTime  $dueDate) {
        $this->dueDate = $dueDate;
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


    public function get_comments_count(): int {
        if (!isset($this->comments)) {
            return CommentDao::comments_count($this);
        }
        return count($this->get_comments());
    }

    // --- lazy get lists ---

    public function get_comments(): array {
        if (!isset($this->comments)) {
            $this->comments = CommentDao::get_comments($this);
            $this->sort_comments();
        }
        return $this->comments;
    }

    // sort les commentaires par date de modification / création
    private function sort_comments() {
        $comp = function(Comment $com1, Comment $com2) {
            $com1_stamp = DateUtils::most_recent_timestamp($com1->get_createdAt(), $com1->get_modifiedAt());
            $com2_stamp = DateUtils::most_recent_timestamp($com2->get_createdAt(), $com2->get_modifiedAt());
            return  $com2_stamp - $com1_stamp;
        };
        usort( $this->comments, $comp);
    }

    public function get_boards_cards(): array {
        return $this->get_board()->get_cards();
    }

    public function get_collaborators(): array {
        if (!isset($this->collaborators)) {
            $this->collaborators = $this->get_board()->get_collaborators();
        }
        return $this->collaborators;
    }

    public function get_collaborators_not_participating() {
        $collabs_and_owner = $this->get_collaborators();
        $collabs_and_owner[] = $this->get_board_owner();

        $collab_not_participating = array_diff($collabs_and_owner, $this->get_participants());
        return $collab_not_participating;


    }

    public function get_participants(): array {
        if (!isset($this->participants)) {
            $this->participants = ParticipationDao::get_participating_users($this);
        }
        return $this->participants;
    }


    // --- booleans ---

    public function has_comments(): bool {
        return count($this->get_comments()) > 0;
    }

    public function is_first(): bool {
        return $this->get_position() == 0;
    }

    public function is_last(): bool {
        return $this->get_position() == count($this->get_column_cards()) - 1;
    }

    public function is_due(): bool {
        if ($this->get_dueDate() != null) {
            $interval = $this->get_dueDate()->diff(new Datetime());
            return $interval->invert == 0 && $interval->d > 0;
        }
        return false;
    }

    public function has_participants(): bool {
        return count($this->get_participants()) > 0;
    }

    public function has_collabs_no_participating(): bool {
        return count($this->get_collaborators_not_participating()) > 0;
    }

    public function has_dueDate(): bool {
        return !is_null($this->get_dueDate());
    }


    // --- move ---

    public function move_up(): void {
        $pos = $this->get_position();

        if ($pos > 0) {
            $target = $this->get_column_cards()[$pos-1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            CardDao::update($this);
            CardDao::update($target);
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
            CardDao::update($target);
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