<?php

use \PHPUnit\Framework\TestCase;

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
        $user = User::get_by_email("boverhaegen@epfc.eu");
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->get_id());
    }

    public function testCreateUserInstance(): User {
        $email = "test@rmail.com";
        $fullName = "Prénom Nom";
        $password = "Pass1!";
        $user = new User($email, $fullName, null, null, null, $password);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->get_email(), $email);
        $this->assertEquals($user->get_fullName(), $fullName);
        $this->assertEquals($user->get_passwdHash(), Tools::my_hash($password));

        return $user;
    }

    /**
     * @depends testCreateUserInstance
     * @param User $user
     */
    public function testInsertUser(User $user) {
        $this->assertNull($user->get_id());

        $data = self::$db->execute("SELECT COUNT(*) as total FROM user")->fetch();
        $this->assertEquals(5, $data["total"]);

        //TODO: changer ça, set les dates et simplement set l'id après insert
        $user = $user->insert();
        $data = self::$db->execute("SELECT COUNT(*) as total FROM user")->fetch();

        $this->assertEquals(6, (int) $data["total"]);
        $this->assertEquals("6", $user->get_id());
    }
}