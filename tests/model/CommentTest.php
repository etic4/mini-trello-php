<?php


class CommentTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetCommentInstanceFromDB() {
        $comment = Comment::get_by_id(1);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals(1, $comment->get_id());
    }

    public function testCreateCommentInstance(): Comment {
        $body = "Texte du commentaire";
        $author = User::get_by_id(1);
        $card = Card::get_by_id(1);
        $comment = new Comment($body, $author, $card);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($comment->get_body(), $body);
        $this->assertEquals($comment->get_author(), $author);
        $this->assertEquals($comment->get_card(), $card);

        return $comment;
    }

    /**
     * @depends testCreateCommentInstance
     * @param Comment $comment
     */
    public function testGetIdProducesErrorOnNotSavedInstance(Comment $comment) {
        $this->expectException(TypeError::class);
        $comment->get_id();
        return $comment;
    }

    /**
     * @depends testCreateCommentInstance
     * @param Comment $comment
     */
    public function testCountPlus1AfterInsert(Comment $comment) {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM comment")->fetch();
        $count = $data["total"];

        $comment->insert();
        $data = self::$db->execute("SELECT COUNT(*) as total FROM comment")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $comment;
    }

    /**
     * @depends testCountPlus1AfterInsert
     * @param Comment $comment
     */
    public function testIdSetAfterInsert(Comment $comment) {
        $this->assertIsString($comment->get_id());
    }
}