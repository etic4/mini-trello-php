<?php

class CtrlTools {

    public static function breadcrumb(): string {
        $breadcrumb = "<p class='breadcrumb'>Boards</p>";

        if (isset($_GET["param1"])) {
            $id = $_GET["param1"];
            $Class = ucfirst($_GET["controller"]);
            $title = $Class::get_by_id($id)->get_title();
            $breadcrumb = "<div><span class='breadcrumb'>$Class \"$title\"</span><span><a href='board/index'> Boards</a></span></div>";
        }
        return $breadcrumb;
    }
}
