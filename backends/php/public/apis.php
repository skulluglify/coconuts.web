<?php

require_once "../app/init.php";

new \app\Application();
echo "<br/>";

$mysql = new \tiny\MySQL("config", prefix: "..");
$user = new \models\User($mysql);

// debug
if ($user->drop()) {

    print_r("tut");
    echo "<br>";
};

/*
 "user_photo"
 "user_name"
 "user_uniq"
 "user_age"
 "user_gender"
 "user_email"
 "user_pass"
 "user_phone"
 "user_location"
 "user_description"
*/

// production
$user->create();
$user->insert(
    array(
        "user_photo" => null,
        "user_name" => "ahmad asy, syafiq",
        "user_uniq" => "asy_0x0",
        "user_age" => "2002-07-07",
        "user_gender" => "male",
        "user_email" => "example@email.com",
        "user_pass" => "Xshuwgdw9089e",
        "user_phone" => null,
        "user_location" => null,
        "user_description" => null
    )
);

$user->insert(
    array(
        "user_photo" => null,
        "user_name" => "ujang",
        "user_uniq" => "sy_0x0",
        "user_age" => "2002-07-07",
        "user_gender" => "male",
        "user_email" => "example@email.com",
        "user_pass" => "Xshuwgdw9089e",
        "user_phone" => null,
        "user_location" => null,
        "user_description" => null
    )
);

if ($user->update(array(
    "user_photo" => null,
    "user_name" => "samsudin",
    "user_uniq" => "asy_0x0",
    "user_age" => "2010-10-12",
    "user_gender" => "male",
    "user_email" => "example@email.com",
    "user_pass" => "ioshd87273bbd",
    "user_phone" => null,
    "user_location" => null,
    "user_description" => null
), array(
    "user_uniq" => "asy_0x0"
))) {

    print_r("tut");
    echo "<br/>";
};

$user->delete(array(
    "user_uniq+" => "asy%"
));

print_r(
    $user->select(array(
        "user_uniq+" => "sy%"
    ))
);

$time = $user->select(array(
    "user_name" => "ujang"
), "time", 1)["time"];

if (is_string($time)) {

    $date = new \tiny\Date($time);

    print_r(
        $date->getTimestamp()
    );

    switch ($date->getTypeOfWeekday()) {

        case \tiny\WeekdayType::TUESDAY:
            print_r("is tuesday");
            break;
        default:
            break;
    }

    echo "<br/>";

    print_r(strtotime($time));

    echo "<br/>";
    print_r($time);
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