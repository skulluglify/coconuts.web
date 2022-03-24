<?php namespace app;

use controllers\Login;
use controllers\Registry;
use Exception;
use tiny\Date;
use tiny\MySQL;
use tiny\Server;

class Application
{
    /*
     * level
    0 30 minutes
    1 2 hour
    2 6 hours
    3 1 day
    4 7 days
    5 1 month
    6 2 months
    7 1 year
    */
    protected array $level = array(1800, 7200, 21600, 86400, 604800, 2419200, 4838400, 29030400);

    /**
     * @throws Exception
     */
    public function __construct() {

        // $date = new Date(abbr: "UTC");
        $server = new Server();
        $conn = new MySQL("config", prefix: __DIR__);

        $attach = true;
        while ($attach) {

            $regis = new Registry($conn, $server);

            if ($regis->lock()) break;

            $login = new Login($conn, $server, $this->level);

            if ($login->lock()) break;

            $attach = false;
        }
        // year in seconds
        // print_r(
            // Date::enhance_time_ms($this->level[4])
        // );

        // echo hash("sha3-256", "fish rod");
        $conn->close();
    }
}