<?php

require_once "autoload.php";

class CtrlTools {

    public static function get_object_or_redirect(array $GET_or_POST, string $param_name, string $className) {
        $obj = null;

        if (isset($GET_or_POST[$param_name])) {
            $obj = $className::get_by_id($GET_or_POST[$param_name]);
        }

        if (is_null($obj)) {
            self::redirect();
        }

        return $obj;
    }

    public static function breadcrumb(): string {
        $breadcrumb = "<span class='breadcrumb-current'>Boards</span>";

        if (isset($_GET["param1"])) {
            $separator = "<span class='breadcrumb-separator'>&lt;</span>";

            $class_name = ucfirst($_GET["controller"]);
            $instance = $class_name::get_by_id($_GET["param1"]);

            $breadcrumb = "<span><a href='board/index'> Boards</a></span>";

            if ($_GET["controller"] == "card") {
                $parent = $instance->get_board();
                $parent_id = $parent->get_id();
                $parent_title = $parent->get_title();

                $breadcrumb = "<span><a href='board/board/$parent_id'>Board \"$parent_title\"</a></span>" . $separator . $breadcrumb;
            }

            $breadcrumb = "<span class='breadcrumb-current'>$class_name \"$instance\"</span>". $separator . $breadcrumb;

        }
        $breadcrumb = "<div class='breadcrumb'>$breadcrumb</div>";
        return $breadcrumb;
    }

    // pour pas devoir hériter de Controller ni devoir instancier
    public static function redirect($controller = "", $action = "index", $param1 = "", $param2 = "", $param3 = "", $statusCode = 303)
    {
        $web_root = Configuration::get("web_root");
        $default_controller = Configuration::get("default_controller");
        if (empty($controller)) {
            $controller = $default_controller;
        }
        if (empty($action)) {
            $action = "index";
        }
        $header = "Location: $web_root$controller/$action";
        if (!empty($param1)) {
            $header .= "/$param1";
            if (!empty($param2)) {
                $header .= "/$param2";
                if (!empty($param3)) {
                    $header .= "/$param3";
                }
            }
        }
        header($header, true, $statusCode);
        die();
    }
}
