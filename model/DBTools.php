<?php
/**/
require_once "framework/Model.php";

class DBTools {

    public static function intvl($firstDate, $secondDate): string {
        $intvl = $secondDate->diff($firstDate);
        if($intvl !== 0) {
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

}
