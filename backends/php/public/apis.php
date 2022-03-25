<?php


require_once "../app/init.php";


use app\Application;


new Application();
//
//header('Vary: Origin');
//header('Access-Control-Allow-Origin: Same-Origin');
//header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
//header('Access-Control-Allow-Headers: token, Content-Type');
//header('Access-Control-Max-Age: 1728000');
//header('Content-Type: application/json');
/*
await fetch("http://127.0.0.1:63342/php/public/apis.php", {method: "POST", body: JSON.stringify({
    "login": {
        "user_uniq": "asy_0x0",
        "user_email": "example@email.com",
        "user_pass": "1234"
    }
}), headers: {'Content-Type': 'application/json'}}).then(e => e.text())
*/
// will work if using browser
print_r(
    (new \tiny\Server())->HTTP->getReferer()
);