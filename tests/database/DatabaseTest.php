<?php namespace database;

use PHPUnit\Framework\TestCase;
use tools\DB;

class DatabaseTest extends TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function tableNamesProvider(): array {
        return [
            ["user", 5],
            ["board", 3],
            ["`column`", 10],
            ["card", 5],
            ["comment", 3]
        ];
    }

    /**
     * @dataProvider tableNamesProvider
     */
    public function testDBInitialized($table, $count) {
        $sql = "SELECT * FROM $table";
        $query = self::$db->execute($sql);
        $data = $query->fetchAll();
        $this->assertCount($count, $data);
    }
}