<?php namespace model;

require_once "tests/tools/DB.php";
use \Board;
use ColumnDao;
use \Column;
use BoardDao;
use \Datetime;
use \TypeError;
use \tools\DB;


class ColumnTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetColumnInstanceFromDB() {
        $column = ColumnDao::get_by_id(1);
        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals(1, $column->get_id());
    }

    public function testCreateColumnInstance(): Column {
        $title = "Titre de la colonne";
        $board = BoardDao::get_by_id(1);
        $position = count($board->get_columns());

        $column = new Column($title, $position, $board);

        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals($column->get_title(), $title);
        $this->assertEquals($column->get_board(), $board);

        return $column;
    }

    /**
     * @depends testCreateColumnInstance
     */
    public function testGetIdReturnNullOnNotSavedInstance(Column $column) {
        $this->assertEquals(null, $column->get_id());
    }

    /**
     * @depends testCreateColumnInstance
     */
    public function testCountPlus1AfterInsert(Column $column): Column {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `column`")->fetch();
        $count = $data["total"];

        $column = ColumnDao::insert($column);
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `column`")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $column;
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testIdSetAfterInsert(Column $column) {
        $this->assertIsString($column->get_id());
    }


}