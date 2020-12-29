<?php

require_once "framework/Model.php";


class DBTools extends Model {

    public static function breadcrumb(): string {
        $uri = str_replace(Configuration::get("web_root"), "", $_SERVER['REQUEST_URI']);
        $path = explode('/', $uri);
        $home = "<a href='board/index'>Boards</a>";
        $breadcrumb = "<p class='homeLink'>Boards</p>";

        if(isset($path[2])) {
            $instance_name = $path[0];
            $instance_id = $path[2];
            $sql =
                "SELECT Title
                 FROM $instance_name
                 WHERE ID=:id";
            $param = array("id" => $instance_id);
            $query = self::execute($sql, $param);
            $data = $query->fetch();

            $title = $data["Title"];

            $breadcrumb = "<p class='" . $instance_name . "Link'>" . ucfirst($instance_name) . " \"" . $title . "\"</p>
                           <p>" . $home . "</p>";
        }
        return $breadcrumb;
    }

   
}
