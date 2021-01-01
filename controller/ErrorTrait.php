<?php


trait ErrorTrait {

    /*Ajoute les erreurs à $_SESSION pour pourvoir les passer à travers une redirection à la méthode qui les affichera*/
    private function set_errors($errors) {
        $_SESSION["errors"] = $errors;
    }

    private function no_errors() {
        return (!isset($_SESSION["errors"]) || empty($_SESSION["errors"]));
    }

    /*La méthode chargée de leur affichage récupère les erreurs, $_SESSION["errors"] est reset*/
    private function get_errors_and_reset(): array {
        $errors = [];

        if (isset($_SESSION["errors"])) {
            $errors = $_SESSION["errors"];
            $_SESSION["errors"] = [];
        }
        return $errors;
    }
}