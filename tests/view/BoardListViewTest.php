<?php namespace view;

require_once "vendor/vendor/autoload.php";
require_once "tests/tools/DB.php";
require_once  "tests/tools/HTTPClient.php";

use tools\DB;
use tools\HTTPClient;

class BoardListViewTest extends \PHPUnit\Framework\TestCase {
    private static DB $db;
    private static HTTPClient $http;


    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();

        self::$http = new HTTPClient();
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    public function testNotLoggedGetUserReturnsSignup() {
        $response = self::$http->get("user/index");

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->default_page(), $response["url"]);
        $this->assertStringContainsString("<h2>Sign in</h2>", $response["body"] );
        $this->assertEquals("Sign in", $response["html"]->find_one("h2")->textContent);
    }

    public function testGetBoardUrlWhenNotLoggedReturnsSignIN() {
        // get board/1 Action '1' doesn't exist in this controller
        $response = self::$http->get("board");
        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->default_page(), $response["url"]);
        $this->assertStringContainsString("<h2>Sign in</h2>", $response["body"] );
    }


}