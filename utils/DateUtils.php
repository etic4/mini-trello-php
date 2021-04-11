<?php

require_once "autoload.php";

class DateUtils {

    public static function now_if_null(?DateTime $date): DateTime {
        return $date == null ? new DateTime() : $date;
    }

    public static function php_date($sqlDate): ?DateTime {
        return $sqlDate != null ? new DateTime($sqlDate) : null;
    }

    public static function sql_date(?DateTime $dateTime): ?string {
        return $dateTime != null ? $dateTime->format('Y-m-d H:i:s') : null;
    }

    public static function most_recent_timestamp(DateTime $dateTimeCreated, ?DateTime $dateTimeModified): int {
        $tstmpCreated = $dateTimeCreated->getTimestamp();
        $tstmpModified = $dateTimeModified != null ? $dateTimeModified->getTimestamp() : 0;
        return max($tstmpCreated, $tstmpModified);
    }

    public static function intvl($firstDate, $secondDate): string {
        $intvl = $secondDate->diff($firstDate);
        $laps = "1 second ago";
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
        return $laps;
    }

}