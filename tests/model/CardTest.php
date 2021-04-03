<?php namespace model;

require_once "tests/tools/DB.php";
use \Card;
use CardDao;
use \Column;
use ColumnDao;
use \User;
use \Datetime;
use \TypeError;
use \tools\DB;
use UserDao;

class CardTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetCardInstanceFromDB() {
        $card = CardDao::get_by_id(1);
        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals(1, $card->get_id());
    }

    public function testCreateCardInstance(): Card {
        $title = "Titre de la carte";
        $body = "the body";
        $column = ColumnDao::get_by_id(1);
        $position = count($column->get_cards());
        $author = UserDao::get_by_id(1);

        $card = new Card($title, $body, $position, $author, $column);

        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals($card->get_title(), $title);
        $this->assertEquals($card->get_body(), $body);
        $this->assertEquals($card->get_column(), $column);
        $this->assertEquals($card->get_position(), $position);
        $this->assertEquals($card->get_author(), $author);

        return $card;
    }

    /**
     * @depends testCreateCardInstance
     */
    public function testGetIdReturnNullOnNotSavedInstance(Card $card) {
        $this->assertEquals(null, $card->get_id());
    }

    /**
     * @depends testCreateCardInstance
     */
    public function testCountPlus1AfterInsert(Card $card): Card {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `card`")->fetch();
        $count = $data["total"];

        $card = CardDao::insert($card);
        $data = self::$db->execute("SELECT COUNT(*) as total FROM `card`")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $card;
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testIdSetAfterInsert(Card $card) {
        $this->assertIsString($card->get_id());
    }

}