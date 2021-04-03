<?php namespace database;

require_once "tests/tools/DB.php";

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
            ["user", 6],
            ["board", 3],
            ["`column`", 10],
            ["card", 5],
            ["comment", 2],
            ["participate", 2],
            ["collaborate", 4]
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