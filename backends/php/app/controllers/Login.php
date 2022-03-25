<?php namespace controllers;


use models\Banned;
use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerStructure;
use tiny\Date;
use tiny\MessageType;
use tiny\MySQL;
use tiny\Server;
use function tiny\c;


class Login extends Controller implements ControllerStructure
{

    protected MySQL $connect;
    protected array $level;

    // tables
    protected User $user;
    protected Session $session;
    protected Banned $banned;

    // server
    protected Server $server;

    // running
    protected bool $wait = false;

    public function __construct(MySQL $conn, Server $server, array $levels)
    {

        parent::__construct($server);
        $this->connect = $conn;
        $this->level = $levels;
        $this->init();
    }

    protected function init(): void
    {

        $this->user = new User($this->connect);
        $this->session = new Session($this->connect);
        $this->banned = new Banned($this->connect);

        // create tables if exists, debug drop it
        $this->user->create();
        $this->session->create();
        $this->banned->create();

        $data = $this->server->getDataJSON();

        // check data json
        if (is_array($data)) {

            // get commands key
            $login = c($data, "login");

            if (!empty($login)) {

                $this->wait = true;

                $user_uniq = c($login, "user_uniq");
                $user_email = c($login, "user_email");
                $user_pass = c($login, "user_pass");

                $attach = true;
                while ($attach)
                {

                    // get try login
                    $user_id = -1;
                    $try_login = -1;
                    $level = -1;

                    if (!is_null($user_uniq)) {

                        $user_id = $this->user->getId(array(
                            "user_uniq" => $user_uniq
                        ));

                    } else if (!is_null($user_email)) {

                        $user_id = $this->user->getId(array(
                            "user_email" => $user_email
                        ));
                    }

                    if ($user_id < 0) {

                        $this->setMessage("var user_name, user_email cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (empty($user_pass)) {
                        $this->setMessage("var user_pass cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    $session_dt = $this->session->select(array(
                        "user_id" => $user_id
                    ), "try_login", 1);

                    if (!is_null($session_dt)) {

                        $v = c($session_dt, 0, "try_login");

                        if (!is_null($v)) {

                            if (!is_int($v)) $v = intval($v);

                            $try_login = $v;
                        }
                    }

                    // collect ban data
                    $banned_dt = $this->banned->select(array(
                        "user_id" => $user_id
                    ), ["level", "time"], 1);

                    if (!empty($banned_dt)) {

                        // $ip = c($banned_dt, 0, "user_ip");
                        // $agent = c($banned_dt, 0, "user_agent");
                        $level = c($banned_dt, 0, "level");
                        $timestamp = c($banned_dt, 0, "time");

                        // get level time
                        if (!is_null($level) and !is_null($timestamp)) {

                            if (!is_int($level)) $level = intval($level);
                            if (!is_int($timestamp)) $timestamp = strtotime($timestamp);

                            if (0 <= $level) {

                                $start = (new Date())->getTimestamp();
                                $end = Date::enhance_time_ms($this->level[$level], $timestamp);

                                if ($start < $end) {

                                    $this->setMessage("You are blocked, due to abnormal traffic activity!", mode: MessageType::FAILURE); // yes u got ban, ha ha ha
                                    break;

                                // } else {

                                    // can log in
                                    // if failed again, ban level has been increase

                                }
                            }
                        } else {

                            $this->setMessage("Something went wrong on the server!", mode: MessageType::FAILURE);
                            break;
                        }
                    }

                    // 2 <= 3 times
                    // set ban if try_login equals great then 3
                    if (3 <= $try_login) {

                        if (!empty($banned_dt)) {

                            $this->banned->update(array(
                                "level" => 0 <= $level ? $level + 1 : 0,
                                "time" => Date::enhance_time(0, (new Date())->getTimestamp())
                            ), array(
                                "user_id" => $user_id
                            ));

                        } else {

                            $this->banned->insert(array(
                                "user_id" => $user_id,
                                "user_ip" => $this->server->getClientIP(),
                                "user_agent" => $this->server->HTTP->getUserAgent(),
                                "level" => 0
                            ));
                        }

                        $this->session->update(array(
                            "try_login" => 0
                        ), array(
                            "user_id" => $user_id
                        ));

                        $this->setMessage("You are blocked, for exceeding the login limit!", mode: MessageType::FAILURE);
                        break;
                    }

                    // check pass
                    $pass = null;

                    $user_dt = $this->user->select(array(
                        "user_uniq" => $user_uniq,
                        "user_email" => $user_email
                    ), "user_pass", 1, "OR");

                    if (!empty($user_dt)) {

                        $pass = c($user_dt, 0, "user_pass");
                    }

                    if ($user_pass == $pass) {

                        $token = Session::createToken(!empty($user_uniq) ? $user_uniq : "unknown");

                        $this->setMessage("success login", array(
                            "token" => $token
                        ));

                        // delete banned
                        $this->banned->delete(array(
                            "user_id" => $user_id
                        ));

                        // delete session
                        $this->session->delete(array(
                            "user_id" => $user_id
                        ));

                        // create new session
                        $this->session->insert(array(
                            "user_id" => $user_id,
                            "user_ip" => $this->server->getClientIP(),
                            "user_agent" => $this->server->HTTP->getUserAgent(),
                            "try_login" => 1,
                            "token" => $token
                        ));

                    } else {

                        // inc try_token
                        // try update, insert

                        if (0 <= $try_login) {

                            $this->session->update(array(
                                "try_login" => $try_login + 1,
                                "token" => "" // delete token
                            ), array(
                                "user_id" => $user_id
                            ));

                        } else {

                            $this->session->insert(array(
                                "user_id" => $user_id,
                                "user_ip" => $this->server->getClientIP(),
                                "user_agent" => $this->server->HTTP->getUserAgent(),
                                "try_login" => 1,
                                "token" => "" // delete token
                            ));
                        }

                        $this->setMessage("failed login!", mode: MessageType::FAILURE);
                        break;
                    }

                    $attach = false;
                }
            }
        }
    }
}
