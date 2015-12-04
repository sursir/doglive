<?php
function curlFileUpload($addr, $file)
{
    $cfh = new CURLFile($file);

    $data = [
        'thefile' => $cfh
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $addr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
}

$addr = 'localhost/receiver.php';
$file = realpath('./t.c');
// $rs = curlFileUpload($addr, $file);
// echo $rs;


function streamFileUpload($addr, $file)
{
    $sh = fopen($file, 'rw+');
    // $sh = fopen('php://temp', 'rw+');
    // $content = 'hello, world';
    // fwrite($sh, $content);

    $filestat = fstat($sh);
    rewind($sh);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $addr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_INFILE, $sh);
    curl_setopt($ch, CURLOPT_INFILESIZE, $filestat['size']);

    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
}

$sResult = streamFileUpload($addr, $file);
echo $sResult;
