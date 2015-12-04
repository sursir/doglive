<?php

$sh = fopen('php://input', 'r');

// $data = fread($sh, 1);
// echo $data;

while (! feof($sh)) {
    $data = fread($sh, 1);
    // echo mb_convert_encoding($data, 'utf-8', 'gbk');
    echo $data;
}

fclose($sh);
