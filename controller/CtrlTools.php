<?php

class CtrlTools {

    public static function breadcrumb(): string {
        $breadcrumb = "<span class='breadcrumb-current'>Boards</span>";

        if (isset($_GET["param1"])) {
            $separator = "<span class='breadcrumb-separator'>&lt;</span>";

            $class_name = ucfirst($_GET["controller"]);
            $instance = $class_name::get_by_id($_GET["param1"]);
            $title = $instance->get_title();

            $breadcrumb = "<span><a href='board/index'> Boards</a></span>";

            if ($_GET["controller"] == "card") {
                $parent = $instance->get_board();
                $parent_id = $parent->get_id();
                $parent_title = $parent->get_title();

                $breadcrumb = "<span><a href='board/board/$parent_id'>Board \"$parent_title\"</a></span>" . $separator . $breadcrumb;
            }

            $breadcrumb = "<span class='breadcrumb-current'>$class_name \"$title\"</span>". $separator . $breadcrumb;
            $breadcrumb = "<div class='breadcrumb'>$breadcrumb</div>";
        }
        return $breadcrumb;
    }
}
