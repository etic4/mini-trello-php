<?php


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
        $position = Column::get_columns_count($board);

        $column = new Column($title, $position, $board);

        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals($column->get_title(), $title);
        $this->assertEquals($column->get_board(), $board);

        return $column;
    }

    /**
     * @depends testCreateColumnInstance
     * @param Column $column
     */
    public function testGetIdProducesErrorOnNotSavedInstance(Column $column) {
        $this->expectException(TypeError::class);
        $column->get_id();
        return $column;
    }

    /**
     * @depends testCreateColumnInstance
     * @param Column $column
     */
    public function testCountPlus1AfterInsert(Column $column) {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `column`")->fetch();
        $count = $data["total"];

        $column->insert();
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `column`")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $column;
    }

    /**
     * @depends testCountPlus1AfterInsert
     * @param Column $column
     */
    public function testIdSetAfterInsert(Column $column) {
        $this->assertIsString($column->get_id());
    }
}