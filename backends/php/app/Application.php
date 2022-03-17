<?php namespace app;

use models\User;
use tiny\Date as Date;
use tiny\MySQL as MySQL;

class Application
{
    /**
     * @throws \Exception
     */
    public function __construct() {

        $date = new Date(abbr: "WIB");
        print_r(
            $date->getMonth()
        );

//        echo hash("sha3-256", "fish rod");
    }
}