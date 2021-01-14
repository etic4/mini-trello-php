<?php
/**/
require_once "framework/Model.php";

class DBTools extends Model {
    public static function sql_date($datetime) {
        return $datetime->format('Y-m-d H:i:s');
    }

    public static function php_date($sqlDate): ?DateTime {
        try {
                return new DateTime($sqlDate);

        } catch (Exception $e) {
            print("Erreur lors de la conversion de la date: " . $sqlDate);
        }
    }

    public static function php_date_modified($sqlDate, $default): ?DateTime {
        if($sqlDate == null) {
            $sqlDate == $default;
        }
        return self::php_date($sqlDate);
    }

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
                    $laps = "1 month ago";
                } else {
                    $laps = $intvl->m . " months ago";
                }
            } elseif ($intvl->d != 0) {
                if($intvl->d == 1) {
                    $laps = "1 day ago";
                } else {
                    $laps = $intvl->d . " days ago";
                }
            } elseif ($intvl->h != 0) {
                if($intvl->h == 1) {
                    $laps = "1 hour ago";
                } else {
                    $laps = $intvl->h . " hours ago";
                }
            } elseif ($intvl->i != 0) {
                if($intvl->i == 1) {
                    $laps = "1 minute ago";
                } else {
                    $laps = $intvl->i . " minutes ago";
                }
            } elseif ($intvl->s != 0) {
                if($intvl->s == 1) {
                    $laps = "1 second ago";
                } else {
                    $laps = $intvl->s . " seconds ago";
                }
            }
        }
        return $laps;
    }
    
    public static function laps ($firstDate, $secondDate): string {
        if (is_null($secondDate->diff($firstDate))) {
            return "Never modified";
        } else {
            $firstDate = new DateTime();
            return "Modified " . self::intvl($firstDate, $secondDate);
        }
    }

    public static function breadcrumb(): string {
        $path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        $home = "<a href='board/index'>Boards</a>";
        $breadcrumb = $home;
        if(isset($path[4])) {
            $instance_name = $path[2];
            $instance_id = $path[4];
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
        } else {
            $breadcrumb = "<p class='homeLink'>Boards</p>";
        }
        return $breadcrumb;
    }

   
}
