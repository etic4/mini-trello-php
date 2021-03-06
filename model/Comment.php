<?php

require_once "autoload.php";


class Comment {
    private ?String $id;
    private String $body;
    private User $author;
    private Card $card;
    private ?DateTime $modifiedAt;
    private ?Datetime $createdAt;


    public function __construct(string $body, User $author, Card $card, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null){
        $this->id=$id;
        $this->body=$body;
        $this->author=$author;
        $this->card=$card;
        $this->createdAt = DateUtils::now_if_null($createdAt);
        $this->modifiedAt = $modifiedAt;
    }

    // --- getters & setters ---

    public function get_id(): ?string {
        return $this->id;
    }

    public function set_id($id){
        $this->id=$id;
    }

    public function get_body(): string {
        return $this->body;
    }

    public function set_body($body){
        $this->body=$body;
    }

    public function get_author(): User {
        return $this->author;
    }

    public function set_author($author){
        $this->author=$author;
    }

    public function get_author_fullName(): string {
        return $this->author->get_fullName();
    }

    public function get_author_id(): string {
        return $this->get_author()->get_id();
    }
 
    public function get_card(): Card {
        return $this->card;
    }

    public function set_card($card){
        $this->card=$card;
    }

    public function get_card_id(): String {
        return $this->card->get_id();
    }

    public function get_board() {
        return $this->card->get_board();
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

}
