<?php

/* Ajouts à la classe Controller du framework */
abstract class EController extends Controller {

    public function get_object_or_redirect(array $GET_or_POST, string $param_name, string $className) {
        $obj = null;

        if (isset($GET_or_POST[$param_name])) {
            $obj = $className::get_by_id($GET_or_POST[$param_name]);
        }

        if (is_null($obj)) {
            // Soit ça soit mettre méthode à static dans framework
            $this->redirect();
        }

        return $obj;
    }

}