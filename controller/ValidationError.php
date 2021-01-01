<?php


class ValidationError {

    /*La méthode chargée de leur affichage récupère les erreurs, $_SESSION["error"] est reset*/
    public static function get_errors_and_reset(): array {
        $error = array();

        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            $_SESSION["error"] = [];
        }
        return $error;
    }

    public function __construct($instance=null, ?string $action=null) {
        $_SESSION["error"] = array(
          "instance_name" => get_class($instance),
          "action" => $action,
          "id" => $instance == null ? null : $instance->get_id(),
          "messages" => []
        );
    }

    /* set la liste des messages.*/
    public function set_messages($messages_list) {
        $_SESSION["error"]["messages"] = $messages_list;
    }

    public function is_empty(): bool {
        return empty($_SESSION["error"]["messages"]);
    }

    /* Retourne true s'il y a des erreurs pour cette instance et cette action*/
    public function has_errors(?string $instance_name=null, ?string $action=null, ?string $id=null): bool {
        /* si pas de messages, pas d'erreur*/
        if ($this->is_empty()) {
            return false;
        }

        /*si instance est pas set, ça revient à invoquer is_empty(). Si on est ici, c'est qu'il y a des messages,
        donc on retourne true */
        if (!isset($instance_name)) {
            return true; //càd !$this->is_empty()
        }

        /*Sinon on check l'égalité */
        return $_SESSION["error"]["instance_name"] == $instance_name
            && $_SESSION["error"]["action"] == $action
            && $_SESSION["error"]["id"] == $id;
    }

    /*La méthode chargée de leur affichage récupère les erreurs*/
    public function get_errors(): array {
        return $_SESSION["error"]["messages"];
    }



}