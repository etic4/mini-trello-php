<?php

class ValidationError extends Model {
    private string $message;
    private string $instance;
    private ?string $action;
    private ?string $instanceId;

    public function __construct(string $message, 
                                string $instance, 
                                string $action=null, 
                                string $instance_id=null) {
        $this->message = $message;
        $this->instance = $instance;
        $this->action = $action;
        $this->instanceId = $instance_id;
    }

    public function get_message(): string {
        return $this->message;
    }

    public function get_instance(): string {
        return $this->instance;
    }

    public function get_action(): ?string {
        return $this->action;
    }

    public function get_instanceId(): ?string {
        return $this->instanceId;
    }

    public function must_be_displayed(string $instance, ?string $action, ?string $instance_id): bool {
        return $this->get_instance() == $instance 
            && $this->get_action() == $action 
            && $this->get_instanceId() == $instance_id;
    }
}
