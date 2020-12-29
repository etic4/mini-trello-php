<?php

require_once "framework/Model.php";


class DBTools extends Model {

    public static function intvl($firstDate, $secondDate): string {
        $intvl = $secondDate->diff($firstDate);
        $laps = "1 second ago";
        if(!is_null($intvl)) {
            if ($intvl->y != 0) {
                if($intvl->y == 1) {
                    $laps = "1 year ago";
                } else {
                    $laps = $intvl->y . " years ago";
                }
            } elseif ($intvl->m != 0) {
                if($intvl->m == 1) {
                    $laps = "1 month";
                } else {
                    $laps = $intvl->m . " months ago";
                }
            } elseif ($intvl->d != 0) {
                if($intvl->d == 1) {
                    $laps = "1 day";
                } else {
                    $laps = $intvl->d . " days ago";
                }
            } elseif ($intvl->h != 0) {
                if($intvl->h == 1) {
                    $laps = "1 hour";
                } else {
                    $laps = $intvl->h . " hours ago";
                }
            } elseif ($intvl->i != 0) {
                if($intvl->i == 1) {
                    $laps = "1 minute";
                } else {
                    $laps = $intvl->i . " minutes ago";
                }
            } elseif ($intvl->s != 0) {
                if($intvl->s == 1) {
                    $laps = "1 second";
                } else {
                    $laps = $intvl->s . " seconds ago";
                }
            }
        }
        return $laps;
    }
    
    public static function laps ($firstDate, $secondDate): string {
        if ($secondDate->diff($firstDate)->format('Y-m-d H:i:s') == "0-0-0 0:0:0") {
            return "Modified " . self::intvl($firstDate, $secondDate);
        }
        else {
            return "Never modified";
        }
    }

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
