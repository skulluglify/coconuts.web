<?php

require_once "app/init.php";

$config = parse_ini_file(filename: "config.ini",process_sections: true, scanner_mode: INI_SCANNER_RAW);

use app\Controller as MyController;

new MyController();

//MYSQLI_REPORT_ERROR | MYSQLI_REPORT_OFF
mysqli_report(flags: MYSQLI_REPORT_OFF);

$link = @new mysqli(
    hostname: $config["mysql"]["default_host"],
    username: $config["mysql"]["default_user"],
    password: $config["mysql"]["default_password"],
    database: $config["mysql"]["default_name"],
    port: $config["mysql"]["default_port"],
    socket: null
);

if ($link->connect_errno) {
    error_log("Connection Error: ".$link->connect_error);
}

$link->query("CREATE TABLE IF NOT EXISTS cocos(cocos_id INT(11) AUTO_INCREMENT, PRIMARY KEY(cocos_id))");

print_r(
    $link->query("DESCRIBE cocos;")->fetch_row()
);

mysqli_commit($link);
mysqli_close($link);