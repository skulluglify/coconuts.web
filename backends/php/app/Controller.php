<?php namespace app;

use app\tiny\Date as Date;

class Controller
{
    /**
     * @throws \Exception
     */
    public function __construct() {

        $date = new Date(abbr: "WIB");
        print_r(
            $date->getHour()
        );
    }
}