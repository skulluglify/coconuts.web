<?php

require_once "../app/init.php";

new \app\Application();


$mysql = new \tiny\MySQL("config", prefix: "..");
$user = new \models\User($mysql);

// debug
$user->drop();

// production
$user->create();
$user->insert(
    null,
    "ahmad asy, syafiq",
    "asy_0x0",
    "2002-07-07",
    "male",
    "user@example.com",
    "Xzcsd0oodeuwdgy",
    null,
    null,
    null
);

print_r(23);

$mysql->close();