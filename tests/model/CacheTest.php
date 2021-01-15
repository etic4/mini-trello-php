<?php namespace model;

require_once "tests/tools/DB.php";
use \User;
use \Board;
use \Column;
use \Card;
use \Comment;
use \tools\DB;


class CacheTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetByIdOnDifferentClassesReturnsInstancesOfRightClass() {
        $user = User::get_by_id(1);
        $board = Board::get_by_id(1);
        $column = Column::get_by_id(1);
        $card = Card::get_by_id(1);
        $comment = Comment::get_by_id(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Board::class, $board);
        $this->assertInstanceOf(Column::class, $column);
        $this->assertInstanceOf(Card::class, $card);
        $this->assertInstanceOf(Comment::class, $comment);

    }

    public function testMultipleGetByIDReturnsSameInstance() {
        $board = Board::get_by_id(1);
        $board->set_title("!!!!");  //n'est pas update en DB

        $board2 = Board::get_by_id(1);  // sans cache retournerait une nouvelle instance depuis la DB
        $this->assertSame($board, $board2);

        $this->assertEquals($board->get_title(), $board2->get_title());
    }

}