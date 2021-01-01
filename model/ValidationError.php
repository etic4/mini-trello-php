<?php

class ValidationError extends Model {
    private string $message;
    private object $instance;
    private ?string $action;

    public function __construct(string $message, 
                                object $instance, 
                                string $action=null) {
        $this->message = $message;
        $this->instance = $instance;
        $this->action = $action;
    }


    //    GETTERS    //

    public function get_message(): string {
        return $this->message;
    }

    public function get_instance(): object {
        return $this->instance;
    }

    public function get_instance_id(): ?string {
        return $this->get_instance()->get_id();
    }

    public function get_instance_name(): string {
        return get_class($this->get_instance());
    }
 
    public function get_action(): ?string {
        return $this->action;
    }

    
    //    TOOLBOX    //

    public function must_be_displayed(object $instance, ?string $action): bool {
        if(is_null($this->get_instance_id())) {
            return $this->get_instance_name() == get_class($instance)
                && $this->get_action() == $action;
        }
        else {
            return $this->get_instance_name() == get_class($instance)
                && $this->get_action() == $action
                && $this->get_instance_id() == $instance->get_id();
        }    
    }
}
