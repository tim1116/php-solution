<?php
/**
 * File: test.php
 * PROJECT_NAME: php-solution
 */

define('ROOT_SRC', dirname(__FILE__) . '/src');

include dirname(__DIR__) . "/tool/init.php";
include ROOT_SRC . "/Ip2AdInfo.php";

$envFile = __DIR__ . "/env.ini";
$config  = parse_ini_file($envFile);


$service = (new Ip2AdInfo($config["key"], $config["sk"]));

var_dump($service->ipInfo("111.206.145.41")->isSuccess());

var_dump($service->ipInfo("113.66.109.247")->isSuccess());

var_dump($service->ipInfo("aa.dd.109.247")->isSuccess());



