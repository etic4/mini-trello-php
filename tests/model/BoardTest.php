<?php namespace model;

require_once "tests/tools/DB.php";
use \Board;
use \ColumnDao;
use \Datetime;
use \TypeError;
use \tools\DB;
use \UserDao;

class BoardTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetBoardInstanceFromDB() {
        $board = ColumnDao::get_by_id(1);
        $this->assertInstanceOf(Board::class, $board);
        $this->assertEquals(1, $board->get_id());
    }

    public function testCreateBoardInstance(): Board {
        $title = "Titre du board";
        $user = UserDao::get_by_id(1);
        $board = new Board($title, $user);

        $this->assertInstanceOf(Board::class, $board);
        $this->assertEquals($board->get_title(), $title);
        $this->assertEquals($board->get_owner(), $user);

        return $board;
    }

    /**
     * @depends testCreateBoardInstance
     */
    public function testGetIdReturnNullOnNotSavedInstance(Board $board) {
        $this->assertEquals(null, $board->get_id());
    }


    /**
     * @depends testCreateBoardInstance
     */
    public function testCountPlus1AfterInsert(Board $board): Board {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM board")->fetch();
        $count = $data["total"];

        $board = ColumnDao::insert($board);
        $data = self::$db->execute("SELECT COUNT(*) as total FROM board")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $board;
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testIdSetAfterInsert(Board $board) {
        $this->assertIsString($board->get_id());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testcreatedAtDoesntEqualToModifiedAtAfterUpdate(Board $board) {
        sleep(1);
        ColumnDao::update($board);
        $this->assertNotEquals($board->get_createdAt(), $board->get_modifiedAt());
    }

}
