<?php


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
        $board = Board::get_by_id(1);
        $this->assertInstanceOf(Board::class, $board);
        $this->assertEquals(1, $board->get_id());
    }

    public function testCreateBoardInstance(): Board {
        $title = "Titre du board";
        $user = User::get_by_id(1);
        $board = new Board($title, $user);

        $this->assertInstanceOf(Board::class, $board);
        $this->assertEquals($board->get_title(), $title);
        $this->assertEquals($board->get_owner(), $user);

        return $board;
    }

    /**
     * @depends testCreateBoardInstance
     * @param Board $board
     */
    public function testGetIdProducesErrorOnNotSavedInstance(Board $board) {
        $this->expectException(TypeError::class);
        $board->get_id();
        return $board;
    }

    /**
     * @depends testCreateBoardInstance
     * @param Board $board
     */
    public function testCountPlus1AfterInsert(Board $board) {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM board")->fetch();
        $count = $data["total"];

        $board->insert();
        $data = self::$db->execute("SELECT COUNT(*) as total FROM board")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $board;
    }

    /**
     * @depends testCountPlus1AfterInsert
     * @param Board $board
     */
    public function testIdSetAfterInsert(Board $board) {
        $this->assertIsString($board->get_id());
    }
}
