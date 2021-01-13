<?php namespace tools;

require_once "vendor/vendor/autoload.php";
require_once "DOMDocumentWrapper.php";

use \GuzzleHttp\Client;
use \GuzzleHttp\TransferStats;
use \GuzzleHttp\Cookie\CookieJar;

class HTTPClient {
    private Client $http;
    private CookieJar $cookies;

    // Retourne l'url de base
    public static function base_url(): string {
        return "http://localhost" . \Configuration::get("web_root");
    }

    // Retourne la page par défaut
    public static function default_page() {
        return self::base_url() . \Configuration::get("default_controller") . "/index";
    }

    public function __construct(string $base_uri = "http://localhost") {
//        $this->cookies = new CookieJar();
        $this->http = new Client([
            "base_uri" => $base_uri,
            "timeout" => 1.0,
            "cookies" => true
        ]);
    }

    // execute un GET sur base_url().$uri et retourne une associative array
    // comprenant le status, l'url, le body et le DOM
    public function get($uri): array {
        $base_url = self::base_url();
        $final_url = "";


        $response = $this->http->get( $base_url . $uri, [
            'on_stats' => function (TransferStats $stats) use (&$final_url) {
                $final_url = $stats->getEffectiveUri();
            }
        ]);

        $resp["status"] = $response->getStatusCode();
        $resp["url"] = $final_url;
        $resp["body"] = $response->getBody();
        $resp["html"] = new DOMDocumentWrapper($resp["body"]);

        return $resp;
    }
}