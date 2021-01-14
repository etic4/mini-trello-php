<?php


class ValidationError {

    private ?string $instance_name;
    private ?string $action;
    private ?string $id;
    private array $messages;

    /* Retourne l'erreur et reset l'erreur pour la session */
    public static function get_error_and_reset(): ValidationError {
        $error = new ValidationError();
        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            $_SESSION["error"] = new ValidationError();
        }
        return $error;
    }

    public function __construct($instance=null, ?string $action=null) {
        $this->instance_name = is_null($instance) ? null : strtolower(get_class($instance));
        $this->action = $action;
        $this->id = is_null($instance) ? null : $instance->get_id();
        $this->messages = [];
    }

    public function set_id(string $id) {
        $this->id = $id;
    }
    
    /* set la liste des messages et ajoute l'erreur à la session */
    public function set_messages_and_add_to_session($messages_list) {
        $this->messages = $messages_list;
        $_SESSION["error"] = $this;
    }

    public function is_empty(): bool {
        return empty($this->messages);
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
        return $this->instance_name == $instance_name
            && $this->action == $action
            && $this->id == $id;
    }

    /*La méthode chargée de leur affichage récupère les erreurs*/
    public function get_messages(): array {
        return $this->messages;
    }

}