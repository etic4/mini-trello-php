<?php namespace view;

require_once "vendor/vendor/autoload.php";
require_once "tests/tools/DB.php";
require_once "tests/tools/HTTPClient.php";

use tools\DB;
use tools\HTTPClient;

class NonLoggedUrlsChecksTest extends \PHPUnit\Framework\TestCase {
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


    /**
     * @dataProvider userUrlsProvider
     */
    public function testNotLoggedGetUserReturnsSignIn($rel_url) {
        echo $rel_url;
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->url_for($rel_url), $response["url"]);
        $this->assertEquals("Sign in", $response["dom"]->first("h2")->text());
    }


    public function testNotLoggedUserLoginReturnsSignUp() {
        $rel_url = "user/signup";
        echo $rel_url;
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(self::$http->url_for($rel_url), $response["url"]);
        $this->assertEquals("Sign up", $response["dom"]->first("h2")->text());
    }


    /**
     * @dataProvider variousUrlsProvider
     */
    public function testGetUrlWhenNotLoggedPrintHelloGuest($rel_url) {
        echo $rel_url;
        $hello = "Hello guest ! Please login or signup.";
        $response = self::$http->get($rel_url);

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals(true, $response["dom"]->has("main > p"), "Pas de noeud 'main > p' dans le DOM");
        if ($response["dom"]->has("main > p")) {
            $this->assertEquals($hello, $response["dom"]->first("main > p")->text());
        }
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


    public function userUrlsProvider(): array {
        return [
            ["user"],
            ["user/index"],
            ["user/login"]
        ];
    }


    public function variousUrlsProvider(): array {
        return [
            ["board"],
            ["board/index"],
            ["board/board"],
            ["board/board/1"],
            ["board/edit"],
            ["board/edit/1"],
            ["board/delete"],
            ["board/delete/1"],
            ["board/add"],
            ["board/remove"],
            ["board/remove/1"],
            ["board/delete_confirm"],
            ["board/delete_confirm/1"],
            ["column"],
            ["column/index"],
            ["column/left"],
            ["column/right"],
            ["column/delete"],
            ["column/edit/1"],
            ["column/delete"],
            ["column/delete_confirm/1"],
            ["column/remove"],
            ["column/remove/1"],
            ["card"],
            ["card/index"],
            ["card/left"],
            ["card/right"],
            ["card/up"],
            ["card/down"],
            ["card/delete"],
            ["card/edit/1"],
            ["card/delete"],
            ["card/delete_confirm"],
            ["card/delete_confirm/1"],
            ["card/remove"],
            ["card/remove/1"],

            //Intéressant de voir que ça passe. Lié à la manière dont le framework fonctionne
            ["board/index/1"],
            ["card/view/salut/les/gars"],
        ];
    }


    public function wrongUrlProvider() {
        return [
            ["bo"],
            ["bo/index"],
            ["board/view"],
            ["board/1"],
            // celle-ci donne une 404
            //["card/view/salut/les/gars/boum!"]

        ];
    }
}