<?php namespace model;

require_once "tests/tools/DB.php";
use \Board;
use \Column;
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
        $column = Column::get_by_id(1);
        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals(1, $column->get_id());
    }

    public function testCreateColumnInstance(): Column {
        $title = "Titre de la colonne";
        $board = Board::get_by_id(1);
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
    public function testGetCreatedAtProducesErrorOnNotSavedInstance(Column $column) {
        $this->expectException(TypeError::class);
        $column->get_createdAt();
    }

    /**
     * @depends testCreateColumnInstance
     */
    public function testGetModifiedAtProducesErrorOnNotSavedInstance(Column $column) {
        $this->expectException(TypeError::class);
        $column->get_modifiedAt();
    }

    /**
     * @depends testCreateColumnInstance
     */
    public function testCountPlus1AfterInsert(Column $column): Column {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `column`")->fetch();
        $count = $data["total"];

        $column->insert();
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

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testCreatedAtSetAfterInsert(Column $column) {
        $this->assertInstanceOf(DateTime::class, $column->get_createdAt());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testModifiedAtSetAfterInsert(Column $column) {
        $this->assertInstanceOf(DateTime::class, $column->get_modifiedAt());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testModifiedAtEqualsCreatedAtAfterInsert(Column $inst) {
        $this->assertEquals($inst->get_createdAt(), $inst->get_modifiedAt());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testcreatedAtDoesntEqualToModifiedAtAfterUpdate(Column $inst) {
        sleep(1);
        $inst->update();
        $this->assertNotEquals($inst->get_createdAt(), $inst->get_modifiedAt());
    }

}