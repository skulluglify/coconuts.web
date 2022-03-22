<?php

require_once "../app/init.php";

new \app\Application();
echo "<br/>";

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

$user->insert(
    null,
    "slamet, udin",
    "udin123",
    "2012-10-12",
    "male",
    "user@example.com",
    "hsuaUHSaoswnUW878",
    null,
    null,
    null
);

$time = $user->select(array(
    "user_gender" => "male"
))["time"];

if (is_string($time)) {

    $date = new \tiny\Date($time);

    print_r(
        $date->getTimestamp()
    );

    echo "<br/>";

    switch ($date->getTypeOfWeekday()) {

        case \tiny\WeekdayType::TUESDAY:
            print_r("is tuesday");
            break;
        default:
            break;
    }

    echo "<br/>";
    echo "<br/>";

    print_r(strtotime($time));
}

echo "<br/>";

print_r(23);

echo "<br/>";

$a = array(
    "user_name" => "ahmad asy, syafiq"
);

foreach ($a as $key => $value) {

    print_r($key);
    echo "<br/>";
    print_r($value);
}

echo "<br/>";

//foreach ($_SERVER as $key => $value) {
//
//    print_r($key);
//    echo "<br/>";
//    print_r($value);
//    echo "<br/>";
//    print_r(gettype($value));
//    echo "<br/>";
//    echo "<br/>";
//}

$server_info = new \tiny\Server();

// GET POST PUT DELETE PATCH

print_r(
    $server_info->Remote->getAddress()
);
echo "<br/>";
print_r(
    $server_info->Server->getPort()
);
echo "<br/>";
print_r(empty(null));

$mysql->close();