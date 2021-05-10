<?php

require_once "autoload.php";

class DisplayableError {
    private ?string $instance_name;
    private ?string $action;
    private ?string $id;
    private array $messages = [];

    public function __construct($instance=null, string $action=null, string $id=null) {
        $this->instance_name = $this->get_instance_name($instance);
        $this->action = $action;

        $this->id = $id;
        if ($id == null) {
            $this->id = $this->get_instance_id($instance);
        }
    }

    public function set_id(string $id) {
        $this->id = $id;
    }
    
    public function set_messages($messages_list) {
        $this->messages = $messages_list;
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

    private function get_instance_name($instance): ?string {
        $instance_name = null;

        if (is_string($instance)) {
            $instance_name = $instance;
        } else if (!is_null($instance)) {
            $instance_name = strtolower(get_class($instance));
        }
        return $instance_name;
    }

    private function get_instance_id($instance): ?int {
        $id = null;

        if (!is_null($instance) && !is_string($instance)) {
            $id = $instance->get_id();
        }
        return $id;
    }

}