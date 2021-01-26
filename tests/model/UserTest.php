<?php namespace model;

use \Tools;
require_once "tests/tools/DB.php";
use \User;
use \Datetime;
use \TypeError;
use \PHPUnit\Framework\TestCase;
use \tools\DB;

class UserTest extends TestCase {
    public static DB $db;

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testGetUserInstanceFromDB() {
        $user = User::get_by_id(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->get_id());
    }

    public function testCreateUserInstance(): User {
        $email = "test@rmail.com";
        $fullName = "Prénom Nom";
        $password = "Pass1!";
        $user = new User($email, $fullName, $password, null, null, null);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->get_email(), $email);
        $this->assertEquals($user->get_fullName(), $fullName);
        $this->assertEquals($user->get_passwdHash(), Tools::my_hash($password));

        return $user;
    }

    /**
     * @depends testCreateUserInstance
     */
    public function testGetIdReturnNullOnNotSavedInstance(User $user): User {
        $this->assertEquals(null, $user->get_id());
        return $user;
    }

    /**
     * @depends testCreateUserInstance
     */
    public function testGetRegisteredAtProducesErrorOnNotSavedInstance(User $user) {
        $this->expectException(TypeError::class);
        $user->get_registeredAt();
        return $user;
    }

    /**
     * @depends testCreateUserInstance
     */
    public function testCountPlus1AfterInsert(User $user) {
        $data = self::$db->execute("SELECT COUNT(*) as total FROM user")->fetch();
        $count = $data["total"];

        $user->insert();
        $data = self::$db->execute("SELECT COUNT(*) as total FROM user")->fetch();

        $this->assertEquals($count + 1, $data["total"]);

        return $user;
    }


    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testIdSetAfterInsert(User $user) {
        $this->assertIsString($user->get_id());
    }

    /**
     * @depends testCountPlus1AfterInsert
     */
    public function testRegisteredAtSetAfterInsert(User $user) {
        $this->assertInstanceOf(DateTime::class, $user->get_registeredAt());
    }
}