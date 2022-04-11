<?php namespace controllers;

use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerStructure;
use tiny\MySQL;
use tiny\Request;
use tiny\Response;
use tiny\Server;
use function tiny\c;

class Upload extends Controller implements ControllerStructure
{

    protected MySQL $connect;
    protected array $level;

    // tables
    protected User $user;
    protected Session $session;

    // server
    protected Server $server;

    // running
    protected bool $wait = false;

    public function __construct(MySQL $conn, Server $server)
    {

        parent::__construct($server);
        $this->connect = $conn;
        $this->init();
    }

    protected function init(): void
    {

        $this->user = new User($this->connect);
        $this->session = new Session($this->connect);

        // create tables if exists, debug drop it
        $this->user->create();
        $this->session->create();

        $this->server->route("upload", function (Request $req, Response $res) {

            $data = $req->json();
            $this->wait = true;

            $token = c($data, "user_token");
            $user_photo = c($data, "user_photo");

            print_r([ $token ]);

            print_r($user_photo);

            if (!empty($user_photo)) {

                $tmp_name = c($user_photo, "tmp_name");

                $distance = join("/", [
                    getcwd(),
                    "../../storage",
                    "user_photo.png" // DEBUG ONLY
                ]);

                print_r([ $tmp_name, $distance ]);

                if (!is_null($tmp_name)) move_uploaded_file($tmp_name, $distance);
            }
        });
    }
}