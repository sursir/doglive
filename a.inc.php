<?php
// echo "inclued file:\n";
// echo __DIR__, "\n";
// echo __FILE__, "\n";

// echo "\n";
// echo $_SERVER['HTTP_HOST'], "\n";
// echo $_SERVER['SCRIPT_NAME'], "\n";
// echo $_SERVER['SCRIPT_FILENAME'], "\n";
// echo $_SERVER['PHP_SELF'], "\n";


$size = 1 << 16;

for ($key = 0, $maxKey = ($size - 1)* $size; $key <= $maxKey; $key += $size) {
    // echo $key, $maxKey, $size, "\n";
}

phpinfo();
