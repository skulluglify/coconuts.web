<?php namespace controllers;

use models\Banned;
use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerStructure;
use tiny\Date;
use tiny\MessageType;
use tiny\MySQL;
use tiny\Request;
use tiny\Response;
use tiny\Server;
use function tiny\c;


class Identify extends Controller implements ControllerStructure
{
    protected MySQL $connect;

    protected Session $session;
    protected Banned $banned;
    protected User $user;

    public function __construct(MySQL $conn, Server $server)
    {
        parent::__construct($server);
        $this->connect = $conn;
        $this->init();
    }

    protected function init(): void
    {

        $this->session = new Session($this->connect);
        $this->banned = new Banned($this->connect);
        $this->user = new User($this->connect);

        $this->session->create();
        $this->banned->create();
        $this->user->create();

        $this->server->route("identify", function (Request $req, Response $res) {

            $this->wait = true;
            $data = $req->json();

            if (!empty($data)) {

                 $identify = c($data, "identify");
                $token = c($identify, "token");

                // enable uri search param
                if (empty($token)) $token = c($data, "token");

                $attach = true;
                while ($attach) {

                    if (!empty($token)) {

                        // check in session
                        $user_session = $this->session->select(array(
                            "token" => $token
                        ), array(
                            "user_id",
                            "time"
                        ), 1);

                        if (!empty($user_session)) {

                            $user_id = c($user_session, 0, "user_id");
                            $session_time = c($user_session, 0, "time");

                            $session_time = new Date($session_time);
                            $time_now = new Date();

                            $expired = Date::enhance_time_ms(86400, $session_time->getTimestamp());

                            if ($expired < $time_now->getTimestamp()) {

                                $res->render($this->trace("Token has been expired!", mode: MessageType::FAILURE));
                                break;
                            }

                            if (is_int($user_id) and $user_id > 0) {

                                $user_banned = $this->banned->select(array(
                                    "user_id" => $user_id
                                ), "level", 1);

                                if (!empty($user_banned)) {

                                    $level = c($user_banned, 0, "level");
                                    $res->render($this->trace("You are blocked, due to abnormal traffic activity!", mode: MessageType::FAILURE));
                                    break;
                                }

                                $users = $this->user->select(array(
                                    "id" => $user_id
                                ), array(
                                    "user_uniq",
                                    "user_name",
                                    "user_photo",
                                    "user_dob",
                                    "user_gender",
                                    "user_email",
                                    "user_phone",
                                    "user_location",
                                    "user_description"
                                ), 1);

                                if (!empty($users)) {

                                    $user = c($users, 0);

                                    if (!empty($user)) {

                                        $user["user_photo"] = str_replace("\\","/",$user["user_photo"]);

                                        $res->render($user);
                                    }
                                }
                            }
                            break;
                        }
                    }

                    $res->render($this->trace("Token not found!", mode: MessageType::FAILURE));
                    $attach = false;
                }
            }
        });
    }
}