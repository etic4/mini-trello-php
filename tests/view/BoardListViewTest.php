<?php

require_once "vendor/vendor/autoload.php";

use GuzzleHttp\Client;


class BoardListViewTest extends \PHPUnit\Framework\TestCase {
    public static DB $db;
    public static Client $http;

    public static function base_url(): string {
        return "http://localhost" . Configuration::get("web_root");
    }

    public static function default_page() {
        return self::base_url() . Configuration::get("default_controller") . "/index";
    }

    public static function setUpBeforeClass(): void {
        self::$db = new DB();
        self::$db->init();

        self::$http = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://localhost',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
    }

    public static function tearDownAfterClass(): void {
        self::$db->init();
    }

    private function http_get($uri): array {
        $base_url = self::base_url();
        $final_uri = "";
        $response = self::$http->get( $base_url . $uri, [
            'on_stats' => function (GuzzleHttp\TransferStats $stats) use (&$final_uri) {
                $final_uri = $stats->getEffectiveUri();
            }
        ]);

        $resp["status"] = $response->getStatusCode();
        $resp["uri"] = $final_uri;
        $resp["body"] = $response->getBody();
        $resp["headers"] = $response->getHeaders();

        return $resp;
    }

    public function testNotLoggedGetUserReturnsSignup() {
        $response = $this->http_get("user/index");

        $this->assertEquals(200, $response["status"]);
        $this->assertEquals($this->default_page(), $response["uri"]);
        $this->assertStringContainsString("<h2>Sign in</h2>", $response["body"] );
    }

    public function testGetBoardUrlWhenNotLoggedReturnsSignIN() {
        // get board/1 Action '1' doesn't exist in this controller
        $response = $this->http_get("user/index");
        $this->assertEquals(200, $response["status"]);
        $this->assertEquals("http://localhost/epfc/prwb_2021_a02/user/index", $response["uri"]);
        $this->assertStringContainsString("<h2>Sign in</h2>", $response["body"] );
    }


}