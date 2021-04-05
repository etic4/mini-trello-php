<?php

require_once "autoload.php";

class TitleValidation extends Validation {
    private string $dao;

    public function __construct(string $dao) {
        $this->dao = $dao;
    }

    public function validate(object $object, $update=false): TitleValidation {
        if (self::str_lower_than($object->get_title(), 3)) {
            $this->errors[] = "Title must be at least 3 characters long";
        }

        if (!$update || $this->dao::title_has_changed($object->get_title())) {
            if (!$this->dao::is_title_unique($object->get_title())){
                $obj_class_name = strtolower(get_class($object));
                $this->errors[] = "A $obj_class_name with the same title already exists";
            }
        }

        return $this;
    }
}