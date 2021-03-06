<?php namespace view;

require_once "vendor/vendor/autoload.php";
require_once "tests/tools/DB.php";
require_once "tests/tools/HTTPClient.php";

use tools\DB;
use tools\HTTPClient;

class LoggedUrlsChecksTest extends \PHPUnit\Framework\TestCase {
    private static DB $db;
    private static HTTPClient $http;


    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();

        self::$http = new HTTPClient();
        self::$http->login("boverhaegen@epfc.eu", "Password1,");
    }


    public static function tearDownAfterClass(): void {
        self::$db->init();
        self::$http->logout();
    }


    /**
     * @dataProvider userUrlsProvider
     */
    public function testLoggedGetUserReturnsBoardList($rel_url) {
        echo $rel_url;
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->default_url(), $response["url"]);
    }


    /**
     * @dataProvider variousUrlsProvider
     */
    public function testGetUrlWhenLoggedRedirectToDefault($rel_url) {
        echo $rel_url;
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->default_url(), $response["url"]);

    }


    /**
     * @dataProvider wrongUrlProvider
     */
    public function testNonExistentControllerOrMethodReturnsErrorView($rel_url) {
        echo $rel_url;
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals("Error", $response["dom"]->first("h1")->text());
    }

    public function testRemoveCollabTwice() {
        $params = [
            "collab_id" => "2",
            "board_id" => "1"
        ];

        $response = self::$http->post("collaboration/add", $params);
        $response = self::$http->post("collaboration/add", $params);

        $this->assertEquals(200, $response["status"]);
    }

    public function onNotSuccessfulTest(\Throwable $t): void {
        parent::onNotSuccessfulTest($t);
    }


    public function userUrlsProvider(): array {
        return [
            ["user"],
            ["user/index"],
            ["user/login"]
        ];
    }


    public function variousUrlsProvider(): array {
        return [
            ["board/index"],
            ["board/view"],
            ["board/edit"],
            ["board/edit/1"],
            ["board/delete"],
            ["board/delete/1"],
            ["board/add"],
            ["board/remove"],
            ["board/remove/1"],
            ["board/delete_confirm"],
            ["column"],
            ["column/index"],
            ["column/left"],
            ["column/right"],
            ["column/delete"],
            ["column/edit/1"],
            ["column/delete"],
            ["column/remove"],
            ["card"],
            ["card/index"],
            ["card/left"],
            ["card/right"],
            ["card/up"],
            ["card/down"],
            ["card/delete"],
            ["card/delete"],
            ["card/delete_confirm"],
            ["card/remove"],
        ];
    }


    public function wrongUrlProvider() {
        return [
            ["bo"],
            ["bo/index"],
            ["board/board"],
            ["board/1"],
            // celle-ci donne une 404
            //["card/view/salut/les/gars/boum!"]

        ];
    }
}