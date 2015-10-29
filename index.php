<?php
/** APP 根目录 指向public上一级 */
define('APP_PATH', __DIR__);
$app = new Yaf\Application(APP_PATH . '/conf/application.ini');

$app->run();

echo 1;

