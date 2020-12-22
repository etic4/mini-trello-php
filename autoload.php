<?php

spl_autoload_register(
    function($class) {
        $SEP = DIRECTORY_SEPARATOR;
        $baseDir =  __DIR__;
        $dirs = array("framework", "controller", "model", "view", "tests");

        $found = false;
        $i = 0;
        while (!$found && $i < count($dirs)) {
            $dirName = $dirs[$i];
            $filepath = $baseDir.$SEP.$dirName.$SEP."$class.php";
            if (file_exists($filepath)) {
                require_once $filepath;
                $found = true;
            }
            ++$i;
        }
    },
    true,
    false
);
