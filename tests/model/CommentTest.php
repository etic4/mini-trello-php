<?php namespace model;

require_once "tests/tools/DB.php";
use \Card;
use CardDao;
use \Comment;
use CommentDao;
use \User;
use \Datetime;
use \TypeError;
use \tools\DB;
use UserDao;

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
        $comment = CommentDao::get_by_id(1);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals(1, $comment->get_id());
    }

    public function testCreateCommentInstance(): Comment {
        $body = "Texte du commentaire";
        $author = UserDao::get_by_id(1);
        $card = CardDao::get_by_id(1);
        $comment = new Comment($body, $author, $card);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($comment->get_body(), $body);
        $this->assertEquals($comment->get_author(), $author);
        $this->assertEquals($comment->get_card(), $card);

        return $comment;
    }

    /**
     * @depends testCreateCommentInstance
     */
    public function testGetIdReturnNullOnNotSavedInstance(Comment $comment) {
        $this->assertEquals(null, $comment->get_id());
    }

    /**
     * @depends testCreateCommentInstance
     */
    public function testCountPlus1AfterInsert(Comment $comment): Comment {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM comment")->fetch();
        $count = $data["total"];

        $comment = CommentDao::insert($comment);
        $data = self::$db->execute("SELECT COUNT(*) as total FROM comment")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $comment;
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testIdSetAfterInsert(Comment $comment) {
        $this->assertIsString($comment->get_id());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testCreatedAtSetAfterInsert(Comment $comment) {
        $this->assertInstanceOf(DateTime::class, $comment->get_createdAt());
    }
}