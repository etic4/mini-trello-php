<?php namespace tools;

require_once "vendor/vendor/autoload.php";

use \GuzzleHttp\Client;
use \GuzzleHttp\TransferStats;
use \DiDom\Document;

class HTTPClient {
    private Client $http;

    // Retourne l'url de base
    public static function base_url(): string {
        return "http://localhost" . \Configuration::get("web_root");
    }


    // Retourne la page par défaut
    public static function default_url() {
        return self::base_url() . \Configuration::get("default_controller") . "/index";
    }


    //retourne une url absolue à partir d'une relative
    public static function url_for(string $relative_url): string {
        return self::base_url() . $relative_url;
    }


    public function __construct(string $base_uri = "http://localhost") {

        $this->http = new Client([
            "base_uri" => $base_uri,
            "timeout" => 1.0,
            "cookies" => true
        ]);
    }


    // execute un GET sur base_url().$uri et retourne une associative array
    // comprenant le status, l'url, le body et le DOM
    public function get($uri): array {
        $final_url = "";

        $response = $this->http->get( self::url_for($uri) , [
            'on_stats' => function (TransferStats $stats) use (&$final_url) {
                $final_url = $stats->getEffectiveUri();
            }
        ]);

        return $this->build_response($response, $final_url);
    }


    public function post(string $uri, array $params): array {
        $final_url = "";

        $response = $this->http->post(self::url_for($uri), [
            'on_stats' => function (TransferStats $stats) use (&$final_url) {
                $final_url = $stats->getEffectiveUri();
            },
            "form_params" => $params
        ]);

        return $this->build_response($response, $final_url);
    }

    public function login(string $email, string $password) {
        return $this->post("user/login", ["email" => $email, "password" => $password]);
    }

    public function logout() {
        $this->get("user/logout");
    }

    private function build_response(\Psr\Http\Message\ResponseInterface $response, string $final_url) {
        $resp["status"] = $response->getStatusCode();
        $resp["url"] = $final_url;
        $resp["body"] = (string)$response->getBody();
        $resp["dom"] = null;

        if (!empty($resp["body"])) {
            $resp["dom"] = new Document($resp["body"]);
        }
        return $resp;
    }

}